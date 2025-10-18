{{-- resources/views/livewire/articles-table.blade.php --}}
<div class="p-6 space-y-4 max-w-none">
    <h1 class="text-4xl font-semibold">Produits</h1>

    {{-- Barre d’actions --}}
    <div class="relative z-30 flex flex-wrap md:flex-nowrap items-center gap-2 md:gap-3 min-w-0">
        <button type="button"
            class="shrink-0 inline-flex items-center gap-1.5 rounded-md bg-blue-600 px-3 py-2 text-sm text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
            wire:click="create">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 5c.414 0 .75.336.75.75V11.25H18.25a.75.75 0 0 1 0 1.5H12.75V18.25a.75.75 0 0 1-1.5 0V12.75H5.75a.75.75 0 0 1 0-1.5H11.25V5.75c0-.414.336-.75.75-.75Z" />
            </svg>
            <span>Ajouter</span>
        </button>

        <div class="flex-1 min-w-0">
            <input type="text"
                placeholder="Rechercher par code, référence, désignation, type, famille..."
                class="w-full rounded-md border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-3 py-2 text-sm"
                wire:model.live.debounce.300ms="search" />
        </div>

        <div class="flex items-center gap-2">
            <div x-data>
                <input type="file" class="hidden" accept=".xlsx" x-ref="xlsxFile" wire:model="xlsxFile" />
                <button type="button"
                    class="inline-flex items-center gap-1.5 rounded-md bg-zinc-100 dark:bg-zinc-700 px-3 py-2 text-sm text-zinc-900 dark:text-zinc-100 hover:bg-zinc-200 dark:hover:bg-zinc-600"
                    @click="$refs.xlsxFile.click()">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 3a4 4 0 0 1 4 4v3h1.25A2.75 2.75 0 0 1 20 12.75v5.5A2.75 2.75 0 0 1 17.25 21H6.75A2.75 2.75 0 0 1 4 18.25v-5.5A2.75 2.75 0 0 1 6.75 10H8V7a4 4 0 0 1 4-4Zm1.5 7V7a1.5 1.5 0 1 0-3 0v3H7.5v2.25h9V10H13.5Z" />
                    </svg>
                    <span>Importer XLSX</span>
                </button>
                @error('xlsxFile') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <button type="button"
                class="inline-flex items-center gap-1.5 rounded-md bg-zinc-100 dark:bg-zinc-700 px-3 py-2 text-sm text-zinc-900 dark:text-zinc-100 hover:bg-zinc-200 dark:hover:bg-zinc-600"
                wire:click="exportXlsx">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 3.75a.75.75 0 0 1 .75.75v8.69l2.72-2.72a.75.75 0 1 1 1.06 1.06l-4 4a.75.75 0 0 1-1.06 0l-4-4a.75.75 0 1 1 1.06-1.06l2.72 2.72V4.5a.75.75 0 0 1 .75-.75ZM5.25 18a.75.75 0 0 0 0 1.5h13.5a.75.75 0 0 0 0-1.5H5.25Z" />
                </svg>
                <span>Exporter XLSX</span>
            </button>
        </div>
    </div>

    {{-- Filtres --}}
    <div class="flex flex-wrap items-center gap-2 md:gap-3">
        <select wire:model.change="filterType"
            class="rounded-md border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-2 py-1.5 text-sm">
            <option value="">Types</option>
            @foreach($types as $opt)
            <option value="{{ $opt }}">{{ $opt }}</option>
            @endforeach
        </select>

        <select wire:model.change="filterFamille"
            class="rounded-md border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-2 py-1.5 text-sm">
            <option value="">Familles</option>
            @foreach($familles as $opt)
            <option value="{{ $opt }}">{{ $opt }}</option>
            @endforeach
        </select>

        <select wire:model.change="filterCalibre"
            class="rounded-md border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-2 py-1.5 text-sm">
            <option value="">Calibres</option>
            @foreach($calibres as $opt)
            <option value="{{ $opt }}">{{ $opt }}</option>
            @endforeach
        </select>
        <select wire:model.change="filterCategorie"
            class="rounded-md border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-2 py-1.5 text-sm">
            <!-- Placeholder neutre -->
            <option value="">{{ __('Catégories') }}</option>

            @foreach($categories as $opt)
            @php
            // Normaliser la clé/valeur si $categories est un tableau de valeurs simples
            $value = is_array($opt) ? ($opt['value'] ?? '') : $opt;
            $label = is_array($opt) ? ($opt['label'] ?? $value) : $opt;
            $isUnclassified = ($value === 0 || $value === '0' || $value === null || $value === '');
            @endphp

            <option value="{{ $isUnclassified ? 0 : $value }}">
                {{ $isUnclassified ? __('Non classé') : $label }}
            </option>
            @endforeach
        </select>
    </div>

    {{-- Modal Flexbox: petits champs par 3 --}}
    <div class="fixed inset-0 z-[9999] flex items-center justify-center p-3" wire:show="showModal">
        <div class="absolute inset-0 bg-black/40" @click="$wire.hide()"></div>

        <div class="relative z-[10000] w-full max-w-xl rounded-lg bg-white dark:bg-zinc-900 shadow-xl border border-zinc-200 dark:border-zinc-700">
            {{-- Header --}}
            <div class="flex items-start justify-between px-4 py-3 border-b border-zinc-200 dark:border-zinc-700">
                <h2 class="text-base font-semibold">{{ $isEditMode ? 'Éditer un article' : 'Ajouter un article' }}</h2>
                <button type="button" class="p-1 rounded hover:bg-zinc-100 dark:hover:bg-zinc-800" @click="$wire.hide()" aria-label="Fermer">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M6.225 4.811a.75.75 0 0 1 1.06 0L12 9.525l4.715-4.714a.75.75 0 1 1 1.06 1.06L13.06 10.586l4.715 4.715a.75.75 0 1 1-1.06 1.06L12 11.646l-4.715 4.715a.75.75 0 1 1-1.06-1.06l4.714-4.715-4.714-4.715a.75.75 0 0 1 0-1.06Z" />
                    </svg>
                </button>
            </div>

            <form wire:submit="save" class="px-4 py-4 space-y-6">
                {{-- Ligne: champs courts en flex, 3 par ligne --}}
                <div class="flex flex-wrap gap-3">
                    {{-- Utiliser basis-* responsive: 100% en xs, 50% en sm, 33.333% en md+ --}}
                    <div class="flex-1 min-w-[8rem] sm:basis-1/2 md:basis-1/4">
                        <label class="block text-xs text-zinc-600 dark:text-zinc-300 mb-1">Code</label>
                        <input type="text" maxlength="4" size="4"
                            class="w-full rounded-md border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-2 py-1.5 text-sm"
                            wire:model="code" />
                        @error('code') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex-1 min-w-[8rem] sm:basis-1/2 md:basis-1/4">
                        <label class="block text-xs text-zinc-600 dark:text-zinc-300 mb-1">Référence</label>
                        <input type="text" maxlength="11" size="11" placeholder="ACC...."
                            class="w-full rounded-md border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-2 py-1.5 text-sm"
                            wire:model="reference" />
                        @error('reference') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex-1 min-w-[8rem] sm:basis-1/2 md:basis-1/4">
                        <label class="block text-xs text-zinc-600 dark:text-zinc-300 mb-1">Code source</label>
                        <input type="text" maxlength="11" size="11"
                            class="w-full rounded-md border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-2 py-1.5 text-sm"
                            wire:model="code_source" />
                        @error('code_source') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                </div>
                <div class="flex flex-wrap gap-3">

                    <div class="flex-1 min-w-[6rem] sm:basis-1/2 md:basis-1/4">
                        <label class="block text-xs text-zinc-600 dark:text-zinc-300 mb-1">Type</label>
                        <select class="w-full rounded-md border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-2 py-1.5 text-sm"
                            wire:model="type" />
                        <option value="">—</option>
                        @foreach($types as $opt)
                        <option value="{{ $opt }}">{{ $opt }}</option>
                        @endforeach
                        </select>
                        @error('type') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex-1 min-w-[10rem] sm:basis-1/2 md:basis-1/4">
                        <label class="block text-xs text-zinc-600 dark:text-zinc-300 mb-1">Famille</label>
                        <select class="w-full rounded-md border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-2.5 py-1.5 text-sm"
                            wire:model="famille" />
                        <option value="">—</option>
                        @foreach($familles as $opt)
                        <option value="{{ $opt }}">{{ $opt }}</option>
                        @endforeach
                        </select>
                        @error('famille') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>


                    <div class="flex-1 min-w-[6rem] sm:basis-1/2 md:basis-1/4">
                        <label class="block text-xs text-zinc-600 dark:text-zinc-300 mb-1">Calibre</label>
                        <select class="w-full rounded-md border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-2 py-1.5 text-sm"
                            wire:model="calibre">
                            <option value="">—</option>
                            @foreach($calibres as $opt)
                            <option value="{{ $opt }}">{{ $opt }}</option>
                            @endforeach
                        </select>
                        @error('calibre') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex-1 min-w-[12rem] sm:basis-1/2 md:basis-1/4">
                        <label class="block text-xs text-zinc-600 dark:text-zinc-300 mb-1">Durée</label>
                        <input type="number" min="0" max="999" step="1"
                            class="w-full rounded-md border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-2 py-1.5 text-sm"
                            wire:model="duree" />
                        @error('duree') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex-1 min-w-[12rem] sm:basis-1/2 md:basis-1/4">
                        <label class="block text-xs text-zinc-600 dark:text-zinc-300 mb-1">Catégorie</label>
                        <select class="w-full rounded-md border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-2.5 py-1.5 text-sm"
                            wire:model="categorie">
                            <option value="">—</option>
                            @foreach($categories as $opt)
                            <option value="{{ $opt }}">{{ $opt }}</option>
                            @endforeach
                        </select>
                        @error('categorie') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Bloc Désignation + trio compact à droite --}}
                <div class="flex flex-wrap gap-3">
                    <div class="flex-1 min-w-[16rem] md:basis-2/3">
                        <label class="block text-xs text-zinc-600 dark:text-zinc-300 mb-1">Désignation</label>
                        <input type="text" maxlength="100" size="100"
                            class="w-full rounded-md border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-3 py-2 text-sm"
                            wire:model="designation" />
                        @error('designation') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex flex-wrap gap-3">
                    <div class="flex-1 min-w-[12rem] sm:basis-1/2 md:basis-1/4">
                        <label class="block text-xs text-zinc-600 dark:text-zinc-300 mb-1">Certification</label>
                        <input type="text"
                            class="w-full rounded-md border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-2.5 py-1.5 text-sm"
                            wire:model="certification" />
                        @error('certification') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div class="flex-1 min-w-[12rem] sm:basis-1/2 md:basis-1/4">
                        <label class="block text-xs text-zinc-600 dark:text-zinc-300 mb-1">Classe de risque</label>
                        <select class="w-full rounded-md border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-2 py-1.5 text-sm"
                            wire:model="classe_risque">
                            <option value="">—</option>
                            <option value="1.3G">1.3G</option>
                            <option value="1.4G">1.4G</option>
                            <option value="1.4S">1.4S</option>
                        </select>
                        @error('classe_risque') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Ligne métriques/prix: 3 par ligne grâce à basis-1/3 --}}
                <div class="flex flex-wrap gap-3">
                    <div class="flex-1 min-w-[6rem] sm:basis-1/2 md:basis-1/4">
                        <label class="block text-xs text-zinc-600 dark:text-zinc-300 mb-1">Poids (M.A)</label>
                        <input type="number" min="0" max="9999" step="0.001" placeholder="0,00"
                            class="w-full rounded-md border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-2 py-1.5 text-sm"
                            wire:model="poids_m_a" />
                        @error('poids_m_a') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div class="flex-1 min-w-[6rem] sm:basis-1/2 md:basis-1/4">
                        <label class="block text-xs text-zinc-600 dark:text-zinc-300 mb-1">Distance</label>
                        <input type="number" min="0" max="999" step="1" placeholder="0"
                            class="w-full rounded-md border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-2 py-1.5 text-sm"
                            wire:model="distance_securite" />
                        @error('distance_securite') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div class="flex-1 min-w-[6rem] sm:basis-1/2 md:basis-1/4">
                        <label class="block text-xs text-zinc-600 dark:text-zinc-300 mb-1">Tarif pièce</label>
                        <input type="number" min="0" max="999" step="0.01" placeholder="0.0"
                            class="w-full rounded-md border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-2 py-1.5 text-sm"
                            wire:model="tarif_piece" />
                        @error('tarif_piece') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div class="flex-1 min-w-[6rem] sm:basis-1/2 md:basis-1/4">
                        <label class="block text-xs text-zinc-600 dark:text-zinc-300 mb-1">CDT</label>
                        <input type="number" min="0" max="999" step="1" placeholder="0"
                            class="w-full rounded-md border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-2 py-1.5 text-sm"
                            wire:model="cdt" />
                        @error('cdt') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div class="flex-1 min-w-[6rem] sm:basis-1/2 md:basis-1/4">
                        <label class="block text-xs text-zinc-600 dark:text-zinc-300 mb-1">Tarif caisse</label>
                        <input type="text" inputmode="decimal" maxlength="5" size="5" placeholder="0.00"
                            class="w-full rounded-md border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-2 py-1.5 text-sm"
                            wire:model="tarif_caisse" />
                        @error('tarif_caisse') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- URLs: 2 par ligne en sm, 3 en md+ --}}
                <div class="flex flex-wrap gap-3">
                    <div class="flex-1 min-w-[10rem] sm:basis-1/2 md:basis-1/4">
                        <label class="block text-xs text-zinc-600 dark:text-zinc-300 mb-1">Vidéo</label>
                        <input type="text"
                            class="w-full rounded-md border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-3 py-2 text-sm"
                            wire:model="video" />
                        @if(!empty($video))
                        <a href="{{ $video }}" target="_blank" rel="noopener noreferrer"
                            class="shrink-0 inline-flex items-center rounded-md px-2 py-1 text-xs text-blue-600 dark:text-blue-400 hover:underline">
                            Ouvrir
                        </a>
                        @endif
                        @error('video') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div class="flex-1 min-w-[10rem] sm:basis-1/2 md:basis-1/4">
                        <label class="block text-xs text-zinc-600 dark:text-zinc-300 mb-1">Photo</label>
                        <input type="text"
                            class="w-full rounded-md border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-3 py-2 text-sm"
                            wire:model="photo" />
                        @if(!empty($photo))
                        <a href="{{ $photo }}" target="_blank" rel="noopener noreferrer"
                            class="shrink-0 inline-flex items-center rounded-md px-2 py-1 text-xs text-blue-600 dark:text-blue-400 hover:underline">
                            Ouvrir
                        </a>
                        @endif
                        @error('photo') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                </div>

                {{-- Notes + Options (2/3 + 1/3) --}}
                <div class="flex flex-wrap gap-3">
                    <div class="flex-1 min-w-[16rem] md:basis-2/3">
                        <label class="block text-xs text-zinc-600 dark:text-zinc-300 mb-1">Note</label>
                        <textarea rows="1"
                            class="w-full rounded-md border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-3 py-2 text-sm"
                            wire:model="note"></textarea>
                        @error('note') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex-1 min-w-[6rem] md:basis-1/3">
                        <label class="inline-flex items-center gap-2">
                            <input
                                id="options"
                                type="checkbox"
                                class="rounded border-zinc-300 dark:border-zinc-700 text-zinc-700 dark:text-zinc-200 focus:ring-zinc-500"
                                wire:model="optionsChecked">
                            <span>Favoris</span>

                        </label>


                        @error('options')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>



                </div>

                {{-- Footer --}}
                <div class="flex items-center justify-between gap-2 pt-2">
                    <div>
                        @if($isEditMode)
                        <button type="button"
                            class="inline-flex items-center gap-1.5 rounded-md bg-red-600 px-3 py-2 text-sm text-white shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                            wire:click="delete"
                            wire:confirm="Confirmer la suppression de cet article ?">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M9 3.75A1.75 1.75 0 0 1 10.75 2h2.5A1.75 1.75 0 0 1 15 3.75V5h3.25a.75.75 0 0 1 0 1.5H5.75a.75.75 0 0 1 0-1.5H9V3.75ZM6.75 8.5h10.5l-.62 10.083A2.75 2.75 0 0 1 13.894 21H10.106a2.75 2.75 0 0 1-2.736-2.417L6.75 8.5Z" />
                            </svg>
                            <span>Supprimer</span>
                        </button>
                        @endif
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" class="rounded-md px-3 py-2 text-sm border border-zinc-300 dark:border-zinc-700 hover:bg-zinc-100 dark:hover:bg-zinc-800" @click="$wire.hide()">Annuler</button>
                        <button type="submit" class="inline-flex items-center rounded-md bg-blue-600 px-3 py-2 text-sm text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            {{ $isEditMode ? 'Mettre à jour' : 'Enregistrer' }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>



    <div class="mb-3 border-b border-zinc-200 dark:border-zinc-700">
        <nav class="flex flex-wrap gap-2 pb-2" role="tablist">
            @php
            $tabs = ['FAVORIS', 'TOUS','BOMBES','PACK','CHANDELLES','EVENTAILS','MONOCOUPS','COLIS','PAT','FEUX','AUTRE'];
            $isActive = fn($t) => $tab === $t
            ? 'bg-zinc-900 text-white dark:bg-zinc-100 dark:text-zinc-900'
            : 'bg-zinc-100 text-zinc-700 hover:bg-zinc-200 dark:bg-zinc-700 dark:text-zinc-200 dark:hover:bg-zinc-600';
            @endphp
            @foreach($tabs as $t)
            <button type="button"
                class="px-3 py-1.5 text-sm rounded-md {{ $isActive($t) }}"
                wire:click="$set('tab','{{ $t }}')"
                role="tab"
                aria-selected="{{ $tab === $t ? 'true' : 'false' }}">
                {{ $t }}
            </button>
            @endforeach
        </nav>
    </div>


    {{-- Tableau --}}
    <div class="relative z-10 w-full overflow-x-auto rounded-md border border-zinc-200 dark:border-zinc-700">
        <table class="w-full table-fixed divide-y divide-zinc-200 dark:divide-zinc-700 text-sm">
            <thead class="bg-zinc-50 dark:bg-zinc-900">
                @php
                $th = 'px-3 py-2 align-middle select-none';
                $btn = 'inline-flex items-center gap-1 cursor-pointer';
                $asc = '<svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M5.293 12.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 11-1.414 1.414L10 8.414l-3.293 3.293a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                </svg>';
                $desc = '<svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M14.707 7.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4A1 1 0 116.707 7.293L10 10.586l3.293-3.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>';
                $icon = fn($k) => ($sortBy === $k) ? ($sortDirection === 'asc' ? $asc : $desc) : '';
                @endphp
                <tr>
                    <th class="{{ $th }} text-left w-[10ch]"><button class="{{ $btn }}" wire:click="toggleSort('code')">Code {!! $icon('code') !!}</button></th>
                    <th class="{{ $th }} text-left w-[16ch]"><button class="{{ $btn }}" wire:click="toggleSort('reference')">Référence {!! $icon('reference') !!}</button></th>
                    <th class="{{ $th }} text-left w-[34ch]"><button class="{{ $btn }}" wire:click="toggleSort('designation')">Désignation {!! $icon('designation') !!}</button></th>
                    <th class="{{ $th }} text-left w-[16ch]"><button class="{{ $btn }}" wire:click="toggleSort('famille')">Famille {!! $icon('famille') !!}</button></th>
                    <th class="{{ $th }} text-left w-[6ch]"><button class="{{ $btn }}" wire:click="toggleSort('calibre')">Calibre {!! $icon('calibre') !!}</button></th>
                    <th class="{{ $th }} text-left w-[6ch]">Catégorie</th>
                    <th class="{{ $th }} text-right w-[12ch]"><button class="{{ $btn }} justify-end" wire:click="toggleSort('tarif_piece')">Tarif pièce {!! $icon('tarif_piece') !!}</button></th>
                    <th class="{{ $th }} text-right w-[12ch]"><button class="{{ $btn }} justify-end" wire:click="toggleSort('tarif_caisse')">Tarif caisse {!! $icon('tarif_caisse') !!}</button></th>
                    <th class="{{ $th }} text-left w-[10ch]"><button class="{{ $btn }}" wire:click="toggleSort('duree')">Duree {!! $icon('duree') !!}</button></th>
                    <th class="{{ $th }} text-right w-[10ch]">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700 bg-white dark:bg-zinc-800">
                @forelse($items as $it)
                <tr class="align-middle">
                    <td class="px-3 py-2 whitespace-nowrap">{{ $it->code }}</td>
                    <td class="px-3 py-2 whitespace-nowrap">{{ $it->reference !== '' && $it->reference !== null ? $it->reference : $it->code_source }}</td>
                    <td class="px-3 py-2 truncate" title="{{ $it->designation }}">{{ $it->designation }}</td>
                    <td class="px-3 py-2 truncate whitespace-nowrap">{{ $it->famille }}</td>
                    <td class="px-3 py-2 whitespace-nowrap">{{ $it->calibre }}</td>
                    <td class="px-3 py-2 whitespace-nowrap">{{ $it->categorie }}</td>
                    <td class="px-3 py-2 text-right whitespace-nowrap">
                        {{ is_null($it->tarif_piece) ? '—' : number_format($it->tarif_piece, 2, ',', ' ') }}
                    </td>
                    <td class="px-3 py-2 text-right whitespace-nowrap">
                        {{ is_null($it->tarif_caisse) ? '—' : number_format($it->tarif_caisse, 2, ',', ' ') }}
                    </td>
                    <td class="px-3 py-2 whitespace-nowrap">{{ $it->duree }}</td>
                    <td class="px-3 py-2 text-right">
                        <button
                            type="button"
                            class="inline-flex items-center rounded-md bg-zinc-100 dark:bg-zinc-700 px-2 py-1 text-xs hover:bg-zinc-200 dark:hover:bg-zinc-600"
                            wire:click="edit({{ $it->id }})"
                            title="Éditer">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 3C7 3 2.73 7.11 2.05 11.97c-.19.92-.19 1.88 0 2.8C2.73 16.89 7 21 12 21s9.27-4.11 9.95-8.97c.19-.92.19-1.88 0-2.8C21.27 7.11 17 3 12 3Zm0 16a6.5 6.5 0 1 1 0-13 6.5 6.5 0 0 1 0 13Zm0-11a4.5 4.5 0 1 0 0 9 4.5 4.5 0 0 0 0-9Z" />
                            </svg>
                        </button>

                    </td>
                </tr>
                @empty
                <tr>
                    <td class="px-4 py-6 text-center text-zinc-500" colspan="12">Aucun article.</td>
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