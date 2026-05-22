{{-- resources/views/pos/index.blade.php --}}
{{-- Layout khusus POS -- fullscreen, tanpa sidebar --}}

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Kasir -- {{ config('app.name') }}</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  @vite(['resources/css/app.css', 'resources/js/app.js'])

  <style>
    :root {
      --accent:    #0D9488; /* Teal 600 */
      --accent-lt: #5EEAD4; /* Teal 300 */
      --dark:     #0F172A; /* Slate 900 */
      --body-bg:  #F4F6F8; /* Cool Gray */
      --card-bg:  #FFFFFF;
      --border:   #E2E8F0; /* Slate 200 */
      --muted:    #64748B; /* Slate 500 */
      --radius:   12px;
      --topbar-h: 64px;
      /* Safe area insets for mobile browsers (notch, home bar) */
      --safe-bottom: env(safe-area-inset-bottom, 0px);
    }
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--body-bg); height: 100vh; height: 100dvh; overflow: hidden; margin: 0; }

    /* -- TOPBAR -- */
    .pos-topbar {
      height: var(--topbar-h);
      background: var(--dark);
      display: flex; align-items: center;
      padding: 0 20px; gap: 16px;
      position: fixed; top: 0; left: 0; right: 0; z-index: 100;
    }
    .pos-brand { font-size: 15px; font-weight: 600; color: #FAFAF9; display: flex; align-items: center; gap: 8px; }
    .pos-brand span { background: var(--accent); padding: 3px 8px; border-radius: 6px; font-size: 11px; font-family: 'DM Mono', monospace; letter-spacing: 0.05em; }
    .pos-topbar-right { margin-left: auto; display: flex; align-items: center; gap: 10px; }
    .pos-cashier { font-size: 12px; color: rgba(255,255,255,0.5); }
    .pos-cashier strong { color: rgba(255,255,255,0.85); }
    .pos-back {
      display: flex; align-items: center; gap: 6px;
      font-size: 12px; font-weight: 500; color: rgba(255,255,255,0.5);
      text-decoration: none; padding: 6px 12px; border-radius: 8px;
      border: 1px solid rgba(255,255,255,0.1); transition: all 0.15s;
    }
    .pos-back:hover { color: #FAFAF9; border-color: rgba(255,255,255,0.25); }

    /* -- MAIN LAYOUT -- */
    .pos-layout {
      display: grid;
      grid-template-columns: 7fr 3fr; /* 70/30 split */
      height: calc(100vh - var(--topbar-h));
      height: calc(100dvh - var(--topbar-h)); /* dvh accounts for mobile browser chrome */
      margin-top: var(--topbar-h);
      overflow: hidden;
    }

    /* -- MOBILE CART TOGGLE -- */
    .mobile-cart-toggle {
      display: none; position: fixed; bottom: 20px; right: 20px; z-index: 200;
      width: 56px; height: 56px; border-radius: 50%; border: none;
      background: var(--accent); color: white; font-size: 22px;
      cursor: pointer; box-shadow: 0 4px 16px rgba(13,148,136,0.4);
      align-items: center; justify-content: center;
    }
    .mobile-cart-badge {
      position: absolute; top: -2px; right: -2px;
      background: #DC2626; color: white; font-size: 10px; font-weight: 700;
      width: 20px; height: 20px; border-radius: 50%; display: flex;
      align-items: center; justify-content: center;
    }
    .mobile-cart-back {
      display: none; padding: 10px 16px; background: var(--card-bg);
      border-bottom: 1px solid var(--border); cursor: pointer;
      font-size: 13px; font-weight: 500; color: var(--muted); align-items: center; gap: 6px;
    }

    /* -- RESPONSIVE -- */
    @media (max-width: 768px) {
      .pos-topbar { padding: 0 12px; gap: 8px; }
      .pos-cashier { display: none; }
      .pos-back { padding: 4px 8px; font-size: 11px; }
      .pos-layout { grid-template-columns: 1fr; }

      /* Mobile cart: full-screen overlay, structured as flex column */
      .pos-cart {
        position: fixed; inset: 0; z-index: 150; top: var(--topbar-h);
        transform: translateX(100%); transition: transform 0.3s ease;
        display: flex; flex-direction: column;
        /* Use dynamic viewport height to exclude browser chrome bars */
        height: calc(100dvh - var(--topbar-h));
      }
      .pos-cart.open { transform: translateX(0); }

      /* On mobile, cart-items should flex-grow to fill remaining space */
      .pos-cart .cart-items {
        flex: 1;
        min-height: 0;
      }

      /* On mobile, remove the 55vh cap so footer doesn't hide the button */
      /* Instead, let it be scrollable within the fixed cart height */
      .pos-cart .cart-footer {
        max-height: none;
        flex-shrink: 0;
        overflow-y: auto;
        /* Padding bottom for mobile safe area (home bar, navigation bar) */
        padding-bottom: var(--safe-bottom);
      }

      /* Ensure process button is always fully visible - add bottom spacing */
      .pos-cart .btn-process {
        margin-bottom: 4px;
      }

      .mobile-cart-toggle { display: flex; }
      .mobile-cart-back { display: flex; }
      .product-grid { grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); gap: 8px; padding: 10px 12px; }
      .product-card { padding: 10px; }
      .product-img { margin-bottom: 6px; }
      .product-name { font-size: 11.5px; }
      .product-price { font-size: 12px; }
      .qris-modal { width: 92vw; max-width: 360px; padding: 20px 16px; }
    }
    @media (max-width: 480px) {
      .product-grid { grid-template-columns: repeat(2, 1fr); }
    }

    /* -- LEFT: PRODUK -- */
    .pos-products { display: flex; flex-direction: column; background: var(--body-bg); overflow: hidden; }
    .pos-search-bar { padding: 14px 16px; background: var(--card-bg); border-bottom: 1px solid var(--border); display: flex; gap: 10px; }
    .pos-search {
      flex: 1; padding: 9px 14px 9px 38px;
      background: var(--body-bg); border: 1.5px solid var(--border);
      border-radius: 8px; font-size: 13.5px; font-family: 'Plus Jakarta Sans', sans-serif;
      outline: none; transition: border-color 0.15s;
    }
    .pos-search:focus { border-color: var(--accent); }
    .search-wrap { position: relative; flex: 1; }
    .search-icon { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--muted); font-size: 14px; pointer-events: none; }
    .cat-filter { display: flex; gap: 6px; overflow-x: auto; padding: 10px 16px; background: var(--card-bg); border-bottom: 1px solid var(--border); scrollbar-width: none; }
    .cat-filter::-webkit-scrollbar { display: none; }
    .cat-btn {
      flex-shrink: 0; padding: 5px 14px; border-radius: 99px;
      font-size: 12px; font-weight: 500; cursor: pointer;
      border: 1.5px solid var(--border); background: var(--card-bg);
      color: var(--muted); transition: all 0.15s; white-space: nowrap;
    }
    .cat-btn.active { background: var(--dark); color: #FAFAF9; border-color: var(--dark); }
    .cat-btn:hover:not(.active) { border-color: var(--accent); color: var(--accent); }

    .product-grid {
      flex: 1; overflow-y: auto; padding: 14px 16px;
      display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
      gap: 10px; align-content: start;
    }
    .product-card {
      background: var(--card-bg); border: 1.5px solid var(--border);
      border-radius: var(--radius); padding: 12px; cursor: pointer;
      transition: all 0.15s; position: relative; user-select: none;
    }
    .product-card:hover { border-color: var(--accent); box-shadow: 0 2px 8px rgba(13,148,136,0.12); transform: translateY(-1px); }
    .product-card.out-of-stock { opacity: 0.5; cursor: not-allowed; }
    .product-card.out-of-stock:hover { transform: none; border-color: var(--border); box-shadow: none; }
    .product-img { width: 100%; aspect-ratio: 1; border-radius: 7px; margin-bottom: 10px; background: var(--body-bg); display: flex; align-items: center; justify-content: center; font-size: 28px; overflow: hidden; }
    .product-img img { width: 100%; height: 100%; object-fit: cover; }
    .product-name { font-size: 12.5px; font-weight: 500; line-height: 1.4; margin-bottom: 6px; color: var(--dark); }
    .product-price { font-size: 13px; font-weight: 600; color: var(--accent); }
    .product-stock { font-size: 10.5px; color: var(--muted); margin-top: 2px; }
    .product-stock.low { color: #DC2626; }
    .out-badge { position: absolute; top: 8px; right: 8px; font-size: 9px; font-weight: 500; padding: 2px 6px; border-radius: 4px; background: #FEE2E2; color: #DC2626; font-family: 'DM Mono', monospace; }

    /* -- RIGHT: CART -- */
    .pos-cart {
      display: flex;
      flex-direction: column;
      background: var(--card-bg);
      border-left: 1px solid var(--border);
      height: 100%; /* fill grid cell */
      overflow: hidden; /* crucial -- children handle their own scroll */
    }
    .cart-header {
      padding: 14px 16px;
      border-bottom: 1px solid var(--border);
      display: flex; align-items: center; justify-content: space-between;
      flex-shrink: 0; /* never shrink */
    }
    .cart-title { font-size: 14px; font-weight: 600; }
    .cart-clear { font-size: 12px; color: #DC2626; cursor: pointer; background: none; border: none; padding: 0; display: flex; align-items: center; gap: 4px; }

    /* scrollable item list -- takes remaining space */
    .cart-items {
      flex: 1;
      min-height: 0; /* allow flex child to shrink below content size */
      overflow-y: auto;
      padding: 8px 10px;
      position: relative;
      scrollbar-width: thin;
      scrollbar-color: #E2E8F0 transparent;
    }
    .cart-items::-webkit-scrollbar { width: 4px; }
    .cart-items::-webkit-scrollbar-thumb { background: #CBD5E1; border-radius: 4px; }

    .cart-empty { display: flex; flex-direction: column; align-items: center; justify-content: center; color: var(--muted); font-size: 13px; gap: 8px; padding: 40px 0; }
    .cart-item { display: flex; align-items: center; gap: 10px; padding: 10px 8px; border-radius: 8px; background: var(--body-bg); margin-bottom: 6px; }
    .cart-item-name { font-size: 12.5px; font-weight: 500; flex: 1; min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .cart-item-price { font-size: 11px; color: var(--muted); margin-top: 1px; }
    .qty-ctrl { display: flex; align-items: center; gap: 6px; flex-shrink: 0; }
    .qty-btn { width: 24px; height: 24px; border-radius: 6px; background: white; border: 1.5px solid var(--border); font-size: 14px; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.12s; line-height: 1; }
    .qty-btn:hover { border-color: var(--accent); color: var(--accent); }
    .qty-val { font-size: 13px; font-weight: 600; min-width: 20px; text-align: center; }
    .cart-item-total { font-size: 13px; font-weight: 600; min-width: 76px; text-align: right; flex-shrink: 0; }
    .remove-btn { color: #DC2626; background: none; border: none; cursor: pointer; font-size: 14px; padding: 2px; flex-shrink: 0; }

    /* payment/checkout footer -- scrollable internally, always visible */
    .cart-footer {
      flex-shrink: 0;
      border-top: 1px solid var(--border);
      display: flex;
      flex-direction: column;
      max-height: 55vh;   /* cap height so it doesn't swallow screen on desktop */
      overflow-y: auto;   /* scroll inside if transfer details push content */
      scrollbar-width: thin;
      scrollbar-color: #E2E8F0 transparent;
    }
    .cart-footer::-webkit-scrollbar { width: 4px; }
    .cart-footer::-webkit-scrollbar-thumb { background: #CBD5E1; border-radius: 4px; }
    .cart-footer-inner { padding: 12px 14px; }
    .cart-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px; font-size: 13px; }
    .cart-row.total { font-size: 16px; font-weight: 700; padding-top: 8px; border-top: 1px solid var(--border); margin-top: 4px; }
    .cart-row .label { color: var(--muted); }

    .discount-row { display: flex; align-items: center; gap: 8px; margin: 8px 0; padding: 8px 10px; background: var(--body-bg); border-radius: 8px; }
    .discount-row label { font-size: 12px; color: var(--muted); flex-shrink: 0; }
    .discount-input { flex: 1; background: none; border: none; outline: none; font-size: 13px; font-weight: 500; font-family: 'Plus Jakarta Sans', sans-serif; text-align: right; color: var(--dark); }

    .pay-methods { display: grid; grid-template-columns: repeat(3,1fr); gap: 6px; margin: 10px 0; }
    .pay-btn { padding: 8px 4px; border-radius: 8px; border: 1.5px solid var(--border); background: white; font-size: 11.5px; font-weight: 500; cursor: pointer; text-align: center; transition: all 0.15s; color: var(--muted); }
    .pay-btn.active { border-color: var(--dark); background: var(--dark); color: white; }
    .pay-btn i { display: block; font-size: 16px; margin-bottom: 3px; }

    .paid-wrap { margin: 8px 0; background: var(--body-bg); border-radius: 8px; padding: 10px 12px; }
    .paid-label { font-size: 11px; color: var(--muted); margin-bottom: 4px; }
    .paid-input { width: 100%; background: none; border: none; outline: none; font-size: 18px; font-weight: 700; font-family: 'Plus Jakarta Sans', sans-serif; color: var(--dark); }

    .change-row { display: flex; justify-content: space-between; align-items: center; padding: 8px 10px; border-radius: 8px; margin: 6px 0; background: #D1FAE5; }
    .change-row .label { font-size: 12px; color: #065F46; }
    .change-row .value { font-size: 14px; font-weight: 700; color: #065F46; }
    .change-row.negative { background: #FEE2E2; }
    .change-row.negative .label, .change-row.negative .value { color: #DC2626; }

    .btn-process {
      width: 100%; padding: 16px; background: #94A3B8; color: white;
      border: none; border-radius: 12px; font-family: 'Plus Jakarta Sans', sans-serif;
      font-size: 16px; font-weight: 700; cursor: not-allowed;
      display: flex; align-items: center; justify-content: center; gap: 8px;
      margin-top: 16px; transition: all 0.2s; opacity: 0.8;
      box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }
    .btn-process.ready { background: #0F172A; cursor: pointer; opacity: 1; }
    .btn-process.ready:hover { background: #1E293B; transform: translateY(-1px); }

    .transfer-details {
      display: none; background: white; border: 1.5px solid var(--border);
      border-radius: 12px; padding: 12px; margin-bottom: 12px;
    }
    .transfer-details.show { display: block; }
    .bank-item {
      display: flex; align-items: center; gap: 12px; padding: 8px 0;
      border-bottom: 1px solid #F1F5F9;
    }
    .bank-item:last-child { border-bottom: none; }
    .bank-logo { width: 40px; height: 40px; object-fit: contain; border-radius: 6px; border: 1px solid #F1F5F9; }
    .bank-info { flex: 1; }
    .bank-name { font-size: 11px; font-weight: 600; color: var(--muted); text-transform: uppercase; letter-spacing: 0.05em; }
    .bank-acc { font-size: 14px; font-weight: 700; color: var(--dark); font-family: 'DM Mono', monospace; }

    /* -- QRIS MODAL -- */
    .qris-overlay {
      display:none; position:fixed; inset:0; z-index:9000;
      background:rgba(0,0,0,0.55); align-items:center; justify-content:center;
    }
    .qris-overlay.show { display:flex; }
    .qris-modal {
      background:white; border-radius:16px; padding:28px 24px; width:360px;
      text-align:center; box-shadow:0 20px 60px rgba(0,0,0,0.25); position:relative;
    }
    .qris-modal-title { font-size:15px; font-weight:600; margin-bottom:4px; }
    .qris-modal-merchant { font-size:12px; color:var(--muted); margin-bottom:16px; }
    .qris-modal-amount { font-size:22px; font-weight:700; color:var(--accent); margin-bottom:16px; }
    .qris-modal-qr { display:flex; align-items:center; justify-content:center; min-height:280px; }
    .qris-modal-qr svg { max-width:100%; }
    .qris-modal-loading { color:var(--muted); font-size:13px; padding:40px 0; }
    .qris-modal-close {
      position:absolute; top:12px; right:14px; background:none; border:none;
      font-size:20px; cursor:pointer; color:var(--muted); line-height:1;
    }
    .qris-modal-close:hover { color:var(--dark); }
    .qris-modal-footer { font-size:11px; color:var(--muted); margin-top:14px; }
  </style>
</head>
<body>

{{-- Flash error (misal stok tidak cukup) --}}
@if (session('error'))
<div id="posFlashError" style="position:fixed; top:64px; left:50%; transform:translateX(-50%); z-index:9999;
  background:#FEE2E2; color:#991B1B; border:1px solid #FCA5A5;
  padding:10px 20px; border-radius:10px; font-size:13px; font-weight:500;
  display:flex; align-items:center; gap:8px; box-shadow:0 4px 12px rgba(0,0,0,0.1); transition:opacity 0.3s;">
  <i class="bi bi-exclamation-circle-fill"></i>
  {{ session('error') }}
  <button onclick="document.getElementById('posFlashError').remove()" style="background:none;border:none;color:#991B1B;cursor:pointer;font-size:16px;padding:0 0 0 8px;">&times;</button>
</div>
<script>setTimeout(function(){ var el=document.getElementById('posFlashError'); if(el){el.style.opacity='0';setTimeout(function(){el.remove();},300);} }, 4000);</script>
@endif

{{-- TOPBAR --}}
<div class="pos-topbar">
  <div class="pos-brand">
    {{ config('app.name') }}
    <span>POS</span>
  </div>
  <div class="pos-topbar-right">
    <div class="pos-cashier">Kasir: <strong>{{ auth()->user()->name }}</strong></div>
    <div class="pos-cashier" id="clock"></div>
    <a href="{{ route('dashboard') }}" class="pos-back">
      <i class="bi bi-arrow-left"></i> Dashboard
    </a>
  </div>
</div>

{{-- Mobile cart toggle FAB --}}
<button class="mobile-cart-toggle" id="mobileCartToggle" onclick="openMobileCart()">
  <i class="bi bi-cart3"></i>
  <span class="mobile-cart-badge" id="mobileCartBadge" style="display:none;">0</span>
</button>

{{-- MAIN LAYOUT --}}
<div class="pos-layout">

  {{-- LEFT: PRODUCTS --}}
  <div class="pos-products">
    <div class="pos-search-bar" style="display: flex; gap: 10px; align-items: center;">
      <div class="search-wrap" style="flex: 1;">
        <i class="bi bi-search search-icon"></i>
        <input type="text" class="pos-search" id="searchInput"
          placeholder="Cari produk atau SKU..." autocomplete="off"
          oninput="filterProducts()"
          onkeydown="if(event.key === 'Enter') { event.preventDefault(); if(this.value.length > 0) { handleBarcodeScan(this.value.trim()); this.value=''; filterProducts(); } }">
      </div>
      <button class="reseller-toggle" id="resellerToggle" onclick="toggleResellerMode()" style="flex-shrink:0; padding: 0 12px; height: 38px; border-radius: 8px; border: 1.5px solid var(--border); background: var(--card-bg); font-size: 13px; font-weight: 600; color: var(--muted); cursor: pointer; transition: all 0.2s;">
        <i class="bi bi-person-badge"></i> Mode Reseller
      </button>
    </div>

    <div class="cat-filter">
      <button class="cat-btn active" onclick="filterCategory(0, this)">Semua</button>
      @foreach ($categories as $cat)
        <button class="cat-btn" onclick="filterCategory({{ $cat->id }}, this)">
          {{ $cat->name }}
        </button>
      @endforeach
    </div>

    <div class="product-grid" id="productGrid">
      @foreach ($products as $product)
      <div class="product-card {{ $product->stock_qty == 0 ? 'out-of-stock' : '' }}"
        data-id="{{ $product->id }}"
        data-name="{{ addslashes($product->name) }}"
        data-price="{{ $product->sell_price }}"
        data-wholesale-price="{{ $product->wholesale_price ?? 0 }}"
        data-wholesale-min-qty="{{ $product->wholesale_min_qty ?? 0 }}"
        data-stock="{{ $product->stock_qty }}"
        data-sku="{{ $product->sku }}"
        data-unit="{{ $product->unit }}"
        data-category="{{ $product->category_id }}"
        onclick="addToCart(this)">

        @if ($product->stock_qty == 0)
          <div class="out-badge">Habis</div>
        @endif

        <div class="product-img">
          @if ($product->image)
            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
          @else
            {{ config('app.name') }}
          @endif
        </div>
        <div class="product-name">{{ $product->name }}</div>
        <div class="product-price">Rp {{ number_format($product->sell_price, 0, ',', '.') }}</div>
        <div class="product-stock {{ $product->stock_qty <= $product->stock_min ? 'low' : '' }}">
          Stok: {{ $product->unit === 'pcs' ? (int)$product->stock_qty : rtrim(rtrim(number_format($product->stock_qty, 3, ',', '.'), '0'), ',') }} {{ $product->unit }}
        </div>
      </div>
      @endforeach
    </div>
  </div>

  {{-- RIGHT: CART --}}
  <div class="pos-cart" id="posCart">
    <div class="mobile-cart-back" onclick="closeMobileCart()">
      <i class="bi bi-arrow-left"></i> Kembali ke Produk
    </div>
    <div class="cart-header">
      <div class="cart-title">
        {{ config('app.name') }}
        <span id="cartCount" style="font-size:12px; color:var(--muted); font-weight:400;">(0 item)</span>
      </div>
      <button class="cart-clear" onclick="clearCart()">
        <i class="bi bi-trash"></i> Kosongkan
      </button>
    </div>

    {{-- FIX #1: cart-items selalu ada, empty state di dalam --}}
    <div class="cart-items" id="cartItems">
      <div class="cart-empty" id="cartEmpty">
        <i class="bi bi-cart3" style="font-size:36px; opacity:0.2;"></i>
        <span>Keranjang masih kosong</span>
        <span style="font-size:12px;">Klik produk untuk menambahkan</span>
      </div>
    </div>

    <div class="cart-footer">
      <div class="cart-footer-inner">

        <div class="cart-row">
          <span class="label">Subtotal</span>
          <span id="subtotalDisplay">Rp 0</span>
        </div>

        <div class="discount-row">
          <label>Diskon (Rp)</label>
          <input type="number" class="discount-input" id="discountInput"
            placeholder="0" min="0" oninput="recalculate()">
        </div>

        <div class="cart-row total">
          <span>Total</span>
          <span id="totalDisplay" style="color:var(--accent);">Rp 0</span>
        </div>

        <div class="pay-methods">
          <button class="pay-btn active" onclick="selectMethod('cash', this)">
            <i class="bi bi-cash"></i> Cash
          </button>
          <button class="pay-btn" onclick="selectMethod('transfer', this)">
            <i class="bi bi-bank"></i> Transfer
          </button>
          <button class="pay-btn" onclick="selectMethod('qris', this)">
            <i class="bi bi-qr-code"></i> QRIS
          </button>
        </div>

        {{-- Transfer Details: shown inline inside scrollable footer --}}
        <div class="transfer-details" id="transferDetails">
          <div style="font-size:12px; font-weight:600; color:var(--dark); margin-bottom:8px;">Transfer ke Rekening / E-Wallet:</div>

          <div class="bank-item">
            <img src="{{ asset('images/banks/seabank.jpg') }}" alt="Seabank" class="bank-logo">
            <div class="bank-info">
              <div class="bank-name">Seabank</div>
              <div class="bank-acc">9019 8421 8639</div>
            </div>
          </div>

          <div class="bank-item">
            <img src="{{ asset('images/banks/bank jago.png') }}" alt="Bank Jago" class="bank-logo">
            <div class="bank-info">
              <div class="bank-name">Bank Jago</div>
              <div class="bank-acc">1089 6437 6572</div>
            </div>
          </div>

          <div class="bank-item">
            <img src="{{ asset('images/banks/BRI.png') }}" alt="BRI" class="bank-logo">
            <div class="bank-info">
              <div class="bank-name">BRI</div>
              <div class="bank-acc">0586 0101 6751 504</div>
            </div>
          </div>

          <div class="bank-item">
            <div style="display:flex; gap:3px; flex-wrap:wrap; width:44px; flex-shrink:0;">
              <img src="{{ asset('images/banks/dana.png') }}" alt="Dana" style="width:20px;height:20px;object-fit:cover;border-radius:4px;">
              <img src="{{ asset('images/banks/ovo.jpg') }}" alt="OVO" style="width:20px;height:20px;object-fit:cover;border-radius:4px;">
              <img src="{{ asset('images/banks/shopeepay.jpg') }}" alt="ShopeePay" style="width:20px;height:20px;object-fit:cover;border-radius:4px;">
              <img src="{{ asset('images/banks/gopay.jpeg') }}" alt="GoPay" style="width:20px;height:20px;object-fit:cover;border-radius:4px;">
            </div>
            <div class="bank-info" style="margin-left:8px;">
              <div class="bank-name">Dana · OVO · ShopeePay · GoPay</div>
              <div class="bank-acc">0895 7003 26271</div>
            </div>
          </div>
        </div>

        <div class="paid-wrap">
          <div class="paid-label">Nominal Dibayar</div>
          <input type="number" class="paid-input" id="paidInput"
            placeholder="0" min="0" oninput="recalculate()">
        </div>

        <div class="change-row" id="changeRow">
          <span class="label">Kembalian</span>
          <span class="value" id="changeDisplay">Rp 0</span>
        </div>

        <div style="margin:6px 0;">
          <input type="text" id="notesField" placeholder="Catatan (opsional)" maxlength="255"
            style="width:100%;padding:8px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:12px;font-family:'Plus Jakarta Sans',sans-serif;background:var(--body-bg);outline:none;">
        </div>

        <form method="POST" action="{{ route('pos.store') }}" id="posForm">
          @csrf
          <input type="hidden" name="payment_method" id="paymentMethodInput" value="cash">
          <input type="hidden" name="paid_amount"    id="paidAmountInput">
          <input type="hidden" name="discount"       id="discountAmountInput">
          <input type="hidden" name="notes"          id="notesInput">
          <input type="hidden" name="is_reseller_mode" id="isResellerModeInput" value="0">
          <div id="cartInputs"></div>

          <button type="button" class="btn-process" id="btnProcess" onclick="submitTransaction()">
            <i class="bi bi-check-circle"></i>
            <span id="btnText">Proses Transaksi</span>
          </button>
        </form>

      </div>{{-- /.cart-footer-inner --}}
    </div>{{-- /.cart-footer --}}
  </div>
</div>

{{-- QRIS Modal --}}
<div class="qris-overlay" id="qrisOverlay">
  <div class="qris-modal">
    <button class="qris-modal-close" onclick="closeQrisModal()">&times;</button>
    <div class="qris-modal-title">Pembayaran QRIS</div>
    <div class="qris-modal-merchant" id="qrisMerchant"></div>
    <div class="qris-modal-amount" id="qrisAmount"></div>
    <div class="qris-modal-qr" id="qrisQrContainer">
      <div class="qris-modal-loading"><i class="bi bi-arrow-repeat"></i> Generating QR...</div>
    </div>
    <div class="qris-modal-footer">Scan QR di atas menggunakan aplikasi e-wallet / m-banking</div>
  </div>
</div>

<script>
// -- STATE --
// FIX #2: cart key selalu string -- konsisten menggunakan String(id)
let cart           = {};
let activeCategory = 0;
let selectedMethod = 'cash';
let isResellerMode = false;

function toggleResellerMode() {
  isResellerMode = !isResellerMode;
  const btn = document.getElementById('resellerToggle');
  if (isResellerMode) {
    btn.style.background = '#0F766E';
    btn.style.borderColor = '#0F766E';
    btn.style.color = 'white';
  } else {
    btn.style.background = 'var(--card-bg)';
    btn.style.borderColor = 'var(--border)';
    btn.style.color = 'var(--muted)';
  }
  renderCart();
}

// -- CLOCK --
function updateClock() {
  const now = new Date();
  const el  = document.getElementById('clock');
  if (el) el.textContent = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
}
setInterval(updateClock, 1000);
updateClock();

// -- ADD TO CART --
function addToCart(el) {
  if (el.classList.contains('out-of-stock')) return;

  const id    = String(el.dataset.id);   // FIX #2: selalu string
  const name  = el.dataset.name;
  const basePrice = parseFloat(el.dataset.price);
  const wholesalePrice = parseFloat(el.dataset.wholesalePrice) || 0;
  const wholesaleMinQty = parseFloat(el.dataset.wholesaleMinQty) || 0;
  const stock = parseFloat(el.dataset.stock);
  const unit  = el.dataset.unit || 'pcs';
  const step  = (unit === 'pcs' || unit === 'pack' || unit === 'box' || unit === 'lusin') ? 1 : 0.1;

  if (cart[id]) {
    if (cart[id].qty >= stock) {
      showToast('Stok ' + name + ' hanya tersisa ' + fmtQty(stock, unit));
      return;
    }
    cart[id].qty = +(cart[id].qty + step).toFixed(3);
  } else {
    cart[id] = { id, name, basePrice, wholesalePrice, wholesaleMinQty, price: basePrice, qty: step, stock, unit, step };
  }

  renderCart();

  // Animasi klik
  el.style.transform = 'scale(0.95)';
  setTimeout(() => { el.style.transform = ''; }, 120);
}

// -- CHANGE QTY --
function changeQty(id, delta) {
  id = String(id);  // FIX #2: paksa string
  if (!cart[id]) return;

  const step = cart[id].step || 1;
  cart[id].qty = +(cart[id].qty + delta * step).toFixed(3);

  if (cart[id].qty <= 0) {
    delete cart[id];
  } else if (cart[id].qty > cart[id].stock) {
    cart[id].qty = +cart[id].stock.toFixed(3);
    showToast('Stok maksimal: ' + fmtQty(cart[id].stock, cart[id].unit));
  }
  renderCart();
}

function fmtQty(val, unit) {
  if (unit === 'pcs' || unit === 'pack' || unit === 'box' || unit === 'lusin') return Math.floor(val);
  return val % 1 === 0 ? val : parseFloat(val.toFixed(3));
}

function removeItem(id) {
  id = String(id);  // FIX #2: paksa string
  delete cart[id];
  renderCart();
}

function clearCart() {
  cart = {};
  renderCart();
}

// -- RENDER CART --
// FIX #1: Tidak manipulasi DOM container secara destructive
// Gunakan innerHTML langsung, jaga cartEmpty terpisah
function renderCart() {
  const container = document.getElementById('cartItems');
  const empty     = document.getElementById('cartEmpty');
  const items     = Object.values(cart);

  if (items.length === 0) {
    // Tampilkan empty state
    container.innerHTML = '';
    container.appendChild(empty);
    empty.style.display = 'flex';
    document.getElementById('cartCount').textContent = '(0 item)';
    recalculate();
    return;
  }

  // Sembunyikan empty state dulu
  empty.style.display = 'none';
  if (!container.contains(empty)) {
    container.appendChild(empty); // jaga empty tetap di DOM
  }

  // Render items -- JANGAN timpa seluruh container, buat wrapper
  let itemsHTML = '';
  items.forEach(function(item) {
    let activePrice = item.basePrice;
    if (item.wholesalePrice > 0 && (isResellerMode || (item.wholesaleMinQty > 0 && item.qty >= item.wholesaleMinQty))) {
      activePrice = item.wholesalePrice;
    }
    item.price = activePrice; // Update active price for recalculate()

    itemsHTML += '<div class="cart-item" data-item-id="' + item.id + '">' +
      '<div style="flex:1; min-width:0;">' +
        '<div class="cart-item-name">' + item.name + '</div>' +
        '<div class="cart-item-price">' + (activePrice !== item.basePrice ? '<span style="color:#16A34A; font-weight:600;"><i class="bi bi-tag-fill"></i> Grosir</span> ' : '') + 'Rp ' + fmt(item.price) + ' / ' + (item.unit || 'pcs') + '</div>' +
      '</div>' +
      '<div class="qty-ctrl">' +
        '<button class="qty-btn" type="button" onclick="changeQty(\'' + item.id + '\', -1)">-</button>' +
        '<span class="qty-val">' + fmtQty(item.qty, item.unit) + '</span>' +
        '<button class="qty-btn" type="button" onclick="changeQty(\'' + item.id + '\', 1)">+</button>' +
      '</div>' +
      '<div class="cart-item-total">Rp ' + fmt(item.price * item.qty) + '</div>' +
      '<button class="remove-btn" type="button" onclick="removeItem(\'' + item.id + '\')">' +
        '<i class="bi bi-x-circle"></i>' +
      '</button>' +
    '</div>';
  });

  // Hapus item lama, sisakan cartEmpty
  Array.from(container.children).forEach(function(child) {
    if (child.id !== 'cartEmpty') child.remove();
  });

  // Insert items sebelum cartEmpty
  container.insertAdjacentHTML('afterbegin', itemsHTML);

  document.getElementById('cartCount').textContent = '(' + items.length + ' item)';
  updateMobileBadge();
  recalculate();
}

// -- RECALCULATE --
// FIX #3: Logika enable/disable tombol diperbaiki
function recalculate() {
  const items    = Object.values(cart);
  const subtotal = items.reduce(function(s, i) { return s + i.price * i.qty; }, 0);
  const discount = parseFloat(document.getElementById('discountInput').value) || 0;
  const total    = Math.max(0, subtotal - discount);
  const paid     = parseFloat(document.getElementById('paidInput').value) || 0;
  const change   = paid - total;

  document.getElementById('subtotalDisplay').textContent = 'Rp ' + fmt(subtotal);
  document.getElementById('totalDisplay').textContent    = 'Rp ' + fmt(total);

  const changeRow = document.getElementById('changeRow');
  const changeEl  = document.getElementById('changeDisplay');

  if (paid > 0) {
    changeEl.textContent = 'Rp ' + fmt(Math.abs(change));
    changeRow.className  = change >= 0 ? 'change-row' : 'change-row negative';
    changeRow.querySelector('.label').textContent = change >= 0 ? 'Kembalian' : 'Kurang';
  } else {
    changeEl.textContent = 'Rp 0';
    changeRow.className  = 'change-row';
    changeRow.querySelector('.label').textContent = 'Kembalian';
  }

  // FIX #3: Cek semua kondisi dengan benar
  // Transfer/QRIS: paid boleh = total (exact), Cash: paid harus >= total
  const hasItems = items.length > 0;
  const hasTotal = total > 0;
  const hasPaid  = paid > 0;
  const isPaidEnough = paid >= total;

  const btn    = document.getElementById('btnProcess');
  const isReady = hasItems && hasTotal && hasPaid && isPaidEnough;

  btn.className = isReady ? 'btn-process ready' : 'btn-process';
}

// -- SUBMIT --
let isSubmitting = false;

function submitTransaction() {
  const btn = document.getElementById('btnProcess');
  if (!btn.classList.contains('ready') || isSubmitting) return;

  const items    = Object.values(cart);
  if (items.length === 0) { showToast('Keranjang masih kosong!'); return; }

  isSubmitting = true;

  const discount = parseFloat(document.getElementById('discountInput').value) || 0;
  const paid     = parseFloat(document.getElementById('paidInput').value) || 0;
  const notes    = document.getElementById('notesField').value || '';

  // Build hidden inputs
  let inputsHtml = '';
  items.forEach(function(item, i) {
    inputsHtml += '<input type="hidden" name="items[' + i + '][id]" value="' + item.id + '">';
    inputsHtml += '<input type="hidden" name="items[' + i + '][qty]" value="' + item.qty + '">';
    inputsHtml += '<input type="hidden" name="items[' + i + '][discount]" value="0">';
  });

  document.getElementById('cartInputs').innerHTML      = inputsHtml;
  document.getElementById('paidAmountInput').value     = paid;
  document.getElementById('discountAmountInput').value = discount;
  document.getElementById('paymentMethodInput').value  = selectedMethod;
  document.getElementById('notesInput').value          = notes;
  document.getElementById('isResellerModeInput').value = isResellerMode ? '1' : '0';

  // Loading state
  btn.className = 'btn-process';
  document.getElementById('btnText').textContent = 'Memproses...';

  document.getElementById('posForm').submit();
}

// -- FILTER --
function filterCategory(catId, btn) {
  activeCategory = catId;
  document.querySelectorAll('.cat-btn').forEach(function(b) { b.classList.remove('active'); });
  btn.classList.add('active');
  filterProducts();
}

function filterProducts() {
  const q     = document.getElementById('searchInput').value.toLowerCase();
  const cards = document.querySelectorAll('.product-card');
  cards.forEach(function(card) {
    const matchSearch   = card.dataset.name.toLowerCase().includes(q) ||
                          card.dataset.sku.toLowerCase().includes(q);
    const matchCategory = activeCategory === 0 ||
                          parseInt(card.dataset.category) === activeCategory;
    card.style.display  = (matchSearch && matchCategory) ? '' : 'none';
  });
}

// -- PAYMENT METHOD --
// FIX #4: Transfer & QRIS auto-isi paid_amount = total
function selectMethod(method, btn) {
  selectedMethod = method;
  document.querySelectorAll('.pay-btn').forEach(function(b) { b.classList.remove('active'); });
  btn.classList.add('active');

  // Auto-isi nominal jika transfer atau QRIS (bayar exact)
  if (method === 'transfer' || method === 'qris') {
    const items    = Object.values(cart);
    const subtotal = items.reduce(function(s, i) { return s + i.price * i.qty; }, 0);
    const discount = parseFloat(document.getElementById('discountInput').value) || 0;
    const total    = Math.max(0, subtotal - discount);
    if (total > 0) {
      document.getElementById('paidInput').value = total;
    }
  }

  // Show Transfer Details if transfer selected
  if (method === 'transfer') {
    document.getElementById('transferDetails').classList.add('show');
  } else {
    document.getElementById('transferDetails').classList.remove('show');
  }

  // Show QRIS modal when selecting QRIS and cart has items
  if (method === 'qris') {
    const items = Object.values(cart);
    const subtotal = items.reduce(function(s, i) { return s + i.price * i.qty; }, 0);
    const discount = parseFloat(document.getElementById('discountInput').value) || 0;
    const total = Math.max(0, subtotal - discount);
    if (total > 0) showQrisModal(Math.round(total));
  }

  recalculate();
}

// -- QRIS MODAL --
function showQrisModal(amount) {
  const overlay   = document.getElementById('qrisOverlay');
  const container = document.getElementById('qrisQrContainer');
  const amountEl  = document.getElementById('qrisAmount');
  const merchant  = document.getElementById('qrisMerchant');

  amountEl.textContent = 'Rp ' + fmt(amount);
  merchant.textContent = '';
  container.innerHTML  = '<div class="qris-modal-loading"><i class="bi bi-arrow-repeat"></i> Generating QR...</div>';
  overlay.classList.add('show');

  fetch('{{ route("pos.qris.generate") }}', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
      'Accept': 'application/json',
    },
    body: JSON.stringify({ amount: amount }),
  })
  .then(function(r) { return r.json(); })
  .then(function(data) {
    if (data.success) {
      container.innerHTML = data.qr_svg;
      if (data.merchant && data.merchant.name) {
        merchant.textContent = data.merchant.name + ' -- ' + data.merchant.city;
      }
    } else {
      container.innerHTML = '<div class="qris-modal-loading" style="color:#DC2626;"><i class="bi bi-exclamation-circle"></i> ' + (data.error || 'Gagal generate QRIS') + '</div>';
    }
  })
  .catch(function(err) {
    container.innerHTML = '<div class="qris-modal-loading" style="color:#DC2626;"><i class="bi bi-exclamation-circle"></i> Gagal menghubungi server</div>';
  });
}

function closeQrisModal() {
  document.getElementById('qrisOverlay').classList.remove('show');
}

// -- BARCODE SCANNER LISTENER --
// Barcode scanners send chars rapidly then Enter.
// We buffer rapid keystrokes (<50ms gap) and match SKU on Enter.
(function() {
  let barcodeBuffer = '';
  let lastKeyTime   = 0;
  const THRESHOLD   = 50; // ms between keystrokes

  document.addEventListener('keydown', function(e) {
    // Ignore if typing in an input field (search, discount, paid, notes)
    const tag = e.target.tagName;
    if (tag === 'INPUT' || tag === 'TEXTAREA' || tag === 'SELECT') return;

    const now = Date.now();

    if (e.key === 'Enter' && barcodeBuffer.length >= 3) {
      e.preventDefault();
      handleBarcodeScan(barcodeBuffer.trim());
      barcodeBuffer = '';
      return;
    }

    // Reset buffer if too slow (manual typing)
    if (now - lastKeyTime > THRESHOLD && barcodeBuffer.length > 0) {
      barcodeBuffer = '';
    }

    if (e.key.length === 1) {
      barcodeBuffer += e.key;
      lastKeyTime = now;
    }
  });

  function handleBarcodeScan(sku) {
    const cards = document.querySelectorAll('.product-card');
    let found = false;

    cards.forEach(function(card) {
      if (card.dataset.sku === sku) {
        found = true;
        addToCart(card);
        showToast('Barcode: ' + sku + ' ditambahkan');
      }
    });

    if (!found) {
      showToast('Produk dengan SKU "' + sku + '" tidak ditemukan');
    }
  }
})();

// -- MOBILE CART --
function openMobileCart() {
  document.getElementById('posCart').classList.add('open');
  document.getElementById('mobileCartToggle').style.display = 'none';
}
function closeMobileCart() {
  document.getElementById('posCart').classList.remove('open');
  document.getElementById('mobileCartToggle').style.display = '';
}
function updateMobileBadge() {
  var count = Object.keys(cart).length;
  var badge = document.getElementById('mobileCartBadge');
  if (count > 0) {
    badge.textContent = count;
    badge.style.display = 'flex';
  } else {
    badge.style.display = 'none';
  }
}

// -- HELPERS --
function fmt(num) {
  return Math.round(num).toLocaleString('id-ID');
}

function showToast(msg) {
  // Hapus toast lama jika ada
  const old = document.getElementById('posToast');
  if (old) old.remove();

  const t  = document.createElement('div');
  t.id     = 'posToast';
  t.textContent = msg;
  t.style.cssText = 'position:fixed;bottom:24px;left:50%;transform:translateX(-50%);background:#1C1917;color:white;padding:10px 20px;border-radius:10px;font-size:13px;z-index:9999;pointer-events:none;';
  document.body.appendChild(t);
  setTimeout(function() { if (t) t.remove(); }, 2200);
}
</script>

</body>
</html>
