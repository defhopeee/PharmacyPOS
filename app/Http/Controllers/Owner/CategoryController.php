<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->string('search')->toString();

        $categories = Category::withCount('products')
            ->when($search, fn ($q) => $q->where('name', 'like', "%{$search}%"))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('owner.categories.index', compact('categories', 'search'));
    }

    public function store(Request $request)
    {
        Category::create($this->validateData($request));

        return $this->respond($request, 'Category created.');
    }

    public function update(Request $request, Category $category)
    {
        $category->update($this->validateData($request, $category->id));

        return $this->respond($request, 'Category updated.');
    }

    public function destroy(Request $request, Category $category)
    {
        $category->delete();

        return $this->respond($request, 'Category archived.');
    }

    private function respond(Request $request, string $message)
    {
        if ($request->wantsJson()) {
            return response()->json(['message' => $message]);
        }

        return redirect()->route('owner.categories.index')->with('status', $message);
    }

    private function validateData(Request $request, ?int $ignore = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:categories,name'.($ignore ? ",{$ignore}" : '')],
            'description' => ['nullable', 'string', 'max:500'],
        ]);
    }
}
