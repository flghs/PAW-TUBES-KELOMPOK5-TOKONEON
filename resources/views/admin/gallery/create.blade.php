@extends('layouts.panel')

@section('title', 'Tambah Foto Galeri')

@section('content')
<h1 class="mb-6 text-2xl font-semibold text-cyan-300">Tambah foto galeri</h1>

<form method="POST" action="{{ route('admin.gallery.store') }}" enctype="multipart/form-data"
    class="max-w-xl space-y-4 rounded-xl border border-zinc-800 bg-zinc-900 p-6">
    @include('admin.gallery._form')
</form>
@endsection
