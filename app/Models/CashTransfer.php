<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CashTransfer extends Model
{
    protected $fillable = ['user_id', 'from_bank_account_id', 'to_bank_account_id', 'transfer_date', 'amount', 'note'];

    protected function casts(): array
    {
        return ['transfer_date' => 'date', 'amount' => 'decimal:2'];
    }

    public function fromAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class, 'from_bank_account_id');
    }

    public function toAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class, 'to_bank_account_id');
    }
}
