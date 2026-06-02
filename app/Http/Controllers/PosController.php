<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Services\Mpesa;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
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

        return view('pos.index', [
            'products' => $products,
            'categories' => $categories,
            'mpesaSimulated' => app(Mpesa::class)->simulating(),
        ]);
    }

    /**
     * Initiate an M-Pesa STK push to the customer's phone.
     */
    public function mpesa(Request $request, Mpesa $mpesa)
    {
        $data = $request->validate([
            'phone' => ['required', 'string', 'min:9', 'max:15'],
            'amount' => ['required', 'numeric', 'min:1'],
        ]);

        $result = $mpesa->stkPush($data['phone'], (float) $data['amount'], 'POS'.Carbon::now()->format('His'));

        return response()->json($result, $result['ok'] ? 200 : 422);
    }

    public function mpesaStatus(string $checkoutid, Mpesa $mpesa)
    {
        return response()->json($mpesa->status($checkoutid));
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
            'paid' => ['required', 'numeric', 'min:0'],
            'method' => ['required', 'in:cash,card,mpesa'],
            'mpesareceipt' => ['nullable', 'string', 'max:50'],
        ]);

        $sale = DB::transaction(function () use ($data, $request) {
            $total = 0;
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
                $total += $linetotal;

                $lines[] = [
                    'product' => $product,
                    'quantity' => $item['quantity'],
                    'price' => $product->price,
                    'total' => $linetotal,
                ];
            }

            if ($data['paid'] < $total) {
                throw ValidationException::withMessages([
                    'paid' => 'Amount paid is less than the total due.',
                ]);
            }

            $sale = Sale::create([
                'reference' => $this->reference(),
                'userid' => $request->user()->id,
                'total' => $total,
                'paid' => $data['paid'],
                'balance' => $data['paid'] - $total,
                'method' => $data['method'],
                'mpesareceipt' => $data['mpesareceipt'] ?? null,
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
        return 'INV'.Carbon::now()->format('ymd').strtoupper(Str::random(5));
    }
}
