<?php

namespace App\Http\Controllers;

use App\Models\CashTransaction;
use App\Models\Damage;
use App\Models\Purchase;
use App\Models\Repair;
use App\Models\Sale;
use App\Models\SalaryPayment;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfitLossReportController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);
        $filters = array_merge(['from' => now()->startOfMonth()->toDateString(), 'to' => now()->toDateString()], $filters);

        return view('reports.profit-loss', ['filters' => $filters, 'summary' => $this->summarize($filters['from'], $filters['to'])]);
    }

    public function summarize(string $from, string $to): array
    {
        $salesTotal = (float) Sale::whereDate('sale_date', '>=', $from)->whereDate('sale_date', '<=', $to)->sum('total');
        $purchaseTotal = (float) Purchase::whereDate('purchase_date', '>=', $from)->whereDate('purchase_date', '<=', $to)->sum('total');
        $repairIncome = (float) Repair::whereDate('repair_date', '>=', $from)->whereDate('repair_date', '<=', $to)->sum('total');
        $repairPartsCost = (float) Repair::whereDate('repair_date', '>=', $from)->whereDate('repair_date', '<=', $to)->sum('parts_purchase_amount');
        $damageLoss = (float) Damage::whereDate('damage_date', '>=', $from)->whereDate('damage_date', '<=', $to)->sum('amount');
        $salaryExpense = (float) SalaryPayment::whereDate('payment_date', '>=', $from)->whereDate('payment_date', '<=', $to)->sum('amount');
        $cashExpense = (float) CashTransaction::where('type', 'payment')->whereDate('transaction_date', '>=', $from)->whereDate('transaction_date', '<=', $to)->sum('amount');

        $grossProfit = $salesTotal + $repairIncome - $purchaseTotal - $repairPartsCost;
        $netProfit = $grossProfit - $damageLoss - $salaryExpense - $cashExpense;

        return [
            'salesTotal' => $salesTotal, 'purchaseTotal' => $purchaseTotal, 'repairIncome' => $repairIncome,
            'repairPartsCost' => $repairPartsCost, 'damageLoss' => $damageLoss, 'salaryExpense' => $salaryExpense,
            'cashExpense' => $cashExpense, 'grossProfit' => $grossProfit, 'netProfit' => $netProfit,
        ];
    }
}
