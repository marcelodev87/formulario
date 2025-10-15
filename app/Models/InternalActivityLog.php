<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InternalActivityLog extends Model
{
    use HasFactory;

    protected $table = 'activity_log_internal';

    /** @var array<int, string> */
    protected $fillable = [
        'internal_user_id',
        'entity',
        'entity_id',
        'action',
        'diff',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'diff' => 'array',
    ];

    public function internalUser(): BelongsTo
    {
        return $this->belongsTo(InternalUser::class);
    }
}
