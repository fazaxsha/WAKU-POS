{{-- ============================================================ --}}
{{-- resources/views/users/create.blade.php                     --}}
{{-- ============================================================ --}}
@extends('layouts.app')
@section('title', 'Tambah User')
@section('page-title', 'Tambah User')
 
@section('content')
<div class="flex items-center gap-2 mb-4">
  <a href="{{ route('users.index') }}" class="topbar-btn no-underline" style="width:32px; height:32px;">
    <i class="bi bi-arrow-left"></i>
  </a>
  <h5 class="font-semibold mb-0" style="font-size:16px;">Tambah Pengguna Baru</h5>
</div>
<div class="max-w-xl mx-auto">
  <div>
    <div class="card p-4">
      <form method="POST" action="{{ route('users.store') }}">
        @csrf
        @include('users._form', ['user' => null])
      </form>
    </div>
  </div>
</div>
@endsection
 