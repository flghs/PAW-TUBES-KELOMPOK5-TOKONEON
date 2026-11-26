@if ($paginator->hasPages())
    <div class="mt-8 flex items-center justify-between text-sm text-zinc-400">
        <span>Halaman {{ $paginator->currentPage() }} dari {{ $paginator->lastPage() }}</span>
        <div class="flex gap-4">
            @if ($paginator->previousPageUrl())
                <a href="{{ $paginator->previousPageUrl() }}" class="text-cyan-300 hover:underline">Sebelumnya</a>
            @endif
            @if ($paginator->nextPageUrl())
                <a href="{{ $paginator->nextPageUrl() }}" class="text-cyan-300 hover:underline">Berikutnya</a>
            @endif
        </div>
    </div>
@endif
