@extends('layouts.app')

@section('content')
@php
    $isNewLocation = !$location->exists;
@endphp
<div class="space-y-8">
    <div class="card space-y-3">
        <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="text-xs uppercase tracking-wide text-slate-500">Processo</p>
                <h1 class="text-2xl font-semibold text-slate-900">{{ $process->title }}</h1>
                <p class="text-sm text-slate-600">Informe os dados de endereco e do imovel da filial.</p>
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
    </div>

    <form method="POST" action="{{ route('processes.branch.location.update', $process) }}" class="card space-y-6">
        @csrf
        @method('PUT')

        <section class="space-y-4">
            <h2 class="text-lg font-semibold text-slate-900">Identificacao da filial</h2>
            <div class="grid gap-4 md:grid-cols-2">
                <div class="space-y-1 md:col-span-2">
                    <label class="form-label" for="branch-name">Nome ou referencia</label>
                    <input id="branch-name" name="name" type="text" class="form-control" value="{{ old('name', $location->name) }}" placeholder="Ex.: Filial Centro">
                </div>
            </div>
        </section>

        <section class="space-y-4">
            <h2 class="text-lg font-semibold text-slate-900">Endereco</h2>
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
                    <input id="cep" name="cep" type="text" class="form-control" value="{{ old('cep', $location->cep) }}" data-mask="cep" placeholder="00000-000" required>
                </div>
            </div>
        </section>

        <section id="property-section" class="space-y-4">
            <h2 class="text-lg font-semibold text-slate-900">Dados do imovel</h2>
            <div class="grid gap-4 md:grid-cols-3">
                <div class="space-y-1 md:col-span-3">
                    <label class="form-label" for="iptu_registration">Numero de inscricao do IPTU</label>
                    <input id="iptu_registration" name="iptu_registration" type="text" class="form-control" value="{{ old('iptu_registration', optional($property)->iptu_registration) }}" placeholder="0000000" />
                </div>
                <div class="space-y-1">
                    <label class="form-label" for="built_area_sqm">Area construida (mÂ²)</label>
                    <input id="built_area_sqm" name="built_area_sqm" type="number" step="0.01" min="0" class="form-control" value="{{ old('built_area_sqm', optional($property)->built_area_sqm) }}" />
                </div>
                <div class="space-y-1">
                    <label class="form-label" for="land_area_sqm">Area total do terreno (mÂ²)</label>
                    <input id="land_area_sqm" name="land_area_sqm" type="number" step="0.01" min="0" class="form-control" value="{{ old('land_area_sqm', optional($property)->land_area_sqm) }}" />
                </div>
                <div class="space-y-1">
                    <label class="form-label" for="capacity">Capacidade de pessoas</label>
                    <input id="capacity" name="capacity" type="number" min="0" class="form-control" value="{{ old('capacity', optional($property)->capacity) }}" />
                </div>
            </div>
            <div class="grid gap-4 md:grid-cols-2">
                <div class="space-y-1">
                    <span class="form-label">Situacao do imovel</span>
                    <label class="flex items-center gap-2 text-sm">
                        <input type="radio" name="tenure_type" value="own" {{ old('tenure_type', optional($property)->tenure_type) === 'own' ? 'checked' : '' }} required>
                        Proprio
                    </label>
                    <label class="flex items-center gap-2 text-sm">
                        <input type="radio" name="tenure_type" value="rented" {{ old('tenure_type', optional($property)->tenure_type) === 'rented' ? 'checked' : '' }}>
                        Alugado
                    </label>
                </div>
                <div class="space-y-1">
                    <label class="form-label" for="floors">Quantidade de andares</label>
                    <input id="floors" name="floors" type="number" min="0" class="form-control" value="{{ old('floors', optional($property)->floors) }}" />
                </div>
                <div class="space-y-1 md:col-span-2">
                    <label class="form-label" for="activity_floor">Andar onde acontecem as atividades</label>
                    <input id="activity_floor" name="activity_floor" type="text" class="form-control" value="{{ old('activity_floor', optional($property)->activity_floor) }}" placeholder="Ex.: terreo" />
                </div>
                <div class="space-y-1">
                    <label class="form-label" for="property_use">Tipo de imovel</label>
                    <input id="property_use" name="property_use" type="text" class="form-control" value="{{ old('property_use', optional($property)->property_use) }}" placeholder="Loja, galpao, sala comercial..." />
                </div>
                <div class="space-y-1">
                    <label class="form-label" for="property_section">Identificacao da unidade</label>
                    <input id="property_section" name="property_section" type="text" class="form-control" value="{{ old('property_section', optional($property)->property_section) }}" placeholder="Loja A, Sala 2..." />
                </div>
            </div>
        </section>

        <div class="flex flex-wrap justify-end gap-3">
            <a href="{{ route('processes.show', $process) }}" class="btn-secondary-sm">Visao geral do processo</a>
            @if($location->exists)
                <a href="{{ route('processes.branch.leader.edit', $process) }}" class="btn-secondary-sm">Dados do dirigente</a>
            @endif
            <button type="submit" class="btn">Salvar dados da filial</button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var ufInput = document.getElementById('uf');
        if (ufInput) {
            ufInput.addEventListener('input', function () {
                var value = (this.value || '').toUpperCase().replace(/[^A-Z]/g, '').slice(0, 2);
                this.value = value;
            });
        }

        var digitsOnly = function (value, max) {
            return (value || '').replace(/\D/g, '').slice(0, max);
        };

        var formatCep = function (value) {
            var digits = digitsOnly(value, 8);
            if (digits.length <= 5) {
                return digits;
            }
            return digits.slice(0, 5) + '-' + digits.slice(5);
        };

        var formatters = {
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


