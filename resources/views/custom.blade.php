@extends('layouts.app')

@section('title', 'Custom Neon')

@section('content')
<section class="rounded-2xl border border-zinc-800 bg-[image:radial-gradient(ellipse_at_top,rgba(236,72,153,0.12),transparent_60%)] px-6 py-14 text-center">
    <h1 class="text-4xl font-bold text-pink-400 drop-shadow-[0_0_20px_rgba(236,72,153,0.8)] sm:text-5xl">Neon Sign Custom</h1>
    <p class="mx-auto mt-4 max-w-xl text-zinc-400">
        Punya desain, tulisan, atau logo sendiri? Ceritakan lewat WhatsApp dan tim kami akan membantu dari ukuran, warna, sampai harga.
    </p>

    @if ($whatsappUrl)
        <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener"
            class="mt-8 inline-block rounded-md border border-green-400 px-6 py-3 font-semibold text-green-300 shadow-[0_0_16px_rgba(74,222,128,0.5)] hover:bg-green-400/10">
            Chat via WhatsApp
        </a>
    @else
        <p class="mt-8 text-sm text-zinc-500">Nomor WhatsApp belum diatur.</p>
    @endif
</section>

<section class="mt-10 grid gap-4 text-sm sm:grid-cols-4">
    @foreach ([
        'Kirim desain atau idemu lewat chat',
        'Diskusi ukuran, warna, dan harga dengan tim kami',
        'Setelah sepakat, pesananmu dicatat dan diproduksi',
        'Neon sign dikirim ke alamatmu',
    ] as $index => $step)
        <div class="rounded-xl border border-zinc-800 bg-zinc-900 p-4">
            <p class="text-2xl font-bold text-cyan-300">{{ $index + 1 }}</p>
            <p class="mt-2 text-zinc-300">{{ $step }}</p>
        </div>
    @endforeach
</section>

<section class="mt-14">
    <h2 class="mb-4 text-xl font-semibold text-zinc-100">Hasil custom kami</h2>

    @if ($items->isEmpty())
        <p class="rounded-xl border border-zinc-800 bg-zinc-900 p-8 text-center text-zinc-500">Belum ada foto.</p>
    @else
        <div class="grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-4">
            @foreach ($items as $item)
                <figure class="overflow-hidden rounded-xl border border-zinc-800 bg-zinc-900">
                    <img src="{{ $item->imageUrl() }}" alt="{{ $item->title }}" class="aspect-square w-full object-cover" loading="lazy">
                    <figcaption class="p-3">
                        <p class="text-sm font-semibold text-zinc-100">{{ $item->title }}</p>
                        @if ($item->description)
                            <p class="mt-1 text-xs text-zinc-400">{{ $item->description }}</p>
                        @endif
                    </figcaption>
                </figure>
            @endforeach
        </div>

        @include('partials.pager', ['paginator' => $items])
    @endif
</section>
@endsection
