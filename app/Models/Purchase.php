<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Purchase extends Model
{
    protected $fillable = ['user_id', 'supplier_id', 'invoice_no', 'supplier_name', 'supplier_mobile', 'supplier_address', 'purchase_date', 'subtotal', 'vat', 'discount', 'transport_cost', 'total', 'paid', 'due'];

    protected function casts(): array
    {
        return ['purchase_date' => 'date', 'subtotal' => 'decimal:2', 'vat' => 'decimal:2', 'discount' => 'decimal:2', 'transport_cost' => 'decimal:2', 'total' => 'decimal:2', 'paid' => 'decimal:2', 'due' => 'decimal:2'];
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
