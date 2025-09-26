<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class Institution extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'document',
        'email',
        'phone',
        'street',
        'number',
        'complement',
        'district',
        'city',
        'uf',
        'cep',
        'owner_user_id',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }

    public function members(): HasMany
    {
        return $this->hasMany(Member::class);
    }

    public function invites(): HasMany
    {
        return $this->hasMany(Invite::class);
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function locations(): HasMany
    {
        return $this->hasMany(Location::class);
    }

    public function processes(): HasMany
    {
        return $this->hasMany(Process::class);
    }

    public function headquartersLocation(): HasOne
    {
        return $this->hasOne(Location::class)->where('type', 'headquarters');
    }

    public function branchLocations(): HasMany
    {
        return $this->hasMany(Location::class)->where('type', 'branch');
    }

    public function property(): HasOneThrough
    {
        return $this->hasOneThrough(LocationProperty::class, Location::class, 'institution_id', 'location_id')
            ->where('locations.type', 'headquarters');
    }

    public function ensureHeadquartersLocation(): Location
    {
        $location = $this->headquartersLocation()->first();

        if ($location) {
            return $location;
        }

        return $this->headquartersLocation()->create([
            'type' => 'headquarters',
            'name' => $this->name,
            'street' => $this->street,
            'number' => $this->number,
            'complement' => $this->complement,
            'district' => $this->district,
            'city' => $this->city,
            'uf' => $this->uf,
            'cep' => $this->cep,
        ]);
    }

    public function administration(): HasOne
    {
        return $this->hasOne(InstitutionAdministration::class);
    }
}
