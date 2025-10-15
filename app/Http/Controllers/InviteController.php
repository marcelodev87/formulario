<?php

namespace App\Http\Controllers;

use App\Http\Requests\MemberRequest;
use App\Models\Invite;
use App\Models\Member;
use App\Models\Process;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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

    public function showInternalMemberForm(Request $request): View
    {
        $institution = $request->attributes->get('institution');
        $user = $request->user();

        abort_unless($institution && $institution->owner_user_id === ($user?->id), 403);

        $processId = $this->sanitizeProcessId($institution, $request->input('process_id'));
        $redirectTo = $request->input('redirect_to');

        $redirectParams = array_filter([
            'redirect_to' => $redirectTo,
            'process_id' => $processId,
        ], fn ($value) => $value !== null && $value !== '');

        return view('invite.internal_form', [
            'institution' => $institution,
            'processId' => $processId,
            'redirectParams' => $redirectParams,
        ]);
    }

    public function storeInternalMember(MemberRequest $request): RedirectResponse
    {
        $institution = $request->attributes->get('institution');
        $user = $request->user();

        abort_unless($institution && $institution->owner_user_id === ($user?->id), 403);

        $validated = $request->validated();
        $validated['uf'] = strtoupper($validated['uf']);

        $processId = $this->sanitizeProcessId($institution, $request->input('process_id'));
        $validated['process_id'] = $processId;
        $validated['institution_id'] = $institution->id;

        $member = $institution->members()->create($validated);

        $this->activityLogger->log(
            actor: $user,
            institution: $institution,
            entityType: Member::class,
            entityId: $member->id,
            action: 'created',
            before: [],
            after: $member->toArray()
        );

        $redirectParams = array_filter([
            'redirect_to' => $request->input('redirect_to'),
            'process_id' => $processId,
        ], fn ($value) => $value !== null && $value !== '');

        $redirectUrl = $this->resolveRedirect($institution, $redirectParams, 'dashboard');

        return redirect($redirectUrl)->with('status', 'Membro cadastrado com sucesso.');
    }

    public function showConfirmation(Invite $invite): View
    {
        return view('invite.confirmation', [
            'institution' => $invite->institution,
        ]);
    }

    private function sanitizeProcessId($institution, $processId): ?int
    {
        if (!is_numeric($processId)) {
            return null;
        }

        $id = (int) $processId;

        return $institution->processes()->whereKey($id)->exists() ? $id : null;
    }

    private function resolveRedirect($institution, array $redirectParams, string $fallbackRoute, array $fallbackParameters = []): string
    {
        $processId = $redirectParams['process_id'] ?? null;
        $redirectTo = $redirectParams['redirect_to'] ?? null;

        if ($processId && $redirectTo) {
            $process = $institution->processes()->whereKey($processId)->first();

            if ($process) {
                return match ($redirectTo) {
                    Process::TYPE_INSTITUTION_OPENING => route('processes.opening.show', $process),
                    Process::TYPE_BOARD_ELECTION_MINUTES_REGISTRATION => route('processes.board_election.dashboard', $process),
                    default => route('processes.show', $process),
                };
            }
        }

        return route($fallbackRoute, $fallbackParameters);
    }
}
