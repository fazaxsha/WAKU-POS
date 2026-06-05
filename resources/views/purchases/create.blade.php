@extends('layouts.app')
@section('title', 'Buat PO')
@section('page-title', 'Buat Purchase Order')

@section('content')
<div class="flex items-center gap-2 mb-4">
  <a href="{{ route('purchases.index') }}" class="topbar-btn no-underline" style="width:32px; height:32px;">
    <i class="bi bi-arrow-left"></i>
  </a>
  <h5 class="font-semibold mb-0" style="font-size:16px;">Buat Purchase Order Baru</h5>
</div>
<form method="POST" action="{{ route('purchases.store') }}">
  @csrf
  @include('purchases._form', ['purchase' => null])
</form>
@endsection
