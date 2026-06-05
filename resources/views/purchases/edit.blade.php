@extends('layouts.app')
@section('title', 'Edit PO')
@section('page-title', 'Edit Purchase Order')

@section('content')
<div class="flex items-center gap-2 mb-4">
  <a href="{{ route('purchases.show', $purchase) }}" class="topbar-btn no-underline" style="width:32px; height:32px;">
    <i class="bi bi-arrow-left"></i>
  </a>
  <h5 class="font-semibold mb-0" style="font-size:16px;">Edit PO: {{ $purchase->reference_no ?? '#'.$purchase->id }}</h5>
</div>
<form method="POST" action="{{ route('purchases.update', $purchase) }}">
  @csrf @method('PUT')
  @include('purchases._form', ['purchase' => $purchase])
</form>
@endsection
