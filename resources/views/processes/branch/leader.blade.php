@extends('layouts.app')

@section('content')
@php($maritalStatuses = config('people.marital_statuses'); $genders = config('people.genders'))
@php
    $leaderExists = $leader->exists;
@endphp
<div class="space-y-8">
    <div class="card space-y-3">
        <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="text-xs uppercase tracking-wide text-slate-500">Processo</p>
                <h1 class="text-2xl font-semibold text-slate-900">{{ $process->title }}</h1>
                <p class="text-sm text-slate-600">Informe os dados completos do dirigente responsavel pela filial.</p>
            </div>
            <div class="rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-600">
                <p><span class="font-semibold text-slate-800">Instituicao:</span> {{ $institution->name }}</p>
                <p><span class="font-semibold text-slate-800">Documento:</span> {{ $institution->document }}</p>
            </div>
        </div>
        <div class="flex flex-wrap items-center gap-3 text-xs text-slate-500">
            <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 font-semibold text-slate-700">{{ $process->status_label }}</span>
            <span>Atualizado em {{ $process->updated_at->format('d/m/Y H:i') }}</span>
        </div>
        <div class="inline-flex flex-wrap gap-3">
            <a href="{{ route('processes.branch.show', $process) }}" class="btn-secondary-sm">Visao geral</a>
            <a href="{{ route('processes.branch.location.edit', $process) }}" class="btn-secondary-sm">Dados do endereco</a>
        </div>
    </div>

    <form method="POST" action="{{ route('processes.branch.leader.update', $process) }}" class="card space-y-6">
        @csrf
        @method('PUT')

        <section class="space-y-4">
            <h2 class="text-lg font-semibold text-slate-900">Dados pessoais</h2>
            <div class="grid gap-4 md:grid-cols-2">
                <div class="space-y-1 md:col-span-2">
                    <label class="form-label" for="name">Nome completo</label>
                    <input id="name" name="name" type="text" class="form-control" value="{{ old('name', $leader->name) }}" required>
                </div>
                <div class="space-y-1">
                    <label class="form-label" for="birth_date">Data de nascimento</label>
                    <input id="birth_date" name="birth_date" type="date" class="form-control" value="{{ old('birth_date', optional($leader->birth_date)->format('Y-m-d')) }}" required>
                </div>
                <div class="space-y-1">
                    <label class="form-label" for="birthplace">Naturalidade</label>
                    <input id="birthplace" name="birthplace" type="text" class="form-control" value="{{ old('birthplace', $leader->birthplace) }}" required>
                </div>
                <div class="space-y-1">
                    <label class="form-label" for="nationality">Nacionalidade</label>
                    <input id="nationality" name="nationality" type="text" class="form-control" value="{{ old('nationality', $leader->nationality) }}" required>
                </div>
                <div class="space-y-1">
                    <label class="form-label" for="father_name">Nome do pai</label>
                    <input id="father_name" name="father_name" type="text" class="form-control" value="{{ old('father_name', $leader->father_name) }}" required>
                </div>
                <div class="space-y-1">
                    <label class="form-label" for="mother_name">Nome da mae</label>
                    <input id="mother_name" name="mother_name" type="text" class="form-control" value="{{ old('mother_name', $leader->mother_name) }}" required>
                </div>
            </div>
        </section>

        <section class="space-y-4">
            <h2 class="text-lg font-semibold text-slate-900">Documentos</h2>
            <div class="grid gap-4 md:grid-cols-2">
                <div class="space-y-1">
                    <label class="form-label" for="cpf">CPF</label>
                    <input id="cpf" name="cpf" type="text" class="form-control" value="{{ old('cpf', $leader->cpf) }}" data-mask="cpf" placeholder="000.000.000-00" required>
                </div>
                <div class="space-y-1">
                    <label class="form-label" for="rg">RG</label>
                    <input id="rg" name="rg" type="text" class="form-control" value="{{ old('rg', $leader->rg) }}" required>
                </div>
                <div class="space-y-1">
                    <label class="form-label" for="rg_issuer">Orgao emissor</label>
                    <input id="rg_issuer" name="rg_issuer" type="text" class="form-control" value="{{ old('rg_issuer', $leader->rg_issuer) }}" required>
                </div>
                <div class="space-y-1">
                    <label class="form-label" for="gender">Genero</label>
                    <select id="gender" name="gender" class="form-control" required>
                        <option value="">Selecione</option>
                        <option value="Masculino" {{ old('gender', $leader->gender) === 'Masculino' ? 'selected' : '' }}>Masculino</option>
                        <option value="Feminino" {{ old('gender', $leader->gender) === 'Feminino' ? 'selected' : '' }}>Feminino</option>
                    </select>
                </div>
                <div class="space-y-1">
                    <label class="form-label" for="marital_status">Estado civil</label>
                    <select id="marital_status" name="marital_status" class="form-control" required>
                        <option value="">Selecione</option>
                        <option value="Divorciado(a)" {{ old('marital_status', $leader->marital_status) === 'Divorciado(a)' ? 'selected' : '' }}>Divorciado(a)</option>
                        <option value="Casado(a)" {{ old('marital_status', $leader->marital_status) === 'Casado(a)' ? 'selected' : '' }}>Casado(a)</option>
                        <option value="Solteiro(a)" {{ old('marital_status', $leader->marital_status) === 'Solteiro(a)' ? 'selected' : '' }}>Solteiro(a)</option>
                        <option value="Viúvo(a)" {{ old('marital_status', $leader->marital_status) === 'Viúvo(a)' ? 'selected' : '' }}>Viúvo(a)</option>
                    </select>
                </div>
                <div class="space-y-1 md:col-span-2">
                    <label class="form-label" for="profession">Profissao</label>
                    <input id="profession" name="profession" type="text" class="form-control" value="{{ old('profession', $leader->profession) }}" required>
                </div>
            </div>
        </section>

        <section class="space-y-4">
            <h2 class="text-lg font-semibold text-slate-900">Contato</h2>
            <div class="grid gap-4 md:grid-cols-2">
                <div class="space-y-1">
                    <label class="form-label" for="email">Email</label>
                    <input id="email" name="email" type="email" class="form-control" value="{{ old('email', $leader->email) }}" required>
                </div>
                <div class="space-y-1">
                    <label class="form-label" for="phone">Telefone</label>
                    <input id="phone" name="phone" type="text" class="form-control" value="{{ old('phone', $leader->phone) }}" data-mask="phone" placeholder="(00) 00000-0000" required>
                </div>
            </div>
        </section>

        <section class="space-y-4">
            <h2 class="text-lg font-semibold text-slate-900">Endereco residencial</h2>
            <div class="grid gap-4 md:grid-cols-2">
                <div class="space-y-1 md:col-span-2">
                    <label class="form-label" for="street">Logradouro</label>
                    <input id="street" name="street" type="text" class="form-control" value="{{ old('street', $leader->street) }}" required>
                </div>
                <div class="space-y-1">
                    <label class="form-label" for="number">Numero</label>
                    <input id="number" name="number" type="text" class="form-control" value="{{ old('number', $leader->number) }}" required>
                </div>
                <div class="space-y-1">
                    <label class="form-label" for="complement">Complemento</label>
                    <input id="complement" name="complement" type="text" class="form-control" value="{{ old('complement', $leader->complement) }}">
                </div>
                <div class="space-y-1">
                    <label class="form-label" for="district">Bairro</label>
                    <input id="district" name="district" type="text" class="form-control" value="{{ old('district', $leader->district) }}" required>
                </div>
                <div class="space-y-1">
                    <label class="form-label" for="city">Cidade</label>
                    <input id="city" name="city" type="text" class="form-control" value="{{ old('city', $leader->city) }}" required>
                </div>
                <div class="space-y-1">
                    <label class="form-label" for="uf">UF</label>
                    <input id="uf" name="uf" type="text" maxlength="2" class="form-control uppercase" value="{{ old('uf', $leader->uf) }}" required>
                </div>
                <div class="space-y-1">
                    <label class="form-label" for="cep">CEP</label>
                    <input id="cep" name="cep" type="text" class="form-control" value="{{ old('cep', $leader->cep) }}" data-mask="cep" placeholder="00000-000" required>
                </div>
            </div>
        </section>

        <div class="flex flex-wrap justify-end gap-3">
            <a href="{{ route('processes.branch.show', $process) }}" class="btn-secondary">Voltar</a>
            <button type="submit" class="btn">Salvar dirigente</button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var digitsOnly = function (value, max) {
            return (value || '').replace(/\D/g, '').slice(0, max);
        };

        var formatCpf = function (value) {
            var digits = digitsOnly(value, 11);
            if (digits.length <= 3) return digits;
            if (digits.length <= 6) return digits.slice(0, 3) + '.' + digits.slice(3);
            if (digits.length <= 9) return digits.slice(0, 3) + '.' + digits.slice(3, 6) + '.' + digits.slice(6);
            return digits.slice(0, 3) + '.' + digits.slice(3, 6) + '.' + digits.slice(6, 9) + '-' + digits.slice(9);
        };

        var formatPhone = function (value) {
            var digits = digitsOnly(value, 11);
            if (digits.length === 0) return '';
            if (digits.length <= 2) return digits;
            if (digits.length <= 6) return '(' + digits.slice(0, 2) + ') ' + digits.slice(2);
            if (digits.length <= 10) return '(' + digits.slice(0, 2) + ') ' + digits.slice(2, 6) + '-' + digits.slice(6);
            return '(' + digits.slice(0, 2) + ') ' + digits.slice(2, 7) + '-' + digits.slice(7);
        };

        var formatCep = function (value) {
            var digits = digitsOnly(value, 8);
            if (digits.length <= 5) return digits;
            return digits.slice(0, 5) + '-' + digits.slice(5);
        };

        var formatters = {
            cpf: formatCpf,
            phone: formatPhone,
            cep: formatCep
        };

        document.querySelectorAll('[data-mask]').forEach(function (input) {
            var formatter = formatters[input.dataset.mask];
            if (!formatter) {
                return;
            }

            var applyMask = function () {
                var formatted = formatter(input.value || '');
                input.value = formatted;
                if (document.activeElement === input) {
                    var pos = formatted.length;
                    input.setSelectionRange(pos, pos);
                }
            };

            applyMask();
            input.addEventListener('input', applyMask);
            input.addEventListener('blur', applyMask);
        });
    });
</script>
@endpush
