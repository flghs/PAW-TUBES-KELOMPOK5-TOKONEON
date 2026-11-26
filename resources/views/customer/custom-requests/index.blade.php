@extends('layouts.app')

@section('title', 'Request Saya')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <h1 class="text-2xl font-semibold text-cyan-300">Request custom saya</h1>
    <a href="{{ route('customer.custom-requests.create') }}"
        class="rounded-md border border-pink-500 px-4 py-2 text-sm text-pink-400 shadow-[0_0_12px_rgba(236,72,153,0.5)] hover:bg-pink-500/10">
        Buat request baru
    </a>
</div>

<div class="overflow-x-auto rounded-xl border border-zinc-800 bg-zinc-900">
    <table class="w-full text-left text-sm">
        <thead class="border-b border-zinc-800 text-zinc-400">
            <tr>
                <th class="px-4 py-3">Nomor</th>
                <th class="px-4 py-3">Tulisan / deskripsi</th>
                <th class="px-4 py-3">Ukuran</th>
                <th class="px-4 py-3">Jumlah</th>
                <th class="px-4 py-3">Status</th>
                <th class="px-4 py-3">Dikirim</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($customRequests as $customRequest)
                <tr class="border-b border-zinc-800 last:border-0">
                    <td class="px-4 py-3">
                        <a href="{{ route('customer.custom-requests.show', $customRequest) }}" class="text-cyan-300 hover:underline">
                            {{ $customRequest->request_number }}
                        </a>
                    </td>
                    <td class="px-4 py-3">{{ \Illuminate\Support\Str::limit($customRequest->neon_text ?: $customRequest->description, 40) }}</td>
                    <td class="px-4 py-3 text-zinc-400">{{ $customRequest->width_cm }} x {{ $customRequest->height_cm }} cm</td>
                    <td class="px-4 py-3">{{ $customRequest->quantity }}</td>
                    <td class="px-4 py-3">@include('partials.request-status', ['customRequest' => $customRequest])</td>
                    <td class="px-4 py-3 text-zinc-400">{{ $customRequest->created_at->format('d/m/Y H:i') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-4 py-6 text-center text-zinc-500">Belum ada request custom.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@include('partials.pager', ['paginator' => $customRequests])
@endsection
