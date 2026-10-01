<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PurchaseRecordController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'supplier' => ['nullable', 'string', 'max:255'],
            'invoice_no' => ['nullable', 'string', 'max:50'],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);
        $filters = array_merge(['supplier' => null, 'invoice_no' => null, 'from' => now()->startOfMonth()->toDateString(), 'to' => now()->toDateString()], $filters);
        $query = Purchase::query()->with('items')->latest('purchase_date')->latest('id');
        $query->when($filters['from'], fn ($builder, $from) => $builder->whereDate('purchase_date', '>=', $from));
        $query->when($filters['to'], fn ($builder, $to) => $builder->whereDate('purchase_date', '<=', $to));
        $query->when($filters['supplier'], fn ($builder, $supplier) => $builder->where('supplier_name', 'like', "%{$supplier}%"));
        $query->when($filters['invoice_no'], fn ($builder, $invoice) => $builder->where('invoice_no', 'like', "%{$invoice}%"));
        $purchases = $query->paginate(20)->withQueryString();
        $suppliers = Purchase::query()->whereNotNull('supplier_name')->distinct()->orderBy('supplier_name')->pluck('supplier_name');

        return view('purchase.record', [
            'purchases' => $purchases,
            'suppliers' => $suppliers,
            'filters' => $filters,
            'summary' => ['invoices' => $purchases->total(), 'quantity' => $purchases->getCollection()->sum(fn (Purchase $purchase) => $purchase->items->sum('quantity')), 'total' => $purchases->getCollection()->sum('total'), 'paid' => $purchases->getCollection()->sum('paid'), 'due' => $purchases->getCollection()->sum('due')],
        ]);
    }
}
