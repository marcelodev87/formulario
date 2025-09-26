<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProcessType extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::saved(fn () => Process::clearTypeDefinitionsCache());
        static::deleted(fn () => Process::clearTypeDefinitionsCache());
    }

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'slug',
        'name',
        'default_title',
        'cta_label',
        'description',
        'is_active',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public static function activeOrdered()
    {
        return self::query()
            ->active()
            ->orderBy('name')
            ->get();
    }
}
