
<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Sede;
use App\Models\Empleado;
use Illuminate\Auth\Access\HandlesAuthorization;

class SedeEmpledaoPolice
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {

    }
    public function tieneRelacion( $sede, $empleado)
    {
        // Asumiendo que Sede tiene una relación empleados() definida en el modelo
        return $sede->empleados()->where('id', $empleado->id)->exists();
    }
}
