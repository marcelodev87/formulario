@extends('layouts.app')

@section('content')
@php
    $redirectParams = $redirectParams ?? [];
    $returnUrl = $returnUrl ?? route('dashboard');
@endphp
<div class="card space-y-8">
    <div class="space-y-2">
        <h1 class="text-2xl font-semibold text-slate-900">{{ __('forms.administration_title') }}</h1>
        <p class="text-sm text-slate-600">{{ __('forms.administration_description') }}</p>
    </div>

    <form method="POST" action="{{ route('administration.store') }}" class="space-y-8">
        @csrf
        @foreach($redirectParams as $paramKey => $paramValue)
            <input type="hidden" name="{{ $paramKey }}" value="{{ $paramValue }}">
        @endforeach

        <section class="space-y-4">
            <p class="text-sm text-slate-600">Precisamos de 3 opções de nome, em ordem de preferência, para regularização.</p>
            <div class="grid gap-4 md:grid-cols-1">
                <div class="space-y-1">
                    <label class="form-label" for="name_option_1">Primeira opção de nome</label>
                    <input id="name_option_1" name="name_option_1" type="text" class="form-control" value="{{ old('name_option_1', $administration->name_option_1 ?? '') }}">
                </div>
                <div class="space-y-1">
                    <label class="form-label" for="name_option_2">Segunda opção de nome</label>
                    <input id="name_option_2" name="name_option_2" type="text" class="form-control" value="{{ old('name_option_2', $administration->name_option_2 ?? '') }}">
                </div>
                <div class="space-y-1">
                    <label class="form-label" for="name_option_3">Terceira opção de nome</label>
                    <input id="name_option_3" name="name_option_3" type="text" class="form-control" value="{{ old('name_option_3', $administration->name_option_3 ?? '') }}">
                </div>
            </div>
        </section>

        <section class="space-y-4">
            <h2 class="text-lg font-semibold">{{ __('forms.government_structure') }}</h2>
            <div class="grid gap-4">
                <div class="space-y-1">
                    <span class="form-label">{{ __('forms.organizacao_extinta') }}</span>
                    <label class="flex items-center gap-2 text-sm">
                        <input type="radio" name="dissolution_mode" value="president" {{ old('dissolution_mode', $administration->dissolution_mode ?? '') === 'president' ? 'checked' : '' }} required>
                        {{ __('forms.por_decisao_presidente') }}
                    </label>
                    <label class="flex items-center gap-2 text-sm">
                        <input type="radio" name="dissolution_mode" value="members" {{ old('dissolution_mode', $administration->dissolution_mode ?? '') === 'members' ? 'checked' : '' }}>
                        {{ __('forms.por_decisao_membros') }}
                    </label>
                </div>

                <div class="space-y-1">
                    <span class="form-label">{{ __('forms.regime_governo') }}</span>
                    <label class="flex items-center gap-2 text-sm">
                        <input type="radio" name="governance_model" value="episcopal" {{ old('governance_model', $administration->governance_model ?? '') === 'episcopal' ? 'checked' : '' }} required>
                        {{ __('forms.episcopal') }}
                    </label>
                    <label class="flex items-center gap-2 text-sm">
                        <input type="radio" name="governance_model" value="presbiterial" {{ old('governance_model', $administration->governance_model ?? '') === 'presbiterial' ? 'checked' : '' }}>
                        {{ __('forms.presbiterial') }}
                    </label>
                    <label class="flex items-center gap-2 text-sm">
                        <input type="radio" name="governance_model" value="congregacional" {{ old('governance_model', $administration->governance_model ?? '') === 'congregacional' ? 'checked' : '' }}>
                        {{ __('forms.congregacional') }}
                    </label>
                </div>

                <div class="grid gap-3 md:grid-cols-2">
                    <div class="space-y-2">
                        <span class="form-label">{{ __('forms.president_term') }}</span>
                        <label class="flex items-center gap-2 text-sm">
                            <input type="radio" name="president_term_type" value="indefinite" {{ old('president_term_type', ($administration->president_term_indefinite ?? false) ? 'indefinite' : 'years') === 'indefinite' ? 'checked' : '' }}>
                            {{ __('forms.indeterminado') }}
                        </label>
                        <label class="flex items-center gap-2 text-sm">
                            <input type="radio" name="president_term_type" value="years" {{ old('president_term_type', ($administration->president_term_indefinite ?? false) ? 'indefinite' : 'years') === 'years' ? 'checked' : '' }}>
                            {{ __('forms.determinado') }}
                        </label>
                        <input type="number" name="president_term_years" class="form-control" min="1" max="50" placeholder="{{ __('forms.years') }}" value="{{ old('president_term_years', $administration->president_term_years ?? '') }}">
                    </div>
                    <div class="space-y-2">
                        <label class="form-label" for="board_term_years">{{ __('forms.board_term') }}</label>
                        <input id="board_term_years" name="board_term_years" type="number" min="1" max="50" class="form-control" value="{{ old('board_term_years', $administration->board_term_years ?? '') }}" required>
                    </div>
                </div>
            </div>
        </section>

        <section class="space-y-4">
            <h2 class="text-lg font-semibold">{{ __('forms.ministerial_decisions') }}</h2>
            <div class="grid gap-4">
                <div class="space-y-1">
                    <span class="form-label">{{ __('forms.ordination_decision') }}</span>
                    <label class="flex items-center gap-2 text-sm">
                        <input type="radio" name="ordination_decision" value="president" {{ old('ordination_decision', $administration->ordination_decision ?? '') === 'president' ? 'checked' : '' }} required>
                        {{ __('forms.por_decisao_presidente') }}
                    </label>
                    <label class="flex items-center gap-2 text-sm">
                        <input type="radio" name="ordination_decision" value="leadership" {{ old('ordination_decision', $administration->ordination_decision ?? '') === 'leadership' ? 'checked' : '' }}>
                        {{ __('forms.por_decisao_lideranca') }}
                    </label>
                    <label class="flex items-center gap-2 text-sm">
                        <input type="radio" name="ordination_decision" value="members" {{ old('ordination_decision', $administration->ordination_decision ?? '') === 'members' ? 'checked' : '' }}>
                        {{ __('forms.por_decisao_membros') }}
                    </label>
                </div>

                <div class="space-y-1">
                    <span class="form-label">{{ __('forms.movement_financial') }}</span>
                    <label class="flex items-center gap-2 text-sm">
                        <input type="radio" name="financial_responsible" value="president" {{ old('financial_responsible', $administration->financial_responsible ?? '') === 'president' ? 'checked' : '' }} required>
                        {{ __('forms.president') }}
                    </label>
                    <label class="flex items-center gap-2 text-sm">
                        <input type="radio" name="financial_responsible" value="president_treasurer" {{ old('financial_responsible', $administration->financial_responsible ?? '') === 'president_treasurer' ? 'checked' : '' }}>
                        {{ __('forms.president_treasurer') }}
                    </label>
                </div>

                <div class="space-y-2">
                    <span class="form-label">{{ __('forms.ministerial_roles_title') }}</span>
                    <div class="grid gap-2 md:grid-cols-3">
                        @php
                            $roles = collect(old('ministerial_roles', $administration->ministerial_roles ?? []))->map(fn($v) => strtolower($v))->toArray();
                            $options = [
                                'apostolo' => 'Apóstolo',
                                'bispo' => 'Bispo',
                                'diacono' => 'Diácono',
                                'dirigente' => 'Dirigente',
                                'evangelista' => 'Evangelista',
                                'missionario' => 'Missionário',
                                'obreiro' => 'Obreiro',
                                'pastor' => 'Pastor',
                                'presbitero' => 'Presbítero',
                            ];
                        @endphp
                        @foreach($options as $value => $label)
                            <label class="flex items-center gap-2 text-sm">
                                <input type="checkbox" name="ministerial_roles[]" value="{{ $value }}" {{ in_array($value, $roles, true) ? 'checked' : '' }}>
                                {{ $label }}
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="space-y-1">
                    <span class="form-label">{{ __('forms.payment_policy') }}</span>
                    <label class="flex items-center gap-2 text-sm">
                        <input type="radio" name="stipend_policy" value="all_pastors" {{ old('stipend_policy', $administration->stipend_policy ?? '') === 'all_pastors' ? 'checked' : '' }} required>
                        {{ __('forms.yes_all') }}
                    </label>
                    <label class="flex items-center gap-2 text-sm">
                        <input type="radio" name="stipend_policy" value="only_president" {{ old('stipend_policy', $administration->stipend_policy ?? '') === 'only_president' ? 'checked' : '' }}>
                        {{ __('forms.yes_president') }}
                    </label>
                    <label class="flex items-center gap-2 text-sm">
                        <input type="radio" name="stipend_policy" value="none" {{ old('stipend_policy', $administration->stipend_policy ?? '') === 'none' ? 'checked' : '' }}>
                        {{ __('forms.no') }}
                    </label>
                </div>
            </div>
        </section>

        <section class="space-y-4">
            <h2 class="text-lg font-semibold">Informações adicionais</h2>
            <div class="space-y-1">
                <label class="form-label" for="additional_info">Fale sobre a sua organização, mencionando detalhes importantes sobre a administração, que não foram perguntados neste formulário:</label>
                <textarea id="additional_info" name="additional_info" class="form-control" rows="5" placeholder="Descreva aqui...">{{ old('additional_info', $administration->additional_info ?? '') }}</textarea>
            </div>
        </section>

        <div class="flex flex-wrap justify-end gap-3">
            <a href="{{ $returnUrl }}" class="btn-secondary">{{ __('forms.back_to_home') }}</a>
            <button type="submit" class="btn">{{ __('forms.save_data') }}</button>
        </div>
    </form>
</div>
@endsection
