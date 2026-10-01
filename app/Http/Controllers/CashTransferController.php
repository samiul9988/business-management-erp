<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\CashTransfer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CashTransferController extends Controller
{
    public function index(): View
    {
        return view('accounts.cash-transfer', [
            'transfers' => CashTransfer::with('fromAccount', 'toAccount')->latest('id')->get(),
            'bankAccounts' => BankAccount::orderBy('account_name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'from_bank_account_id' => ['nullable', 'exists:bank_accounts,id'],
            'to_bank_account_id' => ['nullable', 'exists:bank_accounts,id', 'different:from_bank_account_id'],
            'transfer_date' => ['required', 'date'],
            'amount' => ['required', 'numeric', 'gt:0'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);
        $validated['user_id'] = $request->user()->id;
        CashTransfer::create($validated);

        return redirect()->route('cash-transfer.index')->with('success', 'Cash transfer saved successfully.');
    }
}
