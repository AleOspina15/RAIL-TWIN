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
import {Circle, Fill, Stroke, Style} from 'ol/style';
import Feature from 'ol/Feature';
import Polygon from 'ol/geom/Polygon';
import LineString from 'ol/geom/LineString';
import Draw from 'ol/interaction/Draw';
import Select from 'ol/interaction/Select';
import {MousePosition} from 'ol/control';
import Overlay from 'ol/Overlay';
import {unByKey} from 'ol/Observable';
import WMTS from 'ol/source/WMTS.js';
import WMTSTileGrid from 'ol/tilegrid/WMTS.js';
import {getTopLeft, getWidth} from 'ol/extent.js';
import {toStringXY} from 'ol/coordinate';

import 'ol-ext/dist/ol-ext.css';
import Button from 'ol-ext/control/Button';
import Timeline from 'ol-ext/control/Timeline';
import Bar from 'ol-ext/control/Bar';
import Toggle from 'ol-ext/control/Toggle';
import ModifyFeature from 'ol-ext/interaction/ModifyFeature';
import Popup from 'ol-ext/overlay/Popup';
import WMSCapabilities from 'ol-ext/control/WMSCapabilities';

import 'font-gis/css/font-gis.css';

import 'toastr/build/toastr.css';
import toastr from 'toastr';

import WinBox from 'winbox/src/js/winbox.js';
import 'winbox/dist/css/winbox.min.css';

import axios from 'axios';

import Swal from 'sweetalert2/dist/sweetalert2.js';
import 'sweetalert2/src/sweetalert2.scss';

import 'ol-tidop/dist/ol-tidop.css';
import LayerSwitcherTidop from 'ol-tidop/control/LayerSwitcherTidop';

import TileState from 'ol/TileState.js';
import TileGrid from 'ol/tilegrid/TileGrid.js';

var CSRF_TOKEN = document.querySelector('meta[name=csrf-token]').content;

var ip_server = $("#ip_server").val();
var map;
var url_proxy = 'http://' + ip_server + '/proxy.php?url=';
var title_max_width;
var Rutas_modal;
var Anadir_producto_interval;
var gs_url = $("#gs_url").val() + '/';
var ObteniendoTrabajosProyecto = false;

var timeline_layers = [];
var tline = null;
var tline_ctrl;


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


/***********
 Herramienta medir
 ************/
var source_measure = new VectorSource();
var vector_measure = new VectorLayer({
    id: 'Capa de medida',
    title: 'Capa de medida',
    source: source_measure,
    style: new Style({
        fill: new Fill({
            color: 'rgba(255, 255, 255, 0.2)'
        }),
        stroke: new Stroke({
            color: '#ffcc33',
            width: 2
        }),
        image: new Circle({
            radius: 7,
            fill: new Fill({
                color: '#ffcc33'
            })
        })
    })
});
// A group layer for Measures
var measureLayers = new Group(
    {   id: 'Measure Layers',
        title: 'measureLayers',
        openInLayerSwitcher: false,
        displayInLayerSwitcher: false,
        queryable: false,
        layers:
            [
                vector_measure
            ]
    });

var sketch;
var helpTooltipElement;
var helpTooltip;
var measureTooltipElement;
var measureTooltip;
var continuePolygonMsg = 'Click to continue drawing the polygon';
var continueLineMsg = 'Click to continue drawing the line';

/**
 * Handle pointer move.
 * @param {ol.MapBrowserEvent} evt The event.
 */
var pointerMoveHandler = function(evt) {
    if (evt.dragging) {
        return;
    }
    /** @type {string} */
    var helpMsg = 'Click to start drawing';

    if (sketch) {
        var geom = (sketch.getGeometry());
        if (geom instanceof Polygon) {
            helpMsg = continuePolygonMsg;
        } else if (geom instanceof LineString) {
            helpMsg = continueLineMsg;
        }
    }

    helpTooltipElement.innerHTML = helpMsg;
    helpTooltip.setPosition(evt.coordinate);

    helpTooltipElement.classList.remove('hidden');
};

/*
Measure Functions
 */
var draw2;
/**
 * Format length output.
 * @param {ol.geom.LineString} line The line.
 * @return {string} The formatted length.
 */
var formatLength = function(line) {
    //var length = ol.Sphere.getLength(line);
    var length = line.getLength();
    var output;
    if (length > 100) {
        output = (Math.round(length / 1000 * 100) / 100) +
            ' ' + 'km';
    } else {
        output = (Math.round(length * 100) / 100) +
            ' ' + 'm';
    }
    return output;
};

/**
 * Format area output.
 * @param {ol.geom.Polygon} polygon The polygon.
 * @return {string} Formatted area.
 */
var formatArea = function(polygon) {
    //var area = ol.Sphere.getArea(polygon);
    var area = polygon.getArea();
    var output;
    if (area > 10000) {
        output = (Math.round(area / 1000000 * 100) / 100) +
            ' ' + 'km<sup>2</sup>';
    } else {
        output = (Math.round(area * 100) / 100) +
            ' ' + 'm<sup>2</sup>';
    }
    return output;
};

function addInteraction(type_geom) {
    var type = type_geom; //(typeSelect.value == 'area' ? 'Polygon' : 'LineString');
    draw2 = new Draw({
        source: source_measure,
        type: type,
        style: new Style({
            fill: new Fill({
                color: 'rgba(255, 255, 255, 0.2)'
            }),
            stroke: new Stroke({
                color: 'rgba(0, 0, 0, 0.5)',
                lineDash: [10, 10],
                width: 2
            }),
            image: new Circle({
                radius: 5,
                stroke: new Stroke({
                    color: 'rgba(0, 0, 0, 0.7)'
                }),
                fill: new Fill({
                    color: 'rgba(255, 255, 255, 0.2)'
                })
            })
        })
    });
    map.addInteraction(draw2);

    createMeasureTooltip();
    createHelpTooltip();

    var listener;
    draw2.on('drawstart',
        function(evt) {
            // set sketch
            sketch = evt.feature;

            /** @type {ol.Coordinate|undefined} */
            var tooltipCoord = evt.coordinate;

            listener = sketch.getGeometry().on('change', function(evt) {
                var geom = evt.target;
                var output;
                if (geom instanceof Polygon) {
                    output = formatArea(geom);
                    tooltipCoord = geom.getInteriorPoint().getCoordinates();
                } else if (geom instanceof LineString) {
                    output = formatLength(geom);
                    tooltipCoord = geom.getLastCoordinate();
                }
                measureTooltipElement.innerHTML = output;
                measureTooltip.setPosition(tooltipCoord);
            });
        }, this);

    draw2.on('drawend',
        function() {
            measureTooltipElement.className = 'tooltipm tooltipm-static';
            measureTooltip.setOffset([0, -7]);
            // unset sketch
            sketch = null;
            // unset tooltip so that a new one can be created
            measureTooltipElement = null;
            createMeasureTooltip();
            unByKey(listener);
        }, this);
}

/**
 * Creates a new help tooltip
 */
function createHelpTooltip() {
    if (helpTooltipElement) {
        helpTooltipElement.parentNode.removeChild(helpTooltipElement);
    }
    helpTooltipElement = document.createElement('div');
    helpTooltipElement.className = 'tooltipm hidden';
    helpTooltip = new Overlay({
        element: helpTooltipElement,
        offset: [15, 0],
        positioning: 'center-left'
    });
    map.addOverlay(helpTooltip);
}

/**
 * Creates a new measure tooltip
 */
function createMeasureTooltip() {
    if (measureTooltipElement) {
        measureTooltipElement.parentNode.removeChild(measureTooltipElement);
    }
    measureTooltipElement = document.createElement('div');
    measureTooltipElement.className = 'tooltipm tooltipm-measure';
    measureTooltip = new Overlay({
        element: measureTooltipElement,
        offset: [0, -15],
        positioning: 'bottom-center'
    });
    map.addOverlay(measureTooltip);
}
/*
 Measure Functions
 */

function DeactivateMeasure() {
    map.removeInteraction(draw2);
    source_measure.clear();
    $(".tooltip-static").remove();
}

/***********
 FIN - Herramienta medir
 ************/





/*
Proyectos
 */


var Proyectos_estilos = {
    "nuevo":new Style({
        stroke: new Stroke({
            color: '#009fe3',
            width: 6
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
var coordenadas_nuevo_proyecto;
/*
draw_Proyectos.on('drawend', function (e) {
    var currentFeature = e.feature;//this is the feature fired the event
    var feature_coordinates = e.feature.getGeometry().getCoordinates();
    e.feature.setProperties({'id': 0,'estado': 'nuevo'});
    coordenadas_nuevo_proyecto = feature_coordinates;
    Proyectos_vector_source.clear();
});

var coordenadas_edicion_proyecto;
var modify_Proyectos = new ModifyFeature ({
    sources: Proyectos_vector_source
});
modify_Proyectos.on('modifyend', function (e) {
    var currentFeature = e.features[0];//this is the feature fired the event
    var feature_coordinates = e.features[0].getGeometry().getCoordinates();
    coordenadas_edicion_proyecto = feature_coordinates;
    f_obj.actualizarProyecto(e.features[0].get('id'),coordenadas_edicion_proyecto);
});

var select_proyecto = new Select ({
    hitTolerance: 2,
    layers: [ Proyectos_vector_layer ]
});
select_proyecto.on('select', function (e) {
    if (e.selected.length == 0)
        return;

    f_obj.eliminarProyecto(e.selected[0].get('id'));
    eliminar_Proyecto_ctrl.setActive(false);
});

var Proyectos_bar = new Bar({
    toggleOne: true,
    autoDeactivate: true
});
let cargar_Proyecto_ctrl = new Toggle(
    {	html: '<i class="fa-solid fa-folder-open"></i>',
        title: "Cargar proyecto",
        onToggle: function(active)
        {
            if (active)
                showWinBox('CargarProyecto_winbox_obj');
            else
                hideWinBox('CargarProyecto_winbox_obj');
        }
    });
Proyectos_bar.addControl(cargar_Proyecto_ctrl);
function change_cargar_Proyecto_ctrl() {
    var active = cargar_Proyecto_ctrl.getActive();
    if (!active)
        hideWinBox('CargarProyecto_winbox_obj');
}
cargar_Proyecto_ctrl.addEventListener('change:active', change_cargar_Proyecto_ctrl, false);

let nuevo_Proyecto_ctrl = new Toggle(
    {	html: '<i class="fa-solid fa-plus"></i>',
        title: "Nuevo proyecto",
        interaction: draw_Proyectos,
        onToggle: function(active)
        {
            if (active) {
                showWinBox('NuevoProyecto_winbox_obj');
            }
            else
                hideWinBox('NuevoProyecto_winbox_obj');
        }
    });
Proyectos_bar.addControl(nuevo_Proyecto_ctrl);
function change_nuevo_Proyecto_ctrl() {
    var active = nuevo_Proyecto_ctrl.getActive();
    if (!active)
        hideWinBox('NuevoProyecto_winbox_obj');
}
nuevo_Proyecto_ctrl.addEventListener('change:active', change_nuevo_Proyecto_ctrl, false);
*/
var Proyectos_ctrl = new Toggle({
    html: '<i class="fa-solid fa-toolbox"></i>',
    className: 'toggle_button_proyectos',
    title: "Proyectos",
    onToggle: function(active)
    {
        //nuevo_Proyecto_ctrl.setActive(false);
        if (active)
            showWinBox('CargarProyecto_winbox_obj');
        else
            hideWinBox('CargarProyecto_winbox_obj');
    }/*,
    bar: Proyectos_bar*/
});
function change_Proyectos_ctrl() {
    var active = Proyectos_ctrl.getActive();
    if (!active)
        hideWinBox('CargarProyecto_winbox_obj');
}
Proyectos_ctrl.addEventListener('change:active', change_Proyectos_ctrl, false);

let info_ctrl = new Toggle(
    {
        html: '<i class="fa-solid fa-info"></i>',
        title: "Información",
        onToggle: function (b) {
            DeactivateMeasure();
            if (b) {

            } else {

            }
        }
    });

function change_info_ctrl() {
    var active = info_ctrl.getActive();
    if (!active)
        popup_getFeatureInfo.hide();
}

info_ctrl.addEventListener('change:active', change_info_ctrl, false);


var node = document.getElementById("NuevoProyecto_winbox");
var NuevoProyecto_winbox_obj = new WinBox("Nuevo Proyecto",{
    id: "NuevoProyecto_winbox_obj",
    mount: node,
    x: 85,
    y: 300,
    right: "10px",
    top: "10px",
    bottom: -110,
    left: 10,
    width: "330px",
    height: "160px",
    border: "2px",
    background: "#212529",
    root: document.body,
    class: [
        //"no-close",
        "no-min",
        "no-max",
        "no-full",
        "no-resize"
    ],
    onclose: function(force){
        if(!force) {
            draw_Proyectos.setActive(false);
            Proyectos_vector_source.clear();
            $("#nombre_proyecto").val('');
            hideWinBox('NuevoProyecto_winbox_obj');
            return true;
        }
        return false;
    }
});
hideWinBox('NuevoProyecto_winbox_obj');

var node = document.getElementById("CargarProyecto_winbox");
var CargarProyecto_winbox_obj = new WinBox("Proyectos", {
    id: "CargarProyecto_winbox_obj",
    mount: node,
    x: 130,
    y: 240,
    right: "10px",
    top: "10px",
    bottom: -110,
    left: 10,
    width: "330px",
    height: "73px",
    border: "2px",
    background: "#212529",
    root: document.body,
    class: [
        //"no-close",
        "no-min",
        "no-max",
        "no-full",
        "no-resize"
    ],
    onclose: function(force){
        if(!force) {
            Proyectos_ctrl.setActive(false);
            hideWinBox('CargarProyecto_winbox_obj');
            return true;
        }
        return false;
    }
});
hideWinBox('CargarProyecto_winbox_obj');

/*
FIN - Proyectos
 */


/***********
 Añadir capas y productos
 ************/

var node = document.getElementById("GestorArchivos_winbox");
var GestorArchivos_winbox_obj = new WinBox("Productos",{
    index: 2,
    id: "GestorArchivos_winbox_obj",
    mount: node,
    x: 130,
    y: 300,
    right: 0,
    top: "10px",
    bottom: 0,
    left: 0,
    width: "400px",
    height: "400px",
    border: "2px",
    background: "#212529",
    root: document.body,
    class: [
        //"no-close",
        "no-min",
        "no-max",
        "no-full",
        "no-resize"
    ],
    onclose: function(force){
        if(!force) {
            hideWinBox('GestorArchivos_winbox_obj');
            return true;
        }
        return false;
    }
});
hideWinBox('GestorArchivos_winbox_obj');

var Anadir_capa_winbox_obj;
var node = document.getElementById("Anadir_capa_winbox");
Anadir_capa_winbox_obj = new WinBox("Añadir capa",{
    index: 1,
    id: "Anadir_capa_winbox_obj",
    mount: node,
    x: "center",
    y: 60,
    right: 0,
    top: 60,
    bottom: 0,
    left: 0,
    width: "380px",
    height: "270px",
    border: "2px",
    background: "#212529",
    root: document.body,
    class: [
        "no-min",
        "no-max",
        "no-full"
    ],
    onclose: function(force){
        if(!force) {
            hideWinBox('Anadir_capa_winbox_obj');
            anadir_capa_ctrl.setActive(false);
            return true;
        }
        return false;
    }
});
hideWinBox('Anadir_capa_winbox_obj');

var anadir_capa_ctrl = new Toggle({
    html: '<i class="fg-lg fg-layer-alt-add-o"> </i>',
    className: 'toggle_button_anadir_capa',
    title: "Añadir capa",
    onToggle: function(active) {
        DeactivateMeasure();
        if (active)
            showWinBox('Anadir_capa_winbox_obj');
        else
            hideWinBox('Anadir_capa_winbox_obj');
    }
});
function change_anadir_capa_ctrl() {
    var active = anadir_capa_ctrl.getActive();
    if (!active)
        hideWinBox('Anadir_capa_winbox_obj');
}
anadir_capa_ctrl.addEventListener('change:active', change_anadir_capa_ctrl, false);

var Anadir_producto_winbox_obj;
var node2 = document.getElementById("Anadir_producto_winbox");
Anadir_producto_winbox_obj = new WinBox("Añadir producto",{
    //index: 1,
    id: "Anadir_producto_winbox_obj",
    mount: node2,
    x: 130,
    y: 300,
    right: 0,
    top: 60,
    bottom: 0,
    left: 0,
    width: "410px",
    height: "100px",
    border: "2px",
    background: "#212529",
    root: document.body,
    class: [
        "no-min",
        "no-max",
        "no-full"
    ],
    onclose: function(force){
        if(!force) {
            hideWinBox('Anadir_producto_winbox_obj');
            anadir_producto_ctrl.setActive(false);
            return true;
        }
        return false;
    }
});
hideWinBox('Anadir_producto_winbox_obj');

var anadir_producto_ctrl = new Toggle({
    html: '<i class="fg-lg fg-layer-alt-add-o"> </i>',
    className: 'toggle_button_anadir_producto',
    title: "Añadir producto",
    onToggle: function(active) {
        DeactivateMeasure();
        if (active)
            showWinBox('Anadir_producto_winbox_obj');
        else
            hideWinBox('Anadir_producto_winbox_obj');
    }
});
function change_anadir_producto_ctrl() {
    var active = anadir_producto_ctrl.getActive();
    if (!active)
        hideWinBox('Anadir_producto_winbox_obj');
}
anadir_producto_ctrl.addEventListener('change:active', change_anadir_producto_ctrl, false);

var jstree_obj = false;
function getFiles(extension_arr,id_proyecto,carpeta) {

    $.ajax({
        url:  "/getFiles",
        type: 'POST',
        data: {_token: CSRF_TOKEN, extension_arr: extension_arr, id_proyecto:id_proyecto , carpeta: carpeta},
        dataType: 'text',
        success: function (data) {
            //console.log(data);

            if (jstree_obj)
                $('#jstree_demo_div').jstree('destroy');

            $('#jstree_demo_div').html('<ul>'+data+'</ul>');
            $('#jstree_demo_div').jstree();
            jstree_obj = true;

            $("#jstree_demo_div").on(
                "select_node.jstree", function(evt, data){
                    //selected node object: data.node;
                    //console.log(data);
                    if (data.node.icon == 'far fa-file') {
                        //Obtener nombre de los padres (parents)
                        var parents_str = '';
                        for(var x=0;x<data.node.parents.length-1;x++){
                            var parent_node = $('#jstree_demo_div').jstree(true).get_node(data.node.parents[x]);
                            //console.log(parent_node.text);
                            //var aux_str = parent_node.text.split(';')[1];
                            var aux_str = parent_node.text.trim();
                            parents_str = aux_str + '/' + parents_str;
                        }

                        var nombreCapa = data.node.text.trim().split('.')[0];
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

                        if (!isNaN(outString[0]))
                            outString = 'a_' + outString;

                        $("#nombreCapaGeoserver").val( outString );

                        $("#sidap_file_name").val( parents_str + data.node.text.trim() );
                        $("#producto_file_name").val( parents_str + data.node.text.trim() );

                        hideWinBox("GestorArchivos_winbox_obj");
                    }
                }
            );

            showWinBox("GestorArchivos_winbox_obj");

            setTimeout(function () {
                var h = $("#jstree_demo_div").height();
                var w = GestorArchivos_winbox_obj.width;
                GestorArchivos_winbox_obj.resize(w, h + 60);
                var pX = Anadir_producto_winbox_obj.x;
                var pY = Anadir_producto_winbox_obj.y + Anadir_producto_winbox_obj.height;
                GestorArchivos_winbox_obj.resize(Anadir_producto_winbox_obj.width, GestorArchivos_winbox_obj.height);
                GestorArchivos_winbox_obj.move(pX, pY);
            }, 500);



        }
    });

}

/***********
 FIN - Añadir capas y productos
 ************/




function hideWinBox(id) {
    if (document.getElementById(id))
        document.getElementById(id).style.display = "none";
}

function showWinBox(id) {
    if (document.getElementById(id))
        document.getElementById(id).style.display = "";
}

function toggleWinBox(id) {
    var estado = document.getElementById(id).style.display;
    if (estado === "none")
        document.getElementById(id).style.display = "";
    else
        document.getElementById(id).style.display = "none";
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
}

// Control LayerSwitcher
let LayerSwitcher_ctrl = new LayerSwitcherTidop({
    target:$("#LayerSwitcher_div").get(0),
    collapsed: false,
    show_progress: true,
    trash: false,
    selection: true,
    displayBaseLayersInLayerSwitcher: false,
    onchangeCheck: function (l) {


        var layerArray, len, layer, layer2;
        layerArray = map.getLayers().getArray(),
            len = layerArray.length;
        while (len > 0) {
            layer = layerArray[len - 1];
            if (layer instanceof Group) {
                var layerArray2 = layer.getLayers().getArray();
                var len2 = layerArray2.length;
                while (len2 > 0) {
                    layer2 = layerArray2[len2 - 1];
                    //console.log(layer2.get('id'));

                    if (layer2.get('tipo') === 'potree') {
                        if (layer2.get('visible'))
                            f_obj.visorPotree(layer2);
                        else
                            hideWinBox(layer2.get('id') + 'WinboxObj')
                    }

                    len2--;
                }
            }
            //console.log(layer.get('id'));
            len--;
        }
        /*

        */
    }
});


var potreeWinbox = null;
var capa_seleccionada = false;
var id_capa_seleccionada = 0;
var url_legend_part_1 = '';
var url_legend_part_2 = '';
LayerSwitcher_ctrl.on('select', function(e){
    console.log(e)
    var layer = e.layer;
    if (e.layer === undefined)
        return
    var esGrupo = layer.get('esGrupo');

    hideWinBox("Legend_winbox_obj");


    if (!esGrupo) {
        var title = layer.get('title');
        var id_capa = layer.get('id');
        var descargable = layer.get('descargable');
        var sistema = layer.get('sistema');
        var tipo = layer.get('tipo');
        var workspace = layer.get('workspace');
        var leyenda = layer.get('leyenda');

        if (descargable)
            $("#descargar_opcion").removeClass("d-none");
        else
            $("#descargar_opcion").addClass("d-none");

        if (!sistema)
            $("#eliminar_opcion").removeClass("d-none");
        else
            $("#eliminar_opcion").addClass("d-none");

        $("#titulo-capa-seleccionada").html(title);

        if (leyenda != null) {
            url_legend_part_1 = leyenda + '?SCALE=';
            url_legend_part_2 = '';

            $("#leyenda_opcion").removeClass("d-none");
        }
        else {
            if (tipo === 'wms' || tipo === 'wmts') {
                url_legend_part_1 = url_proxy + gs_url + 'wms&REQUEST=GetLegendGraphic&VERSION=1.0.0&FORMAT=image/png&WIDTH=25&HEIGHT=25&STRICT=false&SCALE=';
                url_legend_part_2 = '&style=' + workspace + ':' + id_capa;

                $("#leyenda_opcion").removeClass("d-none");
            } else {
                url_legend_part_1 = '';
                url_legend_part_2 = '';
                $("#leyenda_capa").attr("src", "");
                $("#leyenda_opcion").addClass("d-none");
            }
        }

        capa_seleccionada = true;
        id_capa_seleccionada = id_capa;
    }
    else {
        capa_seleccionada = false;
        id_capa_seleccionada = 0;
    }


    if (capa_seleccionada) {
        $("#LayerSwitcher_options_div").removeClass("d-none");
        if (tipo === 'potree') {
            $("#zoom_a_la_extension_opcion").addClass("d-none");


            //$("#potree_opcion").removeClass("d-none");
        } else {
            $("#zoom_a_la_extension_opcion").removeClass("d-none")
            //$("#potree_opcion").addClass("d-none");
        }
    }
    else
        $("#LayerSwitcher_options_div").addClass("d-none");

    actualizaLayerControl();

});


var node = document.getElementById("Legend_winbox");
var Legend_winbox_obj = new WinBox("Leyenda",{
    index: 2,
    id: "Legend_winbox_obj",
    mount: node,
    x: window.innerWidth - 400,
    y: "bottom",
    right: "10px",
    top: "60px",
    left: 10,
    width: "350px",
    height: "122px",
    border: "2px",
    background: "#212529",
    root: document.body,
    class: [
        //"no-close",
        "no-min",
        "no-max",
        "no-full"
    ],
    onclose: function(force){
        if(!force) {
            hideWinBox("Legend_winbox_obj");
            return true;
        }
        return false;
    }
});
hideWinBox("Legend_winbox_obj");




// Visor control bar
let visorbar = new Bar({
    className: 'visorbar',
    toggleOne: true,
    autoDeactivate: true
});

// Layer control bar
let layerControlBar = new Bar({
    className: 'visorbar',
    toggleOne: true,
    autoDeactivate: true
});

// app control bar
let appbar = new Bar({
    className: 'appbar',
    toggleOne: true,
    autoDeactivate: true
});

var zoom_in_ctrl = new Button({
    html: '<i class="fa-solid fa-magnifying-glass-plus"></i>',
    title: "Zoom +",
    handleClick: function()
    {
        var zoom = map.getView().getZoom();
        //map.getView().setZoom(zoom + 0.001);
        map.getView().animate({
            zoom: zoom + 0.5,
            duration: 500
        })
    }
});
visorbar.addControl(zoom_in_ctrl);
var zoom_out_ctrl = new Button({
    html: '<i class="fa-solid fa-magnifying-glass-minus"></i>',
    title: "Zoom -",
    handleClick: function()
    {
        var zoom = map.getView().getZoom();
        map.getView().animate({
            zoom: zoom - 0.5,
            duration: 500
        })
    }
});
visorbar.addControl(zoom_out_ctrl);

let LayerSwitcher_btn_ctrl = new Button(
    {	html: '<i class="fg-lg fg-layer-stack"></i>',
        title: "Control de capas",
        handleClick: function()
        {
            var visible = $("#LayerSwitcher_winbox_obj").css("display");
            if (visible === "none") {
                showWinBox('LayerSwitcher_winbox_obj');
                actualizaLayerControl();
            }
            else {
                hideWinBox('LayerSwitcher_winbox_obj');
            }
        }
    });
layerControlBar.addControl(LayerSwitcher_btn_ctrl);



// WMS control
var WMSCapabilities_control = new WMSCapabilities({
    target: document.body,
    title: 'Añadir capa WMS',
    services: {
        'IGN Mapa Base': 'https://www.ign.es/wms-inspire/ign-base?request=GetCapabilities&service=WMS'/*,
        'OSM': 'https://wms.openstreetmap.fr/wms',
        'OSM-Mundialis': 'https://ows.mundialis.de/services/service',
        'CorineLandCover': 'https://wxs.ign.fr/corinelandcover/geoportail/r/wms'*/
    },
    onselect: function(layer) {
        //var index = map.getLayers().getArray().indexOf(LayerSwitcher_ctrl.getSelection());
        //map.getLayers().insertAt(index+1, layer);
        map.addLayer(layer);
        LayerSwitcher_ctrl.selectLayer(layer);
    }
});
WMSCapabilities_control.setHtml('');


/* Nested toobar with one control activated at once */
let nestedvisor = new Bar ({
    toggleOne: true,
    autoDeactivate: true
});

let nestedapp = new Bar ({
    toggleOne: true,
    autoDeactivate: true
});



/***********
 Herramienta medir
 ************/
var medir_bar = new Bar({
    toggleOne: true,
    autoDeactivate: true
});
let medir_distancia_ctrl = new Toggle(
    {	html: '<i class="fg-lg fg-measure-line"></i>',
        title: "Medir distancia",
        onToggle: function(b)
        {
            DeactivateMeasure();
            if (b) {
                //map.removeInteraction(hover);
                addInteraction('LineString');
            }
            else {
                //map.addInteraction(hover);
            }
        }
    });

let medir_area_ctrl = new Toggle(
    {	html: '<i class="fg-lg fg-measure-area"></i>',
        title: "Medir área",
        onToggle: function(b)
        {
            DeactivateMeasure();
            if (b) {
                //map.removeInteraction(hover);
                addInteraction('Polygon');
            }
            else {
                //map.addInteraction(hover);
            }
        }
    });

var medir_ctrl = new Toggle({
    html: '<i class="fg-lg fg-measure"></i>',
    title: "Medir",
    onToggle: function(active)
    {
        DeactivateMeasure();
    },
    bar: medir_bar
});
/***********
 FIN - Herramienta medir
 ************/

var node = document.getElementById("LayerSwitcher_winbox");
var LayerSwitcher_winbox_obj = new WinBox("Capas",{
    index: 2,
    id: "LayerSwitcher_winbox_obj",
    mount: node,
    x: window.innerWidth - 335,
    y: "65px",
    right: "10px",
    top: "60px",
    bottom: -110,
    left: 10,
    width: "350px",
    //height: "390px",
    border: "2px",
    background: "#212529",
    root: document.body,
    class: [
        //"no-close",
        "no-min",
        "no-max",
        "no-full"
    ],
    onclose: function(force){
        if(!force) {
            hideWinBox("LayerSwitcher_winbox_obj");
            return true;
        }
        return false;
    }
});
//hideWinBox("LayerSwitcher_winbox_obj");
$('#LayerSwitcher_winbox').on('click', function() {
    var scrollTop = $('#LayerSwitcher_winbox').parent()[0].scrollTop;
    setTimeout(function(){
        $('#LayerSwitcher_winbox').parent()[0].scrollTop = scrollTop;
    }, 100);
});

function actualizaLayerControl(){
    setTimeout(function(){
        var altura_panel = parseInt($("#LayerSwitcher_div").css('height')) + 23;
        var ancho_panel = LayerSwitcher_winbox_obj.width;
        if (altura_panel > (window.innerHeight - 100))
            altura_panel = window.innerHeight - 100;

        ancho_panel = title_max_width*6.5 + 150;
        if(ancho_panel > 400)
            ancho_panel = 400;

        var altura_opciones_capa = 0;
        if (capa_seleccionada)
            altura_opciones_capa = parseInt($("#LayerSwitcher_options_div").css('height')) + 22;


        LayerSwitcher_winbox_obj.resize(ancho_panel + "px", (altura_panel + altura_opciones_capa) + "px");

        var x_pos = window.innerWidth - ancho_panel - 5;
        LayerSwitcher_winbox_obj.move(x_pos,"65px");

        var div_menos = document.querySelectorAll('.collapse-layers');
        var div_mas = document.querySelectorAll('.expend-layers');
        div_menos.forEach((btn) => {
            btn.addEventListener("click", actualizaLayerControl);
        });
        div_mas.forEach((btn) => {
            btn.addEventListener("click", actualizaLayerControl);
        });

    }, 100);
}

function cambiaTamanioVentana(){
    // Get width and height of the window excluding scrollbars
    var w = document.documentElement.clientWidth;
    var h = document.documentElement.clientHeight;

    $("#map").css('height',(h-57) + 'px');
    map.updateSize();
    //console.log("Width: " + w + ", " + "Height: " + h);
}

function findLayer(id_capa){
    var id_capax = -1;
    var layer_found = null;
    $.each(map.getLayers().getArray(), function (k,v){
        var title = v.get('title');
        var this_id_capa = 0;

        if (v instanceof Group) {
            var child_layers = v.getLayers();
            child_layers.forEach(function (ch_layer, index) {
                //console.log(ch_layer);
                //alert(ch_layer.get('id'));
                if (ch_layer.get('id') == id_capa) {
                    //alert(index+'*'+k);
                    id_capax = index;
                    layer_found = ch_layer;
                }
            });
        } else {
            if (v.get('id') == id_capa) {
                //alert(id_capax+'**'+k);
                id_capax = k;
                layer_found = v;
            }
        }
    });
    //console.log(layer_found);
    return layer_found;
}

function findLayerTitle(titulo){
    var id_capax = -1;
    var layer_found = null;
    $.each(map.getLayers().getArray(), function (k,v){
        var this_id_capa = 0;

        if (v instanceof Group) {
            var child_layers = v.getLayers();
            child_layers.forEach(function (ch_layer, index) {
                //console.log(ch_layer);
                //alert(ch_layer.get('id'));
                if (ch_layer.get('title') === titulo) {
                    //alert(index+'*'+k);
                    id_capax = index;
                    layer_found = ch_layer;
                }
            });
        } else {
            if (v.get('title') === titulo) {
                //alert(id_capax+'**'+k);
                id_capax = k;
                layer_found = v;
            }
        }
    });
    //console.log(layer_found);
    return layer_found;
}

function findGroup(id_group){
    var group_found = null;
    $.each(map.getLayers().getArray(), function (k,v){
        if (v instanceof Group) {
            if (v.get('id') == id_group) {
                group_found = v;
            }
        }
    });
    return group_found;
}

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

var mousePositionControl = new MousePosition({
    coordinateFormat: function(coordinate) {
        return toStringXY(coordinate, 3);
    },
    projection: 'EPSG:4326',
    // comment the following two lines to have the mouse position
    // be placed within the map.
    //className: 'custom-mouse-position',
    //target: document.getElementById('mouse-position'),
    undefinedHTML: '&nbsp;'
});


function mapScale () {
    var unit = map.getView().getProjection().getUnits();
    var resolution = map.getView().getResolution();
    var inchesPerMetre = 39.37;

    scale = resolution * olProj.METERS_PER_UNIT[unit] * inchesPerMetre * dpi;
    //console.log(scale);

    actualizaLeyenda();
}

function actualizaLeyenda () {
    var visible = document.getElementById("Legend_winbox_obj").style.display;
    if (url_legend_part_1 != '' && visible != 'none') {

        $("#leyenda_capa").attr("src",url_legend_part_1 + scale + url_legend_part_2);

        setTimeout(function(){
            var altura_panel = parseInt($("#Legend_div").css('height')) + 23;
            var ancho_panel = $("#leyenda_capa")[0].naturalWidth + 50;

            //var ancho_panel = Legend_winbox_obj.width;
            if (altura_panel > (window.innerHeight - 100))
                altura_panel = window.innerHeight - 100;

            Legend_winbox_obj.resize(ancho_panel + "px", altura_panel + "px");

            var x_pos = window.innerWidth - LayerSwitcher_winbox_obj.width - 60;
            Legend_winbox_obj.move(x_pos,"bottom");

        }, 1500);
    }
}

var popup_getFeatureInfo = new Popup ({
    popupClass: "default anim getFeatureInfo", //"tooltips", "warning" "black" "default", "tips", "shadow",
    closeBox: false,
    onclose: function(){
        //console.log("You close the box");
    },
    positioning: "auto",
    autoPan: true,
    autoPanAnimation: { duration: 1000 }
});



var dpi = 0;
var scale = 0;
document.addEventListener("DOMContentLoaded", function(event) {
    f_obj.inicio();

    dpi = document.getElementById("dpi").offsetHeight;
    //console.log(dpi);

    /*
    $(".ol-layerswitcher-image-tidop button").prop("title","Mapas Base");
    $(".ol-layerswitcher-image-tidop button").prop("data-toggle","tooltip");
    $(".ol-layerswitcher-image-tidop button").prop("data-placement","left");

    $("button").prop("data-toggle","tooltip");
    $("button").prop("data-placement","bottom");

    $("#anadirCapaProyectoButton").prop("data-toggle","tooltip");
    $("#anadirCapaProyectoButton").prop("data-placement","bottom");
    */

    var buttons = document.getElementsByTagName("button");
    for(var x=0; x<buttons.length;x++) {
        buttons[x].setAttribute("data-toggle","tooltip");
        buttons[x].setAttribute("data-placement","auto");
    }
    //console.log(buttons);

/*
    $('[data-toggle="tooltip"]').tooltip({
        boundary: 'window',
        placement: 'auto'
    });
*/
});



var f_obj = {

    /***********
     Proyectos
     ************/
    guardarProyecto: function(nuevo) {
        var nombre = $("#nombre_proyecto").val();
        var coordinates = coordenadas_nuevo_proyecto;

        var duracion = $("#duracion_proyecto").val();
        var inicio = moment(duracion.split(' - ')[0],'DD/MM/YYYY').format('YYYY-MM-DD');
        var fin = moment(duracion.split(' - ')[1],'DD/MM/YYYY').format('YYYY-MM-DD');

        var html = '<p class="text-lg text-center"><i class="fa-solid fa-circle-notch fa-spin mr-2"></i> Guardando proyecto...</p>';
        Rutas_modal = Swal.fire({
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
                hideWinBox('NuevoProyecto_winbox_obj');
                draw_Proyectos.setActive(false);
                f_obj.restablecerAplicacion();

                Rutas_modal.close();
            }
        });
    },

    actualizarProyecto: function(id,coordinates) {
        if (id === 0)
            return;

        $.ajax({
            url: '/actualizarProyecto',
            type: 'POST',
            data: {_token: CSRF_TOKEN,coordinates:coordinates,id:id},
            dataType: 'JSON',
            success: function (data) {
                //console.log(data);
                //hideWinBox('NuevoProyecto_winbox_obj');
                //modify_Proyectos.setActive(false);
                f_obj.obtenerProyectos();
                f_obj.cargarProyecto();
            }
        });
    },

    eliminarProyecto: function() {

        var features = Proyectos_vector_source.getFeatures();
        if (features.length === 0)
            return
        var id = features[0].get('id');

        if (tline)
            map.removeControl(tline);

        $.ajax({
            url: '/eliminarProyecto',
            type: 'POST',
            data: {_token: CSRF_TOKEN,id:id},
            dataType: 'JSON',
            success: function (data) {
                //console.log(data);
                f_obj.restablecerAplicacion();
            }
        });
    },

    obtenerProyectos: function() {
        $.ajax({
            url: '/obtenerProyectos',
            type: 'POST',
            data: {_token: CSRF_TOKEN},
            dataType: 'JSON',
            success: function (data) {
                //console.log(data);

                var option_str = '<option value="0" selected>Seleccione un proyecto</option>';
                for(var x=0;x<data.length;x++) {
                    var id = parseInt(data[x]['id']);
                    var nombre = data[x]['nombre'].toString();
                    option_str += '<option value="' + id + '">' + nombre + '</option>';
                }
                $("#proyectos_select").html(option_str);

                anadir_capa_ctrl.setVisible(false);

            }
        });
    },

    cargarProyecto: function() {
        var id_proyecto = $("#proyectos_select").val();

        capa_seleccionada = false;
        $("#LayerSwitcher_options_div").addClass("d-none");

        $("#ogc_services").html('');
        var w = CargarProyecto_winbox_obj.width;
        CargarProyecto_winbox_obj.resize(w, 73);

        if (id_proyecto === '0') {
            f_obj.restablecerAplicacion();
            return;
        }

        //Proyectos_ctrl.setActive(false);

        // Elimina todas las capas del visor Openlayers
        var layerArray, len, layer;
        layerArray = map.getLayers().getArray(),
            len = layerArray.length;
        while (len > 0){
            layer = layerArray[len-1];
            map.removeLayer(layer);
            len = layerArray.length;
        }


        info_ctrl.setVisible(true);
        anadir_producto_ctrl.setVisible(true);


        $.ajax({
            url: '/cargarProyecto',
            type: 'POST',
            data: {_token: CSRF_TOKEN, id_proyecto:id_proyecto},
            dataType: 'JSON',
            success: function (data_arr) {
                //console.log(data_arr);

                timeline_layers = [];

                // Capas globales
                var data = data_arr[2];
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
                                visible: data[x]['visible_capa'],
                                noSwitcherDelete: true,
                                displayInLayerSwitcher: data[x]['displayinlayerswitcher'],
                                esGrupo: false,
                                leyenda: data[x]['leyenda'],
                                baseLayer: data[x]['baselayer']
                            });

                            if (data[x]['baselayer'] && data[x]['preview'] != null)
                                ol_layer.values_.preview = data[x]['preview'];


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
                                visible: data[x]['visible_capa'],
                                noSwitcherDelete: data[x]['noswitcherdelete'],
                                displayInLayerSwitcher: data[x]['displayinlayerswitcher'],
                                esGrupo: false,
                                leyenda: data[x]['leyenda'],
                                baseLayer: data[x]['baselayer']
                            });

                            if (data[x]['baselayer'] && data[x]['preview'] != null)
                                ol_layer.values_.preview = data[x]['preview'];

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
                                visible: data[x]['visible_capa'],
                                noSwitcherDelete: data[x]['noswitcherdelete'],
                                displayInLayerSwitcher: data[x]['displayinlayerswitcher'],
                                esGrupo: false,
                                leyenda: data[x]['leyenda'],
                                baseLayer: data[x]['baselayer']
                            });

                            if (data[x]['baselayer'] && data[x]['preview'] != null)
                                ol_layer.values_.preview = data[x]['preview'];

                        }



                        ol_array.push(ol_layer);
                        //timeline_layers.push(ol_layer);




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

                if (!findLayerTitle('Proyectos'))
                    map.addLayer(Proyectos_vector_layer);
                if (!findLayerTitle('measureLayers'))
                    map.addLayer(measureLayers);


                // Capas del proyecto
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
                                displayInLayerSwitcher: data[x]['displayinlayerswitcher'],
                                esGrupo: false,
                                leyenda: data[x]['leyenda'],
                                queryable: true
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
                                visible: data[x]['visible_capa'],
                                noSwitcherDelete: data[x]['noswitcherdelete'],
                                displayInLayerSwitcher: data[x]['displayinlayerswitcher'],
                                esGrupo: false,
                                leyenda: data[x]['leyenda'],
                                queryable: true
                            });

                            ol_array.push(ol_layer);
                            if (data[x]['tline'])
                                timeline_layers.push(ol_layer);
                        } else if (tipo === 'potree') {
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
                                    url: url_proxy + gs_url + 'aicedronesdi/wms',
                                    params: {
                                        LAYERS: 'empty_layer',
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
                                visible: data[x]['visible_capa'],
                                noSwitcherDelete: data[x]['noswitcherdelete'],
                                displayInLayerSwitcher: data[x]['displayinlayerswitcher'],
                                esGrupo: false,
                                leyenda: data[x]['leyenda']
                            });

                            ol_array.push(ol_layer);
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
                }




                // Proyecto
                var data = data_arr[1];
                Proyectos_vector_source.clear();
                for(var x=0;x<data.length;x++) {
                    var coord_elem = data[x]['coord'].split('((')[1];
                    coord_elem = coord_elem.split('))')[0];

                    var c_arr = coord_elem.split(',');
                    var ring_arr = [];
                    for (var y = 0; y < c_arr.length; y++) {
                        var coord_x = c_arr[y].split(' ')[0];
                        var coord_y = c_arr[y].split(' ')[1];
                        ring_arr.push([parseFloat(coord_x), parseFloat(coord_y)]);
                    }

                    var polygon_feature = new Feature({});
                    polygon_feature.setId(data[x]['id']);
                    var polygon_geom = new Polygon([ring_arr]);
                    polygon_feature.setGeometry(polygon_geom);
                    var id = parseInt(data[x]['id']);
                    var estado = data[x]['estado'].toString();
                    var nombre = data[x]['nombre'].toString();
                    polygon_feature.setProperties({'id': id, 'estado': estado, 'nombre': nombre});
                    Proyectos_vector_source.addFeature(polygon_feature);
                }
                var extent = Proyectos_vector_source.getExtent();
                var view = map.getView();
                view.fit(extent);

                /*
                if (tline)
                    map.removeControl(tline);

                var inicio_proyecto = data[0]['inicio'];
                var fin_proyecto = data[0]['fin'];

                tline = new Timeline({
                    className: 'ol-pointer', //  ol-zoomhover
                    zoomButton: true,
                    features: timeline_layers,
                    graduation: 'month',
                    minDate: moment(inicio_proyecto,'YYYY-MM-DD').subtract(3, 'months'),
                    maxDate: moment(fin_proyecto,'YYYY-MM-DD').add(3, 'months'),
                    getFeatureDate: function(l) { return l.get('inicio') },
                    endFeatureDate: function(l) { return l.get('fin') },
                    getHTML: function(l) {
                        var title = l.get('title');
                        var html = '<span style="color:#000000">'+title+'</span>';
                        return title;
                    }
                });

                tline.on('scroll', function(e) {
                    var layer, dmin = Infinity;
                    var mes = parseInt(e.date.getMonth()) + 1;
                    var dia = parseInt(e.date.getDate());
                    if (mes < 10)
                        mes = '0' + mes;
                    if (dia < 10)
                        dia = '0' + dia;
                    var hoy = moment(e.date.getFullYear() + '-' + mes + '-' + dia,'YYYY-MM-DD');






                });
                tline.on('select', function(e) {
                    tline.setDate(e.feature);
                });

                map.addControl (tline);
                tline.setDate(moment());

                $(".ol-timeline .ol-scroll").css("height",(timeline_layers.length + 1) + "em");

                // Collapse the line
                tline.on('collapse', function(e) {
                    //if (e.collapsed) $('#map').addClass('noimg')
                    //else $('#map').removeClass('noimg')
                });

                tline.collapse(true);
                */

                var workspace = data_arr[1][0]['workspace'];
                var url_wms = 'http://aicedrone.tidop.es:8080/geoserver/' + workspace + '/ows?service=WMS&version=1.3.0&request=GetCapabilities';
                var url_wmts = 'http://aicedrone.tidop.es:8080/geoserver/' + workspace + '/gwc/service/wmts?service=WMTS&version=1.1.1&request=GetCapabilities';
                var url_wfs = 'http://aicedrone.tidop.es:8080/geoserver/' + workspace + '/ows?service=WFS&acceptversions=2.0.0&request=GetCapabilities';
                var url_wcs = 'http://aicedrone.tidop.es:8080/geoserver/' + workspace + '/ows?service=WCS&acceptversions=2.0.1&request=GetCapabilities';
                var html = '';
                html += '<a class="btn btn-sm mt-2 bg-green mr-1 text-bold" href="javascript:void(0)" onclick="window.f_obj.copyToClipboard(\'' + url_wms + '\')"><i class="fa-solid fa-clipboard mr-1"></i> WMS</a>';
                html += '<a class="btn btn-sm mt-2 bg-blue mr-1 text-bold" href="javascript:void(0)" onclick="window.f_obj.copyToClipboard(\'' + url_wmts + '\')"><i class="fa-solid fa-clipboard mr-1"></i> WMTS</a>';
                html += '<a class="btn btn-sm mt-2 bg-red mr-1 text-bold" href="javascript:void(0)" onclick="window.f_obj.copyToClipboard(\'' + url_wfs + '\')"><i class="fa-solid fa-clipboard mr-1"></i> WFS</a>';
                html += '<a class="btn btn-sm mt-2 bg-indigo mr-1 text-bold" href="javascript:void(0)" onclick="window.f_obj.copyToClipboard(\'' + url_wcs + '\')"><i class="fa-solid fa-clipboard mr-1"></i> WCS</a>';
                $("#ogc_services").html(html);

                var h = $("#CargarProyecto_winbox").height();
                var w = CargarProyecto_winbox_obj.width;
                CargarProyecto_winbox_obj.resize(w, 114);


                setTimeout(function () {
                    actualizaLayerControl();
                }, 3000);






            }
        });
    },

    copyToClipboard: function (text) {
        var sampleTextarea = document.createElement("textarea");
        document.body.appendChild(sampleTextarea);
        sampleTextarea.value = text; //save main text in it
        sampleTextarea.select(); //select textarea contents
        document.execCommand("copy");
        document.body.removeChild(sampleTextarea);

        var tipo = 'info';
        var mensaje = 'URL copiada al portapapeles: <br> ' + text;
        var time = 15000;
        msg(tipo, mensaje, time, true);
    },

    restablecerAplicacion: function() {
        Proyectos_vector_source.clear();
        info_ctrl.setVisible(false);
        anadir_producto_ctrl.setVisible(false);
        f_obj.restablecerCapas();
        f_obj.obtenerProyectos();
    },

    /***********
     FIN - Proyectos
     ************/








    /***********
     Añadir capas y productos
     ************/

    visorPotree: function (layer) {
        var id_capa = layer.get('id');
        var workspace = layer.get('workspace');
        var title = layer.get('title');

        $.ajax({
            url: 'getPotreeStatus',
            type: 'POST',
            data: {
                _token: CSRF_TOKEN,
                id_capa:id_capa,
                workspace:workspace
            },
            dataType: 'JSON',
            success: function (data) {
                //console.log(data);

                if (data[0] === 'Ok') {
                    if (document.getElementById(id_capa + 'WinboxObj')) {
                        showWinBox(id_capa + 'WinboxObj');
                    } else
                        potreeWinbox = new WinBox(title, {
                            id: id_capa + 'WinboxObj',
                            url: "http://" + ip_server + "/potree/" + workspace + "_" + id_capa + "/" + workspace + "_" + id_capa + ".html",
                            x: "center",
                            y: "center",
                            left: "74px", //document.getElementById('menuContent').offsetWidth,
                            top: "57px",
                            border: "2px",
                            background: "#212529",
                            root: document.body,
                            class: [
                                "no-min"
                            ],
                            onclose: function (force) {
                                var id = potreeWinbox.id.split('WinboxObj')[0];
                                var layer = findLayer(id);
                                if (layer)
                                    layer.setVisible(false);
                            }
                        });
                }
                else {
                    var ly = findLayer(id_capa);
                    ly.setVisible(false);
                    msg('error', 'La visualización de la capa <strong>' + title + '</strong> se está generando. En unos minutos estará disponible.', 10000, true);
                }

            }
        });




    },

    showFiles: function(extension_arr,directorio) {
        var features = Proyectos_vector_source.getFeatures();
        if (features.length === 0)
            return
        var id_proyecto = features[0].get('id');

        getFiles(extension_arr,id_proyecto,directorio);
    },

    anadirCapa: function() {

        var features = Proyectos_vector_source.getFeatures();
        if (features.length === 0)
            return
        var id = features[0].get('id');

        var duracion = $("#intervalo_capa").val();
        var inicio = moment(duracion.split(' - ')[0],'DD/MM/YYYY').format('YYYY-MM-DD');
        var fin = moment(duracion.split(' - ')[1],'DD/MM/YYYY').format('YYYY-MM-DD');

        var data = new FormData();
        data.append('_token', CSRF_TOKEN );
        data.append('id', id );
        data.append('nombreCapa', $("#nombreCapa").val() );
        data.append('nombreCapaGeoserver', $("#nombreCapaGeoserver").val() );
        data.append('ruta', $("#sidap_file_name").val() );
        data.append('inicio', inicio );
        data.append('fin', fin );

        var btn = document.getElementById("anadirCapaProyectoButton");
        btn.disabled = true;
        var btnEstado = document.getElementById("anadirCapaProyectoEstadoButton");
        btnEstado.classList.remove("btn-info");
        btnEstado.classList.add("btn-warning");
        btnEstado.innerHTML = '';

        var config = {
            headers: {
                'Content-Type': 'multipart/form-data'
            },
            onUploadProgress: function(progressEvent) {
                var percentCompleted = Math.round( (progressEvent.loaded * 100) / progressEvent.total );
                document.getElementById("anadirCapaProyectoEstadoButton").innerHTML = 'Publicando capa...';
                if (percentCompleted === 100)
                    document.getElementById("anadirCapaProyectoEstadoButton").innerHTML = 'Publicando capa...';
            }
        };

        axios.post('http://' + ip_server + '/anadirCapa', data, config)
            .then(response => {
                btnEstado.innerHTML = 'Publicando capa...';
                //var res = response.data;
                //console.log(response);

                setTimeout(function(){
                    btnEstado.classList.remove("btn-warning");
                    btnEstado.innerHTML = '';
                    btn.disabled = false;

                    hideWinBox('Anadir_capa_winbox_obj');
                    anadir_capa_ctrl.setActive(false);

                    f_obj.cargarProyecto();
                }, 5000);


            }).catch(response => {
                console.log(response);
            });
    },

    descargarCapa: function() {
        var features = Proyectos_vector_source.getFeatures();
        if (features.length === 0)
            return
        var id_proyecto = features[0].get('id');

        var id = id_capa_seleccionada;
        if (id === 0)
            return;

        var html = '<p class="text-lg text-center"><i class="fa-solid fa-circle-notch fa-spin mr-2"></i> Generando descarga...</p>';
        var Swal_modal = Swal.fire({
            html: html,
            allowOutsideClick: false,
            showConfirmButton: false,
            width: 350,
            padding: '0px'
        })

        $.ajax({
            url: '/descargarCapa',
            type: 'POST',
            data: {
                _token: CSRF_TOKEN,
                id_proyecto:id_proyecto,
                id:id
            },
            dataType: 'JSON',
            success: function (data) {
                //console.log(data);

                Swal_modal.close();

                var url = data[0];
                var link = document.createElement("a");
                if (link.download !== undefined) { // feature detection
                    // Browsers that support HTML5 download attribute
                    //var url = URL.createObjectURL(blob);
                    link.setAttribute("href", url);
                    //link.setAttribute("download", filename);
                    link.style.visibility = 'hidden';
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                }

            }
        });

    },

    eliminarCapa: function() {
        var features = Proyectos_vector_source.getFeatures();
        if (features.length === 0)
            return
        var id_proyecto = features[0].get('id');

        var id = id_capa_seleccionada;
        if (id === 0)
            return;

        Swal.fire({
            html: '<p class="text-lg text-center">¿ Desea eliminar la capa seleccionada ?</p>',
            showDenyButton: true,
            showCancelButton: false,
            confirmButtonText: 'Sí',
            denyButtonText: 'No',
        }).then((result) => {
            /* Read more about isConfirmed, isDenied below */
            if (result.isConfirmed) {

                var layer = findLayer(id);
                var tipo = layer.get('tipo');
                if (tipo === 'potree') {
                    hideWinBox(id + 'WinboxObj');
                }

                $.ajax({
                    url: '/eliminarCapa',
                    type: 'POST',
                    data: {
                        _token: CSRF_TOKEN,
                        id_proyecto:id_proyecto,
                        id:id
                    },
                    dataType: 'JSON',
                    success: function (data) {
                        //console.log(data);

                        f_obj.cargarProyecto();

                    }
                });

            } else if (result.isDenied) {

            }
        })
    },

    zoomExtensionCapa: function() {
        var id_capa = id_capa_seleccionada;
        if (id_capa === 0)
            return;

        var layer = findLayer(id_capa);
        var tipo = layer.get("tipo");

        if (tipo === 'wms' || tipo === 'wmts') {
            var base_url = url_proxy + gs_url + 'ows&service=WMS&version=1.3.0&request=GetCapabilities';
            var xmlHttp = new XMLHttpRequest();
            xmlHttp.open("GET", base_url, false); // false for synchronous request
            xmlHttp.send(null);
            var json_capabilities = xmlToJson(($.parseXML(xmlHttp.responseText)));
            //console.log(json_capabilities);
            //console.log(json_capabilities.WMS_Capabilities.Capability.Layer.Layer);
            for (var x = 0; x < json_capabilities.WMS_Capabilities.Capability.Layer.Layer.length; x++) {
                var id = json_capabilities.WMS_Capabilities.Capability.Layer.Layer[x].Title['#text'];
                if (id === id_capa) {
                    var boundingBox = json_capabilities.WMS_Capabilities.Capability.Layer.Layer[x].BoundingBox[0]['@attributes'];
                    //console.log(json_capabilities.WMS_Capabilities.Capability.Layer.Layer[x].BoundingBox);
                    //var bbox = [boundingBox['minx'],boundingBox['miny'],boundingBox['maxx'],boundingBox['maxy']];
                    var a = olProj.transform([parseFloat(boundingBox['minx']), parseFloat(boundingBox['miny'])], 'EPSG:4326', 'EPSG:3857');
                    var b = olProj.transform([parseFloat(boundingBox['maxx']), parseFloat(boundingBox['maxy'])], 'EPSG:4326', 'EPSG:3857');
                    var bbox = [a[0], a[1], b[0], b[1]];
                    var view = map.getView();
                    //console.log(bbox);
                    view.fit(bbox, {duration: 4000, padding: [20, 20, 20, 20]});
                    return;
                }
            }
        }
        else {

            var features = Proyectos_vector_source.getFeatures();
            if (features.length === 0)
                return
            var id_proyecto = features[0].get('id');

            $.ajax({
                url: '/obtenerExtensionCapa',
                type: 'POST',
                data: {
                    _token: CSRF_TOKEN,
                    id_proyecto:id_proyecto,
                    id:id_capa
                },
                dataType: 'JSON',
                success: function (data) {
                    //console.log(data);

                    if (data.length > 0) {
                        var left = data[0]['left'];
                        var bottom = data[0]['bottom'];
                        var right = data[0]['right'];
                        var top = data[0]['top'];

                        if (left && bottom && right && top) {
                            var bbox_layer = [
                                parseFloat(left), parseFloat(bottom), parseFloat(right), parseFloat(top)
                            ];
                            //console.log(bbox_layer);
                            var view = map.getView();
                            view.fit(bbox_layer, {duration: 4000, padding: [0, 0, 0, 0]});
                        }
                    }



                    return;

                }
            });

        }
    },

    leyendaCapa: function() {

        $("#leyenda_capa").attr("src",url_legend_part_1 + scale + url_legend_part_2);
        showWinBox("Legend_winbox_obj");

        setTimeout(function(){
            var altura_panel = parseInt($("#Legend_div").css('height')) + 23;
            var ancho_panel = $("#leyenda_capa")[0].naturalWidth + 50;

            //var ancho_panel = Legend_winbox_obj.width;
            if (altura_panel > (window.innerHeight - 100))
                altura_panel = window.innerHeight - 100;

            Legend_winbox_obj.resize(ancho_panel + "px", altura_panel + "px");

            var x_pos = window.innerWidth - LayerSwitcher_winbox_obj.width - 60;
            Legend_winbox_obj.move(x_pos,"bottom");

        }, 1000);
    },

    anadirProducto: function() {

        var features = Proyectos_vector_source.getFeatures();
        if (features.length === 0)
            return
        var id = features[0].get('id');

        var ruta = $("#producto_file_name").val();

        var btn = document.getElementById("anadirProductoProyectoButton");
        btn.disabled = true;


        $.ajax({
            url: '/anadirProducto',
            type: 'POST',
            data: {
                _token: CSRF_TOKEN,
                id_proyecto: id,
                ruta: ruta
            },
            dataType: 'JSON',
            success: function (data) {
                //console.log(data);
            }
        });

    },

    obtenerTrabajosProyecto: function() {
        var features = Proyectos_vector_source.getFeatures();
        if (features.length === 0)
            return
        var id = features[0].get('id');

        if (ObteniendoTrabajosProyecto)
            return;
        ObteniendoTrabajosProyecto = true

        $.ajax({
            url: '/obtenerTrabajosProyecto',
            type: 'POST',
            data: {
                _token: CSRF_TOKEN,
                id_proyecto: id
            },
            dataType: 'JSON',
            success: function (data) {
                if (data.length > 0) {
                    var tipo = 'info';
                    var time = 10000;
                    if (data[0]['estado'] === 'Error') {
                        tipo = 'error';
                        time = 10000;
                    }
                    if (data[0]['estado'] === 'Terminado') {
                        tipo = 'success';
                        time = 10000;
                    }
                    if (data[0]['estado'] === 'Finalizado') {
                        tipo = 'success';
                        time = 10000;

                        //clearInterval(Anadir_producto_interval);

                        var btn = document.getElementById("anadirProductoProyectoButton");
                        btn.disabled = false;

                        hideWinBox('Anadir_producto_winbox_obj');

                        f_obj.cargarProyecto();
                    }



                    var mensaje = data[0]['mensaje'];

                    msg(tipo, mensaje, time, true);
                }
                ObteniendoTrabajosProyecto = false;
            }
        });



    },

    /***********
     FIN - Añadir capas y productos
     ************/



    restablecerCapas: function() {

        // Elimina todas las capas del visor Openlayers
        var layerArray, len, layer;
        layerArray = map.getLayers().getArray(),
            len = layerArray.length;
        while (len > 0){
            layer = layerArray[len-1];
            map.removeLayer(layer);
            len = layerArray.length;
        }

        f_obj.obtenerCapas();
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
                                visible: data[x]['visible_capa'],
                                noSwitcherDelete: true,
                                displayInLayerSwitcher: data[x]['displayinlayerswitcher'],
                                esGrupo: false,
                                leyenda: data[x]['leyenda'],
                                baseLayer: data[x]['baselayer']
                            });

                            if (data[x]['baselayer'] && data[x]['preview'] != null)
                                ol_layer.values_.preview = data[x]['preview'];


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
                                visible: data[x]['visible_capa'],
                                noSwitcherDelete: data[x]['noswitcherdelete'],
                                displayInLayerSwitcher: data[x]['displayinlayerswitcher'],
                                esGrupo: false,
                                leyenda: data[x]['leyenda'],
                                baseLayer: data[x]['baselayer']
                            });

                            if (data[x]['baselayer'] && data[x]['preview'] != null)
                                ol_layer.values_.preview = data[x]['preview'];

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
                                visible: data[x]['visible_capa'],
                                noSwitcherDelete: data[x]['noswitcherdelete'],
                                displayInLayerSwitcher: data[x]['displayinlayerswitcher'],
                                esGrupo: false,
                                leyenda: data[x]['leyenda'],
                                baseLayer: data[x]['baselayer']
                            });

                            if (data[x]['baselayer'] && data[x]['preview'] != null)
                                ol_layer.values_.preview = data[x]['preview'];

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

                if (!findLayerTitle('Proyectos'))
                    map.addLayer(Proyectos_vector_layer);
                if (!findLayerTitle('measureLayers'))
                    map.addLayer(measureLayers);


                actualizaLayerControl();

            }
        });
    },

    tileLoadFunction: function(imageTile, src) {
        const image = imageTile.getImage();
        const timer = setTimeout(() => {
            imageTile.setState(TileState.ERROR);
            console.log('error');
        }, 5000);
        image.addEventListener('load', () => clearTimeout(timer));
        image.addEventListener('error', () => clearTimeout(timer));
        image.src = src;
    },

    queryWMS: function (title, url, id, coordinate) {
        var features = Proyectos_vector_source.getFeatures();
        if (features.length === 0)
            return
        var id_proyecto = features[0].get('id');


        var coord = [parseFloat(coordinate.split(',')[0]), parseFloat(coordinate.split(',')[1])];

        popup_getFeatureInfo.hide();

        $.ajax({
            url: 'getFeatureInfo',
            type: 'POST',
            data: {_token: CSRF_TOKEN, url: url, table_name: id, id_proyecto: id_proyecto},
            dataType: 'JSON',
            success: function (data_arr) {
                var data = data_arr[0];

                var attributes = data_arr[1];
                var attr = [];
                for (var x = 0; x < attributes.length; x++) {
                    attr.push(attributes[x]["attribute"]);
                }


                var content2 = '<div class="text-bold titulo-popup">' + title + ' <span class="titulo-popup-cerrar text-bold" onclick="window.popup_getFeatureInfo.hide()"><i class="fas fa-times"></i></span></div>';
                content2 += '<div class="pl-2 pr-2 pt-2 mb-2">';
                content2 += '<div id="' + id + '">';
                content2 += '<table class="table-bordered results_wms_table" style="width: 100%">';
                var contador = 0;
                $.each(data['features'][0]['properties'], function (idx, value) {
                    contador++;
                    if (attr.includes(idx) || attr.length === 0) {
                        content2 += '<tr><th class="p-1 text-white text-right" style="line-height: 9px;background-color: #212529;">' + idx + '</th><td class="p-1">' + value + '</td></tr>';
                    }
                });


                content2 += '</table>';
                content2 += '</div>';
                content2 += '</div>';
                popup_getFeatureInfo.show(coord, content2);
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
                zoom: 7.5,
                minZoom: 0,
                maxZoom: 22
            })
        });

        f_obj.obtenerCapas();
        //f_obj.obtenerProyectos();




        map.addOverlay(popup_getFeatureInfo);



        medir_bar.addControl(medir_distancia_ctrl);
        medir_bar.addControl(medir_area_ctrl);
        nestedvisor.addControl(medir_ctrl);
        //nestedvisor.addControl(WMSCapabilities_control);

        map.addControl(LayerSwitcher_ctrl);

        map.addControl(visorbar);
        visorbar.setPosition('top-left');
        visorbar.addControl(nestedvisor);

        map.addControl(layerControlBar);
        layerControlBar.setPosition('top-right');



        map.addControl(appbar);
        appbar.setPosition('top-left');
        appbar.addControl(nestedapp);
        nestedapp.addControl(Proyectos_ctrl);

        //nestedapp.addControl(anadir_capa_ctrl)
        //anadir_capa_ctrl.setVisible(false);

        nestedapp.addControl(info_ctrl)
        info_ctrl.setVisible(false);

        nestedapp.addControl(anadir_producto_ctrl)
        anadir_producto_ctrl.setVisible(false);

        tline_ctrl = new Button({
            html: '<i class="fa-solid fa-clock"></i>',
            title: "Línea del tiempo",
            handleClick: function()
            {
                tline.toggle();
            }
        });
        //nestedapp.addControl(tline_ctrl);
        //tline_ctrl.setVisible(false);


        //map.addControl(new LayerSwitcherImageTidop( { mouseover:true }));


        map.addControl(mousePositionControl);
        //map.addControl(legendCtrl);

        map.on('singleclick', function(evt){
            //console.log(map.getLayers().getArray());

           
        });

        /*
        timeline_layers.push(Proyectos_vector_layer);

        // Create timeline
        tline = new Timeline({
            className: 'ol-pointer ol-zoomhover',
            features: timeline_layers,
            //minDate: moment('2000-01-01'),
            maxDate: moment(),
            getFeatureDate: function(l) { return l.get('inicio'); },
            getHTML: function(l) { return l.get('title'); }
        });

        tline.on('scroll', function(e) {
            var layer, dmin = Infinity;
            timeline_layers.forEach(function(l, i) {
                var inicio = moment(l.get('inicio'),'YYYY-MM-DD');
                var fin = moment(l.get('fin'),'YYYY-MM-DD');
                var title = l.get('title');

                var mes = parseInt(e.date.getMonth()) + 1;
                var dia = parseInt(e.date.getDate());
                if (mes < 10)
                    mes = '0' + mes;
                if (dia < 10)
                    dia = '0' + dia;

                var hoy = moment(e.date.getFullYear() + '-' + mes + '-' + dia,'YYYY-MM-DD');
                if (hoy >= inicio && hoy <= fin) {
                    console.log(title + ' visible');
                    layer = findLayerTitle(title);
                    if (layer)
                        layer.setVisible(true);
                }
                else {
                    console.log(title + ' no visible');
                    layer = findLayerTitle(title);
                    if (layer)
                        layer.setVisible(false);
                }

            });

        });
        tline.on('select', function(e) {
            tline.setDate(e.feature);
        });

        map.addControl (tline);
        tline.setDate(moment());
        */




        setTimeout(function(){
            cambiaTamanioVentana();

            var id_p = $("#id_p").val();
            if (id_p != 0) {
                $("#proyectos_select").val(id_p);
                f_obj.cargarProyecto();
                map.removeControl(Proyectos_ctrl);
            }
            else {
                map.getView().fit([-1535725.2091145376, 4178932.4724139427, 982284.3974207981, 5453130.414900448], { duration:3000 });
            }


        }, 500);
        /*
                map.on('precompose', () =>
                    //console.log(map.getView().getResolution() + ' ' + map.getView().getZoom() + ' ' + olProj.transform(map.getView().calculateExtent(map.getSize()),'EPSG:3857','EPSG:4326') )
                    //console.log(map.getView().getZoom())
                    mapScale()
                );

         */
/*
        map.on('rendercomplete', () =>
            //console.log(map.getView().getResolution() + ' ' + map.getView().getZoom() + ' ' + olProj.transform(map.getView().calculateExtent(map.getSize()),'EPSG:3857','EPSG:4326') )
            //console.log(map.getView().getZoom())
            //console.log(map.getView().calculateExtent(map.getSize()))
            mapScale()
        );
*/


        map.on('singleclick', function (evt) {
            popup_getFeatureInfo.hide();

            if (info_ctrl.getActive()) {

                var viewResolution = (map.getView().getResolution());
                var coordinates = evt.coordinate;
                var ly = findLayer("empty_layer");
                var url_empty_layer = ly.getSource().getFeatureInfoUrl(
                    coordinates, viewResolution, projection,
                    {'INFO_FORMAT': 'application/json'});
                //Codificar caracter ' por %27
                var newchar = "%27";

                var url_arr = [];
                var link_arr = [];

                $.each(map.getLayers().getArray(), function (k, v) {
                    var es_visible = v.getVisible();
                    var es_visible_escala = LayerSwitcher_ctrl.testLayerVisibility(v);
                    if (es_visible && es_visible_escala) { // Si el grupo de capas está activado
                        if (v instanceof Group) {
                            var child_layers = v.getLayers();
                            child_layers.forEach(function (ch_layer, index) {
                                var id = ch_layer.get('id');
                                var workspace = ch_layer.get('workspace');
                                var title = ch_layer.get('title');
                                var queryable = ch_layer.get('queryable');
                                var es_visible = ch_layer.getVisible();
                                var es_visible_escala = LayerSwitcher_ctrl.testLayerVisibility(ch_layer);
                                if (es_visible && es_visible_escala && queryable) {
                                    var url_str = url_empty_layer.split("'").join(newchar).replace(/empty_layer/g, id);
                                    url_str = url_str.split("'").join(newchar).replace(/aicedronesdi/g, workspace);
                                    url_arr.push(url_str);
                                    link_arr.push("<p style=\"line-height: 9px\"> <a onclick=\"window.f_obj.queryWMS('" + title + "','" + url_str + "','" + id + "','" + evt.coordinate + "');\" style=\"cursor: pointer;\">  <span style=\"color: #0099ff\">" + title + "</span></a></p>");
                                }
                            });
                        } else {
                            var id = v.get('id');
                            var workspace = v.get('workspace');
                            var title = v.get('title');
                            var queryable = v.get('queryable');
                            if (v.getVisible() && queryable) {
                                var url_str = url_empty_layer.split("'").join(newchar).replace(/empty_layer/g, id);
                                url_str = url_str.split("'").join(newchar).replace(/aicedronesdi/g, workspace);
                                url_arr.push(url_str);
                            }
                        }
                    }

                });

                console.log(url_arr);
                console.log(link_arr);

                //Comprobar el número de resultados de cada petición para mostrarlas o no en el visor
                $.ajax({
                    url: 'isWmsEmpty',
                    type: 'POST',
                    data: {_token: CSRF_TOKEN, url_arr: url_arr},
                    dataType: 'JSON',
                    success: function (data) {
                        var content = '<div class="text-bold titulo-popup">Información <span class="titulo-popup-cerrar text-bold" onclick="window.popup_getFeatureInfo.hide()"><i class="fas fa-times"></i></span></div>';
                        content += '<div class="pl-2 pr-2 pt-3 mb-1">';
                        for (var x = 0; x < data.length; x++) {
                            content += link_arr[data[x]];
                        }
                        content += '</div>';
                        if (data.length > 0)
                            popup_getFeatureInfo.show(evt.coordinate, content);
                    }
                });



            }
        });

        Anadir_producto_interval = setInterval(f_obj.obtenerTrabajosProyecto, 3000);

    },

};
export default f_obj;

window.f_obj = f_obj;


window.map = map;
window.popup_getFeatureInfo = popup_getFeatureInfo;