@extends('adminlte::page')

@section('title', 'Price List')

@section('content_header')
    <div class="sales-breadcrumb"><i class="bi bi-house-door-fill"></i> Home <span>›</span> Sales Module <span>›</span> Price List</div>
@stop

@section('content')
    <section class="admin-crud-card">
        <div class="sales-card-heading"><h2><i class="bi bi-cash-stack"></i> Product Price List</h2><button type="button" class="btn btn-light" onclick="window.print()"><i class="bi bi-printer"></i> Print</button></div>
        <form method="GET" action="{{ route('sales.price-list') }}" class="record-filter-card">
            <div class="record-filter-field record-search"><label for="search">Search Product</label><input id="search" name="search" value="{{ $filters['search'] }}" placeholder="Product name or code"></div>
            <button class="btn btn-primary record-search-button" type="submit"><i class="bi bi-search"></i> Search</button>
        </form>
        <div class="admin-crud-table-wrap">
            <table class="admin-crud-table">
                <thead><tr><th>Code</th><th>Product Name</th><th>Category</th><th>Brand</th><th>Unit</th><th>Purchase Rate</th><th>Sale Rate</th><th>Wholesale Rate</th></tr></thead>
                <tbody>
                    @forelse ($products as $product)
                        <tr>
                            <td>{{ $product->product_code }}</td>
                            <td>{{ $product->name }}</td>
                            <td>{{ $product->category?->name ?? '-' }}</td>
                            <td>{{ $product->brand?->name ?? '-' }}</td>
                            <td>{{ $product->unit?->name ?? '-' }}</td>
                            <td>{{ number_format((float) $product->purchase_rate, 2) }}</td>
                            <td><strong>{{ number_format((float) $product->sale_rate, 2) }}</strong></td>
                            <td>{{ number_format((float) $product->wholesale_rate, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="8">No products found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@stop
