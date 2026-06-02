<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Route the logged-in user to the correct dashboard.
     */
    public function index()
    {
        return auth()->user()->isOwner()
            ? $this->owner()
            : $this->attendant();
    }

    private function owner()
    {
        $today = Carbon::today();

        $salestoday = Sale::whereDate('createdat', $today)->sum('total');
        $salesmonth = Sale::whereMonth('createdat', $today->month)
            ->whereYear('createdat', $today->year)
            ->sum('total');
        $orderstoday = Sale::whereDate('createdat', $today)->count();
        $revenue = Sale::sum('total');

        $productcount = Product::count();
        $usercount = User::count();
        $lowstock = Product::whereColumn('quantity', '<=', 'reorder')->orderBy('quantity')->get();
        $expiring = Product::whereNotNull('expiry')
            ->whereDate('expiry', '<=', $today->copy()->addDays(30))
            ->orderBy('expiry')
            ->get();

        $recentsales = Sale::with('user')->latest('createdat')->limit(8)->get();

        // Top selling products by quantity.
        $topproducts = SaleItem::select('name', DB::raw('SUM(quantity) as sold'), DB::raw('SUM(total) as earned'))
            ->groupBy('name')
            ->orderByDesc('sold')
            ->limit(5)
            ->get();

        // Sales for the last 7 days for the chart.
        $chart = collect(range(6, 0))->map(function ($i) {
            $day = Carbon::today()->subDays($i);
            return [
                'label' => $day->format('D'),
                'total' => (float) Sale::whereDate('createdat', $day)->sum('total'),
            ];
        });

        return view('owner.dashboard', compact(
            'salestoday', 'salesmonth', 'orderstoday', 'revenue',
            'productcount', 'usercount', 'lowstock', 'expiring',
            'recentsales', 'topproducts', 'chart'
        ));
    }

    private function attendant()
    {
        $today = Carbon::today();
        $userid = auth()->id();

        $mysalestoday = Sale::where('userid', $userid)->whereDate('createdat', $today)->sum('total');
        $myorderstoday = Sale::where('userid', $userid)->whereDate('createdat', $today)->count();
        $myrecent = Sale::where('userid', $userid)->latest('createdat')->limit(8)->get();
        $lowstock = Product::whereColumn('quantity', '<=', 'reorder')->count();

        return view('attendant.dashboard', compact(
            'mysalestoday', 'myorderstoday', 'myrecent', 'lowstock'
        ));
    }
}
