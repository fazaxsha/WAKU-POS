{{-- ============================================================ --}}
{{-- resources/views/products/edit.blade.php                    --}}
{{-- ============================================================ --}}
@extends('layouts.app')
 
@section('title', 'Edit Produk')
@section('page-title', 'Edit Produk')
 
@section('content')
 
<div class="flex items-center gap-2 mb-4">
  <a href="{{ route('products.index') }}" class="topbar-btn no-underline" style="width:32px; height:32px;">
    <i class="bi bi-arrow-left"></i>
  </a>
  <h5 class="font-semibold mb-0" style="font-size:16px;">Edit: {{ $product->name }}</h5>
</div>
 
<form method="POST" action="{{ route('products.update', $product) }}" enctype="multipart/form-data">
  @csrf @method('PUT')
  @include('products._form', ['product' => $product])
</form>
 
@endsection