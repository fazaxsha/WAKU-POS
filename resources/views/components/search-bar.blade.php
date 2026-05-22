{{-- resources/views/components/search-bar.blade.php --}}
{{--
  Props:
  - action (string)       : route untuk form action
  - placeholder (string)  : placeholder search input
  - filters (array)       : [ ['name'=>'', 'label'=>'', 'options'=>[val=>label], 'type'=>'select|date'] ]
  - per_page (bool)       : tampilkan per-page selector
  - export_pdf (string)   : route name untuk export PDF (opsional)
  - export_excel (string) : route name untuk export Excel (opsional)
--}}

@props([
    'action'       => '',
    'placeholder'  => 'Cari...',
    'filters'      => [],
    'perPage'      => true,
    'exportPdf'    => null,
    'exportExcel'  => null,
])

<div class="card mb-4" x-data="searchBar()" x-init="init()">
  <div class="p-3">
    <form method="GET" action="{{ $action }}" id="searchForm">

      <div class="flex flex-wrap items-end gap-2">

        {{-- Search input --}}
        <div class="w-full md:w-auto md:flex-1 md:max-w-xs">
          <div class="flex items-center border border-slate-200 rounded-lg bg-white overflow-hidden focus-within:border-teal-500 focus-within:ring-2 focus-within:ring-teal-100 transition-all">
            <!-- <span class="pl-4 pr-10 text-slate-400 pointer-events-none"><i class="bi bi-search text-sm"></i></span> -->
            <input
              type="text"
              name="search"
              value="{{ request('search') }}"
              class="flex-1 py-2 pr-3 text-sm text-slate-900 placeholder:text-slate-400 outline-none bg-transparent border-0 focus:ring-0"
              placeholder="{{ $placeholder }}"
              x-model="searchTerm"
              x-on:input.debounce.400ms="liveSearch()"
            >
            @if(request('search'))
            <button type="button" class="px-2 text-slate-400 hover:text-slate-600 transition-colors"
              x-on:click="clearSearch()">
              <i class="bi bi-x text-sm"></i>
            </button>
            @endif
          </div>
        </div>

        {{-- Dynamic filters --}}
        @foreach ($filters as $filter)
        <div class="w-full sm:w-auto">
          @if ($filter['type'] === 'select')
            <select name="{{ $filter['name'] }}"
              class="h-9 text-sm pl-3 pr-8 text-slate-700 border border-slate-200 rounded-lg bg-white
                     appearance-none bg-no-repeat bg-[length:16px_16px] bg-[position:right_8px_center]
                     bg-[url('data:image/svg+xml;charset=UTF-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%2216%22%20height%3D%2216%22%20viewBox%3D%220%200%2016%2016%22%3E%3Cpath%20fill%3D%22%2394A3B8%22%20d%3D%22M4.427%206.427l3.396%203.396a.25.25%200%2000.354%200l3.396-3.396A.25.25%200%2011.396%206H4.604a.25.25%200%2000-.177.427z%22%2F%3E%3C%2Fsvg%3E')]
                     focus:outline-none focus:border-teal-500 focus:ring-2 focus:ring-teal-100
                     cursor-pointer transition-all"
              onchange="this.form.submit()">
              <option value="">{{ $filter['label'] }}</option>
              @foreach ($filter['options'] as $val => $label)
                <option value="{{ $val }}" {{ request($filter['name']) == $val ? 'selected' : '' }}>
                  {{ $label }}
                </option>
              @endforeach
            </select>
          @elseif ($filter['type'] === 'date')
            <input type="date" name="{{ $filter['name'] }}"
              value="{{ request($filter['name']) }}"
              class="h-9 text-sm px-3 text-slate-700 border border-slate-200 rounded-lg bg-white
                     focus:outline-none focus:border-teal-500 focus:ring-2 focus:ring-teal-100
                     transition-all"
              title="{{ $filter['label'] }}"
              onchange="this.form.submit()">
          @endif
        </div>
        @endforeach

        {{-- Per page --}}
        @if ($perPage)
        <div>
          <select name="per_page"
            class="h-9 text-sm pl-3 pr-8 text-slate-700 border border-slate-200 rounded-lg bg-white
                   appearance-none bg-no-repeat bg-[length:16px_16px] bg-[position:right_8px_center]
                   bg-[url('data:image/svg+xml;charset=UTF-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%2216%22%20height%3D%2216%22%20viewBox%3D%220%200%2016%2016%22%3E%3Cpath%20fill%3D%22%2394A3B8%22%20d%3D%22M4.427%206.427l3.396%203.396a.25.25%200%2000.354%200l3.396-3.396A.25.25%200%2011.396%206H4.604a.25.25%200%2000-.177.427z%22%2F%3E%3C%2Fsvg%3E')]
                   focus:outline-none focus:border-teal-500 focus:ring-2 focus:ring-teal-100
                   cursor-pointer transition-all"
            onchange="this.form.submit()">
            @foreach ([10, 15, 25, 50] as $n)
              <option value="{{ $n }}" {{ request('per_page', 15) == $n ? 'selected' : '' }}>{{ $n }} / hal</option>
            @endforeach
          </select>
        </div>
        @endif

        {{-- Buttons --}}
        <div class="flex gap-2 ml-auto">
          <button type="submit" class="btn-primary h-9 px-4 text-sm">
            <i class="bi bi-funnel"></i> Filter
          </button>

          @if (request()->anyFilled(['search', 'per_page', ...(array_column($filters, 'name'))]))
          <a href="{{ $action }}" class="btn-outline-sm h-9 px-4 text-sm">
            <i class="bi bi-x-circle"></i> Reset
          </a>
          @endif

          @if ($exportPdf)
          @can('report.export')
          <a href="{{ route($exportPdf, request()->query()) }}" class="btn-outline-sm h-9 px-4 text-sm">
            <i class="bi bi-file-pdf text-red-600"></i> PDF
          </a>
          @endcan
          @endif

          @if ($exportExcel)
          @can('report.export')
          <a href="{{ route($exportExcel, request()->query()) }}" class="btn-outline-sm h-9 px-4 text-sm">
            <i class="bi bi-file-excel text-emerald-600"></i> Excel
          </a>
          @endcan
          @endif
        </div>

      </div>

      {{-- Preserve existing query params yang tidak ada di form --}}
      @foreach (request()->except(['search', 'page', 'per_page', ...(array_column($filters, 'name'))]) as $key => $val)
        <input type="hidden" name="{{ $key }}" value="{{ $val }}">
      @endforeach

    </form>
  </div>

  {{-- Live search indicator --}}
  <div x-show="isSearching" x-transition
    class="px-4 py-2 border-t border-slate-100 text-xs text-slate-400 flex items-center gap-2"
    style="display:none;">
    <i class="bi bi-arrow-repeat spin"></i> Mencari...
  </div>
</div>

<style>
@keyframes spin { to { transform: rotate(360deg); } }
.spin { display: inline-block; animation: spin 0.8s linear infinite; }
</style>

<script>
function searchBar() {
  return {
    searchTerm: '{{ request('search') }}',
    isSearching: false,
    debounceTimer: null,

    init() {
      // Tidak ada inisialisasi khusus
    },

    liveSearch() {
      // Debounce sudah ditangani Alpine x-on:input.debounce.400ms
      // Auto-submit form saat ketik
      this.isSearching = true;
      this.$nextTick(() => {
        document.getElementById('searchForm').submit();
      });
    },

    clearSearch() {
      this.searchTerm = '';
      // Reset ke URL tanpa search param
      const url = new URL(window.location.href);
      url.searchParams.delete('search');
      url.searchParams.delete('page');
      window.location.href = url.toString();
    }
  }
}
</script>