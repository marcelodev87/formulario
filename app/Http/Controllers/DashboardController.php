<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $institution = $user->institution()->with([
            'members' => function ($query) {
                $query->orderBy('name');
            },
            'property',
            'administration',
        ])->firstOrFail();

        $this->authorize('view', $institution);

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
        ]);
    }
}
