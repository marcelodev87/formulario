@extends('layouts.app')

@section('content')
@php
    $membersCount = $members->count();
    $redirectParams = [
        'redirect_to' => \App\Models\Process::TYPE_BOARD_ELECTION_MINUTES_REGISTRATION,
        'process_id' => $process->id,
    ];
@endphp
<div class="space-y-8">
    <div class="card space-y-3">
        <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="text-xs uppercase tracking-wide text-slate-500">PROCESSO</p>
                <h1 class="text-2xl font-semibold text-slate-900">Registro de Ata de Eleicao da Diretoria</h1>
                <p class="text-sm text-slate-600">Gerencie os membros vinculados a este processo.</p>
            </div>
            <div class="rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-600">
                <p><span class="font-semibold text-slate-800">Instituicao:</span> {{ $institution->name }}</p>
                <p><span class="font-semibold text-slate-800">Documento:</span> {{ $institution->document }}</p>
            </div>
        </div>
        <div class="flex items-center gap-2 text-sm">
            <a href="{{ route('processes.board_election.dashboard', $process) }}" class="btn-secondary-sm">Voltar ao dashboard</a>
            <span class="text-slate-400">ou</span>
            <span class="text-slate-600">compartilhe o link publico abaixo.</span>
        </div>
    </div>

    <div class="card space-y-4">
        <div>
            <h2 class="text-lg font-semibold text-slate-900">Link de cadastro para a diretoria</h2>
            <p class="text-sm text-slate-600">Envie aos membros para que preencham os dados sem necessidade de login.</p>
        </div>
        <div class="flex flex-col gap-2 md:flex-row md:items-center">
            <input type="text" class="form-control md:flex-1" value="{{ $shareUrl }}" readonly>
            <button type="button" class="btn-secondary-sm" onclick="navigator.clipboard.writeText('{{ $shareUrl }}')">Copiar link</button>
        </div>
    </div>

    <div class="card space-y-3">
        @if(!$hasRequiredBoard)
            <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                Ainda faltam os cargos: {{ implode(', ', $missingRoles) }}.
            </div>
        @else
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                Cargos essenciais preenchidos. Diretoria pronta para registrar a ata.
            </div>
        @endif
        <p class="text-xs uppercase tracking-wide text-slate-500">Cargos monitorados</p>
        <p class="text-sm text-slate-600">{{ implode(' | ', $requiredRoles) }}</p>
    </div>

    <div class="card space-y-4">
        <div class="flex flex-col gap-1 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="text-xs uppercase tracking-wide text-slate-500">Diretoria cadastrada</p>
                <h2 class="text-lg font-semibold text-slate-900">Membros cadastrados: {{ $membersCount }}</h2>
            </div>
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
                            <td colspan="6" class="px-4 py-6 text-center text-sm text-slate-500">Ainda nao ha membros cadastrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection







