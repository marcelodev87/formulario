<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class LoginToken extends Model
{
    use HasFactory;

    /**
     * @var string
     */
    protected $table = 'login_tokens';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'email',
        'token',
        'used_at',
        'expires_at',
        'ip',
        'user_agent',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'used_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function scopeValid($query)
    {
        return $query->whereNull('used_at')->where('expires_at', '>', now());
    }

    public function markUsed(): void
    {
        $this->forceFill([
            'used_at' => Carbon::now(),
        ])->save();
    }
}
