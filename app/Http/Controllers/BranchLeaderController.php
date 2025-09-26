<?php

namespace App\Http\Controllers;

use App\Http\Requests\BranchLeaderRequest;
use App\Models\Leader;
use App\Models\Process;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BranchLeaderController extends Controller
{
    public function edit(Request $request, Process $process): RedirectResponse|View
    {
        abort_if($process->type !== Process::TYPE_BRANCH_OPENING, 404);

        $this->authorize('view', $process);

        $institution = $process->institution;

        abort_unless($institution && $institution->owner_user_id === $request->user()->id, 403);

        $location = $process->location()->with('leader')->first();

        if (!$location) {
            return redirect()
                ->route('processes.branch.location.edit', $process)
                ->with('error', 'Cadastre o endereco da filial antes de informar o dirigente.');
        }

        $leader = $location->leader ?? new Leader([
            'location_id' => $location->id,
        ]);

        return view('processes.branch.leader', [
            'process' => $process,
            'institution' => $institution,
            'location' => $location,
            'leader' => $leader,
        ]);
    }

    public function update(BranchLeaderRequest $request, Process $process): RedirectResponse
    {
        abort_if($process->type !== Process::TYPE_BRANCH_OPENING, 404);

        $this->authorize('view', $process);

        $institution = $process->institution;

        abort_unless($institution && $institution->owner_user_id === $request->user()->id, 403);

        $location = $process->location()->first();

        if (!$location) {
            return redirect()
                ->route('processes.branch.location.edit', $process)
                ->with('error', 'Cadastre o endereco da filial antes de informar o dirigente.');
        }

        $data = $request->validated();

        $location->leader()->updateOrCreate([], $data);

        return redirect()
            ->route('processes.branch.leader.edit', $process)
            ->with('status', 'Dados do dirigente atualizados com sucesso.');
    }
}
