<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    /**
     * Owners see every sale; attendants see only their own.
     */
    public function index(Request $request)
    {
        $query = Sale::with('user')->latest('createdat');

        if (auth()->user()->isAttendant()) {
            $query->where('userid', auth()->id());
        }

        if ($reference = $request->string('search')->toString()) {
            $query->where('reference', 'like', "%{$reference}%");
        }

        $sales = $query->paginate(15)->withQueryString();

        return view('sales.index', compact('sales'));
    }

    public function show(Sale $sale)
    {
        // Attendants may only view their own receipts.
        if (auth()->user()->isAttendant() && $sale->userid !== auth()->id()) {
            abort(403);
        }

        $sale->load(['items', 'user']);

        return view('sales.show', compact('sale'));
    }

    public function receipt(Sale $sale)
    {
        if (auth()->user()->isAttendant() && $sale->userid !== auth()->id()) {
            abort(403);
        }

        $sale->load(['items', 'user']);

        return view('sales.receipt', compact('sale'));
    }
}
