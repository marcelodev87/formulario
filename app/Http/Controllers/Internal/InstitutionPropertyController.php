<?php

namespace App\Http\Controllers\Internal;

use App\Http\Controllers\Controller;
use App\Http\Requests\InstitutionPropertyRequest;
use App\Models\InternalActivityLog;
use App\Models\Location;
use App\Models\Process;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class InstitutionPropertyController extends Controller
{
    public function edit(Process $process): View|RedirectResponse
    {
        if ($redirect = $this->ensureEditable($process)) {
            return $redirect;
        }

        $process->load('institution.headquartersLocation', 'location.property');

        $institution = $process->institution;
        $location = $this->makeLocationPrototype($process);
        $property = $location->property;

        return view('internal.processes.property.edit', [
            'process' => $process,
            'institution' => $institution,
            'location' => $location,
            'property' => $property,
            'returnUrl' => route('etika.processes.show', $process),
        ]);
    }

    public function update(InstitutionPropertyRequest $request, Process $process): RedirectResponse
    {
        if ($redirect = $this->ensureEditable($process)) {
            return $redirect;
        }

        $process->load('institution');

        $location = $process->location()->firstOrNew([]);
        if (!$location->exists) {
            $location->institution_id = $process->institution_id;
            $location->process_id = $process->id;
            $location->type = 'process';
            $location->save();
        } elseif (!$location->process_id) {
            $location->process_id = $process->id;
            $location->save();
        }

        $data = $request->validated();

        $before = optional($location->property)->toArray() ?? [];

        $property = $location->property()->updateOrCreate([], $data);

        InternalActivityLog::create([
            'internal_user_id' => Auth::guard('internal')->id(),
            'entity' => Process::class,
            'entity_id' => $process->id,
            'action' => 'property_updated',
            'diff' => [
                'before' => $before,
                'after' => $property->fresh()->toArray(),
            ],
        ]);

        return redirect()->route('etika.processes.show', $process)
            ->with('status', 'Dados do imovel atualizados com sucesso.');
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
