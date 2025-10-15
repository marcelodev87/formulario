@extends('layouts.app')

@section('content')
<div class="w-full max-w-3xl mx-auto space-y-6">
    <div class="card space-y-4 text-center">
        <h1 class="text-2xl font-semibold text-slate-900">Cadastro de instituicao</h1>
        <p class="text-sm text-slate-600">
            A configuracao inicial agora acontece diretamente no painel principal.
            Use o botao abaixo para voltar ao dashboard e registrar a instituicao.
        </p>
        <a href="{{ route('dashboard') }}" class="btn w-full sm:w-auto mx-auto">Ir para o dashboard</a>
    </div>
</div>
@endsection
