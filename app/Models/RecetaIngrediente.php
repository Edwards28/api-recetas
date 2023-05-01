<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecetaIngrediente extends Model
{
    use HasFactory;

    protected $table = 'recetas_ingredientes ';

    protected $fillable = [
        'receta_id ',
        'ingrediente_id ',
        'cantidad',
    ];

    public $timestamps = false;
}
