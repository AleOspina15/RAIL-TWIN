@extends('adminlte::page')

@section('title', '')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-1">
            <div class="col-12 col-sm-12 col-md-12 col-lg-8 col-xl-8">
                <h4 class="text-sidap text-bold m-0"><i class="fas fa-fw fa-user" ></i> Usuarios</h4>
            </div>
            <div class="col-lg-4 col-xl-4 d-none d-lg-inline-block">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">pGIS</a></li>
                    <li class="breadcrumb-item active">Usuarios</li>
                </ol>
            </div>
        </div>
    </div>
@stop

@section('content')

    <div class="row">
        <div class="col-12 mt-4">
            <form action="{{ route("users.store") }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                    <label for="name">Nombre*</label>
                    <input type="text" id="name" name="name" class="form-control" value="{{ old('name', isset($user) ? $user->name : '') }}" required>
                    @if($errors->has('name'))
                        <em class="invalid-feedback">
                            {{ $errors->first('name') }}
                        </em>
                    @endif
                </div>
                <div class="mt-4"></div>
                <div class="form-group {{ $errors->has('email') ? 'has-error' : '' }}">
                    <label for="email">Correo electrónico*</label>
                    <input type="email" id="email" name="email" class="form-control" value="{{ old('email', isset($user) ? $user->email : '') }}" required>
                    @if($errors->has('email'))
                        <em class="invalid-feedback">
                            {{ $errors->first('email') }}
                        </em>
                    @endif
                </div>
                <div class="mt-4"></div>
                <div class="form-group {{ $errors->has('password') ? 'has-error' : '' }}">
                    <label for="password">Contraseña</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                    @if($errors->has('password'))
                        <em class="invalid-feedback">
                            {{ $errors->first('password') }}
                        </em>
                    @endif
                </div>
                <div class="mt-4"></div>
                <div class="form-group {{ $errors->has('roles') ? 'has-error' : '' }}">
                    <label for="roles">Roles*
                        <span class="btn btn-primary text-xs select-all">Seleccionar todos</span>
                        <span class="btn btn-primary text-xs deselect-all">Deseleccionar todos</span></label>
                    <select name="roles[]" id="roles" class="form-control select2" multiple="multiple" required>
                        @foreach($roles as $id => $roles)
                            <option value="{{ $id }}" {{ (in_array($id, old('roles', [])) || isset($user) && $user->roles->contains($id)) ? 'selected' : '' }}>{{ $roles }}</option>
                        @endforeach
                    </select>
                    @if($errors->has('roles'))
                        <em class="invalid-feedback">
                            {{ $errors->first('roles') }}
                        </em>
                    @endif
                </div>
                <div class="mt-4">
                    <input class="btn btn-danger" type="submit" value="Guardar">
                    &nbsp;
                    <a href="{{ route('users.index') }}" class="btn btn-primary">Cancelar</a>
                </div>
            </form>


        </div>
    </div>

@stop

@section('css')

    <link rel="stylesheet" href="{{ asset('css/admin_custom.css') }}">
<link rel="stylesheet" href="{{ asset('css/bootstrap_4_extend.css') }}">
@stop

@section('js')
    <script src="{{ asset('js/admin_custom.js') }}"></script>
    <script>
        $(function () {
            $('.select-all').click(function () {
                let $select2 = $(this).parent().siblings('.select2')
                $select2.find('option').prop('selected', 'selected')
                $select2.trigger('change')
            })
            $('.deselect-all').click(function () {
                let $select2 = $(this).parent().siblings('.select2')
                $select2.find('option').prop('selected', '')
                $select2.trigger('change')
            })

            $('.select2').select2()
        })
    </script>
@stop
