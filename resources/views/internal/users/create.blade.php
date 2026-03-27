@extends('internal.layouts.app')

@section('title', 'Novo usuário | Painel Etika')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold">Criar usuário</h1>
            <p class="text-sm text-slate-600">Preencha os dados para adicionar ao painel.</p>
        </div>
        <a href="{{ route('etika.users.index') }}" class="btn-secondary">Voltar</a>
    </div>

    @include('internal.users.form', [
        'action' => route('etika.users.store'),
        'method' => 'POST',
        'buttonLabel' => 'Criar',
        'requirePassword' => true,
        'passwordHelpText' => 'Senha de pelo menos 8 caracteres, com maiúsculas, minúsculas, números e símbolos.',
    ])
</div>
@endsection
