<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;

/**
 * What each role is allowed to do. The owner's role is shown but locked:
 * it always holds everything.
 */
class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::withCount(['permissions', 'users'])->orderBy('id')->get();

        return view('admin.roles.index', [
            'roles' => $roles,
            'total_permissions' => Permission::count(),
        ]);
    }

    public function edit(Role $role)
    {
        abort_if($role->is_locked, 403, __('admin.role_locked'));

        return view('admin.roles.edit', [
            'role'   => $role,
            'groups' => Permission::byGroup(),
            'held'   => $role->permissions()->pluck('permissions.id')->all(),
        ]);
    }

    public function update(Request $request, Role $role)
    {
        abort_if($role->is_locked, 403, __('admin.role_locked'));

        $request->validate([
            'permissions'   => 'array',
            'permissions.*' => 'integer',
        ]);

        // Only real permission ids are kept, so a tampered form cannot invent rows
        $granted = Permission::whereIn('id', $request->input('permissions', []))->pluck('id');

        $role->permissions()->sync($granted);

        return redirect()
            ->route('admin.roles.index')
            ->with('msg', __('admin.role_updated'))
            ->with('type', 'success');
    }
}
