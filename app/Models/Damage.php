<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Damage extends Model
{
    protected $fillable = ['user_id', 'invoice_no', 'damage_date', 'product_id', 'product_name', 'quantity', 'rate', 'amount', 'description'];

    protected function casts(): array
    {
        return ['damage_date' => 'date', 'quantity' => 'decimal:2', 'rate' => 'decimal:2', 'amount' => 'decimal:2'];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
