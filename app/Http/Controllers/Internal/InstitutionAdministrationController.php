<?php

namespace App\Http\Controllers\Internal;

use App\Http\Controllers\Controller;
use App\Http\Requests\InstitutionAdministrationRequest;
use App\Models\InternalActivityLog;
use App\Models\Process;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class InstitutionAdministrationController extends Controller
{
    public function edit(Process $process): View|RedirectResponse
    {
        if ($redirect = $this->ensureEditable($process)) {
            return $redirect;
        }

        $process->load('institution.administration');

        $institution = $process->institution;
        $administration = $institution?->administration;

        return view('internal.processes.administration.edit', [
            'process' => $process,
            'institution' => $institution,
            'administration' => $administration,
            'returnUrl' => route('etika.processes.show', $process),
        ]);
    }

    public function update(InstitutionAdministrationRequest $request, Process $process): RedirectResponse
    {
        if ($redirect = $this->ensureEditable($process)) {
            return $redirect;
        }

        $process->load('institution.administration');

        $institution = $process->institution;
        $administration = $institution?->administration;

        $data = $request->validated();

        $payload = [
            'dissolution_mode' => $data['dissolution_mode'],
            'governance_model' => $data['governance_model'],
            'president_term_indefinite' => $data['president_term_type'] === 'indefinite',
            'president_term_years' => $data['president_term_type'] === 'years' ? $data['president_term_years'] : null,
            'board_term_years' => $data['board_term_years'],
            'ordination_decision' => $data['ordination_decision'],
            'financial_responsible' => $data['financial_responsible'],
            'ministerial_roles' => $data['ministerial_roles'] ?? [],
            'stipend_policy' => $data['stipend_policy'],
        ];

        $before = $administration?->toArray() ?? [];

        $record = $institution->administration()->updateOrCreate([], $payload);

        $after = $record->fresh()->toArray();

        InternalActivityLog::create([
            'internal_user_id' => Auth::guard('internal')->id(),
            'entity' => Process::class,
            'entity_id' => $process->id,
            'action' => 'administration_updated',
            'diff' => [
                'before' => $before,
                'after' => $after,
            ],
        ]);

        return redirect()->route('etika.processes.show', $process)
            ->with('status', 'Dados administrativos atualizados com sucesso.');
    }

    private function ensureEditable(Process $process): ?RedirectResponse
    {
        if ($process->status === Process::STATUS_COMPLETED) {
            return redirect()->route('etika.processes.show', $process)
                ->withErrors(['process' => 'Este processo esta aprovado e nao pode ser editado. Reabra o processo para realizar alteracoes.']);
        }

        return null;
    }
}
