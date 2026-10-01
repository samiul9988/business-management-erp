<?php

namespace App\Http\Controllers;

use App\Models\Repair;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RepairRecordController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'status' => ['nullable', 'in:all,pending,completed,non_completed,delivered,transfer,received'],
            'customer' => ['nullable', 'string', 'max:255'],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);
        $filters = array_merge(['status' => 'all', 'customer' => null, 'from' => now()->startOfMonth()->toDateString(), 'to' => now()->toDateString()], $filters);
        $query = Repair::with('assignedEmployee')->latest('repair_date')->latest('id');
        $query->when($filters['from'], fn ($builder, $from) => $builder->whereDate('repair_date', '>=', $from));
        $query->when($filters['to'], fn ($builder, $to) => $builder->whereDate('repair_date', '<=', $to));
        $query->when($filters['customer'], fn ($builder, $customer) => $builder->where('customer_name', 'like', "%{$customer}%"));
        $query->when($filters['status'] !== 'all', fn ($builder) => $builder->where('status', $filters['status']));
        $repairs = $query->paginate(20)->withQueryString();

        return view('repair.record', [
            'repairs' => $repairs,
            'filters' => $filters,
            'summary' => [
                'invoices' => $repairs->total(),
                'saleTotal' => $repairs->getCollection()->sum('total'),
                'purchaseTotal' => $repairs->getCollection()->sum('parts_purchase_amount'),
                'paid' => $repairs->getCollection()->sum('paid'),
                'due' => $repairs->getCollection()->sum('due'),
                'profit' => $repairs->getCollection()->sum(fn (Repair $repair) => (float) $repair->total - (float) $repair->parts_purchase_amount),
            ],
        ]);
    }
}
