<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
     protected $fillable = [
        'code',
        'reference',
        'designation',
        'classe',
        'stock',
        'poids_ma_kg',
        'updated_at',
        'emplacement',
    ];

    protected function casts(): array
{
    return [
        'stock' => 'integer',
        'poids_ma_kg' => 'decimal:3',
    ];
}

}