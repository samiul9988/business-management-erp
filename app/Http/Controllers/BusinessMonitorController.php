<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BusinessMonitorController extends Controller
{
    private const SECTIONS = [
        'overview' => ['title' => 'Business Overview', 'icon' => 'bi-activity', 'description' => 'Live snapshot of sales, purchases, and cash position.'],
        'sales-trend' => ['title' => 'Sales Trend', 'icon' => 'bi-graph-up', 'description' => 'Track daily sales performance over the last 14 days.'],
        'stock-alert' => ['title' => 'Stock Alert Monitor', 'icon' => 'bi-exclamation-triangle', 'description' => 'Watch for low stock and out-of-stock products.'],
        'due-monitor' => ['title' => 'Due Monitor', 'icon' => 'bi-cash-stack', 'description' => 'Track outstanding customer and supplier dues.'],
        'staff-activity' => ['title' => 'Staff Activity Monitor', 'icon' => 'bi-people-fill', 'description' => 'Review recent sales activity by staff.'],
    ];

    public function index(?string $section = null, ?Request $request = null): View
    {
        $request ??= request();
        abort_unless($section === null || array_key_exists($section, self::SECTIONS), 404);

        $data = match ($section) {
            'sales-trend' => ['trend' => $this->salesTrend()],
            'stock-alert' => ['lowStockProducts' => $this->lowStockProducts()],
            'due-monitor' => ['customers' => Customer::where('previous_due', '>', 0)->orderByDesc('previous_due')->limit(50)->get(), 'suppliers' => Supplier::where('previous_due', '>', 0)->orderByDesc('previous_due')->limit(50)->get()],
            'staff-activity' => ['staff' => $this->staffActivity($request)],
            default => ['summary' => app(ProfitLossReportController::class)->summarize(now()->startOfMonth()->toDateString(), now()->toDateString()), 'trend' => $this->salesTrend(7), 'lowStockCount' => $this->lowStockProducts()->count(), 'dueCustomers' => Customer::where('previous_due', '>', 0)->count(), 'dueSuppliers' => Supplier::where('previous_due', '>', 0)->count()],
        };

        return view('business-monitor.index', [
            'sections' => self::SECTIONS,
            'activeSection' => $section,
            'activeItem' => $section ? self::SECTIONS[$section] : null,
            ...$data,
        ]);
    }

    private function salesTrend(int $days = 14)
    {
        return Sale::query()
            ->select([DB::raw('sale_date as date'), DB::raw('COUNT(*) as invoice_count'), DB::raw('SUM(total) as total')])
            ->whereDate('sale_date', '>=', now()->subDays($days - 1)->toDateString())
            ->groupBy('sale_date')
            ->orderBy('sale_date')
            ->get();
    }

    private function lowStockProducts()
    {
        $purchased = DB::table('purchase_items')->select('product_code', DB::raw('SUM(quantity) as qty'))->groupBy('product_code');
        $sold = DB::table('sale_items')->select('product_code', DB::raw('SUM(quantity) as qty'))->groupBy('product_code');

        return Product::query()
            ->leftJoinSub($purchased, 'purchased', 'purchased.product_code', '=', 'products.product_code')
            ->leftJoinSub($sold, 'sold', 'sold.product_code', '=', 'products.product_code')
            ->select(['products.id', 'products.product_code', 'products.name', 'products.reorder_level', DB::raw('(COALESCE(purchased.qty, 0) - COALESCE(sold.qty, 0)) as current_stock')])
            ->get()
            ->filter(fn ($product) => (float) $product->current_stock <= (float) $product->reorder_level)
            ->sortBy('current_stock')
            ->values();
    }

    private function staffActivity(Request $request)
    {
        $from = now()->startOfMonth()->toDateString();
        $to = now()->toDateString();

        return Sale::query()
            ->join('users', 'users.id', '=', 'sales.user_id')
            ->select(['users.name as user_name', DB::raw('COUNT(sales.id) as invoice_count'), DB::raw('SUM(sales.total) as total_sales'), DB::raw('MAX(sales.sale_date) as last_sale_date')])
            ->whereDate('sales.sale_date', '>=', $from)
            ->whereDate('sales.sale_date', '<=', $to)
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('total_sales')
            ->get();
    }
}
