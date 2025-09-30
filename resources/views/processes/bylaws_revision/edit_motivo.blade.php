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
@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto card p-6">
    <h2 class="text-xl font-semibold mb-4">Novo Endereço</h2>
    <form method="POST" action="{{ route('processes.bylaws_revision.update_motivo', [$process, 'mudanca_endereco']) }}">
        @csrf
        <div class="mb-3">
            <label class="form-label">Logradouro</label>
            <input type="text" name="logradouro" class="form-control" value="{{ old('logradouro', $data['logradouro'] ?? '') }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Número</label>
            <input type="text" name="numero" class="form-control" value="{{ old('numero', $data['numero'] ?? '') }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Complemento</label>
            <input type="text" name="complemento" class="form-control" value="{{ old('complemento', $data['complemento'] ?? '') }}">
        </div>
        <div class="mb-3">
            <label class="form-label">Bairro</label>
            <input type="text" name="bairro" class="form-control" value="{{ old('bairro', $data['bairro'] ?? '') }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Cidade</label>
            <input type="text" name="cidade" class="form-control" value="{{ old('cidade', $data['cidade'] ?? '') }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">UF</label>
            <input type="text" name="uf" class="form-control" value="{{ old('uf', $data['uf'] ?? '') }}" maxlength="2" required>
        </div>
        <div class="mb-3">
            <label class="form-label">CEP</label>
            <input type="text" name="cep" class="form-control" value="{{ old('cep', $data['cep'] ?? '') }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Número de inscrição do imóvel</label>
            <input type="text" name="inscricao_imovel" class="form-control" value="{{ old('inscricao_imovel', $data['inscricao_imovel'] ?? '') }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Área construída do imóvel (m²)</label>
            <input type="number" step="0.01" name="area_construida" class="form-control" value="{{ old('area_construida', $data['area_construida'] ?? '') }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Área total do imóvel (m²)</label>
            <input type="number" step="0.01" name="area_total" class="form-control" value="{{ old('area_total', $data['area_total'] ?? '') }}" required>
        </div>
        <button type="submit" class="btn w-full bg-blue-700 text-white">Salvar endereço</button>
    </form>
</div>
@endsection
