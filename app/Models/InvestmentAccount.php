<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvestmentAccount extends Model
{
    protected $fillable = ['account_name', 'account_no', 'account_type', 'bank_name', 'branch_name', 'initial_balance', 'description'];

    protected function casts(): array
    {
        return ['initial_balance' => 'decimal:2'];
    }
}
