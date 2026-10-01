@extends('adminlte::page')

@section('title', 'Stock Report')

@section('content_header')
    <div class="sales-breadcrumb"><i class="bi bi-house-door-fill"></i> Home <span>›</span> Sales Module <span>›</span> Stock Report</div>
@stop

@section('content')
    <section class="admin-crud-card">
        <div class="sales-card-heading"><h2><i class="bi bi-list-check"></i> Stock Report</h2></div>
        <form method="GET" action="{{ route('sales.stock-report') }}" class="record-filter-card">
            <div class="record-filter-field record-search"><label for="search">Search Product</label><input id="search" name="search" value="{{ $filters['search'] }}" placeholder="Product name or code"></div>
            <button class="btn btn-primary record-search-button" type="submit"><i class="bi bi-search"></i> Search</button>
        </form>
        <div class="admin-crud-table-wrap">
            <table class="admin-crud-table">
                <thead><tr><th>Code</th><th>Product Name</th><th>Purchased</th><th>Sold</th><th>Pur. Return</th><th>Sale Return</th><th>Damaged</th><th>Current Stock</th><th>Reorder Level</th><th>Sale Rate</th></tr></thead>
                <tbody>
                    @forelse ($products as $product)
                        <tr @class(['table-danger' => (float) $product->current_stock <= (float) $product->reorder_level])>
                            <td>{{ $product->product_code }}</td>
                            <td>{{ $product->name }}</td>
                            <td>{{ number_format((float) $product->purchased_qty, 2) }}</td>
                            <td>{{ number_format((float) $product->sold_qty, 2) }}</td>
                            <td>{{ number_format((float) $product->purchase_returned_qty, 2) }}</td>
                            <td>{{ number_format((float) $product->sales_returned_qty, 2) }}</td>
                            <td>{{ number_format((float) $product->damaged_qty, 2) }}</td>
                            <td><strong>{{ number_format((float) $product->current_stock, 2) }}</strong></td>
                            <td>{{ number_format((float) $product->reorder_level, 2) }}</td>
                            <td>{{ number_format((float) $product->sale_rate, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="10">No products found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="record-pagination">{{ $products->links() }}</div>
    </section>
@stop
