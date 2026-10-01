<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SerialSalesReturn extends Model
{
    protected $fillable = ['user_id', 'sale_id', 'return_no', 'serial_no', 'invoice_no', 'customer_name', 'product_code', 'product_name', 'reason', 'return_date', 'quantity', 'rate', 'total'];

    protected function casts(): array
    {
        return ['return_date' => 'date', 'quantity' => 'decimal:2', 'rate' => 'decimal:2', 'total' => 'decimal:2'];
    }

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
