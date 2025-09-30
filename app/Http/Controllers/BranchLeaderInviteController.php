<?php

namespace App\Http\Controllers;

use App\Models\BranchLeaderInvite;
use App\Models\Process;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BranchLeaderInviteController extends Controller
{
    public function showConfirmation(BranchLeaderInvite $invite): View
    {
        $institution = $invite->process->institution;
        return view('branch_leader_invite.confirmation', [
            'institution' => $institution,
        ]);
    }
    public function showPublicForm(BranchLeaderInvite $invite): View
    {
        if ($invite->isExpired()) {
            abort(410, 'Este convite expirou. Solicite um novo link ao responsável.');
        }
        $process = $invite->process;
        return view('branch_leader_invite.form', [
            'invite' => $invite,
            'process' => $process,
        ]);
    }

    public function storeLeader(Request $request, BranchLeaderInvite $invite): RedirectResponse
    {
        if ($invite->isExpired()) {
            abort(410, 'Este convite expirou. Solicite um novo link ao responsável.');
        }
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'birth_date' => 'required|date',
            'birthplace' => 'required|string|max:255',
            'nationality' => 'required|string|max:255',
            'father_name' => 'required|string|max:255',
            'mother_name' => 'required|string|max:255',
            'cpf' => 'required|string|max:14',
            'rg' => 'required|string|max:255',
            'rg_issuer' => 'required|string|max:255',
            'gender' => 'required|string|max:20',
            'marital_status' => 'required|string|max:50',
            'profession' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'street' => 'required|string|max:255',
            'number' => 'required|string|max:20',
            'complement' => 'nullable|string|max:255',
            'district' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'uf' => 'required|string|max:2',
            'cep' => 'required|string|max:9',
        ]);
        $process = $invite->process;
        $location = $process->location()->first();
        $location->leader()->updateOrCreate([], $data);
        $invite->update(['status' => 'used']);
        return redirect()->route('invite.confirmation', ['invite' => $invite->key]);
    }
    public function generate(Request $request, Process $process): RedirectResponse
    {
        $this->authorize('view', $process);

        // Gera ou recupera convite ativo
        $invite = BranchLeaderInvite::firstOrCreate(
            [
                'process_id' => $process->id,
                'status' => 'active',
            ],
            [
                'key' => Str::uuid(),
                'expires_at' => now()->addDays(7),
            ]
        );

        return redirect()->route('processes.branch.show', $process)
            ->with('status', 'Link de convite gerado!');
    }
}
