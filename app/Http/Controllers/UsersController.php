<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{
    public function index()
    {
        $users = User::with('roles')->get();
        $roles = Role::all();
        return view('users.index', compact('users', 'roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'roles' => 'array'
        ]);

        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        if($request->has('roles')){
            $user->syncRoles($request->roles);
        }

        return response()->json(['success' => 'User created successfully', 'user' => $user->load('roles')]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,'.$id,
            'email' => 'required|string|email|max:255|unique:users,email,'.$id,
            'roles' => 'nullable|array'
        ]);

        $user = User::findOrFail($id);
        $user->update([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
        ]);
        
        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        // Always sync roles if the request contains 'update_roles' flag, 
        // or if 'roles' is present. If 'roles' is missing but we are updating, 
        // it might mean unchecking all roles (if we send an empty array from frontend).
        // Best practice: Frontend should send 'roles' as empty array if none selected.
        
        if ($request->has('roles') || $request->has('update_roles')) {
            $user->syncRoles($request->roles ?? []);
        }

        return response()->json(['success' => 'User updated successfully', 'user' => $user->load('roles')]);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(['success' => 'User deleted successfully']);
    }
}
