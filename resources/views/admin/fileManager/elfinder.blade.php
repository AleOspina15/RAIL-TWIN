<?php
config([
    'adminlte.sidebar_collapse' => true
]);
?>
@section('plugins.jqueryUi', true)

@extends('adminlte::page')

@section('title', 'GA')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-1">
            <div class="col-12 col-sm-12 col-md-12 col-lg-9 col-xl-9">
                <h4 class="text-pafyc text-bold m-0"><i class="fas fa-fw fa-folder-open"></i> Gestor de archivos</h4>
            </div>
            <div class="col-lg-3 col-xl-3 d-none d-lg-inline-block">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">AICEDRONE SDI</li>
                    <li class="breadcrumb-item active">Gestor de archivos</li>
                </ol>
            </div>
        </div>
    </div>
@stop

@section('content')
    <input type="hidden" id="_token" name="_token" value="{{ csrf_token() }}" />

    <div id="file_info_dialog" title="Información del archivo" style="display: none; overflow: hidden">
        <div class="row" style="margin-top: 5px;margin-left: 0px; margin-right: 0px;padding: 0px">
            <div class="col-lg-12">
                <label class="mb-0" for="proyecto_file">Proyecto</label>
                <select class="form-control form-control-sm" id="proyecto_file">
                    <option value="">Seleccione un proyecto</option>
                </select>
            </div>
            <div class="col-lg-12 mt-2">
                <label class="mb-0" for="material_file">Material</label>
                <select class="form-control form-control-sm" id="material_file">
                    <option value="">Seleccione un material</option>
                </select>
            </div>
            <div class="col-lg-12 mt-2">
                <label class="mb-0">Descripción</label>
                <div class="input-group">
                    <textarea class="form-control" id="descripcion_file" rows="5"></textarea>
                </div>
            </div>
            <div class="col-lg-12 mt-2 text-right">
                <input id="ruta_file" type="hidden" value="">
                <a class="btn btn-danger btn-sm" href="javascript:void(0)" onclick="guardarInfoArchivo()" style="color: #fff">Guardar</a>
            </div>
        </div>
    </div>


            <div id="elfinder" class="w-100"></div>




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
        $(document).ready(function() {

            var w = document.documentElement.clientWidth;
            var h = document.documentElement.clientHeight;
            //console.log(h);

            var elf = $('#elfinder').elfinder({
                // set your elFinder options here
                lang: 'es', // locale
                customData: {
                    _token: '{{ csrf_token() }}'
                },
                url: '{{ route("elfinder.connector") }}',  // connector URL
                soundPath: '{{ asset('js/elfinder/sounds') }}',
                ui: ['toolbar', 'tree', 'path']

            }).elfinder('instance');

            /*
            elf.bind('select', function(e,fm) {
                $.each(e.data.files || fm.selected(), function(i, h) {
                    console.log(fm.file(h));
                });
            });
            */

            $("#file_info_dialog").dialog({
                autoOpen: false,
                width: 400,
                height: 350,
                modal: true,
                close: function(){

                },
                position: { my: "center center", at: "center center", of: window },
                dialogClass: 'sidap-dialog'
            });


            $("#elfinder").css('height',(h - 160) + 'px');
        });
    </script>
@stop



