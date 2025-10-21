@extends('layouts.app')

@section('content')
@php
    $showAddressCard = in_array('mudanca_endereco', $motivos ?? []);
    $location = $location ?? null;
    $addressComplete = $addressComplete ?? false;
@endphp
<div class="space-y-8">
    <div class="card space-y-2">
        <h1 class="text-2xl font-semibold text-slate-900">Reforma de Estatuto - Dashboard</h1>
        <p class="text-sm text-slate-600">Acompanhe o progresso das principais etapas e acesse os cadastros conforme as opcoes escolhidas.</p>
    </div>

    @if($showAddressCard)
        <div class="card p-6 space-y-4">
            <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-slate-900">Novo endereco</h2>
                    <p class="text-sm text-slate-600">Informe e salve o endereco atualizado que sera refletido no estatuto.</p>
                </div>
                <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $addressComplete ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                    {{ $addressComplete ? 'Concluido' : 'Pendente' }}
                </span>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-4 text-sm text-slate-700">
                @if($addressComplete && $location)
                    <p>{{ $location->street }}, {{ $location->number }} {{ $location->complement ? '- ' . $location->complement : '' }}</p>
                    <p>{{ $location->district }} - {{ $location->city }}/{{ $location->uf }}</p>
                    <p>CEP {{ $location->cep }}</p>
                @else
                    <p>Nenhum endereco cadastrado ainda.</p>
                @endif
            </div>

            <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                <a href="{{ route('processes.bylaws_revision.edit_motivo', [$process, 'mudanca_endereco']) }}" class="btn md:w-auto">
                    {{ $addressComplete ? 'Editar endereco' : 'Preencher endereco' }}
                </a>
            </div>
        </div>
    @endif

    <div class="grid gap-6 md:grid-cols-2">
        <div class="card space-y-3">
            <h2 class="text-lg font-semibold text-slate-900">Copia do Estatuto</h2>
            <p class ="text-sm text-slate-600">Envie o estatuto atual em PDF ou Word (opcional).</p>
            @if(isset($process->answers['estatuto_file']))
                <div class="flex flex-wrap items-center gap-2">
                    @php
                        $estatutoUrl = asset('storage/' . $process->answers['estatuto_file']);
                        $isPdf = \Illuminate\Support\Str::endsWith(strtolower($process->answers['estatuto_file']), '.pdf');
                    @endphp
                    <a href="{{ $estatutoUrl }}" target="_blank" class="btn-secondary">{{ $isPdf ? 'Visualizar PDF' : 'Baixar arquivo' }}</a>
                    <form method="POST" action="{{ route('processes.bylaws_revision.delete_statute', $process) }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-secondary bg-red-100 text-red-700 hover:bg-red-200">Excluir</button>
                    </form>
                </div>
            @else
                <a href="{{ route('processes.bylaws_revision.upload_statute', $process) }}" class="btn-secondary">Enviar arquivo</a>
            @endif
        </div>

        <div class="card space-y-3">
            <h2 class="text-lg font-semibold text-slate-900">Atualizacao do Estatuto</h2>
            <p class="text-sm text-slate-600">Certifique-se de que o novo endereco esta refletido no estatuto antes de finalizarmos o processo.</p>
            <p class="text-xs text-slate-500">Caso precise de auxilio, entre em contato com o suporte.</p>
        </div>
    </div>
</div>
@endsection
