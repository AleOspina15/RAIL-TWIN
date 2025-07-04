@include('admin.adminlte_config')
@section('plugins.jqueryUi', true)
<?php
config([
    'adminlte.sidebar_collapse' => true
]);
?>
@extends('adminlte::page')

@section('title', '')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-1">
            <div class="col-12 col-sm-12 col-md-12 col-lg-9 col-xl-9">
                <h4 class="text-pafyc text-bold m-0"><i class="fas fa-fw fa-folder-open"></i> <b>{{ $nombre }}</b></h4>
            </div>
            <div class="col-lg-3 col-xl-3 d-none d-lg-inline-block">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">RAIL TWIN</li>
                    <li class="breadcrumb-item active">Proyectos</li>
                    <li class="breadcrumb-item active">Gestor de archivos</li>
                </ol>
            </div>
        </div>
    </div>
@stop

@section('content')
    <input type="hidden" id="_token" name="_token" value="{{ csrf_token() }}"/>
    <input type="hidden" id="id_proyecto" name="id_proyecto" value="{{ $id }}"/>
    <input type="hidden" id="carpeta_proyecto" name="carpeta_proyecto" value="{{ $carpeta_proyecto }}">



    <div class="row">
        <div class="col-12">
            <div id="elfinder"></div>
        </div>
    </div>

@stop

@section('css')
    <link rel="stylesheet" href="{{ asset('css/admin_custom.css') }}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap_4_extend.css') }}">

    <link rel="stylesheet" type="text/css" href="{{ asset('css/elfinder/elfinder.full.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/elfinder/theme.css') }}">
@stop

@section('js')
    <!-- <script src="{ { asset('js/admin_custom.js') }}"></script> -->
    <script src="{{ asset('js/elfinder/elfinder.full.js') }}"></script>
    <script src="{{ asset('js/elfinder/i18n/elfinder.es.js') }}"></script>
    <script type="text/javascript" charset="utf-8">
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        /*
                function mostrarEditor(ruta) {
                    $("#ruta_file").val(ruta);

                    $("#file_info_dialog").dialog('open');
                }

                function guardarInfoArchivo() {
                    var ruta = $("#ruta_file").val();
                    var descripcion = $("#descripcion_file").val();


                    $.ajax({
                        url: "guardarInfoArchivo",
                        type: 'POST',
                        data: {_token: CSRF_TOKEN, ruta: ruta, descripcion: descripcion
                        },
                        dataType: 'JSON',
                        success: function (data) {
                            location = "http://92.222.208.150/fileManager";
                        }
                    });


                    $("#file_info_dialog").dialog('close');
                }
        */
        // Documentation for client options:
        // https://github.com/Studio-42/elFinder/wiki/Client-configuration-options
        $(document).ready(function () {

            var w = document.documentElement.clientWidth;
            var h = document.documentElement.clientHeight;
            //console.log(h);


            var volumeId = 'l1_'; // volume id
            var path = 'Proyectos/La Roda'; // without root path
            var hash = volumeId + btoa(path).replace(/\+/g, '-').replace(/\//g, '_').replace(/=/g, '.').replace(/\.+$/, '');
            //console.log(hash);


            var carpeta_proyecto = $("#carpeta_proyecto").val();

            var elf = $('#elfinder').elfinder({
                // set your elFinder options here
                lang: 'es', // locale
                startPath: 'Proyectos',
                customData: {
                    _token: '{{ csrf_token() }}',
                    path: '/var/www/aicedronesdi_filemanager/Proyectos/' + carpeta_proyecto,
                    startPath: '/var/www/aicedronesdi_filemanager/Proyectos/' + carpeta_proyecto,
                },
                rememberLastDir: false,
                url: '{{ route("elfinder2.connector") }}',  // connector URL
                soundPath: '{{ asset('js/elfinder/sounds') }}',
                ui: ['toolbar', 'tree', 'path'],


            }).elfinder('instance');

            /*
            elf.bind('select', function(e,fm) {
                $.each(e.data.files || fm.selected(), function(i, h) {
                    console.log(fm.file(h));
                });
            });
            */


            $("#elfinder").css('height', (h - 160) + 'px');
        });
    </script>
@stop



