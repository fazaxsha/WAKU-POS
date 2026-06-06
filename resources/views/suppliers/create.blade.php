@extends('layouts.app')
 
@section('title', 'Tambah Supplier')
@section('page-title', 'Tambah Supplier Baru')
 
@section('content')
 
<div class="flex items-center gap-2 mb-4">
  <a href="{{ route('suppliers.index') }}" class="topbar-btn no-underline" style="width:32px; height:32px;">
    <i class="bi bi-arrow-left"></i>
  </a>
  <h5 class="font-semibold mb-0" style="font-size:16px;">Tambah Supplier Baru</h5>
</div>
 
<form method="POST" action="{{ route('suppliers.store') }}">
  @csrf
  @include('suppliers._form', ['supplier' => null])
</form>
 
@endsection
