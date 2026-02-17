<?php

namespace App\Policies\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

trait ChecksEmpresaAccess
{
    /**
     * Verifica si el usuario pertenece a la misma empresa que el recurso.
     *
     * @param User $user
     * @param Model $resource Modelo que tiene una relación empresa_id
     * @return bool
     */
    protected function belongsToSameEmpresa(User $user, Model $resource): bool
    {
        // Verificar que el usuario tenga un empleado asociado
        if (!$user->empleado) {
            return false;
        }

        // Verificar que el empleado tenga una empresa asignada
        if (!$user->empleado->empresa_id) {
            return false;
        }

        // Verificar que el recurso tenga empresa_id
        if (!isset($resource->empresa_id)) {
            return false;
        }

        // Comparar las empresas
        return $user->empleado->empresa_id === $resource->empresa_id;
    }

    /**
     * Obtiene el ID de la empresa del usuario autenticado.
     *
     * @param User $user
     * @return int|null
     */
    protected function getUserEmpresaId(User $user): ?int
    {
        return $user->empleado?->empresa_id;
    }

    /**
     * Verifica si el recurso pertenece a la empresa del usuario.
     * Alternativa cuando el recurso tiene una relación empresa()
     *
     * @param User $user
     * @param Model $resource
     * @return bool
     */
    protected function canAccessEmpresaResource(User $user, Model $resource): bool
    {
        $userEmpresaId = $this->getUserEmpresaId($user);

        if (!$userEmpresaId) {
            return false;
        }

        // Si el recurso tiene empresa_id directamente
        if (isset($resource->empresa_id)) {
            return $resource->empresa_id === $userEmpresaId;
        }

        // Si el recurso tiene una relación empresa cargada
        if ($resource->relationLoaded('empresa') && $resource->empresa) {
            return $resource->empresa->id === $userEmpresaId;
        }

        return false;
    }
}
