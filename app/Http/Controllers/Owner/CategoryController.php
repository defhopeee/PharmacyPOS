<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('products')->orderBy('name')->paginate(15);

        return view('owner.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('owner.categories.form', ['category' => new Category()]);
    }

    public function store(Request $request)
    {
        Category::create($this->validateData($request));

        return redirect()->route('owner.categories.index')
            ->with('status', 'Category created.');
    }

    public function edit(Category $category)
    {
        return view('owner.categories.form', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $category->update($this->validateData($request, $category->id));

        return redirect()->route('owner.categories.index')
            ->with('status', 'Category updated.');
    }

    public function destroy(Category $category)
    {
        $category->delete();

        return redirect()->route('owner.categories.index')
            ->with('status', 'Category deleted.');
    }

    private function validateData(Request $request, ?int $ignore = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:categories,name'.($ignore ? ",{$ignore}" : '')],
            'description' => ['nullable', 'string', 'max:500'],
        ]);
    }
}
