<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CodigoDiagnostico extends Model
{
    protected $guarded = ['nivel'];



        public function incrementNivel()
        {
            if ($this->nivel < 255) {
                $this->nivel += 1;
                $this->save();
            }
            return $this->nivel;
        }

}
