<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    const CREATED_AT = 'createdat';
    const UPDATED_AT = 'updatedat';
    const DELETED_AT = 'deletedat';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'active',
    ];

    protected $hidden = [
        'password',
        'remembertoken',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'active' => 'boolean',
        ];
    }

    /**
     * Honour the no-underscore column rule for the remember token.
     */
    public function getRememberTokenName()
    {
        return 'remembertoken';
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class, 'userid');
    }

    public function isOwner(): bool
    {
        return $this->role === 'owner';
    }

    public function isAttendant(): bool
    {
        return $this->role === 'attendant';
    }
}
