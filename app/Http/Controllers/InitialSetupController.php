<?php

namespace App\Http\Controllers;

use App\Http\Requests\InstitutionOwnerRequest;
use App\Models\Institution;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class InitialSetupController extends Controller
{
    public function __construct(private readonly ActivityLogger $activityLogger)
    {
    }

    public function create(Request $request): RedirectResponse
    {
        return redirect()->route('dashboard');
    }

    public function storeInstitutionAndOwner(InstitutionOwnerRequest $request): RedirectResponse
    {
        $this->authorize('create', Institution::class);

        $user = $request->user();
        $validated = $request->validated();

        DB::transaction(function () use ($user, $validated) {
            $user->name = $validated['institution_name'];
            $user->cpf = $validated['institution_document'];
            $user->email = $validated['institution_email'];
            $user->phone = $validated['institution_phone'];
            $user->save();

            $institution = $user->institution()->create([
                'name' => $validated['institution_name'],
                'document' => $validated['institution_document'],
                'email' => $validated['institution_email'],
                'phone' => $validated['institution_phone'],
                'owner_user_id' => $user->id,
            ]);

            $user->institution_id = $institution->id;
            $user->save();

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
        });

        return redirect()->route('dashboard')->with('status', 'Instituicao cadastrada com sucesso.');
    }
}
