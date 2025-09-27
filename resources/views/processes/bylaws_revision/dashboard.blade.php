@extends('layouts.app')

@section('content')
<div class="card space-y-8">
    <div class="space-y-2">
        <h1 class="text-2xl font-semibold text-slate-900">Reforma de Estatuto - Dashboard</h1>
        <p class="text-sm text-slate-600">Acompanhe o progresso das principais etapas e acesse os cadastros conforme as opções escolhidas.</p>
    </div>

    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
        {{-- Exemplo de cards dinâmicos, renderizados conforme respostas --}}
        @if(in_array('mudanca_nome', $motivos ?? []))
        <div class="card p-4">
            <h2 class="text-lg font-semibold">Mudança de nome</h2>
            <p class="text-sm text-slate-600">Preencha os dados para alteração do nome da instituição.</p>
            <div class="flex items-center gap-2 mt-2">
                <span class="badge badge-success">Pendente</span>
                <a href="{{ route('processes.bylaws_revision.edit_motivo', [$process, 'mudanca_nome']) }}" class="btn-secondary">Cadastrar novo nome</a>
            </div>
        </div>
        @endif
        @if(in_array('mudanca_endereco', $motivos ?? []))
        <div class="card p-4">
            <h2 class="text-lg font-semibold">Mudança de endereço</h2>
            <p class="text-sm text-slate-600">Preencha os dados para alteração do endereço institucional.</p>
            <div class="flex items-center gap-2 mt-2">
                <span class="badge badge-success">Pendente</span>
                <a href="{{ route('processes.bylaws_revision.edit_motivo', [$process, 'mudanca_endereco']) }}" class="btn-secondary">Cadastrar novo endereço</a>
            </div>
        </div>
        @endif
        @if(in_array('tempo_mandato', $motivos ?? []))
        <div class="card p-4">
            <h2 class="text-lg font-semibold">Tempo de mandato</h2>
            <p class="text-sm text-slate-600">Defina o novo tempo de mandato dos cargos diretivos.</p>
            <div class="flex items-center gap-2 mt-2">
                <span class="badge badge-success">Pendente</span>
                <a href="{{ route('processes.bylaws_revision.edit_motivo', [$process, 'tempo_mandato']) }}" class="btn-secondary">Definir tempo de mandato</a>
            </div>
        </div>
        @endif
        @if(in_array('cargos_diretoria', $motivos ?? []))
        <div class="card p-4">
            <h2 class="text-lg font-semibold">Cargos da diretoria</h2>
            <p class="text-sm text-slate-600">Atualize os cargos da diretoria conforme a reforma.</p>
            <div class="flex items-center gap-2 mt-2">
                <span class="badge badge-success">Pendente</span>
                <a href="{{ route('processes.bylaws_revision.edit_motivo', [$process, 'cargos_diretoria']) }}" class="btn-secondary">Atualizar cargos</a>
            </div>
        </div>
        @endif
        @if(in_array('outros', $motivos ?? []))
        <div class="card p-4">
            <h2 class="text-lg font-semibold">Outros motivos</h2>
            <p class="text-sm text-slate-600">Preencha os dados para outros motivos da reforma.</p>
            <div class="flex items-center gap-2 mt-2">
                <span class="badge badge-success">Pendente</span>
                <a href="{{ route('processes.bylaws_revision.edit_motivo', [$process, 'outros']) }}" class="btn-secondary">Cadastrar motivo</a>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
