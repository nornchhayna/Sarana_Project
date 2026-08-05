<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OtpVerification extends Model
{
    protected $fillable = [
        'identifier',
        'type',
        'purpose',
        'otp',
        'expires_at',
        'is_used',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_used' => 'boolean',
    ];

    public function isValid(string $otp): bool
    {
        return $this->otp === $otp && 
               !$this->is_used && 
               $this->expires_at->isFuture();
    }

    public function markUsed(): void
    {
        $this->update(['is_used' => true]);
    }
}