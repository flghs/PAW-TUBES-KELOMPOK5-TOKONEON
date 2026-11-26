@extends('layouts.panel')

@section('title', 'Request Custom')

@section('content')
<h1 class="mb-6 text-2xl font-semibold text-cyan-300">Request custom</h1>

<form method="GET" action="{{ route('staff.custom-requests.index') }}" class="mb-6 flex items-center gap-3">
    <select name="status"
        class="rounded-md border border-zinc-700 bg-zinc-900 px-3 py-2 text-sm focus:border-cyan-400 focus:outline-none">
        <option value="">Semua status</option>
        @foreach (\App\Models\CustomRequest::STATUS_LABELS as $value => $label)
            <option value="{{ $value }}" @selected(($filters['status'] ?? '') === $value)>{{ $label }}</option>
        @endforeach
    </select>
    <button type="submit"
        class="rounded-md bg-cyan-400 px-4 py-2 text-sm font-semibold text-zinc-950 shadow-[0_0_16px_rgba(34,211,238,0.6)] hover:bg-cyan-300">
        Filter
    </button>
    @if (! empty($filters['status']))
        <a href="{{ route('staff.custom-requests.index') }}" class="text-sm text-zinc-400 hover:text-zinc-200">Reset</a>
    @endif
</form>

<div class="overflow-x-auto rounded-xl border border-zinc-800 bg-zinc-900">
    <table class="w-full text-left text-sm">
        <thead class="border-b border-zinc-800 text-zinc-400">
            <tr>
                <th class="px-4 py-3">Nomor</th>
                <th class="px-4 py-3">Pelanggan</th>
                <th class="px-4 py-3">Tulisan / deskripsi</th>
                <th class="px-4 py-3">Ukuran</th>
                <th class="px-4 py-3">Jumlah</th>
                <th class="px-4 py-3">Deadline</th>
                <th class="px-4 py-3">Status</th>
                <th class="px-4 py-3">Dikirim</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($customRequests as $customRequest)
                <tr class="border-b border-zinc-800 last:border-0">
                    <td class="px-4 py-3">
                        <a href="{{ route('staff.custom-requests.show', $customRequest) }}" class="text-cyan-300 hover:underline">
                            {{ $customRequest->request_number }}
                        </a>
                    </td>
                    <td class="px-4 py-3">{{ $customRequest->user->name }}</td>
                    <td class="px-4 py-3">{{ \Illuminate\Support\Str::limit($customRequest->neon_text ?: $customRequest->description, 30) }}</td>
                    <td class="px-4 py-3 text-zinc-400">{{ $customRequest->width_cm }} x {{ $customRequest->height_cm }} cm</td>
                    <td class="px-4 py-3">{{ $customRequest->quantity }}</td>
                    <td class="px-4 py-3 text-zinc-400">{{ $customRequest->deadline_date ? $customRequest->deadline_date->format('d/m/Y') : '-' }}</td>
                    <td class="px-4 py-3">@include('partials.request-status', ['customRequest' => $customRequest])</td>
                    <td class="px-4 py-3 text-zinc-400">{{ $customRequest->created_at->format('d/m/Y H:i') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="px-4 py-6 text-center text-zinc-500">Belum ada request custom.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@include('partials.pager', ['paginator' => $customRequests])
@endsection
