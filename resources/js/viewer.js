import 'ol/ol.css';
import {Map,View} from 'ol';
import TileLayer from 'ol/layer/WebGLTile';
import Group from 'ol/layer/Group';
import * as olProj from 'ol/proj.js';
import OSM from 'ol/source/OSM';

// Token que genera Laravel en la cabecera del documento html y que debemos incluir en cada petición POST que realicemos al backend.
var CSRF_TOKEN = document.querySelector('meta[name=csrf-token]').content;

// Definición de la proyección EPSG:3857
const projection = 'EPSG:3857';

// Variable donde se va a almacenar la instancia del objeto map de OpenLayers.
var map;

// Creación de un grupo de capas
var baseLayers = new Group(
    {   id: 'baseLayers',
        title: 'Baselayers',
        openInLayerSwitcher: true,
        noSwitcherDelete: true,
        layers:
            [
                // Creación de capa de OpenStreetMap
                new TileLayer({
                    id: 'osm',
                    title: "OpenStreetMap",
                    name: 'osm',
                    opacity: 1,
                    source: new OSM()
                })
            ]
    });

// Evento que se ejcuta cuando el contenido de la página está totalmente cargado.
document.addEventListener("DOMContentLoaded", function(event) {
    f_obj.resizeWindow();
    f_obj.inicio();
});

var f_obj = {

    // Función que se ejecuta cada vez que redimensionamos la ventana del navegador para que el mapa se ajuste automáticamente.
    resizeWindow: function() {
        var h = document.documentElement.clientHeight;
        $("#map").css('height',(h-57) + 'px');
        if (map)
            map.updateSize();
    },

    inicio: function() {
        // Añadimos un 'Listener' a la ventana del navegador para que cuando se redimensione llame a la función 'resizeWindow'
        window.addEventListener("resize", f_obj.resizeWindow);

        // Creamos una instancia del objeto map
        map = new Map({
            target: 'map',
            layers: [baseLayers],
            view: new View({
                projection: projection,
                center: olProj.transform([-3.627411, 40.007395], 'EPSG:4326', 'EPSG:3857'),
                zoom: 7.5,
                maxZoom: 22,
                minZoom: 1
            })
        });

        // Muestra información de la funcionalidad de este ejemplo al cargar la página
        setTimeout(function(){
            $.blockUI({ message: $('#infoDiv'), css: { width: '500px' } });
            $('#infoButton').click(function() {
                $.unblockUI();
                return false;
            });
        }, 500);

    },

};
export default f_obj;
window.f_obj = f_obj;
window.map = map;