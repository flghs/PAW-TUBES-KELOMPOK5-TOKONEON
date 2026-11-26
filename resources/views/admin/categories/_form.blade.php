@csrf

<div>
    <label for="name" class="mb-1 block text-sm">Nama kategori</label>
    <input id="name" name="name" type="text" value="{{ old('name', $category->name ?? '') }}" required
        class="w-full rounded-md border border-zinc-700 bg-zinc-950 px-3 py-2 focus:border-cyan-400 focus:outline-none">
    @error('name')
        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
    @enderror
    @error('slug')
        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
    @enderror
</div>

<div>
    <label for="description" class="mb-1 block text-sm">Deskripsi (opsional)</label>
    <textarea id="description" name="description" rows="3"
        class="w-full rounded-md border border-zinc-700 bg-zinc-950 px-3 py-2 focus:border-cyan-400 focus:outline-none">{{ old('description', $category->description ?? '') }}</textarea>
    @error('description')
        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
    @enderror
</div>

<div class="flex items-center gap-3">
    <button type="submit"
        class="rounded-md bg-cyan-400 px-4 py-2 font-semibold text-zinc-950 shadow-[0_0_16px_rgba(34,211,238,0.6)] hover:bg-cyan-300">
        Simpan
    </button>
    <a href="{{ route('admin.categories.index') }}" class="text-sm text-zinc-400 hover:text-zinc-200">Batal</a>
</div>
