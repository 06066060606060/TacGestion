<?php

namespace App\Livewire;

use App\Models\Client;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

class ClientsTable extends Component
{
    use WithPagination;

    // Recherche et tri
    public string $search = '';
    public string $sortBy = 'nom';
    public string $sortDirection = 'asc';
    protected array $sortable = ['nom','ville','code_postal','telephone','email'];

    // Modal + mode
    public bool $showModal = false;
    public bool $isEditMode = false;
    public ?int $editingId = null;

    // Form
    public ?string $nom = null;
    public ?string $adresse = null;
    public ?string $code_postal = null;
    public ?string $ville = null;
    public ?string $lieu_de_tir = null;
    public ?string $telephone = null;
    public ?string $email = null;

    // Query string
    protected $queryString = [
        'search',
        'sortBy' => ['except' => 'nom'],
        'sortDirection' => ['except' => 'asc'],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    // Tri
    public function toggleSort(string $column): void
    {
        if (!in_array($column, $this->sortable, true)) {
            return;
        }
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    // Ouvrir modal (création)
    public function create(): void
    {
        $this->resetErrorBag();
        $this->resetValidation();

        $this->isEditMode = false;
        $this->editingId = null;

        $this->nom = null;
        $this->adresse = null;
        $this->code_postal = null;
        $this->ville = null;
        $this->lieu_de_tir = null;
        $this->telephone = null;
        $this->email = null;

        $this->showModal = true;
    }

    // Ouvrir modal (édition)
    public function edit(int $id): void
    {
        $this->resetErrorBag();
        $this->resetValidation();

        $client = Client::find($id);
        if (!$client) {
            return;
        }

        $this->isEditMode = true;
        $this->editingId = $client->id;

        $this->nom = $client->nom;
        $this->adresse = $client->adresse;
        $this->code_postal = $client->code_postal;
        $this->ville = $client->ville;
        $this->lieu_de_tir = $client->lieu_de_tir;
        $this->telephone = $client->telephone;
        $this->email = $client->email;

        $this->showModal = true;
    }

    public function hide(): void
    {
        $this->showModal = false;
    }

    // Enregistrer
    public function save(): void
    {
        $validated = $this->validate([
            'nom' => ['required','string','max:255'],
            'adresse' => ['nullable','string','max:255'],
            'code_postal' => ['nullable','string','max:32'],
            'ville' => ['nullable','string','max:255'],
            'lieu_de_tir' => ['nullable','string','max:255'],
            'telephone' => ['nullable','string','max:64'],
            'email' => ['nullable','string','max:255','email'],
        ]); // validation Livewire/Laravel [web:269][web:268]

        if ($this->isEditMode && $this->editingId) {
            $client = Client::find($this->editingId);
            if ($client) {
                $client->fill($validated)->save(); // update Eloquent [web:202]
            }
        } else {
            Client::create($validated); // create Eloquent [web:202]
        }

        $this->showModal = false;
        $this->resetPage();
    }

    #[Layout('layouts.app')]
    public function render()
    {
        $term = $this->search ? "%{$this->search}%" : null;

        $query = Client::query()
            ->when($term, function ($q) use ($term) {
                $q->where(function ($w) use ($term) {
                    $w->where('nom', 'like', $term)
                      ->orWhere('ville', 'like', $term)
                      ->orWhere('code_postal', 'like', $term)
                      ->orWhere('telephone', 'like', $term)
                      ->orWhere('email', 'like', $term);
                });
            });

        if (in_array($this->sortBy, $this->sortable, true)) {
            $query->orderBy($this->sortBy, $this->sortDirection);
        } else {
            $query->orderBy('nom', 'asc');
        }

        $clients = $query->paginate(15); // pagination Livewire [web:93][web:316]

        return view('livewire.clients-table', [
            'clients' => $clients,
            'sortBy' => $this->sortBy,
            'sortDirection' => $this->sortDirection,
        ]);
    }
}
