@extends('layouts.app')

@section('content')
@php
    $isOwner = auth()->id() === $institution->owner_user_id;
    $typeKeys = array_keys($typeDefinitions);
    $initialType = $typeKeys[0] ?? null;
@endphp



<div class="w-full max-w-5xl mx-auto space-y-6">
    <!-- Cabeçalho com nome da instituição, documento e responsável -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between w-full mb-2">
        <h1 class="text-2xl font-semibold text-slate-900">{{ $institution->name }}</h1>
        <div class="flex flex-row gap-6 mt-2 md:mt-0 text-sm text-slate-600">
            <span><span class="font-semibold text-slate-800">Documento:</span> {{ $institution->document }}</span>
            <span><span class="font-semibold text-slate-800">Responsável:</span> {{ $institution->owner->name ?? $institution->owner->email }}</span>
        </div>
    </div>

    <!-- Nova DIV: Iniciar novo processo -->
    @if($isOwner)
    <div class="card w-full mb-4">
        <h2 class="text-lg font-semibold text-slate-900 mb-1">Iniciar novo processo</h2>
        <p class="text-sm text-slate-600 mb-2">Selecione o tipo de processo desejado e personalize o nome se necessário.</p>
        <form method="POST" action="{{ route('processes.store') }}" class="space-y-4">
            @csrf
            <div class="flex flex-col md:flex-row md:gap-4">
                <div class="flex-1 space-y-2">
                    <label class="form-label" for="process-type">Tipo de processo</label>
                    <select id="process-type" name="type" class="form-control" required>
                        @foreach($typeDefinitions as $type => $definition)
                            <option value="{{ $type }}" @selected(old('type', $initialType) === $type)>
                                {{ $definition['label'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex-1 space-y-2 mt-4 md:mt-0">
                    <label class="form-label" for="process-title">Nome do processo (opcional)</label>
                    <input id="process-title" name="title" type="text" class="form-control" value="{{ old('title') }}" maxlength="255" placeholder="Ex.: Abertura 2025">
                </div>
            </div>
            <div class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-600" id="process-description">
                @if($initialType && isset($typeDefinitions[$initialType]['description']))
                    {{ $typeDefinitions[$initialType]['description'] }}
                @else
                    Selecione um tipo para ver os detalhes do fluxo.
                @endif
            </div>
            <button type="submit" class="btn w-full">Criar processo</button>
        </form>
    </div>
    @endif

    <!-- Nova DIV: Lista de processos -->
    <div class="card w-full">
        <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between mb-2">
            <h2 class="text-xl font-semibold text-slate-900">Processos em andamento</h2>
            <p class="text-sm text-slate-500">Acompanhe o status e continue os cadastros.</p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-slate-600 uppercase">Tipo</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-slate-600 uppercase">Data de início</th>
                        <th class="px-4 py-2 text-center text-xs font-semibold text-slate-600 uppercase">Status</th>
                        <th class="px-4 py-2 text-center text-xs font-semibold text-slate-600 uppercase">Ações</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-100">
                    @forelse($processes as $process)
                        <tr>
                            <td class="px-4 py-2 text-sm text-slate-800">{{ $process->type_label }}</td>
                            <td class="px-4 py-2 text-sm text-slate-700">{{ $process->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-2 text-center align-middle align-middle" style="vertical-align: middle;">
                                @php
                                    $statusColor = match($process->status) {
                                        'finalizado' => 'bg-green-500 text-white',
                                        'em_andamento' => 'bg-yellow-400 text-yellow-900',
                                        default => 'bg-slate-100 text-slate-700',
                                    };
                                    $statusText = $process->status_label;
                                @endphp
                                <span class="inline-flex items-center justify-center rounded-full px-3 py-1 font-normal text-sm {{ $statusColor }} w-32 text-center">
                                    {{ $statusText }}
                                </span>
                            </td>
                            <td class="px-4 py-2 text-center align-middle align-middle" style="vertical-align: middle;">
                                @php $btnColor = 'bg-red-900 hover:bg-red-800 text-white'; @endphp
                                @if($process->type === 'bylaws_revision')
                                    <a href="{{ route('processes.bylaws_revision.dashboard', $process) }}" class="btn btn-sm {{ $btnColor }} w-40 flex items-center justify-center text-sm font-normal">Visualizar processo</a>
                                @elseif($process->type === 'institution_opening')
                                    <a href="{{ route('processes.opening.show', $process) }}" class="btn btn-sm {{ $btnColor }} w-40 flex items-center justify-center text-sm font-normal">Visualizar processo</a>
                                @elseif($process->type === 'branch_opening')
                                    <a href="{{ route('processes.branch.show', $process) }}" class="btn btn-sm {{ $btnColor }} w-40 flex items-center justify-center text-sm font-normal">Visualizar processo</a>
                                @elseif($process->type === 'board_election')
                                    <a href="{{ route('processes.board_election.dashboard', $process) }}" class="btn btn-sm {{ $btnColor }} w-40 flex items-center justify-center text-sm font-normal">Visualizar processo</a>
                                @else
                                    <a href="{{ route('processes.show', $process) }}" class="btn btn-sm {{ $btnColor }} w-40 flex items-center justify-center text-sm font-normal">Visualizar processo</a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-4 text-center text-sm text-slate-500">Nenhum processo foi iniciado ainda.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var typeSelect = document.getElementById('process-type');
            var descriptionBox = document.getElementById('process-description');
            var definitions = @json($typeDefinitions);

            if (!typeSelect || !descriptionBox) {
                return;
            }

            var updateDescription = function () {
                var selected = typeSelect.value;
                if (definitions[selected] && definitions[selected].description) {
                    descriptionBox.textContent = definitions[selected].description;
                } else {
                    descriptionBox.textContent = 'Selecione um tipo para ver os detalhes do fluxo.';
                }
            };

            typeSelect.addEventListener('change', updateDescription);
            updateDescription();
        });
    </script>
@endpush
