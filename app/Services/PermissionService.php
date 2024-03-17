<?php

namespace App\Services;

use Spatie\Permission\Models\Permission;

class PermissionService
{
    public function savePermission(array $permissionData): bool
    {
        $permission = Permission::create(['name' => $permissionData['permission']]);
        if(collect($permissionData)->has('role'))
        {
            $permission->syncRoles($permissionData['role']);
        }
        return true;
    }

    public function updatePermission(array $permissionData): bool
    {
        $permission = Permission::findById($permissionData['permission_id']);
        $permission->name = $permissionData['permission'];
        if(collect($permissionData)->has('role'))
        {
            $permission->save();
            $permission->syncRoles($permissionData['role']);
        }else{
            foreach ($permission->getRoleNames() as $role)
            {
                $permission->removeRole($role);
            }
        }
        return true;
    }
}
