<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

class InternalUser extends Authenticatable
{
    use HasFactory;
    use Notifiable;

    public const ROLE_ADMIN = 'admin';
    public const ROLE_COLLABORATOR = 'collaborator';

    /** @var array<int, string> */
    protected $fillable = [
        'name',
        'email',
        'role',
        'password',
        'last_login_at',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'last_login_at' => 'datetime',
    ];

    /** @var array<int, string> */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isCollaborator(): bool
    {
        return $this->role === self::ROLE_COLLABORATOR;
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(InternalActivityLog::class);
    }

    public function statusChanges(): HasMany
    {
        return $this->hasMany(ProcessStatusTimeline::class, 'actor_internal_id');
    }

    public function setPasswordAttribute($value): void
    {
        if (filled($value)) {
            $info = password_get_info($value);
            $this->attributes['password'] = $info['algo'] ? $value : Hash::make($value);
        }
    }

}
