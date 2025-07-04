<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use JeroenNoten\LaravelAdminLte\Events\BuildingMenu;
use Spatie\Permission\Models\Role;
use DB;
use Hash;
use Illuminate\Support\Arr;

class UserController extends Controller
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

        $users = User::all();

        $breadcum_arr = ["Administración de usuarios","Usuarios"];

        return view('admin.users.index',['layout' => 'simple-menu'], compact('breadcum_arr','users'));
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

        $breadcum_arr = ["Administración de usuarios","Usuarios","Nuevo usuario"];

        $roles = Role::pluck('name','name')->all();
        return view('admin.users.create',['layout' => 'simple-menu'],compact('roles','breadcum_arr'));
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
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required',
            'roles' => 'required'
        ]);

        $input = $request->all();

        $input['password'] = Hash::make($input['password']);

        $user = User::create($input);

        $user->assignRole($request->input('roles'));

        return redirect()->route('users.index')
                        ->with('success','User created successfully');
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

        return redirect()->route('users.index');
    }



    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        if (! Gate::allows('Usuarios')) {
            return abort(401);
        }

        $roles = Role::get()->pluck('name', 'name');

        $breadcum_arr = ["Administración de usuarios","Usuarios","Editar usuario"];

        return view('admin.users.edit',['layout' => 'simple-menu'], compact('user', 'roles','breadcum_arr'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        if (! Gate::allows('Usuarios')) {
            return abort(401);
        }

        $id = $user->id;

        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,'.$id,
            'roles' => 'required'
        ]);

        $input = $request->all();
        if(!empty($input['password'])){
            $input['password'] = Hash::make($input['password']);
        }else{
            $input = Arr::except($input,array('password'));
        }

        $user = User::find($id);

        $user->update($input);

        DB::table('model_has_roles')->where('model_id',$id)->delete();

        $user->assignRole($request->input('roles'));

        return redirect()->route('users.index')
                        ->with('success','User updated successfully');
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

        User::find($id)->delete();

        return redirect()->route('users.index')
                        ->with('success','User deleted successfully');
    }

}
