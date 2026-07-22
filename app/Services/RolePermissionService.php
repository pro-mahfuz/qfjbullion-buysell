<?php

namespace App\Services;
use App\Exceptions\RedirectException;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;


class RolePermissionService
{
    public static function getAllRoles()
    {
        $isSuperAdmin = auth()->user()->hasRole('Super Admin');

        $roles = Role::query();

        if (!$isSuperAdmin) {
            $roles = $roles
            ->where('name', '!=', 'Super Admin')
            ->where('business_id', session()->get('bussinessId'))
                ->get();
        } else {
            $roles = $roles->get();
        }

        return $roles;

    }

    public function getPermissionList()
    {

        // return Permission::orderBy('id', 'desc')->get();

        $permissionOfCurrentRole = null;
        $permissions = [];
        $currentRole = auth()->user()->roles->first();
        if ($currentRole) {
            $permissionOfCurrentRole = $currentRole->permissions->pluck('name')->toArray();
        }

        $isSuperAdmin = auth()->user()->hasRole('Super Admin');
        if ($isSuperAdmin) {
            return Permission::orderBy('id', 'desc')->get();
        }
        if (!$permissionOfCurrentRole) {
            return [];
        }

        return Permission::whereIn('name', $permissionOfCurrentRole)->orderBy('id', 'desc')->get();
    }

    public function getRoleList()
    {
        $isSuperAdmin = auth()->user()->hasRole('Super Admin');
        if ($isSuperAdmin) {
            return Role::orderBy('id', 'desc')->get();
        }
        return Role::where('business_id', session()->get('bussinessId'))->orderBy('id', 'desc')->get();
    }


    public function storeRole($data): void
    {
        try {

            $role = Role::create(['name' => $data['name'], 'business_id' => session()->get('bussinessId')]);
            foreach ($data['permission_id'] as $permission) {
                $permission = Permission::find($permission);
                $role->givePermissionTo($permission->name);
            }
        } catch (\Exception $e) {
            throw new RedirectException('Unable to create role');
        }
    }

    public function storePermission($name): void
    {
        try{
            Permission::create(['name' => $name]);
        } catch (\Exception $e) {
            throw new RedirectException('Unable to create permission');
        }
    }

    public function updateRole($id, $data)
    {
        try {
            $role = Role::find($id);
            $role->update(['name' => $data['name']]);
            $role->permissions()->detach();
            foreach ($data['permission_id'] as $permission) {
                $permission = Permission::find($permission);
                $role->givePermissionTo($permission->name);
            }
        } catch (\Exception $e) {
            throw new RedirectException($e->getMessage());
        }
    }

    public function getRoleById($id)
    {
        return Role::with('permissions')->find($id);
    }
}
