<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class EmployeeSalesController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate(['from' => ['nullable', 'date'], 'to' => ['nullable', 'date', 'after_or_equal:from']]);
        $filters = array_merge(['from' => now()->startOfMonth()->toDateString(), 'to' => now()->toDateString()], $filters);

        $staff = Sale::query()
            ->join('users', 'users.id', '=', 'sales.user_id')
            ->select([
                'users.id as user_id', 'users.name as user_name',
                DB::raw('COUNT(sales.id) as invoice_count'),
                DB::raw('SUM(sales.total) as total_sales'),
                DB::raw('SUM(sales.paid) as total_paid'),
                DB::raw('SUM(sales.due) as total_due'),
            ])
            ->whereDate('sales.sale_date', '>=', $filters['from'])
            ->whereDate('sales.sale_date', '<=', $filters['to'])
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('total_sales')
            ->get();

        return view('reports.employee-sales', ['staff' => $staff, 'filters' => $filters]);
    }
}
