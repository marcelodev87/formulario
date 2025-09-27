@extends('layouts.app')

@section('content')
@php($maritalStatuses = config('people.marital_statuses'); $genders = config('people.genders'))
<div class="card space-y-8">
    <div class="space-y-2">
        <h1 class="text-2xl font-semibold text-slate-900">Cadastro inicial da instituicao</h1>
        <p class="text-sm text-slate-600">Informe os dados do presidente e da instituicao para liberar o acesso ao painel.</p>
    </div>

    <form method="POST" action="{{ route('setup.store') }}" class="space-y-8">
        @csrf

        <section class="space-y-4">
            <h2 class="text-lg font-semibold text-slate-900">Dados do presidente</h2>
            <div class="grid gap-4 md:grid-cols-2">
                <div class="space-y-1">
                    <label class="form-label" for="owner_name">Nome completo</label>
                    <input id="owner_name" name="owner_name" type="text" class="form-control" value="{{ old('owner_name', auth()->user()->name) }}" required>
                </div>
                <div class="space-y-1">
                    <label class="form-label" for="owner_phone">Telefone</label>
                    <input id="owner_phone" name="owner_phone" type="text" class="form-control" value="{{ old('owner_phone', auth()->user()->phone) }}" data-mask="phone" placeholder="(00) 00000-0000" required>
                </div>
                <div class="space-y-1">
                    <label class="form-label" for="owner_cpf">CPF</label>
                    <input id="owner_cpf" name="owner_cpf" type="text" class="form-control" value="{{ old('owner_cpf', auth()->user()->cpf) }}" data-mask="cpf" placeholder="000.000.000-00" required>
                </div>
                <div class="space-y-1">
                    <label class="form-label" for="owner_email">E-mail</label>
                    <input id="owner_email" type="email" class="form-control bg-slate-100 text-slate-500" value="{{ auth()->user()->email }}" disabled>
                </div>
                <div class="space-y-1">
                    <label class="form-label" for="owner_birth_date">Data de nascimento</label>
                    <input id="owner_birth_date" name="owner_birth_date" type="date" class="form-control" value="{{ old('owner_birth_date', optional(optional($ownerMember)->birth_date)->format('Y-m-d')) }}" required>
                </div>
                <div class="space-y-1">
                    <label class="form-label" for="owner_birthplace">Naturalidade</label>
                    <input id="owner_birthplace" name="owner_birthplace" type="text" class="form-control" value="{{ old('owner_birthplace', optional($ownerMember)->birthplace) }}" required>
                </div>
                <div class="space-y-1">
                    <label class="form-label" for="owner_nationality">Nacionalidade</label>
                    <input id="owner_nationality" name="owner_nationality" type="text" class="form-control" value="{{ old('owner_nationality', optional($ownerMember)->nationality) }}" required>
                </div>
                <div class="space-y-1">
                    <label class="form-label" for="owner_father_name">Nome do pai</label>
                    <input id="owner_father_name" name="owner_father_name" type="text" class="form-control" value="{{ old('owner_father_name', optional($ownerMember)->father_name) }}" required>
                </div>
                <div class="space-y-1">
                    <label class="form-label" for="owner_mother_name">Nome da mae</label>
                    <input id="owner_mother_name" name="owner_mother_name" type="text" class="form-control" value="{{ old('owner_mother_name', optional($ownerMember)->mother_name) }}" required>
                </div>
                <div class="space-y-1">
                    <label class="form-label" for="owner_rg">RG</label>
                    <input id="owner_rg" name="owner_rg" type="text" class="form-control" value="{{ old('owner_rg', optional($ownerMember)->rg) }}" required>
                </div>
                <div class="space-y-1">
                    <label class="form-label" for="owner_rg_issuer">Orgao emissor</label>
                    <input id="owner_rg_issuer" name="owner_rg_issuer" type="text" class="form-control" value="{{ old('owner_rg_issuer', optional($ownerMember)->rg_issuer) }}" required>
                </div>
                <div class="space-y-1">
                    <label class="form-label" for="owner_gender">Genero</label>
                    <select id="owner_gender" name="owner_gender" class="form-control" required>
                        <option value="">Selecione</option>
                        <option value="Masculino" {{ old('owner_gender', optional($ownerMember)->gender) === 'Masculino' ? 'selected' : '' }}>Masculino</option>
                        <option value="Feminino" {{ old('owner_gender', optional($ownerMember)->gender) === 'Feminino' ? 'selected' : '' }}>Feminino</option>
                    </select>
                </div>
                <div class="space-y-1">
                    <label class="form-label" for="owner_marital_status">Estado civil</label>
                    <select id="owner_marital_status" name="owner_marital_status" class="form-control" required>
                        <option value="">Selecione</option>
                        <option value="Divorciado(a)" {{ old('owner_marital_status', optional($ownerMember)->marital_status) === 'Divorciado(a)' ? 'selected' : '' }}>Divorciado(a)</option>
                        <option value="Casado(a)" {{ old('owner_marital_status', optional($ownerMember)->marital_status) === 'Casado(a)' ? 'selected' : '' }}>Casado(a)</option>
                        <option value="Solteiro(a)" {{ old('owner_marital_status', optional($ownerMember)->marital_status) === 'Solteiro(a)' ? 'selected' : '' }}>Solteiro(a)</option>
                        <option value="Viúvo(a)" {{ old('owner_marital_status', optional($ownerMember)->marital_status) === 'Viúvo(a)' ? 'selected' : '' }}>Viúvo(a)</option>
                    </select>
                </div>
                <div class="space-y-1">
                    <label class="form-label" for="owner_profession">Profissao</label>
                    <input id="owner_profession" name="owner_profession" type="text" class="form-control" value="{{ old('owner_profession', optional($ownerMember)->profession) }}" required>
                </div>
            </div>
        </section>

        <section class="space-y-4">
            <h2 class="text-lg font-semibold text-slate-900">Endereco do presidente</h2>
            <div class="grid gap-4 md:grid-cols-2">
                <div class="space-y-1 md:col-span-2">
                    <label class="form-label" for="owner_street">Logradouro</label>
                    <input id="owner_street" name="owner_street" type="text" class="form-control" value="{{ old('owner_street', optional($ownerMember)->street) }}" required>
                </div>
                <div class="space-y-1">
                    <label class="form-label" for="owner_number">Numero</label>
                    <input id="owner_number" name="owner_number" type="text" class="form-control" value="{{ old('owner_number', optional($ownerMember)->number) }}" required>
                </div>
                <div class="space-y-1">
                    <label class="form-label" for="owner_complement">Complemento</label>
                    <input id="owner_complement" name="owner_complement" type="text" class="form-control" value="{{ old('owner_complement', optional($ownerMember)->complement) }}">
                </div>
                <div class="space-y-1">
                    <label class="form-label" for="owner_district">Bairro</label>
                    <input id="owner_district" name="owner_district" type="text" class="form-control" value="{{ old('owner_district', optional($ownerMember)->district) }}" required>
                </div>
                <div class="space-y-1">
                    <label class="form-label" for="owner_city">Cidade</label>
                    <input id="owner_city" name="owner_city" type="text" class="form-control" value="{{ old('owner_city', optional($ownerMember)->city) }}" required>
                </div>
                <div class="space-y-1">
                    <label class="form-label" for="owner_uf">UF</label>
                    <input id="owner_uf" name="owner_uf" type="text" maxlength="2" class="form-control uppercase" value="{{ old('owner_uf', optional($ownerMember)->uf) }}" required>
                </div>
                <div class="space-y-1">
                    <label class="form-label" for="owner_cep">CEP</label>
                    <input id="owner_cep" name="owner_cep" type="text" class="form-control" value="{{ old('owner_cep', optional($ownerMember)->cep) }}" data-mask="cep" placeholder="00000-000" required>
                </div>
            </div>
        </section>

        <section class="space-y-4">
            <h2 class="text-lg font-semibold text-slate-900">Dados da instituicao</h2>
            <div class="grid gap-4 md:grid-cols-2">
                <div class="space-y-1">
                    <label class="form-label" for="institution_name">Nome</label>
                    <input id="institution_name" name="institution_name" type="text" class="form-control" value="{{ old('institution_name', $currentInstitution->name ?? '') }}" required>
                </div>
                <div class="space-y-1">
                    <label class="form-label" for="institution_document">CNPJ ou CPF</label>
                    <input id="institution_document" name="institution_document" type="text" class="form-control" value="{{ old('institution_document', $currentInstitution->document ?? '') }}" placeholder="00.000.000/0000-00" data-mask="document" required>
                </div>
                <div class="space-y-1">
                    <label class="form-label" for="institution_email">E-mail institucional</label>
                    <input id="institution_email" name="institution_email" type="email" class="form-control" value="{{ old('institution_email', $currentInstitution->email ?? '') }}" required>
                </div>
                <div class="space-y-1">
                    <label class="form-label" for="institution_phone">Telefone institucional</label>
                    <input id="institution_phone" name="institution_phone" type="text" class="form-control" value="{{ old('institution_phone', $currentInstitution->phone ?? '') }}" data-mask="phone" placeholder="(00) 00000-0000" required>
                </div>
                <div class="space-y-1">
                    <label class="form-label" for="institution_street">Logradouro</label>
                    <input id="institution_street" name="institution_street" type="text" class="form-control" value="{{ old('institution_street', $currentInstitution->street ?? '') }}" required>
                </div>
                <div class="space-y-1">
                    <label class="form-label" for="institution_number">Numero</label>
                    <input id="institution_number" name="institution_number" type="text" class="form-control" value="{{ old('institution_number', $currentInstitution->number ?? '') }}" required>
                </div>
                <div class="space-y-1">
                    <label class="form-label" for="institution_complement">Complemento</label>
                    <input id="institution_complement" name="institution_complement" type="text" class="form-control" value="{{ old('institution_complement', $currentInstitution->complement ?? '') }}">
                </div>
                <div class="space-y-1">
                    <label class="form-label" for="institution_district">Bairro</label>
                    <input id="institution_district" name="institution_district" type="text" class="form-control" value="{{ old('institution_district', $currentInstitution->district ?? '') }}" required>
                </div>
                <div class="space-y-1">
                    <label class="form-label" for="institution_city">Cidade</label>
                    <input id="institution_city" name="institution_city" type="text" class="form-control" value="{{ old('institution_city', $currentInstitution->city ?? '') }}" required>
                </div>
                <div class="space-y-1">
                    <label class="form-label" for="institution_uf">UF</label>
                    <input id="institution_uf" name="institution_uf" type="text" maxlength="2" class="form-control uppercase" value="{{ old('institution_uf', $currentInstitution->uf ?? '') }}" placeholder="UF" required>
                </div>
                <div class="space-y-1">
                    <label class="form-label" for="institution_cep">CEP</label>
                    <input id="institution_cep" name="institution_cep" type="text" class="form-control" value="{{ old('institution_cep', $currentInstitution->cep ?? '') }}" data-mask="cep" placeholder="00000-000" required>
                </div>
            </div>
        </section>

        <div class="flex justify-end">
            <button type="submit" class="btn">Salvar e ir para o dashboard</button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const digitsOnly = (value, max) => value.replace(/\D/g, '').slice(0, max);

        const formatCpf = (value) => {
            const digits = digitsOnly(value, 11);
            if (digits.length <= 3) return digits;
            if (digits.length <= 6) return `${digits.slice(0, 3)}.${digits.slice(3)}`;
            if (digits.length <= 9) return `${digits.slice(0, 3)}.${digits.slice(3, 6)}.${digits.slice(6)}`;
            return `${digits.slice(0, 3)}.${digits.slice(3, 6)}.${digits.slice(6, 9)}-${digits.slice(9)}`;
        };

        const formatCnpj = (value) => {
            const digits = digitsOnly(value, 14);
            if (digits.length <= 2) return digits;
            if (digits.length <= 5) return `${digits.slice(0, 2)}.${digits.slice(2)}`;
            if (digits.length <= 8) return `${digits.slice(0, 2)}.${digits.slice(2, 5)}.${digits.slice(5)}`;
            if (digits.length <= 12) {
                return `${digits.slice(0, 2)}.${digits.slice(2, 5)}.${digits.slice(5, 8)}/${digits.slice(8)}`;
            }
            return `${digits.slice(0, 2)}.${digits.slice(2, 5)}.${digits.slice(5, 8)}/${digits.slice(8, 12)}-${digits.slice(12)}`;
        };

        const formatPhone = (value) => {
            const digits = digitsOnly(value, 11);
            if (digits.length === 0) return '';
            if (digits.length <= 2) return digits;
            if (digits.length <= 6) return `(${digits.slice(0, 2)}) ${digits.slice(2)}`;
            if (digits.length <= 10) {
                return `(${digits.slice(0, 2)}) ${digits.slice(2, 6)}-${digits.slice(6)}`;
            }
            return `(${digits.slice(0, 2)}) ${digits.slice(2, 7)}-${digits.slice(7)}`;
        };

        const formatCep = (value) => {
            const digits = digitsOnly(value, 8);
            if (digits.length <= 5) return digits;
            return `${digits.slice(0, 5)}-${digits.slice(5)}`;
        };

        const formatDocument = (value) => {
            const digits = value.replace(/\D/g, '');
            return digits.length > 11 ? formatCnpj(digits) : formatCpf(digits);
        };

        const formatters = {
            cpf: formatCpf,
            phone: formatPhone,
            cep: formatCep,
            document: formatDocument,
        };

        const applyMask = (input) => {
            const formatter = formatters[input.dataset.mask];
            if (!formatter) {
                return;
            }

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
