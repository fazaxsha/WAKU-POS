<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TransactionDailyExport;
use App\Exports\TransactionMonthlyExport;

class ReportController extends Controller
{
    // ── Dashboard laporan ─────────────────────────────────
    public function index(): View
    {
        $today  = now()->format('Y-m-d');
        $month  = now()->format('Y-m');

        $todayRevenue      = Transaction::whereDate('transaction_date', today())->sum('total_amount');
        $monthRevenue      = Transaction::whereYear('transaction_date', now()->year)
                                ->whereMonth('transaction_date', now()->month)
                                ->sum('total_amount');
        $todayTransactions = Transaction::whereDate('transaction_date', today())->count();
        $monthTransactions = Transaction::whereYear('transaction_date', now()->year)
                                ->whereMonth('transaction_date', now()->month)
                                ->count();

        // Top 5 produk terlaris bulan ini
        $topProducts = TransactionItem::with('product')
            ->select('product_id', DB::raw('SUM(qty) as total_sold'), DB::raw('SUM(subtotal) as total_revenue'))
            ->whereHas('transaction', function ($q) {
                $q->whereYear('transaction_date', now()->year)
                  ->whereMonth('transaction_date', now()->month);
            })
            ->groupBy('product_id')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();

        // Penjualan per metode bayar bulan ini
        $byMethod = Transaction::select('payment_method', DB::raw('COUNT(*) as count'), DB::raw('SUM(total_amount) as total'))
            ->whereYear('transaction_date', now()->year)
            ->whereMonth('transaction_date', now()->month)
            ->groupBy('payment_method')
            ->get();

        // Chart pendapatan 30 hari
        $driver = DB::connection()->getDriverName();
        $dateExpr = match ($driver) {
            'pgsql'            => "TO_CHAR(transaction_date, 'YYYY-MM-DD')",
            'sqlite'           => "strftime('%Y-%m-%d', transaction_date)",
            'mysql', 'mariadb' => "DATE_FORMAT(transaction_date, '%Y-%m-%d')",
            default            => 'DATE(transaction_date)',
        };

        $chart30 = Transaction::select(
                DB::raw("$dateExpr as date"),
                DB::raw('SUM(total_amount) as total')
            )
            ->whereBetween('transaction_date', [now()->subDays(29)->startOfDay(), now()->endOfDay()])
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total', 'date');

        $chartLabels = [];
        $chartData   = [];
        for ($i = 29; $i >= 0; $i--) {
            $d = now()->subDays($i)->format('Y-m-d');
            $chartLabels[] = now()->subDays($i)->format('d/m');
            $chartData[]   = (float) ($chart30[$d] ?? 0);
        }

        return view('reports.index', compact(
            'todayRevenue', 'monthRevenue',
            'todayTransactions', 'monthTransactions',
            'topProducts', 'byMethod',
            'chartLabels', 'chartData',
            'today', 'month'
        ));
    }

    // ── Laporan harian ────────────────────────────────────
    public function daily(Request $request): View
    {
        $date  = $request->input('date', today()->toDateString());
        $start = Carbon::parse($date)->startOfDay();
        $end   = Carbon::parse($date)->endOfDay();

        $transactions = Transaction::with(['items.product', 'cashier'])
            ->whereBetween('transaction_date', [$start, $end])
            ->latest('transaction_date')
            ->get();

        $summary = [
            'revenue'    => $transactions->sum('total_amount'),
            'count'      => $transactions->count(),
            'items_sold' => $transactions->flatMap->items->sum('qty'),
            'by_method'  => $transactions->groupBy('payment_method')
                                ->map(fn($g) => $g->sum('total_amount')),
        ];

        return view('reports.daily', compact('transactions', 'summary', 'date'));
    }

    // ── Laporan bulanan ───────────────────────────────────
    public function monthly(Request $request): View
    {
        $month       = $request->input('month', now()->format('Y-m'));
        [$year, $m]  = explode('-', $month);

        $driver = DB::connection()->getDriverName();
        $dayExpr = match ($driver) {
            'pgsql'            => 'EXTRACT(DAY FROM transaction_date)::int',
            'sqlite'           => "CAST(strftime('%d', transaction_date) AS INTEGER)",
            'mysql', 'mariadb' => 'DAY(transaction_date)',
            default            => 'EXTRACT(DAY FROM transaction_date)',
        };

        $daily = Transaction::select(
                DB::raw("$dayExpr as day"),
                DB::raw('SUM(total_amount) as total'),
                DB::raw('COUNT(*) as count')
            )
            ->whereYear('transaction_date', $year)
            ->whereMonth('transaction_date', $m)
            ->groupBy('day')
            ->orderBy('day')
            ->get()
            ->keyBy('day');

        // Isi hari yang kosong
        $daysInMonth = Carbon::parse($month)->daysInMonth;
        $chartLabels = [];
        $chartData   = [];
        for ($d = 1; $d <= $daysInMonth; $d++) {
            $chartLabels[] = $d;
            $chartData[]   = (float) ($daily[$d]->total ?? 0);
        }

        $summary = [
            'revenue' => array_sum($chartData),
            'count'   => $daily->sum('count'),
        ];

        return view('reports.monthly', compact('daily', 'month', 'year', 'm', 'chartLabels', 'chartData', 'summary'));
    }

    // ── Laporan produk ────────────────────────────────────
    public function products(Request $request): View
    {
        $from = $request->input('from', now()->startOfMonth()->toDateString());
        $to   = $request->input('to',   now()->toDateString());

        $selling = TransactionItem::with('product.category')
            ->select(
                'product_id',
                DB::raw('SUM(qty) as total_qty'),
                DB::raw('SUM(subtotal) as total_revenue')
            )
            ->whereHas('transaction', fn($q) => $q->whereBetween('transaction_date', [$from, $to]))
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->get();

        $lowStock = Product::with('category')
            ->whereColumn('stock_qty', '<=', 'stock_min')
            ->where('is_active', true)
            ->orderBy('stock_qty')
            ->get();

        return view('reports.products', compact('selling', 'lowStock', 'from', 'to'));
    }

    // ── Export PDF harian ─────────────────────────────────
    public function exportPdf(Request $request)
    {
        $date  = $request->input('date', today()->toDateString());

        \App\Jobs\ExportDailyReportJob::dispatch($date, auth()->user());

        return back()->with('success', 'Laporan harian sedang diekspor di latar belakang. Anda akan menerima email saat proses selesai.');
    }

    // ── Export Excel bulanan ──────────────────────────────
    public function exportExcel(Request $request)
    {
        $month = $request->input('month', now()->format('Y-m'));

        \App\Jobs\ExportMonthlyReportJob::dispatch($month, auth()->user());

        return back()->with('success', 'Laporan bulanan sedang diekspor di latar belakang. Anda akan menerima email saat proses selesai.');
    }
}