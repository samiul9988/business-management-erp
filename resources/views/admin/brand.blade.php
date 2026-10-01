@extends('adminlte::page')

@section('title', 'Brand Entry')

@section('content_header')
    <div class="sales-breadcrumb"><i class="bi bi-house-door-fill"></i> Home <span>›</span> Administration <span>›</span> Brand Entry</div>
@stop

@section('content')
    @if (session('success')) <div class="alert alert-success"><i class="bi bi-check-circle-fill"></i> {{ session('success') }}</div> @endif
    <div class="admin-crud-layout">
        <section class="admin-crud-card">
            <div class="sales-card-heading"><h2><i class="bi bi-award"></i> Add Brand</h2></div>
            <form method="POST" action="{{ route('brand.store') }}" class="admin-crud-form">
                @csrf
                <div class="field"><label for="name">Brand Name</label><input id="name" name="name" required value="{{ old('name') }}"></div>
                @error('name') <div class="text-danger" style="font-size:.75rem">{{ $message }}</div> @enderror
                <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Save</button>
            </form>
        </section>
        <section class="admin-crud-card">
            <div class="sales-card-heading"><h2><i class="bi bi-list-ul"></i> Brand List</h2></div>
            <div class="admin-crud-table-wrap">
                <table class="admin-crud-table">
                    <thead><tr><th>Sl No</th><th>Brand Name</th><th>Action</th></tr></thead>
                    <tbody>
                        @forelse ($brands as $index => $brand)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <form method="POST" action="{{ route('brand.update', $brand) }}" style="display:flex; gap:.4rem;">
                                        @csrf @method('PUT')
                                        <input name="name" value="{{ $brand->name }}" style="padding:.3rem .5rem; border:1px solid #cad8e5; border-radius:4px;">
                                        <button type="submit" class="btn btn-sm btn-outline-primary">Update</button>
                                    </form>
                                </td>
                                <td class="row-actions">
                                    <form method="POST" action="{{ route('brand.destroy', $brand) }}" onsubmit="return confirm('Delete this brand?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="action-delete"><i class="bi bi-trash3"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3">No brands added yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
@stop
