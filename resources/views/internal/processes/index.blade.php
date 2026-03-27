@extends('internal.layouts.app')

@section('title', 'Processos | Painel Etika')

@section('content')
@php
    $typeLabels = collect($typeOptions)->map(fn ($definition, $slug) => $definition['label'] ?? $slug);
@endphp
<div class="space-y-8">
    <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
        <div>
            <h1 class="text-3xl font-semibold text-slate-900">Processos</h1>
            <p class="text-sm text-slate-600">Painel da equipe para acompanhar e revisar todos os processos.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('etika.users.index') }}" class="btn-secondary">Gestão de usuários</a>
        </div>
    </div>

    <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
        <div></div>
        <div class="flex flex-wrap items-stretch gap-3">
            <div class="rounded-2xl border border-slate-200 bg-white p-4 text-sm flex-1 min-w-[180px]">
                <p class="text-xs uppercase tracking-wide text-slate-500">Total no mes</p>
                <p class="mt-1 text-2xl font-semibold text-slate-900">{{ $indicators['total'] }}</p>
            </div>
            @foreach($indicators['items'] as $indicator)
                <div class="rounded-2xl border border-slate-200 bg-white p-4 text-sm flex-1 min-w-[180px]">
                    <p class="text-xs uppercase tracking-wide text-slate-500">{{ $indicator['label'] }}</p>
                    <p class="mt-1 text-lg font-semibold text-slate-900">{{ $indicator['total'] }}</p>
                </div>
            @endforeach
        </div>
    </div>

    <div class="card space-y-6">
        <form method="GET" action="{{ route('etika.processes.index') }}" class="grid gap-4 md:grid-cols-5 md:items-end">
            <div class="md:col-span-2 space-y-1">
                <label class="form-label" for="q">Busca</label>
                <input type="search" id="q" name="q" value="{{ $filters['q'] }}" placeholder="Instituicao, CPF/CNPJ, processo" class="form-control">
            </div>
            <div class="space-y-1">
                <label class="form-label" for="status">Status</label>
                <select id="status" name="status" class="form-control">
                    <option value="">Todos</option>
                    @foreach($statusOptions as $value => $label)
                        <option value="{{ $value }}" @selected($filters['status'] === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="space-y-1">
                <label class="form-label" for="uf">UF</label>
                <input type="text" id="uf" name="uf" value="{{ $filters['uf'] }}" maxlength="2" class="form-control uppercase">
            </div>
            <div class="space-y-1">
                <label class="form-label" for="type">Tipo</label>
                <select id="type" name="type" class="form-control">
                    <option value="">Todos</option>
                    @foreach($typeLabels as $slug => $label)
                        <option value="{{ $slug }}" @selected($filters['type'] === $slug)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="space-y-1">
                <label class="form-label" for="per_page">Itens por pagina</label>
                <select id="per_page" name="per_page" class="form-control">
                    @foreach($perPageOptions as $option)
                        <option value="{{ $option }}" @selected($filters['per_page'] === $option)>{{ $option }}</option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-5 flex flex-wrap items-center gap-3">
                <button type="submit" class="btn">Filtrar</button>
                <a href="{{ route('etika.processes.index') }}" class="btn-secondary">Limpar</a>
            </div>
        </form>
    </div>

    <div class="card">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-4 py-3 text-left">Instituicao</th>
                        <th class="px-4 py-3 text-left">Documento</th>
                        <th class="px-4 py-3 text-left">Tipo</th>
                        <th class="px-4 py-3 text-left">UF</th>
                        <th class="px-4 py-3 text-left">Inicio</th>
                        <th class="px-4 py-3 text-left">Status</th>
                        <th class="px-4 py-3 text-left">Acoes</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($processes as $process)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 font-medium text-slate-800">{{ $process->institution->name ?? '-' }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $process->institution->document ?? '-' }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $typeLabels[$process->type] ?? $process->type }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ optional($process->institution->headquartersLocation)->uf ?? '-' }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $process->created_at?->timezone('America/Sao_Paulo')->format('d/m/Y') }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700">{{ $process->status_label }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <a href="{{ route('etika.processes.show', $process) }}" class="inline-flex items-center rounded-lg px-3 py-1 text-sm font-semibold transition" style="background:#ffffff; color:#660000; border:1px solid #660000;" onmouseover="this.style.background='#660000'; this.style.color='#ffffff';" onmouseout="this.style.background='#ffffff'; this.style.color='#660000';">Visualizar</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-6 text-center text-sm text-slate-500">Nenhum processo encontrado.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-slate-200 px-4 py-3">
            {{ $processes->links() }}
        </div>
    </div>
</div>
@endsection
