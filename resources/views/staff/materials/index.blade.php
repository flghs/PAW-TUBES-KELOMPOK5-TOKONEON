@extends('layouts.panel')

@section('title', 'Bahan Baku')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <h1 class="text-2xl font-semibold text-cyan-300">Bahan baku</h1>

    @if (auth()->user()->isAdmin())
        <a href="{{ route('admin.materials.create') }}"
            class="rounded-md border border-pink-500 px-4 py-2 text-sm text-pink-400 shadow-[0_0_12px_rgba(236,72,153,0.5)] hover:bg-pink-500/10">
            Tambah bahan
        </a>
    @endif
</div>

@if ($lowCount > 0)
    <div class="mb-6 rounded-md border border-yellow-500/40 bg-yellow-500/10 px-4 py-3 text-sm text-yellow-200">
        {{ $lowCount }} bahan stoknya menipis atau habis.
        <a href="{{ route('staff.materials.index', ['low' => 1]) }}" class="underline hover:text-yellow-100">Lihat</a>
    </div>
@endif

<form method="GET" action="{{ route('staff.materials.index') }}" class="mb-6 flex flex-col gap-3 lg:flex-row">
    <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Cari nama atau kode"
        class="rounded-md border border-zinc-700 bg-zinc-900 px-3 py-2 text-sm focus:border-cyan-400 focus:outline-none">

    <label class="flex items-center gap-2 text-sm text-zinc-300">
        <input type="checkbox" name="low" value="1" @checked(! empty($filters['low'])) class="rounded border-zinc-700">
        Hanya stok menipis
    </label>

    <button type="submit"
        class="rounded-md bg-cyan-400 px-4 py-2 text-sm font-semibold text-zinc-950 shadow-[0_0_16px_rgba(34,211,238,0.6)] hover:bg-cyan-300">
        Filter
    </button>

    @if (! empty($filters['q']) || ! empty($filters['low']))
        <a href="{{ route('staff.materials.index') }}" class="self-center text-sm text-zinc-400 hover:text-zinc-200">Reset</a>
    @endif
</form>

<div class="overflow-x-auto rounded-xl border border-zinc-800 bg-zinc-900">
    <table class="w-full text-left text-sm">
        <thead class="border-b border-zinc-800 text-zinc-400">
            <tr>
                <th class="px-4 py-3">Kode</th>
                <th class="px-4 py-3">Nama</th>
                <th class="px-4 py-3">Stok</th>
                <th class="px-4 py-3">Minimum</th>
                <th class="px-4 py-3">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($materials as $material)
                @php
                    if (! $material->is_active) {
                        [$statusText, $statusClass] = ['Nonaktif', 'border-zinc-600 text-zinc-400'];
                    } elseif ((float) $material->current_stock <= 0) {
                        [$statusText, $statusClass] = ['Habis', 'border-red-500/50 text-red-300'];
                    } elseif ($material->isLowStock()) {
                        [$statusText, $statusClass] = ['Menipis', 'border-yellow-500/50 text-yellow-300'];
                    } else {
                        [$statusText, $statusClass] = ['Aman', 'border-green-500/50 text-green-300'];
                    }
                @endphp
                <tr class="border-b border-zinc-800 last:border-0">
                    <td class="px-4 py-3 text-zinc-400">{{ $material->code }}</td>
                    <td class="px-4 py-3">
                        <a href="{{ route('staff.materials.show', $material) }}" class="text-cyan-300 hover:underline">
                            {{ $material->name }}
                        </a>
                    </td>
                    <td class="px-4 py-3">{{ \App\Models\Material::formatQuantity($material->current_stock) }} {{ $material->unit }}</td>
                    <td class="px-4 py-3 text-zinc-400">{{ \App\Models\Material::formatQuantity($material->minimum_stock) }} {{ $material->unit }}</td>
                    <td class="px-4 py-3">
                        <span class="rounded-full border px-2 py-0.5 text-xs {{ $statusClass }}">{{ $statusText }}</span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-4 py-6 text-center text-zinc-500">Belum ada bahan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@include('partials.pager', ['paginator' => $materials])
@endsection
