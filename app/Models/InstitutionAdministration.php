<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InstitutionAdministration extends Model
{
    use HasFactory;

    protected $fillable = [
        'institution_id',
        'dissolution_mode',
        'governance_model',
        'president_term_indefinite',
        'president_term_years',
        'board_term_years',
        'ordination_decision',
        'financial_responsible',
        'ministerial_roles',
        'stipend_policy',
    ];

    protected $casts = [
        'president_term_indefinite' => 'boolean',
        'president_term_years' => 'integer',
        'board_term_years' => 'integer',
        'ministerial_roles' => 'array',
    ];

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }
}
