<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Customer extends Model
{
    protected $fillable = [
        'customer_code', 'mobile', 'name', 'area_id', 'address', 'owner_name', 'office_phone', 'email',
        'birthday', 'marriage_day', 'previous_due', 'credit_limit', 'customer_type', 'image',
    ];

    protected function casts(): array
    {
        return [
            'birthday' => 'date', 'marriage_day' => 'date',
            'previous_due' => 'decimal:2', 'credit_limit' => 'decimal:2',
        ];
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }
}
