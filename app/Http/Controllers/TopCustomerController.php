<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class TopCustomerController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate(['from' => ['nullable', 'date'], 'to' => ['nullable', 'date', 'after_or_equal:from']]);
        $filters = array_merge(['from' => now()->startOfMonth()->toDateString(), 'to' => now()->toDateString()], $filters);

        $customers = Sale::query()
            ->select([
                'customer_name', 'customer_mobile',
                DB::raw('COUNT(*) as invoice_count'),
                DB::raw('SUM(total) as total_spent'),
                DB::raw('SUM(due) as total_due'),
            ])
            ->whereDate('sale_date', '>=', $filters['from'])
            ->whereDate('sale_date', '<=', $filters['to'])
            ->groupBy('customer_name', 'customer_mobile')
            ->orderByDesc('total_spent')
            ->limit(50)
            ->get();

        return view('reports.top-customers', ['customers' => $customers, 'filters' => $filters]);
    }
}
