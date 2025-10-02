@extends('layouts.app')

@push('styles')
<style>[x-cloak]{ display:none !important; }</style>
@endpush

@section('content')
<div x-data="{ modal: null }" @keydown.escape.window="modal = null" class="space-y-8">
    <div class="card space-y-3">
        <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="text-xs uppercase tracking-wide text-slate-500">Processo</p>
                <h1 class="text-2xl font-semibold text-slate-900">Registro de Ata de Eleicao da Diretoria</h1>
                <p class="text-sm text-slate-600">Acompanhe e gerencie o processo de registro da ata de eleicao da diretoria.</p>
            </div>
            <div class="rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-600">
                <p><span class="font-semibold text-slate-800">Instituicao:</span> {{ $institution->name }}</p>
                <p><span class="font-semibold text-slate-800">Documento:</span> {{ $institution->document }}</p>
            </div>
        </div>
    </div>

    <div class="card space-y-6">
        <div class="flex flex-col md:flex-row md:gap-6 md:justify-between md:items-stretch">
            <div class="flex-1 space-y-2 flex flex-col justify-between">
                <div>
                    <h2 class="text-lg font-semibold">Compartilhamento</h2>
                    <p class="text-sm text-slate-600">Compartilhe o link abaixo com os membros da diretoria para que realizem o cadastro sem necessidade de login.</p>
                </div>
                <div class="flex items-center gap-2 mt-2">
                    <input type="text" class="form-control w-full" value="{{ $shareUrl }}" readonly>
                    <button type="button" class="btn-secondary-sm" onclick="navigator.clipboard.writeText('{{ $shareUrl }}')">Copiar link</button>
                </div>
            </div>
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
                        @php
                            $badgeClass = $item['complete']
                                ? 'bg-emerald-100 text-emerald-700'
                                : (($item['key'] ?? null) === 'documents'
                                    ? 'bg-amber-100 text-amber-700'
                                    : 'bg-rose-100 text-rose-700');
                            $badgeLabel = $item['complete']
                                ? 'Concluido'
                                : (($item['key'] ?? null) === 'documents' ? 'Não enviado' : 'Pendente');
                        @endphp
                        <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $badgeClass }}">
                            {{ $badgeLabel }}
                        </span>
                        @if(isset($item['modal']))
                            <button type="button" class="btn-secondary-sm" @click="modal = '{{ $item['modal'] }}'">{{ $item['action_label'] }}</button>
                        @elseif(!empty($item['action']))
                            <a href="{{ $item['action'] }}" class="btn-secondary-sm">{{ $item['action_label'] }}</a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>


    </div>

    @php
        $redirectParams = [
            'redirect_to' => \App\Models\Process::TYPE_BOARD_ELECTION_MINUTES_REGISTRATION,
            'process_id' => $process->id,
        ];
        $mandateStartValue = old('mandate_start', optional(optional($mandate)->start_date)->format('Y-m-d'));
        $mandateDurationValue = old('mandate_duration', optional($mandate)->duration_years);
    @endphp
    <div id="members-section" class="card space-y-4">
        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <h2 class="text-xl font-semibold text-slate-900">Membros cadastrados</h2>
            <a href="#members-section" class="btn-secondary-sm">Ver todos</a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-4 py-3 text-left">Nome</th>
                        <th class="px-4 py-3 text-left">CPF</th>
                        <th class="px-4 py-3 text-left">Email</th>
                        <th class="px-4 py-3 text-left">Telefone</th>
                        <th class="px-4 py-3 text-left">Cargo</th>
                        <th class="px-4 py-3 text-left">Acoes</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($members as $member)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 font-medium text-slate-800">{{ $member->name }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $member->cpf }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $member->email }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $member->phone }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $member->role }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <a class="btn-secondary-sm" href="{{ route('members.edit', array_merge(['member' => $member], $redirectParams)) }}">Editar</a>
                                    <form method="POST" action="{{ route('members.destroy', $member) }}" onsubmit="return confirm('Tem certeza que deseja remover este membro?');" class="inline-flex">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="redirect_to" value="{{ $redirectParams['redirect_to'] }}">
                                        <input type="hidden" name="process_id" value="{{ $redirectParams['process_id'] }}">
                                        <button type="submit" class="btn-danger-sm">Remover</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-6 text-center text-sm text-slate-500">Nenhum membro cadastrado neste processo ainda.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modais -->
    <template x-if="modal === 'mandate'">
        <div x-cloak class="fixed inset-0 z-40 flex items-center justify-center bg-black/50 px-4" @click="modal = null">
            <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl" @click.stop>
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-slate-900">Configurar mandato</h3>
                    <button type="button" class="btn-secondary-sm" @click="modal = null">Fechar</button>
                </div>
                <form method="POST" action="{{ route('processes.board_election.mandate.store', $process) }}" class="mt-4 space-y-4">
                    @csrf
                    <div class="space-y-2">
                        <label class="form-label" for="modal_mandate_start">Data de inicio do mandato</label>
                        <input id="modal_mandate_start" name="mandate_start" type="date" class="form-control" value="{{ $mandateStartValue }}" required>
                    </div>
                    <div class="space-y-2">
                        <label class="form-label" for="modal_mandate_duration">Tempo de mandato (em anos)</label>
                        <input id="modal_mandate_duration" name="mandate_duration" type="number" min="1" max="10" class="form-control" value="{{ $mandateDurationValue }}" required>
                    </div>
                    <div class="flex justify-end gap-2">
                        <button type="button" class="btn-secondary-sm" @click="modal = null">Cancelar</button>
                        <button type="submit" class="btn">Salvar mandato</button>
                    </div>
                </form>
            </div>
        </div>
    </template>

    <template x-if="modal === 'documents'">
        <div x-cloak class="fixed inset-0 z-40 flex items-center justify-center bg-black/50 px-4" @click="modal = null">
            <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl" @click.stop>
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-slate-900">Anexar documentos</h3>
                    <button type="button" class="btn-secondary-sm" @click="modal = null">Fechar</button>
                </div>
                <form method="POST" action="{{ route('processes.board_election.documents.store', $process) }}" enctype="multipart/form-data" class="mt-4 space-y-4">
                    @csrf
                    <div class="space-y-2">
                        <label class="form-label" for="documents_minutes">Ultima ata registrada</label>
                        <input id="documents_minutes" name="minutes_file" type="file" class="form-control" accept=".pdf,.doc,.docx">
                    </div>
                    <div class="space-y-2">
                        <label class="form-label" for="documents_bylaws">Estatuto vigente</label>
                        <input id="documents_bylaws" name="bylaws_file" type="file" class="form-control" accept=".pdf,.doc,.docx">
                    </div>
                    <div class="flex justify-end gap-2">
                        <button type="button" class="btn-secondary-sm" @click="modal = null">Cancelar</button>
                        <button type="submit" class="btn">Salvar documentos</button>
                    </div>
                </form>
            </div>
        </div>
    </template>
</div>
@endsection
