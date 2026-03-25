<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;
use App\HospitalHistory;


class RoleController extends Controller
{

    public function index()
    {
        $roles=Role::get();
        $permissions=Permission::get();

        return view("roles.index",compact("roles","permissions"));
    }

    public function create()
    {       
        return view("roles.create");
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'unique:roles|required|string|max:50',
            'description' => 'required|string|max:100',
            'permissions' => 'required',
        ]);

        $funct = new HospitalHistory;
        $roles_below = $funct->arrayValuesTostring($request->roles_below);

        $role = Role::create([
            'name' => $request->name,
            'description' => $request->description,
            'roles_below' => $roles_below
            ]);
        $role->givePermissionTo($request->permissions);

        session()->flash("alert-success", "Role created successfully!");
        return redirect()->route('roles.index');
    }


    public function edit($id)
    {
        $roles=Role::where('id',$id)->get();

        $permissionz = DB::select("SELECT permission_id id FROM role_has_permissions where role_id = ".$id."");
        
        foreach ($roles as $rol) {
            $role = $rol;
        }

        $permission =[];
        foreach ($permissionz as $p) {
            $permission[] = $p->id;
        }
       
        return view("roles.edit",compact('role','permission'));
    }


    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:50',
            'description' => 'required|string|max:100',
            'permissions' => 'required',
        ]);

        $funct = new HospitalHistory;
        $roles_below = $funct->arrayValuesTostring($request->roles_below);

        $role = Role::findOrfail($request->id);
        $role->name = $request->name;
        $role->description = $request->description;
        $role->roles_below = $roles_below;
        $role->save();

        $role->syncPermissions($request->permissions);

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
