<?php

namespace App\Http\Controllers;

use App\Models\Mandate;
use App\Models\Process;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class BoardElectionProcessController extends Controller
{
    public function dashboard(Request $request, Process $process): View
    {
        $this->authorize('view', $process);

        $process = $this->ensureBoardElectionProcess($process);

        $context = $this->resolveContext($process);
        $statusItems = $this->buildStatusItems($process, $context);

        return view('processes.board_election.dashboard', [
            'process' => $process,
            'institution' => $context['institution'],
            'shareUrl' => $context['shareUrl'],
            'statusItems' => $statusItems,
            'members' => $context['members'],
            'mandate' => $context['mandate'],
        ]);
    }

    public function storeMandate(Request $request, Process $process): RedirectResponse
    {
        $this->authorize('view', $process);

        $process = $this->ensureBoardElectionProcess($process);

        $validated = $request->validate([
            'mandate_start' => ['required', 'date'],
            'mandate_duration' => ['required', 'integer', 'min:1', 'max:10'],
        ]);

        Mandate::updateOrCreate(
            ['process_id' => $process->id],
            [
                'start_date' => $validated['mandate_start'],
                'duration_years' => $validated['mandate_duration'],
            ]
        );

        return redirect()->route('processes.board_election.dashboard', $process)
            ->with('status', 'Mandato atualizado com sucesso.');
    }



    public function storeDocuments(Request $request, Process $process): RedirectResponse
    {
        $this->authorize('view', $process);

        $process = $this->ensureBoardElectionProcess($process);

        $validated = $request->validate([
            'minutes_file' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:10240'],
            'bylaws_file' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:10240'],
        ]);

        $answers = $process->answers ?? [];

        if ($request->hasFile('minutes_file')) {
            if (!empty($answers['minutes_file'])) {
                Storage::disk('public')->delete($answers['minutes_file']);
            }
            $answers['minutes_file'] = $request->file('minutes_file')->store('board_election/minutes', 'public');
        }

        if ($request->hasFile('bylaws_file')) {
            if (!empty($answers['bylaws_file'])) {
                Storage::disk('public')->delete($answers['bylaws_file']);
            }
            $answers['bylaws_file'] = $request->file('bylaws_file')->store('board_election/bylaws', 'public');
        }

        $process->answers = $answers;
        $process->save();

        return redirect()->route('processes.board_election.dashboard', $process)
            ->with('status', 'Documentos salvos com sucesso.');
    }

    public function members(Request $request, Process $process): View
    {
        $this->authorize('view', $process);

        $process = $this->ensureBoardElectionProcess($process);

        $context = $this->resolveContext($process);

        return view('processes.board_election.members', [
            'process' => $process,
            'institution' => $context['institution'],
            'shareUrl' => $context['shareUrl'],
            'members' => $context['members'],
            'missingRoles' => $context['missingRoles'],
            'hasRequiredBoard' => $context['hasRequiredBoard'],
            'requiredRoles' => $context['requiredRoles'],
        ]);
    }

    private function ensureBoardElectionProcess(Process $process): Process
    {
        abort_if($process->type !== Process::TYPE_BOARD_ELECTION_MINUTES_REGISTRATION, 404);

        $process->loadMissing([
            'institution',
            'members' => fn ($query) => $query->orderBy('name'),
        ]);

        return $process;
    }

    /**
     * @return array{
     *     institution: \App\Models\Institution,
     *     shareUrl: string,
     *     members: \Illuminate\Support\Collection<int, \App\Models\Member>,
     *     missingRoles: array<int, string>,
     *     hasRequiredBoard: bool,
     *     mandate: ?\App\Models\Mandate,
     *     requiredRoles: array<int, string>
     * }
     */
    private function resolveContext(Process $process): array
    {
        $institution = $process->institution;

        $activeInvite = $institution->invites()
            ->where('process_id', $process->id)
            ->active()
            ->latest()
            ->first();
        if (!$activeInvite) {
            $activeInvite = $institution->invites()->create([
                'process_id' => $process->id,
                'key' => Str::uuid()->toString(),
                'status' => 'active',
                'expires_at' => null,
            ]);
        }

        $shareUrl = url(route('invite.form', ['invite' => $activeInvite->key], false));

        $members = $process->members
            ->sortBy(fn ($member) => mb_strtolower($member->name))
            ->values();

        $requiredRoles = $this->requiredRoles();
        $missingRoles = collect($requiredRoles)
            ->reject(fn (string $role) => $members->contains('role', $role))
            ->values()
            ->all();
        $hasRequiredBoard = $missingRoles === [];


        $mandate = Mandate::where('process_id', $process->id)->latest()->first();
        return [
            'institution' => $institution,
            'shareUrl' => $shareUrl,
            'members' => $members,
            'missingRoles' => $missingRoles,
            'hasRequiredBoard' => $hasRequiredBoard,
            'requiredRoles' => $requiredRoles,
            'mandate' => $mandate,
        ];
    }

    /**
     * @return array<int, string>
     */
    private function requiredRoles(): array
    {
        return [
            'Presidente',
            'Vice Presidente',
            'Tesoureiro',
            'Secretario',
        ];
    }

    /**
     * @param array{
     *     institution: \App\Models\Institution,
     *     shareUrl: string,
     *     members: \Illuminate\Support\Collection<int, \App\Models\Member>,
     *     missingRoles: array<int, string>,
     *     hasRequiredBoard: bool,
     *     mandate: ?\App\Models\Mandate
     * } $context
     *
     * @return array<int, array<string, mixed>>
     */
    private function buildStatusItems(Process $process, array $context): array
    {
        $members = $context['members'];
        $membersCount = $members->count();
        $missingRoles = $context['missingRoles'];
        $hasRequiredBoard = $context['hasRequiredBoard'];

        $mandate = $context['mandate'];
        $membersDescription = $hasRequiredBoard
            ? 'Cargos essenciais preenchidos para registrar a ata.'
            : 'Ainda faltam os cargos: ' . implode(', ', $missingRoles) . '.';
        if ($membersCount === 0) {
            $membersDescription = 'Cadastre os membros da diretoria responsaveis pela ata.';
        }

        $mandateComplete = $mandate !== null;

        if ($mandateComplete) {
            $mandateStart = optional($mandate->start_date)->format('d/m/Y');
            $mandateDuration = $mandate->duration_years
                ? $mandate->duration_years . ' ano(s)'
                : 'duracao indefinida';

            $mandateMeta = sprintf(
                'Inicio em %s por %s.',
                $mandateStart ?? 'data indefinida',
                $mandateDuration
            );
        } else {
            $mandateMeta = 'Configurar inicio e duracao do mandato.';
        }

        $answers = $process->answers ?? [];
        $hasMinutes = !empty($answers['minutes_file']);
        $hasBylaws = !empty($answers['bylaws_file']);

        if ($hasMinutes && $hasBylaws) {
            $documentsMeta = 'Ata e estatuto anexados.';
        } elseif ($hasMinutes) {
            $documentsMeta = 'Ata anexada; estatuto pendente.';
        } elseif ($hasBylaws) {
            $documentsMeta = 'Estatuto anexado; ata pendente.';
        } else {
            $documentsMeta = 'Nenhum documento enviado.';
        }

        $documentsComplete = $hasMinutes && $hasBylaws;

        return [
            [
                'key' => 'board_members',
                'icon' => 'user',
                'title' => 'Diretoria cadastrada',
                'meta' => 'Membros cadastrados: ' . $membersCount,
                'description' => $membersDescription,
                'complete' => $hasRequiredBoard,
                'action' => route('processes.board_election.members', $process),
                'action_label' => $hasRequiredBoard ? 'Gerenciar diretoria' : 'Cadastrar membros',
            ],
            [
                'key' => 'mandate',
                'icon' => 'pin',
                'title' => 'Mandato da diretoria',
                'meta' => $mandateMeta,
                'description' => 'Defina vigencia atual antes de emitir a ata.',
                'complete' => $mandateComplete,
                'action' => null,
                'modal' => 'mandate',
                'action_label' => $mandateComplete ? 'Atualizar mandato' : 'Configurar mandato',
            ],
            [
                'key' => 'documents',
                'icon' => 'building',
                'title' => 'Documentos anexados',
                'meta' => $documentsMeta,
                'description' => 'Anexe a ultima ata registrada e o estatuto vigente.',
                'complete' => $documentsComplete,
                'action' => null,
                'modal' => 'documents',
                'action_label' => $documentsComplete ? 'Gerenciar anexos' : 'Anexar documentos',
            ],
        ];
    }
}


