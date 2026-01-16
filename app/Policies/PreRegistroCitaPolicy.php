<?php

namespace App\Policies;

use App\Models\PreRegistroCita;
use App\Models\User;

class PreRegistroCitaPolicy
{
    /**
     * Ver listado de pre-registros
     */
    public function view(?User $user): bool
    {
        return $user !== null;
    }

    /**
     * Ver detalles de un pre-registro
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Crear pre-registro (aunque sea anónimo, si hay usuario puede hacerlo)
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Actualizar pre-registro
     */
    public function update(User $user, PreRegistroCita $preRegistro): bool
    {
        return $user->can('gestionar_citas');
    }

    /**
     * Eliminar pre-registro
     */
    public function delete(User $user, PreRegistroCita $preRegistro): bool
    {
        return $user->can('gestionar_citas');
    }

    /**
     * Restaurar pre-registro eliminado
     */
    public function restore(User $user, PreRegistroCita $preRegistro): bool
    {
        return $user->can('gestionar_citas');
    }

    /**
     * Eliminar permanentemente pre-registro
     */
    public function forceDelete(User $user, PreRegistroCita $preRegistro): bool
    {
        return $user->can('gestionar_citas');
    }
}
