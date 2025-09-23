@extends('layouts.app')

@section('content')
<div class="card space-y-8">
    <div class="space-y-2">
        <h1 class="text-2xl font-semibold text-slate-900">Dados do imovel</h1>
        <p class="text-sm text-slate-600">Informe as caracteristicas do imovel utilizado pela instituicao.</p>
    </div>

    <form method="POST" action="{{ route('institution.property.update') }}" class="space-y-6">
        @csrf
        @method('PUT')

        <section class="space-y-4">
            <h2 class="text-lg font-semibold">Identificacao</h2>
            <div class="grid gap-4 md:grid-cols-2">
                <div class="space-y-1">
                    <label class="form-label" for="iptu_registration">Numero de inscricao do IPTU</label>
                    <input id="iptu_registration" name="iptu_registration" type="text" class="form-control" value="{{ old('iptu_registration', optional($property)->iptu_registration) }}" placeholder="0000000" />
                </div>
                <div class="space-y-1">
                    <label class="form-label" for="property_use">Tipo de imovel</label>
                    <input id="property_use" name="property_use" type="text" class="form-control" value="{{ old('property_use', optional($property)->property_use) }}" placeholder="Loja, galpao, residencia..." />
                </div>
                <div class="space-y-1">
                    <label class="form-label" for="property_section">Identificacao da unidade</label>
                    <input id="property_section" name="property_section" type="text" class="form-control" value="{{ old('property_section', optional($property)->property_section) }}" placeholder="Loja A, Casa 2..." />
                </div>
            </div>
        </section>

        <section class="space-y-4">
            <h2 class="text-lg font-semibold">Dimensoes</h2>
            <div class="grid gap-4 md:grid-cols-3">
                <div class="space-y-1">
                    <label class="form-label" for="built_area_sqm">Area construida (m2)</label>
                    <input id="built_area_sqm" name="built_area_sqm" type="number" step="0.01" min="0" class="form-control" value="{{ old('built_area_sqm', optional($property)->built_area_sqm) }}" />
                </div>
                <div class="space-y-1">
                    <label class="form-label" for="land_area_sqm">Area total do terreno (m2)</label>
                    <input id="land_area_sqm" name="land_area_sqm" type="number" step="0.01" min="0" class="form-control" value="{{ old('land_area_sqm', optional($property)->land_area_sqm) }}" />
                </div>
                <div class="space-y-1">
                    <label class="form-label" for="capacity">Capacidade de pessoas</label>
                    <input id="capacity" name="capacity" type="number" min="0" class="form-control" value="{{ old('capacity', optional($property)->capacity) }}" />
                </div>
            </div>
        </section>

        <section class="space-y-4">
            <h2 class="text-lg font-semibold">Uso do imovel</h2>
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
            </div>
        </section>

        <div class="flex flex-wrap justify-end gap-3">
            <a href="{{ route('dashboard') }}" class="btn-secondary">Voltar a Pagina Inicial</a>
            <button type="submit" class="btn">Salvar dados do imovel</button>
        </div>
    </form>
</div>
@endsection


