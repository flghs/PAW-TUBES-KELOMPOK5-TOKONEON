@extends('layouts.app')

@section('title', 'Custom Neon')

@section('content')
<h1 class="text-2xl font-semibold text-cyan-300">Pesan neon sign custom</h1>
<p class="mt-2 max-w-2xl text-sm text-zinc-400">
    Ceritakan neon sign yang kamu inginkan. Tim kami akan memeriksa requestmu dan memberikan estimasi harga.
    Isi minimal salah satu: tulisan neon atau deskripsi bentuk.
</p>

<form method="POST" action="{{ route('customer.custom-requests.store') }}" enctype="multipart/form-data"
    class="mt-6 max-w-2xl space-y-4 rounded-xl border border-zinc-800 bg-zinc-900 p-6">
    @csrf

    <div>
        <label for="neon_text" class="mb-1 block text-sm">Tulisan pada neon</label>
        <input id="neon_text" name="neon_text" type="text" value="{{ old('neon_text') }}" placeholder="Contoh: Good Vibes Only"
            class="w-full rounded-md border border-zinc-700 bg-zinc-950 px-3 py-2 focus:border-cyan-400 focus:outline-none">
        @error('neon_text')
            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="description" class="mb-1 block text-sm">Deskripsi bentuk atau catatan</label>
        <textarea id="description" name="description" rows="4" placeholder="Contoh: bentuk logo kopi, huruf tebal, dipasang di dinding kafe"
            class="w-full rounded-md border border-zinc-700 bg-zinc-950 px-3 py-2 focus:border-cyan-400 focus:outline-none">{{ old('description') }}</textarea>
        @error('description')
            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="color" class="mb-1 block text-sm">Warna</label>
        <select id="color" name="color" required
            class="w-full rounded-md border border-zinc-700 bg-zinc-950 px-3 py-2 focus:border-cyan-400 focus:outline-none">
            <option value="">Pilih warna</option>
            @foreach (\App\Models\CustomRequest::COLORS as $color)
                <option value="{{ $color }}" @selected(old('color') === $color)>{{ $color }}</option>
            @endforeach
        </select>
        @error('color')
            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div>
            <label for="width_cm" class="mb-1 block text-sm">Lebar (cm)</label>
            <input id="width_cm" name="width_cm" type="number" min="10" max="500" step="1" value="{{ old('width_cm') }}" required
                class="w-full rounded-md border border-zinc-700 bg-zinc-950 px-3 py-2 focus:border-cyan-400 focus:outline-none">
            @error('width_cm')
                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="height_cm" class="mb-1 block text-sm">Tinggi (cm)</label>
            <input id="height_cm" name="height_cm" type="number" min="10" max="500" step="1" value="{{ old('height_cm') }}" required
                class="w-full rounded-md border border-zinc-700 bg-zinc-950 px-3 py-2 focus:border-cyan-400 focus:outline-none">
            @error('height_cm')
                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="quantity" class="mb-1 block text-sm">Jumlah</label>
            <input id="quantity" name="quantity" type="number" min="1" max="1000" step="1" value="{{ old('quantity', 1) }}" required
                class="w-full rounded-md border border-zinc-700 bg-zinc-950 px-3 py-2 focus:border-cyan-400 focus:outline-none">
            @error('quantity')
                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div>
        <label for="deadline_date" class="mb-1 block text-sm">Deadline (opsional)</label>
        <input id="deadline_date" name="deadline_date" type="date" min="{{ now()->addDay()->format('Y-m-d') }}" value="{{ old('deadline_date') }}"
            class="w-full rounded-md border border-zinc-700 bg-zinc-950 px-3 py-2 focus:border-cyan-400 focus:outline-none sm:max-w-xs">
        @error('deadline_date')
            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="reference_image" class="mb-1 block text-sm">Gambar referensi (opsional; jpg, png, atau webp, maksimal 2 MB)</label>
        <input id="reference_image" name="reference_image" type="file" accept="image/jpeg,image/png,image/webp"
            class="block w-full text-sm text-zinc-300 file:mr-4 file:rounded-md file:border-0 file:bg-zinc-800 file:px-4 file:py-2 file:text-zinc-200 hover:file:bg-zinc-700">
        @error('reference_image')
            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <div class="flex items-center gap-3">
        <button type="submit"
            class="rounded-md bg-pink-500 px-4 py-2 font-semibold text-white shadow-[0_0_16px_rgba(236,72,153,0.6)] hover:bg-pink-400">
            Kirim request
        </button>
        <a href="{{ route('customer.custom-requests.index') }}" class="text-sm text-zinc-400 hover:text-zinc-200">Request saya</a>
    </div>
</form>
@endsection
