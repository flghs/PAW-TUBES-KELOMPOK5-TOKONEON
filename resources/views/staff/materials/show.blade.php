@extends('layouts.panel')

@section('title', $material->name)

@section('content')
<a href="{{ route('staff.materials.index') }}" class="text-sm text-zinc-400 hover:text-cyan-300">&larr; Bahan baku</a>

<div class="mt-6 flex flex-wrap items-center gap-3">
    <h1 class="text-2xl font-semibold text-cyan-300">{{ $material->name }}</h1>
    <span class="text-sm text-zinc-500">{{ $material->code }}</span>

    @if (! $material->is_active)
        <span class="rounded-full border border-zinc-600 px-2 py-0.5 text-xs text-zinc-400">Nonaktif</span>
    @elseif ((float) $material->current_stock <= 0)
        <span class="rounded-full border border-red-500/50 px-2 py-0.5 text-xs text-red-300">Habis</span>
    @elseif ($material->isLowStock())
        <span class="rounded-full border border-yellow-500/50 px-2 py-0.5 text-xs text-yellow-300">Stok menipis</span>
    @endif

    @if (auth()->user()->isAdmin())
        <a href="{{ route('admin.materials.edit', $material) }}" class="text-sm text-cyan-300 hover:underline">Ubah data bahan</a>
    @endif
</div>

<div class="mt-6 grid gap-8 lg:grid-cols-3">
    <div class="space-y-6">
        <div class="rounded-xl border border-zinc-800 bg-zinc-900 p-6">
            <p class="text-sm text-zinc-500">Stok sekarang</p>
            <p class="mt-1 text-3xl font-semibold text-cyan-300">
                {{ \App\Models\Material::formatQuantity($material->current_stock) }}
                <span class="text-lg text-zinc-400">{{ $material->unit }}</span>
            </p>
            <p class="mt-3 text-sm text-zinc-500">
                Batas minimum: {{ \App\Models\Material::formatQuantity($material->minimum_stock) }} {{ $material->unit }}
            </p>
        </div>

        @if ($material->is_active)
            <form method="POST" action="{{ route('staff.materials.stock.store', $material) }}"
                class="space-y-4 rounded-xl border border-zinc-800 bg-zinc-900 p-6 text-sm">
                @csrf
                <h2 class="text-lg font-semibold text-zinc-100">Catat stok</h2>

                <div>
                    <label for="type" class="mb-1 block">Jenis</label>
                    <select id="type" name="type" required
                        class="w-full rounded-md border border-zinc-700 bg-zinc-950 px-3 py-2 focus:border-cyan-400 focus:outline-none">
                        <option value="in" @selected(old('type', 'in') === 'in')>Stok masuk (beli bahan)</option>
                        <option value="out" @selected(old('type') === 'out')>Stok keluar (rusak, hilang, terpakai)</option>
                        <option value="adjustment" @selected(old('type') === 'adjustment')>Koreksi (hasil hitung fisik)</option>
                    </select>
                    @error('type')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="quantity" class="mb-1 block">Jumlah ({{ $material->unit }})</label>
                    <input id="quantity" name="quantity" type="number" step="0.01" min="0" value="{{ old('quantity') }}" required
                        class="w-full rounded-md border border-zinc-700 bg-zinc-950 px-3 py-2 focus:border-cyan-400 focus:outline-none">
                    <p class="mt-1 text-xs text-zinc-500">Untuk Koreksi, isi stok hasil hitung fisik (bukan selisihnya).</p>
                    @error('quantity')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="note" class="mb-1 block">Catatan</label>
                    <input id="note" name="note" type="text" maxlength="255" value="{{ old('note') }}"
                        class="w-full rounded-md border border-zinc-700 bg-zinc-950 px-3 py-2 focus:border-cyan-400 focus:outline-none">
                    <p class="mt-1 text-xs text-zinc-500">Wajib diisi untuk stok keluar dan koreksi.</p>
                    @error('note')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit"
                    class="rounded-md bg-cyan-400 px-4 py-2 font-semibold text-zinc-950 shadow-[0_0_16px_rgba(34,211,238,0.6)] hover:bg-cyan-300">
                    Simpan
                </button>
            </form>
        @else
            <div class="rounded-xl border border-zinc-800 bg-zinc-900 p-6 text-sm text-zinc-400">
                Bahan ini nonaktif, jadi stoknya tidak bisa diubah. Admin bisa mengaktifkannya lagi lewat Ubah data bahan.
            </div>
        @endif
    </div>

    <div class="lg:col-span-2">
        <h2 class="mb-3 text-lg font-semibold text-zinc-100">Riwayat stok</h2>

        <div class="overflow-x-auto rounded-xl border border-zinc-800 bg-zinc-900">
            <table class="w-full text-left text-sm">
                <thead class="border-b border-zinc-800 text-zinc-400">
                    <tr>
                        <th class="px-4 py-3">Waktu</th>
                        <th class="px-4 py-3">Jenis</th>
                        <th class="px-4 py-3">Jumlah</th>
                        <th class="px-4 py-3">Stok sesudah</th>
                        <th class="px-4 py-3">Catatan</th>
                        <th class="px-4 py-3">Oleh</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($movements as $movement)
                        @php
                            $quantity = (float) $movement->quantity;

                            [$typeText, $typeClass] = match ($movement->type) {
                                'in' => ['Masuk', 'border-green-500/50 text-green-300'],
                                'out' => ['Keluar', 'border-red-500/50 text-red-300'],
                                default => ['Koreksi', 'border-cyan-500/50 text-cyan-300'],
                            };

                            $sign = match ($movement->type) {
                                'in' => '+',
                                'out' => '-',
                                default => $quantity >= 0 ? '+' : '-',
                            };
                        @endphp
                        <tr class="border-b border-zinc-800 last:border-0">
                            <td class="whitespace-nowrap px-4 py-3 text-zinc-400">{{ $movement->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-3">
                                <span class="rounded-full border px-2 py-0.5 text-xs {{ $typeClass }}">{{ $typeText }}</span>
                            </td>
                            <td class="whitespace-nowrap px-4 py-3">{{ $sign }}{{ \App\Models\Material::formatQuantity(abs($quantity)) }} {{ $material->unit }}</td>
                            <td class="whitespace-nowrap px-4 py-3">{{ \App\Models\Material::formatQuantity($movement->stock_after) }} {{ $material->unit }}</td>
                            <td class="px-4 py-3 text-zinc-300">{{ $movement->note ?: '-' }}</td>
                            <td class="px-4 py-3 text-zinc-400">{{ $movement->creator?->name ?: '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-6 text-center text-zinc-500">Belum ada riwayat stok.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @include('partials.pager', ['paginator' => $movements])
    </div>
</div>
@endsection
