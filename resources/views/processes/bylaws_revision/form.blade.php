@extends('layouts.app')

@section('content')
<div class="card space-y-8">
    <div class="space-y-2">
        <h1 class="text-2xl font-semibold text-slate-900">Reforma de Estatuto</h1>
        <p class="text-sm text-slate-600">Preencha as etapas do processo conforme o novo schema dinâmico.</p>
    </div>

    <form method="POST" action="{{ route('processes.bylaws_revision.save', $process) }}" enctype="multipart/form-data" class="space-y-8">
        @csrf
        {{-- Exemplo de etapa: Motivos da Reforma --}}
        <section class="space-y-4">
            <h2 class="text-lg font-semibold">Motivos da Reforma</h2>
            <div class="space-y-2">
                <label class="form-label">Selecione os motivos:</label>
                <div class="flex flex-col gap-2">
                    <label><input type="checkbox" name="motivos[]" value="mudanca_endereco"> Mudança de endereço</label>
                    <label><input type="checkbox" name="motivos[]" value="mudanca_nome"> Mudança de nome</label>
                    <label><input type="checkbox" name="motivos[]" value="tempo_mandato"> Tempo de mandato</label>
                    <label><input type="checkbox" name="motivos[]" value="cargos_diretoria"> Cargos da diretoria</label>
                    <label><input type="checkbox" name="motivos[]" value="outros"> Outros</label>
                </div>
            </div>
        </section>

        {{-- Exemplo de campo condicional: Upload IPTU --}}
        <section class="space-y-4" id="iptu-upload-section" style="display:none;">
            <h2 class="text-lg font-semibold">IPTU Atualizado</h2>
            <div class="space-y-2">
                <label class="form-label" for="iptu_atualizado">Anexe o IPTU atualizado:</label>
                <input type="file" name="iptu_atualizado" id="iptu_atualizado" class="form-control">
            </div>
        </section>

        {{-- Outras etapas dinâmicas podem ser renderizadas aqui --}}

        <div class="flex justify-end">
            <button type="submit" class="btn">Salvar e avançar</button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    // Exemplo de regra condicional visual
    document.addEventListener('DOMContentLoaded', function () {
        const motivosCheckboxes = document.querySelectorAll('input[name="motivos[]"]');
        const iptuSection = document.getElementById('iptu-upload-section');
        motivosCheckboxes.forEach(cb => {
            cb.addEventListener('change', function () {
                const checked = Array.from(motivosCheckboxes).some(c => c.checked && c.value === 'mudanca_endereco');
                iptuSection.style.display = checked ? '' : 'none';
            });
        });
    });
</script>
@endpush
