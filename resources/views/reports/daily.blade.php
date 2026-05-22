@extends('layouts.app')
@section('title', 'Laporan Harian')
@section('page-title', 'Laporan Harian')

@section('content')

{{-- Breadcrumb nav --}}
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
  <div class="flex items-center gap-3">
    <a href="{{ route('reports.index') }}" class="topbar-btn" title="Kembali">
      <i class="bi bi-arrow-left text-sm"></i>
    </a>
    <div>
      <h2 class="text-xl font-bold text-slate-900 mb-0.5">Laporan Harian</h2>
      <p class="text-sm text-slate-500">{{ \Carbon\Carbon::parse($date)->translatedFormat('l, d F Y') }}</p>
    </div>
  </div>
  <div class="flex flex-wrap gap-2">
    <a href="{{ route('reports.daily') }}" class="report-nav-btn active">
      <i class="bi bi-calendar-day"></i> Harian
    </a>
    <a href="{{ route('reports.monthly') }}" class="report-nav-btn">
      <i class="bi bi-calendar-month"></i> Bulanan
    </a>
    <a href="{{ route('reports.products') }}" class="report-nav-btn">
      <i class="bi bi-box-seam"></i> Produk
    </a>
  </div>
</div>

{{-- Date Filter Bar --}}
<div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 mb-6">
  <form method="GET" action="{{ route('reports.daily') }}" class="flex flex-wrap gap-3 items-end">
    <div>
      <label class="block text-xs font-semibold text-slate-600 mb-1.5">Pilih Tanggal</label>
      <input type="date" name="date" value="{{ $date }}"
        class="h-9 px-3 text-sm text-slate-700 border border-slate-200 rounded-lg bg-white
               focus:outline-none focus:border-teal-400 focus:ring-2 focus:ring-teal-100 transition-all">
    </div>
    <button type="submit" class="btn-primary">
      <i class="bi bi-search"></i> Tampilkan
    </button>
    @can('report.export')
    <a href="{{ route('reports.export.pdf', ['date' => $date]) }}"
       class="h-9 px-4 inline-flex items-center gap-2 text-sm font-semibold text-red-600
              bg-red-50 hover:bg-red-100 border border-red-200 rounded-xl transition-colors">
      <i class="bi bi-file-pdf"></i> Export PDF
    </a>
    @endcan
  </form>
</div>

{{-- Summary KPI --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
  <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-sm">
    <div class="flex items-center gap-3 mb-3">
      <div class="w-9 h-9 rounded-xl flex items-center justify-center text-base" style="background:#F0FDFA;color:#0F766E;">
        <i class="bi bi-cash-coin"></i>
      </div>
      <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Pendapatan</span>
    </div>
    <div class="text-xl font-extrabold text-slate-900 tracking-tight">Rp {{ number_format($summary['revenue'], 0, ',', '.') }}</div>
  </div>
  <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-sm">
    <div class="flex items-center gap-3 mb-3">
      <div class="w-9 h-9 rounded-xl flex items-center justify-center text-base" style="background:#F0FDF4;color:#16A34A;">
        <i class="bi bi-receipt"></i>
      </div>
      <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Transaksi</span>
    </div>
    <div class="text-xl font-extrabold text-slate-900 tracking-tight">{{ $summary['count'] }}</div>
  </div>
  <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-sm">
    <div class="flex items-center gap-3 mb-3">
      <div class="w-9 h-9 rounded-xl flex items-center justify-center text-base" style="background:#EFF6FF;color:#2563EB;">
        <i class="bi bi-box-seam"></i>
      </div>
      <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Item Terjual</span>
    </div>
    <div class="text-xl font-extrabold text-slate-900 tracking-tight">{{ $summary['items_sold'] }}</div>
  </div>
  <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-sm">
    <div class="flex items-center gap-3 mb-3">
      <div class="w-9 h-9 rounded-xl flex items-center justify-center text-base" style="background:#FDF4FF;color:#A21CAF;">
        <i class="bi bi-calculator"></i>
      </div>
      <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Rata-rata</span>
    </div>
    <div class="text-xl font-extrabold text-slate-900 tracking-tight">
      Rp {{ $summary['count'] > 0 ? number_format($summary['revenue'] / $summary['count'], 0, ',', '.') : 0 }}
    </div>
  </div>
</div>

{{-- Transaction Table --}}
<div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
  <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
    <div class="flex items-center gap-2.5">
      <span class="w-7 h-7 rounded-lg flex items-center justify-center text-xs" style="background:#F0FDFA;color:#0D9488;">
        <i class="bi bi-list-ul"></i>
      </span>
      <span class="text-sm font-bold text-slate-900">
        Detail Transaksi -- {{ \Carbon\Carbon::parse($date)->translatedFormat('d F Y') }}
      </span>
    </div>
    <span class="text-xs font-mono text-slate-400">{{ $transactions->count() }} transaksi</span>
  </div>
  <div class="overflow-x-auto">
    <table style="width:100%;border-collapse:collapse;font-size:13.5px;">
      <thead>
        <tr style="background:#F8FAFC;border-bottom:1.5px solid #F1F5F9;">
          @foreach(['Invoice','Kasir','Item','Diskon','Total','Metode','Waktu'] as $h)
          <th style="padding:10px 18px;text-align:left;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:#94A3B8;white-space:nowrap;">{{ $h }}</th>
          @endforeach
        </tr>
      </thead>
      <tbody>
        @forelse ($transactions as $trx)
        <tr style="border-bottom:1px solid #F8FAFC;" onmouseover="this.style.background='#FAFBFF'" onmouseout="this.style.background=''">
          <td style="padding:11px 18px;vertical-align:middle;">
            <span style="font-family:'DM Mono',monospace;font-size:11.5px;color:#0D9488;background:#F0FDFA;padding:2px 8px;border-radius:6px;">
              {{ $trx->invoice_no }}
            </span>
          </td>
          <td style="padding:11px 18px;vertical-align:middle;font-weight:500;color:#1E293B;">
            {{ $trx->cashier->name ?? '--' }}
          </td>
          <td style="padding:11px 18px;vertical-align:middle;color:#64748B;font-size:12.5px;">
            {{ $trx->items->count() }} item · {{ $trx->items->sum('qty') }} qty
          </td>
          <td style="padding:11px 18px;vertical-align:middle;">
            @if ($trx->discount > 0)
              <span style="font-size:12px;font-weight:600;color:#DC2626;">
                - Rp {{ number_format($trx->discount, 0, ',', '.') }}
              </span>
            @else
              <span style="color:#CBD5E1;">--</span>
            @endif
          </td>
          <td style="padding:11px 18px;vertical-align:middle;font-weight:700;color:#0F172A;">
            Rp {{ number_format($trx->total_amount, 0, ',', '.') }}
          </td>
          <td style="padding:11px 18px;vertical-align:middle;">
            @if($trx->payment_method === 'cash')
              <span style="display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:99px;font-size:10.5px;font-weight:600;background:#F0FDF4;color:#16A34A;">
                <i class="bi bi-cash"></i> Cash
              </span>
            @elseif($trx->payment_method === 'transfer')
              <span style="display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:99px;font-size:10.5px;font-weight:600;background:#EFF6FF;color:#2563EB;">
                <i class="bi bi-bank"></i> Transfer
              </span>
            @else
              <span style="display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:99px;font-size:10.5px;font-weight:600;background:#F0FDFA;color:#0D9488;">
                <i class="bi bi-qr-code"></i> QRIS
              </span>
            @endif
          </td>
          <td style="padding:11px 18px;vertical-align:middle;font-family:'DM Mono',monospace;font-size:12px;color:#94A3B8;">
            {{ $trx->transaction_date->format('H:i') }}
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="7" style="padding:48px 20px;text-align:center;">
            <div style="color:#CBD5E1;font-size:36px;margin-bottom:8px;"><i class="bi bi-calendar-x"></i></div>
            <div style="font-size:13px;font-weight:500;color:#94A3B8;">Tidak ada transaksi pada tanggal ini</div>
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

@endsection

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
