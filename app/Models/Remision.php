<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Remision extends Model
{
    protected $table = 'remisiones';

    protected $fillable = ['cup', 'nombre'];

    public function valores(): HasMany
    {
        return $this->hasMany(ValorRemision::class);
    }
}
