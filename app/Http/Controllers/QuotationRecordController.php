<?php

namespace App\Http\Controllers;

use App\Models\Quotation;
use Illuminate\Http\Request;
use Illuminate\View\View;

class QuotationRecordController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'customer' => ['nullable', 'string', 'max:255'],
            'quotation_no' => ['nullable', 'string', 'max:50'],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);
        $filters = array_merge(['customer' => null, 'quotation_no' => null, 'from' => now()->startOfMonth()->toDateString(), 'to' => now()->toDateString()], $filters);

        $query = Quotation::query()->with('items')->latest('quotation_date')->latest('id');
        $query->when($filters['from'], fn ($builder, $from) => $builder->whereDate('quotation_date', '>=', $from));
        $query->when($filters['to'], fn ($builder, $to) => $builder->whereDate('quotation_date', '<=', $to));
        $query->when($filters['customer'], fn ($builder, $customer) => $builder->where('customer_name', 'like', "%{$customer}%"));
        $query->when($filters['quotation_no'], fn ($builder, $no) => $builder->where('quotation_no', 'like', "%{$no}%"));

        return view('quotations.record', [
            'quotations' => $query->paginate(20)->withQueryString(),
            'filters' => $filters,
        ]);
    }
}
