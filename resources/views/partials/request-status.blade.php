@php
    $badgeClasses = match ($customRequest->status) {
        'submitted' => 'border-cyan-500/50 text-cyan-300',
        'in_discussion' => 'border-pink-500/50 text-pink-300',
        'quoted' => 'border-yellow-500/50 text-yellow-300',
        'approved' => 'border-green-500/50 text-green-300',
        default => 'border-zinc-600 text-zinc-400',
    };
@endphp
<span class="rounded-full border px-2 py-0.5 text-xs {{ $badgeClasses }}">{{ $customRequest->statusLabel() }}</span>
