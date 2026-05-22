<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(): View
    {
        // Pendapatan & transaksi hari ini
        $todayRevenue = Transaction::whereDate('transaction_date', today())
            ->sum('total_amount');

        $todayTransactions = Transaction::whereDate('transaction_date', today())
            ->count();

        // Total produk aktif
        $totalProducts = Product::where('is_active', true)->count();

        // Produk stok kritis
        $lowStockCount = Product::whereColumn('stock_qty', '<=', 'stock_min')
            ->where('is_active', true)
            ->count();

        $lowStockProducts = Product::with('category')
            ->whereColumn('stock_qty', '<=', 'stock_min')
            ->where('is_active', true)
            ->orderBy('stock_qty')
            ->limit(6)
            ->get();

        // Transaksi terakhir (10)
        $recentTransactions = Transaction::with('cashier')
            ->latest('transaction_date')
            ->limit(10)
            ->get();

        // Data chart 7 hari terakhir
        $driver = DB::connection()->getDriverName();
        $dateExpr = match ($driver) {
            'pgsql' => "TO_CHAR(transaction_date, 'YYYY-MM-DD')",
            'sqlite' => "strftime('%Y-%m-%d', transaction_date)",
            'mysql', 'mariadb' => "DATE_FORMAT(transaction_date, '%Y-%m-%d')",
            default => "DATE(transaction_date)",
        };

        $chartRaw = Transaction::select(
                DB::raw("$dateExpr as date"),
                DB::raw('SUM(total_amount) as total')
            )
            ->whereBetween('transaction_date', [
                now()->subDays(6)->startOfDay(),
                now()->endOfDay(),
            ])
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total', 'date');

        // Isi tanggal yang kosong dengan 0
        $chartLabels = [];
        $chartData   = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $chartLabels[] = now()->subDays($i)->translatedFormat('d M');
            $chartData[]   = (float) ($chartRaw[$date] ?? 0);
        }

        return view('dashboard.dashboard-index', compact(
            'todayRevenue',
            'todayTransactions',
            'totalProducts',
            'lowStockCount',
            'lowStockProducts',
            'recentTransactions',
            'chartLabels',
            'chartData',
        ));
    }
}
