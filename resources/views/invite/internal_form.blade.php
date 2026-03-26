@extends('layouts.app')

@section('content')
@php
    $maritalStatuses = config('people.marital_statuses');
    $genders = config('people.genders');
    $roleOptions = [
        'Presidente' => 'Presidente',
        'Vice Presidente' => 'Vice Presidente',
        'Tesoureiro' => 'Tesoureiro',
        'Segundo Tesoureiro' => 'Segundo Tesoureiro',
        'Secretário' => 'Secretário',
        'Segundo Secretário' => 'Segundo Secretário',
        'Conselho Fiscal' => 'Conselho Fiscal',
    ];
    $redirectParams = $redirectParams ?? [];
    $processId = $processId ?? null;
@endphp
<div class="card space-y-8">
    <div class="space-y-2">
        <h1 class="text-2xl font-semibold text-slate-900">Cadastrar membro da diretoria</h1>
        <p class="text-sm text-slate-600">Preencha os dados abaixo para adicionar um membro à instituição {{ $institution->name }}.</p>
    </div>

    <form method="POST" action="{{ route('invite.members.internal.store') }}" class="space-y-6">
        @csrf
        @foreach($redirectParams as $paramKey => $paramValue)
            <input type="hidden" name="{{ $paramKey }}" value="{{ $paramValue }}">
        @endforeach
        @if($processId)
            <input type="hidden" name="process_id" value="{{ $processId }}">
        @endif

        <section class="space-y-4">
            <h2 class="text-lg font-semibold">Dados pessoais</h2>
            <div class="grid gap-4 md:grid-cols-2">
                <div class="space-y-1">
                    <label class="form-label" for="name">Nome completo</label>
                    <input id="name" name="name" type="text" class="form-control" value="{{ old('name') }}" required>
                </div>
                <div class="space-y-1">
                    <label class="form-label" for="birth_date">Data de nascimento</label>
                    <input id="birth_date" name="birth_date" type="date" class="form-control" value="{{ old('birth_date') }}" required>
                </div>
                <div class="space-y-1">
                    <label class="form-label" for="birthplace">Naturalidade</label>
                    <input id="birthplace" name="birthplace" type="text" class="form-control" value="{{ old('birthplace') }}" required>
                </div>
                <div class="space-y-1">
                    <label class="form-label" for="nationality">Nacionalidade</label>
                    <input id="nationality" name="nationality" type="text" class="form-control" value="{{ old('nationality') }}" required>
                </div>
                <div class="space-y-1">
                    <label class="form-label" for="father_name">Nome do pai (Escreva "não consta" caso este campo esteja em branco no seu RG)</label>
                    <input id="father_name" name="father_name" type="text" class="form-control" value="{{ old('father_name') }}" required>
                </div>
                <div class="space-y-1">
                    <label class="form-label" for="mother_name">Nome da mãe (Escreva "não consta" caso este campo esteja em branco no seu RG)</label>
                    <input id="mother_name" name="mother_name" type="text" class="form-control" value="{{ old('mother_name') }}" required>
                </div>
                <div class="space-y-1">
                    <label class="form-label" for="cpf">CPF</label>
                    <input id="cpf" name="cpf" type="text" class="form-control" value="{{ old('cpf') }}" data-mask="cpf" placeholder="000.000.000-00" required>
                </div>
                <div class="space-y-1">
                    <label class="form-label" for="rg">RG</label>
                    <input id="rg" name="rg" type="text" class="form-control" value="{{ old('rg') }}" required>
                </div>
                <div class="space-y-1">
                    <label class="form-label" for="rg_issuer">Órgão emissor</label>
                    <input id="rg_issuer" name="rg_issuer" type="text" class="form-control" value="{{ old('rg_issuer') }}" required>
                </div>
                <div class="space-y-1">
                    <label class="form-label" for="role">Cargo</label>
                    <select id="role" name="role" class="form-control" required>
                        <option value="">Selecione</option>
                        @foreach($roleOptions as $value => $label)
                            <option value="{{ $value }}" @selected(old('role') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="space-y-1">
                    <label class="form-label" for="gender">Gênero</label>
                    <select id="gender" name="gender" class="form-control" required>
                        <option value="">Selecione</option>
                        @foreach($genders as $gender)
                            <option value="{{ $gender }}" @selected(old('gender') === $gender)>{{ $gender }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="space-y-1">
                    <label class="form-label" for="marital_status">Estado civil</label>
                    <select id="marital_status" name="marital_status" class="form-control" required>
                        <option value="">Selecione</option>
                        @foreach($maritalStatuses as $status)
                            <option value="{{ $status }}" @selected(old('marital_status') === $status)>{{ $status }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="space-y-1">
                    <label class="form-label" for="profession">Profissão</label>
                    <input id="profession" name="profession" type="text" class="form-control" value="{{ old('profession') }}" required>
                </div>
                <div class="space-y-1">
                    <label class="form-label" for="email">E-mail</label>
                    <input id="email" name="email" type="email" class="form-control" value="{{ old('email') }}" required>
                </div>
                <div class="space-y-1">
                    <label class="form-label" for="phone">Telefone</label>
                    <input id="phone" name="phone" type="text" class="form-control" value="{{ old('phone') }}" data-mask="phone" placeholder="(00) 00000-0000" required>
                </div>
            </div>
        </section>

        <section class="space-y-4">
            <h2 class="text-lg font-semibold">Endereço</h2>
            <div class="grid gap-4 md:grid-cols-2">
                <div class="space-y-1 md:col-span-2">
                    <label class="form-label" for="street">Logradouro</label>
                    <input id="street" name="street" type="text" class="form-control" value="{{ old('street') }}" required>
                </div>
                <div class="space-y-1">
                    <label class="form-label" for="number">Número</label>
                    <input id="number" name="number" type="text" class="form-control" value="{{ old('number') }}" required>
                </div>
                <div class="space-y-1">
                    <label class="form-label" for="complement">Complemento</label>
                    <input id="complement" name="complement" type="text" class="form-control" value="{{ old('complement') }}">
                </div>
                <div class="space-y-1">
                    <label class="form-label" for="district">Bairro</label>
                    <input id="district" name="district" type="text" class="form-control" value="{{ old('district') }}" required>
                </div>
                <div class="space-y-1">
                    <label class="form-label" for="city">Cidade</label>
                    <input id="city" name="city" type="text" class="form-control" value="{{ old('city') }}" required>
                </div>
                <div class="space-y-1">
                    <label class="form-label" for="uf">UF</label>
                    <input id="uf" name="uf" type="text" maxlength="2" class="form-control uppercase" value="{{ old('uf') }}" required>
                </div>
                <div class="space-y-1">
                    <label class="form-label" for="cep">CEP</label>
                    <input id="cep" name="cep" type="text" class="form-control" value="{{ old('cep') }}" data-mask="cep" placeholder="00000-000" required>
                </div>
            </div>
        </section>

        <div class="flex justify-end">
            <button type="submit" class="btn">Salvar membro</button>
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
            if (digits.length <= 6) return `${digits.slice(0, 3)}.${digits.slice(3)}`;
            if (digits.length <= 9) return `${digits.slice(0, 3)}.${digits.slice(3, 6)}.${digits.slice(6)}`;
            return `${digits.slice(0, 3)}.${digits.slice(3, 6)}.${digits.slice(6, 9)}-${digits.slice(9)}`;
        };

        const formatPhone = (value) => {
            const digits = digitsOnly(value, 11);
            if (digits.length === 0) return '';
            if (digits.length <= 2) return digits;
            if (digits.length <= 6) return `(${digits.slice(0, 2)}) ${digits.slice(2)}`;
            if (digits.length <= 10) return `(${digits.slice(0, 2)}) ${digits.slice(2, 6)}-${digits.slice(6)}`;
            return `(${digits.slice(0, 2)}) ${digits.slice(2, 7)}-${digits.slice(7)}`;
        };

        const formatCep = (value) => {
            const digits = digitsOnly(value, 8);
            if (digits.length <= 5) return digits;
            return `${digits.slice(0, 5)}-${digits.slice(5)}`;
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
