<?php

namespace App\Http\Controllers;

use App\Http\Requests\InstitutionOwnerRequest;
use App\Models\Institution;
use App\Models\Member;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class InitialSetupController extends Controller
{
    public function __construct(private readonly ActivityLogger $activityLogger)
    {
    }

    public function create(Request $request): View
    {
        $ownerMember = $request->user()?->institution?->members()->where('role', 'Presidente')->first();

        return view('setup.create', [
            'currentInstitution' => $request->user()?->institution,
            'ownerMember' => $ownerMember,
        ]);
    }

    public function storeInstitutionAndOwner(InstitutionOwnerRequest $request): RedirectResponse
    {
        $this->authorize('create', Institution::class);

        $user = $request->user();
        $validated = $request->validated();

        DB::transaction(function () use ($user, $validated) {
            $userBefore = $user->only(['name', 'phone', 'cpf']);

            $user->forceFill([
                'name' => $validated['owner_name'],
                'phone' => $validated['owner_phone'],
                'cpf' => $validated['owner_cpf'],
            ])->save();

            $this->activityLogger->log(
                actor: $user,
                institution: null,
                entityType: User::class,
                entityId: $user->id,
                action: 'updated',
                before: $userBefore,
                after: $user->only(['name', 'phone', 'cpf'])
            );

            $institution = $user->institution()->create([
                'name' => $validated['institution_name'],
                'document' => $validated['institution_document'],
                'email' => $validated['institution_email'],
                'phone' => $validated['institution_phone'],
                'street' => $validated['institution_street'],
                'number' => $validated['institution_number'],
                'complement' => $validated['institution_complement'] ?? null,
                'district' => $validated['institution_district'],
                'city' => $validated['institution_city'],
                'uf' => strtoupper($validated['institution_uf']),
                'cep' => $validated['institution_cep'],
            ]);

            $this->activityLogger->log(
                actor: $user,
                institution: $institution,
                entityType: Institution::class,
                entityId: $institution->id,
                action: 'created',
                before: [],
                after: $institution->fresh()->toArray()
            );

            $institution->invites()->create([
                'key' => Str::uuid()->toString(),
                'status' => 'active',
                'expires_at' => null,
            ]);

            $memberData = [
                'name' => $validated['owner_name'],
                'birth_date' => $validated['owner_birth_date'],
                'birthplace' => $validated['owner_birthplace'],
                'nationality' => $validated['owner_nationality'],
                'father_name' => $validated['owner_father_name'],
                'mother_name' => $validated['owner_mother_name'],
                'cpf' => $validated['owner_cpf'],
                'rg' => $validated['owner_rg'],
                'rg_issuer' => $validated['owner_rg_issuer'],
                'role' => 'Presidente',
                'gender' => $validated['owner_gender'],
                'marital_status' => $validated['owner_marital_status'],
                'profession' => $validated['owner_profession'],
                'email' => $user->email,
                'phone' => $validated['owner_phone'],
                'street' => $validated['owner_street'],
                'number' => $validated['owner_number'],
                'complement' => $validated['owner_complement'] ?? null,
                'district' => $validated['owner_district'],
                'city' => $validated['owner_city'],
                'uf' => strtoupper($validated['owner_uf']),
                'cep' => $validated['owner_cep'],
            ];

            $member = $institution->members()->where('cpf', $validated['owner_cpf'])->first();
            $memberBefore = $member?->toArray();

            if ($member) {
                $member->fill($memberData)->save();
                $action = 'updated';
            } else {
                $member = $institution->members()->create($memberData);
                $action = 'created';
            }

            $this->activityLogger->log(
                actor: $user,
                institution: $institution,
                entityType: Member::class,
                entityId: $member->id,
                action: $action,
                before: $memberBefore ?? [],
                after: $member->fresh()->toArray()
            );
        });

        return redirect()->route('dashboard')->with('status', 'Instituicao cadastrada com sucesso. Compartilhe o link de convite com os membros.');
    }
}
