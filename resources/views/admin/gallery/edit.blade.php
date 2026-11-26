@extends('layouts.panel')

@section('title', 'Ubah Foto Galeri')

@section('content')
<h1 class="mb-6 text-2xl font-semibold text-cyan-300">Ubah foto galeri</h1>

<form method="POST" action="{{ route('admin.gallery.update', $gallery) }}" enctype="multipart/form-data"
    class="max-w-xl space-y-4 rounded-xl border border-zinc-800 bg-zinc-900 p-6">
    @method('PUT')
    @include('admin.gallery._form')
</form>
@endsection
