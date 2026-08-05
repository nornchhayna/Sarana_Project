<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'order_number', 'user_id', 'promotion_id', 'status',
        'shipping_name', 'shipping_phone', 'shipping_email',
        'shipping_address', 'shipping_city', 'shipping_province',
        'shipping_postal_code', 'shipping_country', 'notes',
        'subtotal', 'shipping_fee', 'discount_amount', 'tax_amount', 'total',
        'confirmed_at', 'shipped_at', 'delivered_at', 'cancelled_at',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'shipping_fee' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total' => 'decimal:2',
        'confirmed_at' => 'datetime',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    const STATUSES = [
        'pending', 'confirmed', 'processing',
        'shipped', 'delivered', 'cancelled', 'refunded',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function promotion(): BelongsTo
    {
        return $this->belongsTo(Promotion::class);
    }

    // public function items(): HasMany
    // {
    //     return $this->hasMany(OrderItem::class);
    // }
    public function items()
{
    return $this->hasMany(OrderItem::class, 'order_id'); // or whatever it actually is, e.g. 'orderId'
}

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class)->latestOfMany();
    }

    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    // ── Accessors ─────────────────────────────────────────────────

    public function getIsPaidAttribute(): bool
    {
        return $this->payments()->where('status', 'paid')->exists()
            || in_array($this->status, ['confirmed', 'processing', 'shipped', 'delivered'], true);
    }

    public function getIsCancellableAttribute(): bool
    {
        return in_array($this->status, ['pending', 'confirmed']);
    }

    // ── Scopes ────────────────────────────────────────────────────

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    // ── Helpers ───────────────────────────────────────────────────

    public static function generateOrderNumber(): string
    {
        return 'ORD-'.strtoupper(uniqid());
    }
    // In App/Models/Order.php

    public function getDeliveryStatusAttribute()
    {
        return ucfirst($this->status);
    }

    public function getIsDoneAttribute()
    {
        return $this->status === 'delivered';
    }

    public function getPayoutStatusAttribute()
    {
        // Assuming 'confirmed' status means payout is active
        return $this->status === 'confirmed' ? 'Active' : 'Pending';
    }

    // On Order model
    public function canReview(Product $product): bool
    {
        return $this->status === 'delivered'
            && ! Review::where('user_id', $this->user_id)
                ->where('product_id', $product->id)
                ->where('order_id', $this->id)
                ->exists();
    }
}
