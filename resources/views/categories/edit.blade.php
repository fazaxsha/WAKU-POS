{{-- ============================================================ --}}
{{-- resources/views/categories/edit.blade.php                   --}}
{{-- ============================================================ --}}
@extends('layouts.app')
 
@section('title', 'Edit Kategori')
@section('page-title', 'Edit Kategori')
 
@section('content')
 
<div class="flex items-center gap-2 mb-4">
  <a href="{{ route('categories.index') }}" class="topbar-btn no-underline" style="width:32px; height:32px;">
    <i class="bi bi-arrow-left"></i>
  </a>
  <h5 class="font-semibold mb-0" style="font-size:16px;">Edit: {{ $category->name }}</h5>
</div>
 
<div class="max-w-lg mx-auto">
  <div>
    <div class="card p-4">
      <form method="POST" action="{{ route('categories.update', $category) }}">
        @csrf @method('PUT')
        @include('categories._form', ['category' => $category])
      </form>
    </div>
  </div>
</div>
 
@endsection