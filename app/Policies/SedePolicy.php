<?php

namespace App\Policies;

use App\Models\Sede;
use App\Models\User;
use App\Policies\Traits\ChecksEmpresaAccess;

class SedePolicy
{
    use ChecksEmpresaAccess;

    /**
     * Determina si el usuario puede ver el listado de sedes.
     * Solo puede ver las sedes de su empresa.
     */
    public function viewAny(User $user): bool
    {
        // El usuario puede ver sedes si pertenece a una empresa
        return $this->getUserEmpresaId($user) !== null;
    }

    /**
     * Determina si el usuario puede ver una sede específica.
     * Solo puede ver sedes de su propia empresa.
     */
    public function view(User $user, Sede $sede): bool
    {
        // Usar el método del trait para verificar acceso
        return $this->belongsToSameEmpresa($user, $sede);
    }

    /**
     * Determina si el usuario puede crear una sede.
     * Solo puede crear sedes para su propia empresa.
     */
    public function create(User $user): bool
    {
        // Debe pertenecer a una empresa y tener el permiso
        return $this->getUserEmpresaId($user) !== null 
            && ($user->hasRole('admin') || $user->can('crear_sedes'));
    }

    /**
     * Determina si el usuario puede actualizar una sede.
     * Solo puede actualizar sedes de su propia empresa.
     */
    public function update(User $user, Sede $sede): bool
    {
        // Primero verificar que la sede pertenece a su empresa
        if (!$this->belongsToSameEmpresa($user, $sede)) {
            return false;
        }

        // Además debe tener el permiso
        return $user->hasRole('admin') || $user->can('editar_sedes');
    }

    /**
     * Determina si el usuario puede eliminar una sede.
     * Solo puede eliminar sedes de su propia empresa.
     */
    public function delete(User $user, Sede $sede): bool
    {
        // Primero verificar que la sede pertenece a su empresa
        if (!$this->belongsToSameEmpresa($user, $sede)) {
            return false;
        }

        // Además debe tener el permiso específico o ser admin
        return $user->hasRole('admin') || $user->can('eliminar_sedes');
    }
}
