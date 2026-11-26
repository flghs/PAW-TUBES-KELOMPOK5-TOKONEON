@php
    $statusClasses = match ($order->status) {
        'pending_payment' => 'border-yellow-500/50 text-yellow-300',
        'paid', 'completed' => 'border-green-500/50 text-green-300',
        'in_production', 'ready_to_ship', 'shipped' => 'border-cyan-500/50 text-cyan-300',
        default => 'border-zinc-600 text-zinc-400',
    };

    $paymentClasses = match ($order->payment_status) {
        'paid' => 'border-green-500/50 text-green-300',
        'pending_review' => 'border-cyan-500/50 text-cyan-300',
        'rejected' => 'border-red-500/50 text-red-300',
        default => 'border-zinc-600 text-zinc-400',
    };
@endphp
<span class="rounded-full border px-2 py-0.5 text-xs {{ $statusClasses }}">{{ $order->statusLabel() }}</span>
<span class="rounded-full border px-2 py-0.5 text-xs {{ $paymentClasses }}">{{ $order->paymentLabel() }}</span>
