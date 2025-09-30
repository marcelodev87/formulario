@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto card p-6">
    <h2 class="text-xl font-semibold mb-4">Cópia do Estatuto</h2>
    <form method="POST" action="{{ route('processes.bylaws_revision.save_statute', $process) }}" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label class="form-label">Arquivo do Estatuto (PDF ou Word)</label>
            <input type="file" name="estatuto_file" class="form-control" accept=".pdf,.doc,.docx">
            <small class="text-slate-500">Opcional. Envie o estatuto atualizado, se desejar.</small>
        </div>
        <button type="submit" class="btn w-full bg-blue-700 text-white">Enviar arquivo</button>
    </form>
    @if(isset($process->answers['estatuto_file']))
        <div class="mt-4">
            <span class="font-semibold">Arquivo já enviado:</span>
            <a href="{{ asset('storage/' . $process->answers['estatuto_file']) }}" target="_blank" class="text-blue-700 underline">Visualizar arquivo</a>
        </div>
    @endif
</div>
@endsection
