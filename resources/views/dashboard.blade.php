<x-layouts.app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="grid auto-rows-min gap-4 md:grid-cols-3">
            <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
            </div>
            <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
            </div>

            {{-- 3e placeholder : cellules A (libellé) et B (valeur) --}}
<div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
    <div class="absolute inset-0 flex flex-col items-center justify-center gap-2 p-4">
            <span class="text-sm text-neutral-600 dark:text-neutral-300">
                Poids total d'actif
            </span>
          
            <span class="tabular-nums text-2xl font-semibold">
                {{ number_format((float)($poidsTotal ?? 0), 3, ',', ' ') }} kg
            </span>
    </div>
    {{-- motif de fond optionnel --}}
    <x-placeholder-pattern class="pointer-events-none absolute inset-0 size-full stroke-gray-900/10 dark:stroke-neutral-100/10" />
</div>

         <div>
                {{-- motif de fond optionnel --}}
                <x-placeholder-pattern class="pointer-events-none absolute inset-0 size-full stroke-gray-900/10 dark:stroke-neutral-100/10" />
            </div>
        </div>

        <div class="relative h-full flex-1 overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
            <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
        </div>
    </div>
</x-layouts.app>
