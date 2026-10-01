@extends('adminlte::page')

@section('title', $activeItem['title'] ?? 'Business Monitor')

@section('content_header')
    <div class="sales-breadcrumb"><i class="bi bi-house-door-fill"></i> Home <span>›</span> Business Monitor <span>›</span> {{ $activeItem['title'] ?? 'Overview' }}</div>
@stop

@section('content')
    <div class="sales-module-shell">
        <div class="sales-module-heading">
            <h1>{{ $activeItem['title'] ?? 'Business Monitor' }}</h1>
            <p>{{ $activeItem['description'] ?? 'Keep an eye on sales, stock, dues, and staff activity in real time.' }}</p>
        </div>

        @if ($activeSection === null)
            <div class="record-summary">
                <div><small>Sales (This Month)</small><strong>{{ number_format((float) $summary['salesTotal'], 2) }}</strong></div>
                <div><small>Purchases (This Month)</small><strong>{{ number_format((float) $summary['purchaseTotal'], 2) }}</strong></div>
                <div><small>Net Profit (This Month)</small><strong>{{ number_format((float) $summary['netProfit'], 2) }}</strong></div>
                <div><small>Low Stock Products</small><strong>{{ $lowStockCount }}</strong></div>
                <div class="due-summary"><small>Customers With Due</small><strong>{{ $dueCustomers }}</strong></div>
                <div class="due-summary"><small>Suppliers With Due</small><strong>{{ $dueSuppliers }}</strong></div>
            </div>
            <div class="record-table-card">
                <div class="record-table-heading"><h2><i class="bi bi-graph-up"></i> Sales — Last 7 Days</h2></div>
                <div class="table-responsive">
                    <table class="sales-record-table">
                        <thead><tr><th>Date</th><th>Invoices</th><th>Total</th></tr></thead>
                        <tbody>
                            @forelse ($trend as $row)
                                <tr><td>{{ \Illuminate\Support\Carbon::parse($row->date)->format('d/m/Y') }}</td><td>{{ $row->invoice_count }}</td><td class="amount-cell">{{ number_format((float) $row->total, 2) }}</td></tr>
                            @empty
                                <tr><td colspan="3" class="empty-records"><i class="bi bi-inbox"></i><strong>No sales in the last 7 days</strong></td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @elseif ($activeSection === 'sales-trend')
            <div class="record-table-card">
                <div class="record-table-heading"><h2><i class="bi bi-graph-up"></i> Sales — Last 14 Days</h2></div>
                <div class="table-responsive">
                    <table class="sales-record-table">
                        <thead><tr><th>Date</th><th>Invoices</th><th>Total</th></tr></thead>
                        <tbody>
                            @forelse ($trend as $row)
                                <tr><td>{{ \Illuminate\Support\Carbon::parse($row->date)->format('d/m/Y') }}</td><td>{{ $row->invoice_count }}</td><td class="amount-cell">{{ number_format((float) $row->total, 2) }}</td></tr>
                            @empty
                                <tr><td colspan="3" class="empty-records"><i class="bi bi-inbox"></i><strong>No sales in this period</strong></td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @elseif ($activeSection === 'stock-alert')
            <div class="record-table-card">
                <div class="record-table-heading"><h2><i class="bi bi-exclamation-triangle"></i> Low / Out of Stock Products</h2><span>{{ $lowStockProducts->count() }} product(s)</span></div>
                <div class="table-responsive">
                    <table class="sales-record-table">
                        <thead><tr><th>Code</th><th>Product</th><th>Current Stock</th><th>Reorder Level</th></tr></thead>
                        <tbody>
                            @forelse ($lowStockProducts as $product)
                                <tr><td>{{ $product->product_code }}</td><td>{{ $product->name }}</td><td class="due-cell">{{ number_format((float) $product->current_stock, 2) }}</td><td>{{ number_format((float) $product->reorder_level, 2) }}</td></tr>
                            @empty
                                <tr><td colspan="4" class="empty-records"><i class="bi bi-check-circle"></i><strong>All products are sufficiently stocked</strong></td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @elseif ($activeSection === 'due-monitor')
            <div class="record-table-card">
                <div class="record-table-heading"><h2><i class="bi bi-person-vcard"></i> Customers With Due</h2><span>{{ $customers->count() }}</span></div>
                <div class="table-responsive">
                    <table class="sales-record-table">
                        <thead><tr><th>Customer</th><th>Mobile</th><th>Due</th></tr></thead>
                        <tbody>
                            @forelse ($customers as $customer)
                                <tr><td>{{ $customer->name }}</td><td>{{ $customer->mobile ?: '-' }}</td><td class="due-cell">{{ number_format((float) $customer->previous_due, 2) }}</td></tr>
                            @empty
                                <tr><td colspan="3" class="empty-records"><i class="bi bi-check-circle"></i><strong>No customer dues</strong></td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="record-table-card">
                <div class="record-table-heading"><h2><i class="bi bi-truck"></i> Suppliers With Due</h2><span>{{ $suppliers->count() }}</span></div>
                <div class="table-responsive">
                    <table class="sales-record-table">
                        <thead><tr><th>Supplier</th><th>Mobile</th><th>Due</th></tr></thead>
                        <tbody>
                            @forelse ($suppliers as $supplier)
                                <tr><td>{{ $supplier->name }}</td><td>{{ $supplier->mobile ?: '-' }}</td><td class="due-cell">{{ number_format((float) $supplier->previous_due, 2) }}</td></tr>
                            @empty
                                <tr><td colspan="3" class="empty-records"><i class="bi bi-check-circle"></i><strong>No supplier dues</strong></td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @elseif ($activeSection === 'staff-activity')
            <div class="record-table-card">
                <div class="record-table-heading"><h2><i class="bi bi-people-fill"></i> Staff Sales Activity (This Month)</h2></div>
                <div class="table-responsive">
                    <table class="sales-record-table">
                        <thead><tr><th>Staff</th><th>Invoices</th><th>Total Sales</th><th>Last Sale</th></tr></thead>
                        <tbody>
                            @forelse ($staff as $row)
                                <tr><td>{{ $row->user_name }}</td><td>{{ $row->invoice_count }}</td><td class="amount-cell">{{ number_format((float) $row->total_sales, 2) }}</td><td>{{ \Illuminate\Support\Carbon::parse($row->last_sale_date)->format('d/m/Y') }}</td></tr>
                            @empty
                                <tr><td colspan="4" class="empty-records"><i class="bi bi-inbox"></i><strong>No staff sales activity this month</strong></td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <div class="sales-tile-grid">
            @foreach ($sections as $key => $item)
                <a href="{{ route('business-monitor.section', ['section' => $key]) }}" class="sales-tile {{ $activeSection === $key ? 'is-active' : '' }}">
                    <i class="bi {{ $item['icon'] }}"></i>
                    <span>{{ $item['title'] }}</span>
                </a>
            @endforeach
        </div>
    </div>
@stop
