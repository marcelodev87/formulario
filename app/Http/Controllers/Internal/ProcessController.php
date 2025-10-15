<?php

namespace App\Http\Controllers\Internal;

use App\Http\Controllers\Controller;
use App\Models\InternalActivityLog;
use App\Models\Process;
use App\Models\ProcessStatusTimeline;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProcessController extends Controller
{
    private const PER_PAGE_OPTIONS = [10, 25, 50, 100];

    public function index(Request $request): View
    {
        $perPage = (int) $request->input('per_page', 10);
        if (!in_array($perPage, self::PER_PAGE_OPTIONS, true)) {
            $perPage = 10;
        }

        $query = Process::query()->with(['institution.headquartersLocation']);

        $search = trim((string) $request->input('q', ''));
        if ($search !== '') {
            $query->where(function ($inner) use ($search) {
                $inner->where('title', 'like', "%{$search}%");

                if ($id = $this->normalizeNumeric($search)) {
                    $inner->orWhere('id', $id);
                }

                $inner->orWhereHas('institution', function ($institutionQuery) use ($search) {
                    $institutionQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('document', 'like', "%{$search}%");
                });
            });
        }

        $status = (string) $request->input('status');
        if ($status !== '') {
            $query->where('status', $status);
        }

        $uf = strtoupper((string) $request->input('uf'));
        if ($uf !== '') {
            $query->whereHas('institution.headquartersLocation', function ($locationQuery) use ($uf) {
                $locationQuery->where('uf', $uf);
            });
        }

        $type = (string) $request->input('type');
        if ($type !== '') {
            $query->where('type', $type);
        }

        $query->orderByDesc('created_at');

        /** @var LengthAwarePaginator $processes */
        $processes = $query->paginate($perPage)->withQueryString();

        $filters = [
            'q' => $search,
            'status' => $status,
            'uf' => $uf,
            'type' => $type,
            'per_page' => $perPage,
        ];

        $indicators = $this->buildMonthlyIndicators();

        return view('internal.processes.index', [
            'processes' => $processes,
            'filters' => $filters,
            'statusOptions' => Process::statusLabels(),
            'typeOptions' => Process::typeDefinitions(),
            'perPageOptions' => self::PER_PAGE_OPTIONS,
            'indicators' => $indicators,
        ]);
    }

    public function show(Process $process): View
    {
        if ($process->type === Process::TYPE_BRANCH_OPENING) {
            $process->load([
                'institution',
                'location.property',
                'location.leader',
                'statusTimeline.actor',
            ]);
        } else {
            $process->load([
                'institution.headquartersLocation.property',
                'location.property',
                'institution.administration',
                'institution.members' => function ($query) {
                    $query->orderBy('name');
                },
                'statusTimeline.actor',
            ]);
        }

        $activityLogs = InternalActivityLog::query()
            ->where('entity', Process::class)
            ->where('entity_id', $process->id)
            ->latest('created_at')
            ->limit(50)
            ->with('internalUser')
            ->get();

        $activityLogEntries = $activityLogs->map(function (InternalActivityLog $log) {
            $diff = $log->diff ?? [];
            $before = is_array($diff['before'] ?? null) ? $diff['before'] : [];
            $after = is_array($diff['after'] ?? null) ? $diff['after'] : [];

            $fields = collect(array_keys($before))
                ->merge(array_keys($after))
                ->unique()
                ->values();

            $formatValue = fn ($value) => $this->formatLogValue($value);
            $wrapValue = fn (string $value): string => $value === '-' ? $value : '"' . $value . '"';

            $changes = $fields
                ->map(function (string $field) use ($before, $after, $formatValue, $wrapValue) {
                    $hasOld = array_key_exists($field, $before);
                    $hasNew = array_key_exists($field, $after);

                    $oldFormatted = $formatValue($before[$field] ?? null);
                    $newFormatted = $formatValue($after[$field] ?? null);

                    if (!$hasOld && !$hasNew) {
                        return null;
                    }

                    $label = Str::headline(str_replace('_', ' ', $field));

                    if (!$hasOld && $hasNew) {
                        return sprintf('%s definido para %s', $label, $wrapValue($newFormatted));
                    }

                    if ($hasOld && !$hasNew) {
                        return sprintf('%s removido (antes: %s)', $label, $wrapValue($oldFormatted));
                    }

                    if ($oldFormatted === $newFormatted) {
                        return null;
                    }

                    return sprintf('%s: %s -> %s', $label, $wrapValue($oldFormatted), $wrapValue($newFormatted));
                })
                ->filter()
                ->values()
                ->all();

            return [
                'title' => Str::headline($log->action),
                'user' => $log->internalUser->name ?? 'Equipe',
                'timestamp' => optional($log->created_at)->timezone('America/Sao_Paulo'),
                'changes' => $changes,
            ];
        });

        $institution = $process->institution;

        $primaryLocation = $process->location ?? optional($institution)->headquartersLocation;
        $headquartersLocation = $primaryLocation;
        $property = optional($primaryLocation)->property;
        $administration = optional($institution)->administration;
        $members = optional($institution)->members ?? collect();
        $hasMinimumMembers = $members->count() >= 1;

        $branchLocation = null;
        $branchProperty = null;
        $branchLeader = null;
        $branchAddressComplete = false;
        $branchPropertyComplete = false;
        $branchLeaderComplete = false;
        $branchStatusItems = [];

        if ($process->type === Process::TYPE_BRANCH_OPENING) {
            $branchLocation = $process->location;

            if ($branchLocation) {
                $branchAddressComplete = collect(['street', 'number', 'district', 'city', 'uf', 'cep'])
                    ->every(fn (string $field) => filled($branchLocation->{$field}));
            }

            $branchProperty = $branchLocation?->property;
            $branchPropertyComplete = $branchProperty !== null;

            $branchLeader = $branchLocation?->leader;
            $branchLeaderComplete = $branchLeader !== null;

            $branchStatusItems = [
                [
                    'key' => 'location',
                    'title' => 'Endereco da filial',
                    'meta' => $branchAddressComplete ? 'Endereco cadastrado.' : 'Endereco pendente.',
                    'description' => 'Localizacao utilizada em contratos e comunicacoes oficiais.',
                    'complete' => $branchAddressComplete,
                    'action' => route('etika.processes.branch.location.edit', $process),
                    'action_label' => $branchAddressComplete ? 'Revisar endereco' : 'Cadastrar endereco',
                ],
                [
                    'key' => 'property',
                    'title' => 'Dados do imovel',
                    'meta' => $branchPropertyComplete ? 'Informacoes cadastradas.' : 'Dados do imovel pendentes.',
                    'description' => 'Caracteristicas do imovel para documentacao e contratos.',
                    'complete' => $branchPropertyComplete,
                    'action' => route('etika.processes.branch.location.edit', $process) . '#property-section',
                    'action_label' => $branchPropertyComplete ? 'Revisar imovel' : 'Cadastrar imovel',
                ],
                [
                    'key' => 'leader',
                    'title' => 'Dirigente da filial',
                    'meta' => $branchLeaderComplete ? 'Dirigente cadastrado.' : 'Dirigente pendente.',
                    'description' => 'Responsavel legal pela conducao das atividades na filial.',
                    'complete' => $branchLeaderComplete,
                    'action' => route('etika.processes.branch.leader.edit', $process),
                    'action_label' => $branchLeaderComplete ? 'Revisar dirigente' : 'Cadastrar dirigente',
                ],
            ];
        }

        $processLocked = $process->status === Process::STATUS_COMPLETED;

        return view('internal.processes.show', [
            'process' => $process,
            'institution' => $institution,
            'headquartersLocation' => $headquartersLocation,
            'property' => $property,
            'administration' => $administration,
            'members' => $members,
            'hasMinimumMembers' => $hasMinimumMembers,
            'activityLogs' => $activityLogEntries,
            'processLocked' => $processLocked,
            'branchLocation' => $branchLocation,
            'branchProperty' => $branchProperty,
            'branchLeader' => $branchLeader,
            'branchStatusItems' => $branchStatusItems,
            'branchAddressComplete' => $branchAddressComplete,
            'branchPropertyComplete' => $branchPropertyComplete,
            'branchLeaderComplete' => $branchLeaderComplete,
        ]);
    }

    public function approve(Request $request, Process $process): RedirectResponse
    {
        $request->validate([
            'note' => ['nullable', 'string', 'max:500'],
        ]);

        $note = (string) $request->input('note', '');

        return $this->transitionStatus($process, Process::STATUS_COMPLETED, $note, 'Processo aprovado com sucesso.');
    }

    public function reopen(Request $request, Process $process): RedirectResponse
    {
        $request->validate([
            'note' => ['nullable', 'string', 'max:500'],
        ]);

        $note = (string) $request->input('note', '');

        return $this->transitionStatus($process, Process::STATUS_PENDING_UPDATES, $note, 'Processo reaberto e aguardando novas informacoes.');
    }

    private function transitionStatus(Process $process, string $toStatus, ?string $note, string $message): RedirectResponse
    {
        if ($process->status === $toStatus) {
            return redirect()->route('etika.processes.show', $process)
                ->with('status', 'Status ja configurado para este processo.');
        }

        $note = $note !== '' ? $note : null;
        $actor = Auth::guard('internal')->user();
        $fromStatus = $process->status;

        DB::transaction(function () use ($process, $toStatus, $fromStatus, $note, $actor) {
            $process->forceFill(['status' => $toStatus])->save();

            ProcessStatusTimeline::create([
                'process_id' => $process->id,
                'from_status' => $fromStatus,
                'to_status' => $toStatus,
                'actor_internal_id' => $actor?->id,
                'note' => $note,
            ]);

            InternalActivityLog::create([
                'internal_user_id' => $actor?->id,
                'entity' => Process::class,
                'entity_id' => $process->id,
                'action' => 'status_changed',
                'diff' => [
                    'from' => $fromStatus,
                    'to' => $toStatus,
                    'note' => $note,
                ],
            ]);
        });

        return redirect()->route('etika.processes.show', $process)->with('status', $message);
    }

    private function formatLogValue(mixed $value): string
    {
        if ($value === null) {
            return '-';
        }

        if (is_bool($value)) {
            return $value ? 'sim' : 'nao';
        }

        if ($value instanceof \DateTimeInterface) {
            return $value->format('d/m/Y H:i');
        }

        if (is_array($value)) {
            $parts = array_map(function ($item) {
                if (is_scalar($item) || $item === null) {
                    return $this->formatLogValue($item);
                }

                return json_encode($item, JSON_UNESCAPED_UNICODE);
            }, $value);

            return implode(', ', $parts);
        }

        $stringValue = (string) $value;

        return $stringValue === '' ? '-' : $stringValue;
    }

    private function buildMonthlyIndicators(): array
    {
        $start = Carbon::now('America/Sao_Paulo')->startOfMonth();
        $end = (clone $start)->endOfMonth();

        $data = Process::query()
            ->selectRaw('status, COUNT(*) as total')
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('status')
            ->pluck('total', 'status');

        $labels = Process::statusLabels();
        $statuses = [
            Process::STATUS_DRAFT,
            Process::STATUS_COMPLETED,
            Process::STATUS_PENDING_UPDATES,
        ];

        $items = collect($statuses)
            ->map(function ($status) use ($labels, $data) {
                return [
                    'status' => $status,
                    'label' => $labels[$status] ?? $status,
                    'total' => (int) ($data[$status] ?? 0),
                ];
            });

        return [
            'items' => $items,
            'total' => (int) $data->sum(),
        ];
    }

    private function normalizeNumeric(string $value): ?int
    {
        $digits = preg_replace('/\D/', '', $value);
        return $digits === '' ? null : (int) $digits;
    }
}
