<?php

namespace App\Http\Controllers;

use App\Http\Requests\InstitutionPropertyRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InstitutionPropertyController extends Controller
{
    public function edit(Request $request): View
    {
        $institution = $request->attributes->get('institution');

        abort_unless($institution && $institution->owner_user_id === $request->user()->id, 403);

        $location = $institution->ensureHeadquartersLocation()->load('property');

        return view('institution.property.edit', [
            'institution' => $institution,
            'location' => $location,
            'property' => $location->property,
        ]);
    }

    public function update(InstitutionPropertyRequest $request): RedirectResponse
    {
        $institution = $request->attributes->get('institution');

        abort_unless($institution && $institution->owner_user_id === $request->user()->id, 403);

        $data = $request->validated();

        $location = $institution->ensureHeadquartersLocation();

        $location->property()->updateOrCreate([], $data);

        return redirect()->route('institution.property.edit')->with('status', 'Dados do imovel atualizados com sucesso.');
    }
}
