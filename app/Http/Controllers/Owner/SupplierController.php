<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->string('search')->toString();

        $suppliers = Supplier::withCount('products')
            ->when($search, fn ($q) => $q->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%"))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('owner.suppliers.index', compact('suppliers', 'search'));
    }

    public function store(Request $request)
    {
        Supplier::create($this->validateData($request));

        return $this->respond($request, 'Supplier created.');
    }

    public function update(Request $request, Supplier $supplier)
    {
        $supplier->update($this->validateData($request));

        return $this->respond($request, 'Supplier updated.');
    }

    public function destroy(Request $request, Supplier $supplier)
    {
        $supplier->delete();

        return $this->respond($request, 'Supplier archived.');
    }

    private function respond(Request $request, string $message)
    {
        if ($request->wantsJson()) {
            return response()->json(['message' => $message]);
        }

        return redirect()->route('owner.suppliers.index')->with('status', $message);
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
