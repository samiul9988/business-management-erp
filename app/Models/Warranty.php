<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Warranty extends Model
{
    protected $fillable = [
        'user_id', 'invoice_no', 'warranty_date', 'customer_id', 'customer_name', 'customer_mobile',
        'received_by_employee_id', 'warranty_status', 'estimated_delivery_date', 'claim_status', 'product_name',
        'sale_out_date', 'serial_no', 'quantity', 'problem', 'condition', 'note', 'warranty_validity',
        'supplier_id', 'transfer_by_employee_id', 'transfer_date', 'receive_by_sr', 'return_received_by_employee_id',
        'return_condition', 'received_date', 'delivered_by_employee_id', 'remarks', 'delivered_date',
    ];

    protected function casts(): array
    {
        return [
            'warranty_date' => 'date', 'estimated_delivery_date' => 'date', 'sale_out_date' => 'date',
            'transfer_date' => 'date', 'received_date' => 'date', 'delivered_date' => 'date',
            'quantity' => 'decimal:2', 'warranty_validity' => 'boolean',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function receivedByEmployee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'received_by_employee_id');
    }

    public function transferByEmployee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'transfer_by_employee_id');
    }

    public function returnReceivedByEmployee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'return_received_by_employee_id');
    }

    public function deliveredByEmployee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'delivered_by_employee_id');
    }
}
