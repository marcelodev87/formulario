@extends('layouts.app')

@section('content')
<div class="w-full max-w-3xl mx-auto space-y-6">
    <div class="card space-y-4">
        <div class="space-y-2">
            <h1 class="text-2xl font-semibold text-slate-900">Cadastre sua instituicao</h1>
            <p class="text-sm text-slate-600">Preencha os dados abaixo para iniciar o acompanhamento dos processos no painel.</p>
        </div>
        <form method="POST" action="{{ route('setup.store') }}" class="space-y-4">
            @csrf
            <div class="space-y-1">
                <label class="form-label" for="institution_name">Nome da instituicao</label>
                <input id="institution_name" name="institution_name" type="text" class="form-control" value="{{ old('institution_name') }}" required>
            </div>
            <div class="space-y-1">
                <label class="form-label" for="institution_document">Documento (CNPJ ou CPF do Presidente, caso a Igreja ainda não tenha um CNPJ)</label>
                <input id="institution_document" name="institution_document" type="text" class="form-control" value="{{ old('institution_document') }}" data-mask="document" placeholder="00.000.000/0000-00" required>
            </div>
            <div class="space-y-1">
                <label class="form-label" for="institution_email">E-mail</label>
                <input id="institution_email" name="institution_email" type="email" class="form-control" value="{{ old('institution_email', auth()->user()->email) }}" required>
            </div>
            <div class="space-y-1">
                <label class="form-label" for="institution_phone">Telefone</label>
                <input id="institution_phone" name="institution_phone" type="text" class="form-control" value="{{ old('institution_phone') }}" data-mask="phone" placeholder="(00) 00000-0000" required>
            </div>
            <input type="hidden" name="owner_user_id" value="{{ auth()->id() }}">
            <p class="text-xs text-slate-500">Responsavel vinculado: {{ auth()->user()->email }} (ID {{ auth()->id() }})</p>
            <button type="submit" class="btn w-full">Salvar instituicao</button>
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

        var formatCnpj = function (value) {
            var digits = digitsOnly(value, 14);
            if (digits.length <= 2) return digits;
            if (digits.length <= 5) return digits.slice(0, 2) + '.' + digits.slice(2);
            if (digits.length <= 8) return digits.slice(0, 2) + '.' + digits.slice(2, 5) + '.' + digits.slice(5);
            if (digits.length <= 12) return digits.slice(0, 2) + '.' + digits.slice(2, 5) + '.' + digits.slice(5, 8) + '/' + digits.slice(8);
            return digits.slice(0, 2) + '.' + digits.slice(2, 5) + '.' + digits.slice(5, 8) + '/' + digits.slice(8, 12) + '-' + digits.slice(12);
        };

        var formatCpf = function (value) {
            var digits = digitsOnly(value, 11);
            if (digits.length <= 3) return digits;
            if (digits.length <= 6) return digits.slice(0, 3) + '.' + digits.slice(3);
            if (digits.length <= 9) return digits.slice(0, 3) + '.' + digits.slice(3, 6) + '.' + digits.slice(6);
            return digits.slice(0, 3) + '.' + digits.slice(3, 6) + '.' + digits.slice(6, 9) + '-' + digits.slice(9);
        };

        var formatDocument = function (value) {
            var digits = value.replace(/\D/g, '');
            return digits.length > 11 ? formatCnpj(value) : formatCpf(value);
        };

        var formatPhone = function (value) {
            var digits = digitsOnly(value, 11);
            if (digits.length === 0) return '';
            if (digits.length <= 2) return digits;
            if (digits.length <= 6) return '(' + digits.slice(0, 2) + ') ' + digits.slice(2);
            if (digits.length <= 10) return '(' + digits.slice(0, 2) + ') ' + digits.slice(2, 6) + '-' + digits.slice(6);
            return '(' + digits.slice(0, 2) + ') ' + digits.slice(2, 7) + '-' + digits.slice(7);
        };

        var formatters = {
            document: formatDocument,
            phone: formatPhone
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
