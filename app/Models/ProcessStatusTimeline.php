<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProcessStatusTimeline extends Model
{
    use HasFactory;

    protected $table = 'status_timeline';

    /** @var array<int, string> */
    protected $fillable = [
        'process_id',
        'from_status',
        'to_status',
        'actor_internal_id',
        'note',
    ];

    public function process(): BelongsTo
    {
        return $this->belongsTo(Process::class);
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(InternalUser::class, 'actor_internal_id');
    }
}
