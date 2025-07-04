<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePermissionsRequest;
use App\Http\Requests\UpdatePermissionsRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use JeroenNoten\LaravelAdminLte\Events\BuildingMenu;
use Spatie\Permission\Models\Permission;


class PermissionController extends Controller
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
     * Display a listing of Permission.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (! Gate::allows('Usuarios')) {
            return abort(401);
        }

        $permissions = Permission::all();

        $breadcum_arr = ["Administración de usuarios","Permisos"];

        return view('admin.permissions.index',['layout' => 'simple-menu'], compact('breadcum_arr','permissions'));
    }

    /**
     * Show the form for creating new Permission.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! Gate::allows('Usuarios')) {
            return abort(401);
        }

        $breadcum_arr = ["Administración de usuarios","Permisos","Nuevo permiso"];

        return view('admin.permissions.create',['layout' => 'simple-menu'], compact('breadcum_arr'));
    }

    /**
     * Store a newly created Permission in storage.
     *
     * @param  \App\Http\Requests\StorePermissionsRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePermissionsRequest $request)
    {
        if (! Gate::allows('Usuarios')) {
            return abort(401);
        }

        Permission::create($request->all());

        return redirect()->route('permissions.index');
    }


    /**
     * Show the form for editing Permission.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Permission $permission)
    {
        if (! Gate::allows('Usuarios')) {
            return abort(401);
        }

        $breadcum_arr = ["Administración de usuarios","Permisos","Editar permiso"];

        return view('admin.permissions.edit',['layout' => 'simple-menu'], compact('breadcum_arr','permission'));
    }

    /**
     * Update Permission in storage.
     *
     * @param  \App\Http\Requests\UpdatePermissionsRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatePermissionsRequest $request, Permission $permission)
    {

        if (! Gate::allows('Usuarios')) {
            return abort(401);
        }

        $permission->update($request->all());

        return redirect()->route('permissions.index');
    }


    /**
     * Remove Permission from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Permission $permission)
    {
        if (! Gate::allows('Usuarios')) {
            return abort(401);
        }

        $permission->delete();

        return redirect()->route('permissions.index');
    }

    public function show(Permission $permission)
    {
        if (! Gate::allows('Usuarios')) {
            return abort(401);
        }

        return redirect()->route('permissions.index');
    }

}
