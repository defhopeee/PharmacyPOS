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
        $yesterday = $today->copy()->subDay();

        $salestoday = (float) Sale::whereDate('createdat', $today)->sum('total');
        $salesyesterday = (float) Sale::whereDate('createdat', $yesterday)->sum('total');
        $salesmonth = (float) Sale::whereMonth('createdat', $today->month)
            ->whereYear('createdat', $today->year)
            ->sum('total');
        $lastmonth = $today->copy()->subMonth();
        $saleslastmonth = (float) Sale::whereMonth('createdat', $lastmonth->month)
            ->whereYear('createdat', $lastmonth->year)
            ->sum('total');
        $orderstoday = Sale::whereDate('createdat', $today)->count();
        $ordersyesterday = Sale::whereDate('createdat', $yesterday)->count();
        $revenue = (float) Sale::sum('total');

        // Trend indicators (current vs previous period).
        $trends = [
            'salestoday' => trendData($salestoday, $salesyesterday),
            'salesmonth' => trendData($salesmonth, $saleslastmonth),
            'orderstoday' => trendData($orderstoday, $ordersyesterday),
        ];

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

        // Sales for the current week (Monday -> Sunday) for the chart.
        $weekstart = Carbon::today()->startOfWeek(Carbon::MONDAY);
        $chart = collect(range(0, 6))->map(function ($i) use ($weekstart) {
            $day = $weekstart->copy()->addDays($i);
            return [
                'label' => $day->format('D'),
                'total' => (float) Sale::whereDate('createdat', $day)->sum('total'),
            ];
        });

        return view('owner.dashboard', compact(
            'salestoday', 'salesmonth', 'orderstoday', 'revenue',
            'productcount', 'usercount', 'lowstock', 'expiring',
            'recentsales', 'topproducts', 'chart', 'trends'
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
