<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
        'first_name',
        'last_name',
        'email',
        'phone',
        'password',
        'role',
        'role_id',
        'status',
        'avatar',
        'gender',
        'date_of_birth',
        'address',
        'city',
        'province',
        'country',
        'postal_code',
        'is_active',
        'provider',
        'provider_id',
        'provider_token',
        'email_verified_at',
    ];

    protected $appends = [
        'name',
        'avatar_url',
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

    protected static function booted(): void
    {
        static::saving(function (User $user) {
            if (! empty($user->attributes['first_name']) || ! empty($user->attributes['last_name'])) {
                $user->attributes['name'] = trim(($user->attributes['first_name'] ?? '') . ' ' . ($user->attributes['last_name'] ?? ''));
            }
            try {
                static $columns = null;
                if ($columns === null) {
                    $columns = \Illuminate\Support\Facades\Schema::getColumnListing($user->getTable());
                }
                if (! empty($columns)) {
                    $columnMap = array_flip($columns);
                    foreach (array_keys($user->attributes) as $attr) {
                        if (! isset($columnMap[$attr])) {
                            unset($user->attributes[$attr]);
                        }
                    }
                }
            } catch (\Throwable $e) {
                // Ignore if schema introspection is unavailable
            }
        });
    }

    /* ── Scopes ── */

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
    // app/Models/User.php

    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }

    public function roles(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_user');
    }

    public function unseenReplies()
    {
        return $this->contacts()
            ->where('status', 'replied')
            ->where('is_seen_by_customer', false)
            ->latest('replied_at');
    }

    public function unseenRepliesCount()
    {
        return $this->unseenReplies()->count();
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

    public function getFirstNameAttribute($value = null): ?string
    {
        if ($value !== null) {
            return $value;
        }
        if (array_key_exists('first_name', $this->attributes) && $this->attributes['first_name'] !== null) {
            return $this->attributes['first_name'];
        }
        if (! empty($this->attributes['name'])) {
            $parts = explode(' ', trim($this->attributes['name']), 2);

            return $parts[0] ?? '';
        }

        return null;
    }

    public function getLastNameAttribute($value = null): ?string
    {
        if ($value !== null) {
            return $value;
        }
        if (array_key_exists('last_name', $this->attributes) && $this->attributes['last_name'] !== null) {
            return $this->attributes['last_name'];
        }
        if (! empty($this->attributes['name'])) {
            $parts = explode(' ', trim($this->attributes['name']), 2);

            return $parts[1] ?? '';
        }

        return null;
    }

    public function getNameAttribute($value = null): string
    {
        $combined = trim(($this->first_name ?? '') . ' ' . ($this->last_name ?? ''));
        if ($combined !== '') {
            return $combined;
        }

        return trim((string) ($value ?? ($this->attributes['name'] ?? '')));
    }

    public function getAvatarUrlAttribute(): ?string
    {
        if (! empty($this->avatar)) {
            return str_starts_with($this->avatar, 'http')
                ? $this->avatar
                : url('storage/' . ltrim($this->avatar, '/'));
        }

        return null;
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

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }
}
