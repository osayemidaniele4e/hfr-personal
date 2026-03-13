<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;
use App\Models\Hospital;


/**
  * Role Management Controller
  * @group Administration - Roles
  *
  * Handles CRUD operations for roles and permission assignments.
  *
  * @authenticated
  */
class RoleController extends Controller
{


/**
     * List Roles
     *
     * Displays a list of all roles with their permissions.
     *
     * @authenticated
     * @response 200 view HTML
     */
    public function index()
    {
        $roles = Role::get();
        $permissions = Permission::get();

        return view("roles.index", compact("roles", "permissions"));
    }


/**
     * Show Create Role Form
     *
     * Displays a form for creating a new role.
     *
     * @authenticated
     * @response 200 view HTML
     */
    public function create()
    {
        return view("roles.create");
    }



/**
     * Store Role
     *
     * Stores a new role in the system along with assigned permissions.
     *
     * @authenticated
     * @bodyParam name string required Role name. Example: Admin
     * @bodyParam description string required Role description. Example: Administrator role
     * @bodyParam permissions array required Array of permission IDs to assign. Example: [1, 2, 3]
     * @bodyParam roles_below array optional IDs of roles below this role. Example: [2, 4]
     * @response 302 Redirect to roles.index with success message
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'unique:roles|required|string|max:50',
            'description' => 'required|string|max:100',
            'permissions' => 'required',
        ]);

        // \Log::info($request->all());

        // dd(544554);
        $funct = new Hospital();
        $roles_below = $funct->arrayValuesTostring($request->roles_below);

        $role = Role::create([
            'name' => $request->name,
            'description' => $request->description,
            'roles_below' => $roles_below
        ]);

        // Get permission names from IDs
        $permissionNames = Permission::whereIn('id', $request->permissions)->pluck('name')->toArray();
        $role->givePermissionTo($permissionNames);

        // $role->givePermissionTo($request->permissions);

        session()->flash("alert-success", "Role created successfully!");
        return redirect()->route('roles.index');
    }


    protected function assignPermissionsToRole($role, array $permissionIds)
    {
        $permissionNames = [];

        foreach ($permissionIds as $id) {
            // Find or create permission by ID (assuming IDs represent permission names or you map them)
            $permission = Permission::find($id);

            if (! $permission) {
                // You need to decide what 'name' to assign for a missing permission
                // For example, if you know the permission names, create by name here instead
                $permission = Permission::create([
                    'name' => 'permission_' . $id,  // Customize this logic as needed
                    'guard_name' => 'web',          // or your guard name
                ]);
            }

            $permissionNames[] = $permission->name;
        }

        $role->givePermissionTo($permissionNames);
    }



/**
     * Show Edit Role Form
     *
     * Displays a form for editing an existing role.
     *
     * @authenticated
     * @urlParam id int required Role ID. Example: 1
     * @response 200 view HTML
     */
    public function edit($id)
    {
        $roles = Role::where('id', $id)->get();

        $permissionz = DB::select("SELECT permission_id id FROM role_has_permissions where role_id = " . $id . "");

        foreach ($roles as $rol) {
            $role = $rol;
        }

        $permission = [];
        foreach ($permissionz as $p) {
            $permission[] = $p->id;
        }

        return view("roles.edit", compact('role', 'permission'));
    }



 /**
     * Update Role
     *
     * Updates an existing role's information and permissions.
     *
     * @authenticated
     * @bodyParam id int required Role ID. Example: 1
     * @bodyParam name string required Role name. Example: Manager
     * @bodyParam description string required Role description. Example: Manager role
     * @bodyParam permissions array required Array of permission IDs. Example: [1, 2, 3]
     * @bodyParam roles_below array optional IDs of roles below this role. Example: [2, 4]
     * @response 302 Redirect to roles.index with success message
     */
    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:50',
            'description' => 'required|string|max:100',
            'permissions' => 'required',
        ]);

        $funct = new Hospital;
        $roles_below = $funct->arrayValuesTostring($request->roles_below);

        $role = Role::findOrfail($request->id);
        $role->name = $request->name;
        $role->description = $request->description;
        $role->roles_below = $roles_below;
        $role->save();

        // Convert IDs to permission names
        $permissionNames = Permission::whereIn('id', $request->permissions)->pluck('name')->toArray();

        // Sync permissions using names
        $role->syncPermissions($permissionNames);

        // $role->syncPermissions($request->permissions);

        session()->flash("alert-success", "Role updated successfully!");
        return redirect()->route('roles.index');
    }

    public function destroy(Request $request)
    {

        $role = Role::find($request->role_id);
        $role->syncPermissions([]);

        Role::destroy($request->role_id);
        session()->flash("alert-success", "Role deleted successfully!");
        return back();
    }
}
