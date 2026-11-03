<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\Customer;
use App\Models\CustomerPayment;
use Illuminate\Database\DatabaseManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerPaymentController extends Controller
{
    public function index(): View
    {
        return view('accounts.customer-payment', [
            'payments' => CustomerPayment::with('customer', 'bankAccount')->latest('id')->get(),
            'customers' => Customer::orderBy('name')->get(),
            'bankAccounts' => BankAccount::orderBy('account_name')->get(),
        ]);
    }

    public function store(Request $request, DatabaseManager $database): RedirectResponse
    {
        $validated = $request->validate([
            'customer_id' => ['required', 'exists:customers,id'],
            'transaction_type' => ['required', 'in:receive,payment'],
            'payment_type' => ['required', 'in:cash,bank'],
            'bank_account_id' => ['nullable', 'exists:bank_accounts,id'],
            'payment_date' => ['required', 'date'],
            'description' => ['nullable', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'gt:0'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'discount_percent' => ['nullable', 'numeric', 'between:0,100'],
        ]);
        $validated['user_id'] = $request->user()->id;

        $database->transaction(function () use ($validated): void {
            $customer = Customer::query()->findOrFail($validated['customer_id']);
            CustomerPayment::create($validated);
            $this->applyAdjustment($customer, $validated['transaction_type'], (float) $validated['amount'] + (float) ($validated['discount'] ?? 0));
        });

        return redirect()->route('customer-payment.index')->with('success', 'Customer payment saved successfully.');
    }

    public function update(Request $request, CustomerPayment $customerPayment, DatabaseManager $database): RedirectResponse
    {
        $request->merge(['customer_id' => $customerPayment->customer_id]);
        $validated = $request->validate([
            'customer_id' => ['required', 'exists:customers,id'],
            'transaction_type' => ['required', 'in:receive,payment'],
            'payment_type' => ['required', 'in:cash,bank'],
            'bank_account_id' => ['nullable', 'exists:bank_accounts,id'],
            'payment_date' => ['required', 'date'],
            'description' => ['nullable', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'gt:0'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'discount_percent' => ['nullable', 'numeric', 'between:0,100'],
        ]);

        $database->transaction(function () use ($validated, $customerPayment): void {
            $customer = $customerPayment->customer;
            $oldAdjustment = (float) $customerPayment->amount + (float) $customerPayment->discount;
            $this->applyAdjustment($customer, $customerPayment->transaction_type, $oldAdjustment, reverse: true);

            $customerPayment->update($validated);

            $customer->refresh();
            $this->applyAdjustment($customer, $validated['transaction_type'], (float) $validated['amount'] + (float) ($validated['discount'] ?? 0));
        });

        return redirect()->route('customer-payment.index')->with('success', 'Customer payment updated successfully.');
    }

    private function applyAdjustment(Customer $customer, string $type, float $adjustment, bool $reverse = false): void
    {
        $effectiveType = $reverse ? ($type === 'receive' ? 'payment' : 'receive') : $type;
        $due = $effectiveType === 'receive'
            ? max((float) $customer->previous_due - $adjustment, 0)
            : (float) $customer->previous_due + $adjustment;
        $customer->update(['previous_due' => $due]);
    }
}
