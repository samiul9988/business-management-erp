<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    protected $fillable = [
        'product_code', 'name', 'category_id', 'brand_id', 'color_id', 'model', 'barcode', 'reference',
        'unit_id', 'vat', 'warranty_days', 'reorder_level', 'purchase_rate', 'sale_rate', 'min_sale_rate',
        'wholesale_rate', 'is_service', 'status',
    ];

    protected function casts(): array
    {
        return [
            'vat' => 'decimal:2', 'purchase_rate' => 'decimal:2', 'sale_rate' => 'decimal:2',
            'min_sale_rate' => 'decimal:2', 'wholesale_rate' => 'decimal:2', 'is_service' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function color(): BelongsTo
    {
        return $this->belongsTo(Color::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }
}
