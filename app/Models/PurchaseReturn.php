<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseReturn extends Model
{
    protected $fillable = ['user_id', 'purchase_id', 'return_no', 'invoice_no', 'supplier_name', 'return_date', 'reason', 'subtotal', 'total'];

    protected function casts(): array
    {
        return ['return_date' => 'date', 'subtotal' => 'decimal:2', 'total' => 'decimal:2'];
    }

    public function purchase(): BelongsTo
    {
        return $this->belongsTo(Purchase::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseReturnItem::class);
    }
}
