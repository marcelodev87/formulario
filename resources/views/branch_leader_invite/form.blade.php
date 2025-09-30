@extends('layouts.app')

@section('content')
@php($maritalStatuses = config('people.marital_statuses'))
@php($genders = config('people.genders'))
<div class="space-y-8">
    <div class="card space-y-3">
        <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="text-xs uppercase tracking-wide text-slate-500">Processo</p>
                <h1 class="text-2xl font-semibold text-slate-900">{{ $process->title ?? 'Cadastro do Dirigente' }}</h1>
                <p class="text-sm text-slate-600">Informe os dados completos do dirigente responsável pela filial.</p>
            </div>
            <div class="rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-600">
                <p><span class="font-semibold text-slate-800">Instituição:</span> {{ $process->institution->name ?? '-' }}</p>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('branch_leader_invite.store', $invite) }}" class="card space-y-6">
        @csrf
        <section class="space-y-4">
            <h2 class="text-lg font-semibold text-slate-900">Dados pessoais</h2>
            <div class="grid gap-4 md:grid-cols-2">
                <div class="space-y-1 md:col-span-2">
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
                    <label class="form-label" for="mother_name">Nome da mãe</label>
                    <input id="mother_name" name="mother_name" type="text" class="form-control" value="{{ old('mother_name') }}" required>
                </div>
                <div class="space-y-1">
                    <label class="form-label" for="cpf">CPF</label>
                    <input id="cpf" name="cpf" type="text" class="form-control" value="{{ old('cpf') }}" required>
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
                    <label class="form-label" for="gender">Gênero</label>
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
                <div class="space-y-1 md:col-span-2">
                    <label class="form-label" for="profession">Profissão</label>
                    <input id="profession" name="profession" type="text" class="form-control" value="{{ old('profession') }}" required>
                </div>
            </div>
        </section>
        <section class="space-y-4">
            <h2 class="text-lg font-semibold text-slate-900">Contato</h2>
            <div class="grid gap-4 md:grid-cols-2">
                <div class="space-y-1">
                    <label class="form-label" for="email">E-mail</label>
                    <input id="email" name="email" type="email" class="form-control" value="{{ old('email') }}" required>
                </div>
                <div class="space-y-1">
                    <label class="form-label" for="phone">Telefone</label>
                    <input id="phone" name="phone" type="text" class="form-control" value="{{ old('phone') }}" required>
                </div>
            </div>
        </section>
        <section class="space-y-4">
            <h2 class="text-lg font-semibold text-slate-900">Endereço residencial</h2>
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
                    <input id="cep" name="cep" type="text" class="form-control" value="{{ old('cep') }}" required>
                </div>
            </div>
        </section>
        <button type="submit" class="btn w-full bg-blue-700 text-white">Cadastrar dirigente</button>
    </form>
</div>
@endsection
