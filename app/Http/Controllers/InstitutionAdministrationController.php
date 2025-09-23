<?php

namespace App\Http\Controllers;

use App\Http\Requests\InstitutionAdministrationRequest;
use App\Models\InstitutionAdministration;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class InstitutionAdministrationController extends Controller
{
    public function edit(Request $request): View
    {
        $institution = $request->attributes->get('institution');

        abort_unless($institution && $institution->owner_user_id === $request->user()->id, 403);

        $administration = $institution->administration;

        return view('administration.form', [
            'institution' => $institution,
            'administration' => $administration,
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

        return redirect()->route('administration.edit')->with('status', 'Dados administrativos atualizados com sucesso.');
    }
}