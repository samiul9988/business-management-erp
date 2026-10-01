<?php

namespace App\Http\Controllers;

use App\Models\Warranty;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WarrantyRecordController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'status' => ['nullable', 'string', 'max:50'],
            'customer' => ['nullable', 'string', 'max:255'],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);
        $filters = array_merge(['status' => 'all', 'customer' => null, 'from' => now()->startOfMonth()->toDateString(), 'to' => now()->toDateString()], $filters);
        $query = Warranty::with('receivedByEmployee')->latest('warranty_date')->latest('id');
        $query->when($filters['from'], fn ($builder, $from) => $builder->whereDate('warranty_date', '>=', $from));
        $query->when($filters['to'], fn ($builder, $to) => $builder->whereDate('warranty_date', '<=', $to));
        $query->when($filters['customer'], fn ($builder, $customer) => $builder->where('customer_name', 'like', "%{$customer}%"));
        $query->when($filters['status'] !== 'all', fn ($builder) => $builder->where('warranty_status', $filters['status']));
        $warranties = $query->paginate(20)->withQueryString();

        return view('warranty.record', ['warranties' => $warranties, 'filters' => $filters]);
    }
}
