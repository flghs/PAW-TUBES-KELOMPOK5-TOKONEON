@csrf

<div>
    <label for="category_id" class="mb-1 block text-sm">Kategori</label>
    <select id="category_id" name="category_id" required
        class="w-full rounded-md border border-zinc-700 bg-zinc-950 px-3 py-2 focus:border-cyan-400 focus:outline-none">
        <option value="">Pilih kategori</option>
        @foreach ($categories as $category)
            <option value="{{ $category->id }}" @selected(old('category_id', $product->category_id ?? '') == $category->id)>
                {{ $category->name }}
            </option>
        @endforeach
    </select>
    @error('category_id')
        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
    @enderror
</div>

<div>
    <label for="name" class="mb-1 block text-sm">Nama produk</label>
    <input id="name" name="name" type="text" value="{{ old('name', $product->name ?? '') }}" required
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
    <textarea id="description" name="description" rows="4"
        class="w-full rounded-md border border-zinc-700 bg-zinc-950 px-3 py-2 focus:border-cyan-400 focus:outline-none">{{ old('description', $product->description ?? '') }}</textarea>
    @error('description')
        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
    @enderror
</div>

<div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
    <div>
        <label for="price" class="mb-1 block text-sm">Harga (Rp)</label>
        <input id="price" name="price" type="number" min="1" step="1" value="{{ old('price', $product->price ?? '') }}" required
            class="w-full rounded-md border border-zinc-700 bg-zinc-950 px-3 py-2 focus:border-cyan-400 focus:outline-none">
        @error('price')
            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="stock" class="mb-1 block text-sm">Stok</label>
        <input id="stock" name="stock" type="number" min="0" step="1" value="{{ old('stock', $product->stock ?? 0) }}" required
            class="w-full rounded-md border border-zinc-700 bg-zinc-950 px-3 py-2 focus:border-cyan-400 focus:outline-none">
        @error('stock')
            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
        @enderror
    </div>
</div>

<div>
    <label for="image" class="mb-1 block text-sm">Foto produk (jpg, png, atau webp, maksimal 2 MB)</label>

    @if (isset($product) && $product->imageUrl())
        <img src="{{ $product->imageUrl() }}" alt="{{ $product->name }}" class="mb-3 h-32 w-32 rounded-md object-cover">
        <p class="mb-2 text-xs text-zinc-500">Pilih file baru hanya kalau ingin mengganti foto.</p>
    @endif

    <input id="image" name="image" type="file" accept="image/jpeg,image/png,image/webp"
        class="block w-full text-sm text-zinc-300 file:mr-4 file:rounded-md file:border-0 file:bg-zinc-800 file:px-4 file:py-2 file:text-zinc-200 hover:file:bg-zinc-700">
    @error('image')
        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
    @enderror
</div>

<div>
    <input type="hidden" name="is_active" value="0">
    <label class="flex items-center gap-2 text-sm">
        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $product->is_active ?? true)) class="rounded border-zinc-700">
        Tampilkan produk di katalog
    </label>
</div>

<div class="flex items-center gap-3">
    <button type="submit"
        class="rounded-md bg-cyan-400 px-4 py-2 font-semibold text-zinc-950 shadow-[0_0_16px_rgba(34,211,238,0.6)] hover:bg-cyan-300">
        Simpan
    </button>
    <a href="{{ route('admin.products.index') }}" class="text-sm text-zinc-400 hover:text-zinc-200">Batal</a>
</div>
