
@extends('internal.layouts.app')

@section('title', ($mode === 'edit' ? 'Editar membro' : 'Novo membro') . ' | Processo #' . $process->id)

@section('content')
@php
    $maritalStatuses = config('people.marital_statuses');
    $genders = config('people.genders');
    $roles = [
        'Presidente',
        'Vice Presidente',
        'Tesoureiro',
        'Segundo Tesoureiro',
        'Secretario',
        'Segundo Secretario',
        'Conselho Fiscal',
    ];
@endphp
<div class="card space-y-8">
    <div class="space-y-2">
        <h1 class="text-2xl font-semibold text-slate-900">{{ $mode === 'edit' ? 'Editar membro' : 'Cadastrar membro' }}</h1>
        <p class="text-sm text-slate-600">Preencha os dados completos do membro da diretoria.</p>
    </div>

    <form method="POST" action="{{ $submitRoute }}" class="space-y-6">
        @csrf
        @if($mode === 'edit')
            @method('PUT')
        @endif

        <section class="space-y-4">
            <h2 class="text-lg font-semibold">Dados pessoais</h2>
            <div class="grid gap-4 md:grid-cols-2">
                <div class="space-y-1">
                    <label class="form-label" for="name">Nome completo</label>
                    <input id="name" name="name" type="text" class="form-control" value="{{ old('name', optional($member)->name) }}" required>
                </div>
                <div class="space-y-1">
                    <label class="form-label" for="birth_date">Data de nascimento</label>
                    <input id="birth_date" name="birth_date" type="date" class="form-control" value="{{ old('birth_date', optional($member)->birth_date?->format('Y-m-d')) }}" required>
                </div>
                <div class="space-y-1">
                    <label class="form-label" for="birthplace">Naturalidade</label>
                    <input id="birthplace" name="birthplace" type="text" class="form-control" value="{{ old('birthplace', optional($member)->birthplace) }}" required>
                </div>
                <div class="space-y-1">
                    <label class="form-label" for="nationality">Nacionalidade</label>
                    <input id="nationality" name="nationality" type="text" class="form-control" value="{{ old('nationality', optional($member)->nationality) }}" required>
                </div>
                <div class="space-y-1">
                    <label class="form-label" for="father_name">Nome do pai</label>
                    <input id="father_name" name="father_name" type="text" class="form-control" value="{{ old('father_name', optional($member)->father_name) }}" required>
                </div>
                <div class="space-y-1">
                    <label class="form-label" for="mother_name">Nome da mae</label>
                    <input id="mother_name" name="mother_name" type="text" class="form-control" value="{{ old('mother_name', optional($member)->mother_name) }}" required>
                </div>
                <div class="space-y-1">
                    <label class="form-label" for="cpf">CPF</label>
                    <input id="cpf" name="cpf" type="text" class="form-control" value="{{ old('cpf', optional($member)->cpf) }}" data-mask="cpf" placeholder="000.000.000-00" required>
                </div>
                <div class="space-y-1">
                    <label class="form-label" for="rg">RG</label>
                    <input id="rg" name="rg" type="text" class="form-control" value="{{ old('rg', optional($member)->rg) }}" required>
                </div>
                <div class="space-y-1">
                    <label class="form-label" for="rg_issuer">Orgao emissor</label>
                    <input id="rg_issuer" name="rg_issuer" type="text" class="form-control" value="{{ old('rg_issuer', optional($member)->rg_issuer) }}" required>
                </div>
                <div class="space-y-1">
                    <label class="form-label" for="role">Cargo</label>
                    <select id="role" name="role" class="form-control" required>
                        <option value="">Selecione</option>
                        @foreach($roles as $role)
                            <option value="{{ $role }}" {{ old('role', optional($member)->role) === $role ? 'selected' : '' }}>{{ $role }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="space-y-1">
                    <label class="form-label" for="gender">Genero</label>
                    <select id="gender" name="gender" class="form-control" required>
                        <option value="">Selecione</option>
                        @foreach($genders as $gender)
                            <option value="{{ $gender }}" {{ old('gender', optional($member)->gender) === $gender ? 'selected' : '' }}>{{ $gender }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="space-y-1">
                    <label class="form-label" for="marital_status">Estado civil</label>
                    <select id="marital_status" name="marital_status" class="form-control" required>
                        <option value="">Selecione</option>
                        @foreach($maritalStatuses as $status)
                            <option value="{{ $status }}" {{ old('marital_status', optional($member)->marital_status) === $status ? 'selected' : '' }}>{{ $status }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </section>

        <section class="space-y-4">
            <h2 class="text-lg font-semibold">Contato</h2>
            <div class="grid gap-4 md:grid-cols-2">
                <div class="space-y-1">
                    <label class="form-label" for="email">Email</label>
                    <input id="email" name="email" type="email" class="form-control" value="{{ old('email', optional($member)->email) }}" required>
                </div>
                <div class="space-y-1">
                    <label class="form-label" for="phone">Telefone</label>
                    <input id="phone" name="phone" type="text" class="form-control" value="{{ old('phone', optional($member)->phone) }}" data-mask="phone" placeholder="(00) 00000-0000" required>
                </div>
            </div>
        </section>

        <section class="space-y-4">
            <h2 class="text-lg font-semibold">Documentacao</h2>
            <div class="grid gap-4 md:grid-cols-2">
                <div class="space-y-1">
                    <label class="form-label" for="profession">Profissao</label>
                    <input id="profession" name="profession" type="text" class="form-control" value="{{ old('profession', optional($member)->profession) }}" required>
                </div>
                <div class="space-y-1">
                    <label class="form-label" for="education">Escolaridade</label>
                    <input id="education" name="education" type="text" class="form-control" value="{{ old('education', optional($member)->education) }}" required>
                </div>
                <div class="space-y-1">
                    <label class="form-label" for="document_emission_date">Data emissao RG</label>
                    <input id="document_emission_date" name="document_emission_date" type="date" class="form-control" value="{{ old('document_emission_date', optional($member)->document_emission_date?->format('Y-m-d')) }}" required>
                </div>
                <div class="space-y-1">
                    <label class="form-label" for="document_emission_place">Local emissao RG</label>
                    <input id="document_emission_place" name="document_emission_place" type="text" class="form-control" value="{{ old('document_emission_place', optional($member)->document_emission_place) }}" required>
                </div>
            </div>
        </section>

        <section class="space-y-4">
            <h2 class="text-lg font-semibold">Endereco residencial</h2>
            <div class="grid gap-4 md:grid-cols-2">
                <div class="space-y-1 md:col-span-2">
                    <label class="form-label" for="street">Logradouro</label>
                    <input id="street" name="street" type="text" class="form-control" value="{{ old('street', optional($member)->street) }}" required>
                </div>
                <div class="space-y-1">
                    <label class="form-label" for="number">Numero</label>
                    <input id="number" name="number" type="text" class="form-control" value="{{ old('number', optional($member)->number) }}" required>
                </div>
                <div class="space-y-1">
                    <label class="form-label" for="complement">Complemento</label>
                    <input id="complement" name="complement" type="text" class="form-control" value="{{ old('complement', optional($member)->complement) }}">
                </div>
                <div class="space-y-1">
                    <label class="form-label" for="district">Bairro</label>
                    <input id="district" name="district" type="text" class="form-control" value="{{ old('district', optional($member)->district) }}" required>
                </div>
                <div class="space-y-1">
                    <label class="form-label" for="city">Cidade</label>
                    <input id="city" name="city" type="text" class="form-control" value="{{ old('city', optional($member)->city) }}" required>
                </div>
                <div class="space-y-1">
                    <label class="form-label" for="uf">UF</label>
                    <input id="uf" name="uf" type="text" maxlength="2" class="form-control uppercase" value="{{ old('uf', optional($member)->uf) }}" required>
                </div>
                <div class="space-y-1">
                    <label class="form-label" for="cep">CEP</label>
                    <input id="cep" name="cep" type="text" class="form-control" value="{{ old('cep', optional($member)->cep) }}" data-mask="cep" placeholder="00000-000" required>
                </div>
            </div>
        </section>

        <div class="flex flex-wrap justify-end gap-3">
            <a href="{{ route('etika.processes.show', $process) }}" class="btn-secondary">Cancelar</a>
            <button type="submit" class="btn">{{ $mode === 'edit' ? 'Salvar alteracoes' : 'Salvar membro' }}</button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const digitsOnly = (value, max) => value.replace(/\D/g, '').slice(0, max);

        const formatCpf = (value) => {
            const digits = digitsOnly(value, 11);
            if (digits.length <= 3) return digits;
            if (digits.length <= 6) return digits.slice(0, 3) + '.' + digits.slice(3);
            if (digits.length <= 9) return digits.slice(0, 3) + '.' + digits.slice(3, 6) + '.' + digits.slice(6);
            return digits.slice(0, 3) + '.' + digits.slice(3, 6) + '.' + digits.slice(6, 9) + '-' + digits.slice(9);
        };

        const formatPhone = (value) => {
            const digits = digitsOnly(value, 11);
            if (digits.length === 0) return '';
            if (digits.length <= 2) return digits;
            if (digits.length <= 6) return '(' + digits.slice(0, 2) + ') ' + digits.slice(2);
            if (digits.length <= 10) return '(' + digits.slice(0, 2) + ') ' + digits.slice(2, 6) + '-' + digits.slice(6);
            return '(' + digits.slice(0, 2) + ') ' + digits.slice(2, 7) + '-' + digits.slice(7);
        };

        const formatCep = (value) => {
            const digits = digitsOnly(value, 8);
            if (digits.length <= 5) return digits;
            return digits.slice(0, 5) + '-' + digits.slice(5);
        };

        const formatters = {
            cpf: formatCpf,
            phone: formatPhone,
            cep: formatCep,
        };

        const applyMask = (input) => {
            const formatter = formatters[input.dataset.mask];
            if (!formatter) return;

            const formatted = formatter(input.value || '');
            input.value = formatted;

            if (document.activeElement === input) {
                const pos = formatted.length;
                input.setSelectionRange(pos, pos);
            }
        };

        document.querySelectorAll('[data-mask]').forEach((input) => {
            applyMask(input);
            input.addEventListener('input', () => applyMask(input));
            input.addEventListener('blur', () => applyMask(input));
        });
    });
</script>
@endpush
