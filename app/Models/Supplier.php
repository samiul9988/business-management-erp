<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $fillable = ['supplier_code', 'mobile', 'serial_number', 'name', 'owner_name', 'mode', 'address', 'email', 'previous_due', 'image'];

    protected function casts(): array
    {
        return ['previous_due' => 'decimal:2'];
    }
}
