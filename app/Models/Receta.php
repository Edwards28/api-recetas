<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Receta extends Model
{
    use HasFactory;

    protected $table = 'recetas';

    protected $fillable = [
        'nombre',
        'descripcion',
        'categoria_id',
        'tiempo_preparacion',
        'porciones',
        'imagen',
        'usuario_id',
    ];

    public $timestamps = false;
}
