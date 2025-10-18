{{-- resources/views/livewire/stocks-table.blade.php --}}
<div class="p-6 space-y-4 max-w-none">
    <h1 class="text-4xl font-semibold">Stock</h1>

    {{-- Barre d’actions --}}
    <div class="relative z-30 flex flex-wrap md:flex-nowrap items-center gap-2 md:gap-3 min-w-0">
        <button
            type="button"
            class="shrink-0 inline-flex items-center gap-1.5 rounded-md bg-blue-600 px-3 py-2 text-sm text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
            wire:click="showCreate"
        >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
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

        {{-- Zone en haut à droite : Poids d'actif total --}}
        <div class="ml-auto shrink-0">
            <div class="inline-flex items-center gap-2 rounded-md border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2">
                <span class="text-xs text-zinc-500">Poids d'actif total:</span>
                <span class="tabular-nums font-semibold">
                    {{ number_format((float)($totalPoidsActif ?? 0), 3, ',', ' ') }} kg
                </span>
            </div>
        </div>

        {{-- Actions XLSX --}}
        <div class="flex items-center gap-2">
            <div x-data>
                <input type="file" class="hidden" accept=".xlsx" x-ref="xlsxFile" wire:model="xlsxFile" />
                <button
                    type="button"
                    class="inline-flex items-center gap-1.5 rounded-md bg-zinc-100 dark:bg-zinc-700 px-3 py-2 text-sm text-zinc-900 dark:text-zinc-100 hover:bg-zinc-200 dark:hover:bg-zinc-600"
                    @click="$refs.xlsxFile.click()"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M12 3a4 4 0 0 1 4 4v3h1.25A2.75 2.75 0 0 1 20 12.75v5.5A2.75 2.75 0 0 1 17.25 21H6.75A2.75 2.75 0 0 1 4 18.25v-5.5A2.75 2.75 0 0 1 6.75 10H8V7a4 4 0 0 1 4-4Zm1.5 7V7a1.5 1.5 0 1 0-3 0v3H7.5v2.25h9V10H13.5Z"/>
                    </svg>
                    <span>Importer XLSX</span>
                </button>
                @error('xlsxFile') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <button type="button" class="inline-flex items-center gap-1.5 rounded-md bg-zinc-100 dark:bg-zinc-700 px-3 py-2 text-sm text-zinc-900 dark:text-zinc-100 hover:bg-zinc-200 dark:hover:bg-zinc-600"
                wire:click="exportXlsx">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M12 3.75a.75.75 0 0 1 .75.75v8.69l2.72-2.72a.75.75 0 1 1 1.06 1.06l-4 4a.75.75 0 0 1-1.06 0l-4-4a.75.75 0 1 1 1.06-1.06l2.72 2.72V4.5a.75.75 0 0 1 .75-.75ZM5.25 18a.75.75 0 0 0 0 1.5h13.5a.75.75 0 0 0 0-1.5H5.25Z"/>
                </svg>
                <span>Exporter XLSX</span>
            </button>
        </div>
    </div>

    {{-- Modal création/édition (wire:show) --}}
    <div class="fixed inset-0 z-[9999] flex items-center justify-center" wire:show="showCreateModal">
        <div class="absolute inset-0 bg-black/40" @click="$wire.hideCreate()"></div>

        <div class="relative z-[10000] w-full max-w-lg rounded-lg bg-white dark:bg-zinc-900 shadow-xl border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-start justify-between px-4 py-3 border-b border-zinc-200 dark:border-zinc-700">
                <h2 class="text-base font-semibold">
                    {{ $isEditMode ? 'Éditer un produit' : 'Ajouter un produit' }}
                </h2>
                <button type="button" class="p-1 rounded hover:bg-zinc-100 dark:hover:bg-zinc-800" @click="$wire.hideCreate()" aria-label="Fermer">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"><path d="M6.225 4.811a.75.75 0 0 1 1.06 0L12 9.525l4.715-4.714a.75.75 0 1 1 1.06 1.06L13.06 10.586l4.715 4.715a.75.75 0 1 1-1.06 1.06L12 11.646l-4.715 4.715a.75.75 0 1 1-1.06-1.06l4.714-4.715-4.714-4.715a.75.75 0 0 1 0-1.06Z"/></svg>
                </button>
            </div>

            <form wire:submit="save" class="px-4 py-4 space-y-3">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs text-zinc-600 dark:text-zinc-300 mb-1">Code</label>
                        <input type="number" wire:model="code" class="w-full rounded-md border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-3 py-2 text-sm" />
                        @error('code') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs text-zinc-600 dark:text-zinc-300 mb-1">Référence</label>
                        <input type="text" wire:model="reference" class="w-full rounded-md border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-3 py-2 text-sm" />
                        @error('reference') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs text-zinc-600 dark:text-zinc-300 mb-1">Désignation</label>
                        <input type="text" wire:model="designation" class="w-full rounded-md border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-3 py-2 text-sm" />
                        @error('designation') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs text-zinc-600 dark:text-zinc-300 mb-1">Classe</label>
                        <select wire:model="classe"
                                class="w-full rounded-md border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-3 py-2 text-sm">
                            <option value="">— Sélectionner —</option>
                            <option value="1.3G">1.3G</option>
                            <option value="1.4G">1.4G</option>
                            <option value="1.4S">1.4S</option>
                        </select>
                        @error('classe') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs text-zinc-600 dark:text-zinc-300 mb-1">Stock</label>
                        <input type="number" min="0" wire:model="stock" class="w-full rounded-md border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-3 py-2 text-sm" />
                        @error('stock') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs text-zinc-600 dark:text-zinc-300 mb-1">Poids (kg)</label>
                        <input type="number" step="0.001" wire:model="poids_ma_kg" class="w-full rounded-md border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-3 py-2 text-sm" />
                        @error('poids_ma_kg') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs text-zinc-600 dark:text-zinc-300 mb-1">Emplacement</label>
                        <input type="text" wire:model="emplacement" class="w-full rounded-md border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-3 py-2 text-sm" />
                        @error('emplacement') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2">
                    <button type="button" class="rounded-md px-3 py-2 text-sm border border-zinc-300 dark:border-zinc-700 hover:bg-zinc-100 dark:hover:bg-zinc-800" @click="$wire.hideCreate()">Annuler</button>
                    <button type="submit" class="inline-flex items-center rounded-md bg-blue-600 px-3 py-2 text-sm text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        {{ $isEditMode ? 'Mettre à jour' : 'Enregistrer' }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Tableau --}}
    <div class="relative z-10 w-full overflow-x-auto rounded-md border border-zinc-200 dark:border-zinc-700">
        <table class="w-full table-fixed divide-y divide-zinc-200 dark:divide-zinc-700 text-sm">
            <thead class="bg-zinc-50 dark:bg-zinc-900">
                @php
                    $thBase = 'px-3 py-2 align-middle select-none';
                    $btnBase = 'inline-flex items-center gap-1 cursor-pointer';
                    $iconAsc = '<svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.293 12.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 11-1.414 1.414L10 8.414l-3.293 3.293a1 1 0 01-1.414 0z" clip-rule="evenodd"/></svg>';
                    $iconDesc = '<svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M14.707 7.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4A1 1 0 116.707 7.293L10 10.586l3.293-3.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>';
                    $dirIcon = fn($key) => ($sortBy === $key) ? ($sortDirection === 'asc' ? $iconAsc : $iconDesc) : '';
                @endphp
                <tr>
                    <th class="{{ $thBase }} text-left w-[4ch] whitespace-nowrap">
                        <button type="button" class="{{ $btnBase }}" wire:click="toggleSort('code')">
                            Code {!! $dirIcon('code') !!}
                        </button>
                    </th>
                    <th class="{{ $thBase }} text-left w-[9ch] whitespace-nowrap">
                        <button type="button" class="{{ $btnBase }}" wire:click="toggleSort('reference')">
                            Référence {!! $dirIcon('reference') !!}
                        </button>
                    </th>
                    <th class="{{ $thBase }} text-left w-[30ch]">
                        <button type="button" class="{{ $btnBase }}" wire:click="toggleSort('designation')">
                            Désignation {!! $dirIcon('designation') !!}
                        </button>
                    </th>
                    <th class="{{ $thBase }} text-right w-[10ch] whitespace-nowrap">
                        <button type="button" class="{{ $btnBase }} justify-end" wire:click="toggleSort('classe')">
                            Classe de risque {!! $dirIcon('classe') !!}
                        </button>
                    </th>
                 
                    <th class="{{ $thBase }} text-right w-[10ch] whitespace-nowrap">
                        <button type="button" class="{{ $btnBase }} justify-end" wire:click="toggleSort('poids_total')">
                            Poids d'actif {!! $dirIcon('poids_total') !!}
                        </button>
                    </th>
                    <th class="{{ $thBase }} text-right w-[10ch]">
                        <button type="button" class="{{ $btnBase }} justify-end" wire:click="toggleSort('emplacement')">
                            Emplacement {!! $dirIcon('emplacement') !!}
                        </button>
                    </th>
                       <th class="{{ $thBase }} text-right w-[10ch] whitespace-nowrap">
                        <button type="button" class="{{ $btnBase }} justify-end" wire:click="toggleSort('stock')">
                            Stock {!! $dirIcon('stock') !!}
                        </button>
                    </th>
                    <th class="{{ $thBase }} text-right w-[12ch]">Actions</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700 bg-white dark:bg-zinc-800">
                @forelse($items as $item)
                    <tr class="align-middle">
                        <td class="px-3 py-2 whitespace-nowrap">{{ $item->code }}</td>
                        <td class="px-3 py-2 whitespace-nowrap">{{ $item->reference }}</td>
                        <td class="px-3 py-2 truncate" title="{{ $item->designation }}">{{ $item->designation }}</td>
                        <td class="px-3 py-2 text-right whitespace-nowrap">{{ $item->classe }}</td>
                       
                        <td class="px-3 py-2 text-right whitespace-nowrap">
                            {{ is_null($item->poids_ma_kg*$item->stock) ? '—' : number_format($item->poids_ma_kg*$item->stock, 3, ',', ' ') }}
                        </td>
                        <td class="px-3 py-2 text-right whitespace-nowrap">{{ $item->emplacement }}</td>
                         <td class="px-3 py-2 text-right whitespace-nowrap">{{ $item->stock }}</td>
                        <td class="px-3 py-2 text-right">
                            <span class="inline-flex items-center justify-end gap-2">
                                <button type="button"
                                    class="inline-flex items-center rounded-md bg-zinc-100 dark:bg-zinc-700 px-2 py-1 text-xs hover:bg-zinc-200 dark:hover:bg-blue-900"
                                    wire:click="edit({{ $item->id }})" aria-label="Éditer" title="Éditer">
                                    {{-- Icone crayon flux --}}
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                        <path d="M17.862 3.487a2.75 2.75 0 0 1 3.888 3.888l-9.903 9.903-5.25 1.75a1 1 0 0 1-1.266-1.265l1.75-5.251 9.781-9.825Zm-2.598 2.598L7.52 14.83l-.959 2.874 2.874-.96 7.785-7.785-2.874-2.874Z"/>
                                        <path d="M4.25 5A2.25 2.25 0 0 0 2 7.25v13.5A2.25 2.25 0 0 0 4.25 23h13.5A2.25 2.25 0 0 0 20 20.75V16a1 1 0 1 1 2 0v4.75A4.25 4.25 0 0 1 17.75 25H4.25A4.25 4.25 0 0 1 .001 20.75V7.25A4.25 4.25 0 0 1 4.25 3h4a1 1 0 1 1 0 2h-4Z"/>
                                    
                                </button>

                                <button type="button"
                                    class="inline-flex items-center rounded-md bg-zinc-100 dark:bg-zinc-700 px-2 py-1 text-xs hover:bg-zinc-200 dark:hover:bg-red-800 dark:active:bg-red-500"
                                    wire:click="decrement('{{ $item->id }}')"
                                    wire:loading.attr="disabled" aria-label="Diminuer" title="Enlever une unité du stock">
                                    {{-- Icone - flux --}}
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                        <path d="M5.25 10.5a1 1 0 0 1 1-1h11.5a1 1 0 1 1 0 2H6.25a1 1 0 0 1-1-1Z"/>
</svg>
                                    </button>

                                <button type="button"
                                    class="inline-flex items-center rounded-md bg-zinc-100 dark:bg-zinc-700 px-2 py-1 text-xs hover:bg-zinc-200 dark:hover:bg-green-800 dark:active:bg-green-500"
                                    wire:click="increment('{{ $item->id }}')" wire:loading.attr="disabled" aria-label="Augmenter " title="Ajouter une unité au stock">
                                    {{-- Icone + flux --}}
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                        <path d="M12 5.25a1 1 0 0 1 1 1v5.75h5.75a1 1 0 1 1 0 2H13v5.75a1 1 0 1 1-2 0V13H5.25a1 1 0 1 1 0-2H11V6.25a1 1 0 0 1 1-1Z"/>
                                    </svg>
                                    </button>
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td class="px-4 py-6 text-center text-zinc-500" colspan="9">Aucun résultat.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if(method_exists($items, 'links'))
        <div class="w-full">
            {{ $items->links('custom-pagination-links') }}
        </div>
    @endif
</div>
