@extends('layouts.app')

@section('content')
<div class="space-y-8">
    <div class="card space-y-3">
        <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="text-xs uppercase tracking-wide text-slate-500">PROCESSO</p>
                <h1 class="text-2xl font-semibold text-slate-900">Registro de Ata de Eleição da Diretoria</h1>
                <p class="text-sm text-slate-600">Acompanhe e gerencie o processo de registro da ata de eleição da diretoria.</p>
            </div>
            <div class="rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-600">
                <p><span class="font-semibold text-slate-800">Instituição:</span> {{ $institution->name }}</p>
                <p><span class="font-semibold text-slate-800">Documento:</span> {{ $institution->document }}</p>
            </div>
        </div>
    </div>

    <div class="card space-y-6">
        <div class="flex flex-col md:flex-row md:gap-6 md:justify-between md:items-stretch">
            <!-- Link de compartilhamento -->
            <div class="flex-1 space-y-2 flex flex-col justify-between">
                <div>
                    <h2 class="text-lg font-semibold">Compartilhamento</h2>
                    <p class="text-sm text-slate-600">Compartilhe o link abaixo com os membros da diretoria para que realizem seu cadastro sem necessidade de login.</p>
                </div>
                <div class="flex items-center gap-2 mt-2">
                    <input type="text" class="form-control w-full" value="{{ $shareUrl }}" readonly>
                    <button type="button" class="btn-secondary-sm" onclick="navigator.clipboard.writeText('{{ $shareUrl }}')">Copiar link</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Card de andamento do processo e botões de configuração -->
    <div class="card space-y-4">
        <div class="flex flex-col gap-1 md:flex-row md:items-center md:justify-between">
            <p class="text-xs uppercase tracking-wide text-slate-500">Andamento do cadastro</p>
            <p class="text-xs text-slate-500">Acompanhe o progresso das principais etapas.</p>
        </div>
        <div class="grid gap-4 md:grid-cols-3">
            @foreach($statusItems as $item)
                <div class="flex h-full flex-col justify-between rounded-xl border border-slate-100 bg-white p-4 shadow-sm">
                    <div class="space-y-2">
                        <div class="flex items-center gap-2 text-sm font-semibold text-slate-700">
                            <span class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-slate-100 text-slate-600">
                                @switch($item['icon'])
                                    @case('pin')
                                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                            <path d="M10 2a5 5 0 0 0-5 5c0 3.866 5 11 5 11s5-7.134 5-11a5 5 0 0 0-5-5Zm0 7a2 2 0 1 1 0-4 2 2 0 0 1 0 4Z" />
                                        </svg>
                                        @break
                                    @case('building')
                                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                            <path d="M4 3a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v15h-3v-3a1 1 0 0 0-1-1H8a1 1 0 0 0-1 1v3H4V3Zm5 2H7v2h2V5Zm2 0v2h2V5h-2ZM7 9v2h2V9H7Zm4 0v2h2V9h-2ZM7 13v2h2v-2H7Z" />
                                        </svg>
                                        @break
                                    @case('user')
                                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                            <path d="M10 4a3 3 0 1 1 0 6 3 3 0 0 1 0-6Zm-6 11a6 6 0 0 1 12 0v1a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-1Z" />
                                        </svg>
                                        @break
                                    @default
                                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m5 10 3 3 7-7" />
                                        </svg>
                                @endswitch
                            </span>
                            <span>{{ $item['title'] }}</span>
                        </div>
                        <p class="text-xs text-slate-500">{{ $item['meta'] }}</p>
                        <p class="text-sm text-slate-600">{{ $item['description'] }}</p>
                    </div>
                    <div class="flex items-center justify-between pt-4">
                        <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $item['complete'] ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                            {{ $item['complete'] ? 'Concluido' : 'Pendente' }}
                        </span>
                        <a href="{{ $item['action'] }}" class="btn-secondary-sm">{{ $item['action_label'] }}</a>
                    </div>
                </div>
            @endforeach
        </div>
        <!-- Botões de configuração do processo -->
        {{-- <div class="flex flex-col md:flex-row md:justify-end gap-2 pt-4">
            <button type="button" class="btn" data-modal="mandate-config">Configurar mandato</button>
            <button type="button" class="btn-secondary" data-modal="upload-minutes">Anexar última ata</button>
            <button type="button" class="btn-secondary" data-modal="upload-bylaws">Anexar estatuto</button>
        </div> --}}
    </div>

    <!-- Modais (placeholders) -->
    <div id="modal-mandate-config" class="hidden">@include('processes.board_election.partials.mandate_config')</div>
    <div id="modal-upload-minutes" class="hidden">@include('processes.board_election.partials.upload_minutes')</div>
    <div id="modal-upload-bylaws" class="hidden">@include('processes.board_election.partials.upload_bylaws')</div>
</div>
@endsection
