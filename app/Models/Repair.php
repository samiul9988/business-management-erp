<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Repair extends Model
{
    protected $fillable = [
        'user_id', 'invoice_no', 'repair_date', 'customer_id', 'customer_name', 'customer_mobile', 'customer_address',
        'assigned_employee_id', 'expected_delivery_date', 'status', 'repair_company_id', 'brand', 'model', 'serial_no',
        'problem', 'warranty_period', 'extra_received', 'parts_name', 'parts_supplier_id', 'parts_purchase_date',
        'parts_purchase_rate', 'parts_quantity', 'parts_purchase_amount', 'parts_warranty_days',
        'subtotal', 'discount', 'total', 'payment_method', 'paid', 'due',
    ];

    protected function casts(): array
    {
        return [
            'repair_date' => 'date', 'expected_delivery_date' => 'date', 'parts_purchase_date' => 'date',
            'warranty_period' => 'boolean', 'extra_received' => 'boolean',
            'parts_purchase_rate' => 'decimal:2', 'parts_quantity' => 'decimal:2', 'parts_purchase_amount' => 'decimal:2',
            'subtotal' => 'decimal:2', 'discount' => 'decimal:2', 'total' => 'decimal:2', 'paid' => 'decimal:2', 'due' => 'decimal:2',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function assignedEmployee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'assigned_employee_id');
    }

    public function repairCompany(): BelongsTo
    {
        return $this->belongsTo(RepairCompany::class);
    }

    public function partsSupplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'parts_supplier_id');
    }
}
