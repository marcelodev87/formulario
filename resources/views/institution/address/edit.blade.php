@extends('layouts.app')

@section('content')
<div class="card space-y-8">
    <div class="space-y-2">
        <h1 class="text-2xl font-semibold text-slate-900">Editar endereco da instituicao</h1>
        <p class="text-sm text-slate-600">Atualize as informacoes de localizacao utilizadas em documentos e formularios oficiais.</p>
    </div>

    <form method="POST" action="{{ route('institution.address.update') }}" class="space-y-6">
        @csrf
        @method('PUT')

        <section class="space-y-4">
            <h2 class="text-lg font-semibold">Endereco</h2>
            <div class="grid gap-4 md:grid-cols-2">
                <div class="space-y-1 md:col-span-2">
                    <label class="form-label" for="street">Logradouro</label>
                    <input id="street" name="street" type="text" class="form-control" value="{{ old('street', $institution->street) }}" required>
                </div>
                <div class="space-y-1">
                    <label class="form-label" for="number">Numero</label>
                    <input id="number" name="number" type="text" class="form-control" value="{{ old('number', $institution->number) }}" required>
                </div>
                <div class="space-y-1">
                    <label class="form-label" for="complement">Complemento</label>
                    <input id="complement" name="complement" type="text" class="form-control" value="{{ old('complement', $institution->complement) }}">
                </div>
                <div class="space-y-1">
                    <label class="form-label" for="district">Bairro</label>
                    <input id="district" name="district" type="text" class="form-control" value="{{ old('district', $institution->district) }}" required>
                </div>
                <div class="space-y-1">
                    <label class="form-label" for="city">Cidade</label>
                    <input id="city" name="city" type="text" class="form-control" value="{{ old('city', $institution->city) }}" required>
                </div>
                <div class="space-y-1">
                    <label class="form-label" for="uf">UF</label>
                    <input id="uf" name="uf" type="text" maxlength="2" class="form-control uppercase" value="{{ old('uf', $institution->uf) }}" required>
                </div>
                <div class="space-y-1">
                    <label class="form-label" for="cep">CEP</label>
                    <input id="cep" name="cep" type="text" class="form-control" value="{{ old('cep', $institution->cep) }}" placeholder="00000-000" required>
                </div>
            </div>
        </section>

        <div class="flex flex-wrap justify-end gap-3">
            <a href="{{ route('dashboard') }}" class="btn-secondary">Voltar a Pagina Inicial</a>
            <button type="submit" class="btn">Salvar endereco</button>
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

        var cepInput = document.getElementById('cep');
        if (cepInput) {
            var formatCep = function (value) {
                var digits = (value || '').replace(/\D/g, '').slice(0, 8);
                if (digits.length <= 5) {
                    return digits;
                }
                return digits.slice(0, 5) + '-' + digits.slice(5);
            };

            var applyCepMask = function () {
                var formatted = formatCep(cepInput.value);
                cepInput.value = formatted;
                if (document.activeElement === cepInput) {
                    var pos = formatted.length;
                    cepInput.setSelectionRange(pos, pos);
                }
            };

            cepInput.addEventListener('input', applyCepMask);
            applyCepMask();
        }
    });
</script>
@endpush


