<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyProfile extends Model
{
    protected $fillable = [
        'name', 'description', 'logo', 'branch_name', 'branch_title', 'invoice_print_type',
        'branch_address', 'invoice_header', 'invoice_footer',
    ];
}
