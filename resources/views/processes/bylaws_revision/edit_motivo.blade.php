@extends('layouts.app')

@section('content')
<div class="space-y-8">
    <div class="card space-y-2">
        <p class="text-xs uppercase tracking-wide text-slate-500">Processo</p>
        <h1 class="text-2xl font-semibold text-slate-900">{{ $process->title }}</h1>
        <p class="text-sm text-slate-600">{{ __('forms.update_info_ref') }} {{ strtolower($motivoLabel) }}.</p>
    </div>

    <div class="card space-y-6 max-w-3xl">
        <h2 class="text-lg font-semibold text-slate-900">{{ $motivoLabel }}</h2>
        <form method="POST" action="{{ route('processes.bylaws_revision.update_motivo', [$process, $motivo]) }}" class="space-y-6">
            @csrf
            @method('PUT')

            @if($motivo === 'mudanca_endereco')
                <div class="grid gap-4 md:grid-cols-2">
                    <div class="space-y-1 md:col-span-2">
                        <label class="form-label" for="street">Logradouro</label>
                        <input id="street" name="street" type="text" class="form-control" value="{{ old('street', $location->street) }}" required>
                    </div>
                    <div class="space-y-1">
                        <label class="form-label" for="number">Numero</label>
                        <input id="number" name="number" type="text" class="form-control" value="{{ old('number', $location->number) }}" required>
                    </div>
                    <div class="space-y-1">
                        <label class="form-label" for="complement">Complemento</label>
                        <input id="complement" name="complement" type="text" class="form-control" value="{{ old('complement', $location->complement) }}">
                    </div>
                    <div class="space-y-1">
                        <label class="form-label" for="district">Bairro</label>
                        <input id="district" name="district" type="text" class="form-control" value="{{ old('district', $location->district) }}" required>
                    </div>
                    <div class="space-y-1">
                        <label class="form-label" for="city">Cidade</label>
                        <input id="city" name="city" type="text" class="form-control" value="{{ old('city', $location->city) }}" required>
                    </div>
                    <div class="space-y-1">
                        <label class="form-label" for="uf">UF</label>
                        <input id="uf" name="uf" type="text" maxlength="2" class="form-control uppercase" value="{{ old('uf', $location->uf) }}" required>
                    </div>
                    <div class="space-y-1">
                        <label class="form-label" for="cep">CEP</label>
                        <input id="cep" name="cep" type="text" class="form-control" value="{{ old('cep', $location->cep) }}" required>
                    </div>
                </div>
            @elseif($motivo === 'mudanca_nome')
                <div class="space-y-1">
                    <label class="form-label" for="novo_nome">Novo nome da instituicao</label>
                    <input id="novo_nome" name="novo_nome" type="text" class="form-control" value="{{ old('novo_nome', $data['novo_nome'] ?? '') }}" required>
                </div>
            @elseif($motivo === 'tempo_mandato')
                <div class="space-y-1">
                    <label class="form-label" for="novo_tempo">Novo tempo de mandato (anos)</label>
                    <input id="novo_tempo" name="novo_tempo" type="number" min="1" max="10" class="form-control" value="{{ old('novo_tempo', $data['novo_tempo'] ?? '') }}" required>
                </div>
            @elseif($motivo === 'cargos_diretoria')
                <div class="space-y-1">
                    <label class="form-label" for="novos_cargos">Novos cargos da diretoria</label>
                    <textarea id="novos_cargos" name="novos_cargos" class="form-control" rows="3" required>{{ old('novos_cargos', $data['novos_cargos'] ?? '') }}</textarea>
                </div>
            @elseif($motivo === 'outros')
                <div class="space-y-1">
                    <label class="form-label" for="descricao_outros">Descreva o motivo</label>
                    <textarea id="descricao_outros" name="descricao_outros" class="form-control" rows="3" required>{{ old('descricao_outros', $data['descricao_outros'] ?? '') }}</textarea>
                </div>
            @endif

            <div class="flex justify-end gap-3">
                <a href="{{ route('processes.bylaws_revision.dashboard', $process) }}" class="btn-secondary">Cancelar</a>
                <button type="submit" class="btn">Salvar</button>
            </div>
        </form>
    </div>
</div>
@endsection


@push('scripts')
@if($motivo === 'mudanca_endereco')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const digitsOnly = (value, max) => (value || '').replace(/\D/g, '').slice(0, max);
        const formatCep = (value) => {
            const digits = digitsOnly(value, 8);
            if (digits.length <= 5) return digits;
            return `${digits.slice(0, 5)}-${digits.slice(5)}`;
        };

        const applyMask = (input) => {
            const formatted = formatCep(input.value || '');
            input.value = formatted;
            if (document.activeElement === input) {
                const pos = formatted.length;
                input.setSelectionRange(pos, pos);
            }
        };

        const cep = document.getElementById('cep');
        if (cep) {
            applyMask(cep);
            cep.addEventListener('input', () => applyMask(cep));
            cep.addEventListener('blur', () => applyMask(cep));
        }
    });
</script>
@endif
@endpush
