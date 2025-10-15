<?php

namespace App\Http\Controllers\Internal;

use App\Http\Controllers\Controller;
use App\Http\Requests\BranchLeaderRequest;
use App\Models\InternalActivityLog;
use App\Models\Process;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class BranchLeaderController extends Controller
{
    public function edit(Process $process): View|RedirectResponse
    {
        abort_if($process->type !== Process::TYPE_BRANCH_OPENING, 404);

        if ($redirect = $this->ensureEditable($process)) {
            return $redirect;
        }

        $process->load('institution', 'location.leader');

        $location = $process->location;

        if (!$location) {
            return redirect()
                ->route('etika.processes.branch.location.edit', $process)
                ->withErrors(['location' => 'Cadastre o endereco da filial antes de informar o dirigente.']);
        }

        $leader = $location->leader;

        return view('internal.processes.branch.leader', [
            'process' => $process,
            'institution' => $process->institution,
            'location' => $location,
            'leader' => $leader,
        ]);
    }

    public function update(BranchLeaderRequest $request, Process $process): RedirectResponse
    {
        abort_if($process->type !== Process::TYPE_BRANCH_OPENING, 404);

        if ($redirect = $this->ensureEditable($process)) {
            return $redirect;
        }

        $process->load('location.leader');

        $location = $process->location;

        if (!$location) {
            return redirect()
                ->route('etika.processes.branch.location.edit', $process)
                ->withErrors(['location' => 'Cadastre o endereco da filial antes de informar o dirigente.']);
        }

        $data = $request->validated();
        $data['uf'] = strtoupper($data['uf']);

        $leader = $location->leader()->firstOrNew([]);
        $before = $leader->exists ? $leader->toArray() : [];

        $leader->fill($data + ['location_id' => $location->id])->save();

        InternalActivityLog::create([
            'internal_user_id' => Auth::guard('internal')->id(),
            'entity' => Process::class,
            'entity_id' => $process->id,
            'action' => 'branch_leader_updated',
            'diff' => [
                'before' => $before,
                'after' => $leader->fresh()->toArray(),
            ],
        ]);

        return redirect()->route('etika.processes.show', $process)
            ->with('status', 'Dados do dirigente atualizados com sucesso.');
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
