<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Sale extends Model
{
    protected $fillable = ['user_id', 'customer_id', 'invoice_no', 'share_token', 'sale_type', 'customer_name', 'customer_mobile', 'customer_address', 'sale_date', 'subtotal', 'vat', 'discount', 'transport_cost', 'total', 'paid', 'due'];

    protected function casts(): array
    {
        return ['sale_date' => 'date', 'subtotal' => 'decimal:2', 'vat' => 'decimal:2', 'discount' => 'decimal:2', 'transport_cost' => 'decimal:2', 'total' => 'decimal:2', 'paid' => 'decimal:2', 'due' => 'decimal:2'];
    }

    protected static function booted(): void
    {
        static::creating(function (self $sale): void {
            $sale->share_token ??= self::generateUniqueShareToken();
        });
    }

    private static function generateUniqueShareToken(): string
    {
        do {
            $token = Str::random(12);
        } while (self::where('share_token', $token)->exists());

        return $token;
    }

    public function items(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
