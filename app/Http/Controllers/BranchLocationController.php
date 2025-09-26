<?php

namespace App\Http\Controllers;

use App\Http\Requests\BranchLocationRequest;
use App\Models\Location;
use App\Models\Process;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\View\View;

class BranchLocationController extends Controller
{
    public function edit(Request $request, Process $process): View
    {
        abort_if($process->type !== Process::TYPE_BRANCH_OPENING, 404);

        $this->authorize('view', $process);

        $institution = $process->institution()->with('branchLocations')->firstOrFail();

        abort_unless($institution->owner_user_id === $request->user()->id, 403);

        $location = $process->location()->with('property')->first();

        if (!$location) {
            $location = new Location([
                'institution_id' => $institution->id,
                'process_id' => $process->id,
                'type' => 'branch',
            ]);
        }

        return view('processes.branch.location', [
            'process' => $process,
            'institution' => $institution,
            'location' => $location,
            'property' => $location->property,
        ]);
    }

    public function update(BranchLocationRequest $request, Process $process): RedirectResponse
    {
        abort_if($process->type !== Process::TYPE_BRANCH_OPENING, 404);

        $this->authorize('view', $process);

        $institution = $process->institution;

        abort_unless($institution && $institution->owner_user_id === $request->user()->id, 403);

        $data = $request->validated();

        $location = $process->location()->firstOrNew([]);

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
            'uf' => $data['uf'],
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

        $location->property()->updateOrCreate([], $propertyData);

        return redirect()
            ->route('processes.branch.location.edit', $process)
            ->with('status', 'Dados da filial atualizados com sucesso.');
    }
}
