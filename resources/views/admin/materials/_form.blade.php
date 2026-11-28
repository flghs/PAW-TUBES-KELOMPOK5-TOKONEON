{{-- Dipakai oleh create dan edit. $material bernilai null saat membuat bahan baru. --}}
<form method="POST" action="{{ $action }}" class="mt-6 max-w-xl space-y-4 rounded-xl border border-zinc-800 bg-zinc-900 p-6">
    @csrf
    @if ($material)
        @method('PUT')
    @endif

    <div>
        <label for="name" class="mb-1 block text-sm">Nama bahan</label>
        <input id="name" name="name" type="text" value="{{ old('name', $material?->name) }}" required
            placeholder="Contoh: Kabel LED Neon Flex Pink"
            class="w-full rounded-md border border-zinc-700 bg-zinc-950 px-3 py-2 focus:border-cyan-400 focus:outline-none">
        @error('name')
            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div>
            <label for="code" class="mb-1 block text-sm">Kode</label>
            <input id="code" name="code" type="text" value="{{ old('code', $material?->code) }}" required
                placeholder="Contoh: LED-PINK"
                class="w-full rounded-md border border-zinc-700 bg-zinc-950 px-3 py-2 uppercase focus:border-cyan-400 focus:outline-none">
            @error('code')
                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="unit" class="mb-1 block text-sm">Satuan</label>
            <input id="unit" name="unit" type="text" value="{{ old('unit', $material?->unit) }}" required
                placeholder="meter, pcs, lembar, botol"
                class="w-full rounded-md border border-zinc-700 bg-zinc-950 px-3 py-2 focus:border-cyan-400 focus:outline-none">
            @error('unit')
                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div>
            <label for="minimum_stock" class="mb-1 block text-sm">Batas stok minimum</label>
            <input id="minimum_stock" name="minimum_stock" type="number" step="0.01" min="0"
                value="{{ old('minimum_stock', $material?->minimum_stock ?? 0) }}" required
                class="w-full rounded-md border border-zinc-700 bg-zinc-950 px-3 py-2 focus:border-cyan-400 focus:outline-none">
            <p class="mt-1 text-xs text-zinc-500">Stok sama dengan atau di bawah angka ini dianggap menipis.</p>
            @error('minimum_stock')
                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

        @unless ($material)
            <div>
                <label for="initial_stock" class="mb-1 block text-sm">Stok awal (opsional)</label>
                <input id="initial_stock" name="initial_stock" type="number" step="0.01" min="0" value="{{ old('initial_stock', 0) }}"
                    class="w-full rounded-md border border-zinc-700 bg-zinc-950 px-3 py-2 focus:border-cyan-400 focus:outline-none">
                <p class="mt-1 text-xs text-zinc-500">Dicatat sebagai stok masuk di riwayat.</p>
                @error('initial_stock')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>
        @endunless
    </div>

    @if ($material)
        <p class="text-sm text-zinc-400">
            Stok sekarang: {{ \App\Models\Material::formatQuantity($material->current_stock) }} {{ $material->unit }}.
            Stok tidak diubah di sini, pakai Catat stok di halaman detail bahan.
        </p>
    @endif

    <div>
        <input type="hidden" name="is_active" value="0">
        <label class="flex items-center gap-2 text-sm">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $material?->is_active ?? true)) class="rounded border-zinc-700">
            Aktif
        </label>
        <p class="mt-1 text-xs text-zinc-500">Bahan nonaktif tidak bisa diubah stoknya dan tidak dihitung menipis. Riwayatnya tetap tersimpan.</p>
    </div>

    <div class="flex items-center gap-3">
        <button type="submit"
            class="rounded-md bg-cyan-400 px-4 py-2 font-semibold text-zinc-950 shadow-[0_0_16px_rgba(34,211,238,0.6)] hover:bg-cyan-300">
            {{ $submit }}
        </button>
        <a href="{{ $material ? route('staff.materials.show', $material) : route('staff.materials.index') }}"
            class="text-sm text-zinc-400 hover:text-zinc-200">Batal</a>
    </div>
</form>
