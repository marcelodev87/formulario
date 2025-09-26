<?php

namespace App\Http\Controllers;

use App\Http\Requests\InstitutionAddressRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InstitutionAddressController extends Controller
{
    public function edit(Request $request): View
    {
        $institution = $request->attributes->get('institution');

        abort_unless($institution && $institution->owner_user_id === $request->user()->id, 403);

        $location = $institution->ensureHeadquartersLocation();

        return view('institution.address.edit', [
            'institution' => $institution,
            'location' => $location,
        ]);
    }

    public function update(InstitutionAddressRequest $request): RedirectResponse
    {
        $institution = $request->attributes->get('institution');

        abort_unless($institution && $institution->owner_user_id === $request->user()->id, 403);

        $data = $request->validated();

        $location = $institution->ensureHeadquartersLocation();

        $location->fill([
            'street' => $data['street'],
            'number' => $data['number'],
            'complement' => $data['complement'] ?? null,
            'district' => $data['district'],
            'city' => $data['city'],
            'uf' => $data['uf'],
            'cep' => $data['cep'],
        ])->save();

        $location->syncInstitutionAddressIfHeadquarters();

        return redirect()->route('institution.address.edit')->with('status', 'Endereco atualizado com sucesso.');
    }
}
