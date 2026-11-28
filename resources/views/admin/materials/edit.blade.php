@extends('layouts.panel')

@section('title', 'Ubah Bahan')

@section('content')
<a href="{{ route('staff.materials.show', $material) }}" class="text-sm text-zinc-400 hover:text-cyan-300">&larr; {{ $material->name }}</a>

<h1 class="mt-6 text-2xl font-semibold text-cyan-300">Ubah data bahan</h1>

@include('admin.materials._form', [
    'material' => $material,
    'action' => route('admin.materials.update', $material),
    'submit' => 'Simpan perubahan',
])
@endsection
