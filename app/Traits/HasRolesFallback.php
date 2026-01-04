<?php

namespace App\Traits;

/**
 * Fallback trait when Spatie Permission is not installed
 * Remove this and use Spatie\Permission\Traits\HasRoles when package is available
 */
trait HasRolesFallback
{
    public function hasRole($roles, $guard = null): bool
    {
        return true; // Allow all for now
    }

    public function hasPermissionTo($permission, $guardName = null): bool
    {
        return true;
    }

    public function hasAnyRole($roles, $guard = null): bool
    {
        return true;
    }

    public function hasAllRoles($roles, $guard = null): bool
    {
        return true;
    }

    public function assignRole(...$roles)
    {
        return $this;
    }

    public function removeRole($role)
    {
        return $this;
    }

    public function syncRoles(...$roles)
    {
        return $this;
    }

    public function roles()
    {
        return $this->belongsToMany(\App\Models\Role::class ?? self::class);
    }

    public function getRoleNames()
    {
        return collect([]);
    }
}
