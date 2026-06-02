<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Every seeded user shares this password as requested.
     */
    private const PASSWORD = 'Password123!';

    public function run(): void
    {
        $this->seedUsers();
        $categories = $this->seedCategories();
        $suppliers = $this->seedSuppliers();
        $this->seedProducts($categories, $suppliers);
        $this->seedSales();
    }

    private function seedUsers(): void
    {
        $users = [
            ['name' => 'Diana Mwangi', 'email' => 'dianamwangi@gmail.com', 'role' => 'owner', 'phone' => '+1 555 0100'],
            ['name' => 'Alex Otieno', 'email' => 'alexotieno@gmail.com', 'role' => 'attendant', 'phone' => '+1 555 0101'],
            ['name' => 'Brenda Achieng', 'email' => 'brendaachieng@gmail.com', 'role' => 'attendant', 'phone' => '+1 555 0102'],
            ['name' => 'Caleb Kimani', 'email' => 'calebkimani@gmail.com', 'role' => 'attendant', 'phone' => '+1 555 0103'],
        ];

        foreach ($users as $u) {
            User::updateOrCreate(
                ['email' => $u['email']],
                array_merge($u, ['password' => self::PASSWORD, 'active' => true])
            );
        }
    }

    private function seedCategories(): array
    {
        $names = [
            'Pain Relief' => 'Analgesics and anti-inflammatory medication',
            'Antibiotics' => 'Prescription antibacterial medication',
            'Vitamins & Supplements' => 'Daily health and wellness products',
            'Cold & Flu' => 'Cough, cold and allergy relief',
            'First Aid' => 'Bandages, antiseptics and wound care',
            'Personal Care' => 'Hygiene and personal care products',
            'Digestive Health' => 'Antacids and digestive remedies',
        ];

        $map = [];
        foreach ($names as $name => $desc) {
            $map[$name] = Category::updateOrCreate(['name' => $name], ['description' => $desc]);
        }

        return $map;
    }

    private function seedSuppliers(): array
    {
        $suppliers = [
            ['name' => 'MediSource Distributors', 'phone' => '+1 555 2000', 'email' => 'medisourcesales@gmail.com', 'address' => '12 Industrial Way'],
            ['name' => 'HealthLine Pharma', 'phone' => '+1 555 2001', 'email' => 'healthlineorders@gmail.com', 'address' => '88 Market Street'],
            ['name' => 'GlobalMed Supplies', 'phone' => '+1 555 2002', 'email' => 'globalmedinfo@gmail.com', 'address' => '5 Commerce Plaza'],
        ];

        $list = [];
        foreach ($suppliers as $s) {
            $list[] = Supplier::updateOrCreate(['name' => $s['name']], $s);
        }

        return $list;
    }

    private function seedProducts(array $categories, array $suppliers): void
    {
        $products = [
            ['Paracetamol 500mg', 'Pain Relief', 2.50, 1.20, 320, 50],
            ['Ibuprofen 400mg', 'Pain Relief', 3.20, 1.60, 210, 40],
            ['Aspirin 300mg', 'Pain Relief', 2.10, 0.90, 28, 30],
            ['Amoxicillin 250mg', 'Antibiotics', 6.80, 3.50, 90, 25],
            ['Azithromycin 500mg', 'Antibiotics', 9.40, 5.10, 14, 20],
            ['Vitamin C 1000mg', 'Vitamins & Supplements', 5.50, 2.40, 180, 40],
            ['Vitamin D3 2000IU', 'Vitamins & Supplements', 7.20, 3.30, 130, 30],
            ['Multivitamin Daily', 'Vitamins & Supplements', 8.90, 4.20, 75, 30],
            ['Cough Syrup 120ml', 'Cold & Flu', 4.60, 2.10, 95, 25],
            ['Antihistamine Tablets', 'Cold & Flu', 3.80, 1.70, 60, 25],
            ['Nasal Decongestant', 'Cold & Flu', 4.10, 1.90, 8, 20],
            ['Adhesive Bandages', 'First Aid', 3.00, 1.10, 240, 50],
            ['Antiseptic Solution', 'First Aid', 5.20, 2.30, 110, 30],
            ['Gauze Roll', 'First Aid', 2.40, 0.95, 150, 40],
            ['Hand Sanitiser 250ml', 'Personal Care', 3.90, 1.50, 200, 50],
            ['Face Masks (50pk)', 'Personal Care', 9.90, 4.50, 65, 30],
            ['Antacid Tablets', 'Digestive Health', 3.30, 1.40, 140, 35],
            ['Oral Rehydration Salts', 'Digestive Health', 1.80, 0.70, 18, 30],
        ];

        foreach ($products as $i => [$name, $cat, $price, $cost, $qty, $reorder]) {
            Product::updateOrCreate(
                ['name' => $name],
                [
                    'categoryid' => $categories[$cat]->id,
                    'supplierid' => $suppliers[$i % count($suppliers)]->id,
                    'barcode' => '50' . str_pad((string) ($i + 1), 6, '0', STR_PAD_LEFT),
                    'description' => $name . ' — quality assured pharmaceutical product.',
                    'price' => $price,
                    'cost' => $cost,
                    'quantity' => $qty,
                    'reorder' => $reorder,
                    'expiry' => Carbon::today()->addDays(($i % 6) * 25 + 20),
                ]
            );
        }
    }

    private function seedSales(): void
    {
        if (Sale::count() > 0) {
            return; // keep idempotent — do not pile up demo sales on re-seed
        }

        $attendants = User::where('role', 'attendant')->get();
        $products = Product::all();
        $methods = ['cash', 'card', 'mpesa'];
        $customers = ['Walk-in', 'John Doe', 'Mary Smith', 'Peter Jones', null, 'Grace Lee'];

        // Spread ~40 sales across the last 14 days.
        for ($n = 0; $n < 40; $n++) {
            $when = Carbon::now()->subDays($n % 14)->subHours(($n * 3) % 9)->subMinutes(($n * 7) % 60);
            $attendant = $attendants[$n % $attendants->count()];

            $lineCount = 1 + ($n % 4);
            $picked = $products->random(min($lineCount, $products->count()));

            $subtotal = 0;
            $lines = [];
            foreach ($picked as $p) {
                $q = 1 + ($n % 3);
                $lineTotal = $p->price * $q;
                $subtotal += $lineTotal;
                $lines[] = ['p' => $p, 'q' => $q, 'total' => $lineTotal];
            }

            $discount = $n % 5 === 0 ? round($subtotal * 0.05, 2) : 0;
            $total = $subtotal - $discount;
            $paid = ceil($total / 5) * 5; // round up to nearest 5

            $sale = Sale::create([
                'reference' => 'INV-' . $when->format('ymd') . '-' . strtoupper(Str::random(5)),
                'userid' => $attendant->id,
                'customer' => $customers[$n % count($customers)],
                'subtotal' => $subtotal,
                'tax' => 0,
                'discount' => $discount,
                'total' => $total,
                'paid' => $paid,
                'balance' => $paid - $total,
                'method' => $methods[$n % count($methods)],
                'createdat' => $when,
                'updatedat' => $when,
            ]);

            foreach ($lines as $line) {
                SaleItem::create([
                    'saleid' => $sale->id,
                    'productid' => $line['p']->id,
                    'name' => $line['p']->name,
                    'price' => $line['p']->price,
                    'quantity' => $line['q'],
                    'total' => $line['total'],
                    'createdat' => $when,
                    'updatedat' => $when,
                ]);
            }
        }
    }
}
