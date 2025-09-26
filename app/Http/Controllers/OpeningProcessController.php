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

        $institution = $process->institution()->with([
            'members' => function ($query) {
                $query->orderBy('name');
            },
            'administration',
            'headquartersLocation.property',
        ])->firstOrFail();

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

        $hasMinimumMembers = $institution->members->count() >= 1;

        return view('dashboard.index', [
            'institution' => $institution,
            'members' => $institution->members,
            'inviteUrl' => $inviteUrl,
            'recentActivity' => $recentActivity,
            'hasMinimumMembers' => $hasMinimumMembers,
            'process' => $process,
            'headquartersLocation' => $institution->headquartersLocation,
        ]);
    }
}
