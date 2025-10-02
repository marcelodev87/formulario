@extends('layouts.app')

@section('content')
@php
    $membersCount = $members->count();
    $redirectParams = [
        'redirect_to' => \App\Models\Process::TYPE_BOARD_ELECTION_MINUTES_REGISTRATION,
        'process_id' => $process->id,
    ];
    $maritalStatuses = config('people.marital_statuses');
    $genders = config('people.genders');
@endphp
<div class="space-y-8">
    <div class="card space-y-6">
        <div class="space-y-2">
            <h2 class="text-lg font-semibold text-slate-900">Cadastrar novo membro</h2>
            <p class="text-sm text-slate-600">Preencha os dados abaixo para adicionar um membro da diretoria neste processo.</p>
        </div>
        @if(session('status'))
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('status') }}
            </div>
        @endif
        @if($errors->any())
            <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                <p class="font-semibold">Revise os campos abaixo:</p>
                <ul class="mt-2 list-disc space-y-1 pl-5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form method="POST" action="{{ route('processes.board_election.members.store', $process) }}" class="space-y-6">
            @csrf
            <section class="space-y-4">
                <h3 class="text-lg font-semibold">Dados pessoais</h3>
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
                        <label class="form-label" for="father_name">Nome do pai</label>
                        <input id="father_name" name="father_name" type="text" class="form-control" value="{{ old('father_name') }}" required>
                    </div>
                    <div class="space-y-1">
                        <label class="form-label" for="mother_name">Nome da mae</label>
                        <input id="mother_name" name="mother_name" type="text" class="form-control" value="{{ old('mother_name') }}" required>
                    </div>
                    <div class="space-y-1">
                        <label class="form-label" for="cpf">CPF</label>
                        <input id="cpf" name="cpf" type="text" class="form-control" data-mask="cpf" placeholder="000.000.000-00" value="{{ old('cpf') }}" required>
                    </div>
                    <div class="space-y-1">
                        <label class="form-label" for="rg">RG</label>
                        <input id="rg" name="rg" type="text" class="form-control" value="{{ old('rg') }}" required>
                    </div>
                    <div class="space-y-1">
                        <label class="form-label" for="rg_issuer">Orgao emissor</label>
                        <input id="rg_issuer" name="rg_issuer" type="text" class="form-control" value="{{ old('rg_issuer') }}" required>
                    </div>
                    <div class="space-y-1">
                        <label class="form-label" for="role">Cargo</label>
                        <select id="role" name="role" class="form-control" required>
                            <option value="">Selecione</option>
                            <option value="Presidente" {{ old('role') === 'Presidente' ? 'selected' : '' }}>Presidente</option>
                            <option value="Vice Presidente" {{ old('role') === 'Vice Presidente' ? 'selected' : '' }}>Vice Presidente</option>
                            <option value="Tesoureiro" {{ old('role') === 'Tesoureiro' ? 'selected' : '' }}>Tesoureiro</option>
                            <option value="Segundo Tesoureiro" {{ old('role') === 'Segundo Tesoureiro' ? 'selected' : '' }}>Segundo Tesoureiro</option>
                            <option value="Secretario" {{ old('role') === 'Secretario' ? 'selected' : '' }}>Secretario</option>
                            <option value="Segundo Secretario" {{ old('role') === 'Segundo Secretario' ? 'selected' : '' }}>Segundo Secretario</option>
                            <option value="Conselho Fiscal" {{ old('role') === 'Conselho Fiscal' ? 'selected' : '' }}>Conselho Fiscal</option>
                        </select>
                    </div>
                    <div class="space-y-1">
                        <label class="form-label" for="gender">Genero</label>
                        <select id="gender" name="gender" class="form-control" required>
                            <option value="">Selecione</option>
                            @foreach($genders as $gender)
                                <option value="{{ $gender }}" {{ old('gender') === $gender ? 'selected' : '' }}>{{ $gender }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="space-y-1">
                        <label class="form-label" for="marital_status">Estado civil</label>
                        <select id="marital_status" name="marital_status" class="form-control" required>
                            <option value="">Selecione</option>
                            @foreach($maritalStatuses as $status)
                                <option value="{{ $status }}" {{ old('marital_status') === $status ? 'selected' : '' }}>{{ $status }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="space-y-1">
                        <label class="form-label" for="profession">Profissao</label>
                        <input id="profession" name="profession" type="text" class="form-control" value="{{ old('profession') }}" required>
                    </div>
                    <div class="space-y-1">
                        <label class="form-label" for="email">Email</label>
                        <input id="email" name="email" type="email" class="form-control" value="{{ old('email') }}" required>
                    </div>
                    <div class="space-y-1">
                        <label class="form-label" for="phone">Telefone</label>
                        <input id="phone" name="phone" type="text" class="form-control" data-mask="phone" value="{{ old('phone') }}" required>
                    </div>
                </div>
            </section>

            <section class="space-y-4">
                <h3 class="text-lg font-semibold">Endereco</h3>
                <div class="grid gap-4 md:grid-cols-2">
                    <div class="space-y-1">
                        <label class="form-label" for="street">Rua</label>
                        <input id="street" name="street" type="text" class="form-control" value="{{ old('street') }}" required>
                    </div>
                    <div class="space-y-1">
                        <label class="form-label" for="number">Numero</label>
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
                        <input id="cep" name="cep" type="text" class="form-control" data-mask="cep" placeholder="00000-000" value="{{ old('cep') }}" required>
                    </div>
                </div>
            </section>

            <div class="flex justify-end gap-3">
                <button type="submit" class="btn">Salvar membro</button>
            </div>
        </form>
    </div>

</div>
@endsection









@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var digitsOnly = function (value, max) {
            return value.replace(/\D/g, '').slice(0, max);
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

        var applyMask = function (input) {
            var formatter = formatters[input.dataset.mask];
            if (!formatter) return;

            var formatted = formatter(input.value || '');
            input.value = formatted;

            if (document.activeElement === input) {
                var pos = formatted.length;
                input.setSelectionRange(pos, pos);
            }
        };

        document.querySelectorAll('[data-mask]').forEach(function (input) {
            applyMask(input);
            input.addEventListener('input', function () { applyMask(input); });
            input.addEventListener('blur', function () { applyMask(input); });
        });
    });
</script>
@endpush
