@extends('layouts.app')

@section('title', 'Request ' . $customRequest->request_number)

@section('content')
<a href="{{ route('customer.custom-requests.index') }}" class="text-sm text-zinc-400 hover:text-cyan-300">&larr; Request saya</a>

<div class="mt-6 flex flex-wrap items-center gap-3">
    <h1 class="text-2xl font-semibold text-cyan-300">{{ $customRequest->request_number }}</h1>
    @include('partials.request-status', ['customRequest' => $customRequest])
</div>
<p class="mt-1 text-sm text-zinc-500">Dikirim {{ $customRequest->created_at->format('d/m/Y H:i') }}</p>

<div class="mt-6 grid gap-8 md:grid-cols-2">
    <dl class="space-y-4 rounded-xl border border-zinc-800 bg-zinc-900 p-6 text-sm">
        <div>
            <dt class="text-zinc-500">Tulisan neon</dt>
            <dd class="mt-1 text-zinc-100">{{ $customRequest->neon_text ?: '-' }}</dd>
        </div>
        <div>
            <dt class="text-zinc-500">Deskripsi</dt>
            <dd class="mt-1 text-zinc-100">{!! $customRequest->description ? nl2br(e($customRequest->description)) : '-' !!}</dd>
        </div>
        <div>
            <dt class="text-zinc-500">Warna</dt>
            <dd class="mt-1 text-zinc-100">{{ $customRequest->color }}</dd>
        </div>
        <div>
            <dt class="text-zinc-500">Ukuran</dt>
            <dd class="mt-1 text-zinc-100">{{ $customRequest->width_cm }} x {{ $customRequest->height_cm }} cm</dd>
        </div>
        <div>
            <dt class="text-zinc-500">Jumlah</dt>
            <dd class="mt-1 text-zinc-100">{{ $customRequest->quantity }}</dd>
        </div>
        <div>
            <dt class="text-zinc-500">Deadline</dt>
            <dd class="mt-1 text-zinc-100">{{ $customRequest->deadline_date ? $customRequest->deadline_date->format('d/m/Y') : '-' }}</dd>
        </div>
    </dl>

    <div>
        <p class="mb-2 text-sm text-zinc-500">Gambar referensi</p>
        @if ($customRequest->reference_image_path)
            <img src="{{ route('custom-requests.image', $customRequest) }}" alt="Gambar referensi"
                class="w-full rounded-xl border border-zinc-800 object-contain shadow-[0_0_30px_rgba(236,72,153,0.15)]">
        @else
            <div class="flex h-40 items-center justify-center rounded-xl border border-zinc-800 bg-zinc-900 text-zinc-500">
                Tidak ada gambar
            </div>
        @endif

        <p class="mt-6 text-sm text-zinc-400">Requestmu sudah diterima dan akan diperiksa oleh tim kami.</p>
    </div>
</div>
@endsection
