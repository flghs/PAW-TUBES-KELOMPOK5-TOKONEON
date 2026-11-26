@extends('layouts.panel')

@section('title', 'Galeri Custom')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <h1 class="text-2xl font-semibold text-cyan-300">Galeri custom</h1>
    <a href="{{ route('admin.gallery.create') }}"
        class="rounded-md border border-pink-500 px-4 py-2 text-sm text-pink-400 shadow-[0_0_12px_rgba(236,72,153,0.5)] hover:bg-pink-500/10">
        Tambah foto
    </a>
</div>

@if ($items->isEmpty())
    <p class="rounded-xl border border-zinc-800 bg-zinc-900 p-8 text-center text-zinc-500">Belum ada foto.</p>
@else
    <div class="grid grid-cols-2 gap-4 md:grid-cols-3 xl:grid-cols-4">
        @foreach ($items as $item)
            <div class="overflow-hidden rounded-xl border border-zinc-800 bg-zinc-900">
                <img src="{{ $item->imageUrl() }}" alt="{{ $item->title }}" class="aspect-square w-full object-cover">
                <div class="p-3 text-sm">
                    <p class="font-semibold text-zinc-100">{{ $item->title }}</p>
                    <p class="mt-1 text-xs {{ $item->is_published ? 'text-cyan-300' : 'text-zinc-500' }}">
                        {{ $item->is_published ? 'Tampil' : 'Disembunyikan' }}
                    </p>
                    <div class="mt-3 flex items-center gap-4">
                        <a href="{{ route('admin.gallery.edit', $item) }}" class="text-cyan-300 hover:underline">Ubah</a>
                        <form method="POST" action="{{ route('admin.gallery.destroy', $item) }}"
                            onsubmit="return confirm('Hapus foto ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-400 hover:underline">Hapus</button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    @include('partials.pager', ['paginator' => $items])
@endif
@endsection
