@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-md">
    <div class="card space-y-4 text-center">
        <h1 class="text-2xl font-semibold text-slate-900">Link inválido ou expirado</h1>
        <p class="text-sm text-slate-600">Não foi possível validar o link informado. Solicite um novo acesso ou entre em contato com o suporte da instituição.</p>
        <a href="{{ route('login') }}" class="btn w-full justify-center">Solicitar novo link</a>
    </div>
</div>
@endsection