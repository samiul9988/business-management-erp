<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Color;
use App\Models\Product;
use App\Models\Unit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(): View
    {
        return view('admin.product', [
            'products' => Product::with(['category', 'brand', 'color', 'unit'])->latest('id')->get(),
            'categories' => Category::orderBy('name')->get(),
            'brands' => Brand::orderBy('name')->get(),
            'colors' => Color::orderBy('name')->get(),
            'units' => Unit::orderBy('name')->get(),
            'nextProductCode' => $this->nextProductCode(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validated($request);
        $validated['product_code'] = $this->nextProductCode();
        $validated['is_service'] = $request->boolean('is_service');
        Product::create($validated);

        return redirect()->route('product.index')->with('success', 'Product added successfully.');
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $this->validated($request);
        $validated['is_service'] = $request->boolean('is_service');
        $product->update($validated);

        return redirect()->route('product.index')->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();

        return redirect()->route('product.index')->with('success', 'Product deleted successfully.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'brand_id' => ['nullable', 'exists:brands,id'],
            'color_id' => ['nullable', 'exists:colors,id'],
            'model' => ['nullable', 'string', 'max:255'],
            'barcode' => ['nullable', 'string', 'max:255'],
            'reference' => ['nullable', 'string', 'max:255'],
            'unit_id' => ['nullable', 'exists:units,id'],
            'vat' => ['nullable', 'numeric', 'min:0'],
            'warranty_days' => ['nullable', 'integer', 'min:0'],
            'reorder_level' => ['nullable', 'integer', 'min:0'],
            'purchase_rate' => ['nullable', 'numeric', 'min:0'],
            'sale_rate' => ['nullable', 'numeric', 'min:0'],
            'min_sale_rate' => ['nullable', 'numeric', 'min:0'],
            'wholesale_rate' => ['nullable', 'numeric', 'min:0'],
        ]);
    }

    private function nextProductCode(): string
    {
        $lastCode = Product::query()->latest('id')->value('product_code');
        $number = $lastCode ? ((int) $lastCode + 1) : 100001;

        return (string) $number;
    }
}
