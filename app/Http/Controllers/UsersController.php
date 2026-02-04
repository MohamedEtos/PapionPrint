<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

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
            'base_salary' => 'nullable|numeric',
            'working_hours' => 'nullable|integer',
            'shift_start' => 'nullable',
            'shift_end' => 'nullable',
            'overtime_rate' => 'nullable|numeric|min:0',
            'joining_date' => 'nullable|date',
            'resignation_date' => 'nullable|date',
            'roles' => 'array'
        ]);

        DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => $request->name,
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'base_salary' => $request->base_salary,
                'working_hours' => $request->working_hours ?? 8,
                'shift_start' => $request->shift_start,
                'shift_end' => $request->shift_end,
                'overtime_rate' => $request->overtime_rate,
                'joining_date' => $request->joining_date,
                'resignation_date' => $request->resignation_date,
            ]);

            if ($request->has('roles')) {
                $user->assignRole($request->roles);
            }
        });

        return response()->json(['success' => 'تم اضافة المستخدم بنجاح!']);
    }

    public function update(Request $request, $id)
    {
        \Illuminate\Support\Facades\Validator::make(['id' => $id], [
            'id' => 'required|exists:users,id',
        ])->validate();
        $user = User::findOrFail($id);
        
        $request->validate([
            'name' => 'required',
            'username' => 'required|unique:users,username,'.$user->id,
            'email' => 'required|email|unique:users,email,'.$user->id,
            'base_salary' => 'nullable|numeric',
            'working_hours' => 'nullable|numeric',
            'shift_start' => 'nullable',
            'shift_end' => 'nullable',
            'overtime_rate' => 'nullable|numeric|min:0',
            'joining_date' => 'nullable|date',
            'resignation_date' => 'nullable|date',
            'roles' => 'nullable|array'
        ]);

        $data = [
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'base_salary' => $request->base_salary,
            'working_hours' => $request->working_hours,
            'shift_start' => $request->shift_start,
            'shift_end' => $request->shift_end,
            'overtime_rate' => $request->overtime_rate,
            'joining_date' => $request->joining_date,
            'resignation_date' => $request->resignation_date,
        ];

        if ($request->password) {
            $data['password'] = Hash::make($request->password);
        }

        DB::transaction(function () use ($user, $data, $request) {
            $user->update($data);
            
            if ($request->has('roles') || $request->has('update_roles')) {
                $user->syncRoles($request->roles ?? []);
            }
        });

        return response()->json(['success' => 'User updated successfully', 'user' => $user->load('roles')]);
    }

    public function destroy($id)
    {
        \Illuminate\Support\Facades\Validator::make(['id' => $id], [
            'id' => 'required|exists:users,id',
        ])->validate();
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(['success' => 'User deleted successfully']);
    }
}
