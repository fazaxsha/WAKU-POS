@extends('layouts.app')
@section('title', 'Laporan Produk')
@section('page-title', 'Laporan Performa Produk')

@push('styles')
<style>
.report-nav-btn {
  display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border-radius:10px;
  font-size:13px;font-weight:600;border:1.5px solid #E2E8F0;background:white;
  color:#475569;text-decoration:none;transition:all .18s ease;
}
.report-nav-btn:hover { border-color:#0D9488;background:#F0FDFA;color:#0F766E; }
.report-nav-btn.active { background:#0F172A;border-color:#0F172A;color:white; }
.stock-progress { height:4px;background:#FEE2E2;border-radius:99px;overflow:hidden;margin-top:4px; }
.stock-progress-bar { height:100%;border-radius:99px;background:linear-gradient(90deg,#EF4444,#F97316);transition:width .6s ease; }
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
      <h2 class="text-xl font-bold text-slate-900 mb-0.5">Laporan Performa Produk</h2>
      <p class="text-sm text-slate-500">{{ $from }} -- {{ $to }}</p>
    </div>
  </div>
  <div class="flex flex-wrap gap-2">
    <a href="{{ route('reports.daily') }}" class="report-nav-btn">
      <i class="bi bi-calendar-day"></i> Harian
    </a>
    <a href="{{ route('reports.monthly') }}" class="report-nav-btn">
      <i class="bi bi-calendar-month"></i> Bulanan
    </a>
    <a href="{{ route('reports.products') }}" class="report-nav-btn active">
      <i class="bi bi-box-seam"></i> Produk
    </a>
  </div>
</div>

{{-- Date Range Filter --}}
<div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 mb-6">
  <form method="GET" action="{{ route('reports.products') }}" class="flex flex-wrap gap-3 items-end">
    <div>
      <label class="block text-xs font-semibold text-slate-600 mb-1.5">Dari Tanggal</label>
      <input type="date" name="from" value="{{ $from }}"
        class="h-9 px-3 text-sm text-slate-700 border border-slate-200 rounded-lg bg-white
               focus:outline-none focus:border-teal-400 focus:ring-2 focus:ring-teal-100 transition-all">
    </div>
    <div>
      <label class="block text-xs font-semibold text-slate-600 mb-1.5">Sampai Tanggal</label>
      <input type="date" name="to" value="{{ $to }}"
        class="h-9 px-3 text-sm text-slate-700 border border-slate-200 rounded-lg bg-white
               focus:outline-none focus:border-teal-400 focus:ring-2 focus:ring-teal-100 transition-all">
    </div>
    <button type="submit" class="btn-primary">
      <i class="bi bi-funnel"></i> Filter
    </button>
  </form>
</div>

{{-- Main Grid --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

  {{-- Best Sellers Table --}}
  <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
      <div class="flex items-center gap-2.5">
        <span class="w-7 h-7 rounded-lg flex items-center justify-center text-xs" style="background:#FFFBEB;color:#D97706;">
          <i class="bi bi-trophy"></i>
        </span>
        <span class="text-sm font-bold text-slate-900">Produk Terlaris</span>
      </div>
      <span class="text-xs font-mono text-slate-400">{{ $from }} -- {{ $to }}</span>
    </div>
    <div class="overflow-x-auto">
      <table style="width:100%;border-collapse:collapse;font-size:13.5px;">
        <thead>
          <tr style="background:#F8FAFC;border-bottom:1.5px solid #F1F5F9;">
            <th style="padding:10px 18px;text-align:left;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:#94A3B8;">#</th>
            <th style="padding:10px 18px;text-align:left;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:#94A3B8;">Produk</th>
            <th style="padding:10px 18px;text-align:left;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:#94A3B8;">Kategori</th>
            <th style="padding:10px 18px;text-align:right;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:#94A3B8;">Qty</th>
            <th style="padding:10px 18px;text-align:right;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:#94A3B8;">Revenue</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($selling as $i => $item)
          <tr style="border-bottom:1px solid #F8FAFC;" onmouseover="this.style.background='#FAFBFF'" onmouseout="this.style.background=''">
            <td style="padding:12px 18px;vertical-align:middle;">
              @php
                $rankClasses = ['rank-1'=>'background:#FEF3C7;color:#D97706;', 'rank-2'=>'background:#F1F5F9;color:#64748B;', 'rank-3'=>'background:#FEF2F2;color:#DC2626;'];
                $rc = $i < 3 ? array_values($rankClasses)[$i] : 'background:#F8FAFC;color:#94A3B8;';
              @endphp
              <span style="width:26px;height:26px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;font-size:11px;font-weight:800;{{ $rc }}">
                {{ $i + 1 }}
              </span>
            </td>
            <td style="padding:12px 18px;vertical-align:middle;">
              <div style="font-size:13.5px;font-weight:600;color:#1E293B;">{{ $item->product->name ?? '--' }}</div>
              <div style="font-size:11px;color:#94A3B8;font-family:'DM Mono',monospace;">{{ $item->product->sku ?? '' }}</div>
            </td>
            <td style="padding:12px 18px;vertical-align:middle;font-size:12px;color:#64748B;">
              {{ $item->product->category->name ?? '--' }}
            </td>
            <td style="padding:12px 18px;text-align:right;font-weight:700;color:#0F172A;vertical-align:middle;">
              {{ number_format($item->total_qty) }}
              <span style="font-size:11px;color:#94A3B8;font-weight:400;"> unit</span>
            </td>
            <td style="padding:12px 18px;text-align:right;vertical-align:middle;">
              <span style="font-size:13px;font-weight:600;color:#0D9488;">Rp {{ number_format($item->total_revenue, 0, ',', '.') }}</span>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="5" style="padding:48px 20px;text-align:center;">
              <div style="color:#CBD5E1;font-size:36px;margin-bottom:8px;"><i class="bi bi-box-seam"></i></div>
              <div style="font-size:13px;font-weight:500;color:#94A3B8;">Tidak ada data untuk rentang tanggal ini</div>
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  {{-- Critical Stock --}}
  <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden flex flex-col">
    <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between flex-shrink-0">
      <div class="flex items-center gap-2.5">
        <span class="w-7 h-7 rounded-lg flex items-center justify-center text-xs" style="background:#FEF2F2;color:#DC2626;">
          <i class="bi bi-exclamation-triangle"></i>
        </span>
        <span class="text-sm font-bold text-slate-900">Stok Kritis</span>
      </div>
      @if($lowStock->count() > 0)
      <span class="text-[11px] font-semibold bg-red-50 text-red-600 px-2 py-0.5 rounded-full">
        {{ $lowStock->count() }} produk
      </span>
      @endif
    </div>
    <div class="flex-1 overflow-y-auto" style="scrollbar-width:thin;scrollbar-color:#e2e8f0 transparent;">
      @forelse ($lowStock as $product)
      @php
        $pct = $product->stock_min > 0 ? min(100, round(($product->stock_qty / $product->stock_min) * 100)) : 0;
      @endphp
      <div class="flex items-center gap-3 px-5 py-3 border-b border-slate-50 hover:bg-slate-50 transition-colors">
        <div class="w-8 h-8 rounded-lg bg-red-50 flex items-center justify-center text-sm flex-shrink-0">📦</div>
        <div class="flex-1 min-w-0">
          <div class="text-sm font-semibold text-slate-800 truncate">{{ $product->name }}</div>
          <div class="text-[10px] text-slate-400">{{ $product->category->name ?? '--' }}</div>
          <div class="stock-progress"><div class="stock-progress-bar" style="width:{{ $pct }}%"></div></div>
        </div>
        <div class="text-right flex-shrink-0">
          <div class="text-sm font-bold text-red-600">{{ $product->stock_qty }}</div>
          <div class="text-[10px] text-slate-400">/ {{ $product->stock_min }}</div>
        </div>
      </div>
      @empty
      <div class="flex flex-col items-center justify-center py-10 text-slate-400">
        <i class="bi bi-check-circle-fill text-3xl text-emerald-500 mb-3"></i>
        <span class="text-sm font-medium">Semua stok aman</span>
      </div>
      @endforelse
    </div>
  </div>

</div>

@endsection
