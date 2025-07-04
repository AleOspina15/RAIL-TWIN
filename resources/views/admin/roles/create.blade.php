
@extends('adminlte::page')

@section('title', '')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-1">
            <div class="col-12 col-sm-12 col-md-12 col-lg-8 col-xl-8">
                <h4 class="text-sidap text-bold m-0"><i class="fas fa-fw fa-briefcase" ></i> Roles</h4>
            </div>
            <div class="col-lg-4 col-xl-4 d-none d-lg-inline-block">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">pGIS</a></li>
                    <li class="breadcrumb-item">Usuarios</li>
                    <li class="breadcrumb-item active">Roles</li>
                </ol>
            </div>
        </div>
    </div>
@stop

@section('content')

    <div class="row">
        <div class="col-12 mt-4">
            <form action="{{ route("roles.store") }}" method="POST" enctype="multipart/form-data">
                @csrf
                <label for="name">Nombre*</label>
                <div class="input-group {{ $errors->has('name') ? 'has-error' : '' }}">
                    <input type="text" id="name" name="name" class="form-control" value="{{ old('name', isset($role) ? $role->name : '') }}" required>
                    @if($errors->has('name'))
                        <em class="invalid-feedback">
                            {{ $errors->first('name') }}
                        </em>
                    @endif
                </div>
                <div class="mt-4"></div>
                <div class="form-group {{ $errors->has('permissions') ? 'has-error' : '' }}">
                    <label for="permission">Permisos*
                        <span class="btn btn-primary text-xs select-all">Seleccionar todos</span>
                        <span class="btn btn-primary text-xs deselect-all">Deseleccionar todos</span></label>
                    <select name="permission[]" id="permission" class="form-control select2" multiple="multiple" required>
                        @foreach($permissions as $id => $permissions)
                            <option value="{{ $id }}" {{ (in_array($id, old('permission', [])) || isset($role) && $role->permissions->contains($id)) ? 'selected' : '' }}>{{ $permissions }}</option>
                        @endforeach
                    </select>
                    @if($errors->has('permission'))
                        <em class="invalid-feedback">
                            {{ $errors->first('permission') }}
                        </em>
                    @endif
                </div>
                <div class="mt-4">
                    <input class="btn btn-danger" type="submit" value="Guardar">
                    &nbsp;
                    <a href="{{ route('roles.index') }}" class="btn btn-primary">Cancelar</a>
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
