<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'Dashboard') — {{ config('app.name', 'WAKU-POS') }}</title>
  <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

  {{-- Google Fonts (preconnect + preload for faster LCP) --}}
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&family=DM+Mono:wght@400;500&display=swap">
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">

  {{-- Bootstrap Icons --}}
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

  {{-- Tailwind CSS & Vite --}}
  @vite(['resources/css/app.css', 'resources/js/app.js'])

  <style>
    [x-cloak] { display: none !important; }
    /* Utility hide scrollbar for sidebar */
    .hidden-scrollbar::-webkit-scrollbar { display: none; }
    .hidden-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    
    /* Animation */
    .fade-in-up { animation: fadeInUp 0.3s ease-out forwards; }
    @keyframes fadeInUp {
      from { opacity: 0; transform: translateY(10px); }
      to { opacity: 1; transform: translateY(0); }
    }
  </style>

  @stack('styles')
</head>
<body class="font-sans text-slate-900 min-h-screen" style="background:#F4F6F8;">

{{-- Mobile Sidebar Overlay --}}
<div id="sidebarOverlay" class="fixed inset-0 bg-slate-900/50 z-40 hidden md:hidden backdrop-blur-sm transition-opacity" onclick="closeSidebar()"></div>

{{-- Sidebar Component --}}
<x-sidebar />

{{-- Main Content Wrapper --}}
<div class="md:ml-64 flex flex-col min-h-screen transition-all duration-300">
  
  {{-- Topbar --}}
  <header class="sticky top-0 z-30 bg-white/80 backdrop-blur-md border-b border-slate-200 h-16 flex items-center justify-between px-4 sm:px-6 lg:px-8">
    <div class="flex items-center gap-4">
      <button onclick="openSidebar()" class="md:hidden w-10 h-10 flex items-center justify-center rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50 transition-colors">
        <i class="bi bi-list text-xl"></i>
      </button>
      <h1 class="text-lg font-semibold text-slate-900">@yield('page-title', 'Dashboard')</h1>
    </div>

    <div class="flex items-center gap-3">

      {{-- Notification Bell --}}
      <div class="relative" x-data="notificationPanel()" x-init="fetchNotifications()">
        <button @click="toggle()" class="relative w-10 h-10 flex items-center justify-center rounded-full text-slate-500 hover:bg-slate-100 transition-colors" title="Notifikasi">
          <i class="bi bi-bell text-lg"></i>
          <span x-show="unreadCount > 0" x-cloak
                class="absolute top-1.5 right-1.5 min-w-[18px] h-[18px] flex items-center justify-center rounded-full bg-red-500 text-white text-[10px] font-bold leading-none px-1 border-2 border-white"
                x-text="unreadCount > 9 ? '9+' : unreadCount"></span>
        </button>

        {{-- Dropdown Panel --}}
        <div x-show="open" x-cloak
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-y-1 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 translate-y-1 scale-95"
             @click.outside="open = false"
             class="absolute right-0 top-12 w-80 sm:w-96 bg-white border border-slate-200 rounded-2xl shadow-2xl overflow-hidden z-50">

          {{-- Header --}}
          <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-sm font-bold text-slate-900">Notifikasi</h3>
            <button x-show="unreadCount > 0" @click="markAllRead()" class="text-xs font-medium text-teal-600 hover:text-teal-800 transition-colors">
              Tandai semua dibaca
            </button>
          </div>

          {{-- List --}}
          <div class="max-h-80 overflow-y-auto divide-y divide-slate-50">

            {{-- Loading state --}}
            <template x-if="loading">
              <div class="py-10 text-center text-slate-400">
                <i class="bi bi-arrow-repeat text-xl animate-spin inline-block"></i>
                <div class="text-xs mt-2">Memuat...</div>
              </div>
            </template>

            {{-- Empty state --}}
            <template x-if="!loading && notifications.length === 0">
              <div class="py-10 text-center">
                <i class="bi bi-bell-slash text-3xl text-slate-300 block mb-2"></i>
                <div class="text-sm text-slate-400">Belum ada notifikasi</div>
              </div>
            </template>

            {{-- Notification items --}}
            <template x-for="n in notifications" :key="n.id">
              <a :href="n.url || '#'" @click="markRead(n)"
                 class="flex items-start gap-3 px-4 py-3 hover:bg-slate-50 transition-colors cursor-pointer"
                 :class="!n.read ? 'bg-teal-50/40' : ''">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0 mt-0.5"
                     :class="{
                       'bg-amber-100 text-amber-600': n.color === 'amber',
                       'bg-teal-100 text-teal-600': n.color === 'teal',
                       'bg-red-100 text-red-600': n.color === 'red',
                       'bg-blue-100 text-blue-600': n.color === 'blue',
                       'bg-slate-100 text-slate-500': !['amber','teal','red','blue'].includes(n.color)
                     }">
                  <i class="bi" :class="n.icon || 'bi-bell'"></i>
                </div>
                <div class="flex-1 min-w-0">
                  <div class="text-sm font-semibold text-slate-800 truncate" x-text="n.title"></div>
                  <div class="text-xs text-slate-500 mt-0.5 line-clamp-2" x-text="n.message"></div>
                  <div class="text-[10px] text-slate-400 mt-1 font-mono" x-text="n.time"></div>
                </div>
                <div x-show="!n.read" class="w-2 h-2 rounded-full bg-teal-500 flex-shrink-0 mt-2"></div>
              </a>
            </template>

          </div>
        </div>
      </div>

      <div class="hidden sm:flex items-center gap-2 text-xs font-medium text-slate-500 bg-slate-100 px-3 py-1.5 rounded-full">
        <i class="bi bi-calendar3"></i>
        {{ now()->translatedFormat('d M Y') }}
      </div>
    </div>
  </header>

  {{-- Main Content Area --}}
  <main class="flex-1 p-4 sm:p-6 lg:p-8 fade-in-up">
    
    {{-- Global Alerts --}}
    @if (session('success'))
      <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl flex items-center gap-3 shadow-sm" role="alert">
        <i class="bi bi-check-circle-fill text-lg"></i>
        <span class="flex-1 text-sm font-medium">{{ session('success') }}</span>
        <button type="button" class="text-green-500 hover:text-green-700 focus:outline-none" onclick="this.parentElement.remove()">&times;</button>
      </div>
    @endif

    @if (session('error'))
      <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl flex items-center gap-3 shadow-sm" role="alert">
        <i class="bi bi-exclamation-circle-fill text-lg"></i>
        <span class="flex-1 text-sm font-medium">{{ session('error') }}</span>
        <button type="button" class="text-red-500 hover:text-red-700 focus:outline-none" onclick="this.parentElement.remove()">&times;</button>
      </div>
    @endif

    {{-- Page Content --}}
    @yield('content')

  </main>
</div>

<script>
  function openSidebar() {
    document.getElementById('sidebar').classList.remove('-translate-x-full');
    document.getElementById('sidebarOverlay').classList.remove('hidden');
  }
  function closeSidebar() {
    document.getElementById('sidebar').classList.add('-translate-x-full');
    document.getElementById('sidebarOverlay').classList.add('hidden');
  }
</script>

{{-- Alpine.js — required for search-bar, dropdowns & interactive components --}}
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<script>
function notificationPanel() {
  return {
    open: false,
    loading: false,
    notifications: [],
    unreadCount: 0,

    toggle() {
      this.open = !this.open;
      if (this.open) this.fetchNotifications();
    },

    async fetchNotifications() {
      this.loading = true;
      try {
        const res = await fetch('/notifications', {
          headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        });
        const data = await res.json();
        this.notifications = data.notifications;
        this.unreadCount = data.unread_count;
      } catch (e) {
        console.error('Failed to fetch notifications', e);
      } finally {
        this.loading = false;
      }
    },

    async markRead(n) {
      if (n.read) return;
      n.read = true;
      this.unreadCount = Math.max(0, this.unreadCount - 1);
      try {
        await fetch('/notifications/' + n.id + '/read', {
          method: 'PATCH',
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
            'Accept': 'application/json',
          }
        });
      } catch (e) { console.error(e); }
    },

    async markAllRead() {
      this.notifications.forEach(n => n.read = true);
      this.unreadCount = 0;
      try {
        await fetch('/notifications/read-all', {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
            'Accept': 'application/json',
          }
        });
      } catch (e) { console.error(e); }
    }
  }
}
</script>

@stack('scripts')

</body>
</html>