<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();

        return view('users.index', compact('users'));
    }
    public function create()
    {
        $roles = Role::all();
        return view('users.create', compact('roles'));
    }
    public function edit($id)
    {
        $user = User::findOrFail($id);
        $roles = Role::all();

        return view('users.edit', compact('user', 'roles'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'role_id' => 'nullable|exists:roles,id',
            'password' => 'required|confirmed|min:6',
        ]); 
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->role_id = $request->role_id;
        $user->password = Hash::make($request->password);
        $user->save();
        return redirect()->route('users.index')->with('success', 'User created successfully!');
    }
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $user->name = $request->name;
        $user->phone = $request->phone;
        $user->role = $request->role_id;
        $user->email = $request->email;

        $user->save();

        return redirect()->route('users.index')->with('success', 'User updated successfully!');
    }
    public function toggleStatus(User $user)
    {
        $user->status = !$user->status;
        $user->save();
        return redirect()->route('users.index')
            ->with('success', 'User status updated successfully.');
    }
}