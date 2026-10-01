<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\View\View;

class CustomerDueReportController extends Controller
{
    public function index(): View
    {
        return view('reports.customer-due', [
            'customers' => Customer::with('area')->where('previous_due', '>', 0)->orderByDesc('previous_due')->get(),
        ]);
    }
}
