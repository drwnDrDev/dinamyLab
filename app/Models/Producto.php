<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nombre',
        'descripcion',
        'categoria',
    ];


    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'productos';
}
