<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::withCount('products')->orderBy('name')->paginate(15);

        return view('owner.suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        return view('owner.suppliers.form', ['supplier' => new Supplier()]);
    }

    public function store(Request $request)
    {
        Supplier::create($this->validateData($request));

        return redirect()->route('owner.suppliers.index')
            ->with('status', 'Supplier created.');
    }

    public function edit(Supplier $supplier)
    {
        return view('owner.suppliers.form', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $supplier->update($this->validateData($request));

        return redirect()->route('owner.suppliers.index')
            ->with('status', 'Supplier updated.');
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();

        return redirect()->route('owner.suppliers.index')
            ->with('status', 'Supplier deleted.');
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string', 'max:500'],
        ]);
    }
}
