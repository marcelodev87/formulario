<?php

namespace App\Http\Controllers;

use App\Models\Process;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class OpeningProcessController extends Controller
{
    public function show(Request $request, Process $process): View
    {
        if ($process->type !== Process::TYPE_INSTITUTION_OPENING) {
            abort(404);
        }

        $this->authorize('view', $process);

        $institution = $process->institution;

        // Carrega membros do PROCESSO, não da instituição
        $members = $process->members()->orderBy('name')->get();

        // Carrega localização do PROCESSO (se existir)
        $location = $process->location;

        // Se não houver localização do processo, trata como vazia
        if (!$location) {
            $location = null;
        }

        $activeInvite = $institution->invites()->active()->latest()->first();

        if (!$activeInvite) {
            $activeInvite = $institution->invites()->create([
                'key' => Str::uuid()->toString(),
                'status' => 'active',
                'expires_at' => null,
            ]);
        }

        $inviteUrl = url(route('invite.form', ['invite' => $activeInvite->key], false));

        $recentActivity = $institution->activityLogs()
            ->with('actor')
            ->latest('created_at')
            ->take(10)
            ->get();

        $hasMinimumMembers = $members->count() >= 1;

        $internalMemberUrl = route('invite.members.internal', [
            'redirect_to' => $process->type,
            'process_id' => $process->id,
        ]);

        return view('dashboard.index', [
            'institution' => $institution,
            'members' => $members,
            'inviteUrl' => $inviteUrl,
            'inviteKey' => $activeInvite->key,
            'recentActivity' => $recentActivity,
            'hasMinimumMembers' => $hasMinimumMembers,
            'process' => $process,
            'headquartersLocation' => $location,
            'internalMemberUrl' => $internalMemberUrl,
        ]);
    }
}






