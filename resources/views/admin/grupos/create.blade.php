@extends('adminlte::page')

@section('title', '')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-1">
            <div class="col-12 col-sm-12 col-md-12 col-lg-9 col-xl-9">
                <h4 class="text-sidap text-bold m-0"><i class="fas fa-fw fa-clone" ></i> Grupos</h4>
            </div>
            <div class="col-lg-3 col-xl-3 d-none d-lg-inline-block">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">pGIS</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Capas</a></li>
                    <li class="breadcrumb-item active">Grupos</li>
                </ol>
            </div>
        </div>
    </div>
@stop

@section('content')
<div class="card">


    <div class="card-body">
        <form action="{{ route("grupos.store") }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="row">

                <div class="col-xl-4 col-lg-4 col-12">
                    <div class="form-group {{ $errors->has('titulo') ? 'has-error' : '' }}">
                        <label for="titulo" class="mb-0">Título</label>
                        <input type="text" id="titulo" name="titulo" class="form-control" value="{{ old('titulo', isset($grupo) ? $grupo->titulo : '') }}" required>
                    </div>
                </div>
                <div class="col-xl-1 col-lg-2 col-md-4 col-12">
                    <div class="form-group">
                        <label class="mb-0" style="display: block">Visibilidad inicial</label>
                        <div class="btn-group btn-group-toggle" data-toggle="buttons">
                            <label class="btn btn-secondary active">
                                <input type="radio" name="visible" id="visible_radio_si" autocomplete="off" checked value="true"> Sí
                            </label>
                            <label class="btn btn-secondary">
                                <input type="radio" name="visible" id="visible_radio_no" autocomplete="off" value="false"> No
                            </label>
                        </div>
                    </div>
                </div>
                <div class="col-xl-1 col-lg-3 col-md-4 col-12">
                    <div class="form-group {{ $errors->has('zoom_max') ? 'has-error' : '' }}">
                        <label for="zoom_max" class="mb-0">Zoom máximo</label>
                        <input type="number" id="zoom_max" name="zoom_max" class="form-control" min="0" max="28" step="0.01" value="{{ old('zoom_max', isset($grupo) ? $grupo->zoom_max : 28) }}" required>
                    </div>
                </div>

                <div class="col-xl-1 col-lg-3 col-md-4 col-12">
                    <div class="form-group {{ $errors->has('zoom_min') ? 'has-error' : '' }}">
                        <label for="zoom_min" class="mb-0">Zoom mínimo</label>
                        <input type="number" id="zoom_min" name="zoom_min" class="form-control" min="0" max="28" step="0.01" value="{{ old('zoom_min', isset($grupo) ? $grupo->zoom_min : 0) }}" required>
                    </div>
                </div>
                <div class="col-12 mb-1">
                    <div class="form-group">
                        <label class="mb-0">Descripción</label>
                        <textarea class="form-control" id="descripcion" name="descripcion"></textarea>
                    </div>
                </div>


            </div>
            <div>

                <input type="hidden" id="posicion" name="posicion" value="{{ old('posicion', isset($grupo) ? $grupo->posicion : 1) }}">

                <input class="btn btn-danger" type="submit" value="Guardar">
                <a class="btn btn-primary ml-4" href="{{ url()->previous() }}">
                    Volver
                </a>
            </div>
        </form>


    </div>
</div>
@stop

@section('css')
    <link rel="stylesheet" href="{{ asset('css/admin_custom.css') }}">
@stop
