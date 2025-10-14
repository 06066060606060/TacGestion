<?php
// app/Livewire/StocksTable.php

namespace App\Livewire;

use App\Models\Stock;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

class StocksTable extends Component
{
    use WithPagination;

    public string $search = '';

    protected $queryString = ['search'];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    // Retirer le type-hint strict pour éviter la DI
    public function increment($id): void
    {
        $item = Stock::find($id);
        if ($item) {
            $item->increment('stock', 1);
        }
    }

    public function decrement($id): void
    {
        $item = Stock::find($id);
        if ($item && $item->stock > 0) {
            $item->decrement('stock', 1);
        }
    }

    #[Layout('layouts.app')]
    public function render()
    {
        $term = $this->search ? "%{$this->search}%" : null;

        $items = Stock::query()
            ->when($term, function ($q) use ($term) {
                $q->where(function ($w) use ($term) {
                    $w->where('code', 'like', $term)
                      ->orWhere('reference', 'like', $term)
                      ->orWhere('designation', 'like', $term);
                });
            })
            ->orderBy('designation')
            ->paginate(25);

        return view('livewire.stocks-table', [
            'items' => $items,
        ]);
    }
}
