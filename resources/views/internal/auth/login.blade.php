@extends('internal.layouts.app')

@section('title', 'Acesso Painel Etika')

@section('content')
<div class="mx-auto w-full max-w-md">
    <div class="card space-y-6">
        <div class="space-y-2 text-center">
            <h1 class="text-2xl font-semibold">Painel Etika</h1>
            <p class="text-sm text-slate-600">Informe suas credenciais para acessar o backoffice.</p>
        </div>
        <form method="POST" action="{{ route('etika.login.store') }}" class="space-y-4">
            @csrf
            <div class="space-y-1">
                <label for="email" class="form-label">E-mail</label>
                <input id="email" name="email" type="email" class="form-control" value="{{ old('email') }}" required autofocus>
            </div>
            <div class="space-y-1">
                <label for="password" class="form-label">Senha</label>
                <input id="password" name="password" type="password" class="form-control" required>
            </div>
            <div class="flex items-center justify-between">
                <label class="flex items-center gap-2 text-sm">
                    <input type="checkbox" name="remember" value="1" class="form-checkbox">
                    <span>Lembrar acesso</span>
                </label>
            </div>
            <button type="submit" class="btn w-full">Entrar</button>
        </form>
    </div>
</div>
@endsection
