@extends('adminlte::page')

@section('title', 'Product')

@section('content_header')
    <div class="sales-breadcrumb"><i class="bi bi-house-door-fill"></i> Home <span>›</span> Administration <span>›</span> Product</div>
@stop

@section('content')
    @if (session('success')) <div class="alert alert-success"><i class="bi bi-check-circle-fill"></i> {{ session('success') }}</div> @endif
    @if ($errors->any()) <div class="alert alert-danger"><strong>Please correct the highlighted fields.</strong></div> @endif
    <div class="admin-crud-layout">
        <section class="admin-crud-card">
            <div class="sales-card-heading"><h2><i class="bi bi-box-seam"></i> Add Product</h2></div>
            <form method="POST" action="{{ route('product.store') }}" class="admin-crud-form">
                @csrf
                <div class="field"><label>Product Id / Barcode</label><input value="{{ $nextProductCode }}" disabled></div>
                <div class="field"><label for="name">Product Name</label><input id="name" name="name" required value="{{ old('name') }}"></div>
                <div class="field-row">
                    <div class="field"><label for="category_id">Category</label><select id="category_id" name="category_id"><option value="">-- Select --</option>@foreach ($categories as $category)<option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>{{ $category->name }}</option>@endforeach</select></div>
                    <div class="field"><label for="brand_id">Brand</label><select id="brand_id" name="brand_id"><option value="">-- Select --</option>@foreach ($brands as $brand)<option value="{{ $brand->id }}" @selected(old('brand_id') == $brand->id)>{{ $brand->name }}</option>@endforeach</select></div>
                </div>
                <div class="field-row">
                    <div class="field"><label for="color_id">Color</label><select id="color_id" name="color_id"><option value="">-- Select --</option>@foreach ($colors as $color)<option value="{{ $color->id }}" @selected(old('color_id') == $color->id)>{{ $color->name }}</option>@endforeach</select></div>
                    <div class="field"><label for="model">Model</label><input id="model" name="model" value="{{ old('model') }}"></div>
                </div>
                <div class="field-row">
                    <div class="field"><label for="barcode">Barcode</label><input id="barcode" name="barcode" value="{{ old('barcode') }}"></div>
                    <div class="field"><label for="reference">Reference</label><input id="reference" name="reference" value="{{ old('reference') }}"></div>
                </div>
                <div class="field-row">
                    <div class="field"><label for="unit_id">Unit</label><select id="unit_id" name="unit_id"><option value="">-- Select --</option>@foreach ($units as $unit)<option value="{{ $unit->id }}" @selected(old('unit_id') == $unit->id)>{{ $unit->name }}</option>@endforeach</select></div>
                    <div class="field"><label for="vat">VAT</label><input id="vat" name="vat" type="number" step=".01" min="0" value="{{ old('vat', 0) }}"></div>
                </div>
                <div class="field-row">
                    <div class="field"><label for="warranty_days">Warranty (Days)</label><input id="warranty_days" name="warranty_days" type="number" min="0" value="{{ old('warranty_days', 0) }}"></div>
                    <div class="field"><label for="reorder_level">Re-order Level</label><input id="reorder_level" name="reorder_level" type="number" min="0" value="{{ old('reorder_level', 0) }}"></div>
                </div>
                <div class="field-row">
                    <div class="field"><label for="purchase_rate">Purchase Rate</label><input id="purchase_rate" name="purchase_rate" type="number" step=".01" min="0" value="{{ old('purchase_rate', 0) }}"></div>
                    <div class="field"><label for="sale_rate">Sales Rate</label><input id="sale_rate" name="sale_rate" type="number" step=".01" min="0" value="{{ old('sale_rate', 0) }}"></div>
                </div>
                <div class="field-row">
                    <div class="field"><label for="min_sale_rate">Min. Sales Rate</label><input id="min_sale_rate" name="min_sale_rate" type="number" step=".01" min="0" value="{{ old('min_sale_rate', 0) }}"></div>
                    <div class="field"><label for="wholesale_rate">Wholesale Rate</label><input id="wholesale_rate" name="wholesale_rate" type="number" step=".01" min="0" value="{{ old('wholesale_rate', 0) }}"></div>
                </div>
                <div class="field" style="flex-direction:row; align-items:center; gap:.5rem;"><label style="margin:0;"><input type="checkbox" name="is_service" value="1" style="width:auto;" @checked(old('is_service'))> Is Service</label></div>
                <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Save</button>
            </form>
        </section>
        <section class="admin-crud-card">
            <div class="sales-card-heading"><h2><i class="bi bi-list-ul"></i> Product List</h2></div>
            <div class="admin-crud-table-wrap">
                <table class="admin-crud-table">
                    <thead><tr><th>Code</th><th>Name</th><th>Category</th><th>Brand</th><th>Unit</th><th>Purchase Rate</th><th>Sale Rate</th><th>Action</th></tr></thead>
                    <tbody>
                        @forelse ($products as $product)
                            <tr>
                                <td>{{ $product->product_code }}</td>
                                <td>{{ $product->name }}</td>
                                <td>{{ $product->category?->name ?? '-' }}</td>
                                <td>{{ $product->brand?->name ?? '-' }}</td>
                                <td>{{ $product->unit?->name ?? '-' }}</td>
                                <td>{{ number_format($product->purchase_rate, 2) }}</td>
                                <td>{{ number_format($product->sale_rate, 2) }}</td>
                                <td class="row-actions">
                                    <form method="POST" action="{{ route('product.destroy', $product) }}" onsubmit="return confirm('Delete this product?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="action-delete"><i class="bi bi-trash3"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8">No products added yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
@stop
