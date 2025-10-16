<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'adresse',
        'code_postal',
        'ville',
        'lieu_de_tir',
        'telephone',
        'email',
    ];
}
