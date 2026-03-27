@extends('internal.layouts.app')

@section('title', 'Editar usuário | Painel Etika')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-semibold">Editar usuário</h1>
        <p class="text-sm text-slate-600">Altere dados do usuário e atualize a senha opcionalmente.</p>
    </div>

    @include('internal.users.form', [
        'action' => route('etika.users.update', $user),
        'method' => 'PUT',
        'user' => $user,
        'buttonLabel' => 'Salvar',
        'requirePassword' => false,
        'passwordHelpText' => 'Deixe em branco para manter a senha atual.',
    ])
</div>
@endsection
