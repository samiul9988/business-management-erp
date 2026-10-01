<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SalesRecordController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'search_type' => ['nullable', 'in:all,customer,employee,quantity,category,user,invoice'],
            'record_type' => ['nullable', 'in:without_details,with_details'],
            'status' => ['nullable', 'in:all,approved,pending'],
            'customer' => ['nullable', 'string', 'max:255'],
            'invoice_no' => ['nullable', 'string', 'max:50'],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);
        $filters = array_merge(['search_type' => 'all', 'record_type' => 'without_details', 'status' => 'all', 'customer' => null, 'invoice_no' => null, 'from' => now()->startOfMonth()->toDateString(), 'to' => now()->toDateString()], $filters);
        $query = Sale::query()->with('items')->latest('sale_date')->latest('id');
        $query->when($filters['from'], fn ($builder, $from) => $builder->whereDate('sale_date', '>=', $from));
        $query->when($filters['to'], fn ($builder, $to) => $builder->whereDate('sale_date', '<=', $to));
        $query->when($filters['customer'], fn ($builder, $customer) => $builder->where('customer_name', 'like', "%{$customer}%"));
        $query->when($filters['invoice_no'], fn ($builder, $invoice) => $builder->where('invoice_no', 'like', "%{$invoice}%"));
        $sales = $query->paginate(20)->withQueryString();
        $customers = Sale::query()->whereNotNull('customer_name')->distinct()->orderBy('customer_name')->pluck('customer_name');

        return view('sales.record', [
            'sales' => $sales,
            'customers' => $customers,
            'filters' => $filters,
            'summary' => ['invoices' => $sales->total(), 'quantity' => $sales->getCollection()->sum(fn (Sale $sale) => $sale->items->sum('quantity')), 'total' => $sales->getCollection()->sum('total'), 'paid' => $sales->getCollection()->sum('paid'), 'due' => $sales->getCollection()->sum('due')],
        ]);
    }
}
