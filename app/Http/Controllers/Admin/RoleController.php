<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Helpers\PermissionHelper;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::with('permissions')->orderBy('name')->paginate(15);

        // Statistics
        $totalRoles = Role::count();
        $totalPermissions = Permission::count();
        $rolesWithUsers = Role::has('users')->count();

        // Calculate average permissions per role
        $rolesWithCounts = Role::withCount('permissions')->get();
        $avgPermissionsPerRole = $rolesWithCounts->avg('permissions_count');

        return view('admin.roles.index', compact('roles', 'totalRoles', 'totalPermissions', 'rolesWithUsers', 'avgPermissionsPerRole'));
    }

    public function create()
    {
        $permissions = Permission::orderBy('name')->get();
        $groupedPermissions = PermissionHelper::getGroupedPermissions($permissions);
        return view('admin.roles.create', compact('permissions', 'groupedPermissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'name_ar' => 'nullable|string|max:255',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role = Role::create([
            'name' => $request->name,
            'guard_name' => 'web',
        ]);

        if ($request->has('permissions')) {
            $permissions = Permission::whereIn('id', $request->permissions)->get();
            $role->syncPermissions($permissions);
        }

        return redirect()->route('admin.roles.index')
            ->with('success', 'تم إنشاء الدور بنجاح');
    }

    public function show(Role $role)
    {
        $role->load('permissions', 'users');
        return view('admin.roles.show', compact('role'));
    }

    public function edit(Role $role)
    {
        $permissions = Permission::orderBy('name')->get();
        $groupedPermissions = PermissionHelper::getGroupedPermissions($permissions);
        $rolePermissions = $role->permissions->pluck('id')->toArray();
        return view('admin.roles.edit', compact('role', 'permissions', 'rolePermissions', 'groupedPermissions'));
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role->update(['name' => $request->name]);

        if ($request->has('permissions')) {
            $permissions = Permission::whereIn('id', $request->permissions)->get();
            $role->syncPermissions($permissions);
        } else {
            $role->syncPermissions([]);
        }

        return redirect()->route('admin.roles.index')
            ->with('success', 'تم تحديث الدور بنجاح');
    }

    public function destroy(Role $role)
    {
        if ($role->users()->count() > 0) {
            return redirect()->route('admin.roles.index')
                ->with('error', 'لا يمكن حذف هذا الدور لأنه مرتبط بمستخدمين');
        }

        $role->delete();

        return redirect()->route('admin.roles.index')
            ->with('success', 'تم حذف الدور بنجاح');
    }
}
