<?php

namespace App\Http\Controllers;

use App\Http\Requests\MemberRequest;
use App\Models\Member;
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

        return redirect()->route('dashboard');
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

        return redirect()->route('dashboard')->with('status', 'Dados do membro atualizados com sucesso.');
    }

    public function destroy(Request $request, Member $member): RedirectResponse
    {
        $this->authorize('delete', $member);

        $institution = $member->institution;
        $activeMembers = $institution->members()->count();

        if ($activeMembers <= 1) {
            return back()->withErrors(['member' => 'É necessário manter ao menos um membro além do presidente.']);
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

        return redirect()->route('dashboard')->with('status', 'Membro removido com sucesso.');
    }
}