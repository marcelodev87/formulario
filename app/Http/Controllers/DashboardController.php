<?php

namespace App\Http\Controllers;

use App\Models\Process;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $institution = $user->institution()->with('processes')->firstOrFail();

        $this->authorize('view', $institution);

        $openingProcess = Process::forInstitutionAndType($institution, Process::TYPE_INSTITUTION_OPENING);

        if (!$openingProcess) {
            $openingProcess = $institution->processes()->create([
                'type' => Process::TYPE_INSTITUTION_OPENING,
                'title' => Process::defaultTitleForType(Process::TYPE_INSTITUTION_OPENING),
                'status' => Process::STATUS_IN_PROGRESS,
            ]);
        }

        $processes = $institution->processes()->latest()->get();
        $typeDefinitions = Process::typeDefinitions();

        return view('dashboard.processes', [
            'institution' => $institution,
            'processes' => $processes,
            'typeDefinitions' => $typeDefinitions,
            'openingProcess' => $openingProcess,
        ]);
    }
}
