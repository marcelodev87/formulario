@extends('layouts.app')

@section('content')
@php
    $isOwner = auth()->id() === $institution->owner_user_id;
    $typeKeys = array_keys($typeDefinitions);
    $initialType = $typeKeys[0] ?? null;
@endphp
<div class="space-y-8">
    <div class="card space-y-4">
        <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-slate-900">Processos da instituicao</h1>
                <p class="text-sm text-slate-600">Gerencie os fluxos de trabalho disponiveis para {{ $institution->name }}.</p>
            </div>
            <div class="rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-600">
                <p><span class="font-semibold text-slate-800">Documento:</span> {{ $institution->document }}</p>
                <p><span class="font-semibold text-slate-800">Responsavel:</span> {{ $institution->owner->name ?? $institution->owner->email }}</p>
            </div>
        </div>
        <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-600">
            <p>Escolha um processo abaixo para continuar ou iniciar um novo fluxo conforme a necessidade da instituicao.</p>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <div class="card space-y-4">
                <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                    <h2 class="text-xl font-semibold text-slate-900">Processos em andamento</h2>
                    <p class="text-sm text-slate-500">Acompanhe o status e continue os cadastros.</p>
                </div>
                <div class="space-y-4">
                    @forelse($processes as $process)
                        <div class="flex flex-col gap-4 rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                            <div class="space-y-1">
                                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ $process->type_label }}</p>
                                <h3 class="text-lg font-semibold text-slate-900">{{ $process->title }}</h3>
                                @if($process->type_definition['description'] ?? false)
                                    <p class="text-sm text-slate-600">{{ $process->type_definition['description'] }}</p>
                                @endif
                            </div>
                            <div class="flex flex-wrap items-center justify-between gap-3">
                                <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">{{ $process->status_label }}</span>
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="text-xs text-slate-500">Atualizado em {{ $process->updated_at->format('d/m/Y H:i') }}</span>
                                    <a href="{{ route('processes.show', $process) }}" class="btn px-4 py-2 text-sm">
                                        {{ $process->type_definition['cta_label'] ?? 'Acessar processo' }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">Nenhum processo foi iniciado ainda.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="space-y-6">
            @if($isOwner)
                <div class="card space-y-4">
                    <h2 class="text-lg font-semibold text-slate-900">Iniciar novo processo</h2>
                    <p class="text-sm text-slate-600">Selecione o tipo de processo desejado e personalize o nome se necessario.</p>
                    <form method="POST" action="{{ route('processes.store') }}" class="space-y-4">
                        @csrf
                        <div class="space-y-2">
                            <label class="form-label" for="process-type">Tipo de processo</label>
                            <select id="process-type" name="type" class="form-control" required>
                                @foreach($typeDefinitions as $type => $definition)
                                    <option value="{{ $type }}" @selected(old('type', $initialType) === $type)>
                                        {{ $definition['label'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="form-label" for="process-title">Nome do processo (opcional)</label>
                            <input id="process-title" name="title" type="text" class="form-control" value="{{ old('title') }}" maxlength="255" placeholder="Ex.: Abertura 2025">
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

            <div class="card space-y-3">
                <h2 class="text-lg font-semibold text-slate-900">Processo de abertura</h2>
                <p class="text-sm text-slate-600">Consulte rapidamente o progresso do cadastro principal da instituicao.</p>
                <dl class="space-y-2 text-sm text-slate-600">
                    <div class="flex items-center justify-between">
                        <dt>Status atual</dt>
                        <dd class="font-semibold text-slate-900">{{ $openingProcess->status_label }}</dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt>Atualizado em</dt>
                        <dd>{{ $openingProcess->updated_at->format('d/m/Y H:i') }}</dd>
                    </div>
                </dl>
                <a href="{{ route('processes.show', $openingProcess) }}" class="btn w-full">Continuar cadastro</a>
            </div>
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
