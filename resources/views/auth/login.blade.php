@extends('layouts.login')

@section('content')
<div class="card w-full max-w-md space-y-6 text-center">
    <span class="mx-auto brand-logo-wrapper">
        <img src="{{ asset('images/logo-ci.png') }}" alt="Contabilidade para Igrejas" class="brand-logo-img">
    </span>
    <div class="space-y-2">
        <h1 class="text-2xl font-semibold">Entrar com link magico</h1>
        <p class="text-sm text-slate-600">Informe o e-mail do presidente para receber um link de acesso valido por 30 minutos.</p>
    </div>
    <form method="POST" action="{{ route('auth.request-link') }}" class="space-y-4 text-left">
        @csrf
        <div class="space-y-1">
            <label class="form-label" for="email">E-mail</label>
            <input id="email" name="email" type="email" class="form-control" placeholder="presidente@instituicao.com" required autofocus>
        </div>
        <button type="submit" class="btn w-full justify-center">Enviar link de acesso</button>
    </form>
</div>
@endsection
