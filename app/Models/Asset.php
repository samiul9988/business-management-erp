<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Asset extends Model
{
    protected $fillable = ['user_id', 'type', 'name', 'party_name', 'rate', 'quantity', 'amount', 'note'];

    protected function casts(): array
    {
        return ['rate' => 'decimal:2', 'quantity' => 'decimal:2', 'amount' => 'decimal:2'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
