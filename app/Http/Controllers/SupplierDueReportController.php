<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\View\View;

class SupplierDueReportController extends Controller
{
    public function index(): View
    {
        return view('reports.supplier-due', [
            'suppliers' => Supplier::where('previous_due', '>', 0)->orderByDesc('previous_due')->get(),
        ]);
    }
}
