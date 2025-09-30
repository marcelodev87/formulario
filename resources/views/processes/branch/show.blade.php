@extends('layouts.app')

@section('content')
<div class="space-y-8">
    <div class="card space-y-3">
        <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-slate-900">{{ $process->title }}</h1>
                <p class="text-sm text-slate-600">Gestao do processo de abertura da filial.</p>
            </div>
            <div class="rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-600">
                <p><span class="font-semibold text-slate-800">Instituicao:</span> {{ $institution->name }}</p>
                <p><span class="font-semibold text-slate-800">Documento:</span> {{ $institution->document }}</p>
            </div>
        </div>

            <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 font-semibold text-slate-700">{{ $process->status_label }}</span>
            <span>Atualizado em {{ $process->updated_at->format('d/m/Y H:i') }}</span>
        </div>
        <div class="rounded-2xl border border-slate-200 p-4 text-sm text-slate-600">
            <p class="text-xs uppercase tracking-wide text-slate-500">Contato da filial</p>
            @if($leader)
                <p class="mt-1 text-sm text-slate-700">Dirigente: {{ $leader->name }}</p>
                <p class="text-sm text-slate-700">Telefone: {{ $leader->phone }}</p>
                <p class="text-sm text-slate-700">E-mail: {{ $leader->email }}</p>
            @else
                <p class="mt-1 text-sm text-slate-700">Cadastre o dirigente para definir o responsavel pela filial.</p>
                @php
                    $invite = \App\Models\BranchLeaderInvite::where('process_id', $process->id)->active()->first();
                @endphp
                @if($invite)
                <div class="card space-y-4">
                    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                        <h2 class="text-xl font-semibold text-slate-900">Convite para dirigente</h2>
                        <a href="{{ url('/cadastro-dirigente/' . $invite->key) }}" target="_blank" class="link text-sm font-semibold">Abrir formulario publico</a>
                    </div>
                    <div class="flex flex-col gap-3 md:flex-row md:items-center">
                        <input type="text" id="dirigente-link" value="{{ url('/cadastro-dirigente/' . $invite->key) }}" readonly class="form-control md:flex-1">
                        <button type="button" class="btn" id="copy-dirigente">Copiar link</button>
                    </div>
                    <p class="text-xs text-slate-500">O link permanece ativo ate ser revogado ou expirado.</p>
                </div>
                @push('scripts')
                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        var button = document.getElementById('copy-dirigente');
                        if (!button) return;
                        var input = document.getElementById('dirigente-link');
                        var defaultLabel = button.textContent;
                        button.addEventListener('click', async function () {
                            try {
                                await navigator.clipboard.writeText(input.value);
                                button.textContent = 'Copiado!';
                            } catch (error) {
                                input.select();
                                document.execCommand('copy');
                                button.textContent = 'Copiado!';
                            }
                            setTimeout(function () {
                                button.textContent = defaultLabel;
                            }, 2000);
                        });
                    });
                </script>
                @endpush
                @endif
            @endif
        </div>
    </div>

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
    </div>

    <div class="card space-y-4">
        <h2 class="text-lg font-semibold text-slate-900">Resumo</h2>
        <dl class="grid gap-4 md:grid-cols-2">
            <div class="rounded-xl border border-slate-200 p-4">
                <dt class="text-xs uppercase tracking-wide text-slate-500">Endereco da filial</dt>
                <dd class="mt-1 text-sm text-slate-700">
                    @if($location)
                        {{ $location->street }}, {{ $location->number }}
                        {{ $location->complement ? ' - ' . $location->complement : '' }}<br>
                        {{ $location->district }} - {{ $location->city }}/{{ $location->uf }}<br>
                        CEP {{ $location->cep }}
                    @else
                        Ainda nao informado.
                    @endif
                </dd>
            </div>
            <div class="rounded-xl border border-slate-200 p-4">
                <dt class="text-xs uppercase tracking-wide text-slate-500">Dirigente</dt>
                <dd class="mt-1 text-sm text-slate-700">
                    @if($leader)
                        {{ $leader->name }}<br>
                        CPF {{ $leader->cpf }}<br>
                        Telefone {{ $leader->phone }}
                    @else
                        Cadastre o dirigente para concluir a filial.
                    @endif
                </dd>
            </div>
        </dl>
    </div>
</div>
@endsection

