<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mandate extends Model
{
    use HasFactory;

    protected $fillable = [
        'process_id',
        'start_date',
        'duration_years',
    ];

    protected $casts = [
        'start_date' => 'date',
        'duration_years' => 'integer',
    ];

    public function process()
    {
        return $this->belongsTo(Process::class);
    }
}
