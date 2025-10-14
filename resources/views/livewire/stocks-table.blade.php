{{-- resources/views/livewire/stocks-table.blade.php --}}
<div class="p-6 space-y-4 max-w-none">
    <h1 class="text-4xl font-semibold">Stock</h1>

{{-- Barre d’actions avec message HTML --}}
<div x-data="{ showSoon:false, fireSoon(){ this.showSoon = true; setTimeout(()=> this.showSoon = false, 1500) } }"
     class="relative z-30 flex flex-wrap md:flex-nowrap items-center gap-2 md:gap-3 min-w-0">
    <button
        type="button"
        class="shrink-0 inline-flex items-center gap-1.5 rounded-md bg-grey-600 px-2 py-1 text-xs text-white shadow-sm hover:bg-blue-700 active:bg-blue-800 border-1 border-b-gray-400 focus:outline-none"
        @click="fireSoon()"
    >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
            <path d="M12 5c.414 0 .75.336.75.75V11.25H18.25a.75.75 0 0 1 0 1.5H12.75V18.25a.75.75 0 0 1-1.5 0V12.75H5.75a.75.75 0 0 1 0-1.5H11.25V5.75c0-.414.336-.75.75-.75Z"/>
        </svg>
        <span>Ajouter</span>
    </button>

    <div class="flex-1 min-w-0">
        <input
            type="text"
            placeholder="Rechercher par code, référence ou désignation..."
            class="w-full rounded-md border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-3 py-2 text-sm"
            wire:model.live.debounce.300ms="search"
        />
    </div>

    {{-- Message HTML "En construction" --}}
    <div x-show="showSoon" x-transition
         class="absolute right-0 bottom-full mt-2 rounded-md bg-green-800 text-white text-xs px-3 py-1.5 shadow-lg pointer-events-none"
         style="white-space: nowrap">
        En construction
    </div>
</div>


{{-- Wrapper du tableau: s’assurer qu’il n’empiète pas sur la barre --}}
<div class="relative z-10 w-full overflow-x-auto rounded-md border border-zinc-200 dark:border-zinc-700">
    {{-- ... table ici ... --}}




    {{-- Tableau des stocks aligné --}}
    <div class="w-full overflow-x-auto rounded-md border border-zinc-200 dark:border-zinc-700">
        <table class="w-full table-fixed divide-y divide-zinc-200 dark:divide-zinc-700 text-sm">
            <thead class="bg-zinc-50 dark:bg-zinc-900">
                <tr>
                    <th class="px-3 py-2 text-left align-middle w-[4ch] whitespace-nowrap">Code</th>
                    <th class="px-3 py-2 text-left align-middle w-[9ch] whitespace-nowrap">Réference</th>
                    <th class="px-3 py-2 text-left align-middle w-[30ch]">Désignation</th>
                    <th class="px-3 py-2 text-right align-middle w-[3ch] whitespace-nowrap">Stock</th>
                    <th class="px-3 py-2 text-right align-middle w-[4ch] whitespace-nowrap">Poids</th>
                    <th class="px-3 py-2 text-right align-middle w-[4ch] whitespace-nowrap">Emplacement</th>
                    <th class="px-3 py-2 text-right align-middle w-[10ch]">Actions</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700 bg-white dark:bg-zinc-800">
                @forelse($items as $item)
                    <tr class="align-middle">
                        <td class="px-3 py-2 whitespace-nowrap">{{ $item->code }}</td>
                        <td class="px-3 py-2 whitespace-nowrap">{{ $item->reference }}</td>
                        <td class="px-3 py-2 truncate" title="{{ $item->designation }}">{{ $item->designation }}</td>
                        <td class="px-3 py-2 text-right whitespace-nowrap">{{ $item->stock }}</td>
                        <td class="px-3 py-2 text-right whitespace-nowrap">{{ $item->poids_ma_kg}}</td>
                        <td class="px-3 py-2 text-right whitespace-nowrap">{{ $item->emplacement }}</td>
                        <td class="px-3 py-2 text-right">
                            <span class="inline-flex items-center justify-end gap-2">
                                <button
                                    type="button"
                                    class="inline-flex items-center rounded-md bg-zinc-100 dark:bg-zinc-700 px-2 py-1 text-xs hover:bg-zinc-200 dark:hover:bg-zinc-600 dark:active:bg-red-800"
                                    wire:click="decrement('{{ $item->id }}')"
                                    wire:loading.attr="disabled"
                                    aria-label="Diminuer"
                                >
                                    −
                                </button>
                                <button
                                    type="button"
                                    class="inline-flex items-center rounded-md bg-zinc-100 dark:bg-zinc-700 px-2 py-1 text-xs hover:bg-zinc-200 dark:hover:bg-zinc-600 dark:active:bg-green-800"
                                     wire:click="increment('{{ $item->id }}')"
                                    wire:loading.attr="disabled"
                                    aria-label="Augmenter"
                                >
                                    +
                                </button>
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td class="px-4 py-6 text-center text-zinc-500" colspan="6">Aucun résultat.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    </div>

    {{-- Pagination --}}
    @if(method_exists($items, 'links'))
        <div class="w-full">
            {{ $items->links() }}
        </div>
    @endif
</div>
