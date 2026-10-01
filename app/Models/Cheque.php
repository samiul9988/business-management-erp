<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cheque extends Model
{
    protected $fillable = ['user_id', 'customer_id', 'bank_name', 'branch_name', 'cheque_no', 'amount', 'status', 'issue_date', 'cheque_date', 'reminder_date', 'submit_date', 'description'];

    protected function casts(): array
    {
        return ['amount' => 'decimal:2', 'issue_date' => 'date', 'cheque_date' => 'date', 'reminder_date' => 'date', 'submit_date' => 'date'];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
