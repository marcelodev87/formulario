<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Leader extends Model
{
    use HasFactory;

    /** @var array<int, string> */
    protected $fillable = [
        'location_id',
        'name',
        'birth_date',
        'birthplace',
        'nationality',
        'father_name',
        'mother_name',
        'cpf',
        'rg',
        'rg_issuer',
        'gender',
        'marital_status',
        'profession',
        'email',
        'phone',
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
        'birth_date' => 'date',
    ];

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }
}
