<?php

namespace App\Http\Controllers;

use App\Models\TransactionAccount;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TransactionAccountController extends Controller
{
    public function index(): View
    {
        return view('accounts.account', ['accounts' => TransactionAccount::latest('id')->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:cash_in,cash_out'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);
        TransactionAccount::create($validated);

        return redirect()->route('account.index')->with('success', 'Account added successfully.');
    }

    public function destroy(TransactionAccount $account): RedirectResponse
    {
        $account->delete();

        return redirect()->route('account.index')->with('success', 'Account deleted successfully.');
    }
}
