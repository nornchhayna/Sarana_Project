<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model
{
    // use SoftDeletes;

    protected $fillable = [
        'order_id', 'invoice_number', 'status',
        'subtotal', 'tax_amount', 'discount_amount', 'total',
        'pdf_path', 'issued_at', 'due_at',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total' => 'decimal:2',
        'issued_at' => 'datetime',
        'due_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public static function generateInvoiceNumber(): string
    {
        return 'INV-'.date('Ymd').'-'.strtoupper(substr(uniqid(), -5));
    }
}
