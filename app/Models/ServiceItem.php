<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceItem extends Model
{
    protected $fillable = ['service_code', 'service_name', 'device_serial', 'problem_description', 'warranty_days', 'quantity', 'rate', 'discount_percent', 'discount_amount', 'subtotal', 'total'];

    protected function casts(): array
    {
        return ['quantity' => 'decimal:2', 'rate' => 'decimal:2', 'discount_percent' => 'decimal:2', 'discount_amount' => 'decimal:2', 'subtotal' => 'decimal:2', 'total' => 'decimal:2'];
    }

    public function serviceEntry(): BelongsTo
    {
        return $this->belongsTo(ServiceEntry::class);
    }
}
