@extends('layouts.app')

@section('content')
<div class="card space-y-8 max-w-xl mx-auto">
    <div class="space-y-2">
        <h1 class="text-2xl font-semibold text-slate-900">Editar Motivo: {{ $motivoLabel }}</h1>
        <p class="text-sm text-slate-600">Preencha as informações referentes a este motivo da reforma.</p>
    </div>
    <form method="POST" action="{{ route('processes.bylaws_revision.update_motivo', [$process, $motivo]) }}">
        @csrf
        @method('PUT')
        @if($motivo === 'mudanca_nome')
            <div class="space-y-2">
                <label class="form-label" for="novo_nome">Novo nome da instituição</label>
                <input id="novo_nome" name="novo_nome" type="text" class="form-control" value="{{ old('novo_nome', $data['novo_nome'] ?? '') }}" required>
            </div>
        @elseif($motivo === 'mudanca_endereco')
            <div class="space-y-2">
                <label class="form-label" for="novo_endereco">Novo endereço</label>
                <input id="novo_endereco" name="novo_endereco" type="text" class="form-control" value="{{ old('novo_endereco', $data['novo_endereco'] ?? '') }}" required>
            </div>
        @elseif($motivo === 'tempo_mandato')
            <div class="space-y-2">
                <label class="form-label" for="novo_tempo">Novo tempo de mandato (anos)</label>
                <input id="novo_tempo" name="novo_tempo" type="number" min="1" max="10" class="form-control" value="{{ old('novo_tempo', $data['novo_tempo'] ?? '') }}" required>
            </div>
        @elseif($motivo === 'cargos_diretoria')
            <div class="space-y-2">
                <label class="form-label" for="novos_cargos">Novos cargos da diretoria</label>
                <textarea id="novos_cargos" name="novos_cargos" class="form-control" rows="3" required>{{ old('novos_cargos', $data['novos_cargos'] ?? '') }}</textarea>
            </div>
        @elseif($motivo === 'outros')
            <div class="space-y-2">
                <label class="form-label" for="descricao_outros">Descreva o motivo</label>
                <textarea id="descricao_outros" name="descricao_outros" class="form-control" rows="3" required>{{ old('descricao_outros', $data['descricao_outros'] ?? '') }}</textarea>
            </div>
        @endif
        <div class="flex justify-end mt-4">
            <button type="submit" class="btn">Salvar</button>
        </div>
    </form>
</div>
@endsection
