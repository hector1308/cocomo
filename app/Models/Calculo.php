<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Calculo extends Model
{
    use HasFactory;

    protected $fillable = [
        'kloc',
        'tipo',
        'salario',
        'eaf',
        'esfuerzo',
        'duracion',
        'personas',
        'costo_total',
        'factores'
    ];

    protected $casts = [
        'factores' => 'array',
    ];
}

