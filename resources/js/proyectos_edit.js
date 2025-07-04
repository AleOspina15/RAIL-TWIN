import 'ol/ol.css';
import {Map, View} from 'ol';
import TileLayer from 'ol/layer/Tile';
import Group from 'ol/layer/Group';
import BingMaps from 'ol/source/BingMaps';
import TileWMS from 'ol/source/TileWMS';
import * as olProj from 'ol/proj.js';
import {get as getProjection} from 'ol/proj.js';
import VectorLayer from 'ol/layer/Vector';
import VectorSource from 'ol/source/Vector';
import {Fill, Stroke, Style, Text} from 'ol/style';
import Feature from 'ol/Feature';
import Point from 'ol/geom/Point';
import Polygon from 'ol/geom/Polygon';
import LineString from 'ol/geom/LineString';
//import proj4 from 'proj4';
//import 'ol-ext/dist/ol-ext.css';
import 'font-gis/css/font-gis.css';
import ModifyFeature from 'ol-ext/interaction/ModifyFeature';

import 'toastr/build/toastr.css';
import 'winbox/dist/css/winbox.min.css';
/*

import 'toastr/build/toastr.css';
import toastr from 'toastr';
*/
import WMTS from 'ol/source/WMTS.js';
import WMTSTileGrid from 'ol/tilegrid/WMTS.js';
import {getTopLeft, getWidth} from 'ol/extent.js';

import TileState from 'ol/TileState.js';
import TileGrid from 'ol/tilegrid/TileGrid.js';

import Swal from 'sweetalert2/dist/sweetalert2.js';
import 'sweetalert2/src/sweetalert2.scss';


var CSRF_TOKEN = document.querySelector('meta[name=csrf-token]').content;

var ip_server = $("#ip_server").val();
var map;
var url_proxy = 'http://' + ip_server + '/proxy.php?url=';
var title_max_width;
var Dialog_modal;
var gs_url = $("#gs_url").val() + '/';

var timeline_layers = [];
var tline = null;


var tileSize = 512;
const projection = getProjection('EPSG:3857');
const projectionExtent = projection.getExtent();
const size = getWidth(projectionExtent) / tileSize;
const resolutions = new Array(25);
const matrixIds = new Array(25);

for (let z = 0; z < 25; ++z) {
    // generate resolutions and matrixIds arrays for this WMTS
    resolutions[z] = size / Math.pow(2, z);
    matrixIds[z] = z;
}
var tileGrid = new WMTSTileGrid({
    origin: getTopLeft(projectionExtent),
    resolutions: resolutions,
    matrixIds: matrixIds,
    tileSize: [tileSize, tileSize]
})

var tileGrid512 = new TileGrid({
    origin: getTopLeft(projectionExtent),
    resolutions: resolutions,
    tileSize: [tileSize, tileSize]
});



/*
Proyectos
 */

var Proyectos_estilos = {
    "nuevo":new Style({
        stroke: new Stroke({
            color: '#0000ff',
            width: 3
        })
    }),
    "activo":new Style({
        stroke: new Stroke({
            color: '#00ff00',
            width: 3
        })
    }),
    "terminado":new Style({
        stroke: new Stroke({
            color: '#ff0000',
            width: 3
        })
    }),
};
var Proyectos_vector_source = new VectorSource();
var Proyectos_vector_layer = new VectorLayer({
    title: 'Proyectos',
    name: 'Proyectos',
    //inicio: '2015-01-01',
    //fin: '2020-01-01',
    source: Proyectos_vector_source,
    style: function(feature,resolution) {
        var estado = feature.get('estado');
        return [Proyectos_estilos[estado]];
    },
    displayInLayerSwitcher: false
});
var draw_Proyectos = new ModifyFeature({
    sources: Proyectos_vector_source
});
var coordenadas_nuevo_proyecto = null;
draw_Proyectos.on('modifystart', function (e) {
    //coordenadas_nuevo_proyecto = null;
    //Proyectos_vector_source.clear();

});
draw_Proyectos.on('modifyend', function (e) {
    var currentFeature = e.features[0];//this is the feature fired the event
    var feature_coordinates = e.features[0].getGeometry().getCoordinates();
    e.features[0].setProperties({'id': 0,'estado': 'nuevo'});
    coordenadas_nuevo_proyecto = feature_coordinates;
});


/*
FIN - Proyectos
 */


function renderizandoMapa(esta_renderizando) {



}

var EstadoGenerarCache_interval;
var GenerandoCache = false;


function obtenerEstadoGenerarCache() {
    $.ajax({
        url: '/obtenerEstadoGenerarCache',
        type: 'POST',
        data: {_token: CSRF_TOKEN},
        dataType: 'JSON',
        success: function (data_arr) {
            //console.log(data_arr);

            // {"long-array-array":[[320,1840,48,38,1]]}
            // {"long-array-array":[]}
            var p0 = '{"long-array-array":[]}';
            var p1,p2,p3,p4;
            p1 = p0;
            p2 = p0;
            p3 = p0;
            p4 = p0;

            if (data_arr[0].length > 0 && data_arr[1].length > 0 && data_arr[2].length > 0 && data_arr[3].length > 0 && data_arr[4].length > 0) {
                p0 = data_arr[0][0];
                p1 = data_arr[1][0];
                p2 = data_arr[2][0];
                p3 = data_arr[3][0];
                p4 = data_arr[4][0];
            }


            /*
                  console.log(p0);
                  console.log(p1);
                  console.log(p2);
                  console.log(p3);
            */




            if (p0 === '{"long-array-array":[]}' && p0 === p1 && p1 === p2 && p2 === p3 && p3 === p4) {
                GenerandoCache = false;
                $("#infoCache").html('Generar caché');
            }
            else {
                GenerandoCache = true;

                var p0_completed = 0;
                var p0_total = 0;
                var p1_completed = 0;
                var p1_total = 0;
                var p2_completed = 0;
                var p2_total = 0;
                var p3_completed = 0;
                var p3_total = 0;
                var p4_completed = 0;
                var p4_total = 0;

                if (p0 != '{"long-array-array":[]}') {
                    var p0_str = p0.split('{"long-array-array":[[')[1].split(']]}')[0];
                    var p0_arr = p0_str.split(',');
                    p0_completed = parseInt(p0_arr[0]);
                    p0_total = parseInt(p0_arr[1]);
                }
                if (p1 != '{"long-array-array":[]}') {
                    var p1_str = p1.split('{"long-array-array":[[')[1].split(']]}')[0];
                    var p1_arr = p1_str.split(',');
                    p1_completed = parseInt(p1_arr[0]);
                    p1_total = parseInt(p1_arr[1]);
                }
                if (p2 != '{"long-array-array":[]}') {
                    var p2_str = p2.split('{"long-array-array":[[')[1].split(']]}')[0];
                    var p2_arr = p2_str.split(',');
                    p2_completed = parseInt(p2_arr[0]);
                    p2_total = parseInt(p2_arr[1]);
                }
                if (p3 != '{"long-array-array":[]}') {
                    var p3_str = p3.split('{"long-array-array":[[')[1].split(']]}')[0];
                    var p3_arr = p3_str.split(',');
                    p3_completed = parseInt(p3_arr[0]);
                    p3_total = parseInt(p3_arr[1]);
                }
                if (p4 != '{"long-array-array":[]}') {
                    var p4_str = p4.split('{"long-array-array":[[')[1].split(']]}')[0];
                    var p4_arr = p4_str.split(',');
                    p4_completed = parseInt(p4_arr[0]);
                    p4_total = parseInt(p4_arr[1]);
                }

                var p_completed = p0_completed + p1_completed + p2_completed + p3_completed + p4_completed;
                var p_total = p0_total + p1_total + p2_total + p3_total + p4_total;
                var porcentaje = Math.round(p_completed*100/p_total);

                $("#infoCache").html('<i class="fa-solid fa-sync fa-spin mr-1"></i> Generando caché ' + porcentaje + '%');

            }


        }
    });
}


function cambiaTamanioVentana(){
    var w = $("#map").width();
    var h = parseInt(parseInt(w) * 600 / 800);

    $("#map").css('height',h + 'px');
    map.updateSize();
}

document.addEventListener("DOMContentLoaded", function(event) {
    f_obj.inicio();
});









var f_obj = {

    generarCache: function () {
        if (GenerandoCache)
            return;

        GenerandoCache = true;

        $.ajax({
            url: '/generarCache',
            type: 'POST',
            data: {_token: CSRF_TOKEN, id_proyecto: $("#id_proyecto").val() },
            dataType: 'JSON',
            success: function (data_arr) {
            }
        });
    },

    guardarProyecto: function() {
        var nombre = $("#nombre").val();
        var coordinates = coordenadas_nuevo_proyecto;

        var duracion = $("#duracion_proyecto").val();
        var inicio = moment(duracion.split(' - ')[0],'DD/MM/YYYY').format('YYYY-MM-DD');
        var fin = moment(duracion.split(' - ')[1],'DD/MM/YYYY').format('YYYY-MM-DD');

        var html = '<p class="text-lg text-center"><i class="fa-solid fa-circle-notch fa-spin mr-2"></i> Guardando proyecto...</p>';
        Dialog_modal = Swal.fire({
            html: html,
            allowOutsideClick: false,
            showConfirmButton: false,
            width: 350,
            padding: '0px'
        })

        $.ajax({
            url: '/guardarProyecto',
            type: 'POST',
            data: {
                _token: CSRF_TOKEN,
                coordinates:coordinates,
                nombre:nombre,
                inicio:inicio,
                fin:fin
            },
            dataType: 'JSON',
            success: function (data) {
                //console.log(data);

                Dialog_modal.close();
                window.location = "../proyectos";
            }
        });
    },

    actualizarProyecto: function() {
        var nombre = $("#nombre").val();
        var coordinates = coordenadas_nuevo_proyecto;

        var duracion = $("#duracion_proyecto").val();
        var inicio = moment(duracion.split(' - ')[0],'DD/MM/YYYY').format('YYYY-MM-DD');
        var fin = moment(duracion.split(' - ')[1],'DD/MM/YYYY').format('YYYY-MM-DD');

        var html = '<p class="text-lg text-center"><i class="fa-solid fa-circle-notch fa-spin mr-2"></i> Actualizando proyecto...</p>';
        Dialog_modal = Swal.fire({
            html: html,
            allowOutsideClick: false,
            showConfirmButton: false,
            width: 350,
            padding: '0px'
        })

        $.ajax({
            url: '/actualizarProyecto',
            type: 'POST',
            data: {
                _token: CSRF_TOKEN,
                coordinates:coordinates,
                nombre:nombre,
                inicio:inicio,
                fin:fin,
                id_proyecto: $("#id_proyecto").val()
            },
            dataType: 'JSON',
            success: function (data) {
                //console.log(data);

                Dialog_modal.close();
                window.location = "../proyectos";
            }
        });
    },



    obtenerCapas: function() {
        //Cargar grupos de capas del usuario
        $.ajax({
            url: '/obtenerCapas',
            type: 'POST',
            data: {_token: CSRF_TOKEN},
            dataType: 'JSON',
            success: function (data_arr) {
                //console.log(data_arr);

                var data = data_arr[0];

                //title_max_width = 0;
                if (data.length > 0) {
                    var id_layer_group = data[0]['id_grupo'];
                    var id_layer_group_old = data[0]['id_grupo'];
                    var id_layer = 0;
                    var ol_layer_group = null;
                    var ol_layer = null;
                    var ol_array = [];
                    for (var x = 0; x < data.length; x++) {

                        id_layer_group = data[x]['id_grupo'];

                        if (id_layer_group != id_layer_group_old) { //nuevo grupo de capas

                            ol_layer_group = new Group(
                                {
                                    id: data[x - 1]['nombre_grupo'],
                                    title: data[x - 1]['nombre_grupo'],
                                    openInLayerSwitcher: data[x - 1]['openinlayerswitcher'],
                                    layers: ol_array,
                                    noSwitcherDelete: true,
                                    minZoom: data[x - 1]['min_zoom_grupo'],
                                    maxZoom: data[x - 1]['max_zoom_grupo'],
                                    visible: data[x - 1]['visible_grupo'],
                                    esGrupo: true
                                });

                            map.addLayer(ol_layer_group);
                            ol_array = [];

                            // Longitud título
                            var title_aux = data[x - 1]['nombre_grupo'];
                            var width_aux = title_aux.length;
                            if (width_aux > title_max_width)
                                title_max_width = width_aux;
                            // FIN - Longitud título

                        }

                        var tipo = data[x]['tipo'];

                        if (tipo === 'wmts') {
                            ol_layer = new TileLayer({
                                id: data[x]['name'],
                                title: data[x]['titulo'],
                                sistema: data[x]['capa_sistema'],
                                descargable: data[x]['descargable'],
                                tipo: data[x]['tipo'],
                                workspace: data[x]['workspace'],
                                opacity: 1,
                                source: new WMTS({
                                    attributions: '',
                                    url: url_proxy + 'https://' + ip_server + '/geoserver/gwc/service/wmts',
                                    layer: data[x]['workspace'] + ':' + data[x]['name'],
                                    matrixSet: 'WebMercatorQuad',
                                    format: 'image/png',
                                    projection: projection,
                                    tileGrid: new WMTSTileGrid({
                                        origin: getTopLeft(projectionExtent),
                                        resolutions: resolutions,
                                        matrixIds: matrixIds,
                                    }),
                                    //style: 'default',
                                    wrapX: true,
                                }),
                                minZoom: data[x]['min_zoom_capa'],
                                maxZoom: data[x]['max_zoom_capa'],
                                visible: true, // data[x]['visible_capa'],
                                noSwitcherDelete: true,
                                displayInLayerSwitcher: data[x]['displayinlayerswitcher'],
                                esGrupo: false,
                                leyenda: data[x]['leyenda']
                            });


                        }

                        if (tipo === 'wms') {
                            ol_layer = new TileLayer({
                                id: data[x]['name'],
                                title: data[x]['titulo'],
                                sistema: data[x]['capa_sistema'],
                                descargable: data[x]['descargable'],
                                tipo: data[x]['tipo'],
                                workspace: data[x]['workspace'],
                                inicio: data[x]['inicio'],
                                fin: data[x]['fin'],
                                opacity: 1,
                                source: new TileWMS({
                                    ratio: 1,
                                    url: url_proxy + gs_url + data[x]['workspace'] + '/wms',
                                    params: {
                                        LAYERS: data[x]['name'],
                                        VERSION: '1.1.1',
                                        TILED: true
                                    },
                                    projection: projection,
                                    //tileLoadFunction: window.f_obj.tileLoadFunction,
                                    tileGrid: tileGrid512,
                                    transition: 0
                                }),
                                minZoom: data[x]['min_zoom_capa'],
                                maxZoom: data[x]['max_zoom_capa'],
                                visible: true, // data[x]['visible_capa'],
                                noSwitcherDelete: data[x]['noswitcherdelete'],
                                displayInLayerSwitcher: data[x]['displayinlayerswitcher'],
                                esGrupo: false,
                                leyenda: data[x]['leyenda']
                            });
                        }

                        if (tipo === 'BingMaps') {
                            ol_layer = new TileLayer({
                                id: data[x]['name'],
                                title: data[x]['titulo'],
                                sistema: data[x]['capa_sistema'],
                                descargable: data[x]['descargable'],
                                tipo: data[x]['tipo'],
                                workspace: data[x]['workspace'],
                                inicio: data[x]['inicio'],
                                fin: data[x]['fin'],
                                preload: Infinity,
                                source: new BingMaps({
                                    key: 'Amohz-23imdDcRGnxhgEfu9C1kg57eAX9WXvrttEKBHO8myREslXeG_QfxHY8GRT',
                                    imagerySet: 'AerialWithLabelsOnDemand',
                                    maxZoom: 19,
                                    // use maxZoom 19 to see stretched tiles instead of the BingMaps
                                    // "no photos at this zoom level" tiles
                                    // maxZoom: 19
                                }),
                                minZoom: data[x]['min_zoom_capa'],
                                maxZoom: data[x]['max_zoom_capa'],
                                visible: true, // data[x]['visible_capa'],
                                noSwitcherDelete: data[x]['noswitcherdelete'],
                                displayInLayerSwitcher: data[x]['displayinlayerswitcher'],
                                esGrupo: false,
                                leyenda: data[x]['leyenda']
                            });
                        }

                        ol_array.push(ol_layer);
                        timeline_layers.push(ol_layer);




                        id_layer_group_old = id_layer_group;

                        // Longitud título
                        var title_aux = data[x]['titulo'];
                        var width_aux = title_aux.length;
                        if (width_aux > title_max_width)
                            title_max_width = width_aux;
                        // FIN - Longitud título

                        if (x === data.length - 1) {

                            ol_layer_group = new Group(
                                {
                                    id: data[x]['nombre_grupo'],
                                    title: data[x]['nombre_grupo'],
                                    openInLayerSwitcher: data[x]['openinlayerswitcher'],
                                    layers: ol_array,
                                    noSwitcherDelete: true,
                                    minZoom: data[x]['min_zoom_grupo'],
                                    maxZoom: data[x]['max_zoom_grupo'],
                                    visible: data[x]['visible_grupo'],
                                    esGrupo: true
                                });

                            map.addLayer(ol_layer_group);
                            ol_array = [];

                            // Longitud título
                            var title_aux = data[x]['nombre_grupo'];
                            var width_aux = title_aux.length;
                            if (width_aux > title_max_width)
                                title_max_width = width_aux;
                            // FIN - Longitud título

                        }

                    }




                    //console.log(title_max_width);

                }



                map.addLayer(Proyectos_vector_layer);

            }
        });
    },

    cargarProyecto: function() {
        var id_proyecto = $("#id_proyecto").val();


        $.ajax({
            url: '/cargarProyecto',
            type: 'POST',
            data: {_token: CSRF_TOKEN, id_proyecto:id_proyecto},
            dataType: 'JSON',
            success: function (data_arr) {
                //console.log(data_arr);

                timeline_layers = [];

                // Capas del proyecto
                var data = data_arr[0];
                title_max_width = 0;
                if (data.length > 0) {
                    var id_layer_group = data[0]['id_grupo'];
                    var id_layer_group_old = data[0]['id_grupo'];
                    var id_layer = 0;
                    var ol_layer_group = null;
                    var ol_layer = null;
                    var ol_array = [];
                    for (var x = 0; x < data.length; x++) {
                        id_layer_group = data[x]['id_grupo'];
                        if (id_layer_group != id_layer_group_old) { //nuevo grupo de capas
                            ol_layer_group = new Group(
                                {
                                    id: data[x - 1]['nombre_grupo'],
                                    title: data[x - 1]['nombre_grupo'],
                                    openInLayerSwitcher: data[x - 1]['openinlayerswitcher'],
                                    layers: ol_array,
                                    noSwitcherDelete: true,
                                    minZoom: data[x - 1]['min_zoom_grupo'],
                                    maxZoom: data[x - 1]['max_zoom_grupo'],
                                    visible: data[x - 1]['visible_grupo']
                                });

                            map.addLayer(ol_layer_group);
                            ol_array = [];

                            // Longitud título
                            var title_aux = data[x - 1]['nombre_grupo'];
                            var width_aux = title_aux.length;
                            if (width_aux > title_max_width)
                                title_max_width = width_aux;
                            // FIN - Longitud título
                        }

                        var tipo = data[x]['tipo'];

                        if (tipo === 'wmts') {
                            ol_layer = new TileLayer({
                                id: data[x]['name'],
                                title: data[x]['titulo'],
                                sistema: data[x]['capa_sistema'],
                                descargable: data[x]['descargable'],
                                tipo: data[x]['tipo'],
                                inicio: data[x]['inicio'],
                                fin: data[x]['fin'],
                                opacity: 1,
                                source: new WMTS({
                                    attributions: '',
                                    url: url_proxy + 'https://' + ip_server + '/geoserver/gwc/service/wmts',
                                    layer: data[x]['workspace'] + ':' + data[x]['name'],
                                    matrixSet: 'WebMercatorQuad',
                                    format: 'image/png',
                                    projection: projection,
                                    tileGrid: new WMTSTileGrid({
                                        origin: getTopLeft(projectionExtent),
                                        resolutions: resolutions,
                                        matrixIds: matrixIds,
                                    }),
                                    //style: 'default',
                                    wrapX: true,
                                }),
                                minZoom: data[x]['min_zoom_capa'],
                                maxZoom: data[x]['max_zoom_capa'],
                                visible: data[x]['visible_capa'],
                                noSwitcherDelete: data[x]['noswitcherdelete'],
                                displayInLayerSwitcher: data[x]['displayinlayerswitcher']
                            });

                            ol_array.push(ol_layer);
                            if (data[x]['tline'])
                                timeline_layers.push(ol_layer);

                        }
                        else
                        if (tipo === 'wms') {
                            ol_layer = new TileLayer({
                                id: data[x]['name'],
                                title: data[x]['titulo'],
                                sistema: data[x]['capa_sistema'],
                                descargable: data[x]['descargable'],
                                tipo: data[x]['tipo'],
                                inicio: data[x]['inicio'],
                                fin: data[x]['fin'],
                                opacity: 1,
                                source: new TileWMS({
                                    ratio: 1,
                                    url: url_proxy + 'https://' + ip_server + '/geoserver/optimhc/wms',
                                    params: {
                                        LAYERS: data[x]['workspace'] + ':' + data[x]['name'],
                                        VERSION: '1.1.1', 'TILED': true
                                    },
                                    projection: projection
                                }),
                                minZoom: data[x]['min_zoom_capa'],
                                maxZoom: data[x]['max_zoom_capa'],
                                visible: data[x]['visible_capa'],
                                noSwitcherDelete: data[x]['noswitcherdelete'],
                                displayInLayerSwitcher: data[x]['displayinlayerswitcher']
                            });

                            ol_array.push(ol_layer);
                            if (data[x]['tline'])
                                timeline_layers.push(ol_layer);
                        }
                        else {

                        }

                        id_layer_group_old = id_layer_group;

                        // Longitud título
                        var title_aux = data[x]['titulo'];
                        var width_aux = title_aux.length;
                        if (width_aux > title_max_width)
                            title_max_width = width_aux;
                        // FIN - Longitud título

                        if (x === data.length - 1) {



                            ol_layer_group = new Group(
                                {
                                    id: data[x]['nombre_grupo'],
                                    title: data[x]['nombre_grupo'],
                                    openInLayerSwitcher: data[x]['openinlayerswitcher'],
                                    layers: ol_array,
                                    noSwitcherDelete: true,
                                    minZoom: data[x]['min_zoom_grupo'],
                                    maxZoom: data[x]['max_zoom_grupo'],
                                    visible: data[x]['visible_grupo']
                                });

                            map.addLayer(ol_layer_group);
                            ol_array = [];

                            // Longitud título
                            var title_aux = data[x]['nombre_grupo'];
                            var width_aux = title_aux.length;
                            if (width_aux > title_max_width)
                                title_max_width = width_aux;
                            // FIN - Longitud título
                        }
                    }
                }











            }
        });
    },



    inicio: function() {

        window.addEventListener("resize", cambiaTamanioVentana);

        var center = olProj.transform([-3.627411, 40.007395], 'EPSG:4326', 'EPSG:3857');
        //var center = olProj.transform([-1.8631781, 38.9933465], 'EPSG:4326', 'EPSG:3857');

        var title = 'Callejero IGN para Orto';
        title_max_width = title.length;


        map = new Map({
            target: 'map',
            layers: [],
            overlays: [],
            controls: [],
            view: new View({
                projection: projection,
                center: center,
                zoom: 6.5,
                //minResolution: 0.5971642834779395,
                minResolution: 0.07464553543474244,
                maxResolution: 156543.03392804097
            })
        });

        f_obj.obtenerCapas();


        /*
        let LayerSwitcher_ctrl = new LayerSwitcher({
            collapsed: false,
            show_progress: true,
            trash: false
        });
        map.addControl(LayerSwitcher_ctrl);
        */

        map.addInteraction(draw_Proyectos);


        setTimeout(function(){
            cambiaTamanioVentana();

            var coord = $("#coord").val();
            var coord_elem = coord.split('((')[1];
            coord_elem = coord_elem.split('))')[0];

            var c_arr = coord_elem.split(',');
            var ring_arr = [];
            for (var y = 0; y < c_arr.length; y++) {
                var coord_x = c_arr[y].split(' ')[0];
                var coord_y = c_arr[y].split(' ')[1];
                ring_arr.push([parseFloat(coord_x), parseFloat(coord_y)]);
            }

            var polygon_feature = new Feature({});
            polygon_feature.setId(1);
            var polygon_geom = new Polygon([ring_arr]);
            polygon_feature.setGeometry(polygon_geom);
            var id = parseInt(1);
            var estado = 'nuevo';
            var nombre = '';
            polygon_feature.setProperties({'id': id, 'estado': estado, 'nombre': nombre});
            Proyectos_vector_source.addFeature(polygon_feature);
            var extent = Proyectos_vector_source.getExtent();
            var view = map.getView();
            view.fit(extent);

            coordenadas_nuevo_proyecto = polygon_feature.getGeometry().getCoordinates();

            //obtenerEstadoGenerarCache();
            //EstadoGenerarCache_interval = setInterval(obtenerEstadoGenerarCache, 5000);

            f_obj.cargarProyecto();

        }, 1000);


        map.on('precompose', () =>
            renderizandoMapa(true)
        );
        map.on('rendercomplete', () =>
            renderizandoMapa(false)
        );


    },
}
export default f_obj;

window.f_obj = f_obj;


window.map = map;

