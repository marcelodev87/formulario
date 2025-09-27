<?php

namespace App\Http\Controllers;

use App\Models\Process;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BylawsRevisionController extends Controller
{
    public function show(Request $request, Process $process): View
    {
        // Verifica se o tipo do processo é reforma de estatuto
        abort_if($process->type !== 'bylaws_revision', 404);
        // Aqui pode-se adicionar lógica para carregar schema, respostas, etc.
        return view('processes.bylaws_revision.form', [
            'process' => $process,
        ]);
    }

    public function save(Request $request, Process $process)
    {
        // Exemplo: salvar respostas no campo 'answers' (JSON)
        $answers = $request->except(['_token']);
        $process->answers = $answers;
        $process->save();
        // Redireciona para o dashboard do processo
        return redirect()->route('processes.bylaws_revision.dashboard', $process)
            ->with('status', 'Respostas salvas com sucesso.');
    }

    public function dashboard(Process $process)
    {
        // Recupera respostas do processo (exemplo: $process->answers)
        $motivos = $process->answers['motivos'] ?? [];
        return view('processes.bylaws_revision.dashboard', [
            'process' => $process,
            'motivos' => $motivos,
        ]);
    }

    public function editMotivo(Process $process, string $motivo)
    {
        $motivoLabels = [
            'mudanca_nome' => 'Mudança de nome',
            'mudanca_endereco' => 'Mudança de endereço',
            'tempo_mandato' => 'Tempo de mandato',
            'cargos_diretoria' => 'Cargos da diretoria',
            'outros' => 'Outros',
        ];
        $data = $process->answers[$motivo] ?? [];
        return view('processes.bylaws_revision.edit_motivo', [
            'process' => $process,
            'motivo' => $motivo,
            'motivoLabel' => $motivoLabels[$motivo] ?? $motivo,
            'data' => $data,
        ]);
    }

    public function updateMotivo(Request $request, Process $process, string $motivo)
    {
        $data = $request->except(['_token', '_method']);
        $answers = $process->answers ?? [];
        $answers[$motivo] = $data;
        $process->answers = $answers;
        $process->save();
        return redirect()->route('processes.bylaws_revision.dashboard', $process)
            ->with('status', 'Informações do motivo atualizadas com sucesso.');
    }
}
