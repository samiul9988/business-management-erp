<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quotation extends Model
{
    protected $fillable = [
        'user_id', 'quotation_no', 'customer_name', 'customer_mobile', 'customer_address',
        'quotation_date', 'valid_until', 'subtotal', 'vat', 'discount', 'transport_cost', 'total', 'status',
    ];

    protected function casts(): array
    {
        return [
            'quotation_date' => 'date', 'valid_until' => 'date',
            'subtotal' => 'decimal:2', 'vat' => 'decimal:2', 'discount' => 'decimal:2',
            'transport_cost' => 'decimal:2', 'total' => 'decimal:2',
        ];
    }

    public function items(): HasMany
    {
        return $this->hasMany(QuotationItem::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
