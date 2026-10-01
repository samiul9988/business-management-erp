<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sale extends Model
{
    protected $fillable = ['user_id', 'invoice_no', 'sale_type', 'customer_name', 'customer_mobile', 'customer_address', 'sale_date', 'subtotal', 'vat', 'discount', 'transport_cost', 'total', 'paid', 'due'];

    protected function casts(): array
    {
        return ['sale_date' => 'date', 'subtotal' => 'decimal:2', 'vat' => 'decimal:2', 'discount' => 'decimal:2', 'transport_cost' => 'decimal:2', 'total' => 'decimal:2', 'paid' => 'decimal:2', 'due' => 'decimal:2'];
    }

    public function items(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
