@extends('adminlte::page')

@section('title', '')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-1">
            <div class="col-12 col-sm-12 col-md-12 col-lg-9 col-xl-9">
                <h4 class="text-sidap text-bold m-0"><i class="fas fa-fw fa-square" ></i> Capas</h4>
            </div>
            <div class="col-lg-3 col-xl-3 d-none d-lg-inline-block">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">pGIS</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Capas</a></li>
                    <li class="breadcrumb-item active">Capas</li>
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


            <form action="{{ route("capas.store") }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="form-row">
                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                        <div class="form-group {{ $errors->has('titulo') ? 'has-error' : '' }}">
                            <label for="titulo">Título</label>
                            <input type="text" id="titulo" name="titulo" class="form-control" value="{{ old('titulo', isset($capa) ? $capa->titulo : '') }}" required>
                        </div>
                    </div>
                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                        <div class="form-group {{ $errors->has('nombre') ? 'has-error' : '' }}">
                            <label for="nombre">Nombre</label>
                            <input type="text" id="nombre" name="nombre" class="form-control" value="{{ old('nombre', isset($capa) ? $capa->nombre : '') }}" required readonly>
                        </div>
                    </div>
                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                        <div class="form-group {{ $errors->has('id_grupo') ? 'has-error' : '' }}">
                            <label for="id_grupo">Grupo</label>
                            <select id="id_grupo" name="id_grupo" class="form-control">
                                @foreach($grupos as $key => $grupo)
                                    @if (Request::old('id_grupo') == $grupo->id)
                                        <option value="{{ $grupo->id }}" selected>{{ $grupo->titulo }}</option>
                                    @else
                                        <option value="{{ $grupo->id }}">{{ $grupo->titulo }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-xl-2 col-lg-2 col-md-6 col-sm-12">
                        <label for="visible">Visibilidad inicial</label>
                        <div class="form-group">
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

                </div>



                <div class="form-row mb-4">
                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                        <label for="pafyc_file_name">Ruta del archivo</label>
                        <div class="input-group">
                            <input type="text" id="pafyc_file_name" name="pafyc_file_name" class="form-control" aria-describedby="basic-addon2" value="" required readonly>
                            <div class="input-group-append">
                                <span class="input-group-text btn btn-primary" id="basic-addon2" style="cursor: pointer" onclick="showPafycFiles(['tif','shp','TIF','SLD']);"><i class="far fa-folder-open"></i></span>
                            </div>
                        </div>
                    </div>

                    <div id="tipo_capa_vectorial_div" class="col-xl-6 col-lg-6 col-md-6 col-sm-12" style="display: none">
                        <div class="form-group">
                            <label for="tipo_capa_vectorial">Tipo de capa vectorial</label>
                            <select id="tipo_capa_vectorial" name="tipo_capa_vectorial" class="form-control">
                                <option value="point" selected>Puntos</option>
                                <option value="line">Líneas</option>
                                <option value="polygon">Polígonos</option>
                            </select>
                        </div>
                    </div>

                </div>

                <input type="hidden" id="posicion" name="posicion" value="{{ old('posicion', isset($capa) ? $capa->posicion : 0) }}">
                <input id="url" name="url" type="hidden" value="http://{{ env('DB_HOST') }}:8080/geoserver/pgis/wms">
                <input id="estilo" name="estilo" type="hidden" value="">
                <input id="id_tipo" name="id_tipo" type="hidden" value="1">
                <input id="origen" name="origen" type="text" value="">



                <div>
                    <input class="btn btn-danger" type="submit" value="Guardar">
                    <a class="btn btn-primary ml-2" type="button" href="{{ route('capas.index') }}">Volver</a>
                </div>
            </form>


        </div>


        <div id="pafyc_files_dialog" title="Gestor de archivos" style="display: none">
            <div class="row" style="padding: 0px;margin: 0px">
                <div class="col-lg-12" style="margin: 0px; padding: 0px">
                    <div id="jstree_demo_div">

                    </div>
                </div>
            </div>
        </div>


    </div>
@stop

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.2.1/themes/default/style.min.css" />

    <link rel="stylesheet" href="{{ asset('css/admin_custom.css') }}">
<link rel="stylesheet" href="{{ asset('css/bootstrap_4_extend.css') }}">
@stop

@section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.2.1/jstree.min.js"></script>
    <script type="text/javascript">
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

        $('#titulo').on('input', function() {
            var name = $(this).val();
            var outString = name.replace("Á","A")
                .replace("É","E")
                .replace("Í","I")
                .replace("Ó","O")
                .replace("Ú","U")
                .replace("á","a")
                .replace("é","e")
                .replace("í","i")
                .replace("ó","o")
                .replace("ú","u")
                .replace("Ñ","n")
                .replace("ñ","n")
                .replace("Ä","A")
                .replace("Ë","E")
                .replace("Ï","I")
                .replace("Ö","O")
                .replace("Ü","U")
                .replace("ä","a")
                .replace("ë","e")
                .replace("ï","i")
                .replace("ö","o")
                .replace("ü","u")
                .replace(/\s/g,"_");
            outString = outString.replace(/[`~!@#$%^&*()|+\-=?;:'",.<>\{\}\[\]\\\/]/gi, '_');
            outString = outString.replace(/(?!\w|\s)./g, '_')
                .replace(/\s+/g, '_')
                .replace(/^(\s*)([\W\w]*)(\b\s*$)/g, '$2');
            outString = outString.replace(' ','_');
            outString = outString.toLowerCase();

            $("#nombre").val( outString );
        });



        $('#tipo_capa_vectorial').on('change', function() {
            var tipo_capa_vectorial = $(this).val();
            $("#estilo").val(tipo_capa_vectorial);
        });

        function getPafycFiles(extension_arr) {

            $.ajax({
                url:  "{{ route('getPafycFiles') }}",
                type: 'POST',
                data: {_token: CSRF_TOKEN, extension_arr: extension_arr},
                dataType: 'text',
                success: function (data) {
                    //console.log(data);

                    $('#jstree_demo_div').html('<ul>'+data+'</ul>');
                    $('#jstree_demo_div').jstree();

                    $("#jstree_demo_div").on(
                        "select_node.jstree", function(evt, data){
                            //selected node object: data.node;
                            //console.log(data);
                            if (data.node.icon == 'far fa-file') {
                                //Obtener nombre de los padres (parents)
                                var parents_str = '';
                                for(x=0;x<data.node.parents.length-1;x++){
                                    var parent_node = $('#jstree_demo_div').jstree(true).get_node(data.node.parents[x]);
                                    //console.log(parent_node.text);
                                    //var aux_str = parent_node.text.split(';')[1];
                                    var aux_str = parent_node.text.trim();
                                    parents_str = aux_str + '/' + parents_str;
                                }

                                var source_type = data.node.text.trim().split('.')[1];
                                if (source_type === 'tif') {
                                    $("#estilo").val('raster');
                                    $("#tipo_capa_vectorial_div").css('display','none');
                                }
                                else {
                                    $("#tipo_capa_vectorial_div").css('display','block');
                                    var tipo_capa_vectorial = $("#tipo_capa_vectorial").val();
                                    $("#estilo").val(tipo_capa_vectorial);
                                }

                                if (source_type === 'zip')
                                    source_type = 'shp';

                                $("#source_type").val( source_type );
                                $("#origen").val( source_type );
                                $("#pafyc_file_name").val( parents_str + data.node.text.trim() );

                                $("#pafyc_files_dialog").dialog('close');
                            }
                        }
                    );

                }
            });

        }

        function showPafycFiles() {
            $("#pafyc_files_dialog").dialog('open');
        }

        $(document).ready(function(){

            $("#pafyc_files_dialog").dialog({
                autoOpen: false,
                width: 400,
                height: 500,
                modal: true,
                close: function(){

                },
                position: { my: "center center+50", at: "center center", of: window },
                dialogClass: 'pafyc-dialog'
            });

            //getProjectsSymbologies();
            getPafycFiles(['tif','shp','TIF','SHP','zip','ZIP']);


            var source_type = $("#pafyc_file_name").val().split('.')[1];
            if (source_type === 'tif') {
                $("#estilo").val('raster');
                $("#tipo_capa_vectorial_div").css('display','none');
                $("#origen").val('tif');
            }
            else {
                $("#tipo_capa_vectorial_div").css('display','block');
                var tipo_capa_vectorial = $("#tipo_capa_vectorial").val();
                $("#estilo").val(tipo_capa_vectorial);
                $("#origen").val('shp');
            }


        });


    </script>
@stop
