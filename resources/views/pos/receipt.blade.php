{{-- resources/views/pos/receipt.blade.php --}}

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Struk #{{ $transaction->invoice_no }}</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

  <style>
    :root {
      --amber:  #D97706;
      --dark:   #1C1917;
      --border: #E7E5E4;
      --muted:  #78716C;
      --bg:     #F5F5F4;
    }
    body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--bg); color: var(--dark); }

    .page-wrapper {
      min-height: 100vh;
      display: flex; align-items: flex-start; justify-content: center;
      padding: 32px 16px;
      gap: 24px;
    }

    /* ── RECEIPT CARD ──────────────── */
    .receipt-card {
      width: 100%; max-width: 380px;
      background: white;
      border-radius: 16px;
      box-shadow: 0 4px 24px rgba(0,0,0,0.08);
      overflow: hidden;
    }
    .receipt-header {
      background: var(--dark);
      padding: 24px 24px 20px;
      text-align: center;
    }
    .receipt-brand { font-size: 18px; font-weight: 600; color: white; margin-bottom: 4px; }
    .receipt-tagline { font-size: 11px; color: rgba(255,255,255,0.4); letter-spacing: 0.08em; text-transform: uppercase; }
    .receipt-invoice {
      display: inline-block; margin-top: 14px;
      background: rgba(255,255,255,0.1);
      padding: 5px 14px; border-radius: 99px;
      font-family: 'DM Mono', monospace; font-size: 12px;
      color: rgba(255,255,255,0.7);
    }

    .receipt-body { padding: 20px 24px; }

    .receipt-meta {
      display: grid; grid-template-columns: 1fr 1fr;
      gap: 8px; margin-bottom: 18px;
    }
    .meta-item { }
    .meta-label { font-size: 10px; color: var(--muted); text-transform: uppercase; letter-spacing: 0.06em; margin-bottom: 2px; }
    .meta-value { font-size: 12.5px; font-weight: 500; }

    .receipt-divider {
      border: none; border-top: 1.5px dashed var(--border);
      margin: 16px 0;
    }

    /* Items */
    .receipt-items { margin-bottom: 4px; }
    .receipt-item {
      display: flex; justify-content: space-between; align-items: flex-start;
      padding: 7px 0;
      border-bottom: 1px solid var(--border);
      font-size: 13px;
    }
    .receipt-item:last-child { border-bottom: none; }
    .item-left { flex: 1; }
    .item-name { font-weight: 500; margin-bottom: 2px; }
    .item-qty-price { font-size: 11px; color: var(--muted); font-family: 'DM Mono', monospace; }
    .item-subtotal { font-weight: 600; font-size: 13px; white-space: nowrap; padding-left: 10px; }

    /* Totals */
    .receipt-totals { margin-top: 12px; }
    .total-row {
      display: flex; justify-content: space-between;
      font-size: 13px; padding: 4px 0; color: var(--muted);
    }
    .total-row.grand {
      font-size: 16px; font-weight: 700; color: var(--dark);
      padding-top: 10px; border-top: 2px solid var(--dark);
      margin-top: 6px;
    }
    .total-row.grand .val { color: var(--amber); }
    .total-row.change { color: #059669; font-weight: 500; }

    /* Payment badge */
    .pay-badge {
      display: inline-flex; align-items: center; gap: 5px;
      background: #D1FAE5; color: #065F46;
      padding: 4px 12px; border-radius: 99px;
      font-size: 11px; font-weight: 500;
      margin-top: 14px;
    }

    /* Footer */
    .receipt-footer {
      background: #FAFAF9; padding: 16px 24px;
      text-align: center; border-top: 1px solid var(--border);
    }
    .receipt-footer p { font-size: 11.5px; color: var(--muted); margin: 0; line-height: 1.6; }
    .receipt-footer .thank { font-size: 13px; font-weight: 600; color: var(--dark); margin-bottom: 4px; }

    /* ── ACTION PANEL ──────────────── */
    .action-panel {
      width: 100%; max-width: 260px;
    }
    .action-title { font-size: 13px; font-weight: 600; margin-bottom: 12px; color: var(--dark); }
    .action-btn {
      display: flex; align-items: center; gap: 10px;
      width: 100%; padding: 12px 16px;
      border-radius: 10px; border: 1.5px solid var(--border);
      background: white; color: var(--dark);
      font-family: 'Plus Jakarta Sans', sans-serif;
      font-size: 13.5px; font-weight: 500;
      cursor: pointer; text-decoration: none;
      transition: all 0.15s; margin-bottom: 8px;
    }
    .action-btn:hover { border-color: var(--amber); color: var(--amber); }
    .action-btn.primary { background: var(--dark); color: white; border-color: var(--dark); }
    .action-btn.primary:hover { opacity: 0.85; color: white; }
    .action-btn i { font-size: 16px; }
    .action-btn .lbl { flex: 1; text-align: left; }
    .action-btn .sub { font-size: 11px; opacity: 0.6; }

    .success-badge {
      display: flex; align-items: center; gap: 8px;
      background: #D1FAE5; color: #065F46;
      padding: 10px 14px; border-radius: 10px;
      font-size: 13px; font-weight: 500;
      margin-bottom: 16px;
    }

    @media print {
      body { background: white; }
      .page-wrapper { padding: 0; display: block; }
      .receipt-card { max-width: 100%; box-shadow: none; border-radius: 0; }
      .action-panel { display: none; }
    }
    @media (max-width: 680px) {
      .page-wrapper { flex-direction: column; align-items: center; }
      .action-panel { max-width: 380px; }
    }
  </style>
  <script src="https://cdn.jsdelivr.net/npm/qz-tray@2.2.4/qz-tray.js"></script>
</head>
<body>

<div class="page-wrapper">

  {{-- ── RECEIPT ───────────────────── --}}
  <div class="receipt-card" id="receiptCard">

    <div class="receipt-header">
      <div class="receipt-brand">🛒 {{ config('app.name') }}</div>
      <div class="receipt-tagline">Struk Pembayaran</div>
      <div class="receipt-invoice">{{ $transaction->invoice_no }}</div>
    </div>

    <div class="receipt-body">

      {{-- Meta info --}}
      <div class="receipt-meta">
        <div class="meta-item">
          <div class="meta-label">Tanggal</div>
          <div class="meta-value">{{ $transaction->transaction_date->format('d M Y') }}</div>
        </div>
        <div class="meta-item">
          <div class="meta-label">Waktu</div>
          <div class="meta-value">{{ $transaction->transaction_date->format('H:i') }} WIB</div>
        </div>
        <div class="meta-item">
          <div class="meta-label">Kasir</div>
          <div class="meta-value">{{ $transaction->cashier->name ?? '-' }}</div>
        </div>
        <div class="meta-item">
          <div class="meta-label">Metode</div>
          <div class="meta-value">{{ strtoupper($transaction->payment_method) }}</div>
        </div>
      </div>

      <hr class="receipt-divider">

      {{-- Items --}}
      <div class="receipt-items">
        @foreach ($transaction->items as $item)
        <div class="receipt-item">
          <div class="item-left">
            <div class="item-name">{{ $item->product->name ?? 'Produk dihapus' }}</div>
            <div class="item-qty-price">
              {{ $item->qty }} × Rp {{ number_format($item->unit_price, 0, ',', '.') }}
            </div>
          </div>
          <div class="item-subtotal">
            Rp {{ number_format($item->subtotal, 0, ',', '.') }}
          </div>
        </div>
        @endforeach
      </div>

      {{-- Totals --}}
      <div class="receipt-totals">
        <div class="total-row">
          <span>Subtotal</span>
          <span>Rp {{ number_format($transaction->total_amount + $transaction->discount, 0, ',', '.') }}</span>
        </div>
        @if ($transaction->discount > 0)
        <div class="total-row">
          <span>Diskon</span>
          <span>− Rp {{ number_format($transaction->discount, 0, ',', '.') }}</span>
        </div>
        @endif
        <div class="total-row grand">
          <span>Total</span>
          <span class="val">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</span>
        </div>
        <div class="total-row">
          <span>Dibayar</span>
          <span>Rp {{ number_format($transaction->paid_amount, 0, ',', '.') }}</span>
        </div>
        <div class="total-row change">
          <span>Kembalian</span>
          <span>Rp {{ number_format($transaction->change, 0, ',', '.') }}</span>
        </div>
      </div>

      @if ($transaction->notes)
      <div style="margin-top:12px; padding:10px 12px; background:#FAFAF9; border-radius:8px; font-size:12px; color:var(--muted);">
        <i class="bi bi-chat-text mr-1"></i> {{ $transaction->notes }}
      </div>
      @endif

    </div>

    <div class="receipt-footer">
      <p class="thank">Terima kasih sudah berbelanja! 🙏</p>
      <p>Barang yang sudah dibeli tidak dapat dikembalikan kecuali ada kesepakatan sebelumnya.</p>
      <p style="margin-top:6px; font-size:10px; color:#A8A29E;">
        Dicetak: {{ now()->format('d M Y H:i') }} · {{ config('app.name') }}
      </p>
    </div>

  </div>

  {{-- ── ACTION PANEL ──────────────── --}}
  <div class="action-panel">

    @if (session('success'))
    <div class="success-badge">
      <i class="bi bi-check-circle-fill"></i>
      {{ session('success') }}
    </div>
    @endif

    <div class="action-title">Tindakan</div>

    {{-- Transaksi baru --}}
    <a href="{{ route('pos.index') }}" class="action-btn primary">
      <i class="bi bi-plus-circle"></i>
      <span class="lbl">
        Transaksi Baru
        <div class="sub">Mulai kasir lagi</div>
      </span>
    </a>

    {{-- Print QZ Tray --}}
    <button class="action-btn" onclick="printQzTray('{{ route('pos.receipt.raw', $transaction) }}')">
      <i class="bi bi-printer-fill" style="color:var(--amber)"></i>
      <span class="lbl">
        Cetak Thermal (QZ Tray)
        <div class="sub">Print ke printer struk</div>
      </span>
    </button>

    {{-- Print HTML --}}
    <button class="action-btn" onclick="window.print()">
      <i class="bi bi-printer"></i>
      <span class="lbl">
        Cetak (Browser)
        <div class="sub">Print halaman HTML</div>
      </span>
    </button>

    {{-- Download PDF --}}
    <a href="{{ route('pos.receipt', $transaction) }}?download=1" class="action-btn">
      <i class="bi bi-file-pdf"></i>
      <span class="lbl">
        Download PDF
        <div class="sub">Simpan sebagai file</div>
      </span>
    </a>

    {{-- Dashboard --}}
    <a href="{{ route('dashboard') }}" class="action-btn">
      <i class="bi bi-grid-1x2"></i>
      <span class="lbl">
        Kembali ke Dashboard
        <div class="sub">Lihat ringkasan hari ini</div>
      </span>
    </a>

    {{-- Transaction info --}}
    <div style="margin-top:16px; padding:14px; background:white; border-radius:10px; border:1.5px solid var(--border);">
      <div style="font-size:11px; color:var(--muted); text-transform:uppercase; letter-spacing:0.06em; margin-bottom:8px;">Info Transaksi</div>
      <div style="font-family:'DM Mono',monospace; font-size:12px; color:var(--dark); margin-bottom:4px;">{{ $transaction->invoice_no }}</div>
      <div style="font-size:12px; color:var(--muted);">{{ $transaction->items->count() }} item · {{ $transaction->transaction_date->diffForHumans() }}</div>
    </div>

  </div>

</div>

<script>
  function printQzTray(rawReceiptUrl) {
    if (!qz.websocket.isActive()) {
      qz.websocket.connect().then(function() {
        return performPrint(rawReceiptUrl);
      }).catch(function(e) {
        console.error(e);
        alert("Gagal koneksi ke QZ Tray. Pastikan aplikasi QZ Tray sedang berjalan di komputer kasir.");
      });
    } else {
      performPrint(rawReceiptUrl);
    }
  }

  function performPrint(url) {
    qz.printers.getDefault().then(function(printer) {
      fetch(url)
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            var config = qz.configs.create(printer);
            var printData = [
              { type: 'raw', format: 'base64', data: data.raw_data }
            ];
            return qz.print(config, printData).then(() => {
              // Sukses print
              console.log("Berhasil mencetak ke thermal printer.");
            });
          } else {
            alert("Gagal mengambil data struk dari server.");
          }
        })
        .catch(e => {
          console.error(e);
          alert("Gagal mencetak struk.");
        });
    }).catch(function(e) {
      console.error(e);
      alert("Tidak dapat menemukan printer default. Pastikan printer sudah terhubung.");
    });
  }
</script>

</body>
</html>