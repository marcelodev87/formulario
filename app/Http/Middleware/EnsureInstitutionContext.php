<?php

namespace App\Http\Middleware;

use App\Models\Process;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class EnsureInstitutionContext
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return $next($request);
        }

        $institution = $user->institution;

        if (!$institution) {
            return redirect()->route('setup.create');
        }

        $institution->ensureHeadquartersLocation();

        $request->attributes->set('institution', $institution);
        View::share('currentInstitution', $institution);

        $process = null;
        $isLocked = false;

        $currentProcessParam = $request->route('process');
        if ($currentProcessParam instanceof Process) {
            $process = $currentProcessParam;
            $isLocked = $process->status === Process::STATUS_COMPLETED;
        }

        $request->attributes->set('institution_process', $process);
        $request->attributes->set('institution_process_locked', $isLocked);
        View::share('institutionProcess', $process);
        View::share('institutionProcessLocked', $isLocked);

        if ($isLocked && !in_array($request->method(), ['GET', 'HEAD', 'OPTIONS'], true)) {
            return redirect()->route('dashboard')
                ->withErrors(['process' => 'Este processo foi aprovado e nao pode ser editado no momento.']);
        }

        return $next($request);
    }
}
