<?php

namespace App\Http\Controllers;

use App\Models\Process;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProcessController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $institution = $request->user()->institution;

        abort_unless($institution && $institution->owner_user_id === $request->user()->id, 403);

        $typeDefinitions = Process::typeDefinitions();

        $validated = $request->validate([
            'type' => ['required', Rule::in(array_keys($typeDefinitions))],
            'title' => ['nullable', 'string', 'max:255'],
        ]);

        if ($validated['type'] === Process::TYPE_INSTITUTION_OPENING) {
            $existing = Process::forInstitutionAndType($institution, Process::TYPE_INSTITUTION_OPENING);

            if ($existing) {
                return redirect()->route('processes.show', $existing)
                    ->with('status', 'O processo de abertura ja esta em andamento.');
            }
        }

        $process = $institution->processes()->create([
            'type' => $validated['type'],
            'title' => $validated['title'] !== null
                ? trim($validated['title'])
                : Process::defaultTitleForType($validated['type']),
            'status' => Process::STATUS_IN_PROGRESS,
        ]);

        // Redireciona para a tela de configuração inicial do processo, conforme o tipo
        switch ($process->type) {
            case Process::TYPE_INSTITUTION_OPENING:
                return redirect()->route('processes.opening.show', $process)
                    ->with('status', 'Processo criado com sucesso.');
            case Process::TYPE_BRANCH_OPENING:
                return redirect()->route('processes.branch.show', $process)
                    ->with('status', 'Processo criado com sucesso.');
            case 'bylaws_revision':
                return redirect()->route('processes.bylaws_revision.show', $process)
                    ->with('status', 'Processo criado com sucesso.');
            default:
                return redirect()->route('dashboard')->with('error', 'Este processo ainda nao possui paginas configuradas.');
        }
    }

    public function show(Process $process): RedirectResponse
    {
        $this->authorize('view', $process);

        return match ($process->type) {
            Process::TYPE_INSTITUTION_OPENING => redirect()->route('processes.opening.show', $process),
            Process::TYPE_BRANCH_OPENING => redirect()->route('processes.branch.show', $process),
            'bylaws_revision' => redirect()->route('processes.bylaws_revision.show', $process),
            default => redirect()->route('dashboard')->with('error', 'Este processo ainda nao possui paginas configuradas.'),
        };
    }
}
