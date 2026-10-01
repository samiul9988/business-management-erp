<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class StockReportController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate(['search' => ['nullable', 'string', 'max:255']]);

        $purchased = DB::table('purchase_items')->select('product_code', DB::raw('SUM(quantity) as qty'))->groupBy('product_code');
        $sold = DB::table('sale_items')->select('product_code', DB::raw('SUM(quantity) as qty'))->groupBy('product_code');
        $purchaseReturned = DB::table('purchase_return_items')->select('product_code', DB::raw('SUM(quantity) as qty'))->groupBy('product_code');
        $salesReturned = DB::table('sales_return_items')->select('product_code', DB::raw('SUM(quantity) as qty'))->groupBy('product_code');
        $damaged = DB::table('damages')->select('product_id', DB::raw('SUM(quantity) as qty'))->groupBy('product_id');

        $query = Product::query()
            ->leftJoinSub($purchased, 'purchased', 'purchased.product_code', '=', 'products.product_code')
            ->leftJoinSub($sold, 'sold', 'sold.product_code', '=', 'products.product_code')
            ->leftJoinSub($purchaseReturned, 'purchase_returned', 'purchase_returned.product_code', '=', 'products.product_code')
            ->leftJoinSub($salesReturned, 'sales_returned', 'sales_returned.product_code', '=', 'products.product_code')
            ->leftJoinSub($damaged, 'damaged', 'damaged.product_id', '=', 'products.id')
            ->select([
                'products.id', 'products.product_code', 'products.name', 'products.reorder_level', 'products.sale_rate',
                DB::raw('COALESCE(purchased.qty, 0) as purchased_qty'),
                DB::raw('COALESCE(sold.qty, 0) as sold_qty'),
                DB::raw('COALESCE(purchase_returned.qty, 0) as purchase_returned_qty'),
                DB::raw('COALESCE(sales_returned.qty, 0) as sales_returned_qty'),
                DB::raw('COALESCE(damaged.qty, 0) as damaged_qty'),
                DB::raw('(COALESCE(purchased.qty, 0) + COALESCE(sales_returned.qty, 0) - COALESCE(sold.qty, 0) - COALESCE(purchase_returned.qty, 0) - COALESCE(damaged.qty, 0)) as current_stock'),
            ])
            ->when($filters['search'] ?? null, fn ($builder, $search) => $builder->where(fn ($q) => $q->where('products.name', 'like', "%{$search}%")->orWhere('products.product_code', 'like', "%{$search}%")))
            ->orderBy('products.name');

        return view('reports.stock-report', [
            'products' => $query->paginate(25)->withQueryString(),
            'filters' => array_merge(['search' => null], $filters),
        ]);
    }
}
