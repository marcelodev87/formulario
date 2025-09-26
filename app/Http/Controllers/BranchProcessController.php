<?php

namespace App\Http\Controllers;

use App\Models\Process;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BranchProcessController extends Controller
{
    public function show(Request $request, Process $process): View
    {
        abort_if($process->type !== Process::TYPE_BRANCH_OPENING, 404);

        $this->authorize('view', $process);

        $process->load('institution.owner');

        $location = $process->location()->with(['property', 'leader'])->first();

        $addressComplete = false;
        $displayLocation = $location;

        if ($location) {
            $addressComplete = collect(['street', 'number', 'district', 'city', 'uf', 'cep'])
                ->every(fn (string $field) => filled($location->{$field}));
        }

        $property = $location?->property;
        $propertyComplete = $property !== null;

        $leader = $location?->leader;
        $leaderComplete = $leader !== null;

        $statusItems = [
            [
                'key' => 'location',
                'icon' => 'pin',
                'title' => 'Endereco da filial',
                'meta' => $addressComplete ? 'Endereco cadastrado.' : 'Endereco pendente.',
                'description' => 'Localizacao utilizada em contratos e comunicacoes oficiais.',
                'complete' => $addressComplete,
                'action' => route('processes.branch.location.edit', $process),
                'action_label' => $addressComplete ? 'Revisar endereco' : 'Cadastrar endereco',
            ],
            [
                'key' => 'property',
                'icon' => 'building',
                'title' => 'Dados do imovel',
                'meta' => $propertyComplete ? 'Informacoes cadastradas.' : 'Dados do imovel pendentes.',
                'description' => 'Caracteristicas do imovel para documentacao e contratos.',
                'complete' => $propertyComplete,
                'action' => route('processes.branch.location.edit', $process) . '#property-section',
                'action_label' => $propertyComplete ? 'Revisar imovel' : 'Cadastrar imovel',
            ],
            [
                'key' => 'leader',
                'icon' => 'user',
                'title' => 'Dirigente da filial',
                'meta' => $leaderComplete ? 'Dirigente cadastrado.' : 'Dirigente pendente.',
                'description' => 'Responsavel legal pela conducao das atividades na filial.',
                'complete' => $leaderComplete,
                'action' => route('processes.branch.leader.edit', $process),
                'action_label' => $leaderComplete ? 'Revisar dirigente' : 'Cadastrar dirigente',
            ],
        ];

        return view('processes.branch.show', [
            'process' => $process,
            'institution' => $process->institution,
            'location' => $displayLocation,
            'property' => $property,
            'leader' => $leader,
            'statusItems' => $statusItems,
        ]);
    }
}
