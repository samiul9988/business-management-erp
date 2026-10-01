<?php

namespace App\Http\Controllers;

use App\Models\Cheque;
use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ChequeController extends Controller
{
    public function create(): View
    {
        return view('accounts.cheque-entry', ['customers' => Customer::orderBy('name')->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'customer_id' => ['required', 'exists:customers,id'],
            'bank_name' => ['required', 'string', 'max:255'],
            'branch_name' => ['nullable', 'string', 'max:255'],
            'cheque_no' => ['required', 'string', 'max:100'],
            'amount' => ['required', 'numeric', 'gt:0'],
            'status' => ['required', 'in:pending,paid,dishonoured'],
            'issue_date' => ['required', 'date'],
            'cheque_date' => ['required', 'date'],
            'reminder_date' => ['nullable', 'date'],
            'submit_date' => ['nullable', 'date'],
            'description' => ['nullable', 'string', 'max:255'],
        ]);
        $validated['user_id'] = $request->user()->id;
        Cheque::create($validated);

        return redirect()->route('cheque.create')->with('success', 'Cheque saved successfully.');
    }

    public function index(Request $request): View
    {
        $status = $request->query('status', 'all');
        $query = Cheque::with('customer')->latest('id');
        if (in_array($status, ['pending', 'paid', 'dishonoured'], true)) {
            $query->where('status', $status);
        }
        if ($status === 'reminder') {
            $query->whereNotNull('reminder_date')->whereDate('reminder_date', '<=', now());
        }

        return view('accounts.cheque-list', ['cheques' => $query->get(), 'status' => $status]);
    }
}
