<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\SaleItem;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        return view('owner.reports.index', $this->build($request));
    }

    public function pdf(Request $request)
    {
        $data = $this->build($request);
        $data['generatedAt'] = Carbon::now();

        $pdf = Pdf::loadView('owner.reports.pdf', $data)->setPaper('a4');

        $name = 'pharmacypos-report-'
            .Carbon::parse($data['from'])->format('Ymd').'-to-'
            .Carbon::parse($data['to'])->format('Ymd').'.pdf';

        return $pdf->download($name);
    }

    /**
     * Build the full report dataset for the given date range.
     */
    private function build(Request $request): array
    {
        $from = $request->date('from') ?? Carbon::today()->subDays(30);
        $to = $request->date('to') ?? Carbon::today();

        $from = Carbon::parse($from)->startOfDay();
        $to = Carbon::parse($to)->endOfDay();

        $sales = Sale::whereBetween('createdat', [$from, $to]);

        $totalsales = (clone $sales)->sum('total');
        $ordercount = (clone $sales)->count();
        $average = $ordercount > 0 ? $totalsales / $ordercount : 0;

        $byday = (clone $sales)
            ->select(DB::raw('DATE(createdat) as day'), DB::raw('SUM(total) as total'), DB::raw('COUNT(*) as orders'))
            ->groupBy('day')->orderBy('day')->get();

        $bymethod = (clone $sales)
            ->select('method', DB::raw('SUM(total) as total'), DB::raw('COUNT(*) as orders'))
            ->groupBy('method')->get();

        // Sales per staff member — accountability.
        $bystaff = (clone $sales)
            ->select('userid', DB::raw('SUM(total) as total'), DB::raw('COUNT(*) as orders'))
            ->groupBy('userid')
            ->with('user')
            ->get();

        $topproducts = SaleItem::whereHas('sale', function ($q) use ($from, $to) {
                $q->whereBetween('createdat', [$from, $to]);
            })
            ->select('name', DB::raw('SUM(quantity) as sold'), DB::raw('SUM(total) as earned'))
            ->groupBy('name')->orderByDesc('earned')->limit(10)->get();

        // Full transaction log: who sold what, when (newest first).
        $log = (clone $sales)->with(['user', 'items'])->latest('createdat')->limit(300)->get();

        return compact(
            'from', 'to', 'totalsales', 'ordercount', 'average',
            'byday', 'bymethod', 'bystaff', 'topproducts', 'log'
        );
    }
}
