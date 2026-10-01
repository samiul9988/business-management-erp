<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    protected $fillable = [
        'employee_code', 'name', 'designation_id', 'department_id', 'join_date', 'salary_range', 'status',
        'present_address', 'permanent_address', 'contact_no', 'email', 'image', 'reference',
        'father_name', 'mother_name', 'gender', 'date_of_birth', 'marital_status',
    ];

    protected function casts(): array
    {
        return ['join_date' => 'date', 'date_of_birth' => 'date', 'salary_range' => 'decimal:2'];
    }

    public function designation(): BelongsTo
    {
        return $this->belongsTo(Designation::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function salaries(): HasMany
    {
        return $this->hasMany(Salary::class);
    }
}
