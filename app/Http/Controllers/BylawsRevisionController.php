<?php

namespace App\Http\Controllers;

use App\Http\Requests\InstitutionAddressRequest;
use App\Models\InternalActivityLog;
use App\Models\Location;
use App\Models\Process;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class BylawsRevisionController extends Controller
{
    public function deleteStatute(Process $process): RedirectResponse
    {
        $answers = $process->answers ?? [];

        if (!empty($answers['estatuto_file'])) {
            Storage::disk('public')->delete($answers['estatuto_file']);
            unset($answers['estatuto_file']);
            $process->answers = $answers;
            $process->save();
        }

        return redirect()->route('processes.bylaws_revision.dashboard', $process)
            ->with('status', 'Arquivo do estatuto excluido com sucesso.');
    }

    public function show(Request $request, Process $process): View
    {
        abort_if($process->type !== 'bylaws_revision', 404);

        return view('processes.bylaws_revision.form', [
            'process' => $process,
        ]);
    }

    public function save(Request $request, Process $process): RedirectResponse
    {
        $answers = $request->except(['_token']);
        $process->answers = $answers;
        $process->save();

        return redirect()->route('processes.bylaws_revision.dashboard', $process)
            ->with('status', 'Respostas salvas com sucesso.');
    }

    public function dashboard(Process $process): View
    {
        abort_if($process->type !== 'bylaws_revision', 404);

        $motivos = $process->answers['motivos'] ?? [];
        $location = $process->location ?? new Location();
        $addressComplete = $location->exists
            ? collect(['street', 'number', 'district', 'city', 'uf', 'cep'])->every(fn ($field) => filled($location->{$field}))
            : false;

        return view('processes.bylaws_revision.dashboard', [
            'process' => $process,
            'motivos' => $motivos,
            'location' => $location,
            'addressComplete' => $addressComplete,
        ]);
    }

    public function editMotivo(Process $process, string $motivo): View
    {
        abort_if($process->type !== 'bylaws_revision', 404);

        $motivoLabels = [
            'mudanca_nome' => 'Mudanca de nome',
            'mudanca_endereco' => 'Mudanca de endereco',
            'tempo_mandato' => 'Tempo de mandato',
            'cargos_diretoria' => 'Cargos da diretoria',
            'outros' => 'Outros',
        ];

        $location = $this->makeLocationPrototype($process);

        return view('processes.bylaws_revision.edit_motivo', [
            'process' => $process,
            'motivo' => $motivo,
            'motivoLabel' => $motivoLabels[$motivo] ?? $motivo,
            'location' => $location,
            'data' => $process->answers[$motivo] ?? [],
        ]);
    }

    public function updateMotivo(Request $request, Process $process, string $motivo): RedirectResponse
    {
        abort_if($process->type !== 'bylaws_revision', 404);

        if ($motivo === 'mudanca_endereco') {
            $validated = $this->validateAddress($request);

            $location = $process->location()->firstOrNew([]);
            $before = $location->exists ? $location->toArray() : [];

            $location->fill([
                'institution_id' => $process->institution_id,
                'process_id' => $process->id,
                'type' => 'process',
                'name' => $location->name,
                'street' => $validated['street'],
                'number' => $validated['number'],
                'complement' => $validated['complement'] ?? null,
                'district' => $validated['district'],
                'city' => $validated['city'],
                'uf' => strtoupper($validated['uf']),
                'cep' => $validated['cep'],
            ])->save();

            InternalActivityLog::create([
                'internal_user_id' => $request->user()?->id,
                'entity' => Process::class,
                'entity_id' => $process->id,
                'action' => 'bylaws_address_updated',
                'diff' => [
                    'before' => $before,
                    'after' => $location->fresh()->toArray(),
                ],
            ]);

            $process->answers = $this->syncAnswers($process->answers ?? [], $motivo, $validated);
            $process->save();

            return redirect()->route('processes.bylaws_revision.dashboard', $process)
                ->with('status', 'Endereco atualizado com sucesso.');
        }

        $payload = $request->except(['_token', '_method']);
        $process->answers = $this->syncAnswers($process->answers ?? [], $motivo, $payload);
        $process->save();

        return redirect()->route('processes.bylaws_revision.dashboard', $process)
            ->with('status', 'Informacoes do motivo atualizadas com sucesso.');
    }

    public function uploadStatute(Process $process): View
    {
        abort_if($process->type !== 'bylaws_revision', 404);

        return view('processes.bylaws_revision.upload_statute', [
            'process' => $process,
        ]);
    }

    public function saveStatute(Request $request, Process $process): RedirectResponse
    {
        abort_if($process->type !== 'bylaws_revision', 404);

        if ($request->hasFile('estatuto_file')) {
            $file = $request->file('estatuto_file');
            $path = $file->store('estatutos', 'public');
            $answers = $process->answers ?? [];
            $answers['estatuto_file'] = $path;
            $process->answers = $answers;
            $process->save();
        }

        return redirect()->route('processes.bylaws_revision.dashboard', $process)
            ->with('status', 'Arquivo do estatuto salvo com sucesso.');
    }

    private function validateAddress(Request $request): array
    {
        $rules = (new InstitutionAddressRequest())->rules();
        $messages = (new InstitutionAddressRequest())->messages();

        return $request->validate($rules, $messages);
    }

    private function syncAnswers(array $answers, string $motivo, array $payload): array
    {
        $answers[$motivo] = $payload;

        $motivos = Arr::wrap($answers['motivos'] ?? []);
        $motivos[] = $motivo;
        $answers['motivos'] = array_values(array_unique($motivos));

        return $answers;
    }

    private function makeLocationPrototype(Process $process): Location
    {
        $location = $process->location;

        if ($location) {
            return $location;
        }

        $base = optional($process->institution)->headquartersLocation;

        $attributes = [
            'street' => $base->street ?? '',
            'number' => $base->number ?? '',
            'complement' => $base->complement ?? '',
            'district' => $base->district ?? '',
            'city' => $base->city ?? '',
            'uf' => $base->uf ?? '',
            'cep' => $base->cep ?? '',
        ];

        return new Location($attributes);
    }
}
