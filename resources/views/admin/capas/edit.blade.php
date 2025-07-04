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
<input id="ip_server" name="ip_server" type="hidden" value="{{ env('DB_HOST') }}">

<div class="card">


    <div class="card-body">
        <form action="{{ route("capas.update", [$capa->id]) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="form-row">
                <div class="col-xl-7 col-lg-7 col-md-7 col-sm-12">
                    <div class="form-row">
                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                            <div class="form-group {{ $errors->has('titulo') ? 'has-error' : '' }}">
                                <label for="titulo" class="mb-0">Título</label>
                                <input type="text" id="titulo" name="titulo" class="form-control" value="{{ old('titulo', isset($capa) ? $capa->titulo : '') }}" required>
                            </div>
                        </div>
                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                            <div class="form-group {{ $errors->has('nombre') ? 'has-error' : '' }}">
                                <label for="nombre" class="mb-0">Nombre</label>
                                <input type="text" id="nombre" name="nombre" class="form-control" value="{{ old('name', isset($capa) ? $capa->nombre : '') }}" required readonly>
                            </div>
                        </div>
                        <div class="col-xl-5 col-lg-5 col-md-6 col-sm-12">
                            <div class="form-group {{ $errors->has('id_grupo') ? 'has-error' : '' }}">
                                <label for="id_grupo" class="mb-0">Grupo</label>
                                <select id="id_grupo" name="id_grupo" class="form-control">
                                    @foreach($grupos as $key => $grupo)
                                        @if ($capa->id_grupo === $grupo->id)
                                            <option value="{{ $grupo->id }}" selected>{{ $grupo->titulo }}</option>
                                        @else
                                            <option value="{{ $grupo->id }}">{{ $grupo->titulo }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-xl-2 col-lg-2 col-md-6 col-sm-12">
                            <label for="visible" class="mb-0">Visibilidad</label>
                            <div class="form-group">
                                <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                    <label class="btn btn-secondary @if($capa->visible) active @endif">
                                        <input type="radio" name="init_visible" id="visible_radio_si" autocomplete="off" @if($capa->visible) checked @endif value="true"> Sí
                                    </label>
                                    <label class="btn btn-secondary @if(!$capa->visible) active @endif">
                                        <input type="radio" name="init_visible" id="visible_radio_no" autocomplete="off" @if(!$capa->visible) checked @endif value="false"> No
                                    </label>
                                </div>
                            </div>
                        </div>





                        <div class="col-xl-2 col-lg-6 col-md-6 col-sm-12">
                            <div class="form-group {{ $errors->has('zoom_max') ? 'has-error' : '' }}">
                                <label for="zoom_max" class="mb-0">Zoom máximo</label>
                                <input type="number" id="zoom_max" name="zoom_max" class="form-control" min="0" max="28" step="0.01" value="{{ old('zoom_max', isset($capa) ? $capa->zoom_max : '') }}" required>
                            </div>
                        </div>

                        <div class="col-xl-2 col-lg-6 col-md-6 col-sm-12">
                            <div class="form-group {{ $errors->has('zoom_min') ? 'has-error' : '' }}">
                                <label for="zoom_min" class="mb-0">Zoom mínimo</label>
                                <input type="number" id="zoom_min" name="zoom_min" class="form-control" min="0" max="28" step="0.01" value="{{ old('zoom_min', isset($capa) ? $capa->zoom_min : '') }}" required>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="row">
                                <div class="col-xl-5 col-lg-5 col-12">

                                    <div class="form-group">
                                        <label for="estilo" class="mb-0">Estilo*</label>
                                        <select id="estilo" name="estilo" class="form-control">
                                            @foreach($estilos as $key => $estilo)
                                                @if ($capa->estilo == $estilo->nombre)
                                                    <option value="{{ $estilo->nombre }}" selected>{{ $estilo->titulo }}</option>
                                                @else
                                                    <option value="{{ $estilo->nombre }}">{{ $estilo->titulo }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="table-responsive">
                                        <table class="alias-table table table-bordered table-striped table-hover" width="100%" style="width: 100% !important;">
                                            <thead>
                                            <tr class="text-center">
                                                <th class="p-1 text-white" style="background-color: #001f3f">Leyenda</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr>
                                                <td>
                                                    <div id="legend_img">
                                                    </div>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                </div>
                                <div class="col-xl-7 col-lg-7 col-12">

                                        <div class="form-group">
                                            <label class="mb-0">Descripción</label>
                                            <textarea class="form-control" id="descripcion" name="descripcion">{{ old('descripcion', isset($capa) ? $capa->descripcion : '') }}</textarea>
                                        </div>

                                </div>
                            </div>
                        </div>



                    </div>
                </div>
                <div class="col-xl-5 col-lg-5 col-md-5 col-sm-12">
                    <div id="map" style="width: 100%;height: 500px;border: solid;border-color: #001f3f;background-color: #001f3f"></div>
                    <div class="form-row mt-2">
                        <div class="col-xl-12">Zoom actual
                            <input class="form-control-sm" id="resolucion_actual" type="number" step="0.01" value="" readonly>
                        </div>
                    </div>
                </div>
            </div>






            <input type="hidden" id="posicion" name="posicion" value="{{ old('posicion', isset($capa) ? $capa->posicion : 0) }}">

            <div>
                <input class="btn btn-danger" type="submit" value="Guardar">
                <a class="btn btn-primary ml-2" type="button" href="{{ route('capas.index') }}">Volver</a>
            </div>



        </form>


    </div>
</div>
@stop

@section('css')
    <link rel="stylesheet" href="{{ asset('css/ol.css') }}">
    <link rel="stylesheet" href="{{ asset('css/ol-ext/ol-ext.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin_custom.css') }}">
<link rel="stylesheet" href="{{ asset('css/bootstrap_4_extend.css') }}">
@stop

@section('js')
    <script src="{{ asset('js/ol.js') }}"></script>
    <script src="{{ asset('js/proj4.js') }}"></script>
    <script src="{{ asset('js/ol-ext/ol-ext.min.js') }}"></script>
    <script type="text/javascript">
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        var ip_server = $("#ip_server").val();
        var url_proxy = 'http://'+ip_server+'/proxy.php?url=';

        proj4.defs("EPSG:25830","+proj=utm +zone=30 +ellps=GRS80 +units=m +no_defs");
        ol.proj.proj4.register(proj4);

        var EPSG_25830 = new ol.proj.Projection({
            code: 'EPSG:25830',
            extent: [-729785.76, 3715125.82, 945351.10, 9522561.39]
        });

        var map;
        var ol_layer;

        // A group layer for base layers
        var baseLayers = new ol.layer.Group(
            {   id: 'baseLayers',
                title: 'Mapas Base',
                openInLayerSwitcher: false,
                noSwitcherDelete: true,
                layers:
                    [
                        new ol.layer.Tile(
                            {	id: 116,
                                title: 'OpenStreetMap',
                                source: new ol.source.OSM({
                                    projection: 'EPSG:4326'
                                }),
                                visible: true
                            })
                    ]
            });

        $('#estilo').on('change', function() {
            var estilo = $(this).val();
            var ip_server = $("#ip_server").val();
            // Leyenda del estilo
            var url_legend = 'http://' + ip_server + ':8080/geoserver/wms?REQUEST=GetLegendGraphic&VERSION=1.0.0&FORMAT=image/png&WIDTH=28&HEIGHT=28&STRICT=false&legend_options=fontName:Roboto%20Serif%2020pt%20Regular;fontAntiAliasing:true;fontColor:0x000033;fontSize:12;bgColor:0xFFFFEE;dpi:100;forceLabels:on&style=pgis:' + estilo;
            $("#legend_img").html('<img src="' + url_legend + '">');

            recargarCapa();
        });





        function onMoveEnd() {
            //var resolution = map.getView().getResolution();
            //resolution = Math.round((resolution + Number.EPSILON) * 1000) / 1000;
            var zoom = map.getView().getZoom();
            zoom = Math.round((zoom + Number.EPSILON) * 100) / 100;
            $("#resolucion_actual").val(zoom);
        }


        function xmlToJson( xml ) {

            // Create the return object
            var obj = {};

            if ( xml.nodeType == 1 ) { // element
                // do attributes
                if ( xml.attributes.length > 0 ) {
                    obj["@attributes"] = {};
                    for ( var j = 0; j < xml.attributes.length; j++ ) {
                        var attribute = xml.attributes.item( j );
                        obj["@attributes"][attribute.nodeName] = attribute.nodeValue;
                    }
                }
            } else if ( xml.nodeType == 3 ) { // text
                obj = xml.nodeValue;
            }

            // do children
            if ( xml.hasChildNodes() ) {
                for( var i = 0; i < xml.childNodes.length; i++ ) {
                    var item = xml.childNodes.item(i);
                    var nodeName = item.nodeName;
                    if ( typeof(obj[nodeName] ) == "undefined" ) {
                        obj[nodeName] = xmlToJson( item );
                    } else {
                        if ( typeof( obj[nodeName].push ) == "undefined" ) {
                            var old = obj[nodeName];
                            obj[nodeName] = [];
                            obj[nodeName].push( old );
                        }
                        obj[nodeName].push( xmlToJson( item ) );
                    }
                }
            }
            return obj;
        };


        function cambiaGrupoEstilo() {
            var id_grupo_estilo = $("#id_grupo_estilo").val();

            $.ajax({
                url:  "/obtenerEstilos",
                type: 'POST',
                data: {_token: CSRF_TOKEN, id_grupo_estilo:id_grupo_estilo},
                dataType: 'JSON',
                success: function (data) {
                    //console.log(data);
                    var str_option = '';
                    var gsn = '';
                    for (var x=0;x<data.length;x++) {
                        gsn = data[0]['geoserver_name'];
                        str_option += '<option value="' + data[x]['geoserver_name'] + '">' + data[x]['name'] + '</option>';
                    }
                    $("#geoserver_style").html(str_option);


                    var ip_server = $("#ip_server").val();
                    // Leyenda del estilo
                    var url_legend = 'http://' + ip_server + ':8080/geoserver/wms?REQUEST=GetLegendGraphic&VERSION=1.0.0&FORMAT=image/png&WIDTH=28&HEIGHT=28&STRICT=false&legend_options=fontName:Roboto%20Serif%2020pt%20Regular;fontAntiAliasing:true;fontColor:0x000033;fontSize:12;bgColor:0xFFFFEE;dpi:100;forceLabels:on&style=sidap:' + gsn;
                    $("#legend_img").html('<img src="' + url_legend + '">');


                }
            });
        }

        function recargarCapa() {
            var estilo = $("#estilo").val();
            var ip_server = $("#ip_server").val();
            var nombre = $("#nombre").val();
            var titulo = $("#titulo").val();
            var zoom_min = parseFloat($("#zoom_min").val());
            var zoom_max = parseFloat($("#zoom_max").val());
            var visible = $("input[name='visible']:checked").val();

            map.removeLayer(ol_layer);

            ol_layer = new ol.layer.Tile(
                {
                    id: nombre,
                    title: titulo,
                    name: nombre,
                    source: new ol.source.TileWMS({
                        url: url_proxy + 'http://' + ip_server + ':8080/geoserver/pgis/wms?',
                        params: {
                            LAYERS: 'pgis:' + nombre,
                            VERSION: '1.1.1', TILED: true,
                            STYLES: 'pgis:' + estilo,
                        },
                        projection: 'EPSG:4326',
                        serverType: 'geoserver'
                    }),
                    minZoom: zoom_min,
                    maxZoom: zoom_max,
                    visible: visible
                });

            map.addLayer(ol_layer);
        }

        $(document).ready(function(){

            $('.select2').select2();
            $('[data-toggle="tooltip"]').tooltip();

            var ip_server = $("#ip_server").val();
            var estilo = $("#estilo").val();
            // Leyenda del estilo
            var url_legend = 'http://' + ip_server + ':8080/geoserver/wms?REQUEST=GetLegendGraphic&VERSION=1.0.0&FORMAT=image/png&WIDTH=28&HEIGHT=28&STRICT=false&legend_options=fontName:Roboto%20Serif%2020pt%20Regular;fontAntiAliasing:true;fontColor:0x000033;fontSize:12;bgColor:0xFFFFEE;dpi:100;forceLabels:on&style=pgis:' + estilo;
            $("#legend_img").html('<img src="' + url_legend + '">');

            var nombre = $("#nombre").val();
            var titulo = $("#titulo").val();
            var zoom_min = parseFloat($("#zoom_min").val());
            var zoom_max = parseFloat($("#zoom_max").val());
            var visible = $("input[name='visible']:checked").val();

            ol_layer = new ol.layer.Tile(
                {
                    id: nombre,
                    title: titulo,
                    name: nombre,
                    source: new ol.source.TileWMS({
                        url: url_proxy + 'http://' + ip_server + ':8080/geoserver/pgis/wms?',
                        params: {
                            LAYERS: 'pgis:' + nombre,
                            VERSION: '1.1.1', TILED: true,
                            STYLES: 'pgis:' + estilo,
                        },
                        projection: 'EPSG:4326',
                        serverType: 'geoserver'
                    }),
                    minZoom: zoom_min,
                    maxZoom: zoom_max,
                    visible: visible
                });



            map = new ol.Map({
                target: 'map',
                layers: [
                    baseLayers, ol_layer
                ],
                view: new ol.View({
                    center: ol.proj.transform([-3.627411,40.007395], 'EPSG:4326', 'EPSG:4326'),
                    zoom: 7,
                    projection: 'EPSG:4326',
                    minZoom: 3
                })
            });

            // LayerSwitcher
            var layerControl = new ol.control.LayerSwitcher({
                extent: false,
                show_progress: true,
                trash: true
            });
            map.addControl(layerControl);
            layerControl.on('toggle', function(e) {
                //console.log('Collapse layerswitcher', e.collapsed);
            });


            // Zoom a la extensión de la capa
            var base_url = url_proxy + 'http://'+ip_server+':8080/geoserver/pgis/wms?service=wms&request=GetCapabilities';
            var xmlHttp = new XMLHttpRequest();
            xmlHttp.open( "GET", base_url, false ); // false for synchronous request
            xmlHttp.send( null );
            var json_capabilities = xmlToJson( ($.parseXML(xmlHttp.responseText)) );
            //console.log(json_capabilities);
            //console.log(json_capabilities.WMS_Capabilities.Capability.Layer.Layer);
            for(var x=0;x<json_capabilities.WMS_Capabilities.Capability.Layer.Layer.length;x++) {
                var id = json_capabilities.WMS_Capabilities.Capability.Layer.Layer[x].Title['#text'];
                if (id == nombre) {
                    var boundingBox = json_capabilities.WMS_Capabilities.Capability.Layer.Layer[x].BoundingBox[1]['@attributes'];
                    //var bbox = [boundingBox['minx'],boundingBox['miny'],boundingBox['maxx'],boundingBox['maxy']];

                    //console.log(boundingBox);


                    var a = ol.proj.transform([parseFloat(boundingBox['minx']),parseFloat(boundingBox['miny'])],'EPSG:25830','EPSG:4326');
                    var b = ol.proj.transform([parseFloat(boundingBox['maxx']),parseFloat(boundingBox['maxy'])],'EPSG:25830','EPSG:4326');
                    var bbox = [a[0],a[1],b[0],b[1]];

                    var view = map.getView();
                    view.fit(bbox, map.getSize());



                }
            }













            map.on('moveend', onMoveEnd);
            onMoveEnd();


        });


    </script>
@stop

