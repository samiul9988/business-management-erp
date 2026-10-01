<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\BankTransaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BankTransactionController extends Controller
{
    public function index(): View
    {
        return view('accounts.bank-transaction', [
            'transactions' => BankTransaction::with('bankAccount')->latest('id')->get(),
            'bankAccounts' => BankAccount::orderBy('account_name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'bank_account_id' => ['required', 'exists:bank_accounts,id'],
            'type' => ['required', 'in:deposit,withdraw'],
            'transaction_date' => ['required', 'date'],
            'amount' => ['required', 'numeric', 'gt:0'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);
        $validated['user_id'] = $request->user()->id;
        BankTransaction::create($validated);

        return redirect()->route('bank-transaction.index')->with('success', 'Bank transaction saved successfully.');
    }

    public function destroy(BankTransaction $bankTransaction): RedirectResponse
    {
        $bankTransaction->delete();

        return redirect()->route('bank-transaction.index')->with('success', 'Bank transaction deleted successfully.');
    }
}
