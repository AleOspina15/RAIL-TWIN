
@extends('adminlte::page')

@section('title', '')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-1">
            <div class="col-12 col-sm-12 col-md-12 col-lg-8 col-xl-8">
                <h4 class="text-sidap text-bold m-0"><i class="fas fa-fw fa-lock" ></i> Permisos</h4>
            </div>
            <div class="col-lg-4 col-xl-4 d-none d-lg-inline-block">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">pGIS</a></li>
                    <li class="breadcrumb-item">Usuarios</li>
                    <li class="breadcrumb-item active">Permisos</li>
                </ol>
            </div>
        </div>
    </div>
@stop

@section('content')

    <div class="row">
        <div class="col-12 mt-4">
            <form action="{{ route("permissions.store") }}" method="POST" enctype="multipart/form-data">
                @csrf
                <label for="name">Nombre*</label>
                <div class="input-group {{ $errors->has('name') ? 'has-error' : '' }}">
                    <input type="text" id="name" name="name" class="form-control" value="{{ old('name', isset($permission) ? $permission->name : '') }}" required>
                    @if($errors->has('name'))
                        <em class="invalid-feedback">
                            {{ $errors->first('name') }}
                        </em>
                    @endif
                </div>
                <div class="mt-4">
                    <input class="btn btn-danger" type="submit" value="Guardar">
                    &nbsp;
                    <a href="{{ route('permissions.index') }}" class="btn btn-primary">Cancelar</a>
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

@stop
