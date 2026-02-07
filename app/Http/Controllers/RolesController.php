<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class RolesController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:super-admin');
    }
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

        $role = DB::transaction(function () use ($request) {
            $role = Role::create(['name' => $request->name]);
            
            if($request->has('permissions')){
                $role->syncPermissions($request->permissions);
            }
            return $role;
        });

        return response()->json(['success' => 'Role created successfully', 'role' => $role->load('permissions')]);
    }

    public function update(Request $request, $id)
    {
        \Illuminate\Support\Facades\Validator::make(['id' => $id], [
            'id' => 'required|exists:roles,id',
        ])->validate();
        $request->validate([
            'name' => 'required|unique:roles,name,'.$id,
            'permissions' => 'array'
        ]);

        $role = Role::findById($id);
        DB::transaction(function () use ($role, $request) {
            $role->update(['name' => $request->name]);

            if($request->has('permissions')){
                $role->syncPermissions($request->permissions);
            }
        });

        return response()->json(['success' => 'Role updated successfully', 'role' => $role->load('permissions')]);
    }

    public function destroy($id)
    {
        \Illuminate\Support\Facades\Validator::make(['id' => $id], [
            'id' => 'required|exists:roles,id',
        ])->validate();
        $role = Role::findById($id);
        $role->delete();

        return response()->json(['success' => 'Role deleted successfully']);
    }
}
