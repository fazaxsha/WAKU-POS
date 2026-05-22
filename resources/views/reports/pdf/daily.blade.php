{{-- resources/views/reports/pdf/daily.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Laporan Harian {{ $date }}</title>
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #1C1917; }
    .header { background: #1C1917; color: white; padding: 16px 20px; margin-bottom: 16px; }
    .header h1 { font-size: 16px; font-weight: bold; }
    .header p  { font-size: 10px; opacity: 0.6; margin-top: 3px; }
    .summary { display: flex; gap: 12px; margin-bottom: 16px; padding: 0 20px; }
    .summary-card { flex: 1; background: #F5F5F4; padding: 10px 12px; border-radius: 6px; }
    .summary-label { font-size: 9px; color: #78716C; text-transform: uppercase; letter-spacing: 0.05em; }
    .summary-value { font-size: 14px; font-weight: bold; margin-top: 3px; }
    .section-title { font-size: 11px; font-weight: bold; padding: 0 20px; margin-bottom: 6px; color: #78716C; text-transform: uppercase; letter-spacing: 0.05em; }
    table { width: 100%; border-collapse: collapse; }
    thead th { background: #1C1917; color: white; padding: 7px 10px; font-size: 9px; text-transform: uppercase; letter-spacing: 0.05em; text-align: left; }
    tbody td { padding: 7px 10px; border-bottom: 1px solid #E7E5E4; font-size: 10px; }
    tbody tr:nth-child(even) td { background: #FAFAF9; }
    .text-right { text-align: right; }
    .fw-bold { font-weight: bold; }
    .total-row td { background: #FEF3C7 !important; font-weight: bold; font-size: 11px; }
    .footer { margin-top: 20px; padding: 12px 20px; border-top: 1px solid #E7E5E4; text-align: center; font-size: 9px; color: #78716C; }
  </style>
</head>
<body>
  <div class="header">
    <h1>Laporan Penjualan Harian</h1>
    <p>Tanggal: {{ \Carbon\Carbon::parse($date)->translatedFormat('l, d F Y') }} &nbsp;|&nbsp; Dicetak: {{ now()->format('d/m/Y H:i') }}</p>
  </div>

  <div class="summary">
    <div class="summary-card">
      <div class="summary-label">Total Pendapatan</div>
      <div class="summary-value">Rp {{ number_format($summary['revenue'], 0, ',', '.') }}</div>
    </div>
    <div class="summary-card">
      <div class="summary-label">Total Transaksi</div>
      <div class="summary-value">{{ $summary['count'] }}</div>
    </div>
    <div class="summary-card">
      <div class="summary-label">Item Terjual</div>
      <div class="summary-value">{{ $summary['items_sold'] }}</div>
    </div>
    <div class="summary-card">
      <div class="summary-label">Rata-rata / Transaksi</div>
      <div class="summary-value">Rp {{ $summary['count'] > 0 ? number_format($summary['revenue'] / $summary['count'], 0, ',', '.') : 0 }}</div>
    </div>
  </div>

  <div class="section-title">Detail Transaksi</div>
  <table>
    <thead>
      <tr>
        <th>No Invoice</th>
        <th>Kasir</th>
        <th>Item</th>
        <th class="text-right">Diskon</th>
        <th class="text-right">Total</th>
        <th>Metode</th>
        <th>Waktu</th>
      </tr>
    </thead>
    <tbody>
      @forelse ($transactions as $trx)
      <tr>
        <td>{{ $trx->invoice_no }}</td>
        <td>{{ $trx->cashier->name ?? '-' }}</td>
        <td>{{ $trx->items->count() }} item</td>
        <td class="text-right">{{ $trx->discount > 0 ? number_format($trx->discount, 0, ',', '.') : '-' }}</td>
        <td class="text-right fw-bold">{{ number_format($trx->total_amount, 0, ',', '.') }}</td>
        <td>{{ strtoupper($trx->payment_method) }}</td>
        <td>{{ $trx->transaction_date->format('H:i') }}</td>
      </tr>
      @empty
      <tr><td colspan="7" style="text-align:center; padding:20px; color:#78716C;">Tidak ada transaksi</td></tr>
      @endforelse
      @if ($transactions->count())
      <tr class="total-row">
        <td colspan="4" class="text-right">TOTAL</td>
        <td class="text-right">{{ number_format($summary['revenue'], 0, ',', '.') }}</td>
        <td colspan="2"></td>
      </tr>
      @endif
    </tbody>
  </table>

  <div class="footer">
    Laporan ini digenerate otomatis oleh sistem {{ config('app.name') }} &nbsp;·&nbsp; {{ now()->format('d/m/Y H:i:s') }}
  </div>
</body>
</html>