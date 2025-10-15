<?php

namespace App\Http\Controllers\Internal;

use App\Http\Controllers\Controller;
use App\Http\Requests\InstitutionAddressRequest;
use App\Models\InternalActivityLog;
use App\Models\Location;
use App\Models\Process;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class InstitutionAddressController extends Controller
{
    public function edit(Process $process): View|RedirectResponse
    {
        if ($redirect = $this->ensureEditable($process)) {
            return $redirect;
        }

        $process->load('institution.headquartersLocation', 'location');

        $institution = $process->institution;
        $location = $this->makeLocationPrototype($process);

        return view('internal.processes.address.edit', [
            'process' => $process,
            'institution' => $institution,
            'location' => $location,
            'returnUrl' => route('etika.processes.show', $process),
        ]);
    }

    public function update(InstitutionAddressRequest $request, Process $process): RedirectResponse
    {
        if ($redirect = $this->ensureEditable($process)) {
            return $redirect;
        }

        $process->load('institution');

        $location = $process->location()->firstOrNew([]);
        $before = $location->exists ? $location->toArray() : [];

        $data = $request->validated();

        $location->fill([
            'institution_id' => $process->institution_id,
            'process_id' => $process->id,
            'type' => $location->type ?? 'process',
            'street' => $data['street'],
            'number' => $data['number'],
            'complement' => $data['complement'] ?? null,
            'district' => $data['district'],
            'city' => $data['city'],
            'uf' => strtoupper($data['uf']),
            'cep' => $data['cep'],
        ])->save();

        $after = $location->fresh()->only(['street', 'number', 'complement', 'district', 'city', 'uf', 'cep']);

        InternalActivityLog::create([
            'internal_user_id' => Auth::guard('internal')->id(),
            'entity' => Process::class,
            'entity_id' => $process->id,
            'action' => 'address_updated',
            'diff' => [
                'before' => $before,
                'after' => $after,
            ],
        ]);

        return redirect()->route('etika.processes.show', $process)
            ->with('status', 'Endereco atualizado com sucesso.');
    }

    private function makeLocationPrototype(Process $process): Location
    {
        $location = $process->location;

        if ($location) {
            return $location;
        }

        $institution = $process->institution;
        $base = $institution?->headquartersLocation;

        $attributes = [
            'institution_id' => $process->institution_id,
            'process_id' => $process->id,
            'type' => 'process',
        ];

        if ($base) {
            foreach (['name', 'street', 'number', 'complement', 'district', 'city', 'uf', 'cep'] as $field) {
                $attributes[$field] = $base->{$field};
            }
        }

        return new Location($attributes);
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
