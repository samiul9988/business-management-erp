<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\View\View;

class EmployeeListController extends Controller
{
    public function index(string $status = 'all'): View
    {
        $query = Employee::with('designation', 'department')->latest('id');
        if (in_array($status, ['active', 'deactive'], true)) {
            $query->where('status', $status);
        }

        return view('hr.employee-list', ['employees' => $query->get(), 'status' => $status]);
    }
}
