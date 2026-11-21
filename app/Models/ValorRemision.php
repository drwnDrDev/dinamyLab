<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ValorRemision extends Model
{
    protected $table = 'valores_remision';

    protected $fillable = ['remision_id', 'ips_externa_id', 'precio', 'valor_remitido'];

    protected function casts(): array
    {
        return ['precio' => 'decimal:2', 'valor_remitido' => 'decimal:2'];
    }

    public function remision(): BelongsTo
    {
        return $this->belongsTo(Remision::class);
    }

    public function ipsExterna(): BelongsTo
    {
        return $this->belongsTo(IpsExterna::class);
    }
}
