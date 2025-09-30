@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-lg">
    <div class="card space-y-4 text-center">
        <h1 class="text-2xl font-semibold text-slate-900">Cadastro enviado</h1>
        <p class="text-sm text-slate-600">Obrigado por informar seus dados. A instituição {{ $institution->name }} já recebeu seu cadastro.</p>
        <p class="text-sm text-slate-500">Em caso de dúvidas, entre em contato pelo e-mail {{ $institution->email }}.</p>
    </div>
</div>
@endsection
