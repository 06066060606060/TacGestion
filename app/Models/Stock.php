<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
     protected $fillable = [
        'code',
        'reference',
        'designation',
        'stock',
        'poids_ma_kg',
        'updated_at',
        'emplacement',
    ];
}