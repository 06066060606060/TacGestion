<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'code','reference','code_source','designation','type','famille','calibre','duree',
        'categorie','certification','poids_m_a','distance_securite','classe_risque',
        'tarif_piece','cdt','tarif_caisse','rem','video','photo','provenance', 'note','options'
    ];

    protected function casts(): array
    {
        return [
            'poids_m_a' => 'decimal:3',
            'tarif_piece' => 'decimal:2',
            'tarif_caisse' => 'decimal:2',
        ];
    }
}
