<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerPayment;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerPaymentReportController extends Controller
{
    public function index(Request $request): View
    {
        $validated = $request->validate(['customer_id' => ['nullable', 'exists:customers,id']]);
        $customer = ! empty($validated['customer_id']) ? Customer::query()->find($validated['customer_id']) : null;
        $payments = collect();

        if ($customer) {
            $payments = CustomerPayment::where('customer_id', $customer->id)->latest('payment_date')->latest('id')->get();
            $balance = (float) $customer->previous_due;
            foreach ($payments as $payment) {
                $payment->running_balance = $balance;
                $adjustment = (float) $payment->amount + (float) $payment->discount;
                $balance += $payment->transaction_type === 'receive' ? $adjustment : -$adjustment;
            }
            $payments = $payments->sortBy('payment_date')->values();
        }

        return view('reports.customer-payment', [
            'customers' => Customer::orderBy('name')->get(),
            'customer' => $customer,
            'payments' => $payments,
        ]);
    }
}
