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
        'desripcion',
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

    public function procedimiento()
    {
        return $this->belongsTo(Procedimiento::class);
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

    protected $table = 'examenes';
    public function casts()
    {
        return [
            'valor' => 'decimal:2',
        ];
    }


}
