<?php
config([
    'adminlte.sidebar_collapse' => true,
    'adminlte.sidebar_collapse_remember' => false,
    //'adminlte.layout_fixed_footer' => true,
]);
?>
@extends('adminlte::page')

@section('title', 'RAIL TWIN')


@section('content')
    <input type="hidden" id="json" name="json" value="{{ $json }}" />
    <input type="hidden" id="id_p" name="id_p" value="{{ $id }}" />

    <div id="dpi"></div>

    <div class="row m-0 p-0">
        <div class="col-12 m-0 p-0">
            <div id="map" class="m-0 p-0" style="width:100%;height: 200px;overflow: hidden;"></div>
        </div>
    </div>

    <div id="LayerSwitcher_dialog" title="Capas" style="display: none; overflow-x: hidden">
        <div id="LayerSwitcher_winbox" style="overflow-x: hidden;z-index: 1 !important;background-color: #fff !important;">
            <!--
            <div class="form-group form-group-sm form-check mb-0 text-right pr-2" style="background-color: #3c8dbc">
                <input type="checkbox" class="form-check-input" id="opacidad_chk" onchange="$('#LayerSwitcher_div').toggleClass('hideOpacity');">
                <label class="form-check-label" for="exampleCheck1" style="color: #fff; font-size: 13px !important; ">Opacidad</label>
            </div>
            -->
            <div id="LayerSwitcher_div" class="hideOpacity">

            </div>
            <div id="LayerSwitcher_options_div" class="d-none">
                <div class="wb-header-opciones-capa">
                    <div id="titulo-capa-seleccionada" class="wb-title-opciones-capa"></div>
                </div>
                <div class="ml-2 mr-2 text-center">
                    <button id="potree_opcion" class="btn btn-sm btn-datatable btn-warning pt-0 pb-0 pl-1 pr-1 d-none"
                            title="Visor Potree" onclick="f_obj.visorPotree();"><i class="fa-lg fa-solid fa-cubes"></i>
                    </button>
                    <button id="zoom_a_la_extension_opcion"
                            class="btn btn-sm btn-datatable btn-info pt-0 pb-0 pl-1 pr-1 d-none"
                            title="Zoom a la extensión" onclick="f_obj.zoomExtensionCapa();"><i
                            class="fa-lg fa-solid fa-expand"></i></button>
                    <button id="descargar_opcion" class="btn btn-sm btn-datatable btn-primary pt-0 pb-0 pl-1 pr-1 ml-2 d-none" title="Descargar" onclick="f_obj.descargarCapa();"><i class="fa-lg fa-solid fa-circle-down"></i></button>
                    <button id="leyenda_opcion" class="btn btn-sm btn-datatable btn-success pt-0 pb-0 pl-1 pr-1 ml-2 d-none" title="Leyenda" onclick="f_obj.leyendaCapa();"><i class="fa-lg fa-solid fa-palette"></i></button>
                    <button id="eliminar_opcion" class="btn btn-sm btn-datatable btn-danger pt-0 pb-0 pl-1 pr-1 ml-2 d-none" title="Eliminar" onclick="f_obj.eliminarCapa();"><i class="fa-lg fa-solid fa-trash"></i></button>
                </div>
            </div>
        </div>
    </div>

    <div id="Legend_dialog" title="Leyenda" style="display: none; overflow-x: hidden">
        <div id="Legend_winbox" style="overflow-x: hidden;z-index: 1 !important;background-color: #fff !important;">
            <div id="Legend_div" class="row">
                <div class="col-12 ml-1 mr-1 p-2">
                    <img id="leyenda_capa" class="img-fluid" src="">
                </div>
            </div>
        </div>
    </div>



    <div id="CargarProyecto_dialog" title="Cargar Proyecto" style="display: none">
        <div id="CargarProyecto_winbox" style="overflow: hidden">
            <div class="row p-2">
                <div class="col-12 mb-0">
                    <div class="input-group input-group-sm mb-0">
                        <select id="proyectos_select" class="form-control" onchange="window.f_obj.cargarProyecto()">

                        </select>
                    </div>
                </div>
                <div id="ogc_services" class="col-12 text-center mt-1">

                </div>
            </div>
        </div>
    </div>

    <div id="GestorArchivos_dialog" title="Gestor de Archivos" style="display: none">
        <div id="GestorArchivos_winbox" style="overflow: hidden;z-index: 1">
            <div class="row" style="padding: 0px;margin: 0px">
                <div class="col-lg-12" style="margin: 0px; padding: 0px">
                    <div id="jstree_demo_div">

                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="Anadir_producto_dialog" style="display: none">
        <div id="Anadir_producto_winbox" style="overflow: hidden">
            <div class="row pl-1 pr-1 pt-1 pb-0 m-0">
                <div class="col-9">
                    <label class="mb-0 text-sm" for="producto_file_name">Ruta del archivo (zip)</label>
                    <div class="input-group input-group-sm">
                        <input type="text" id="producto_file_name" name="producto_file_name" class="form-control" aria-describedby="basic-addon2" value="" required readonly>
                        <div class="input-group-append">
                            <span class="input-group-text btn btn-primary" id="basic-addon2" style="cursor: pointer" onclick="f_obj.showFiles(['zip'],'Productos');"><i class="far fa-folder-open"></i></span>
                        </div>
                    </div>
                </div>
                <div class="col-3">
                    <label class="mb-0 text-sm">&nbsp;&nbsp;&nbsp;</label>
                    <div class="input-group input-group-sm pb-0">
                        <button id="anadirProductoProyectoButton" type="button" class="btn btn-sm btn-success btn-block" onclick="window.f_obj.anadirProducto()">Publicar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="Anadir_capa_dialog" style="display: none">
        <div id="Anadir_capa_winbox" style="overflow: hidden">
            <div class="row pl-1 pr-1 pt-1 pb-0 m-0">
                <div class="col-12 pb-2">
                    <label class="mb-0 text-sm">Título</label>
                    <div class="input-group input-group-sm">
                        <input type="text" id="nombreCapa" name="nombreCapa" class="form-control form-control-sm">
                        <input type="hidden" id="nombreCapaGeoserver" name="nombreCapaGeoserver" class="form-control">
                    </div>
                </div>
                <div class="col-12 mb-2">
                    <label class="mb-0 text-sm" for="sidap_file_name">Ruta del archivo (zip o tif)</label>
                    <div class="input-group input-group-sm">
                        <input type="text" id="sidap_file_name" name="sidap_file_name" class="form-control" aria-describedby="basic-addon2" value="" required readonly>
                        <div class="input-group-append">
                            <span class="input-group-text btn btn-primary" id="basic-addon2" style="cursor: pointer" onclick="f_obj.showFiles(['tif','TIF','zip'],'.');"><i class="far fa-folder-open"></i></span>
                        </div>
                    </div>
                </div>
                <div class="col-8 mb-0 pb-0">
                    <label class="mb-0 text-sm">Intervalo temporal</label>
                    <div class="input-group input-group-sm">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="far fa-clock"></i></span>
                        </div>
                        <input type="text" class="form-control form-control-sm float-right" id="intervalo_capa">
                    </div>
                </div>
                <div class="col-9 mb-0 pb-0 mt-2">
                    <div class="input-group input-group-sm pb-0">
                        <button id="anadirCapaProyectoEstadoButton" type="button" class="btn btn-sm btn-block">&nbsp;</button>
                    </div>
                </div>
                <div class="col-3 mb-0 pb-0 mt-2">
                    <div class="input-group input-group-sm pb-0">
                        <button id="anadirCapaProyectoButton" type="button" class="btn btn-sm btn-success btn-block" onclick="window.f_obj.anadirCapa()">Publicar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>




@stop


@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.2.1/themes/default/style.min.css" />
    <link rel="stylesheet" href="{{ asset('css/bootstrap_4_extend.css') }}">
    <link href="https://viglino.github.io/font-gis/css/font-gis.css" rel="stylesheet" />
    <link href="{{ asset('vendor/winbox/dist/css/winbox.min.css') }}" rel="stylesheet" />

    <link rel="stylesheet" href="{{ asset('css/visor.css') }}">

@stop

@section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.2.1/jstree.min.js"></script>
    <script src="{{ asset('js/bootstrap4-toggle.min.js') }}"></script>
    <script src="{{ asset('js/visor.js') }}"></script>
    <script>

        function isNumeric(num){
            return !isNaN(num)
        }

        document.addEventListener("DOMContentLoaded", function(event) {

            $('#nombreCapa').on('input', function() {
                var name = $(this).val();
                var outString = name
                    .replace("Á","A")
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

                if (isNumeric(outString[0]))
                    outString = 'a_' + outString;

                $("#nombreCapaGeoserver").val( outString );
            });

            // Add the following code if you want the name of the file appear on select
            $(".custom-file-input").on("change", function() {
                var fileName = $(this).val().split("\\").pop();
                $(this).siblings(".custom-file-label").addClass("selected").html(fileName);


                var nombreCapa = fileName.split(".")[0];
                $("#nombreCapa").val(nombreCapa);
                var outString = nombreCapa
                    .replace("Á","A")
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

                if (isNumeric(outString[0]))
                    outString = 'a_' + outString;

                $("#nombreCapaGeoserver").val(outString);
            });


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

            $('#intervalo_capa').daterangepicker({
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

            $('#intervalo_segmento').daterangepicker({
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

            $('#intervalo_cantera').daterangepicker({
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

            $('#intervalo_vertedero').daterangepicker({
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
