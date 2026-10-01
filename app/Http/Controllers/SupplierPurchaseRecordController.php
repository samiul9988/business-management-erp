<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SupplierPurchaseRecordController extends Controller
{
    public function index(Request $request): View
    {
        $validated = $request->validate(['supplier_id' => ['nullable', 'exists:suppliers,id']]);
        $supplier = ! empty($validated['supplier_id']) ? Supplier::query()->find($validated['supplier_id']) : null;
        $purchases = $supplier
            ? Purchase::with('items')->where('supplier_id', $supplier->id)->latest('purchase_date')->latest('id')->get()
            : collect();

        return view('reports.supplier-purchase-record', [
            'suppliers' => Supplier::orderBy('name')->get(),
            'supplier' => $supplier,
            'purchases' => $purchases,
        ]);
    }
}
