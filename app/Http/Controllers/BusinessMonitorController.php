<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class BusinessMonitorController extends Controller
{
    public function index(?string $section = null): View
    {
        $sections = [
            'overview' => ['title' => 'Business Overview', 'icon' => 'bi-activity', 'description' => 'Live snapshot of sales, purchases, and cash position.'],
            'sales-trend' => ['title' => 'Sales Trend', 'icon' => 'bi-graph-up', 'description' => 'Track sales performance over time.'],
            'profit-loss' => ['title' => 'Profit & Loss Monitor', 'icon' => 'bi-bar-chart-fill', 'description' => 'Monitor profit and loss across branches.'],
            'stock-alert' => ['title' => 'Stock Alert Monitor', 'icon' => 'bi-exclamation-triangle', 'description' => 'Watch for low stock and out-of-stock products.'],
            'due-monitor' => ['title' => 'Due Monitor', 'icon' => 'bi-cash-stack', 'description' => 'Track outstanding customer and supplier dues.'],
            'staff-activity' => ['title' => 'Staff Activity Monitor', 'icon' => 'bi-people-fill', 'description' => 'Review recent staff activity and performance.'],
            'branch-performance' => ['title' => 'Branch Performance', 'icon' => 'bi-diagram-3-fill', 'description' => 'Compare performance across all branches.'],
        ];

        abort_unless($section === null || array_key_exists($section, $sections), 404);

        return view('business-monitor.index', [
            'sections' => $sections,
            'activeSection' => $section,
            'activeItem' => $section ? $sections[$section] : null,
        ]);
    }
}
