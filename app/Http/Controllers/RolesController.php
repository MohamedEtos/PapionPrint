<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesController extends Controller
{
    public function index()
    {
        $roles = Role::with('permissions')->get();
        $permissions = Permission::all();
        return view('roles.index', compact('roles', 'permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name',
            'permissions' => 'array'
        ]);

        $role = Role::create(['name' => $request->name]);
        
        if($request->has('permissions')){
            $role->syncPermissions($request->permissions);
        }

        return response()->json(['success' => 'Role created successfully', 'role' => $role->load('permissions')]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|unique:roles,name,'.$id,
            'permissions' => 'array'
        ]);

        $role = Role::findById($id);
        $role->update(['name' => $request->name]);

        if($request->has('permissions')){
            $role->syncPermissions($request->permissions);
        }

        return response()->json(['success' => 'Role updated successfully', 'role' => $role->load('permissions')]);
    }

    public function destroy($id)
    {
        $role = Role::findById($id);
        $role->delete();

        return response()->json(['success' => 'Role deleted successfully']);
    }
}
