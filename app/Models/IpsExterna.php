<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class IpsExterna extends Model
{
    protected $table = 'ips_externas';

    protected $fillable = ['nombre', 'nit', 'telefono'];

    public function valoresRemision(): HasMany
    {
        return $this->hasMany(ValorRemision::class);
    }
}
