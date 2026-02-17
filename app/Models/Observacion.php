<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Observacion extends Model
{
    protected $fillable = [
        'observable_id',
        'observable_type',
        'observacion',
    ];

    public function observable()
    {
        return $this->morphTo();
    }
    protected $table = 'observaciones';
}
