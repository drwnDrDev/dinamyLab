<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Services\ElegirEmpresa;

class Factura extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $table = 'facturas';


    protected $guarded = ['epmpleado_id', 'sede_id','numero'];

    public function sede()
    {
        return $this->belongsTo(Sede::class);
    }
    public function convenio()
    {
        return $this->belongsTo(Convenio::class);
    }
    public function empleado()
    {
        return $this->belongsTo(Empleado::class);
    }
    public function resolucion()
    {
        return $this->belongsTo(Resolucion::class);
    }
    public function mediosPago()
    {
        return $this->hasMany(FacturaMedioPago::class);
    }
    public function pagador()
    {
        return $this->morphTo();
    }

    protected static function boot()
    {
        parent::boot();


        static::creating(function ($model) {
            $sede = ElegirEmpresa::elegirSede();
            $model->sede_id = $sede->id;
            $model->empleado_id = auth()->user()->empleado->id;

            if(!$model->resolucion_id) {
                $model->resolucion_id = $sede->resolucion_id;
            }


            // Asignar el número de factura automáticamente si no se ha proporcionado
            if (!$model->numero) {
                $model->numero = Factura::max('numero') + 1;
            }
        });
    }


    protected function casts(): array
    {
        return [
            'fecha_emision' => 'datetime',
            'fecha_vencimiento' => 'date',

            'total' => 'decimal:2',
            'subtotal' => 'decimal:2',
            'total_ajustes' => 'decimal:2'
        ];
    }

}
