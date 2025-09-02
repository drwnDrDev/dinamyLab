<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CausaExterna extends Model
{
    protected $table = 'causas_externas';
    protected $primaryKey = 'codigo';
    public $incrementing = false; // Since 'codigo' is not an auto-incrementing integer
    protected $keyType = 'string'; // Since 'codigo' is a string
    public $timestamps = false; // If you don't have created_at and updated_at columns

    protected $fillable = [
        'codigo',
        'descripcion',
        'nivel',
        'activo',
    ];
}
