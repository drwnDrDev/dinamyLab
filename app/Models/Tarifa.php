<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tarifa extends Model
{
    protected $fillable = [
        'precio',
        'tarifable_id',
        'tarifable_type',
        'sede_id'
    ];

    public function tarifable()
    {
        return $this->morphTo();
    }

    public function sede()
    {
        return $this->belongsTo(Sede::class);
    }
}
