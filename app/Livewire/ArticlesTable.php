<?php

namespace App\Livewire;

use App\Models\Article;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;

class ArticlesTable extends Component
{
    use WithPagination;
    use WithFileUploads;

    // Onglet actif synchronisé URL (?onglet=...)
    #[Url(as: 'onglet', except: 'FAVORIS')]
    public string $tab = 'FAVORIS';

    public string $search = '';
    public string $sortBy = 'designation';
    public string $sortDirection = 'asc';
    protected array $sortable = [
        'code','reference','code_source','designation','type','famille','calibre','categorie',
        'classe_risque','tarif_piece','tarif_caisse','provenance'
    ];

    // Filtres actifs
    public ?string $filterType = null;
    public ?string $filterFamille = null;
    public ?string $filterCalibre = null;
    public ?string $filterCategorie = null;

    // Listes d’options (préchargées)
    public array $types = [];
    public array $familles = [];
    public array $calibres = [];
    public array $categories = [];

    // Modal / mode
    public bool $showModal = false;
    public bool $isEditMode = false;
    public ?int $editingId = null;

    // Form
    public ?string $code = null;
    public ?string $reference = null;
    public ?string $code_source = null;
    public ?string $designation = null;
    public ?string $type = null;
    public ?string $famille = null;
    public ?string $calibre = null;
    public ?string $duree = null;
    public ?string $categorie = null;
    public ?string $certification = null;
    public $poids_m_a = null;
    public ?string $distance_securite = null;
    public ?string $classe_risque = null;
    public $tarif_piece = null;
    public ?string $cdt = null;
    public $tarif_caisse = null;
    public ?string $rem = null;
    public ?string $video = null;
    public ?string $photo = null;
    public ?string $provenance = null;
    public ?string $note = null;
    public ?int $options = null;
    public bool $optionsChecked = false;

    public $xlsxFile = null;

    // Query string (conservé pour compat, l’onglet est géré via #[Url])
    protected $queryString = [
        'search',
        'sortBy' => ['except' => 'designation'],
        'sortDirection' => ['except' => 'asc'],
        'filterType' => ['except' => null],
        'filterFamille' => ['except' => null],
        'filterCalibre' => ['except' => null],
        'filterCategorie' => ['except' => null],
    ];

    public function mount(): void
    {
        // Précharger les listes pour le premier rendu
        $this->types = Article::query()
            ->select('type')->whereNotNull('type')->where('type','<>','')
            ->distinct()->orderBy('type')->pluck('type')->toArray(); // listes options
        $this->familles = Article::query()
            ->select('famille')->whereNotNull('famille')->where('famille','<>','')
            ->distinct()->orderBy('famille')->pluck('famille')->toArray(); // listes options
        $this->calibres = Article::query()
            ->select('calibre')->whereNotNull('calibre')->where('calibre','<>','')
            ->distinct()->orderBy('calibre')->pluck('calibre')->toArray(); // listes options
        $this->categories = Article::query()
            ->select('categorie')->whereNotNull('categorie')->where('categorie','<>','')
            ->distinct()->orderBy('categorie')->pluck('categorie')->toArray(); // listes options
       // $this->syncOptionsChecked();
    } // [web:34]

    public function updatingSearch(): void { $this->resetPage(); }
    public function updatedFilterType()      { $this->resetPage(); }
    public function updatedFilterFamille()   { $this->resetPage(); }
    public function updatedFilterCalibre()   { $this->resetPage(); }
    public function updatedFilterCategorie() { $this->resetPage(); }
    public function updatedTab()             { $this->resetPage(); }

    public function toggleSort(string $column): void
    {
        if (!in_array($column, $this->sortable, true)) return;
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    } // [web:34]

    // Modal handlers
    public function create(): void
    {
        $this->resetErrorBag();
        $this->resetValidation();

        $this->isEditMode = false;
        $this->editingId = null;

        foreach ([
            'code','reference','code_source','designation','type','famille','calibre','duree',
            'categorie','certification','poids_m_a','distance_securite','classe_risque',
            'tarif_piece','cdt','tarif_caisse','rem','video','photo','provenance',
            'note','options'
        ] as $prop) { $this->$prop = null; }

        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        $this->resetErrorBag();
        $this->resetValidation();

        $a = Article::find($id);
        if (!$a) return;

        $this->isEditMode = true;
        $this->editingId = $a->id;

        $this->code = $a->code;
        $this->reference = $a->reference;
        $this->code_source = $a->code_source;
        $this->designation = $a->designation;
        $this->type = $a->type;
        $this->famille = $a->famille;
        $this->calibre = $a->calibre;
        $this->duree = $a->duree;
        $this->categorie = $a->categorie;
        $this->certification = $a->certification;
        $this->poids_m_a = is_null($a->poids_m_a) ? null : (float)$a->poids_m_a;
        $this->distance_securite = $a->distance_securite;
        $this->classe_risque = $a->classe_risque;
        $this->tarif_piece = is_null($a->tarif_piece) ? null : (float)$a->tarif_piece;
        $this->cdt = $a->cdt;
        $this->tarif_caisse = is_null($a->tarif_caisse) ? null : (float)$a->tarif_caisse;
        $this->rem = $a->rem;
        $this->video = $a->video;
        $this->photo = $a->photo;
        $this->provenance = $a->provenance;
        $this->note = $a->note;
        $this->options = $a->options;
        $this->syncOptionsChecked();

        $this->showModal = true;
    }


public function updatedOptionsChecked($checked): void
{
    // Répercuter vers l’int
    $this->options = $checked ? 1 : 0;
}

private function syncOptionsChecked(): void
{
    $this->optionsChecked = ($this->options === 1);
}

    public function hide(): void
    {
        $this->showModal = false;
    }

    // Validation + save
    public function save(): void
    {
        $validated = $this->validate([
            'designation' => ['required','string','max:512'],
            'code' => ['nullable','string','max:255'],
            'reference' => ['nullable','string','max:255'],
            'code_source' => ['nullable','string','max:255'],
            'type' => ['nullable','string','max:255'],
            'famille' => ['nullable','string','max:255'],
            'calibre' => ['nullable','string','max:255'],
            'duree' => ['nullable','string','max:255'],
            'categorie' => ['nullable','string','max:255'],
            'certification' => ['nullable','string','max:255'],
            'poids_m_a' => ['nullable','numeric'],
            'distance_securite' => ['nullable','string','max:255'],
            'classe_risque' => ['nullable','string','max:255'],
            'tarif_piece' => ['nullable','numeric'],
            'cdt' => ['nullable','string','max:255'],
            'tarif_caisse' => ['nullable','numeric'],
            'rem' => ['nullable','string','max:255'],
            'video' => ['nullable','string','max:255'],
            'photo' => ['nullable','string','max:255'],
            'provenance' => ['nullable','string','max:255'],
            'note' => ['nullable','string','max:65535'],
            'options' => ['nullable','integer','min:0','max:255']
        ]);

        if (isset($validated['poids_m_a']))    $validated['poids_m_a']    = number_format((float)$validated['poids_m_a'], 3, '.', '');
        if (isset($validated['tarif_piece']))   $validated['tarif_piece']  = number_format((float)$validated['tarif_piece'], 2, '.', '');
        if (isset($validated['tarif_caisse']))  $validated['tarif_caisse'] = number_format((float)$validated['tarif_caisse'], 2, '.', '');

        if ($this->isEditMode && $this->editingId) {
            $a = Article::find($this->editingId);
            if ($a) $a->fill($validated)->save();
        } else {
            Article::create($validated);
        }

        $this->showModal = false;
        $this->resetPage();
    }

    // Décimaux FR
    protected function normalizeDecimalString($val, int $scale): ?string
    {
        if ($val === '' || $val === null) return null;
        if (is_string($val)) {
            $val = trim($val);
            $val = str_replace(["\xC2\xA0", ' '], '', $val);
            $val = str_replace(',', '.', $val);
        }
        if (!is_numeric($val)) return null;
        return number_format((float)$val, $scale, '.', '');
    }

    public function delete(): void
    {
        if ($this->isEditMode && $this->editingId) {
            $a = \App\Models\Article::find($this->editingId);
            if ($a) {
                $a->delete();
            }
            $this->showModal = false;
            $this->resetPage();
        }
    }

    // Import XLSX
    public function updatedXlsxFile(): void
    {
        $this->validate([
            'xlsxFile' => ['required','file','mimes:xlsx','max:20480'],
        ]);

        $path = $this->xlsxFile->getRealPath();
        if (!$path || !is_readable($path)) {
            $this->addError('xlsxFile', "Fichier illisible.");
            return;
        }

        $reader = new XlsxReader();
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($path);
        $sheet = $spreadsheet->getSheet($spreadsheet->getFirstSheetIndex());

        $rows = $sheet->toArray(null, true, true, true);
        if (empty($rows) || count($rows) < 2) {
            $this->addError('xlsxFile', "XLSX vide ou en-têtes manquants.");
            return;
        }

        // retirer la 1ère ligne (titres)
        $headerRow = array_shift($rows);

        // Mapping Excel -> DB
        $map = [
            'A' => 'code',
            'B' => 'reference',
            'C' => 'code_source',
            'E' => 'designation',
            'F' => 'type',
            'G' => 'famille',
            'H' => 'calibre',
            'I' => 'duree',
            'L' => 'categorie',
            'N' => 'certification',
            'O' => 'poids_m_a',
            'P' => 'distance_securite',
            'R' => 'classe_risque',
            'T' => 'tarif_piece',
            'U' => 'cdt',
            'V' => 'tarif_caisse',
            'W' => 'rem',
            'X' => 'video',
            'Y' => 'photo',
            'Z' => 'provenance',
        ];

        DB::beginTransaction();
        try {
            foreach ($rows as $row) {
                $joined = trim(implode('', array_map(static fn($v) => (string)$v, $row)));
                if ($joined === '') continue;

                $data = [];
                foreach ($map as $col => $field) {
                    $value = $row[$col] ?? null;
                    $val = is_string($value) ? trim($value) : $value;

                    if (in_array($field, ['poids_m_a'], true)) {
                        $data[$field] = $this->normalizeDecimalString($val, 3);
                    } elseif (in_array($field, ['tarif_piece','tarif_caisse'], true)) {
                        $data[$field] = $this->normalizeDecimalString($val, 2);
                    } else {
                        $data[$field] = ($val === '' ? null : (string)$val);
                    }
                }

                if (empty($data['designation'])) continue;

                $query = Article::query();
                if (!empty($data['reference'])) {
                    $query->where('reference', $data['reference']);
                } elseif (!empty($data['code'])) {
                    $query->where('code', $data['code']);
                } else {
                    $query->where('designation', $data['designation']);
                }

                $existing = $query->first();
                if ($existing) {
                    $existing->fill($data)->save();
                } else {
                    Article::create($data);
                }
            }
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->addError('xlsxFile', "Erreur d’import: ".$e->getMessage());
            return;
        }

        $this->xlsxFile = null;
        $this->resetPage();
    }

    // Export XLSX (respecte recherche + filtres + onglet + tri par désignation)
    public function exportXlsx()
    {
        $filename = 'articles_' . now()->format('Ymd_His') . '.xlsx';

        $term = $this->search ? "%{$this->search}%" : null;

        $query = Article::query()
            ->when($term, function ($q) use ($term) {
                $q->where(function ($w) use ($term) {
                    $w->where('code','like',$term)
                      ->orWhere('reference','like',$term)
                      ->orWhere('designation','like',$term)
                      ->orWhere('type','like',$term)
                      ->orWhere('famille','like',$term)
                      ->orWhere('calibre','like',$term);
                });
            })
            ->when($this->filterType, fn($q) => $q->where('type', $this->filterType))
            ->when($this->filterFamille, fn($q) => $q->where('famille', $this->filterFamille))
            ->when($this->filterCalibre, fn($q) => $q->where('calibre', $this->filterCalibre))
            ->when($this->filterCategorie, fn($q) => $q->where('categorie', $this->filterCategorie));

        // Appliquer le filtre d’onglet pour l’export
        $this->applyTabFilter($query);

        $query->orderBy('designation');

        $items = $query->get([
            'code','reference','code_source','designation','type','famille','calibre','duree',
            'categorie','certification','poids_m_a','distance_securite','classe_risque',
            'tarif_piece','cdt','tarif_caisse','rem','video','photo','provenance'
        ]);

        return response()->streamDownload(function () use ($items) {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $headers = [
                'code','reference','code_source','designation','type','famille','calibre','duree',
                'categorie','certification','poids_m_a','distance_securite','classe_risque',
                'tarif_piece','cdt','tarif_caisse','rem','video','photo','provenance'
            ];
            $sheet->fromArray($headers, null, 'A1');

            $row = 2;
            foreach ($items as $it) {
                $sheet->setCellValue("A{$row}", $it->code);
                $sheet->setCellValue("B{$row}", $it->reference);
                $sheet->setCellValue("C{$row}", $it->code_source);
                $sheet->setCellValue("D{$row}", $it->designation);
                $sheet->setCellValue("E{$row}", $it->type);
                $sheet->setCellValue("F{$row}", $it->famille);
                $sheet->setCellValue("G{$row}", $it->calibre);
                $sheet->setCellValue("H{$row}", $it->duree);
                $sheet->setCellValue("I{$row}", $it->categorie);
                $sheet->setCellValue("J{$row}", $it->certification);

                if (is_null($it->poids_m_a)) {
                    $sheet->setCellValue("K{$row}", null);
                } else {
                    $sheet->setCellValueExplicit("K{$row}", (float)$it->poids_m_a, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
                    $sheet->getStyle("K{$row}")->getNumberFormat()->setFormatCode('0.000');
                }

                $sheet->setCellValue("L{$row}", $it->distance_securite);
                $sheet->setCellValue("M{$row}", $it->classe_risque);

                if (is_null($it->tarif_piece)) {
                    $sheet->setCellValue("N{$row}", null);
                } else {
                    $sheet->setCellValueExplicit("N{$row}", (float)$it->tarif_piece, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
                    $sheet->getStyle("N{$row}")->getNumberFormat()->setFormatCode('0.00');
                }

                $sheet->setCellValue("O{$row}", $it->cdt);

                if (is_null($it->tarif_caisse)) {
                    $sheet->setCellValue("P{$row}", null);
                } else {
                    $sheet->setCellValueExplicit("P{$row}", (float)$it->tarif_caisse, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
                    $sheet->getStyle("P{$row}")->getNumberFormat()->setFormatCode('0.00');
                }

                $sheet->setCellValue("Q{$row}", $it->rem);
                $sheet->setCellValue("R{$row}", $it->video);
                $sheet->setCellValue("S{$row}", $it->photo);
                $sheet->setCellValue("T{$row}", $it->provenance);

                $row++;
            }

            $sheet->getStyle('A1:T1')->getFont()->setBold(true);
            foreach (range('A','T') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    } // [web:34]

    // Applique la logique d’onglet sur une query Article::query()
    protected function applyTabFilter($q): void
    {
        // Helper LIKE pour famille (insensible à la casse si collation le permet)
        $famLike = function ($builder, string $needle): void {
            $builder->where('famille', 'LIKE', "%{$needle}%");
        };

        switch ($this->tab) {
            case 'FAVORIS':
                // Favoris: options == 1
                $q->where('options', 1);
                break;

            case 'BOMBES':
                $famLike($q, 'BOMBES');
                break;

            case 'CHANDELLES':
                $famLike($q, 'CHANDELLES');
                break;

            case 'PACK':
                // Couvrir PACK et PACKS
                $q->where(function ($qq) {
                    $qq->where('famille', 'LIKE', '%PACKS%')
                       ->orWhere('famille', 'LIKE', '%PACK%');
                });
                break;

            case 'EVENTAILS':
                $famLike($q, 'EVENTAILS');
                break;

            case 'MONOCOUPS':
                // Couvrir MONO-COUPS et MONOCOUPS
                $q->where(function ($qq) {
                    $qq->where('famille', 'LIKE', '%MONO-COUPS%')
                       ->orWhere('famille', 'LIKE', '%MONOCOUPS%');
                });
                break;

            case 'COLIS':
                $famLike($q, 'COLIS');
                break;

            case 'PAT':
                $famLike($q, 'PAT');
                break;

            case 'FEUX':
                $famLike($q, 'FEUX');
                break;

            case 'TOUS':
                // Ne rien filtrer
                break;

            case 'AUTRE':
                // Exclure tous les autres cas + non favoris
                $q->where(function ($qq) {
                    $qq->whereNull('options')->orWhere('options', '!=', 1);
                })->where(function ($qq) {
                    $qq->where('famille', 'NOT LIKE', '%BOMBES%')
                       ->where('famille', 'NOT LIKE', '%CHANDELLES%')
                       ->where('famille', 'NOT LIKE', '%PACKS%')
                       ->where('famille', 'NOT LIKE', '%PACK%')
                       ->where('famille', 'NOT LIKE', '%EVENTAILS%')
                       ->where('famille', 'NOT LIKE', '%MONO-COUPS%')
                       ->where('famille', 'NOT LIKE', '%MONOCOUPS%')
                       ->where('famille', 'NOT LIKE', '%COLIS%')
                       ->where('famille', 'NOT LIKE', '%PAT%')
                       ->where('famille', 'NOT LIKE', '%FEUX%');
                       
                });
                break;
        }
    } // [web:34][web:10]

    #[Layout('layouts.app')]
    public function render()
    {
        $term = $this->search ? "%{$this->search}%" : null;

        $query = Article::query()
            ->when($term, function ($q) use ($term) {
                $q->where(function ($w) use ($term) {
                    $w->where('code','like',$term)
                      ->orWhere('reference','like',$term)
                      ->orWhere('designation','like',$term)
                      ->orWhere('type','like',$term)
                      ->orWhere('famille','like',$term)
                      ->orWhere('calibre','like',$term);
                });
            })
            ->when($this->filterType, fn($q) => $q->where('type', $this->filterType))
            ->when($this->filterFamille, fn($q) => $q->where('famille', $this->filterFamille))
            ->when($this->filterCalibre, fn($q) => $q->where('calibre', $this->filterCalibre))
            ->when($this->filterCategorie, fn($q) => $q->where('categorie', $this->filterCategorie));

        // Appliquer l’onglet
        $this->applyTabFilter($query);

        // Tri
        if (in_array($this->sortBy, $this->sortable, true)) {
            $query->orderBy($this->sortBy, $this->sortDirection);
        } else {
            $query->orderBy('designation', 'asc');
        }

        $items = $query->paginate(15); // pagination

        return view('livewire.articles-table', [
            'items' => $items,
            'sortBy' => $this->sortBy,
            'sortDirection' => $this->sortDirection,
            // listes déjà prêtes via mount()
            'types' => $this->types,
            'familles' => $this->familles,
            'calibres' => $this->calibres,
            'categories' => $this->categories,
            'tab' => $this->tab,
        ]);
    }
}
