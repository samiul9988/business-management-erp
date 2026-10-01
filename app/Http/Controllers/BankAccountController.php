<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BankAccountController extends Controller
{
    public function index(): View
    {
        return view('accounts.bank-account', ['bankAccounts' => BankAccount::latest('id')->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validated($request);
        BankAccount::create($validated);

        return redirect()->route('bank-account.index')->with('success', 'Bank account added successfully.');
    }

    public function destroy(BankAccount $bankAccount): RedirectResponse
    {
        $bankAccount->delete();

        return redirect()->route('bank-account.index')->with('success', 'Bank account deleted successfully.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'account_name' => ['required', 'string', 'max:255'],
            'account_no' => ['nullable', 'string', 'max:100'],
            'account_type' => ['nullable', 'string', 'max:100'],
            'bank_name' => ['nullable', 'string', 'max:255'],
            'branch_name' => ['nullable', 'string', 'max:255'],
            'initial_balance' => ['nullable', 'numeric', 'min:0'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);
    }
}
