@extends('layouts.app')

@section('content')
<div class="card space-y-8">
    <div class="space-y-2">
        <h1 class="text-2xl font-semibold text-slate-900">Reforma de Estatuto - Dashboard</h1>
        <p class="text-sm text-slate-600">Acompanhe o progresso das principais etapas e acesse os cadastros conforme as opções escolhidas.</p>
    </div>

    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
        @if(in_array('mudanca_endereco', $motivos ?? []))
            <!-- Card: Novo Endereço -->
            <div class="card p-4 flex flex-col justify-between" x-data="{ openModal: false }" style="min-height: 230px;">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <h2 class="text-lg font-semibold">Novo Endereço</h2>
                        <span class="inline-block rounded-full bg-slate-300 text-slate-700 px-3 py-1 text-xs font-semibold">Pendente</span>
                    </div>
                    <p class="text-sm text-slate-600">Preencha os dados do novo endereço e informações do imóvel.</p>
                </div>
                <div class="flex items-center gap-2 mt-6">
                    <a href="{{ route('processes.bylaws_revision.edit_motivo', [$process, 'mudanca_endereco']) }}" class="btn-secondary flex-1 text-sm">Preencher endereço</a>
                </div>
            </div>
            <!-- Card: Cópia do Estatuto -->
            <div class="card p-4 flex flex-col justify-between" x-data="{ openModal: false }" style="min-height: 230px;">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <h2 class="text-lg font-semibold">Cópia do Estatuto</h2>
                        @if(isset($process->answers['estatuto_file']))
                            <span class="inline-block rounded-full bg-green-500 text-white px-3 py-1 text-xs font-semibold">Enviado</span>
                        @else
                            <span class="inline-block rounded-full bg-slate-300 text-slate-700 px-3 py-1 text-xs font-semibold">Pendente</span>
                        @endif
                    </div>
                    <p class="text-sm text-slate-600">Envie o estatuto atualizado em PDF ou Word (opcional).</p>
                </div>
                <div class="flex items-center gap-2 mt-6">
                    @if(isset($process->answers['estatuto_file']))
                        @php
                            $estatutoUrl = asset('storage/' . $process->answers['estatuto_file']);
                            $isPdf = Str::endsWith(strtolower($process->answers['estatuto_file']), '.pdf');
                        @endphp
                        <button type="button" class="btn-secondary flex-1 text-sm" @click="openModal = true">Visualizar arquivo</button>
                        <!-- Modal Alpine.js -->
                        <div x-show="openModal" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-60">
                            <div class="bg-white rounded-lg shadow-lg w-[90vw] h-[90vh] p-4 relative flex flex-col">
                                <button @click="openModal = false" class="absolute top-2 right-2 text-slate-500 hover:text-slate-800">&times;</button>
                                <h3 class="text-lg font-semibold mb-2">Visualização do Estatuto</h3>
                                @if($isPdf)
                                    <iframe src="{{ $estatutoUrl }}" class="w-full flex-1 border" style="height:75vh;" frameborder="0"></iframe>
                                @else
                                    <div class="text-center py-8 flex-1 flex flex-col justify-center items-center">
                                        <p class="mb-2">Visualização não suportada para este formato.</p>
                                        <a href="{{ $estatutoUrl }}" class="btn" download>Baixar arquivo</a>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <form method="POST" action="{{ route('processes.bylaws_revision.delete_statute', $process) }}" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-secondary bg-red-100 text-red-700 hover:bg-red-200 flex-1 text-sm">Excluir</button>
                        </form>
                    @else
                        <button type="button" class="btn-secondary flex-1 text-sm" @click="openModal = true">Enviar arquivo</button>
                        <!-- Modal de upload do estatuto -->
                        <div x-show="openModal" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-60">
                            <div class="bg-white rounded-lg shadow-lg w-[90vw] max-w-xl p-6 relative flex flex-col">
                                <button @click="openModal = false" class="absolute top-2 right-2 text-slate-500 hover:text-slate-800 text-2xl">&times;</button>
                                <h2 class="text-xl font-semibold mb-4">Cópia do Estatuto</h2>
                                <form method="POST" action="{{ route('processes.bylaws_revision.save_statute', $process) }}" enctype="multipart/form-data" class="space-y-4">
                                    @csrf
                                    <div>
                                        <label class="form-label">Arquivo do Estatuto (PDF ou Word)</label>
                                        <input type="file" name="estatuto_file" class="form-control" accept=".pdf,.doc,.docx">
                                        <small class="text-slate-500">Opcional. Envie o estatuto atualizado, se desejar.</small>
                                    </div>
                                    <button type="submit" class="btn w-full bg-blue-700 text-white">Enviar arquivo</button>
                                </form>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            <!-- Card: Atualização do Estatuto -->
            <div class="card p-4 flex flex-col justify-between" style="min-height: 230px;">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <h2 class="text-lg font-semibold">Atualização do Estatuto</h2>
                    </div>
                    <p class="text-sm text-slate-600">Certifique-se de que o novo endereço está refletido no estatuto. Caso precise de auxílio, entre em contato com o suporte.</p>
                </div>
            </div>
        @endif
        {{-- Outros cards de motivos podem ser adicionados aqui --}}
    </div>
</div>
@endsection
