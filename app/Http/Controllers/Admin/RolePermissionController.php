<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\RolePermissionService;
use Illuminate\Http\Request;
use Validator;

class RolePermissionController extends Controller
{
    public function __construct(private RolePermissionService $rolePermissionService)
    {
        $this->rolePermissionService = $rolePermissionService;
    }

    public function roleList()
    {
        $result = $this->rolePermissionService->getRoleList();

        return view('admin.role.list', compact('result'));
    }

    public function roleCreate()
    {
        $permissions = $this->rolePermissionService->getPermissionList();

        return view('admin.role.create', compact('permissions'));
    }

    public function storeRole(Request $request)
    {
        // dd($request->all());
        $validation = Validator::make($request->all(), [
            'name' => 'required|unique:roles,name',
            'permission_id' => 'required'
        ]);
        if ($validation->fails()) {
            return redirect()->back()->withErrors($validation)->withInput();
        }
        $this->rolePermissionService->storeRole($request->all());
        return redirect()->route('admin.role.list')->with('success', 'Role created successfully');
    }

    public function editRole($id)
    {
        $role = $this->rolePermissionService->getRoleById($id);
        $permissions = $this->rolePermissionService->getPermissionList();
        return view('admin.role.edit', compact('role', 'permissions'));
    }

    public function updateRole(Request $request, $id)
    {
        $this->rolePermissionService->updateRole($id, $request->all());
        return redirect()->route('admin.role.list')->with('success', 'Role updated successfully');
    }

    public function permissionList()
    {
        $result = $this->rolePermissionService->getPermissionList();
        return view('admin.permission.list', compact('result'));
    }

    public function permissionCreate()
    {
        return view('admin.permission.create');
    }

    public function storePermission(Request $request)
    {

        $validation = Validator::make($request->all(), [
            'name' => 'required|unique:permissions,name'
        ]);

        if ($validation->fails()) {
            return redirect()->back()->withErrors($validation)->withInput();
        }

        $this->rolePermissionService->storePermission($request->name);
        return redirect()->route('admin.permission.list')->with('success', 'Permission created successfully');
    }

    public function assignPemissionToRole()
    {

    }
}
