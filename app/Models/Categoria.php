<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Categoria extends Model
{
    protected $fillable = [
        'nombre',
        'descripcion',
    ];

    protected $table = 'categorias';

    public function examenes(): BelongsToMany
    {
        return $this->belongsToMany(Examen::class, 'categoria_examen', 'categoria_id', 'examen_id')
            ->withTimestamps();
    }
}
