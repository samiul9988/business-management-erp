<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PriceListController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate(['search' => ['nullable', 'string', 'max:255']]);

        $products = Product::query()
            ->with(['category', 'brand', 'unit'])
            ->when($filters['search'] ?? null, fn ($builder, $search) => $builder->where(fn ($q) => $q->where('name', 'like', "%{$search}%")->orWhere('product_code', 'like', "%{$search}%")))
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('reports.price-list', ['products' => $products, 'filters' => array_merge(['search' => null], $filters)]);
    }
}
