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

        return $this->redirectAfterAction($request, $request->user()->institution);
    }

    public function storeForBoardElection(MemberRequest $request, Process $process): RedirectResponse
    {
        $this->authorize('view', $process);

        abort_if($process->type !== Process::TYPE_BOARD_ELECTION_MINUTES_REGISTRATION, 404);

        $institution = $process->institution;

        $data = $request->validated();
        $data['institution_id'] = $institution->id;
        $data['process_id'] = $process->id;
        $data['uf'] = strtoupper($data['uf']);

        $member = Member::create($data);

        $this->activityLogger->log(
            actor: $request->user(),
            institution: $institution,
            entityType: Member::class,
            entityId: $member->id,
            action: 'created',
            before: [],
            after: $member->toArray()
        );

        return redirect()->route('processes.board_election.dashboard', $process)
            ->with('status', 'Membro cadastrado com sucesso.');
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

        return $this->redirectAfterAction($request, $member->institution, 'Dados do membro atualizados com sucesso.');
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

        return $this->redirectAfterAction($request, $institution, 'Membro removido com sucesso.');
    }

    private function redirectAfterAction(Request $request, ?Institution $institution, ?string $message = null): RedirectResponse
    {
        $redirectTo = (string) $request->input('redirect_to', '');
        $processId = $request->input('process_id');
        $processId = is_numeric($processId) ? (int) $processId : null;

        if ($redirectTo === Process::TYPE_BOARD_ELECTION_MINUTES_REGISTRATION && $processId !== null) {
            $process = Process::query()
                ->whereKey($processId)
                ->when($institution, fn ($query) => $query->where('institution_id', $institution->id))
                ->first();

            if ($process && $process->type === Process::TYPE_BOARD_ELECTION_MINUTES_REGISTRATION) {
                $redirect = redirect()->route('processes.board_election.dashboard', $process);

                return $message !== null ? $redirect->with('status', $message) : $redirect;
            }
        }

        $process = Process::forInstitutionAndType($institution, Process::TYPE_INSTITUTION_OPENING);

        $redirect = $process
            ? redirect()->route('processes.show', $process)
            : redirect()->route('dashboard');

        return $message !== null ? $redirect->with('status', $message) : $redirect;
    }
}

