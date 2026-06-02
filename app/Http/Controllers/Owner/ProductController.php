<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->string('search')->toString();

        $products = Product::with(['category', 'supplier'])
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('barcode', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        return view('owner.products.index', compact('products', 'search'));
    }

    public function create()
    {
        return view('owner.products.form', [
            'product' => new Product(),
            'categories' => Category::orderBy('name')->get(),
            'suppliers' => Supplier::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        Product::create($data);

        return redirect()->route('owner.products.index')
            ->with('status', 'Product created successfully.');
    }

    public function edit(Product $product)
    {
        return view('owner.products.form', [
            'product' => $product,
            'categories' => Category::orderBy('name')->get(),
            'suppliers' => Supplier::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $data = $this->validateData($request, $product->id);
        $product->update($data);

        return redirect()->route('owner.products.index')
            ->with('status', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('owner.products.index')
            ->with('status', 'Product deleted.');
    }

    private function validateData(Request $request, ?int $ignore = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'categoryid' => ['nullable', 'exists:categories,id'],
            'supplierid' => ['nullable', 'exists:suppliers,id'],
            'barcode' => ['nullable', 'string', 'max:100', 'unique:products,barcode'.($ignore ? ",{$ignore}" : '')],
            'description' => ['nullable', 'string', 'max:1000'],
            'price' => ['required', 'numeric', 'min:0'],
            'cost' => ['required', 'numeric', 'min:0'],
            'quantity' => ['required', 'integer', 'min:0'],
            'reorder' => ['required', 'integer', 'min:0'],
            'expiry' => ['nullable', 'date'],
        ]);
    }
}
