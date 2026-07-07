<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use App\Models\ActivityLog;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::all();
        return view('roles.index', compact('roles'));
    }
    public function create()
    {
        return view('roles.create');
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name'
        ]);

        $role = Role::create([
            'name' => $request->name
        ]);
        ActivityLog::create([
            'user_id'     => auth()->id(),
            'module'      => 'Role',
            'action'      => 'CREATE',
            'reference_id' => $role->id,
            'description' => "Created role: {$role->name}",
            'ip_address'  => request()->ip(),
            'user_agent'  => request()->userAgent(),
        ]);

        return redirect()
            ->route('roles.index')
            ->with('success', 'Role created successfully!');
    }
    public function edit(Role $role)
    {
        return view('roles.edit', compact('role'));
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|unique:roles,name,' . $role->id
        ]);

        $role->update([
            'name' => $request->name
        ]);
        ActivityLog::create([
            'user_id'      => auth()->id(),
            'module'       => 'Role',
            'action'       => 'UPDATE',
            'reference_id'    => $role->id, // illa reference_id if DB-la innum adhu dhaan
            'description'  => "Updated role: {$role->name}",
            'ip_address'   => request()->ip(),
            'user_agent'   => request()->userAgent(),
        ]);

        return redirect()
            ->route('roles.index')
            ->with('success', 'Role updated successfully!');
    }
}