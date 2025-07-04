@extends('adminlte::page')

@section('title', '')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-1">
            <div class="col-12 col-sm-12 col-md-12 col-lg-9 col-xl-9">
                <h4 class="text-sidap text-bold m-0"><i class="fa-solid fa-toolbox"></i> Nuevo Proyecto</h4>
            </div>
            <div class="col-lg-3 col-xl-3 d-none d-lg-inline-block">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">RAIL TWIN</li>
                    <li class="breadcrumb-item">Proyectos</li>
                    <li class="breadcrumb-item active">Nuevo Proyecto</li>
                </ol>
            </div>
        </div>
    </div>
@stop

@section('content')
    <div class="card">


        <div class="card-body">

            @if($errors->count() > 0)
                <div class="alert alert-danger">
                    <ul class="list-unstyled">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif


            <form>
                <div class="row">
                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">

                        <div class="row">
                            <div class="col-xxl-8 col-xl-7 col-lg-6 col-md-6 col-sm-12">
                                <div class="form-group {{ $errors->has('nombre') ? 'has-error' : '' }}">
                                    <label class="mb-0">Nombre</label>
                                    <input type="text" id="nombre" name="nombre" class="form-control" value="{{ old('nombre', isset($proyecto) ? $proyecto->nombre : '') }}" required>
                                </div>
                            </div>
                            <div class="col-xxl-4 col-xl-5 col-lg-6 col-md-6 col-sm-12">
                                <div class="form-group b-0 mt-0">
                                    <label class="mb-0">Duración</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="far fa-clock"></i></span>
                                        </div>
                                        <input type="text" class="form-control float-right" id="duracion_proyecto">
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                        <div id="map" style="width: 100%;height: 500px;border: solid;border-color: #212529;background-color: #212529"></div>
                    </div>
                </div>


                <div class="mt-2">
                    <a href="javascript:void(0)" onclick="f_obj.guardarProyecto()" class="btn btn-danger">Guardar</a>
                    <a class="btn btn-primary ml-2" type="button" href="{{ route('proyectos.index') }}">Volver</a>
                </div>
            </form>


        </div>





    </div>
@stop

@section('css')


@stop

@section('js')
    <script src="{{ asset('js/proyectos_create.js') }}"></script>
    <script type="text/javascript">
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');



        $(document).ready(function(){

            $('#duracion_proyecto').daterangepicker({
                "timePicker": false,
                "timePicker24Hour": false,
                "startDate": moment().subtract(1,'days'),
                "endDate": moment().add(1, 'years'),
                locale: {
                    format: 'DD/MM/YYYY'
                }
            }, function(start, end, label) {
                //console.log('New date range selected: ' + start.format('YYYY-MM-DD HH:mm:ss') + ' to ' + end.format('YYYY-MM-DD HH:mm:ss') + ' (predefined range: ' + label + ')');
                //window.f_obj.cargaFirmsNasaHotSpots(false);
            });


        });


    </script>
@stop
