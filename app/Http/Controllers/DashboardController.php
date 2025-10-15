<?php

namespace App\Http\Controllers;

use App\Models\Process;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\View\View as ViewResponse;

class DashboardController extends Controller
{
    public function index(Request $request): ViewResponse
    {
        $user = $request->user();
        $institution = $user?->institution()->with('processes')->first();

        if (!$institution) {
            View::share('currentInstitution', null);

            return view('dashboard.empty', [
                'currentInstitution' => null,
            ]);
        }

        $this->authorize('view', $institution);
        View::share('currentInstitution', $institution);

        $processes = $institution->processes()->latest()->get();
        $typeDefinitions = Process::typeDefinitions();

        return view('dashboard.processes', [
            'institution' => $institution,
            'processes' => $processes,
            'typeDefinitions' => $typeDefinitions,
        ]);
    }
}
