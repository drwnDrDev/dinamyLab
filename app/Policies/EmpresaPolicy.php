<?php

namespace App\Policies;

use App\Models\Empresa;
use App\Models\User;
use App\Policies\Traits\ChecksEmpresaAccess;

class EmpresaPolicy
{
    use ChecksEmpresaAccess;

    /**
     * Determina si el usuario puede ver el listado de empresas.
     * Solo puede ver su propia empresa.
     */
    public function viewAny(User $user): bool
    {
        // El usuario puede ver empresas si tiene un empleado asociado
        return $user->empleado !== null;
    }

    /**
     * Determina si el usuario puede ver una empresa específica.
     * Solo puede ver su propia empresa.
     */
    public function view(User $user, Empresa $empresa): bool
    {
        // Verificar que el usuario pertenezca a la empresa
        $userEmpresaId = $this->getUserEmpresaId($user);
        
        return $userEmpresaId && $userEmpresaId === $empresa->id;
    }

    /**
     * Determina si el usuario puede crear una empresa.
     * Generalmente solo administradores del sistema.
     */
    public function create(User $user): bool
    {
        // Solo usuarios con rol admin pueden crear empresas
        return $user->hasRole('admin');
    }

    /**
     * Determina si el usuario puede actualizar una empresa.
     * Solo puede actualizar su propia empresa y debe tener permisos.
     */
    public function update(User $user, Empresa $empresa): bool
    {
        // Debe pertenecer a la empresa
        if (!$this->belongsToUserEmpresa($user, $empresa)) {
            return false;
        }

        // Además, debe tener el permiso específico o ser admin
        return $user->hasRole('admin') || $user->can('editar_empresa');
    }

    /**
     * Determina si el usuario puede eliminar una empresa.
     * Generalmente solo administradores del sistema.
     */
    public function delete(User $user, Empresa $empresa): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Verificar si una empresa pertenece al usuario.
     */
    protected function belongsToUserEmpresa(User $user, Empresa $empresa): bool
    {
        $userEmpresaId = $this->getUserEmpresaId($user);
        return $userEmpresaId && $userEmpresaId === $empresa->id;
    }
}
