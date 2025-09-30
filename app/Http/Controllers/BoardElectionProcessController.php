<?php

namespace App\Http\Controllers;

use App\Models\Mandate;
use App\Models\Process;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
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
        ]);
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

        return [
            'institution' => $institution,
            'shareUrl' => $shareUrl,
            'members' => $members,
            'missingRoles' => $missingRoles,
            'hasRequiredBoard' => $hasRequiredBoard,
            'requiredRoles' => $requiredRoles,
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
     *     hasRequiredBoard: bool
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

        $membersDescription = $hasRequiredBoard
            ? 'Cargos essenciais preenchidos para registrar a ata.'
            : 'Ainda faltam os cargos: ' . implode(', ', $missingRoles) . '.';
        if ($membersCount === 0) {
            $membersDescription = 'Cadastre os membros da diretoria responsaveis pela ata.';
        }

        $mandate = Mandate::where('process_id', $process->id)->latest()->first();
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
            $documentsMeta = 'Nenhum documento anexado.';
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
                'action' => '#modal-mandate-config',
                'action_label' => $mandateComplete ? 'Atualizar mandato' : 'Configurar mandato',
            ],
            [
                'key' => 'documents',
                'icon' => 'building',
                'title' => 'Documentos anexados',
                'meta' => $documentsMeta,
                'description' => 'Anexe a ultima ata registrada e o estatuto vigente.',
                'complete' => $documentsComplete,
                'action' => '#modal-upload-minutes',
                'action_label' => $documentsComplete ? 'Gerenciar anexos' : 'Anexar documentos',
            ],
        ];
    }
}

