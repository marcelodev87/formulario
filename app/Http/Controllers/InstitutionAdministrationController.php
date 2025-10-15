<?php

namespace App\Http\Controllers;

use App\Http\Requests\InstitutionAdministrationRequest;
use App\Models\Process;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InstitutionAdministrationController extends Controller
{
    public function edit(Request $request): View
    {
        $institution = $request->attributes->get('institution');

        abort_unless($institution && $institution->owner_user_id === $request->user()->id, 403);

        $administration = $institution->administration;
        $redirectParams = $this->sanitizeRedirectParams($request->query());
        $returnUrl = $this->resolveTargetRoute($institution, $redirectParams, 'dashboard');

        return view('administration.form', [
            'institution' => $institution,
            'administration' => $administration,
            'redirectParams' => $redirectParams,
            'returnUrl' => $returnUrl,
        ]);
    }

    public function store(InstitutionAdministrationRequest $request): RedirectResponse
    {
        $institution = $request->attributes->get('institution');

        abort_unless($institution && $institution->owner_user_id === $request->user()->id, 403);

        $data = $request->validated();
        $presidentTermYears = $data['president_term_type'] === 'years' ? $data['president_term_years'] : null;

        $institution->administration()->updateOrCreate([], [
            'dissolution_mode' => $data['dissolution_mode'],
            'governance_model' => $data['governance_model'],
            'president_term_indefinite' => $data['president_term_type'] === 'indefinite',
            'president_term_years' => $presidentTermYears,
            'board_term_years' => $data['board_term_years'],
            'ordination_decision' => $data['ordination_decision'],
            'financial_responsible' => $data['financial_responsible'],
            'ministerial_roles' => $data['ministerial_roles'] ?? [],
            'stipend_policy' => $data['stipend_policy'],
        ]);

        $redirectParams = $this->sanitizeRedirectParams($request->input());
        $redirectUrl = $this->resolveTargetRoute($institution, $redirectParams, 'administration.edit', $redirectParams);

        return redirect($redirectUrl)->with('status', 'Dados administrativos atualizados com sucesso.');
    }

    /**
     * @param  array<string, mixed>  $source
     * @return array<string, int|string>
     */
    private function sanitizeRedirectParams(array $source): array
    {
        $redirectTo = isset($source['redirect_to']) ? (string) $source['redirect_to'] : null;
        $processId = isset($source['process_id']) && is_numeric($source['process_id'])
            ? (int) $source['process_id']
            : null;

        return array_filter([
            'redirect_to' => $redirectTo,
            'process_id' => $processId,
        ], fn ($value) => $value !== null && $value !== '');
    }

    /**
     * @param  array<string, int|string>  $redirectParams
     */
    private function resolveTargetRoute($institution, array $redirectParams, string $fallbackRoute, array $fallbackParameters = []): string
    {
        $processId = $redirectParams['process_id'] ?? null;
        $redirectTo = $redirectParams['redirect_to'] ?? null;

        if ($processId && $redirectTo) {
            $process = $institution->processes()->whereKey($processId)->first();

            if ($process) {
                return match ($redirectTo) {
                    Process::TYPE_INSTITUTION_OPENING => route('processes.opening.show', $process),
                    Process::TYPE_BOARD_ELECTION_MINUTES_REGISTRATION => route('processes.board_election.dashboard', $process),
                    default => route('processes.show', $process),
                };
            }
        }

        return route($fallbackRoute, $fallbackParameters);
    }
}





