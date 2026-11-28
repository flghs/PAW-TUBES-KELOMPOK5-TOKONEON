@extends('layouts.panel')

@section('title', 'Tambah Bahan')

@section('content')
<a href="{{ route('staff.materials.index') }}" class="text-sm text-zinc-400 hover:text-cyan-300">&larr; Bahan baku</a>

<h1 class="mt-6 text-2xl font-semibold text-cyan-300">Tambah bahan</h1>

@include('admin.materials._form', [
    'material' => null,
    'action' => route('admin.materials.store'),
    'submit' => 'Simpan bahan',
])
@endsection
