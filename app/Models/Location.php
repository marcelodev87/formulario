<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Location extends Model
{
    use HasFactory;

    /** @var array<int, string> */
    protected $fillable = [
        'institution_id',
        'process_id',
        'type',
        'name',
        'street',
        'number',
        'complement',
        'district',
        'city',
        'uf',
        'cep',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'institution_id' => 'integer',
        'process_id' => 'integer',
    ];

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    public function process(): BelongsTo
    {
        return $this->belongsTo(Process::class);
    }

    public function property(): HasOne
    {
        return $this->hasOne(LocationProperty::class);
    }

    public function leader(): HasOne
    {
        return $this->hasOne(Leader::class);
    }

    public function scopeType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function isHeadquarters(): bool
    {
        return $this->type === 'headquarters';
    }

    public function isBranch(): bool
    {
        return $this->type === 'branch';
    }

    public function syncInstitutionAddressIfHeadquarters(): void
    {
        if (!$this->relationLoaded('institution')) {
            $this->load('institution');
        }

        if ($this->institution && $this->isHeadquarters()) {
            $this->institution->forceFill([
                'street' => $this->street,
                'number' => $this->number,
                'complement' => $this->complement,
                'district' => $this->district,
                'city' => $this->city,
                'uf' => $this->uf,
                'cep' => $this->cep,
            ])->save();
        }
    }
}
