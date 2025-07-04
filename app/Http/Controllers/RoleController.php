<?php
namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use JeroenNoten\LaravelAdminLte\Events\BuildingMenu;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{

    public function __construct()
    {
        /*
        Event::listen(BuildingMenu::class, function (BuildingMenu $event) {
            if (!Auth::guest()) {
                $event->menu->remove('iniciar_sesion_key');
                $event->menu->remove('restablecer_contrasenia_key');
            }
        });
        */
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (! Gate::allows('Usuarios')) {
            return abort(401);
        }

        $roles = Role::all();

        $breadcum_arr = ["Administración de usuarios","Roles"];

        return view('admin.roles.index',['layout' => 'simple-menu'], compact('roles','breadcum_arr'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! Gate::allows('Usuarios')) {
            return abort(401);
        }

        $breadcum_arr = ["Administración de usuarios","Roles","Nuevo rol"];

        $permissions = Permission::get()->pluck('name', 'name');
        return view('admin.roles.create',['layout' => 'simple-menu'],compact('permissions','breadcum_arr'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (! Gate::allows('Usuarios')) {
            return abort(401);
        }

        $this->validate($request, [
            'name' => 'required|unique:roles,name',
            'permission' => 'required',
        ]);

        $role = Role::create(['name' => $request->input('name')]);
        $role->syncPermissions($request->input('permission'));

        return redirect()->route('roles.index')
                        ->with('success','Role created successfully');
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (! Gate::allows('Usuarios')) {
            return abort(401);
        }

        return redirect()->route('roles.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Role $role)
    {
        if (! Gate::allows('Usuarios')) {
            return abort(401);
        }

        $permissions = Permission::get()->pluck('name', 'name');

        $breadcum_arr = ["Administración de usuarios","Roles","Editar rol"];

        return view('admin.roles.edit',['layout' => 'simple-menu'], compact('role', 'permissions','breadcum_arr'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Role $role)
    {
        if (! Gate::allows('Usuarios')) {
            return abort(401);
        }

        $this->validate($request, [
            'name' => 'required',
            'permission' => 'required',
        ]);

        $role->update($request->except('permission'));
        $permissions = $request->input('permission') ? $request->input('permission') : [];
        $role->syncPermissions($permissions);

        return redirect()->route('roles.index');
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (! Gate::allows('Usuarios')) {
            return abort(401);
        }

        DB::table("roles")->where('id',$id)->delete();
        return redirect()->route('roles.index')
                        ->with('success','Role deleted successfully');
    }
}
