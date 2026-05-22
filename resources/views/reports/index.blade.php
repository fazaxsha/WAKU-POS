@extends('layouts.app')
@section('title', 'Laporan')
@section('page-title', 'Laporan & Analitik')

@push('styles')
<style>
  .report-nav-btn {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 9px 18px; border-radius: 12px; font-size: 13.5px; font-weight: 600;
    border: 1.5px solid #E2E8F0; background: white; color: #475569;
    text-decoration: none; transition: all 0.18s ease;
  }
  .report-nav-btn:hover {
    border-color: #0D9488; background: #F0FDFA; color: #0F766E;
    transform: translateY(-1px); box-shadow: 0 4px 12px rgba(99,102,241,0.15);
  }
  .report-nav-btn.active {
    background: #0F172A; border-color: #0F172A; color: white;
  }
  .report-nav-btn .nav-icon {
    width: 28px; height: 28px; border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    font-size: 13px; background: rgba(255,255,255,0.15);
  }
  .report-nav-btn:not(.active) .nav-icon { background: #F1F5F9; }

  /* Rank badges */
  .rank-badge {
    width: 26px; height: 26px; border-radius: 50%;
    display: inline-flex; align-items: center; justify-content: center;
    font-size: 11px; font-weight: 800; flex-shrink: 0;
  }
  .rank-1 { background: #FEF3C7; color: #D97706; }
  .rank-2 { background: #F1F5F9; color: #64748B; }
  .rank-3 { background: #FEF2F2; color: #DC2626; }
  .rank-n { background: #F8FAFC; color: #94A3B8; }

  /* Export card */
  .export-option {
    display: flex; align-items: center; gap: 14px;
    padding: 14px 16px; border-radius: 12px; border: 1.5px solid #F1F5F9;
    background: #FAFBFF; transition: all 0.18s;
  }
  .export-option:hover { border-color: #C7D2FE; background: #F0FDFA; }
  .export-icon {
    width: 40px; height: 40px; border-radius: 10px; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center; font-size: 18px;
  }
</style>
@endpush

@section('content')

{{-- Header --}}
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
  <div>
    <h2 class="text-xl font-bold text-slate-900 mb-1">Ringkasan Laporan</h2>
    <p class="text-sm text-slate-500">{{ now()->translatedFormat('l, d F Y') }}</p>
  </div>
  <div class="flex flex-wrap gap-2">
    <a href="{{ route('reports.index') }}" class="report-nav-btn active">
      <span class="nav-icon"><i class="bi bi-grid"></i></span> Overview
    </a>
    <a href="{{ route('reports.daily') }}" class="report-nav-btn">
      <span class="nav-icon"><i class="bi bi-calendar-day"></i></span> Harian
    </a>
    <a href="{{ route('reports.monthly') }}" class="report-nav-btn">
      <span class="nav-icon"><i class="bi bi-calendar-month"></i></span> Bulanan
    </a>
    <a href="{{ route('reports.products') }}" class="report-nav-btn">
      <span class="nav-icon"><i class="bi bi-box-seam"></i></span> Produk
    </a>
  </div>
</div>

{{-- KPI Cards --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
  <div class="rounded-2xl p-5 overflow-hidden relative" style="background:linear-gradient(135deg,#0D9488,#0F766E);color:white;">
    <div class="absolute right-[-12px] top-[-12px] text-[72px] opacity-[0.08] leading-none pointer-events-none"><i class="bi bi-cash-coin"></i></div>
    <div class="text-[11px] font-semibold uppercase tracking-wider opacity-75 mb-2">Pendapatan Hari Ini</div>
    <div class="text-2xl font-extrabold tracking-tight mb-1">Rp {{ number_format($todayRevenue, 0, ',', '.') }}</div>
    <div class="text-[11px] opacity-60 flex items-center gap-1"><i class="bi bi-calendar3"></i> {{ now()->format('d M Y') }}</div>
  </div>
  <div class="rounded-2xl p-5 overflow-hidden relative" style="background:linear-gradient(135deg,#22C55E,#16A34A);color:white;">
    <div class="absolute right-[-12px] top-[-12px] text-[72px] opacity-[0.08] leading-none pointer-events-none"><i class="bi bi-receipt-cutoff"></i></div>
    <div class="text-[11px] font-semibold uppercase tracking-wider opacity-75 mb-2">Transaksi Hari Ini</div>
    <div class="text-2xl font-extrabold tracking-tight mb-1">{{ $todayTransactions }}</div>
    <div class="text-[11px] opacity-60 flex items-center gap-1"><i class="bi bi-arrow-up-circle"></i> transaksi masuk</div>
  </div>
  <div class="rounded-2xl p-5 overflow-hidden relative" style="background:linear-gradient(135deg,#3B82F6,#2563EB);color:white;">
    <div class="absolute right-[-12px] top-[-12px] text-[72px] opacity-[0.08] leading-none pointer-events-none"><i class="bi bi-calendar-month"></i></div>
    <div class="text-[11px] font-semibold uppercase tracking-wider opacity-75 mb-2">Pendapatan Bulan Ini</div>
    <div class="text-2xl font-extrabold tracking-tight mb-1">Rp {{ number_format($monthRevenue, 0, ',', '.') }}</div>
    <div class="text-[11px] opacity-60 flex items-center gap-1"><i class="bi bi-calendar3"></i> {{ now()->format('M Y') }}</div>
  </div>
  <div class="rounded-2xl p-5 overflow-hidden relative" style="background:linear-gradient(135deg,#14B8A6,#0D9488);color:white;">
    <div class="absolute right-[-12px] top-[-12px] text-[72px] opacity-[0.08] leading-none pointer-events-none"><i class="bi bi-graph-up"></i></div>
    <div class="text-[11px] font-semibold uppercase tracking-wider opacity-75 mb-2">Transaksi Bulan Ini</div>
    <div class="text-2xl font-extrabold tracking-tight mb-1">{{ $monthTransactions }}</div>
    <div class="text-[11px] opacity-60 flex items-center gap-1"><i class="bi bi-receipt"></i> total transaksi</div>
  </div>
</div>

{{-- Chart + Payment Method --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-5">

  {{-- Trend Chart --}}
  <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
      <div class="flex items-center gap-2.5">
        <span class="w-7 h-7 rounded-lg flex items-center justify-center text-xs" style="background:#F0FDFA;color:#0F766E;">
          <i class="bi bi-graph-up-arrow"></i>
        </span>
        <span class="text-sm font-bold text-slate-900">Tren Pendapatan 30 Hari Terakhir</span>
      </div>
      @can('report.export')
      <a href="{{ route('reports.export.pdf', ['date' => $today]) }}"
         class="inline-flex items-center gap-1.5 text-xs font-semibold text-red-600 bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-lg transition-colors">
        <i class="bi bi-file-pdf"></i> Export PDF
      </a>
      @endcan
    </div>
    <div class="p-5">
      <canvas id="chart30" height="90"></canvas>
    </div>
  </div>

  {{-- Payment Method Donut --}}
  <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
      <div class="flex items-center gap-2.5">
        <span class="w-7 h-7 rounded-lg flex items-center justify-center text-xs" style="background:#F0FDF4;color:#16A34A;">
          <i class="bi bi-pie-chart"></i>
        </span>
        <span class="text-sm font-bold text-slate-900">Metode Pembayaran</span>
      </div>
      <span class="text-xs text-slate-400 font-mono">Bulan ini</span>
    </div>
    <div class="p-5">
      <canvas id="methodChart" height="180"></canvas>
    </div>
  </div>
</div>

{{-- Top Products + Export --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

  {{-- Top Products --}}
  <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
      <div class="flex items-center gap-2.5">
        <span class="w-7 h-7 rounded-lg flex items-center justify-center text-xs" style="background:#FFFBEB;color:#D97706;">
          <i class="bi bi-trophy"></i>
        </span>
        <span class="text-sm font-bold text-slate-900">Produk Terlaris Bulan Ini</span>
      </div>
      <a href="{{ route('reports.products') }}"
         class="text-xs font-semibold text-teal-600 hover:text-teal-800 transition-colors">
        Lihat semua →
      </a>
    </div>
    <div class="overflow-x-auto">
      <table style="width:100%;border-collapse:collapse;">
        <thead>
          <tr style="background:#F8FAFC;border-bottom:1.5px solid #F1F5F9;">
            <th style="padding:10px 20px;text-align:left;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:#94A3B8;">#</th>
            <th style="padding:10px 20px;text-align:left;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:#94A3B8;">Produk</th>
            <th style="padding:10px 20px;text-align:right;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:#94A3B8;">Qty Terjual</th>
            <th style="padding:10px 20px;text-align:right;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:#94A3B8;">Revenue</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($topProducts as $i => $item)
          <tr style="border-bottom:1px solid #F8FAFC;" onmouseover="this.style.background='#FAFBFF'" onmouseout="this.style.background=''">
            <td style="padding:12px 20px;vertical-align:middle;">
              <span class="rank-badge {{ $i === 0 ? 'rank-1' : ($i === 1 ? 'rank-2' : ($i === 2 ? 'rank-3' : 'rank-n')) }}">
                {{ $i + 1 }}
              </span>
            </td>
            <td style="padding:12px 20px;vertical-align:middle;">
              <div style="font-size:13.5px;font-weight:600;color:#1E293B;">{{ $item->product->name ?? '--' }}</div>
              <div style="font-size:11px;color:#94A3B8;font-family:'DM Mono',monospace;">{{ $item->product->sku ?? '' }}</div>
            </td>
            <td style="padding:12px 20px;text-align:right;font-weight:700;color:#0F172A;vertical-align:middle;">
              {{ number_format($item->total_sold) }}
              <span style="font-size:11px;color:#94A3B8;font-weight:400;"> unit</span>
            </td>
            <td style="padding:12px 20px;text-align:right;vertical-align:middle;">
              <span style="font-size:13px;font-weight:600;color:#0D9488;">Rp {{ number_format($item->total_revenue, 0, ',', '.') }}</span>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="4" style="padding:48px 20px;text-align:center;color:#CBD5E1;">
              <div style="font-size:32px;margin-bottom:8px;"><i class="bi bi-box-seam"></i></div>
              <div style="font-size:13px;font-weight:500;">Belum ada data penjualan bulan ini</div>
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  {{-- Export Panel --}}
  <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-100">
      <div class="flex items-center gap-2.5">
        <span class="w-7 h-7 rounded-lg flex items-center justify-center text-xs" style="background:#F0FDFA;color:#0D9488;">
          <i class="bi bi-download"></i>
        </span>
        <span class="text-sm font-bold text-slate-900">Export Laporan</span>
      </div>
    </div>
    <div class="p-5 space-y-4">
      @can('report.export')
      {{-- PDF Daily --}}
      <div>
        <label class="block text-xs font-semibold text-slate-600 mb-2">
          <i class="bi bi-file-pdf text-red-500 mr-1"></i> Laporan Harian (PDF)
        </label>
        <form method="GET" action="{{ route('reports.export.pdf') }}" class="flex gap-2">
          <input type="date" name="date" value="{{ $today }}"
            class="flex-1 h-9 px-3 text-sm text-slate-700 border border-slate-200 rounded-lg bg-white
                   focus:outline-none focus:border-teal-400 focus:ring-2 focus:ring-teal-100 transition-all">
          <button type="submit"
            class="h-9 px-4 text-xs font-bold text-white bg-red-600 hover:bg-red-700 rounded-lg flex items-center gap-1.5 transition-colors whitespace-nowrap shadow-sm">
            <i class="bi bi-file-pdf"></i> PDF
          </button>
        </form>
      </div>

      <div class="border-t border-slate-100"></div>

      {{-- Excel Monthly --}}
      <div>
        <label class="block text-xs font-semibold text-slate-600 mb-2">
          <i class="bi bi-file-excel text-emerald-600 mr-1"></i> Laporan Bulanan (Excel)
        </label>
        <form method="GET" action="{{ route('reports.export.excel') }}" class="flex gap-2">
          <input type="month" name="month" value="{{ $month }}"
            class="flex-1 h-9 px-3 text-sm text-slate-700 border border-slate-200 rounded-lg bg-white
                   focus:outline-none focus:border-teal-400 focus:ring-2 focus:ring-teal-100 transition-all">
          <button type="submit"
            class="h-9 px-4 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg flex items-center gap-1.5 transition-colors whitespace-nowrap shadow-sm">
            <i class="bi bi-file-excel"></i> Excel
          </button>
        </form>
      </div>
      @else
      <div class="py-8 text-center text-slate-400">
        <i class="bi bi-lock text-3xl block mb-3 text-slate-300"></i>
        <p class="text-sm">Anda tidak memiliki akses untuk export laporan.</p>
      </div>
      @endcan
    </div>
  </div>

</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function() {
  // 30-day trend chart
  const ctx30 = document.getElementById('chart30');
  if (ctx30) {
    const grad = ctx30.getContext('2d').createLinearGradient(0, 0, 0, 200);
    grad.addColorStop(0, 'rgba(99,102,241,0.2)');
    grad.addColorStop(1, 'rgba(99,102,241,0.01)');
    new Chart(ctx30, {
      type: 'line',
      data: {
        labels: {!! json_encode($chartLabels) !!},
        datasets: [{
          data: {!! json_encode($chartData) !!},
          borderColor: '#0D9488', backgroundColor: grad,
          borderWidth: 2.5, fill: true, tension: 0.4,
          pointRadius: 0, pointHoverRadius: 5,
          pointBackgroundColor: '#0D9488', pointBorderColor: '#fff', pointBorderWidth: 2,
        }]
      },
      options: {
        responsive: true,
        interaction: { mode: 'index', intersect: false },
        plugins: {
          legend: { display: false },
          tooltip: {
            backgroundColor: '#0F172A', titleColor: '#94A3B8', bodyColor: '#F8FAFC',
            padding: 12, cornerRadius: 10,
            callbacks: { label: c => ' Rp ' + c.raw.toLocaleString('id-ID') }
          }
        },
        scales: {
          x: { grid: { display: false }, border: { display: false },
               ticks: { font: { size: 10, family: "'Plus Jakarta Sans'" }, color: '#94A3B8', maxTicksLimit: 10 } },
          y: { grid: { color: 'rgba(0,0,0,0.04)' }, border: { display: false },
               ticks: { font: { size: 10, family: "'DM Mono'" }, color: '#94A3B8',
                        callback: v => v >= 1000000 ? 'Rp '+(v/1000000).toFixed(1)+'jt' : 'Rp '+(v/1000).toFixed(0)+'rb' } }
        }
      }
    });
  }

  // Payment method doughnut
  const ctxM = document.getElementById('methodChart');
  if (ctxM) {
    new Chart(ctxM, {
      type: 'doughnut',
      data: {
        labels: {!! json_encode($byMethod->pluck('payment_method')->map(fn($m) => strtoupper($m))->values()) !!},
        datasets: [{
          data: {!! json_encode($byMethod->pluck('total')->values()) !!},
          backgroundColor: ['#0D9488', '#22C55E', '#14B8A6'],
          borderWidth: 3, borderColor: '#fff',
          hoverBorderColor: '#fff', hoverOffset: 4,
        }]
      },
      options: {
        responsive: true, cutout: '68%',
        plugins: {
          legend: { position: 'bottom', labels: { font: { size: 12, family: "'Plus Jakarta Sans'" }, padding: 16, usePointStyle: true, pointStyleWidth: 8 } },
          tooltip: {
            backgroundColor: '#0F172A', titleColor: '#94A3B8', bodyColor: '#F8FAFC',
            padding: 10, cornerRadius: 8,
            callbacks: { label: c => ' Rp ' + c.raw.toLocaleString('id-ID') }
          }
        }
      }
    });
  }
})();
</script>
@endpush
