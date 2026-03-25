@extends('internal.layouts.app')

@section('title', 'Processo #' . $process->id . ' | Painel Etika')

@section('content')
@php
    use App\Models\Process;
    use Illuminate\Support\Str;
    $typeDefinition = Process::typeDefinitions()[$process->type] ?? null;
    $statusLabels = Process::statusLabels();
    $canApprove = $process->status !== Process::STATUS_COMPLETED;
    $canReopen = $process->status === Process::STATUS_COMPLETED;
    $processLocked = $processLocked ?? false;
    $isBranchProcess = $process->type === Process::TYPE_BRANCH_OPENING;
    $isBylawsProcess = $process->type === 'bylaws_revision';

    if ($isBranchProcess) {
        $branchStatusItems = $branchStatusItems ?? [];
        $branchLocation = $branchLocation ?? null;
        $branchProperty = $branchProperty ?? null;
        $branchLeader = $branchLeader ?? null;
        $branchAddressComplete = $branchAddressComplete ?? false;
        $branchPropertyComplete = $branchPropertyComplete ?? false;
        $branchLeaderComplete = $branchLeaderComplete ?? false;
        $branchStatusIconDefault = '<svg class="h-5 w-5" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m5 10 3 3 7-7" /></svg>';
        $branchStatusIcons = [
            'location' => '<svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M10 2a5 5 0 0 0-5 5c0 3.866 5 11 5 11s5-7.134 5-11a5 5 0 0 0-5-5Zm0 7a2 2 0 1 1 0-4 2 2 0 0 1 0 4Z" /></svg>',
            'property' => '<svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M4 3a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v15h-3v-3a1 1 0 0 0-1-1H8a1 1 0 0 0-1 1v3H4V3Zm5 2H7v2h2V5Zm2 0v2h2V5h-2ZM7 9v2h2V9H7Zm4 0v2h2V9h-2ZM7 13 v2h2 v-2H7Z" /></svg>',
            'leader' => '<svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M10 4a3 3 0 1 1 0 6 3 3 0 0 1 0-6Zm-6 11a6 6 0 0 1 12 0v1a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-1Z" /></svg>',
        ];
    } else {
        $addressComplete = $headquartersLocation && collect(['street', 'number', 'district', 'city', 'uf', 'cep'])->every(fn ($field) => filled($headquartersLocation->{$field}));
        $propertyComplete = $property !== null;
        $administrationComplete = $administration !== null;
    }
    $bylawsEstatutoFile = $process->answers['estatuto_file'] ?? null;
    $bylawsEstatutoUrl = $bylawsEstatutoFile ? asset('storage/' . $bylawsEstatutoFile) : null;
    $bylawsEstatutoIsPdf = $bylawsEstatutoFile ? Str::endsWith(strtolower($bylawsEstatutoFile), '.pdf') : false;

@endphp
<div class="space-y-8">
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div class="space-y-1">
            <p class="text-xs uppercase tracking-wide text-slate-500">Processo #{{ $process->id }}</p>
            <h1 class="text-3xl font-semibold text-slate-900">{{ $typeDefinition['label'] ?? $process->title }}</h1>
            <p class="text-sm text-slate-600">{{ $typeDefinition['description'] ?? 'Sem descricao definida.' }}</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-4 text-sm">
            <p><span class="font-semibold text-slate-800">Status atual:</span> {{ $statusLabels[$process->status] ?? $process->status }}</p>
            <p><span class="font-semibold text-slate-800">Atualizado em:</span> {{ $process->updated_at?->timezone('America/Sao_Paulo')->format('d/m/Y H:i') }}</p>
        </div>
    </div>

    @if($processLocked)
        <div class="rounded-xl border border-amber-300 bg-amber-50 p-4 text-sm text-amber-700">{{ __('forms.process_approved') }}</div>
    @endif

<div class="grid gap-6 md:grid-cols-2">
        <div class="card space-y-4">
            <h2 class="text-xl font-semibold text-slate-900">{{ __('forms.institution') }}</h2>
            <dl class="grid grid-cols-1 gap-3 text-sm">
                <div>
                    <dt class="text-slate-500">Nome</dt>
                    <dd class="font-medium text-slate-800">{{ $process->institution->name ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="text-slate-500">Documento</dt>
                    <dd class="font-medium text-slate-800">{{ $process->institution->document ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="text-slate-500">UF</dt>
                    <dd class="font-medium text-slate-800 uppercase">{{ optional($headquartersLocation)->uf ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="text-slate-500">Contato</dt>
                    <dd class="font-medium text-slate-800">{{ $process->institution->email ?? '-' }}<br>{{ $process->institution->phone ?? '-' }}</dd>
                </div>
            </dl>
        </div>

        <div class="card space-y-4">
            <h2 class="text-xl font-semibold text-slate-900">{{ __('forms.actions') }}</h2>
            <p class="text-sm text-slate-600">{{ __('forms.manage_process_lifecycle') }}</p>
            <div class="space-y-4">
                @if($canApprove)
                    <form method="POST" action="{{ route('etika.processes.approve', $process) }}" class="space-y-2">
                        @csrf
                        <label class="form-label" for="approve-note">Observacao (opcional)</label>
                        <textarea id="approve-note" name="note" rows="3" class="form-control" placeholder="Resumo da aprovacao"></textarea>
                        <button type="submit" class="btn w-full">{{ __('forms.approve_info') }}</button>
                    </form>
                @endif

                @if($canReopen)
                    <form method="POST" action="{{ route('etika.processes.reopen', $process) }}" class="space-y-2">
                        @csrf
                        <label class="form-label" for="reopen-note">Solicitacao ao cliente</label>
                        <textarea id="reopen-note" name="note" rows="3" class="form-control" placeholder="Descreva o complemento necessario" required></textarea>
                        <button type="submit" class="btn-secondary w-full">Reabrir processo</button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    @if($isBylawsProcess)
        <div class="card space-y-4">
            <h2 class="text-xl font-semibold text-slate-900">Reforma de estatuto</h2>
            <div class="grid gap-4 md:grid-cols-2">
                <div class="rounded-xl border border-slate-200 bg-white p-4 text-sm text-slate-700">
                    <div class="flex items-center justify-between">
                        <p class="text-lg font-semibold text-slate-900">Novo endereco</p>
                        <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $addressComplete ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                            {{ $addressComplete ? 'Concluido' : 'Pendente' }}
                        </span>
                    </div>
                    <div class="mt-3 space-y-1">
                        @if($headquartersLocation)
                            <p>{{ optional($headquartersLocation)->street ?? '-' }}, {{ optional($headquartersLocation)->number ?? '-' }} {{ optional($headquartersLocation)->complement ? '- ' . optional($headquartersLocation)->complement : '' }}</p>
                            <p>{{ optional($headquartersLocation)->district ?? '-' }} - {{ optional($headquartersLocation)->city ?? '-' }}/{{ optional($headquartersLocation)->uf ?? '-' }}</p>
                            <p>CEP {{ optional($headquartersLocation)->cep ?? '-' }}</p>
                        @else
                            <p>Nenhum endereco informado.</p>
                        @endif
                    </div>
                    <div class="mt-3 flex flex-wrap gap-2">
                        @unless($processLocked)
                            <a href="{{ route('etika.processes.address.edit', $process) }}" class="btn-secondary-sm">Editar endereco</a>
                        @else
                            <span class="text-xs text-slate-400">acoes indisponiveis</span>
                        @endunless
                    </div>
                </div>
                <div class="rounded-xl border border-slate-200 bg-white p-4 text-sm text-slate-700">
                    <div class="flex items-center justify-between">
                        <p class="text-lg font-semibold text-slate-900">Copia do estatuto</p>
                        @if($bylawsEstatutoFile)
                            <span class="inline-flex items-center rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">Enviado</span>
                        @else
                            <span class="inline-flex items-center rounded-full bg-rose-100 px-3 py-1 text-xs font-semibold text-rose-700">Pendente</span>
                        @endif
                    </div>
                    <div class="mt-3 space-y-2">
                        @if($bylawsEstatutoFile)
                            <div class="flex flex-wrap gap-2">
                                <a href="{{ $bylawsEstatutoUrl }}" target="_blank" class="btn-secondary-sm">{{ $bylawsEstatutoIsPdf ? 'Visualizar PDF' : 'Baixar arquivo' }}</a>
                                <form method="POST" action="{{ route('processes.bylaws_revision.delete_statute', $process) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-secondary-sm bg-red-100 text-red-700 hover:bg-red-200">Excluir</button>
                                </form>
                            </div>
                        @else
                            <p>Envie o estatuto atualizado em PDF ou Word para concluir o processo.</p>
                            <a href="{{ route('processes.bylaws_revision.upload_statute', $process) }}" class="btn-secondary-sm">Enviar arquivo</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if($isBranchProcess)
    <div class="card space-y-3">
        <div class="rounded-2xl border border-slate-200 bg-white p-4 text-sm text-slate-600">
            <p class="text-xs uppercase tracking-wide text-slate-500">Contato da filial</p>
            @if($branchLeader)
                <p class="mt-1 text-sm text-slate-700">Dirigente: {{ $branchLeader->name }}</p>
                <p class="text-sm text-slate-700">Telefone: {{ $branchLeader->phone }}</p>
                <p class="text-sm text-slate-700">E-mail: {{ $branchLeader->email }}</p>
            @else
                <p class="mt-1 text-sm text-slate-700">Cadastre o dirigente para definir o responsavel pela filial.</p>
            @endif
        </div>
    </div>

    <div class="card space-y-4">
        <div class="flex flex-col gap-1 md:flex-row md:items-center md:justify-between">
            <p class="text-xs uppercase tracking-wide text-slate-500">Andamento do cadastro</p>
            <p class="text-xs text-slate-500">Acompanhe o progresso das principais etapas.</p>
        </div>
        <div class="grid gap-4 md:grid-cols-3">
            @foreach($branchStatusItems as $item)
                <div class="flex h-full flex-col justify-between rounded-xl border border-slate-100 bg-white p-4 shadow-sm">
                    <div class="space-y-2">
                        <div class="flex items-center gap-2 text-sm font-semibold text-slate-700">
                            <span class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-slate-100 text-slate-600">
                                @php
                                    $iconSvg = $branchStatusIcons[$item['key']] ?? $branchStatusIconDefault;
                                    echo $iconSvg;
                                @endphp
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
                        @unless($processLocked)
                            <a href="{{ $item['action'] }}" class="btn-secondary-sm">{{ $item['action_label'] }}</a>
                        @else
                            <span class="text-xs text-slate-400">acoes indisponiveis</span>
                        @endunless
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="card space-y-4">
        <h2 class="text-lg font-semibold text-slate-900">Resumo da filial</h2>
        <dl class="grid gap-4 md:grid-cols-3">
            <div class="rounded-xl border border-slate-200 p-4">
                <dt class="text-xs uppercase tracking-wide text-slate-500">Endereco da filial</dt>
                <dd class="mt-1 text-sm text-slate-700">
                    @if($branchLocation)
                        {{ $branchLocation->street ?? '-' }}, {{ $branchLocation->number ?? '-' }} {{ $branchLocation->complement ? '- ' . $branchLocation->complement : '' }}<br>
                        {{ $branchLocation->district ?? '-' }} - {{ $branchLocation->city ?? '-' }}/{{ $branchLocation->uf ?? '-' }}<br>
                        CEP {{ $branchLocation->cep ?? '-' }}
                    @else
                        Ainda nao informado.
                    @endif
                </dd>
            </div>
            <div class="rounded-xl border border-slate-200 p-4">
                <dt class="text-xs uppercase tracking-wide text-slate-500">Dados do imovel</dt>
                <dd class="mt-1 text-sm text-slate-700">
                    @if($branchProperty)
                        <p><span class="font-semibold">Situacao:</span> {{ $branchProperty->tenure_type === 'own' ? 'Proprio' : ($branchProperty->tenure_type === 'rented' ? 'Alugado' : '-') }}</p>
                        <p><span class="font-semibold">Area construida:</span> {{ $branchProperty->built_area_sqm ?? '-' }}{{ $branchProperty->built_area_sqm ? ' m2' : '' }}</p>
                        <p><span class="font-semibold">Capacidade:</span> {{ $branchProperty->capacity ?? '-' }}</p>
                        <p><span class="font-semibold">Uso:</span> {{ $branchProperty->property_use ?? '-' }}</p>
                    @else
                        Nenhum dado cadastrado.
                    @endif
                </dd>
            </div>
            <div class="rounded-xl border border-slate-200 p-4">
                <dt class="text-xs uppercase tracking-wide text-slate-500">Dirigente</dt>
                <dd class="mt-1 text-sm text-slate-700">
                    @if($branchLeader)
                        <p>{{ $branchLeader->name }}</p>
                        <p>CPF {{ $branchLeader->cpf }}</p>
                        <p>Telefone {{ $branchLeader->phone }}</p>
                        <p>E-mail {{ $branchLeader->email }}</p>
                    @else
                        Cadastre o dirigente para concluir a filial.
                    @endif
                </dd>
            </div>
        </dl>
    </div>
@else
        <div class="grid gap-6 md:grid-cols-2">
            <div class="card space-y-3">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h2 class="text-xl font-semibold text-slate-900">{{ __('forms.institution_address') }}</h2>
                        <p class="text-sm text-slate-600">{{ $addressComplete ? __('forms.address_registered') : __('forms.address_incomplete') }}</p>
                    </div>
                    @unless($processLocked)
                        <a href="{{ route('etika.processes.address.edit', $process) }}" class="btn-secondary-sm">{{ __('forms.edit') }}</a>
                    @endunless
                </div>
                <div class="text-sm text-slate-600">
                    @if($headquartersLocation)
                        <p>{{ $headquartersLocation->street ?? '-' }}, {{ $headquartersLocation->number ?? '-' }} {{ $headquartersLocation->complement ? '- ' . $headquartersLocation->complement : '' }}</p>
                        <p>{{ $headquartersLocation->district ?? '-' }}</p>
                        <p>{{ $headquartersLocation->city ?? '-' }} / {{ $headquartersLocation->uf ?? '-' }}</p>
                        <p>CEP {{ $headquartersLocation->cep ?? '-' }}</p>
                    @else
                        <p>{{ __('forms.address_incomplete') }}</p>
                    @endif
                </div>
            </div>

            <div class="card space-y-3">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h2 class="text-xl font-semibold text-slate-900">{{ __('forms.institution_property') }}</h2>
                        <p class="text-sm text-slate-600">{{ $propertyComplete ? __('forms.info_registered') : __('forms.property_not_registered') }}</p>
                    </div>
                    @unless($processLocked)
                        <a href="{{ route('etika.processes.property.edit', $process) }}" class="btn-secondary-sm">{{ __('forms.edit') }}</a>
                    @endunless
                </div>
                <div class="text-sm text-slate-600">
                    @if($propertyComplete)
                        <p><span class="font-semibold">IPTU:</span> {{ $property->iptu_registration ?? '-' }}</p>
                        <p><span class="font-semibold">Tipo:</span> {{ $property->property_use ?? '-' }}</p>
                        <p><span class="font-semibold">Situação:</span> {{ $property->tenure_type === 'own' ? 'Próprio' : ($property->tenure_type === 'rented' ? 'Alugado' : '-') }}</p>
                        <p><span class="font-semibold">Área construída:</span> {{ $property->built_area_sqm ? $property->built_area_sqm . ' m²' : '-' }}</p>
                        <p><span class="font-semibold">Capacidade:</span> {{ $property->capacity ?? '-' }}</p>
                    @else
                        <p>Nenhuma informacao cadastrada.</p>
                    @endif
                </div>
            </div>

            <div class="card space-y-3 md:col-span-2">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h2 class="text-xl font-semibold text-slate-900">{{ __('forms.administration') }}</h2>
                        <p class="text-sm text-slate-600">{{ $administrationComplete ? __('forms.administration_registered') : __('forms.administration_define_rules') }}</p>
                    </div>
                    @unless($processLocked)
                        <a href="{{ route('etika.processes.administration.edit', $process) }}" class="btn-secondary-sm">{{ __('forms.edit') }}</a>
                    @endunless
                </div>
                <div class="grid gap-2 text-sm text-slate-600 md:grid-cols-2">
                    @if($administrationComplete)
                        <p><span class="font-semibold">Modelo:</span> {{ $administration->governance_model ?? '-' }}</p>
                        <p><span class="font-semibold">Extinção:</span> {{ $administration->dissolution_mode ?? '-' }}</p>
                        <p><span class="font-semibold">Mandato do presidente:</span> {{ ($administration->president_term_indefinite ?? false) ? 'Indeterminado' : (($administration->president_term_years ?? '-') . ' anos') }}</p>
                        <p><span class="font-semibold">Mandato da diretoria:</span> {{ $administration->board_term_years ?? '-' }} anos</p>
                        <p class="md:col-span-2"><span class="font-semibold">Cargos:</span> {{ $administration->ministerial_roles ? implode(', ', $administration->ministerial_roles) : '-' }}</p>
                        <p class="md:col-span-2"><span class="font-semibold">Prebenda:</span> {{ $administration->stipend_policy ?? '-' }}</p>
                    @else
                        <p>Nenhuma informação administrativa cadastrada.</p>
                    @endif
                </div>
            </div>

            <div class="card space-y-4 md:col-span-2" id="members-section">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h2 class="text-xl font-semibold text-slate-900">{{ __('forms.board_members') }}</h2>
                        <p class="text-sm text-slate-600">{{ $hasMinimumMembers ? __('forms.minimum_members_met') : __('forms.minimum_members_required') }}</p>
                    </div>
                    @unless($processLocked)
                        <a href="{{ route('etika.processes.members.create', $process) }}" class="btn">Adicionar membro</a>
                    @endunless
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
                                <th class="px-4 py-3 text-left">{{ __('forms.actions') }}</th>
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
                                        @if(!$processLocked)
                                            <div class="flex items-center gap-2">
                                                <a href="{{ route('etika.processes.members.edit', [$process, $member]) }}" class="btn-secondary-sm">{{ __('forms.edit') }}</a>
                                                <form method="POST" action="{{ route('etika.processes.members.destroy', [$process, $member]) }}" onsubmit="return confirm('Confirma remover este membro?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn-danger-sm">Remover</button>
                                                </form>
                                            </div>
                                        @else
                                            <span class="text-xs text-slate-400">acoes indisponiveis</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-6 text-center text-sm text-slate-500">Nenhum membro cadastrado.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @error('member')
                    <p class="text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
    @endif

    <div class="card space-y-4">
        <h2 class="text-xl font-semibold text-slate-900">Log interno</h2>
        <ul class="space-y-3 text-sm text-slate-600">
            @forelse($activityLogs as $log)
                <li class="rounded-xl border border-slate-200 p-4">
                    <div class="flex flex-col gap-2">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <p class="font-semibold text-slate-800">{{ $log['title'] }}</p>
                            <span class="text-xs text-slate-500">{{ optional($log['timestamp'])->format('d/m/Y H:i') ?? '-' }} - {{ $log['user'] }}</span>
                        </div>
                        @if(!empty($log['changes']))
                            <ul class="space-y-1 text-slate-700">
                                @foreach($log['changes'] as $change)
                                    <li>- {{ $change }}</li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-slate-500">Sem detalhes registrados.</p>
                        @endif
                    </div>
                </li>
            @empty
                <li class="text-slate-500">Nenhuma acao registrada.</li>
            @endforelse
        </ul>
    </div>

    <div class="card space-y-4">
        <h2 class="text-xl font-semibold text-slate-900">Linha do tempo do status</h2>
        <ul class="space-y-4">
            @forelse($process->statusTimeline as $entry)
                <li class="rounded-xl border border-slate-200 p-4">
                    <div class="flex flex-col gap-1 text-sm text-slate-600">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <p class="font-semibold text-slate-800">{{ $statusLabels[$entry->from_status] ?? ($entry->from_status ?? '-') }} -> {{ $statusLabels[$entry->to_status] ?? $entry->to_status }}</p>
                            <span>{{ $entry->created_at?->timezone('America/Sao_Paulo')->format('d/m/Y H:i') }}</span>
                        </div>
                        <p>Por {{ $entry->actor?->name ?? 'sistema' }}</p>
                        @if($entry->note)
                            <p class="text-slate-700">{{ $entry->note }}</p>
                        @endif
                    </div>
                </li>
            @empty
                <li class="text-sm text-slate-500">Ainda nao ha historico registrado.</li>
            @endforelse
        </ul>
    </div>
</div>
@endsection
