@csrf

<div>
    <label for="title" class="mb-1 block text-sm">Judul</label>
    <input id="title" name="title" type="text" value="{{ old('title', $gallery->title ?? '') }}" required
        class="w-full rounded-md border border-zinc-700 bg-zinc-950 px-3 py-2 focus:border-cyan-400 focus:outline-none">
    @error('title')
        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
    @enderror
</div>

<div>
    <label for="description" class="mb-1 block text-sm">Deskripsi (opsional)</label>
    <textarea id="description" name="description" rows="3"
        class="w-full rounded-md border border-zinc-700 bg-zinc-950 px-3 py-2 focus:border-cyan-400 focus:outline-none">{{ old('description', $gallery->description ?? '') }}</textarea>
    @error('description')
        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
    @enderror
</div>

<div>
    <label for="image" class="mb-1 block text-sm">Foto (jpg, png, atau webp, maksimal 2 MB)</label>

    @if (isset($gallery))
        <img src="{{ $gallery->imageUrl() }}" alt="{{ $gallery->title }}" class="mb-3 h-32 w-32 rounded-md object-cover">
        <p class="mb-2 text-xs text-zinc-500">Pilih file baru hanya kalau ingin mengganti foto.</p>
    @endif

    <input id="image" name="image" type="file" accept="image/jpeg,image/png,image/webp"
        class="block w-full text-sm text-zinc-300 file:mr-4 file:rounded-md file:border-0 file:bg-zinc-800 file:px-4 file:py-2 file:text-zinc-200 hover:file:bg-zinc-700">
    @error('image')
        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
    @enderror
</div>

<div>
    <input type="hidden" name="is_published" value="0">
    <label class="flex items-center gap-2 text-sm">
        <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $gallery->is_published ?? true)) class="rounded border-zinc-700">
        Tampilkan di halaman Custom
    </label>
</div>

<div class="flex items-center gap-3">
    <button type="submit"
        class="rounded-md bg-cyan-400 px-4 py-2 font-semibold text-zinc-950 shadow-[0_0_16px_rgba(34,211,238,0.6)] hover:bg-cyan-300">
        Simpan
    </button>
    <a href="{{ route('admin.gallery.index') }}" class="text-sm text-zinc-400 hover:text-zinc-200">Batal</a>
</div>
