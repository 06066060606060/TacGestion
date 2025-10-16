<?php

namespace App\Livewire;

use App\Models\Stock;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;

class StocksTable extends Component
{
    use WithPagination;
    use WithFileUploads;

    // Recherche
    public string $search = '';

    // Modal + mode
    public bool $showCreateModal = false;
    public ?int $editingId = null;
    public bool $isEditMode = false;

    // Form
    public $code = null;
    public ?string $reference = null;
    public ?string $designation = null;
    public ?string $classe = null; // 1.3G / 1.4G / 1.4S
    public $stock = 0;
    public $poids_ma_kg = null;
    public ?string $emplacement = null;

    // Import XLSX
    public $xlsxFile = null;

    // Tri
    public string $sortBy = 'designation';
    public string $sortDirection = 'asc';
    protected array $sortable = ['code','reference','designation','classe','stock','poids_total','emplacement'];

    // Lier à l’URL
    protected $queryString = [
        'search',
        'sortBy' => ['except' => 'designation'],
        'sortDirection' => ['except' => 'asc'],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    // Ouvrir modal (création)
    public function showCreate(): void
    {
        $this->resetErrorBag();
        $this->resetValidation();

        $this->editingId = null;
        $this->isEditMode = false;

        $this->code = null;
        $this->reference = null;
        $this->designation = null;
        $this->classe = null;
        $this->stock = 0;
        $this->poids_ma_kg = null;
        $this->emplacement = null;

        $this->showCreateModal = true;
    }

    // Ouvrir modal (édition)
    public function edit(int $id): void
    {
        $this->resetErrorBag();
        $this->resetValidation();

        $item = Stock::find($id);
        if (!$item) {
            return;
        }

        $this->editingId   = $item->id;
        $this->isEditMode  = true;

        $this->code        = $item->code;
        $this->reference   = $item->reference;
        $this->designation = $item->designation;
        $this->classe      = $item->classe;
        $this->stock       = (int)$item->stock;
        $this->poids_ma_kg = is_null($item->poids_ma_kg) ? null : (float)$item->poids_ma_kg;
        $this->emplacement = $item->emplacement;

        $this->showCreateModal = true;
    }

    public function hideCreate(): void
    {
        $this->showCreateModal = false;
    }

    // Enregistrer (create/update)
    public function save(): void
    {
        $validated = $this->validate([
            'code' => ['nullable','integer'],
            'reference' => ['nullable','string','max:512'],
            'designation' => ['required','string','max:512'],
            'classe' => ['nullable','in:1.3G,1.4G,1.4S'], // restriction aux 3 valeurs
            'stock' => ['required','integer','min:0'],
            'poids_ma_kg' => ['nullable','numeric'],
            'emplacement' => ['nullable','string','max:255'],
        ]); // règles de validation [web:269]

        if (isset($validated['poids_ma_kg'])) {
            $validated['poids_ma_kg'] = number_format((float)$validated['poids_ma_kg'], 3, '.', '');
        }

        if ($this->isEditMode && $this->editingId) {
            $item = Stock::find($this->editingId);
            if ($item) {
                $item->fill($validated)->save(); // update [web:202]
            }
        } else {
            Stock::create($validated); // create [web:202]
        }

        $this->showCreateModal = false;
        $this->resetPage();
    }

    // + / -
    public function increment($id): void
    {
        $item = Stock::find($id);
        if ($item) {
            $item->increment('stock', 1);
        }
    } // Eloquent [web:202]

    public function decrement($id): void
    {
        $item = Stock::find($id);
        if ($item && $item->stock > 0) {
            $item->decrement('stock', 1);
        }
    } // Eloquent [web:202]

    // Normalisation décimale FR -> "x.xxx"
    protected function normalizeExcelDecimalString($val): ?string
    {
        if ($val === '' || $val === null) {
            return null;
        }
        if (is_string($val)) {
            $val = trim($val);
            $val = str_replace(["\xC2\xA0", ' '], '', $val);
            $val = str_replace(',', '.', $val);
        }
        if (!is_numeric($val)) {
            return null;
        }
        return number_format((float)$val, 3, '.', '');
    }

    // Normaliser entêtes
    protected function normalizeHeader(string $label): string
    {
        $key = strtolower(trim($label));
        $key = preg_replace('/[\p{P}]+/u', '', $key);
        $key = preg_replace('/\s+/u', '_', $key);
        return $key;
    }

    // Tri (en-têtes cliquables)
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

    // Import XLSX
    public function updatedXlsxFile(): void
    {
        $this->validate([
            'xlsxFile' => ['required','file','mimes:xlsx','max:20480'],
        ]); // validation import [web:269]

        $path = $this->xlsxFile->getRealPath();
        if (!$path || !is_readable($path)) {
            $this->addError('xlsxFile', "Fichier illisible.");
            return;
        }

        $reader = new XlsxReader();
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($path);
        $sheet = $spreadsheet->getSheet($spreadsheet->getFirstSheetIndex());

        $rows = $sheet->toArray(null, true, true, true); // lecture [web:74]

        if (empty($rows) || count($rows) < 2) {
            $this->addError('xlsxFile', "XLSX vide ou en-têtes manquants.");
            return;
        }

        $headerRow = array_shift($rows);

        $headers = [];
        foreach ($headerRow as $colKey => $label) {
            $norm = $this->normalizeHeader((string)$label);
            if (in_array($norm, ['poids', 'poids_kg', 'poids_ma', 'poids_ma_kg'], true)) {
                $norm = 'poids_ma_kg';
            }
            $headers[$colKey] = $norm;
        }

        if (!in_array('designation', $headers, true) || !in_array('stock', $headers, true)) {
            $this->addError('xlsxFile', "En-têtes requis manquants: designation, stock.");
            return;
        }

        DB::beginTransaction();
        try {
            foreach ($rows as $row) {
                $joined = trim(implode('', array_map(static fn($v) => (string)$v, $row)));
                if ($joined === '') {
                    continue;
                }

                $data = [
                    'code' => null,
                    'reference' => null,
                    'designation' => null,
                    'classe' => null,
                    'stock' => 0,
                    'poids_ma_kg' => null,
                    'emplacement' => null,
                ];

                foreach ($row as $colKey => $value) {
                    $key = $headers[$colKey] ?? null;
                    if ($key === null) { continue; }

                    $val = is_string($value) ? trim($value) : $value;

                    if (is_string($val) && in_array($key, ['stock','poids_ma_kg'], true)) {
                        $val = str_replace(["\xC2\xA0", ' '], '', $val);
                    }

                    switch ($key) {
                        case 'code':
                            $data['code'] = ($val === '' ? null : (int)$val);
                            break;
                        case 'reference':
                            $data['reference'] = ($val === '' ? null : (string)$val);
                            break;
                        case 'designation':
                            $data['designation'] = ($val === '' ? null : (string)$val);
                            break;
                        case 'classe':
                            $data['classe'] = ($val === '' ? null : (string)$val);
                            break;
                        case 'stock':
                            $dec = $this->normalizeExcelDecimalString($val);
                            $data['stock'] = $dec === null ? 0 : (int)round((float)$dec);
                            break;
                        case 'poids_ma_kg':
                            $data['poids_ma_kg'] = $this->normalizeExcelDecimalString($val);
                            break;
                        case 'emplacement':
                            $data['emplacement'] = ($val === '' ? null : (string)$val);
                            break;
                    }
                }

                if (empty($data['designation'])) {
                    continue;
                }

                $query = Stock::query();
                if (!empty($data['reference'])) {
                    $query->where('reference', $data['reference']);
                } else {
                    $query->where('designation', $data['designation']);
                }
                $existing = $query->first();

                if ($existing) {
                    $existing->fill([
                        'code' => $data['code'],
                        'stock' => $data['stock'],
                        'poids_ma_kg' => $data['poids_ma_kg'],
                        'emplacement' => $data['emplacement'],
                        'designation' => $data['designation'],
                        'classe' => $data['classe'],
                        'reference' => $data['reference'],
                    ])->save();
                } else {
                    Stock::create($data);
                }
            }
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->addError('xlsxFile', "Erreur d’import: " . $e->getMessage());
            return;
        }

        $this->xlsxFile = null;
        $this->resetPage();
    }

    // Export XLSX
    public function exportXlsx()
    {
        $filename = 'stocks_' . now()->format('Ymd_His') . '.xlsx';

        $term = $this->search ? "%{$this->search}%" : null;
        $items = Stock::query()
            ->when($term, function ($q) use ($term) {
                $q->where(function ($w) use ($term) {
                    $w->where('code', 'like', $term)
                      ->orWhere('reference', 'like', $term)
                      ->orWhere('designation', 'like', $term)
                      ->orWhere('classe', 'like', $term);
                });
            })
            ->orderBy('designation')
            ->get(['code','reference','designation','classe','stock','poids_ma_kg','emplacement']); // [web:74]

        return response()->streamDownload(function () use ($items) {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $headers = ['code','reference','designation','classe','stock','poids_ma_kg','emplacement'];
            $sheet->fromArray($headers, null, 'A1');

            $row = 2;
            foreach ($items as $it) {
                $sheet->setCellValue("A{$row}", $it->code);
                $sheet->setCellValue("B{$row}", $it->reference);
                $sheet->setCellValue("C{$row}", $it->designation);
                $sheet->setCellValue("D{$row}", $it->classe);
                $sheet->setCellValueExplicit("E{$row}", (int)$it->stock, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
                if (is_null($it->poids_ma_kg)) {
                    $sheet->setCellValue("F{$row}", null);
                } else {
                    $sheet->setCellValueExplicit("F{$row}", (float)$it->poids_ma_kg, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
                    $sheet->getStyle("F{$row}")->getNumberFormat()->setFormatCode('0.000');
                }
                $sheet->setCellValue("G{$row}", $it->emplacement);
                $row++;
            }

            $sheet->getStyle('A1:G1')->getFont()->setBold(true);
            foreach (range('A','G') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output'); // [web:74]
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    #[Layout('layouts.app')]
    public function render()
    {
        $term = $this->search ? "%{$this->search}%" : null;

        $query = Stock::query()
            ->when($term, function ($q) use ($term) {
                $q->where(function ($w) use ($term) {
                    $w->where('code', 'like', $term)
                      ->orWhere('reference', 'like', $term)
                      ->orWhere('designation', 'like', $term)
                      ->orWhere('classe', 'like', $term);
                });
            });

        if ($this->sortBy === 'poids_total') {
            $query->orderByRaw('COALESCE(poids_ma_kg,0) * COALESCE(stock,0) ' . ($this->sortDirection === 'asc' ? 'asc' : 'desc'));
        } else {
            if (in_array($this->sortBy, ['code','reference','designation','classe','stock','emplacement'], true)) {
                $query->orderBy($this->sortBy, $this->sortDirection);
            } else {
                $query->orderBy('designation', 'asc');
            }
        }

        $items = $query->paginate(15);

        $totalPoidsActif = Stock::query()
            ->selectRaw('COALESCE(SUM(COALESCE(poids_ma_kg,0) * COALESCE(stock,0)),0) as total')
            ->value('total'); // agrégat SQL [web:74]

        return view('livewire.stocks-table', [
            'items' => $items,
            'totalPoidsActif' => $totalPoidsActif,
            'sortBy' => $this->sortBy,
            'sortDirection' => $this->sortDirection,
        ]);
    }
}
