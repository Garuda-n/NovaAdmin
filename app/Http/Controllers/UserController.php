<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();

        return view('users.index', compact('users'));
    }
    public function edit($id)
    {
        $user = User::findOrFail($id);

        return view('users.edit', compact('user'));
    }
    public function update(Request $request, $id)
{
    $user = User::findOrFail($id);

    $user->name = $request->name;
    $user->phone = $request->phone;
    $user->email = $request->email;

    $user->save();

    return redirect()->route('users.index')->with('success', 'User updated successfully!');
}
}