<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\View\View;

class PendingSalesController extends Controller
{
    public function index(): View
    {
        return view('pending.sales', ['sales' => Sale::with('user')->where('due', '>', 0)->latest('sale_date')->latest('id')->get()]);
    }
}
