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
import {Stroke, Style} from 'ol/style';
import Polygon from 'ol/geom/Polygon';
import Draw from 'ol/interaction/Draw';
//import proj4 from 'proj4';
import 'ol-ext/dist/ol-ext.css';
import 'font-gis/css/font-gis.css';


import 'toastr/build/toastr.css';
import toastr from 'toastr';
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
var draw_Proyectos = new Draw({
    type: 'Polygon',
    source: Proyectos_vector_source
});
var coordenadas_nuevo_proyecto = null;
draw_Proyectos.on('drawstart', function (e) {
    coordenadas_nuevo_proyecto = null;
    Proyectos_vector_source.clear();
});
draw_Proyectos.on('drawend', function (e) {
    var currentFeature = e.feature;//this is the feature fired the event
    var feature_coordinates = e.feature.getGeometry().getCoordinates();
    e.feature.setProperties({'id': 0,'estado': 'nuevo'});
    coordenadas_nuevo_proyecto = feature_coordinates;
});


/*
FIN - Proyectos
 */


function cambiaTamanioVentana(){
    var w = $("#map").width();
    var h = parseInt(parseInt(w) * 600 / 800);

    $("#map").css('height',h + 'px');
    map.updateSize();
}

document.addEventListener("DOMContentLoaded", function(event) {
    f_obj.inicio();
});

function msg(tipo,contenido,timeOut,progressBar=true) {

    toastr.options = {
        "closeButton": true,
        "debug": false,
        "newestOnTop": true,
        "progressBar": progressBar,
        "positionClass": "toast-bottom-right",
        "preventDuplicates": true,
        "showDuration": "1000",
        "hideDuration": "1000",
        "timeOut": "" + timeOut + "",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "slideDown",
        "hideMethod": "slideUp",
        "closeMethod": "slideUp",
        "iconClasses": {
            error: 'toast-error',
            info: 'toast-info',
            success: 'toast-success',
            warning: 'toast-warning'
        },
    };

    toastr[""+tipo+""](contenido);
}

var f_obj = {

    guardarProyecto: function() {
        var nombre = $("#nombre").val();
        var coordinates = coordenadas_nuevo_proyecto;

        if (nombre.length === 0) {
            msg("error", 'Error. El campo <b>Nombre</b> es obligatorio.', 5000);
            return;
        }
        if (coordenadas_nuevo_proyecto === null) {
            msg("error", 'Error. Dibuje el área del proyecto en el mapa.', 5000);
            return;
        }

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
            },
            error: function( jqXHR, textStatus, errorThrown ) {
                Dialog_modal.close();
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
                        console.log(id_layer_group," : ",id_layer_group_old); 
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
                            //console.log(data[x]['nombre_grupo']);    
                            if(data[x-1]['id_grupo']!=2)
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
                                leyenda: data[x]['leyenda'],
                                baseLayer: true
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
                                leyenda: data[x]['leyenda'],
                                baseLayer: true
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
                            console.log(data[x]['nombre_grupo']);    
                            //map.addLayer(ol_layer_group);
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


        map.addInteraction(draw_Proyectos);


        setTimeout(function(){

            cambiaTamanioVentana();
        }, 1000);

    },
}
export default f_obj;

window.f_obj = f_obj;


window.map = map;

