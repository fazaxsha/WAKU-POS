@extends('layouts.app')
 
@section('title', 'Edit Supplier')
@section('page-title', 'Edit Supplier')
 
@section('content')
 
<div class="flex items-center gap-2 mb-4">
  <a href="{{ route('suppliers.index') }}" class="topbar-btn no-underline" style="width:32px; height:32px;">
    <i class="bi bi-arrow-left"></i>
  </a>
  <h5 class="font-semibold mb-0" style="font-size:16px;">Edit: {{ $supplier->name }}</h5>
</div>
 
<form method="POST" action="{{ route('suppliers.update', $supplier) }}">
  @csrf @method('PUT')
  @include('suppliers._form', ['supplier' => $supplier])
</form>
 
@endsection
