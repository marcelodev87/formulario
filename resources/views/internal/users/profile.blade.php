@extends('internal.layouts.app')

@section('title', 'Meu perfil | Painel Etika')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-semibold">Meu perfil</h1>
        <p class="text-sm text-slate-600">Atualize seu nome, e-mail e senha.</p>
    </div>

    <div class="card space-y-6">
        <form method="POST" action="{{ route('etika.users.profile.update') }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="space-y-1">
                <label for="name" class="form-label">Nome</label>
                <input id="name" name="name" type="text" class="form-control" value="{{ old('name', $user->name) }}" required>
            </div>

            <div class="space-y-1">
                <label for="email" class="form-label">E-mail</label>
                <input id="email" name="email" type="email" class="form-control" value="{{ old('email', $user->email) }}" required>
            </div>

            <div class="space-y-1">
                <label for="current_password" class="form-label">Senha atual</label>
                <input id="current_password" name="current_password" type="password" class="form-control">
            </div>

            <div class="space-y-1">
                <label for="password" class="form-label">Nova senha</label>
                <input id="password" name="password" type="password" class="form-control">
                <small class="text-slate-500">Nova senha deve ter 8+ chars com maiúscula, minúscula, número e símbolo.</small>
            </div>

            <div class="space-y-1">
                <label for="password_confirmation" class="form-label">Confirmar nova senha</label>
                <input id="password_confirmation" name="password_confirmation" type="password" class="form-control">
            </div>

            <button type="submit" class="btn">Salvar alterações</button>
        </form>
    </div>
</div>
@endsection
