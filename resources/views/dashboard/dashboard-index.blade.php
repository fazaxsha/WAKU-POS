@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@push('styles')
<style>
  [x-cloak] { display: none !important; }

  /* ── Gradient Stat Cards ─────────────────────── */
  .kpi-card {
    position: relative;
    border-radius: 20px;
    padding: 22px 22px 18px;
    overflow: hidden;
    box-shadow: 0 4px 24px rgba(0,0,0,0.07);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
  }
  .kpi-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 32px rgba(0,0,0,0.12);
  }
  .kpi-card .kpi-bg-icon {
    position: absolute;
    right: -10px;
    top: -10px;
    font-size: 80px;
    opacity: 0.08;
    line-height: 1;
    pointer-events: none;
  }
  .kpi-card .kpi-label {
    font-size: 11px;
    font-weight: 600;
    letter-spacing: 0.07em;
    text-transform: uppercase;
    opacity: 0.75;
    margin-bottom: 6px;
  }
  .kpi-card .kpi-value {
    font-size: 24px;
    font-weight: 800;
    line-height: 1.1;
    letter-spacing: -0.03em;
    margin-bottom: 10px;
  }
  .kpi-card .kpi-sub {
    font-size: 11px;
    opacity: 0.65;
    display: flex;
    align-items: center;
    gap: 4px;
  }

  /* Card color themes */
  .kpi-teal   { background: linear-gradient(135deg, #0D9488 0%, #0F766E 100%); color: white; }
  .kpi-green  { background: linear-gradient(135deg, #22C55E 0%, #16A34A 100%); color: white; }
  .kpi-amber  { background: linear-gradient(135deg, #F59E0B 0%, #D97706 100%); color: white; }
  .kpi-red    { background: linear-gradient(135deg, #EF4444 0%, #DC2626 100%); color: white; }

  /* ── Chart Card ──────────────────────────────── */
  .section-card {
    background: white;
    border: 1px solid #F1F5F9;
    border-radius: 20px;
    box-shadow: 0 2px 16px rgba(0,0,0,0.05);
    overflow: hidden;
  }
  .section-card-header {
    padding: 18px 22px 16px;
    border-bottom: 1px solid #F8FAFC;
    display: flex;
    align-items: center;
    justify-content: space-between;
  }
  .section-card-title {
    font-size: 14px;
    font-weight: 700;
    color: #0F172A;
    display: flex;
    align-items: center;
    gap: 8px;
  }
  .section-card-title .title-icon {
    width: 28px;
    height: 28px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
  }

  /* ── Quick Action Buttons ────────────────────── */
  .quick-action {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    padding: 16px 12px;
    border-radius: 14px;
    border: 1.5px solid #E2E8F0;
    background: white;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.2s ease;
    color: #475569;
  }
  .quick-action:hover {
    border-color: #0D9488;
    background: #F0FDFA;
    color: #0F766E;
    transform: translateY(-2px);
    box-shadow: 0 4px 16px rgba(13,148,136,0.15);
  }
  .quick-action .qa-icon {
    width: 40px;
    height: 40px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    background: #F8FAFC;
    transition: background 0.2s;
  }
  .quick-action:hover .qa-icon {
    background: #99F6E4;
  }
  .quick-action .qa-label {
    font-size: 11px;
    font-weight: 600;
    text-align: center;
    line-height: 1.3;
  }

  /* ── Low Stock Items ─────────────────────────── */
  .stock-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 20px;
    border-bottom: 1px solid #F8FAFC;
    transition: background 0.15s;
  }
  .stock-item:last-child { border-bottom: none; }
  .stock-item:hover { background: #F8FAFC; }
  .stock-bar-wrap {
    height: 4px;
    background: #F1F5F9;
    border-radius: 99px;
    margin-top: 4px;
    overflow: hidden;
  }
  .stock-bar {
    height: 100%;
    border-radius: 99px;
    background: linear-gradient(90deg, #EF4444, #F97316);
    transition: width 0.6s ease;
  }

  /* ── Transaction Table ───────────────────────── */
  .trx-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    font-weight: 700;
    flex-shrink: 0;
    background: linear-gradient(135deg, #0D9488, #14B8A6);
    color: white;
  }
  .pay-pill {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 3px 10px;
    border-radius: 99px;
    font-size: 10.5px;
    font-weight: 600;
    font-family: 'DM Mono', monospace;
    letter-spacing: 0.04em;
  }
  .pay-cash     { background: #F0FDF4; color: #16A34A; }
  .pay-transfer { background: #EFF6FF; color: #2563EB; }
  .pay-qris     { background: #F5F3FF; color: #7C3AED; }

  /* ── Skeleton Shimmer ─────────────────────────── */
  .skeleton {
    background: linear-gradient(90deg, #F1F5F9 25%, #E2E8F0 50%, #F1F5F9 75%);
    background-size: 200% 100%;
    animation: shimmer 1.5s infinite ease-in-out;
    border-radius: 8px;
  }
  @keyframes shimmer {
    0%   { background-position: 200% 0; }
    100% { background-position: -200% 0; }
  }
</style>
@endpush

@section('content')

{{-- ── Greeting + Quick Stats Banner ────────────────── --}}
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
  <div>
    <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight mb-0.5">
      Halo, {{ auth()->user()->name }}! 👋
    </h2>
    <p class="text-sm text-slate-500">
      Berikut ringkasan performa toko Anda hari ini,
      <span class="font-semibold text-slate-700">{{ now()->translatedFormat('l, d F Y') }}</span>
    </p>
  </div>
  @can('pos.access')
  <a href="{{ route('pos.index') }}"
     class="inline-flex items-center gap-2.5 px-5 py-2.5 rounded-xl text-sm font-bold text-white
            bg-gradient-to-r from-teal-600 to-teal-700 hover:from-teal-700 hover:to-teal-800
            shadow-lg shadow-teal-600/30 transition-all hover:shadow-teal-600/50 hover:-translate-y-0.5">
    <i class="bi bi-receipt-cutoff text-base"></i> Buka Kasir POS
  </a>
  @endcan
</div>

{{-- ── KPI Cards ───────────────────────────────────── --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">

  {{-- Pendapatan --}}
  <div class="kpi-card kpi-teal">
    <div class="kpi-bg-icon"><i class="bi bi-wallet2"></i></div>
    <div class="kpi-label">Pendapatan Hari Ini</div>
    <div class="kpi-value">Rp {{ number_format($todayRevenue, 0, ',', '.') }}</div>
    <div class="kpi-sub"><i class="bi bi-calendar3"></i> {{ now()->format('d M Y') }}</div>
  </div>

  {{-- Transaksi --}}
  <div class="kpi-card kpi-green">
    <div class="kpi-bg-icon"><i class="bi bi-receipt-cutoff"></i></div>
    <div class="kpi-label">Transaksi Hari Ini</div>
    <div class="kpi-value">{{ $todayTransactions }}</div>
    <div class="kpi-sub"><i class="bi bi-arrow-up-circle"></i> transaksi masuk</div>
  </div>

  {{-- Total Produk --}}
  <div class="kpi-card kpi-amber">
    <div class="kpi-bg-icon"><i class="bi bi-box-seam"></i></div>
    <div class="kpi-label">Total Produk Aktif</div>
    <div class="kpi-value">{{ $totalProducts }}</div>
    <div class="kpi-sub"><i class="bi bi-check-circle"></i> item terdaftar</div>
  </div>

  {{-- Stok Kritis --}}
  <div class="kpi-card kpi-red">
    <div class="kpi-bg-icon"><i class="bi bi-exclamation-triangle"></i></div>
    <div class="kpi-label">Stok Kritis</div>
    <div class="kpi-value">{{ $lowStockCount }}</div>
    <div class="kpi-sub">
      @if ($lowStockCount > 0)
        <i class="bi bi-exclamation-circle"></i> perlu restock segera
      @else
        <i class="bi bi-check-circle"></i> semua aman
      @endif
    </div>
  </div>

</div>

{{-- ── Main Content Grid ───────────────────────────── --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-5">

  {{-- Revenue Chart ── takes 2/3 --}}
  <div class="lg:col-span-2 section-card">
    <div class="section-card-header">
      <div class="section-card-title">
        <span class="title-icon" style="background:#F0FDFA; color:#0F766E;">
          <i class="bi bi-graph-up-arrow"></i>
        </span>
        Grafik Pendapatan 7 Hari
      </div>
      <span class="text-xs font-mono text-slate-400">
        {{ now()->subDays(6)->format('d M') }} – {{ now()->format('d M Y') }}
      </span>
    </div>
    <div class="p-5 relative" x-data="{ chartLoaded: false }">
      <div x-show="!chartLoaded" class="skeleton h-[220px] w-full rounded-xl"></div>
      <canvas x-show="chartLoaded" x-cloak id="revenueChart" height="88"
              x-init="$nextTick(() => { setTimeout(() => chartLoaded = true, 300) })"></canvas>
    </div>
  </div>

  {{-- Quick Actions + Low Stock ── takes 1/3 --}}
  <div class="flex flex-col gap-4">

    {{-- Quick Actions --}}
    <div class="section-card">
      <div class="section-card-header">
        <div class="section-card-title">
          <span class="title-icon" style="background:#F0FDF4; color:#16A34A;">
            <i class="bi bi-lightning-charge"></i>
          </span>
          Akses Cepat
        </div>
      </div>
      <div class="p-4 grid grid-cols-3 gap-3">
        @can('pos.access')
        <a href="{{ route('pos.index') }}" class="quick-action">
          <div class="qa-icon"><i class="bi bi-receipt"></i></div>
          <span class="qa-label">Kasir POS</span>
        </a>
        @endcan
        @can('product.view')
        <a href="{{ route('products.index') }}" class="quick-action">
          <div class="qa-icon"><i class="bi bi-box-seam"></i></div>
          <span class="qa-label">Produk</span>
        </a>
        @endcan
        @can('report.view')
        <a href="{{ route('reports.index') }}" class="quick-action">
          <div class="qa-icon"><i class="bi bi-bar-chart-line"></i></div>
          <span class="qa-label">Laporan</span>
        </a>
        @endcan
        @can('purchase.create')
        <a href="{{ route('purchases.index') }}" class="quick-action">
          <div class="qa-icon"><i class="bi bi-cart-plus"></i></div>
          <span class="qa-label">Pembelian</span>
        </a>
        @endcan
        @can('product.view')
        <a href="{{ route('categories.index') }}" class="quick-action">
          <div class="qa-icon"><i class="bi bi-tag"></i></div>
          <span class="qa-label">Kategori</span>
        </a>
        @endcan
        @can('product.view')
        <a href="{{ route('stock-opnames.index') }}" class="quick-action">
          <div class="qa-icon"><i class="bi bi-clipboard-check"></i></div>
          <span class="qa-label">Stok Opname</span>
        </a>
        @endcan
      </div>
    </div>

    {{-- Low Stock Alert --}}
    <div class="section-card flex flex-col" style="max-height: 300px;">
      <div class="section-card-header flex-shrink-0">
        <div class="section-card-title">
          <span class="title-icon" style="background:#FEF2F2; color:#DC2626;">
            <i class="bi bi-exclamation-triangle"></i>
          </span>
          Stok Hampir Habis
        </div>
        @can('product.view')
        <a href="{{ route('products.index', ['low_stock' => 1]) }}"
           class="text-[11px] font-semibold text-teal-600 hover:text-teal-800 transition-colors">
          Lihat semua →
        </a>
        @endcan
      </div>
      <div class="overflow-y-auto flex-1" style="scrollbar-width: thin; scrollbar-color: #e2e8f0 transparent;">
        @forelse ($lowStockProducts as $product)
          @php
            $pct = $product->stock_min > 0
              ? min(100, round(($product->stock_qty / $product->stock_min) * 100))
              : 0;
          @endphp
          <div class="stock-item">
            <div class="w-8 h-8 rounded-lg bg-red-50 flex items-center justify-center text-sm flex-shrink-0">📦</div>
            <div class="flex-1 min-w-0">
              <div class="text-sm font-semibold text-slate-800 truncate">{{ $product->name }}</div>
              <div class="stock-bar-wrap">
                <div class="stock-bar" style="width: {{ $pct }}%"></div>
              </div>
            </div>
            <div class="text-right flex-shrink-0 ml-2">
              <div class="text-sm font-bold text-red-600">{{ $product->stock_qty }}</div>
              <div class="text-[10px] text-slate-400">/ {{ $product->stock_min }}</div>
            </div>
          </div>
        @empty
          <div class="flex flex-col items-center justify-center py-8 text-slate-400">
            <i class="bi bi-check-circle-fill text-3xl text-emerald-500 mb-2"></i>
            <span class="text-sm font-medium">Semua stok aman</span>
          </div>
        @endforelse
      </div>
    </div>

  </div>
</div>

{{-- ── Recent Transactions Table ───────────────────── --}}
<div class="section-card">
  <div class="section-card-header">
    <div class="section-card-title">
      <span class="title-icon" style="background:#F0FDFA; color:#0D9488;">
        <i class="bi bi-clock-history"></i>
      </span>
      Transaksi Terakhir
    </div>
    <div class="flex items-center gap-3">
      <span class="hidden sm:inline text-[11px] font-mono text-slate-400">10 transaksi terbaru</span>
      @can('report.view')
      <a href="{{ route('reports.daily') }}"
         class="inline-flex items-center gap-1 text-xs font-semibold text-teal-600 hover:text-teal-800 transition-colors">
        Laporan lengkap <i class="bi bi-arrow-right"></i>
      </a>
      @endcan
    </div>
  </div>
  <div class="overflow-x-auto">
    <table class="table">
      <thead>
        <tr>
          <th>Kasir</th>
          <th>Invoice</th>
          <th>Total</th>
          <th>Metode</th>
          <th>Waktu</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($recentTransactions as $trx)
        <tr>
          <td>
            <div class="flex items-center gap-2.5">
              <div class="trx-avatar">{{ strtoupper(substr($trx->cashier->name ?? 'U', 0, 1)) }}</div>
              <span class="font-medium text-slate-800 text-[13px]">{{ $trx->cashier->name ?? '—' }}</span>
            </div>
          </td>
          <td>
            <span class="font-mono text-[11.5px] text-teal-600 bg-teal-50 px-2 py-0.5 rounded-md">
              {{ $trx->invoice_no }}
            </span>
          </td>
          <td class="font-bold text-slate-900 text-sm">
            Rp {{ number_format($trx->total_amount, 0, ',', '.') }}
          </td>
          <td>
            @if($trx->payment_method === 'cash')
              <span class="pay-pill pay-cash"><i class="bi bi-cash"></i> Cash</span>
            @elseif($trx->payment_method === 'transfer')
              <span class="pay-pill pay-transfer"><i class="bi bi-bank"></i> Transfer</span>
            @else
              <span class="pay-pill pay-qris"><i class="bi bi-qr-code"></i> QRIS</span>
            @endif
          </td>
          <td class="text-slate-400 text-xs font-mono">
            {{ $trx->transaction_date->format('d M, H:i') }}
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="5" class="!py-12 text-center">
            <div class="text-slate-300 text-4xl mb-2.5"><i class="bi bi-receipt"></i></div>
            <div class="text-[13px] text-slate-400 font-medium">Belum ada transaksi hari ini</div>
            <div class="text-xs text-slate-300 mt-1">Transaksi akan muncul di sini setelah kasir memproses pembayaran</div>
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function() {
  const ctx = document.getElementById('revenueChart');
  if (!ctx) return;

  const labels = {!! json_encode($chartLabels) !!};
  const data   = {!! json_encode($chartData) !!};

  // Gradient fill
  const gradient = ctx.getContext('2d').createLinearGradient(0, 0, 0, 220);
  gradient.addColorStop(0,   'rgba(13,148,136,0.25)');
  gradient.addColorStop(1,   'rgba(13,148,136,0.02)');

  new Chart(ctx, {
    type: 'bar',
    data: {
      labels,
      datasets: [
        {
          type: 'line',
          label: 'Tren',
          data,
          borderColor: '#0D9488',
          borderWidth: 2.5,
          tension: 0.4,
          pointBackgroundColor: '#0D9488',
          pointBorderColor: '#fff',
          pointBorderWidth: 2,
          pointRadius: 4,
          pointHoverRadius: 6,
          fill: true,
          backgroundColor: gradient,
          order: 1,
        },
        {
          type: 'bar',
          label: 'Pendapatan',
          data,
          backgroundColor: 'rgba(13,148,136,0.1)',
          borderColor: 'rgba(13,148,136,0.3)',
          borderWidth: 1,
          borderRadius: 6,
          borderSkipped: false,
          order: 2,
        }
      ]
    },
    options: {
      responsive: true,
      interaction: { mode: 'index', intersect: false },
      plugins: {
        legend: { display: false },
        tooltip: {
          backgroundColor: '#0F172A',
          titleColor: '#94A3B8',
          bodyColor: '#F8FAFC',
          padding: 12,
          cornerRadius: 10,
          callbacks: {
            title: (items) => items[0].label,
            label: (ctx) => {
              if (ctx.datasetIndex !== 0) return null;
              return ' Rp ' + ctx.raw.toLocaleString('id-ID');
            }
          }
        }
      },
      scales: {
        x: {
          grid: { display: false },
          border: { display: false },
          ticks: { font: { size: 11, family: "'Plus Jakarta Sans', sans-serif" }, color: '#94A3B8' }
        },
        y: {
          grid: { color: 'rgba(0,0,0,0.04)', drawBorder: false },
          border: { display: false, dash: [4,4] },
          ticks: {
            font: { size: 11, family: "'DM Mono', monospace" },
            color: '#94A3B8',
            callback: v => {
              if (v === 0) return 'Rp 0';
              if (v >= 1000000) return 'Rp ' + (v/1000000).toFixed(1) + 'jt';
              if (v >= 1000)    return 'Rp ' + (v/1000).toFixed(0) + 'rb';
              return 'Rp ' + v;
            }
          }
        }
      }
    }
  });
})();
</script>
@endpush