<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\IncrementaNivel;

class CodigoDiagnostico extends Model
{
    use IncrementaNivel;
    protected $guarded = ['nivel'];

    protected static function booted()
    {
        self::resetearNiveles(100);
    }

}
