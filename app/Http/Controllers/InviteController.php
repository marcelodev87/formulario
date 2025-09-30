<?php

namespace App\Http\Controllers;

use App\Http\Requests\MemberRequest;
use App\Models\Invite;
use App\Models\Member;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class InviteController extends Controller
{
    public function __construct(private readonly ActivityLogger $activityLogger)
    {
    }

    public function showPublicForm(Invite $invite): View
    {
        if ($invite->isExpired()) {
            $invite->update(['status' => 'expired']);

            return redirect()->route('auth.error')->with('error', 'Convite expirado. Solicite um novo link à instituição.');
        }

        return view('invite.form', [
            'invite' => $invite,
            'institution' => $invite->institution,
        ]);
    }

    public function storeMember(MemberRequest $request, Invite $invite): RedirectResponse
    {
        if ($invite->isExpired()) {
            $invite->update(['status' => 'expired']);

            return redirect()->route('auth.error')->with('error', 'Convite expirado. Solicite um novo link à instituição.');
        }

        $validated = $request->validated();
        $validated['uf'] = strtoupper($validated['uf']);
        $validated['process_id'] = $invite->process_id;

        $member = $invite->institution->members()->create($validated);

        $this->activityLogger->log(
            actor: null,
            institution: $invite->institution,
            entityType: Member::class,
            entityId: $member->id,
            action: 'created',
            before: [],
            after: $member->toArray()
        );

        return redirect()->route('invite.confirmation', ['invite' => $invite->key]);
    }

    public function showConfirmation(Invite $invite): View
    {
        return view('invite.confirmation', [
            'institution' => $invite->institution,
        ]);
    }
}
