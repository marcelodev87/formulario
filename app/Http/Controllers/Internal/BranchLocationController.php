<?php

namespace App\Http\Controllers\Internal;

use App\Http\Controllers\Controller;
use App\Http\Requests\BranchLocationRequest;
use App\Models\InternalActivityLog;
use App\Models\Location;
use App\Models\Process;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class BranchLocationController extends Controller
{
    public function edit(Process $process): View|RedirectResponse
    {
        abort_if($process->type !== Process::TYPE_BRANCH_OPENING, 404);

        if ($redirect = $this->ensureEditable($process)) {
            return $redirect;
        }

        $process->load('institution');

        $institution = $process->institution;
        $location = $process->location()->with('property')->first();

        if (!$location) {
            $location = new Location([
                'institution_id' => $institution->id,
                'process_id' => $process->id,
                'type' => 'branch',
            ]);
        }

        return view('internal.processes.branch.location', [
            'process' => $process,
            'institution' => $institution,
            'location' => $location,
            'property' => $location->property,
            'returnUrl' => route('etika.processes.show', $process),
        ]);
    }

    public function update(BranchLocationRequest $request, Process $process): RedirectResponse
    {
        abort_if($process->type !== Process::TYPE_BRANCH_OPENING, 404);

        if ($redirect = $this->ensureEditable($process)) {
            return $redirect;
        }

        $process->load('institution');

        $institution = $process->institution;
        $data = $request->validated();

        $location = $process->location()->firstOrNew([]);
        $locationBefore = $location->exists ? $location->toArray() : [];
        $propertyBefore = optional($location->property)->toArray() ?? [];

        $location->fill([
            'institution_id' => $institution->id,
            'process_id' => $process->id,
            'type' => 'branch',
            'name' => $data['name'] ?? null,
            'street' => $data['street'],
            'number' => $data['number'],
            'complement' => $data['complement'] ?? null,
            'district' => $data['district'],
            'city' => $data['city'],
            'uf' => strtoupper($data['uf']),
            'cep' => $data['cep'],
        ])->save();

        $propertyData = Arr::only($data, [
            'iptu_registration',
            'built_area_sqm',
            'land_area_sqm',
            'tenure_type',
            'capacity',
            'floors',
            'activity_floor',
            'property_use',
            'property_section',
        ]);

        $property = $location->property()->updateOrCreate([], $propertyData);
        $propertyAfter = $property->fresh()->toArray();

        InternalActivityLog::create([
            'internal_user_id' => Auth::guard('internal')->id(),
            'entity' => Process::class,
            'entity_id' => $process->id,
            'action' => 'branch_location_updated',
            'diff' => [
                'before' => [
                    'location' => $locationBefore,
                    'property' => $propertyBefore,
                ],
                'after' => [
                    'location' => $location->fresh()->toArray(),
                    'property' => $propertyAfter,
                ],
            ],
        ]);

        return redirect()->route('etika.processes.show', $process)
            ->with('status', 'Dados da filial atualizados com sucesso.');
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
