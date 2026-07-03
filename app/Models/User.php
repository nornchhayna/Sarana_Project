<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    const ROLE_CUSTOMER = 'customer';

    const ROLE_WEBMASTER = 'webmaster';

    const ROLE_ADMIN = 'admin';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role',
        'avatar',
        'gender',
        'date_of_birth',
        'address',
        'city',
        'province',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'phone_verified_at' => 'datetime',
        'date_of_birth' => 'date',
        'is_active' => 'boolean',
        'password' => 'hashed',
        'deleted_at' => 'datetime',
    ];

    /* ── Scopes ── */

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /* ── Role helpers ── */

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isWebmaster(): bool
    {
        return $this->role === self::ROLE_WEBMASTER;
    }

    public function isCustomer(): bool
    {
        return $this->role === self::ROLE_CUSTOMER;
    }

    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    /* ── Accessors ── */

    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) {
            return asset('storage/'.$this->avatar);
        }

        return 'https://ui-avatars.com/api/?name='.urlencode($this->name).'&background=6366f1&color=fff';
    }

    public function getAgeAttribute(): ?int
    {
        return $this->date_of_birth?->age;
    }

    public function getFullAddressAttribute(): string
    {
        return collect([$this->address, $this->city, $this->province])
            ->filter()
            ->implode(', ');
    }

    public function isEmailVerified(): bool
    {
        return ! is_null($this->email_verified_at);
    }

    public function isPhoneVerified(): bool
    {
        return ! is_null($this->phone_verified_at);
    }

    public function hasPhone(): bool
    {
        return ! is_null($this->phone) && $this->phone !== '';
    }
}
