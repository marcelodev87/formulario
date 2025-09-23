@extends('layouts.app')

@section('content')
<div class="card space-y-8">
    <div class="space-y-2">
        <h1 class="text-2xl font-semibold text-slate-900">Dados administrativos</h1>
        <p class="text-sm text-slate-600">Defina as regras administrativas da institui????o.</p>
    </div>

    <form method="POST" action="{{ route('administration.store') }}" class="space-y-8">
        @csrf
        <section class="space-y-4">
            <h2 class="text-lg font-semibold">Estrutura de governo</h2>
            <div class="grid gap-4">
                <div class="space-y-1">
                    <span class="form-label">A organiza????o poder?? ser extinta</span>
                    <label class="flex items-center gap-2 text-sm">
                        <input type="radio" name="dissolution_mode" value="president" {{ old('dissolution_mode', $administration->dissolution_mode ?? '') === 'president' ? 'checked' : '' }} required>
                        Por decis??o do Presidente
                    </label>
                    <label class="flex items-center gap-2 text-sm">
                        <input type="radio" name="dissolution_mode" value="members" {{ old('dissolution_mode', $administration->dissolution_mode ?? '') === 'members' ? 'checked' : '' }}>
                        Por decis??o dos Membros
                    </label>
                </div>

                <div class="space-y-1">
                    <span class="form-label">Regime de governo</span>
                    <label class="flex items-center gap-2 text-sm">
                        <input type="radio" name="governance_model" value="episcopal" {{ old('governance_model', $administration->governance_model ?? '') === 'episcopal' ? 'checked' : '' }} required>
                        Episcopal: decis??es centralizadas no Pastor
                    </label>
                    <label class="flex items-center gap-2 text-sm">
                        <input type="radio" name="governance_model" value="presbiterial" {{ old('governance_model', $administration->governance_model ?? '') === 'presbiterial' ? 'checked' : '' }}>
                        Presbiterial: decis??es tomadas por um grupo de l??deres
                    </label>
                    <label class="flex items-center gap-2 text-sm">
                        <input type="radio" name="governance_model" value="congregacional" {{ old('governance_model', $administration->governance_model ?? '') === 'congregacional' ? 'checked' : '' }}>
                        Congregacional: decis??es tomadas por todos os membros
                    </label>
                </div>

                <div class="grid gap-3 md:grid-cols-2">
                    <div class="space-y-2">
                        <span class="form-label">Tempo de mandato do Presidente</span>
                        <label class="flex items-center gap-2 text-sm">
                            <input type="radio" name="president_term_type" value="indefinite" {{ old('president_term_type', ($administration->president_term_indefinite ?? false) ? 'indefinite' : 'years') === 'indefinite' ? 'checked' : '' }}>
                            Indeterminado
                        </label>
                        <label class="flex items-center gap-2 text-sm">
                            <input type="radio" name="president_term_type" value="years" {{ old('president_term_type', ($administration->president_term_indefinite ?? false) ? 'indefinite' : 'years') === 'years' ? 'checked' : '' }}>
                            Determinado
                        </label>
                        <input type="number" name="president_term_years" class="form-control" min="1" max="50" placeholder="Anos" value="{{ old('president_term_years', $administration->president_term_years ?? '') }}">
                    </div>
                    <div class="space-y-2">
                        <label class="form-label" for="board_term_years">Tempo de mandato dos membros da diretoria (anos)</label>
                        <input id="board_term_years" name="board_term_years" type="number" min="1" max="50" class="form-control" value="{{ old('board_term_years', $administration->board_term_years ?? '') }}" required>
                    </div>
                </div>
            </div>
        </section>

        <section class="space-y-4">
            <h2 class="text-lg font-semibold">Decis??es ministeriais</h2>
            <div class="grid gap-4">
                <div class="space-y-1">
                    <span class="form-label">Ordena????es ao minist??rio</span>
                    <label class="flex items-center gap-2 text-sm">
                        <input type="radio" name="ordination_decision" value="president" {{ old('ordination_decision', $administration->ordination_decision ?? '') === 'president' ? 'checked' : '' }} required>
                        Por decis??o do Presidente
                    </label>
                    <label class="flex items-center gap-2 text-sm">
                        <input type="radio" name="ordination_decision" value="leadership" {{ old('ordination_decision', $administration->ordination_decision ?? '') === 'leadership' ? 'checked' : '' }}>
                        Por decis??o da Lideran??a
                    </label>
                    <label class="flex items-center gap-2 text-sm">
                        <input type="radio" name="ordination_decision" value="members" {{ old('ordination_decision', $administration->ordination_decision ?? '') === 'members' ? 'checked' : '' }}>
                        Por decis??o dos Membros
                    </label>
                </div>

                <div class="space-y-1">
                    <span class="form-label">Movimenta????es financeiras e banc??rias ser??o feitas pelo</span>
                    <label class="flex items-center gap-2 text-sm">
                        <input type="radio" name="financial_responsible" value="president" {{ old('financial_responsible', $administration->financial_responsible ?? '') === 'president' ? 'checked' : '' }} required>
                        Presidente
                    </label>
                    <label class="flex items-center gap-2 text-sm">
                        <input type="radio" name="financial_responsible" value="president_treasurer" {{ old('financial_responsible', $administration->financial_responsible ?? '') === 'president_treasurer' ? 'checked' : '' }}>
                        Presidente e Tesoureiro em conjunto
                    </label>
                </div>

                <div class="space-y-2">
                    <span class="form-label">Cargos ministeriais que a organiza????o ter??</span>
                    <div class="grid gap-2 md:grid-cols-3">
                        @php
                            $roles = collect(old('ministerial_roles', $administration->ministerial_roles ?? []))->map(fn($v) => strtolower($v))->toArray();
                            $options = [
                                'apostolo' => 'Ap??stolo',
                                'bispo' => 'Bispo',
                                'diacono' => 'Di??cono',
                                'dirigente' => 'Dirigente',
                                'evangelista' => 'Evangelista',
                                'missionario' => 'Mission??rio',
                                'obreiro' => 'Obreiro',
                                'pastor' => 'Pastor',
                                'presbitero' => 'Presb??tero',
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
                    <span class="form-label">Haver?? pagamento de prebenda pastoral?</span>
                    <label class="flex items-center gap-2 text-sm">
                        <input type="radio" name="stipend_policy" value="all_pastors" {{ old('stipend_policy', $administration->stipend_policy ?? '') === 'all_pastors' ? 'checked' : '' }} required>
                        Sim, todos os Pastores receber??o pagamento
                    </label>
                    <label class="flex items-center gap-2 text-sm">
                        <input type="radio" name="stipend_policy" value="only_president" {{ old('stipend_policy', $administration->stipend_policy ?? '') === 'only_president' ? 'checked' : '' }}>
                        Sim, apenas o Pastor Presidente receber?? pagamento
                    </label>
                    <label class="flex items-center gap-2 text-sm">
                        <input type="radio" name="stipend_policy" value="none" {{ old('stipend_policy', $administration->stipend_policy ?? '') === 'none' ? 'checked' : '' }}>
                        N??o, todos os ministros trabalhar??o de maneira volunt??ria
                    </label>
                </div>
            </div>
        </section>

        <div class="flex flex-wrap justify-end gap-3">
            <a href="{{ route('dashboard') }}" class="btn-secondary">Voltar a Pagina Inicial</a>
            <button type="submit" class="btn">Salvar dados</button>
        </div>
    </form>
</div>
@endsection

