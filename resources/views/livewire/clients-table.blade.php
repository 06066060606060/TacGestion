{{-- resources/views/livewire/client-table.blade.php --}}
<div class="p-6 space-y-4 max-w-none">
    <h1 class="text-4xl font-semibold">Clients</h1>

    {{-- Barre d’actions --}}
    <div class="relative z-30 flex flex-wrap md:flex-nowrap items-center gap-2 md:gap-3 min-w-0">
        <button
            type="button"
            class="shrink-0 inline-flex items-center gap-1.5 rounded-md bg-blue-600 px-3 py-2 text-sm text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
            wire:click="create">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                <path d="M12 5c.414 0 .75.336.75.75V11.25H18.25a.75.75 0 0 1 0 1.5H12.75V18.25a.75.75 0 0 1-1.5 0V12.75H5.75a.75.75 0 0 1 0-1.5H11.25V5.75c0-.414.336-.75.75-.75Z" />
            </svg>
            <span>Ajouter</span>
        </button>

        <div class="flex-1 min-w-0">
            <input
                type="text"
                placeholder="Rechercher par nom, ville, code postal ou téléphone..."
                class="w-full rounded-md border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-3 py-2 text-sm"
                wire:model.live.debounce.300ms="search" />
        </div>
    </div>

    {{-- Modal création/édition --}}
    <div class="fixed inset-0 z-[9999] flex items-center justify-center" wire:show="showModal">
        <div class="absolute inset-0 bg-black/40" @click="$wire.hide()"></div>

        <div class="relative z-[10000] w-full max-w-lg rounded-lg bg-white dark:bg-zinc-900 shadow-xl border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-start justify-between px-4 py-3 border-b border-zinc-200 dark:border-zinc-700">
                <h2 class="text-base font-semibold">
                    {{ $isEditMode ? 'Éditer un client' : 'Ajouter un client' }}
                </h2>
                <button type="button" class="p-1 rounded hover:bg-zinc-100 dark:hover:bg-zinc-800" @click="$wire.hide()" aria-label="Fermer">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M6.225 4.811a.75.75 0 0 1 1.06 0L12 9.525l4.715-4.714a.75.75 0 1 1 1.06 1.06L13.06 10.586l4.715 4.715a.75.75 0 1 1-1.06 1.06L12 11.646l-4.715 4.715a.75.75 0 1 1-1.06-1.06l4.714-4.715-4.714-4.715a.75.75 0 0 1 0-1.06Z" />
                    </svg>
                </button>
            </div>

            <form wire:submit="save" class="px-4 py-4 space-y-3">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="sm:col-span-2">
                        <label class="block text-xs text-zinc-600 dark:text-zinc-300 mb-1">Nom</label>
                        <input type="text" wire:model="nom" class="w-full rounded-md border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-3 py-2 text-sm" />
                        @error('nom') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs text-zinc-600 dark:text-zinc-300 mb-1">Adresse</label>
                        <input type="text" wire:model="adresse" class="w-full rounded-md border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-3 py-2 text-sm" />
                        @error('adresse') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs text-zinc-600 dark:text-zinc-300 mb-1">Code postal</label>
                        <input type="text" wire:model="code_postal" class="w-full rounded-md border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-3 py-2 text-sm" />
                        @error('code_postal') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs text-zinc-600 dark:text-zinc-300 mb-1">Ville</label>
                        <input type="text" wire:model="ville" class="w-full rounded-md border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-3 py-2 text-sm" />
                        @error('ville') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    {{-- le lieu de tir en multi ligne --}}
                    <div class="sm:col-span-2">
                        <label class="block text-xs text-zinc-600 dark:text-zinc-300 mb-1">Lieu de tir</label>
                        <textarea wire:model="lieu_de_tir" rows="2" class="w-full rounded-md border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-3 py-2 text-sm"></textarea>
                        @error('lieu_de_tir') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs text-zinc-600 dark:text-zinc-300 mb-1">Téléphone</label>
                        <input type="text" wire:model="telephone" class="w-full rounded-md border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-3 py-2 text-sm" />
                        @error('telephone') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs text-zinc-600 dark:text-zinc-300 mb-1">Email</label>
                        <input type="email" wire:model="email" class="w-full rounded-md border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-3 py-2 text-sm" />
                        @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror

                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2">
                    <button type="button" class="rounded-md px-3 py-2 text-sm border border-zinc-300 dark:border-zinc-700 hover:bg-zinc-100 dark:hover:bg-zinc-800" @click="$wire.hide()">Annuler</button>
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
                $iconAsc = '<svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M5.293 12.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 11-1.414 1.414L10 8.414l-3.293 3.293a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                </svg>';
                $iconDesc = '<svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M14.707 7.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4A1 1 0 116.707 7.293L10 10.586l3.293-3.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>';
                $dirIcon = fn($key) => ($sortBy === $key) ? ($sortDirection === 'asc' ? $iconAsc : $iconDesc) : '';
                @endphp
                <tr>
                    <th class="{{ $thBase }} text-left w-[24ch]">
                        <button type="button" class="{{ $btnBase }}" wire:click="toggleSort('nom')">
                            Nom {!! $dirIcon('nom') !!}
                        </button>
                    </th>
                    <th class="{{ $thBase }} text-left w-[30ch]">Adresse</th>
                    <th class="{{ $thBase }} text-left w-[12ch]">
                        <button type="button" class="{{ $btnBase }}" wire:click="toggleSort('code_postal')">
                            Code postal {!! $dirIcon('code_postal') !!}
                        </button>
                    </th>
                    <th class="{{ $thBase }} text-left w-[18ch]">
                        <button type="button" class="{{ $btnBase }}" wire:click="toggleSort('ville')">
                            Ville {!! $dirIcon('ville') !!}
                        </button>
                    </th>
                    <th class="{{ $thBase }} text-left w-[22ch]">Lieu de tir</th>
                    <th class="{{ $thBase }} text-left w-[18ch]">
                        <button type="button" class="{{ $btnBase }}" wire:click="toggleSort('telephone')">
                            Téléphone {!! $dirIcon('telephone') !!}
                        </button>
                    </th>
                    <th class="{{ $thBase }} text-left w-[22ch]">Email</th>
            
                    <th class="{{ $thBase }} text-right w-[12ch]">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700 bg-white dark:bg-zinc-800">
                @forelse($clients as $client)
                <tr class="align-middle">
                    <td class="px-3 py-2 whitespace-nowrap">{{ $client->nom }}</td>
                    <td class="px-3 py-2 whitespace-nowrap">{{ $client->adresse }}</td>
                    <td class="px-3 py-2 whitespace-nowrap">{{ $client->code_postal }}</td>
                    <td class="px-3 py-2 whitespace-nowrap">{{ $client->ville }}</td>
                    <td class="px-3 py-2 whitespace-nowrap">{{ $client->lieu_de_tir }}</td>
                    <td class="px-3 py-2 whitespace-nowrap">{{ $client->telephone }}</td>
                    <td class="px-3 py-2 whitespace-nowrap">{{ $client->email }}</td>
                    <td class="px-3 py-2 text-right">
                        <span class="inline-flex items-center justify-end gap-2">
                            <button type="button"
                                class="inline-flex items-center rounded-md bg-zinc-100 dark:bg-zinc-700 px-2 py-1 text-xs hover:bg-zinc-200 dark:hover:bg-zinc-600"
                                wire:click="edit({{ $client->id }})">
                                Éditer
                            </button>
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td class="px-4 py-6 text-center text-zinc-500" colspan="7">Aucun client.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if(method_exists($clients, 'links'))
    <div class="w-full">
        {{ $clients->links() }}
    </div>
    @endif
</div>