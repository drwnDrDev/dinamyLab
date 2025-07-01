<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nombre',
        'descripcion',
        'empresa_id'
    ];
    /**
     * The table associated with the model.
     *
     * @var string
     */
    public function examenes()
    {
        return $this->hasMany(Examen::class);
    }
    protected $table = 'categoria_examen';
}
