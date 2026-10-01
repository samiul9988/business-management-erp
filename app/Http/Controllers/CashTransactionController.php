<?php

namespace App\Http\Controllers;

use App\Models\CashTransaction;
use App\Models\TransactionAccount;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CashTransactionController extends Controller
{
    public function index(): View
    {
        return view('accounts.cash-transaction', [
            'transactions' => CashTransaction::with('account')->latest('id')->get(),
            'accounts' => TransactionAccount::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'transaction_account_id' => ['required', 'exists:transaction_accounts,id'],
            'type' => ['required', 'in:receive,payment'],
            'transaction_date' => ['required', 'date'],
            'description' => ['nullable', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'gt:0'],
        ]);
        $validated['user_id'] = $request->user()->id;
        CashTransaction::create($validated);

        return redirect()->route('cash-transaction.index')->with('success', 'Cash transaction saved successfully.');
    }

    public function destroy(CashTransaction $cashTransaction): RedirectResponse
    {
        $cashTransaction->delete();

        return redirect()->route('cash-transaction.index')->with('success', 'Cash transaction deleted successfully.');
    }
}
