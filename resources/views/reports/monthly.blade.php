@extends('layouts.app')
@section('title', 'Laporan Bulanan')
@section('page-title', 'Laporan Bulanan')

@push('styles')
<style>
.report-nav-btn {
  display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border-radius:10px;
  font-size:13px;font-weight:600;border:1.5px solid #E2E8F0;background:white;
  color:#475569;text-decoration:none;transition:all .18s ease;
}
.report-nav-btn:hover { border-color:#0D9488;background:#F0FDFA;color:#0F766E; }
.report-nav-btn.active { background:#0F172A;border-color:#0F172A;color:white; }
</style>
@endpush

@section('content')

{{-- Header --}}
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
  <div class="flex items-center gap-3">
    <a href="{{ route('reports.index') }}" class="topbar-btn" title="Kembali">
      <i class="bi bi-arrow-left text-sm"></i>
    </a>
    <div>
      <h2 class="text-xl font-bold text-slate-900 mb-0.5">Laporan Bulanan</h2>
      <p class="text-sm text-slate-500">{{ \Carbon\Carbon::parse($month)->translatedFormat('F Y') }}</p>
    </div>
  </div>
  <div class="flex flex-wrap gap-2">
    <a href="{{ route('reports.daily') }}" class="report-nav-btn">
      <i class="bi bi-calendar-day"></i> Harian
    </a>
    <a href="{{ route('reports.monthly') }}" class="report-nav-btn active">
      <i class="bi bi-calendar-month"></i> Bulanan
    </a>
    <a href="{{ route('reports.products') }}" class="report-nav-btn">
      <i class="bi bi-box-seam"></i> Produk
    </a>
  </div>
</div>

{{-- Filter Bar --}}
<div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 mb-6">
  <form method="GET" action="{{ route('reports.monthly') }}" class="flex flex-wrap gap-3 items-end">
    <div>
      <label class="block text-xs font-semibold text-slate-600 mb-1.5">Pilih Bulan</label>
      <input type="month" name="month" value="{{ $month }}"
        class="h-9 px-3 text-sm text-slate-700 border border-slate-200 rounded-lg bg-white
               focus:outline-none focus:border-teal-400 focus:ring-2 focus:ring-teal-100 transition-all">
    </div>
    <button type="submit" class="btn-primary">
      <i class="bi bi-search"></i> Tampilkan
    </button>
    @can('report.export')
    <a href="{{ route('reports.export.excel', ['month' => $month]) }}"
       class="h-9 px-4 inline-flex items-center gap-2 text-sm font-semibold text-emerald-700
              bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 rounded-xl transition-colors">
      <i class="bi bi-file-excel"></i> Export Excel
    </a>
    @endcan
  </form>
</div>

{{-- KPI Summary --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
  <div class="rounded-2xl p-5 relative overflow-hidden" style="background:linear-gradient(135deg,#0D9488,#0F766E);color:white;">
    <div class="absolute right-[-12px] top-[-12px] text-[72px] opacity-[0.08] leading-none pointer-events-none"><i class="bi bi-cash-coin"></i></div>
    <div class="text-[11px] font-semibold uppercase tracking-wider opacity-75 mb-2">Total Pendapatan</div>
    <div class="text-2xl font-extrabold tracking-tight">Rp {{ number_format($summary['revenue'], 0, ',', '.') }}</div>
    <div class="text-[11px] opacity-60 mt-1 flex items-center gap-1"><i class="bi bi-calendar3"></i> {{ \Carbon\Carbon::parse($month)->translatedFormat('F Y') }}</div>
  </div>
  <div class="rounded-2xl p-5 relative overflow-hidden" style="background:linear-gradient(135deg,#22C55E,#16A34A);color:white;">
    <div class="absolute right-[-12px] top-[-12px] text-[72px] opacity-[0.08] leading-none pointer-events-none"><i class="bi bi-receipt"></i></div>
    <div class="text-[11px] font-semibold uppercase tracking-wider opacity-75 mb-2">Total Transaksi</div>
    <div class="text-2xl font-extrabold tracking-tight">{{ $summary['count'] }}</div>
    <div class="text-[11px] opacity-60 mt-1">transaksi tercatat</div>
  </div>
  <div class="rounded-2xl p-5 relative overflow-hidden" style="background:linear-gradient(135deg,#14B8A6,#0D9488);color:white;">
    <div class="absolute right-[-12px] top-[-12px] text-[72px] opacity-[0.08] leading-none pointer-events-none"><i class="bi bi-graph-up"></i></div>
    <div class="text-[11px] font-semibold uppercase tracking-wider opacity-75 mb-2">Rata-rata per Transaksi</div>
    <div class="text-2xl font-extrabold tracking-tight">
      Rp {{ $summary['count'] > 0 ? number_format($summary['revenue'] / $summary['count'], 0, ',', '.') : 0 }}
    </div>
    <div class="text-[11px] opacity-60 mt-1">avg. ticket size</div>
  </div>
</div>

{{-- Monthly Chart --}}
<div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
  <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
    <div class="flex items-center gap-2.5">
      <span class="w-7 h-7 rounded-lg flex items-center justify-center text-xs" style="background:#F0FDFA;color:#0F766E;">
        <i class="bi bi-bar-chart"></i>
      </span>
      <span class="text-sm font-bold text-slate-900">
        Pendapatan Harian -- {{ \Carbon\Carbon::parse($month)->translatedFormat('F Y') }}
      </span>
    </div>
  </div>
  <div class="p-5">
    <canvas id="monthlyChart" height="80"></canvas>
  </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function() {
  const ctx = document.getElementById('monthlyChart');
  if (!ctx) return;
  new Chart(ctx, {
    type: 'bar',
    data: {
      labels: {!! json_encode($chartLabels) !!},
      datasets: [{
        label: 'Pendapatan',
        data: {!! json_encode($chartData) !!},
        backgroundColor: 'rgba(99,102,241,0.12)',
        borderColor: '#0D9488',
        borderWidth: 1.5,
        borderRadius: 6,
        borderSkipped: false,
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
             ticks: { font: { size: 11, family: "'Plus Jakarta Sans'" }, color: '#94A3B8' } },
        y: { grid: { color: 'rgba(0,0,0,0.04)' }, border: { display: false },
             ticks: { font: { size: 11, family: "'DM Mono'" }, color: '#94A3B8',
                      callback: v => v >= 1000000 ? 'Rp '+(v/1000000).toFixed(1)+'jt' : 'Rp '+(v/1000).toFixed(0)+'rb' } }
      }
    }
  });
})();
</script>
@endpush
