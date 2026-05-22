<aside id="sidebar" class="fixed top-0 left-0 w-64 h-full bg-white border-r border-slate-200 flex flex-col z-50 transform -translate-x-full md:translate-x-0 transition-transform duration-300 overflow-y-auto hidden-scrollbar">
    <div class="flex items-center gap-3 p-5 border-b border-slate-100">
        <div class="w-9 h-9 rounded-lg bg-teal-600 flex items-center justify-center text-white font-bold text-lg shadow-md shadow-teal-600/20">
            🌤️
        </div>
        <div>
            <div class="text-sm font-bold text-slate-900">{{ config('app.name', 'WAKU-POS') }}</div>
            <div class="text-[10px] font-mono text-slate-500 uppercase tracking-widest">Retail Management</div>
        </div>
    </div>

    <nav class="flex-1 p-3 space-y-1">
        <div class="px-3 pt-4 pb-2 text-[10px] font-medium tracking-wider text-slate-400 uppercase">Utama</div>

        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('dashboard') ? 'bg-teal-50 text-teal-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
            <i class="bi bi-grid-1x2 text-base w-5 text-center"></i> Dashboard
        </a>

        @can('pos.access')
        <a href="{{ route('pos.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('pos.*') ? 'bg-teal-50 text-teal-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
            <i class="bi bi-receipt text-base w-5 text-center"></i> Kasir / POS
        </a>
        @endcan

        @can('product.view')
        <div class="px-3 pt-4 pb-2 text-[10px] font-medium tracking-wider text-slate-400 uppercase">Inventaris</div>
        <a href="{{ route('products.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('products.*') ? 'bg-teal-50 text-teal-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
            <i class="bi bi-box-seam text-base w-5 text-center"></i> Produk
        </a>
        <a href="{{ route('categories.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('categories.*') ? 'bg-teal-50 text-teal-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
            <i class="bi bi-tag text-base w-5 text-center"></i> Kategori
        </a>
        <a href="{{ route('stock-opnames.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('stock-opnames.*') ? 'bg-teal-50 text-teal-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
            <i class="bi bi-clipboard-check text-base w-5 text-center"></i> Stok Opname
        </a>
        @endcan

        @can('purchase.create')
        <div class="px-3 pt-4 pb-2 text-[10px] font-medium tracking-wider text-slate-400 uppercase">Pembelian</div>
        <a href="{{ route('purchases.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('purchases.*') ? 'bg-teal-50 text-teal-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
            <i class="bi bi-cart-plus text-base w-5 text-center"></i> Purchase Order
        </a>
        <a href="{{ route('suppliers.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('suppliers.*') ? 'bg-teal-50 text-teal-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
            <i class="bi bi-truck text-base w-5 text-center"></i> Supplier
        </a>
        @endcan

        @can('report.view')
        <div class="px-3 pt-4 pb-2 text-[10px] font-medium tracking-wider text-slate-400 uppercase">Laporan</div>
        <a href="{{ route('reports.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('reports.*') ? 'bg-teal-50 text-teal-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
            <i class="bi bi-bar-chart-line text-base w-5 text-center"></i> Laporan
        </a>
        @endcan

        @can('user.manage')
        <div class="px-3 pt-4 pb-2 text-[10px] font-medium tracking-wider text-slate-400 uppercase">Pengaturan</div>
        <a href="{{ route('users.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('users.*') ? 'bg-teal-50 text-teal-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
            <i class="bi bi-people text-base w-5 text-center"></i> Manajemen User
        </a>
        @endcan
    </nav>

    <div class="p-4 border-t border-slate-100 flex items-center gap-3">
        <div class="w-10 h-10 rounded-full bg-slate-900 text-white flex items-center justify-center font-bold text-sm shadow-sm">
            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
        </div>
        <div class="flex-1 min-w-0">
            <div class="text-sm font-semibold text-slate-900 truncate">{{ auth()->user()->name }}</div>
            <div class="text-xs text-slate-500 capitalize truncate">{{ auth()->user()->getRoleNames()->first() ?? 'user' }}</div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="text-slate-400 hover:text-red-500 transition-colors p-1" title="Logout">
                <i class="bi bi-box-arrow-right text-lg"></i>
            </button>
        </form>
    </div>
</aside>
