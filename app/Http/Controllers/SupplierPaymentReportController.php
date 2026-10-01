<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\SupplierPayment;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SupplierPaymentReportController extends Controller
{
    public function index(Request $request): View
    {
        $validated = $request->validate(['supplier_id' => ['nullable', 'exists:suppliers,id']]);
        $supplier = ! empty($validated['supplier_id']) ? Supplier::query()->find($validated['supplier_id']) : null;
        $payments = collect();

        if ($supplier) {
            $payments = SupplierPayment::where('supplier_id', $supplier->id)->latest('payment_date')->latest('id')->get();
            $balance = (float) $supplier->previous_due;
            foreach ($payments as $payment) {
                $payment->running_balance = $balance;
                $adjustment = (float) $payment->amount + (float) $payment->discount;
                $balance += $payment->transaction_type === 'payment' ? $adjustment : -$adjustment;
            }
            $payments = $payments->sortBy('payment_date')->values();
        }

        return view('reports.supplier-payment', [
            'suppliers' => Supplier::orderBy('name')->get(),
            'supplier' => $supplier,
            'payments' => $payments,
        ]);
    }
}
