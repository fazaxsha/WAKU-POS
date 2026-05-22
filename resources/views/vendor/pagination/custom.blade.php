{{-- resources/views/vendor/pagination/custom.blade.php --}}
{{-- Daftarkan di AppServiceProvider: Paginator::defaultView('vendor.pagination.custom') --}}

@if ($paginator->hasPages())
<div class="flex items-center justify-between flex-wrap gap-2"
     style="font-size:13px;">

  {{-- Info --}}
  <div class="text-slate-500">
    Menampilkan
    <strong class="text-slate-900">{{ $paginator->firstItem() }}</strong>
    –
    <strong class="text-slate-900">{{ $paginator->lastItem() }}</strong>
    dari
    <strong class="text-slate-900">{{ $paginator->total() }}</strong>
    data
  </div>

  {{-- Navigation --}}
  <div class="flex items-center gap-1">

    {{-- Prev --}}
    @if ($paginator->onFirstPage())
      <span class="page-nav disabled">
        <i class="bi bi-chevron-left"></i>
      </span>
    @else
      <a href="{{ $paginator->previousPageUrl() }}" class="page-nav">
        <i class="bi bi-chevron-left"></i>
      </a>
    @endif

    {{-- Pages --}}
    @foreach ($elements as $element)
      @if (is_string($element))
        <span class="page-item-dot">···</span>
      @endif

      @if (is_array($element))
        @foreach ($element as $page => $url)
          @if ($page == $paginator->currentPage())
            <span class="page-item active">{{ $page }}</span>
          @else
            <a href="{{ $url }}" class="page-item">{{ $page }}</a>
          @endif
        @endforeach
      @endif
    @endforeach

    {{-- Next --}}
    @if ($paginator->hasMorePages())
      <a href="{{ $paginator->nextPageUrl() }}" class="page-nav">
        <i class="bi bi-chevron-right"></i>
      </a>
    @else
      <span class="page-nav disabled">
        <i class="bi bi-chevron-right"></i>
      </span>
    @endif

  </div>

</div>

<style>
.page-nav, .page-item {
  display: inline-flex; align-items: center; justify-content: center;
  min-width: 30px; height: 30px; padding: 0 6px;
  border-radius: 7px; font-size: 12px; font-weight: 500;
  border: 1.5px solid #E2E8F0;
  background: white; color: #0F172A;
  text-decoration: none; transition: all 0.15s; cursor: pointer;
}
.page-nav:hover, .page-item:hover {
  border-color: #0D9488; color: #0D9488; background: #F0FDFA;
}
.page-item.active {
  background: #0F172A; color: white;
  border-color: #0F172A; cursor: default;
}
.page-nav.disabled {
  opacity: 0.35; cursor: not-allowed; pointer-events: none;
}
.page-item-dot {
  font-size: 13px; color: #64748B;
  padding: 0 4px; user-select: none;
}
</style>
@endif