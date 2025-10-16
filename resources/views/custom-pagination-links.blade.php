@if ($paginator->hasPages())
<nav role="navigation" aria-label="Pagination" class="flex items-center justify-end gap-1">
    {{-- Previous --}}
    @if ($paginator->onFirstPage())
        <span class="text-sm px-2.5 py-1.5 rounded border border-zinc-200 text-zinc-400 cursor-not-allowed select-none">Préc.</span>
    @else
        <button wire:click="previousPage('{{ $paginator->getPageName() }}')" wire:loading.attr="disabled"
                class="text-sm px-2.5 py-1.5 rounded border border-zinc-200 hover:bg-blue-800 bg-zinc-600">Préc.</button>
    @endif

    {{-- Pages --}}
    @foreach ($elements as $element)
        @if (is_string($element))
            <span class="px-2.5 py-1.5 text-zinc-400">…</span>
        @endif
        @if (is_array($element))
            @foreach ($element as $page => $url)
                @if ($page == $paginator->currentPage())
                    <span class="px-3 py-1.5 rounded bg-blue-800 text-white">{{ $page }}</span>
                @else
                    <button wire:click="gotoPage({{ $page }}, '{{ $paginator->getPageName() }}')" class="px-3 py-1.5 rounded border border-zinc-200 hover:bg-blue-800">{{ $page }}</button>
                @endif
            @endforeach
        @endif
    @endforeach

    {{-- Next --}}
    @if ($paginator->hasMorePages())
        <button wire:click="nextPage('{{ $paginator->getPageName() }}')" wire:loading.attr="disabled"
                class="text-sm px-2.5 py-1.5 rounded border border-zinc-200 hover:bg-blue-800 bg-zinc-600">Suiv.</button>
    @else
        <span class="text-sm px-2.5 py-1.5 rounded border border-zinc-200 text-zinc-400 cursor-not-allowed select-none">Suiv.</span>
    @endif
</nav>
@endif
