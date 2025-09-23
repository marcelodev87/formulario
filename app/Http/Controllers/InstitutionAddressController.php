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

        return view('institution.address.edit', [
            'institution' => $institution,
        ]);
    }

    public function update(InstitutionAddressRequest $request): RedirectResponse
    {
        $institution = $request->attributes->get('institution');

        abort_unless($institution && $institution->owner_user_id === $request->user()->id, 403);

        $data = $request->validated();

        $institution->update([
            'street' => $data['street'],
            'number' => $data['number'],
            'complement' => $data['complement'] ?? null,
            'district' => $data['district'],
            'city' => $data['city'],
            'uf' => $data['uf'],
            'cep' => $data['cep'],
        ]);

        return redirect()->route('institution.address.edit')->with('status', 'Endereco atualizado com sucesso.');
    }
}
