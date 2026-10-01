<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class DailyProfitLossController extends Controller
{
    public function __construct(private readonly ProfitLossReportController $profitLossReport)
    {
    }

    public function index(Request $request): View
    {
        $filters = $request->validate(['month' => ['nullable', 'date_format:Y-m']]);
        $month = $filters['month'] ?? now()->format('Y-m');
        $start = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $end = (clone $start)->endOfMonth()->min(now());

        $days = [];
        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            $days[] = ['date' => $date->toDateString(), 'summary' => $this->profitLossReport->summarize($date->toDateString(), $date->toDateString())];
        }

        return view('reports.daily-profit-loss', ['month' => $month, 'days' => array_reverse($days)]);
    }
}
