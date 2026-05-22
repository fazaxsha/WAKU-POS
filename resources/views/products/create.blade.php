{{-- ============================================================ --}}
{{-- resources/views/products/create.blade.php                  --}}
{{-- ============================================================ --}}
@extends('layouts.app')
 
@section('title', 'Tambah Produk')
@section('page-title', 'Tambah Produk Baru')
 
@section('content')
 
<div class="flex items-center gap-2 mb-4">
  <a href="{{ route('products.index') }}" class="topbar-btn no-underline" style="width:32px; height:32px;">
    <i class="bi bi-arrow-left"></i>
  </a>
  <h5 class="font-semibold mb-0" style="font-size:16px;">Tambah Produk Baru</h5>
</div>
 
<form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data">
  @csrf
  @include('products._form', ['product' => null])
</form>
 
@endsection