{{-- ============================================================ --}}
{{-- resources/views/categories/create.blade.php                 --}}
{{-- ============================================================ --}}
@extends('layouts.app')
 
@section('title', 'Tambah Kategori')
@section('page-title', 'Tambah Kategori')
 
@section('content')
 
<div class="flex items-center gap-2 mb-4">
  <a href="{{ route('categories.index') }}" class="topbar-btn no-underline" style="width:32px; height:32px;">
    <i class="bi bi-arrow-left"></i>
  </a>
  <h5 class="font-semibold mb-0" style="font-size:16px;">Tambah Kategori Baru</h5>
</div>
 
<div class="max-w-lg mx-auto">
  <div>
    <div class="card p-4">
      <form method="POST" action="{{ route('categories.store') }}">
        @csrf
        @include('categories._form', ['category' => null])
      </form>
    </div>
  </div>
</div>
 
@endsection