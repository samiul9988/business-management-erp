<?php

namespace App\Http\Controllers;

use App\Models\CashTransaction;
use App\Models\CashTransfer;
use App\Models\CustomerPayment;
use App\Models\SalaryPayment;
use App\Models\SupplierPayment;
use Illuminate\View\View;

class CashViewController extends Controller
{
    public function index(): View
    {
        $movements = collect();

        CashTransaction::with('account')->each(function (CashTransaction $transaction) use ($movements): void {
            $movements->push([
                'date' => $transaction->transaction_date, 'description' => $transaction->account?->name ?? $transaction->description,
                'in' => $transaction->type === 'receive' ? (float) $transaction->amount : 0,
                'out' => $transaction->type === 'payment' ? (float) $transaction->amount : 0,
            ]);
        });

        CustomerPayment::where('payment_type', 'cash')->with('customer')->each(function (CustomerPayment $payment) use ($movements): void {
            $movements->push([
                'date' => $payment->payment_date, 'description' => 'Customer: '.($payment->customer?->name ?? '-'),
                'in' => $payment->transaction_type === 'receive' ? (float) $payment->amount : 0,
                'out' => $payment->transaction_type === 'payment' ? (float) $payment->amount : 0,
            ]);
        });

        SupplierPayment::where('payment_type', 'cash')->with('supplier')->each(function (SupplierPayment $payment) use ($movements): void {
            $movements->push([
                'date' => $payment->payment_date, 'description' => 'Supplier: '.($payment->supplier?->name ?? '-'),
                'in' => $payment->transaction_type === 'receive' ? (float) $payment->amount : 0,
                'out' => $payment->transaction_type === 'payment' ? (float) $payment->amount : 0,
            ]);
        });

        SalaryPayment::where('payment_method', 'cash')->with('salary.employee')->each(function (SalaryPayment $payment) use ($movements): void {
            $movements->push(['date' => $payment->payment_date, 'description' => 'Salary: '.($payment->salary?->employee?->name ?? '-'), 'in' => 0, 'out' => (float) $payment->amount]);
        });

        CashTransfer::where(fn ($q) => $q->whereNull('from_bank_account_id')->orWhereNull('to_bank_account_id'))->with('fromAccount', 'toAccount')->each(function (CashTransfer $transfer) use ($movements): void {
            if ($transfer->from_bank_account_id === null) {
                $movements->push(['date' => $transfer->transfer_date, 'description' => 'Transfer to '.($transfer->toAccount?->account_name ?? 'bank'), 'in' => 0, 'out' => (float) $transfer->amount]);
            }
            if ($transfer->to_bank_account_id === null) {
                $movements->push(['date' => $transfer->transfer_date, 'description' => 'Transfer from '.($transfer->fromAccount?->account_name ?? 'bank'), 'in' => (float) $transfer->amount, 'out' => 0]);
            }
        });

        $movements = $movements->sortBy('date')->values();
        $balance = 0;
        $movements = $movements->map(function (array $movement) use (&$balance): array {
            $balance += $movement['in'] - $movement['out'];
            $movement['balance'] = $balance;

            return $movement;
        })->reverse()->values();

        return view('reports.cash-view', ['movements' => $movements, 'currentBalance' => $balance]);
    }
}
