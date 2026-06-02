<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PosController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::with('category')
            ->where('quantity', '>', 0)
            ->orderBy('name')
            ->get();

        $categories = Category::orderBy('name')->get();

        return view('pos.index', compact('products', 'categories'));
    }

    /**
     * Process a checkout. Stock is decremented inside a transaction and
     * re-checked under a row lock to prevent overselling.
     */
    public function checkout(Request $request)
    {
        $data = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.id' => ['required', 'integer', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'customer' => ['nullable', 'string', 'max:255'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'tax' => ['nullable', 'numeric', 'min:0'],
            'paid' => ['required', 'numeric', 'min:0'],
            'method' => ['required', 'in:cash,card,mobile'],
        ]);

        $sale = DB::transaction(function () use ($data, $request) {
            $subtotal = 0;
            $lines = [];

            foreach ($data['items'] as $item) {
                /** @var Product $product */
                $product = Product::lockForUpdate()->findOrFail($item['id']);

                if ($product->quantity < $item['quantity']) {
                    throw ValidationException::withMessages([
                        'items' => "Not enough stock for {$product->name}. Only {$product->quantity} left.",
                    ]);
                }

                $linetotal = $product->price * $item['quantity'];
                $subtotal += $linetotal;

                $lines[] = [
                    'product' => $product,
                    'quantity' => $item['quantity'],
                    'price' => $product->price,
                    'total' => $linetotal,
                ];
            }

            $discount = (float) ($data['discount'] ?? 0);
            $tax = (float) ($data['tax'] ?? 0);
            $total = max(0, $subtotal - $discount + $tax);

            if ($data['paid'] < $total) {
                throw ValidationException::withMessages([
                    'paid' => 'Amount paid is less than the total due.',
                ]);
            }

            $sale = Sale::create([
                'reference' => $this->reference(),
                'userid' => $request->user()->id,
                'customer' => $data['customer'] ?? null,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'discount' => $discount,
                'total' => $total,
                'paid' => $data['paid'],
                'balance' => $data['paid'] - $total,
                'method' => $data['method'],
            ]);

            foreach ($lines as $line) {
                SaleItem::create([
                    'saleid' => $sale->id,
                    'productid' => $line['product']->id,
                    'name' => $line['product']->name,
                    'price' => $line['price'],
                    'quantity' => $line['quantity'],
                    'total' => $line['total'],
                ]);

                $line['product']->decrement('quantity', $line['quantity']);
            }

            return $sale;
        });

        return response()->json([
            'message' => 'Sale completed successfully.',
            'reference' => $sale->reference,
            'receipt' => route('sales.receipt', $sale),
        ]);
    }

    private function reference(): string
    {
        return 'INV-'.Carbon::now()->format('ymd').'-'.strtoupper(\Illuminate\Support\Str::random(5));
    }
}
