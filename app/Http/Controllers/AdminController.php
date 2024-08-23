<?php

namespace App\Http\Controllers;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Traits\HasRoles;

class AdminController extends Controller
{
    //
    public function showAssignRoleForm()
    {
        $users = User::all();
        $roles = Role::all();

        return view('Admin.roles_assign', compact('users', 'roles'));
    }

    public function assignRole(Request $request)
{
    $request->validate([
        'user_id' => 'required|exists:users,id',
        'role' => 'required|exists:roles,name',
    ]);

    $user = User::find($request->user_id);
    $role = $request->role;

    // Remove all existing roles
    $user->roles()->detach();

    // Assign the new role
    $user->assignRole($role);

    return redirect()->back()->with('status', 'Role assigned successfully.');
}

}

