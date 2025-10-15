<?php

namespace App\Http\Controllers\Internal;

use App\Http\Controllers\Controller;
use App\Http\Requests\MemberRequest;
use App\Models\InternalActivityLog;
use App\Models\Member;
use App\Models\Process;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class MemberController extends Controller
{
    public function create(Process $process): View|RedirectResponse
    {
        if ($redirect = $this->ensureEditable($process)) {
            return $redirect;
        }

        $process->load('institution');

        return view('internal.processes.members.form', [
            'process' => $process,
            'institution' => $process->institution,
            'member' => null,
            'mode' => 'create',
            'submitRoute' => route('etika.processes.members.store', $process),
        ]);
    }

    public function store(MemberRequest $request, Process $process): RedirectResponse
    {
        if ($redirect = $this->ensureEditable($process)) {
            return $redirect;
        }

        $process->load('institution');
        $institution = $process->institution;

        $data = $request->validated();
        $data['uf'] = strtoupper($data['uf']);
        $data['institution_id'] = $institution->id;
        $data['process_id'] = $process->id;

        $member = Member::create($data);

        InternalActivityLog::create([
            'internal_user_id' => Auth::guard('internal')->id(),
            'entity' => Process::class,
            'entity_id' => $process->id,
            'action' => 'member_created',
            'diff' => [
                'after' => $member->toArray(),
            ],
        ]);

        return redirect()->route('etika.processes.show', $process)
            ->with('status', 'Membro cadastrado com sucesso.');
    }

    public function edit(Process $process, Member $member): View|RedirectResponse
    {
        if ($redirect = $this->ensureEditable($process)) {
            return $redirect;
        }

        abort_unless($member->institution_id === $process->institution_id, 404);

        return view('internal.processes.members.form', [
            'process' => $process,
            'institution' => $process->institution,
            'member' => $member,
            'mode' => 'edit',
            'submitRoute' => route('etika.processes.members.update', [$process, $member]),
        ]);
    }

    public function update(MemberRequest $request, Process $process, Member $member): RedirectResponse
    {
        if ($redirect = $this->ensureEditable($process)) {
            return $redirect;
        }

        abort_unless($member->institution_id === $process->institution_id, 404);

        $data = $request->validated();
        $data['uf'] = strtoupper($data['uf']);

        $before = $member->toArray();
        $member->update($data);

        InternalActivityLog::create([
            'internal_user_id' => Auth::guard('internal')->id(),
            'entity' => Process::class,
            'entity_id' => $process->id,
            'action' => 'member_updated',
            'diff' => [
                'before' => $before,
                'after' => $member->fresh()->toArray(),
            ],
        ]);

        return redirect()->route('etika.processes.show', $process)
            ->with('status', 'Dados do membro atualizados com sucesso.');
    }

    public function destroy(Request $request, Process $process, Member $member): RedirectResponse
    {
        if ($redirect = $this->ensureEditable($process)) {
            return $redirect;
        }

        abort_unless($member->institution_id === $process->institution_id, 404);

        $institution = $process->institution;
        $activeMembers = $institution->members()->count();

        if ($activeMembers <= 1) {
            return redirect()->route('etika.processes.show', $process)
                ->withErrors(['member' => 'E necessario manter ao menos um membro cadastrado.']);
        }

        $before = $member->toArray();
        $member->delete();

        InternalActivityLog::create([
            'internal_user_id' => Auth::guard('internal')->id(),
            'entity' => Process::class,
            'entity_id' => $process->id,
            'action' => 'member_deleted',
            'diff' => [
                'before' => $before,
            ],
        ]);

        return redirect()->route('etika.processes.show', $process)
            ->with('status', 'Membro removido com sucesso.');
    }

    private function ensureEditable(Process $process): ?RedirectResponse
    {
        if ($process->status === Process::STATUS_COMPLETED) {
            return redirect()->route('etika.processes.show', $process)
                ->withErrors(['process' => 'Este processo esta aprovado e nao pode ser editado. Reabra o processo para realizar alteracoes.']);
        }

        return null;
    }
}
