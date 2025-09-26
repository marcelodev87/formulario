<?php

namespace App\Http\Controllers;

use App\Http\Requests\MemberRequest;
use App\Models\Institution;
use App\Models\Member;
use App\Models\Process;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MemberController extends Controller
{
    public function __construct(private readonly ActivityLogger $activityLogger)
    {
    }

    public function index(Request $request): RedirectResponse
    {
        $this->authorize('viewAny', Member::class);

        return $this->redirectToOpeningProcess($request->user()->institution);
    }

    public function edit(Member $member): View
    {
        $this->authorize('update', $member);

        return view('members.edit', [
            'member' => $member,
        ]);
    }

    public function update(MemberRequest $request, Member $member): RedirectResponse
    {
        $this->authorize('update', $member);

        $before = $member->toArray();
        $data = $request->validated();
        $data['uf'] = strtoupper($data['uf']);

        $member->update($data);

        $this->activityLogger->log(
            actor: $request->user(),
            institution: $member->institution,
            entityType: Member::class,
            entityId: $member->id,
            action: 'updated',
            before: $before,
            after: $member->toArray()
        );

        return $this->redirectToOpeningProcess($member->institution, 'Dados do membro atualizados com sucesso.');
    }

    public function destroy(Request $request, Member $member): RedirectResponse
    {
        $this->authorize('delete', $member);

        $institution = $member->institution;
        $activeMembers = $institution->members()->count();

        if ($activeMembers <= 1) {
            return back()->withErrors(['member' => 'E necessario manter ao menos um membro alem do presidente.']);
        }

        $before = $member->toArray();
        $member->delete();

        $this->activityLogger->log(
            actor: $request->user(),
            institution: $institution,
            entityType: Member::class,
            entityId: $member->id,
            action: 'deleted',
            before: $before,
            after: []
        );

        return $this->redirectToOpeningProcess($institution, 'Membro removido com sucesso.');
    }

    private function redirectToOpeningProcess(?Institution $institution, ?string $message = null): RedirectResponse
    {
        $process = Process::forInstitutionAndType($institution, Process::TYPE_INSTITUTION_OPENING);

        $redirect = $process
            ? redirect()->route('processes.show', $process)
            : redirect()->route('dashboard');

        return $message !== null ? $redirect->with('status', $message) : $redirect;
    }
}
