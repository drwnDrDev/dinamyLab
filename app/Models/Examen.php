<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Examen extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'cup',
        'valor',
        'descripcion',
        'nombre_alternativo',
        'sexo_aplicable',
        'activo',
        'nivel'
    ];


    public function ordenes()
    {
        return $this->belongsToMany(Orden::class, 'orden_examen','examen_id', 'orden_medica_id')
            ->withPivot('cantidad')
            ->withTimestamps();
    }

    public function procedimientos(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Procedimiento::class);
    }

    public function parametros()
    {
        return $this->hasMany(Parametro::class);
    }

    public static function booted()
    {
        static::created(function ($examen) {
            if ($examen->cup) {
                \DB::table('codigo_cups')->where('codigo', $examen->cup)->update(['activo' => true]);
            }
        });
    }

    public function cup()
    {
        return $this->belongsTo(CodigoCup::class, 'cup', 'codigo');
    }

    public function tarifas()
    {
        return $this->morphMany(Tarifa::class, 'tarifable');
    }

    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'categoria_id');
    }


    protected $table = 'examenes';
    public function casts()
    {
        return [
            'valor' => 'decimal:2',
        ];
    }


}
