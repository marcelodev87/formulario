@extends('layouts.app')

@section('content')
@php
    $isOwner = auth()->user()->institution->owner_user_id === auth()->id();
    $hasAdministration = $institution->relationLoaded('administration')
        ? $institution->administration !== null
        : $institution->administration()->exists();
    $totalMembers = $members->count();
    $addressComplete = collect(['street', 'number', 'district', 'city', 'uf', 'cep'])
        ->every(fn ($field) => filled($institution->{$field}));
    $property = $institution->property;
    $hasProperty = $property !== null;
    $statusItems = [
        [
            'key' => 'members',
            'icon' => 'users',
            'title' => 'Membros da diretoria',
            'meta' => 'Cadastrados: ' . $totalMembers,
            'description' => $hasMinimumMembers
                ? 'Requisitos minimos atendidos.'
                : 'Cadastre pelo menos um membro alem do presidente.',
            'complete' => $hasMinimumMembers,
            'action' => $isOwner ? '#members-section' : null,
            'action_label' => $hasMinimumMembers ? 'Ver membros' : 'Cadastrar membros',
            'action_class' => $hasMinimumMembers ? 'btn-secondary px-4 py-2 text-sm' : 'btn px-4 py-2 text-sm',
        ],
        [
            'key' => 'address',
            'icon' => 'pin',
            'title' => 'Endereco institucional',
            'meta' => $addressComplete ? 'Endereco cadastrado.' : 'Endereco incompleto.',
            'description' => 'Manter o endereco correto agiliza emissoes de documentos.',
            'complete' => $addressComplete,
            'action' => $isOwner ? route('institution.address.edit') : null,
            'action_label' => $addressComplete ? 'Revisar endereco' : 'Cadastrar endereco',
            'action_class' => $addressComplete ? 'btn-secondary px-4 py-2 text-sm' : 'btn px-4 py-2 text-sm',
        ],
        [
            'key' => 'property',
            'icon' => 'building',
            'title' => 'Dados do imovel',
            'meta' => $hasProperty ? 'Informa????es Cadastradas.' : 'Dados do imovel pendentes.',
            'description' => 'Esses dados sao usados em contratos, licencas e laudos.',
            'complete' => $hasProperty,
            'action' => $isOwner ? route('institution.property.edit') : null,
            'action_label' => $hasProperty ? 'Revisar imovel' : 'Cadastrar imovel',
            'action_class' => $hasProperty ? 'btn-secondary px-4 py-2 text-sm' : 'btn px-4 py-2 text-sm',
        ],
        [
            'key' => 'administration',
            'icon' => 'folder',
            'title' => 'Dados administrativos',
            'meta' => $hasAdministration ? 'Informa????es definidas.' : 'Dados administrativos pendentes.',
            'description' => 'Defina regras de governo e responsabilidades da diretoria.',
            'complete' => $hasAdministration,
            'action' => $isOwner ? route('administration.edit') : null,
            'action_label' => $hasAdministration ? 'Revisar dados' : 'Cadastrar dados',
            'action_class' => $hasAdministration ? 'btn-secondary px-4 py-2 text-sm' : 'btn px-4 py-2 text-sm',
        ],
    ];
@endphp
<div class="space-y-8">
    <div class="card space-y-6">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-slate-900">Dashboard</h1>
                <p class="text-sm text-slate-600">Gestao da instituicao e membros vinculados.</p>
            </div>
            <div class="space-y-1 text-sm text-slate-500 md:text-right">
                <p><span class="font-semibold text-slate-700">Instituicao:</span> {{ $institution->name }}</p>
                <p><span class="font-semibold text-slate-700">Documento:</span> {{ $institution->document }}</p>
            </div>
        </div>
        <div class="rounded-2xl border border-slate-200 p-4">
            <p class="text-xs uppercase tracking-wide text-slate-500">Contato</p>
            <p class="mt-1 text-sm text-slate-700">Telefone: {{ $institution->phone }}</p>
            <p class="text-sm text-slate-700">E-mail: {{ $institution->email }}</p>
        </div>
        <div class="rounded-2xl border border-slate-200 p-4 space-y-4">
            <div class="flex flex-col gap-1 md:flex-row md:items-center md:justify-between">
                <p class="text-xs uppercase tracking-wide text-slate-500">Andamento dos cadastros</p>
                <p class="text-xs text-slate-500">Acompanhe o progresso das principais etapas.</p>
            </div>
            <div class="grid gap-4 md:grid-cols-2">
                @foreach($statusItems as $item)
                    <div class="flex h-full flex-col justify-between rounded-xl border border-slate-100 bg-white p-4 shadow-sm">
                        <div class="flex items-start gap-3">
                            @php
                                $statusIndicatorClass = $item['complete']
                                    ? 'status-indicator status-indicator--complete'
                                    : 'status-indicator status-indicator--pending';
                                $iconIndicatorClass = $item['complete']
                                    ? 'status-indicator__icon status-indicator__icon--complete'
                                    : 'status-indicator__icon status-indicator__icon--pending';
                            @endphp
                            <span class="{{ $iconIndicatorClass }}">
                                @if($item['icon'] === 'users')
                                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path d="M13 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm-9 8a4 4 0 0 1 4-4h4a4 4 0 0 1 4 4v1a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-1Z" />
                                    </svg>
                                @elseif($item['icon'] === 'pin')
                                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path d="M10 2a5 5 0 0 0-5 5c0 3.866 5 11 5 11s5-7.134 5-11a5 5 0 0 0-5-5Zm0 7a2 2 0 1 1 0-4 2 2 0 0 1 0 4Z" />
                                    </svg>
                                @elseif($item['icon'] === 'building')
                                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path d="M4 3a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v15h-3v-3a1 1 0 0 0-1-1H8a1 1 0 0 0-1 1v3H4V3Zm5 2H7v2h2V5Zm2 0v2h2V5h-2ZM7 9v2h2V9H7Zm4 0v2h2V9h-2ZM7 13v2h2v-2H7Z" />
                                    </svg>
                                @elseif($item['icon'] === 'folder')
                                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path d="M2 5a2 2 0 0 1 2-2h3.172a2 2 0 0 1 1.414.586L9.828 5H16a2 2 0 0 1 2 2v7a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5Z" />
                                    </svg>
                                @endif
                            </span>
                            <div class="space-y-1">
                                <p class="text-sm font-semibold text-slate-800">{{ $item['title'] }}</p>
                                <p class="text-xs text-slate-500">{{ $item['meta'] }}</p>
                                <p class="text-xs text-slate-500">{{ $item['description'] }}</p>
                            </div>
                        </div>
                        <div class="mt-4 flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                            <span class="{{ $statusIndicatorClass }}">
                                @if($item['complete'])
                                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m5 10 3 3 7-7" />
                                    </svg>
                                    Concluido
                                @else
                                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 4v6m0 4h.01M10 18a8 8 0 1 1 0-16 8 8 0 0 1 0 16Z" />
                                    </svg>
                                    Pendente
                                @endif
                            </span>
                            @if($isOwner && $item['action'])
                                <a href="{{ $item['action'] }}" class="{{ $item['action_class'] }}">
                                    {{ $item['action_label'] }}
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>




    <div class="card space-y-4">
        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <h2 class="text-xl font-semibold text-slate-900">Convite para novos membros</h2>
            <a href="{{ $inviteUrl }}" target="_blank" class="link text-sm font-semibold">Abrir formulario publico</a>
        </div>
        <div class="flex flex-col gap-3 md:flex-row md:items-center">
            <input type="text" id="invite-link" value="{{ $inviteUrl }}" readonly class="form-control md:flex-1">
            <button type="button" class="btn" id="copy-invite">Copiar link</button>
        </div>
        <p class="text-xs text-slate-500">O link permanece ativo ate ser revogado manualmente.</p>
    </div>

    @if(!$hasMinimumMembers)
        <div class="alert alert-warning">
            E necessario cadastrar pelo menos um membro alem do presidente para concluir o processo de abertura.
        </div>
    @endif

    <div id="members-section" class="card space-y-4">
        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <h2 class="text-xl font-semibold text-slate-900">Membros cadastrados</h2>
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
                        <th class="px-4 py-3 text-left">A????es</th>
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
                                    <a class="btn-secondary-sm" href="{{ route('members.edit', $member) }}">
                                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                            <path d="M17.414 2.586a2 2 0 0 0-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 0 0 0-2.828Z" />
                                            <path d="M5 6a1 1 0 0 1 1-1h3a1 1 0 1 1 0 2H7v8h8v-2a1 1 0 1 1 2 0v2a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6Z" />
                                        </svg>
                                        Editar
                                    </a>
                                    <form method="POST" action="{{ route('members.destroy', $member) }}" onsubmit="return confirm('Tem certeza que deseja remover este membro?')" class="inline-flex">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-danger-sm">
                                            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                <path fill-rule="evenodd" d="M8 4a2 2 0 0 1 4 0h4a1 1 0 1 1 0 2h-.341l-.764 9.175A2 2 0 0 1 12.904 17H7.096a2 2 0 0 1-1.991-1.825L4.341 6H4a1 1 0 0 1 0-2h4Zm2 3a1 1 0 0 0-1 1v6a1 1 0 1 0 2 0V8a1 1 0 0 0-1-1Z" clip-rule="evenodd" />
                                            </svg>
                                            Remover
                                        </button>
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

    <div class="card space-y-4">
        <h2 class="text-xl font-semibold text-slate-900">Atividades recentes</h2>
        <ul class="space-y-3 text-sm text-slate-600">
            @forelse($recentActivity as $log)
                <li class="rounded-xl border border-slate-200 px-4 py-3">
                    <p class="font-semibold text-slate-800">{{ $log->created_at->format('d/m/Y H:i') }}</p>
                    <p>
                        {{ class_basename($log->entity_type) }} {{ $log->action }}
                        @if($log->actor)
                            por {{ $log->actor->name ?? $log->actor->email }}
                        @else
                            por link publico
                        @endif
                    </p>
                </li>
            @empty
                <li class="text-slate-500">Nenhuma atividade registrada ainda.</li>
            @endforelse
        </ul>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var button = document.getElementById('copy-invite');
        if (!button) {
            return;
        }

        var input = document.getElementById('invite-link');
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












