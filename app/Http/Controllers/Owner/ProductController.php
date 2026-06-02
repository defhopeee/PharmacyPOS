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
        $categoryid = $request->integer('categoryid');

        $products = Product::with(['category', 'supplier'])
            ->when($search, fn ($query) => $query->where('name', 'like', "%{$search}%"))
            ->when($categoryid, fn ($q) => $q->where('categoryid', $categoryid))
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        return view('owner.products.index', [
            'products' => $products,
            'search' => $search,
            'categoryid' => $categoryid,
            'categories' => Category::orderBy('name')->get(),
            'suppliers' => Supplier::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        Product::create($this->validateData($request));

        return $this->respond($request, 'Product created successfully.');
    }

    public function update(Request $request, Product $product)
    {
        $product->update($this->validateData($request, $product->id));

        return $this->respond($request, 'Product updated successfully.');
    }

    public function destroy(Request $request, Product $product)
    {
        $product->delete();

        return $this->respond($request, 'Product archived.');
    }

    private function respond(Request $request, string $message)
    {
        if ($request->wantsJson()) {
            return response()->json(['message' => $message]);
        }

        return redirect()->route('owner.products.index')->with('status', $message);
    }

    private function validateData(Request $request, ?int $ignore = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'categoryid' => ['nullable', 'exists:categories,id'],
            'supplierid' => ['nullable', 'exists:suppliers,id'],
            'description' => ['nullable', 'string', 'max:1000'],
            'price' => ['required', 'numeric', 'min:0'],
            'cost' => ['required', 'numeric', 'min:0'],
            'quantity' => ['required', 'integer', 'min:0'],
            'reorder' => ['required', 'integer', 'min:0'],
            'expiry' => ['nullable', 'date'],
        ]);
    }
}
