<?php

namespace App\Http\Controllers;

use App\Models\SalesReturn;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SalesReturnRecordController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'customer' => ['nullable', 'string', 'max:255'],
            'invoice_no' => ['nullable', 'string', 'max:50'],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);
        $filters = array_merge(['customer' => null, 'invoice_no' => null, 'from' => now()->startOfMonth()->toDateString(), 'to' => now()->toDateString()], $filters);

        $query = SalesReturn::query()->with('items')->latest('return_date')->latest('id');
        $query->when($filters['from'], fn ($builder, $from) => $builder->whereDate('return_date', '>=', $from));
        $query->when($filters['to'], fn ($builder, $to) => $builder->whereDate('return_date', '<=', $to));
        $query->when($filters['customer'], fn ($builder, $customer) => $builder->where('customer_name', 'like', "%{$customer}%"));
        $query->when($filters['invoice_no'], fn ($builder, $invoice) => $builder->where('invoice_no', 'like', "%{$invoice}%"));

        $returns = $query->paginate(20)->withQueryString();

        return view('sales.return-record', [
            'returns' => $returns,
            'filters' => $filters,
            'summary' => ['count' => $returns->total(), 'total' => $returns->getCollection()->sum('total')],
        ]);
    }
}
