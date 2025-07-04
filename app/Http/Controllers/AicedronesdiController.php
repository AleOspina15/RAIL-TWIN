<?php

namespace App\Http\Controllers;

use App\Jobs\AnadirCapa;
use App\Jobs\AnadirProducto;
use App\Models\Proyecto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;


class AicedronesdiController extends Controller
{
    private $isMobile;

    public function __construct() {
        $this->middleware('auth');

        $agent = new \Jenssegers\Agent\Agent;

        $isMobile = $agent->isMobile();
        $isDesktop = $agent->isDesktop();
        $isTablet = $agent->isTablet();

        if ($isMobile || $isTablet)
            $this->isMobile = true;
        if ($isDesktop)
            $this->isMobile = false;
    }


    public function visor() {
        $json = '{"view":{"center":[-3.627411,40.007395],"zoom":7},"tools":[{"name":"Catálogo"}],"layers":[{"id":6,"name":"Mapas Base","type":"group","layers":[{"id":116,"name":"OpenStreetMap","type":"osmLayer","visible":true}]}]}';

        $id = 0;
        if ($this->isMobile)
            return view('visor',compact('json','id'));
        else
            return view('visor',compact('json','id'));
    }

    public function visor2() {

        $json = '{"view":{"center":[-3.627411,40.007395],"zoom":7},"tools":[{"name":"Catálogo"}],"layers":[{"id":6,"name":"Mapas Base","type":"group","layers":[{"id":116,"name":"OpenStreetMap","type":"osmLayer","visible":true}]}]}';

        $id = 0;
        if ($this->isMobile)
            return view('visor2',compact('json','id'));
        else
            return view('visor2',compact('json','id'));
    }

    public function visorProyecto($id) {
        $json = '{"view":{"center":[-3.627411,40.007395],"zoom":7},"tools":[{"name":"Catálogo"}],"layers":[{"id":6,"name":"Mapas Base","type":"group","layers":[{"id":116,"name":"OpenStreetMap","type":"osmLayer","visible":true}]}]}';

        $str_sql = "SELECT *";
        $str_sql .= " FROM sch_aicedronesdi.project WHERE id = $id";
        $proyecto = DB::select($str_sql)[0];
        $workspace = $proyecto->workspace;
        $nombre = $proyecto->nombre;

        return view('visorProyecto',compact('json','id','nombre'));
    }


    public function proyectos_() {
        return redirect()->route('proyectos.index');
    }



    public function obtenerCapas(Request $request)
    {
        $publica_str = "";
        $str_sql = "SELECT groups.id as id_grupo, layers.id as id_capa, groups.nombre as nombre_grupo, layers.titulo,
                        layers.min_zoom as min_zoom_capa, layers.max_zoom as max_zoom_capa,
                        groups.min_zoom as min_zoom_grupo, groups.max_zoom as max_zoom_grupo,
                        layers.posicion AS posicion_capa, groups.posicion AS posicion_grupo,
                        layers.visible as visible_capa, groups.visible as visible_grupo
                        ,*
                        FROM sch_viewer.groups, sch_viewer.layers
                        WHERE groups.id = layers.id_grupo
                         $publica_str AND layers.activa IS TRUE
                        ORDER BY groups.posicion DESC, layers.posicion DESC";
        $results = DB::select($str_sql);

        return [$results];
    }

    /*
     * Proyectos
     */

    public function guardarProyecto(Request $request) {
        $coordinates = $request->input('coordinates');
        $nombre = $request->input('nombre');
        $inicio = $request->input('inicio');
        $fin = $request->input('fin');

        $polygon_str = "ST_Transform(ST_MakePolygon(ST_MakeLine(ARRAY[";
        for ($x=0;$x<count($coordinates[0]);$x++) {
            $polygon_str .= "ST_SetSRID(ST_MakePoint(".$coordinates[0][$x][0].", ".$coordinates[0][$x][1]."),3857)";
            if ($x<count($coordinates[0])-1)
                $polygon_str .= ",";
        }
        $polygon_str .= "])),4326)";

        $custom_request = new Request([
            'id_usuario' => Auth::user()->id,
            'nombre' => $nombre,
            'inicio' => $inicio,
            'fin' => $fin,
            'estado' => 'nuevo'
        ]);

        $proyecto = Proyecto::create($custom_request->all());
        $id_proyecto = $proyecto->id;

        $workspace = "proyecto_$id_proyecto";

        $str_sql = "UPDATE sch_aicedronesdi.project SET workspace = '$workspace', geom = $polygon_str WHERE id = $id_proyecto";
        $res_insert = DB::select($str_sql);

        // Carpeta del proyecto en el gestor de archivos
        $carpeta_proyecto = $nombre;
        $carpeta_proyecto = str_replace("á","a",$carpeta_proyecto);
        $carpeta_proyecto = str_replace("é","e",$carpeta_proyecto);
        $carpeta_proyecto = str_replace("í","i",$carpeta_proyecto);
        $carpeta_proyecto = str_replace("ó","o",$carpeta_proyecto);
        $carpeta_proyecto = str_replace("ú","u",$carpeta_proyecto);
        $carpeta_proyecto = str_replace("ñ","n",$carpeta_proyecto);
        $carpeta_proyecto = str_replace("ä","a",$carpeta_proyecto);
        $carpeta_proyecto = str_replace("ë","e",$carpeta_proyecto);
        $carpeta_proyecto = str_replace("ï","i",$carpeta_proyecto);
        $carpeta_proyecto = str_replace("ö","o",$carpeta_proyecto);
        $carpeta_proyecto = str_replace("ü","u",$carpeta_proyecto);
        $caracteres = array("!","#","$","%","&","'","*","+","-","=","?","^","`","{","|","}","~","@",".","[","]","."," ");
        $carpeta_proyecto = str_replace($caracteres,"_",$carpeta_proyecto);
        \Storage::disk('aicedronesdi_filemanager')->makeDirectory("Proyectos/$carpeta_proyecto");
        \Storage::disk('aicedronesdi_filemanager')->makeDirectory("Proyectos/$carpeta_proyecto/Documentación");
        \Storage::disk('aicedronesdi_filemanager')->makeDirectory("Proyectos/$carpeta_proyecto/Productos");

        // Crear espacio de trabajo en Geoserver
        $command = 'curl -u admin:' . env('DB_PASSWORD') . ' -XPOST '.env('GS_URL').'/rest/workspaces -H  "accept: application/json" -H  "content-type: application/xml" -d "<workspace><name>' . $workspace . '</name><enabled>true</enabled></workspace>"';
        exec($command, $out, $ret);

        // Habilitar servicio WMS,WMTS,WFS,WCS

        $command = 'curl -u admin:' . env('DB_PASSWORD') . ' -XPUT '.env('GS_URL').'/rest/services/wms/workspaces/' . $workspace . '/settings -H  "accept: application/json" -H  "content-type: application/xml" -d "<wms><enabled>true</enabled></wms>"';
        exec($command, $out, $ret);

        $command = 'curl -u admin:' . env('DB_PASSWORD') . ' -XPUT '.env('GS_URL').'/rest/services/wmts/workspaces/' . $workspace . '/settings -H  "accept: application/json" -H  "content-type: application/xml" -d "<wmts><enabled>true</enabled></wmts>"';
        exec($command, $out2, $ret2);

        $command = 'curl -u admin:' . env('DB_PASSWORD') . ' -XPUT '.env('GS_URL').'/rest/services/wfs/workspaces/' . $workspace . '/settings -H  "accept: application/json" -H  "content-type: application/xml" -d "<wfs><enabled>true</enabled></wfs>"';
        exec($command, $out3, $ret3);

        $command = 'curl -u admin:' . env('DB_PASSWORD') . ' -XPUT '.env('GS_URL').'/rest/services/wcs/workspaces/' . $workspace . '/settings -H  "accept: application/json" -H  "content-type: application/xml" -d "<wcs><enabled>true</enabled></wcs>"';
        exec($command, $out4, $ret4);

        // Crear esquema
        $str_sql = "CREATE SCHEMA IF NOT EXISTS \"$workspace\" AUTHORIZATION postgres";
        $create_schema = DB::select($str_sql);

        // Crear almacén de datos postgis en Geoserver
        $ip_server = env('APP_HOST');
        $service = env('GS_URL');
        $request_gs = "/rest/workspaces/$workspace/datastores";
        $url = $service . $request_gs;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        curl_setopt($ch, CURLOPT_POST, True);
        $passwordStr = "admin:".env('DB_PASSWORD');
        curl_setopt($ch, CURLOPT_USERPWD, $passwordStr);
        curl_setopt($ch, CURLOPT_HTTPHEADER,
            array("Content-type: text/xml"));
        $xmlStr = '<dataStore><name>' . $workspace . '</name>';
        $xmlStr .= '<connectionParameters>';
        $xmlStr .= '<host>postgres</host>';
        $xmlStr .= '<port>5432</port>';
        $xmlStr .= '<database>aicedronesdi</database>';
        $xmlStr .= '<schema>' . $workspace . '</schema>';
        $xmlStr .= '<user>postgres</user>';
        $xmlStr .= '<passwd>'.env('DB_PASSWORD').'</passwd>';
        $xmlStr .= '<dbtype>postgis</dbtype>';
        $xmlStr .= '</connectionParameters>';
        $xmlStr .= '</dataStore>';
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlStr);
        $successCode = 201;
        $buffer = curl_exec($ch);

        // Crear tabla grupos
        $str_sql = "CREATE TABLE IF NOT EXISTS $workspace.groups
        (
            id bigserial,
            nombre text NOT NULL,
            descripcion text,
            visible boolean NOT NULL DEFAULT true,
            posicion integer,
            min_zoom numeric DEFAULT 12,
            max_zoom numeric DEFAULT 28,
            grupo_sistema boolean DEFAULT false,
            openinlayerswitcher boolean DEFAULT false,
            created_at timestamp without time zone DEFAULT now(),
            updated_at timestamp without time zone,
            CONSTRAINT pk_grupos PRIMARY KEY (id)
        )
        TABLESPACE pg_default";
        $create = DB::select($str_sql);

        // Crear tabla capas
        $str_sql = "CREATE TABLE IF NOT EXISTS $workspace.layers
        (
            id bigserial,
            id_grupo integer NOT NULL,
            titulo text NOT NULL,
            workspace text NOT NULL,
            name text NOT NULL,
            style text,
            visible boolean NOT NULL DEFAULT false,
            posicion integer DEFAULT 1,
            queryable boolean DEFAULT true,
            origen text,
            min_zoom numeric DEFAULT 12,
            max_zoom numeric DEFAULT 28,
            capa_sistema boolean DEFAULT false,
            publica boolean DEFAULT false,
            displayinlayerswitcher boolean NOT NULL DEFAULT true,
            created_at timestamp without time zone NOT NULL DEFAULT now(),
            updated_at timestamp without time zone,
            activa boolean NOT NULL DEFAULT true,
            inicio date DEFAULT '$inicio',
            fin date DEFAULT '$fin',
            tipo text DEFAULT 'wms',
            noswitcherdelete boolean NOT NULL DEFAULT true,
            descargable boolean NOT NULL DEFAULT false,
            tline boolean NOT NULL DEFAULT true,
            leyenda text,
            CONSTRAINT pk_capas PRIMARY KEY (id),
            CONSTRAINT fk_capas_grupos FOREIGN KEY (id_grupo)
                REFERENCES $workspace.groups (id) MATCH SIMPLE
                ON UPDATE CASCADE
                ON DELETE CASCADE
        )
        TABLESPACE pg_default";
        $create = DB::select($str_sql);

        // Crear tabla productos
        $str_sql = "CREATE TABLE IF NOT EXISTS $workspace.products
        (
            id_capa bigint NOT NULL,
            position integer,
            name text,
            crs text,
            product_date date,
            type text,
            uav text,
            sensors text,
            description text,
            CONSTRAINT pk_productos PRIMARY KEY (id_capa),
            CONSTRAINT fk_productos_capas FOREIGN KEY (id_capa)
                REFERENCES $workspace.layers (id) MATCH SIMPLE
                ON UPDATE CASCADE
                ON DELETE CASCADE
        )
        TABLESPACE pg_default";
        $create = DB::select($str_sql);

        // Crear tabla enlaces
        $str_sql = "CREATE TABLE IF NOT EXISTS $workspace.links
        (
            id bigserial,
            id_capa bigint NOT NULL,
            name text,
            type text,
            url text,
            CONSTRAINT pk_enlaces PRIMARY KEY (id,id_capa),
            CONSTRAINT fk_enlaces_capas FOREIGN KEY (id_capa)
                REFERENCES $workspace.layers (id) MATCH SIMPLE
                ON UPDATE CASCADE
                ON DELETE CASCADE
        )
        TABLESPACE pg_default";
        $create = DB::select($str_sql);

        // Crear tabla attributes
        $str_sql = "CREATE TABLE IF NOT EXISTS $workspace.attributes
        (
            id_capa bigint NOT NULL,
            attribute text NOT NULL,
            CONSTRAINT pk_attributes PRIMARY KEY (id_capa,attribute),
            CONSTRAINT fk_attribute_capas FOREIGN KEY (id_capa)
                REFERENCES $workspace.layers (id) MATCH SIMPLE
                ON UPDATE CASCADE
                ON DELETE CASCADE
        )
        TABLESPACE pg_default";
        $create = DB::select($str_sql);

        /*
        ;
         */

        $str_sql = "INSERT INTO $workspace.groups(nombre, visible, posicion, min_zoom, max_zoom, grupo_sistema,openinlayerswitcher)";
        $str_sql .= " VALUES ('$nombre',TRUE,1,0,25,TRUE,TRUE)";
        $insert = DB::select($str_sql);

        \Storage::disk('local_geoserver')->makeDirectory('proyectos/'.$workspace);
        \Storage::disk('local_geoserver')->makeDirectory('proyectos/'.$workspace.'/uploads');


        return [];
    }

    public function actualizarProyecto(Request $request) {
        $coordinates = $request->input('coordinates');
        $nombre = $request->input('nombre');
        $inicio = $request->input('inicio');
        $fin = $request->input('fin');
        $id_proyecto = $request->input('id_proyecto');

        $str_sql = "SELECT * FROM sch_aicedronesdi.project WHERE id = $id_proyecto";
        $proyecto = DB::select($str_sql);
        $nombre_old = $proyecto[0]->nombre;
        $workspace = $proyecto[0]->workspace;

        $polygon_str = "ST_Transform(ST_MakePolygon(ST_MakeLine(ARRAY[";
        for ($x=0;$x<count($coordinates[0]);$x++) {
            $polygon_str .= "ST_SetSRID(ST_MakePoint(".$coordinates[0][$x][0].", ".$coordinates[0][$x][1]."),3857)";
            if ($x<count($coordinates[0])-1)
                $polygon_str .= ",";
        }
        $polygon_str .= "])),4326)";

        $str_sql = "UPDATE sch_aicedronesdi.project SET nombre = '$nombre', geom = $polygon_str WHERE id = $id_proyecto";
        $res_insert = DB::select($str_sql);

        if ($nombre != $nombre_old) {
            // Carpeta del proyecto en el gestor de archivos
            $carpeta_proyecto_old = $nombre_old;
            $carpeta_proyecto_old = str_replace("á", "a", $carpeta_proyecto_old);
            $carpeta_proyecto_old = str_replace("é", "e", $carpeta_proyecto_old);
            $carpeta_proyecto_old = str_replace("í", "i", $carpeta_proyecto_old);
            $carpeta_proyecto_old = str_replace("ó", "o", $carpeta_proyecto_old);
            $carpeta_proyecto_old = str_replace("ú", "u", $carpeta_proyecto_old);
            $carpeta_proyecto_old = str_replace("ñ", "n", $carpeta_proyecto_old);
            $carpeta_proyecto_old = str_replace("ä", "a", $carpeta_proyecto_old);
            $carpeta_proyecto_old = str_replace("ë", "e", $carpeta_proyecto_old);
            $carpeta_proyecto_old = str_replace("ï", "i", $carpeta_proyecto_old);
            $carpeta_proyecto_old = str_replace("ö", "o", $carpeta_proyecto_old);
            $carpeta_proyecto_old = str_replace("ü", "u", $carpeta_proyecto_old);
            $caracteres = array("!", "#", "$", "%", "&", "'", "*", "+", "-", "=", "?", "^", "`", "{", "|", "}", "~", "@", ".", "[", "]", ".", " ", "(", ")");
            $carpeta_proyecto_old = str_replace($caracteres, "_", $carpeta_proyecto_old);

            $carpeta_proyecto = $nombre;
            $carpeta_proyecto = str_replace("á", "a", $carpeta_proyecto);
            $carpeta_proyecto = str_replace("é", "e", $carpeta_proyecto);
            $carpeta_proyecto = str_replace("í", "i", $carpeta_proyecto);
            $carpeta_proyecto = str_replace("ó", "o", $carpeta_proyecto);
            $carpeta_proyecto = str_replace("ú", "u", $carpeta_proyecto);
            $carpeta_proyecto = str_replace("ñ", "n", $carpeta_proyecto);
            $carpeta_proyecto = str_replace("ä", "a", $carpeta_proyecto);
            $carpeta_proyecto = str_replace("ë", "e", $carpeta_proyecto);
            $carpeta_proyecto = str_replace("ï", "i", $carpeta_proyecto);
            $carpeta_proyecto = str_replace("ö", "o", $carpeta_proyecto);
            $carpeta_proyecto = str_replace("ü", "u", $carpeta_proyecto);
            $caracteres = array("!", "#", "$", "%", "&", "'", "*", "+", "-", "=", "?", "^", "`", "{", "|", "}", "~", "@", ".", "[", "]", ".", " ", "(", ")");
            $carpeta_proyecto = str_replace($caracteres, "_", $carpeta_proyecto);
            \Storage::disk('aicedronesdi_filemanager')->move("Proyectos/$carpeta_proyecto_old","Proyectos/$carpeta_proyecto");
        }







        return [];
    }

    public function obtenerProyectos(Request $request) {
        $str_sql = "SELECT ST_AsText(ST_Transform(geom,3857)) AS coord, * FROM sch_aicedronesdi.project";
        $str_sql .= " ORDER BY nombre ASC";
        $proyectos = DB::select($str_sql);

        return $proyectos;
    }

    public function actualizarProyecto_old(Request $request) {

        $coordinates = $request->input('coordinates');
        $id = $request->input('id');

        $polygon_str = "ST_Transform(ST_MakePolygon(ST_MakeLine(ARRAY[";
        for ($x=0;$x<count($coordinates[0]);$x++) {
            $polygon_str .= "ST_SetSRID(ST_MakePoint(".$coordinates[0][$x][0].", ".$coordinates[0][$x][1]."),3857)";
            if ($x<count($coordinates[0])-1)
                $polygon_str .= ",";
        }
        $polygon_str .= "])),4326)";

        $str_sql = "UPDATE sch_aicedronesdi.project SET geom = $polygon_str WHERE id = $id";
        $res_insert = DB::select($str_sql);

        /*
        $query = "DELETE FROM sch_aicedronesdi.way WHERE id_proyecto = $id";
        $res_delete = DB::select($query);
        $query = "DELETE FROM sch_aicedronesdi.vertex WHERE id_proyecto = $id";
        $res_delete = DB::select($query);

        $query = "INSERT INTO sch_aicedronesdi.way ";
        $query .= " SELECT $id, id, osm_id, osm_name, osm_meta, osm_source_id, osm_target_id, clazz, flags, source, target, km, kmh, cost, reverse_cost, x1, y1, x2, y2, geom_way";
        $query .= " FROM sch_pgrouting.osm_2po_4pgr";
        $query .= " WHERE ST_Intersects(geom_way,$polygon_str) OR ST_Contains($polygon_str,geom_way)";
        $res_insert = DB::select($query);

        $query = "INSERT INTO sch_aicedronesdi.vertex ";
        $query .= " SELECT $id, id, clazz, osm_id, osm_name, ref_count, restrictions, geom_vertex";
        $query .= " FROM sch_pgrouting.osm_2po_vertex";
        $query .= " WHERE ST_Intersects(geom_vertex,$polygon_str) OR ST_Contains($polygon_str,geom_vertex)";
        $res_insert = DB::select($query);
        */

        return [];
    }

    public function eliminarProyecto(Request $request) {
        $id = $request->input('id');

        $str_sql = "SELECT * FROM sch_aicedronesdi.project WHERE id = $id";
        $res = DB::select($str_sql);
        $workspace = $res[0]->workspace;

        // Eliminar espacio de trabajo de Geoserver
        $command = 'curl -u admin:'.env('GS_PASSWORD').' -XDELETE '.env('GS_URL').'/rest/workspaces/' . $workspace . '?recurse=true -H  "accept: application/json" -H  "content-type: application/json"';
        exec($command, $out, $ret);

        // Eliminar esquema de PostgreSQL
        $str_sql = "DROP SCHEMA IF EXISTS \"$workspace\" CASCADE";
        $drop = DB::select($str_sql);

        // Eliminar carpeta de datos de Geoserver
        $command = 'rm -r /var/www/app/docker/geoserver/data/proyectos/'.$workspace;
        exec($command, $out, $ret);

        $str_sql = "DELETE FROM sch_aicedronesdi.project WHERE id = $id";
        $res = DB::select($str_sql);

        return [];
    }

    public function cargarProyecto(Request $request)
    {
        $id_proyecto = $request->input('id_proyecto');

        $str_sql = "SELECT ST_AsText(ST_Transform(geom,3857)) AS coord,* FROM sch_aicedronesdi.project WHERE id = $id_proyecto";
        $proyecto = DB::select($str_sql);

        $workspace = $proyecto[0]->workspace;


        $publica_str = "";
        $str_sql = "SELECT groups.id as id_grupo, layers.id as id_capa, groups.nombre as nombre_grupo, layers.titulo,
                        layers.min_zoom as min_zoom_capa, layers.max_zoom as max_zoom_capa,
                        groups.min_zoom as min_zoom_grupo, groups.max_zoom as max_zoom_grupo,
                        layers.posicion AS posicion_capa, groups.posicion AS posicion_grupo,
                        layers.visible as visible_capa, groups.visible as visible_grupo
                        ,*
                        FROM $workspace.groups, $workspace.layers
                        WHERE groups.id = layers.id_grupo
                         $publica_str AND layers.activa IS TRUE
                        ORDER BY groups.posicion DESC, layers.posicion DESC";
        $capas = DB::select($str_sql);

        $publica_str = "";
        $str_sql = "SELECT groups.id as id_grupo, layers.id as id_capa, groups.nombre as nombre_grupo, layers.titulo,
                        layers.min_zoom as min_zoom_capa, layers.max_zoom as max_zoom_capa,
                        groups.min_zoom as min_zoom_grupo, groups.max_zoom as max_zoom_grupo,
                        layers.posicion AS posicion_capa, groups.posicion AS posicion_grupo,
                        layers.visible as visible_capa, groups.visible as visible_grupo
                        ,*
                        FROM sch_viewer.groups, sch_viewer.layers
                        WHERE groups.id = layers.id_grupo
                         $publica_str AND layers.activa IS TRUE
                        ORDER BY groups.posicion DESC, layers.posicion DESC";
        $capas_globales = DB::select($str_sql);

        $command = 'docker exec -it aicedrone_sdi-php-1 chown -R www-data:www-data /var/www/app/public/potree';
        exec($command, $out, $ret);

        return [$capas,$proyecto,$capas_globales];
    }



    /*
     * FIN - Proyectos
     */

    /*
     * Añadir capa a un proyecto
     */

    public function anadirCapa(Request $request) {
        $id_proyecto = $request->input('id');
        $nombreCapa = $request->input('nombreCapa');
        $inicio = $request->input('inicio');
        $fin = $request->input('fin');
        $nombreCapaGeoserver = $request->input('nombreCapaGeoserver');
        $ruta = $request->input('ruta');
        $extension = explode(".",$ruta)[1];

        $str_sql = "SELECT * FROM sch_aicedronesdi.project WHERE id = $id_proyecto";
        $res = DB::select($str_sql);
        $workspace = $res[0]->workspace;
        $nombre = $res[0]->nombre;

        $carpeta_proyecto = $nombre;
        $carpeta_proyecto = str_replace("á", "a", $carpeta_proyecto);
        $carpeta_proyecto = str_replace("é", "e", $carpeta_proyecto);
        $carpeta_proyecto = str_replace("í", "i", $carpeta_proyecto);
        $carpeta_proyecto = str_replace("ó", "o", $carpeta_proyecto);
        $carpeta_proyecto = str_replace("ú", "u", $carpeta_proyecto);
        $carpeta_proyecto = str_replace("ñ", "n", $carpeta_proyecto);
        $carpeta_proyecto = str_replace("ä", "a", $carpeta_proyecto);
        $carpeta_proyecto = str_replace("ë", "e", $carpeta_proyecto);
        $carpeta_proyecto = str_replace("ï", "i", $carpeta_proyecto);
        $carpeta_proyecto = str_replace("ö", "o", $carpeta_proyecto);
        $carpeta_proyecto = str_replace("ü", "u", $carpeta_proyecto);
        $caracteres = array("!", "#", "$", "%", "&", "'", "*", "+", "-", "=", "?", "^", "`", "{", "|", "}", "~", "@", ".", "[", "]", ".", " ");
        $carpeta_proyecto = str_replace($caracteres, "_", $carpeta_proyecto);


        // Generar el nombre del archivo(s) para que no contenga espacios ni caracteres no válidos. Cogemos el nombre de $nombreCapaGeoserver
        $changed_complete_file_name = "$nombreCapaGeoserver.$extension";

        $command = 'cp "/var/www/app/docker/php/aicedronesdi_filemanager/Proyectos/'.$carpeta_proyecto.'/'.$ruta.'" /var/www/app/docker/geoserver/data/proyectos/'.$workspace.'/'.$changed_complete_file_name;
        exec($command, $out, $ret);

        $command = 'chmod -R 777 /var/www/app/docker/geoserver/data';
        exec($command, $out, $ret);

        if ($extension === 'tif') {

            $custom_request = [
                'id_proyecto' => $id_proyecto,
                'nombreCapa' => $nombreCapa,
                'nombreCapaGeoserver' => $nombreCapaGeoserver,
                'changed_complete_file_name' => $changed_complete_file_name,
                'extension' => $extension
            ];

            dispatch(new AnadirCapa($custom_request));

            // php artisan queue:work --stop-when-empty
            $command = 'cd /var/www/app && php artisan queue:work --stop-when-empty > /dev/null 2>&1 &';
            exec($command, $out, $ret);

            $str_sql = "INSERT INTO $workspace.layers(id_grupo, titulo, workspace, name, style, visible, posicion, queryable, origen, min_zoom, max_zoom, capa_sistema, publica, displayinlayerswitcher, activa,inicio,fin,tipo,noswitcherdelete,descargable)";
            $str_sql .= " VALUES (1,'$nombreCapa','$workspace','$nombreCapaGeoserver',NULL,TRUE,1,TRUE,'tif',0,25,FALSE,TRUE,TRUE,TRUE,'$inicio','$fin','wms',FALSE,TRUE)";
            $res = DB::select($str_sql);

            return [$out];
        }

        if ($extension === 'zip') {

            $custom_request = [
                'id_proyecto' => $id_proyecto,
                'nombreCapa' => $nombreCapa,
                'nombreCapaGeoserver' => $nombreCapaGeoserver,
                'changed_complete_file_name' => $changed_complete_file_name,
                'extension' => $extension
            ];

            dispatch(new AnadirCapa($custom_request));

            // php artisan queue:work --stop-when-empty
            $command = 'cd /var/www/app && php artisan queue:work --stop-when-empty > /dev/null 2>&1 &';
            exec($command, $out, $ret);

            $str_sql = "INSERT INTO $workspace.layers(id_grupo, titulo, workspace, name, style, visible, posicion, queryable, origen, min_zoom, max_zoom, capa_sistema, publica, displayinlayerswitcher, activa,inicio,fin,tipo,noswitcherdelete,descargable)";
            $str_sql .= " VALUES (1,'$nombreCapa','$workspace','$nombreCapaGeoserver',NULL,TRUE,1,TRUE,'shp',0,25,FALSE,TRUE,TRUE,TRUE,'$inicio','$fin','wms',FALSE,TRUE)";
            $res = DB::select($str_sql);

            return [$out];
        }



        return [];
    }

    public function descargarCapa(Request $request) {
        $id_proyecto = $request->input('id_proyecto');
        $id = $request->input('id');

        $str_sql = "SELECT * FROM sch_aicedronesdi.project WHERE id = $id_proyecto";
        $res = DB::select($str_sql);
        $workspace = $res[0]->workspace;

        $str_sql = "SELECT * FROM $workspace.layers WHERE name = '$id'";
        $capa = DB::select($str_sql);

        $nombre = $capa[0]->name;
        $origen = $capa[0]->origen;

        if ($origen === 'shp') {
            $command = 'rm /var/www/app/public/descargas/' . $nombre . '.zip';
            exec($command, $out, $ret);

            $command = 'mkdir /var/www/app/public/descargas/' . $nombre;
            exec($command, $out, $ret);

            $str_sql = "SELECT * FROM $workspace.$nombre";
            $command = 'pgsql2shp -f /var/www/app/public/descargas/' . $nombre . '/' . $nombre . '.shp -u postgres -P '.env('DB_PASSWORD').' -h '.env('DB_HOST').' -p 5432 ' . env('DB_DATABASE') . ' "'.$str_sql.'"';
            exec($command, $out, $ret);

            $command = 'zip -j -r /var/www/app/public/descargas/' . $nombre . '.zip /var/www/app/public/descargas/' . $nombre;
            exec($command, $out, $ret);

            $command = 'rm -r /var/www/app/public/descargas/' . $nombre;
            exec($command, $out, $ret);

            return ['http://' . env('APP_HOST') . '/descargas/' . $nombre . '.zip'];
        }

        if ($origen === 'tif') {
            $command = 'cp /var/www/app/docker/geoserver/data/proyectos/'.$workspace.'/' . $nombre . '.tif /var/www/app/public/descargas/' . $nombre . '.tif';
            exec($command, $out, $ret);

            return ['http://' . env('APP_HOST') . '/descargas/' . $nombre . '.tif'];
        }
    }

    public function eliminarCapa(Request $request) {
        $id_proyecto = $request->input('id_proyecto');
        $id = $request->input('id');

        $str_sql = "SELECT * FROM sch_aicedronesdi.project WHERE id = $id_proyecto";
        $res = DB::select($str_sql);
        $workspace = $res[0]->workspace;

        $str_sql = "SELECT * FROM $workspace.layers WHERE name = '$id'";
        $capa = DB::select($str_sql);

        //return $capa;

        $nombre = $capa[0]->name;
        $origen = $capa[0]->origen;
        $tipo = $capa[0]->tipo;
        $style = $capa[0]->style;

        $ip_server = env('APP_HOST');

        if ($origen === 'tif') {
            /*
             * Elimina la capa de Geoserver
             */
            $service = "http://$ip_server:8080/geoserver/";
            $request_gs = "rest/workspaces/$workspace/coveragestores/" . $nombre . ".xml?recurse=true";
            $url = $service . $request_gs;
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_VERBOSE, true);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
            $passwordStr = "admin:".env('DB_PASSWORD');
            curl_setopt($ch, CURLOPT_USERPWD, $passwordStr);
            $buffer = curl_exec($ch);
            /*
             * Fin Elimina la capa de Geoserver
             */

            $command = 'chmod -R 777 /var/www/app/docker/geoserver/data';
            exec($command, $out, $ret);

            $command = 'rm /var/www/app/docker/geoserver/data/proyectos/'.$workspace.'/'.$nombre.'.tif';
            exec($command, $out, $ret);

            /*
             * Elimina el archivo del disco tif
             */
            // \Storage::disk('local_geoserver')->delete('data/proyectos/'.$workspace.'/'.$nombre.'.tif');
            /*
             * Fin Elimina el archivo del disco tif
             */
        }

        if ($origen === 'shp') {
            /*
             * Eliminar capa de Geoserver
             */
            $only_name = $nombre;

            //Borra Layer
            $service = "http://$ip_server:8080/geoserver/";
            $request_gs = "rest/layers/$workspace:" . $only_name . ".xml";
            $url = $service . $request_gs;
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_VERBOSE, true);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
            $passwordStr = "admin:".env('DB_PASSWORD');
            curl_setopt($ch, CURLOPT_USERPWD, $passwordStr);
            $buffer = curl_exec($ch);

            //Borra Featuretype
            $service = "http://$ip_server:8080/geoserver/";
            $request_gs = "rest/workspaces/$workspace/datastores/$workspace/featuretypes/$only_name?recurse=true";
            $url = $service . $request_gs;
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_VERBOSE, true);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
            $passwordStr = "admin:".env('DB_PASSWORD');
            curl_setopt($ch, CURLOPT_USERPWD, $passwordStr);
            $buffer = curl_exec($ch);

            //Borra Estilo
            if ($style) {
                $service = "http://$ip_server:8080/geoserver/";
                $request_gs = "rest/workspaces/$workspace/styles/$style?recurse=true&purge=true";
                $url = $service . $request_gs;
                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_VERBOSE, true);
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
                $passwordStr = "admin:" . env('DB_PASSWORD');
                curl_setopt($ch, CURLOPT_USERPWD, $passwordStr);
                $buffer = curl_exec($ch);
            }

            /*
             * Fin Eliminar capa de Geoserver
             */


            /*
             * Elimina tabla de Postgres
             */
            $str_sql = "DROP TABLE \"$workspace\".$only_name";
            $res = DB::select($str_sql);
            /*
             * Fin Elimina tabla de Postgres
             */

        }

        if ($tipo === 'potree') {
            $command = 'chmod -R 777 /var/www/app/public/potree';
            exec($command, $out, $ret);
            $command = 'chown -R www-data:www-data /var/www/app/public/potree';
            exec($command, $out, $ret);

            $command = 'rm -r /var/www/app/public/potree/' . $workspace . '_' . $nombre;
            exec($command, $out, $ret);

            $command = 'rm /var/www/app/public/potree/' . $workspace . '_' . $nombre . '*';
            exec($command, $out, $ret);
        }

        /*
        // Borrar estilo
        $service = "http://$ip_server:8080/geoserver/"; // replace with your URL
        $request_gs = "rest/workspaces/$workspace/styles/" . $nombre . "?purge=true&recurse=true";
        $url = $service . $request_gs;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); //option to return string
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        $passwordStr = "admin:".env('DB_PASSWORD'); // replace with your username:password
        curl_setopt($ch, CURLOPT_USERPWD, $passwordStr);
        //POST return code
        $buffer_2 = curl_exec($ch); // Execute the curl request
        curl_close($ch);
        // FIN - Borrar estilo
        */

        /*
         * Elimina registro de la tabla capa_mision
         */
        $str_sql = "DELETE FROM $workspace.layers WHERE name = '$id'";
        $delete = DB::select($str_sql);
        /*
         * Fin Elimina registro de la tabla capa_mision
         */

        return [];

    }

    public function obtenerExtensionCapa(Request $request) {
        $id_proyecto = $request->input('id_proyecto');
        $id = $request->input('id');

        $str_sql = "SELECT * FROM sch_aicedronesdi.project WHERE id = $id_proyecto";
        $res = DB::select($str_sql);
        $workspace = $res[0]->workspace;

        $str_sql = "SELECT * FROM $workspace.layers WHERE name = '$id'";
        $capa = DB::select($str_sql);

        $nombre = $capa[0]->name;



        $str_sql = "SELECT min(ST_XMin(ST_Transform(the_geom,3857))) as left,
                  min(ST_YMin(ST_Transform(the_geom,3857))) as bottom,
                  max(ST_XMax(ST_Transform(the_geom,3857))) as right,
                  max(ST_YMax(ST_Transform(the_geom,3857))) as top FROM $workspace.$nombre";
        if ($nombre === 'vertex' || $nombre === 'way')
            $str_sql .= " WHERE origen = 'edit'";
        $bbox = DB::select($str_sql);

        return $bbox;

    }

    public function sanitizeString($str) {
        $out = $str;
        $out = str_replace("á", "a", $out);
        $out = str_replace("é", "e", $out);
        $out = str_replace("í", "i", $out);
        $out = str_replace("ó", "o", $out);
        $out = str_replace("ú", "u", $out);
        $out = str_replace("ñ", "n", $out);
        $out = str_replace("ä", "a", $out);
        $out = str_replace("ë", "e", $out);
        $out = str_replace("ï", "i", $out);
        $out = str_replace("ö", "o", $out);
        $out = str_replace("ü", "u", $out);
        $caracteres = array("!", "#", "$", "%", "&", "'", "*", "+", "-", "=", "?", "^", "`", "{", "|", "}", "~", "@", ".", "[", "]", ".", " ", "(", ")");
        $out = str_replace($caracteres, "_", $out);
        return $out;
    }

    public function anadirProducto(Request $request) {
        $id_proyecto = $request->input('id_proyecto');
        $ruta = $request->input('ruta');
        $nombreArchivo = explode("/",$ruta)[ count(explode("/",$ruta)) -1 ];
        $changed_complete_file_name = $this->sanitizeString( explode(".",$nombreArchivo)[0] ).".zip";

        $str_sql = "DELETE FROM sch_aicedronesdi.job WHERE id_proyecto = $id_proyecto";
        $res = DB::select($str_sql);

        $str_sql = "INSERT INTO sch_aicedronesdi.job(id_proyecto, nombre, mensaje, estado)";
        $str_sql .= " VALUES($id_proyecto,'$nombreArchivo','Iniciando publicación del producto <strong>$nombreArchivo</strong>','Activo')";
        $res = DB::select($str_sql);

        /*
         * tasks
         */
        $str_sql = "DELETE FROM sch_aicedronesdi.tasks WHERE project_id = $id_proyecto";
        $res = DB::select($str_sql);

        $str_sql = "INSERT INTO sch_aicedronesdi.tasks(project_id, name, message, type, command)";
        $str_sql .= " VALUES($id_proyecto,'$nombreArchivo','Procesando producto $nombreArchivo','artisan','/usr/local/bin/php artisan add:product $id_proyecto ''$ruta''')";
        $res = DB::select($str_sql);

        $str_sql = "INSERT INTO sch_aicedronesdi.tasks(project_id, name, message, type, command)";
        $str_sql .= " VALUES($id_proyecto,'$nombreArchivo','Descomprimiendo $nombreArchivo','artisan','/usr/local/bin/php artisan unzip:product $id_proyecto ''$ruta''')";
        $res = DB::select($str_sql);
        /*
         * END - tasks
         */

        return [];
    }

    public function obtenerTrabajosProyecto(Request $request) {
        $id_proyecto = $request->input('id_proyecto');

        /*
        $str_sql = "SELECT count(*) as n_tasks FROM sch_aicedronesdi.tasks WHERE project_id = $id_proyecto";
        $n_tasks = DB::select($str_sql)[0]->n_tasks;
        if ($n_tasks === 0)
            return [];
        */

        $str_sql = "SELECT * FROM sch_aicedronesdi.job WHERE id_proyecto = $id_proyecto ORDER BY fechahora ASC LIMIT 1"; // AND notificado IS FALSE
        $res = DB::select($str_sql);
        if (count($res) > 0) {
            $id_trabajo = $res[0]->id;

            $str_sql = "DELETE FROM sch_aicedronesdi.job WHERE id = $id_trabajo AND notificado = TRUE AND (estado = 'Terminado' OR estado = 'Finalizado' OR estado = 'Error')";
            $delete = DB::select($str_sql);

            $str_sql = "UPDATE sch_aicedronesdi.job SET notificado = TRUE WHERE id = $id_trabajo";
            $update = DB::select($str_sql);
        }




        return $res;
    }

    public function getPotreeStatus(Request $request) {
        $id_capa = $request->input('id_capa');
        $workspace = $request->input('workspace');

        if (is_dir('/var/www/app/public/potree/'.$workspace.'_'.$id_capa.'/pointclouds/'.$workspace.'_'.$id_capa.'/chunks'))
            return ['error!'];
        if (!is_dir('/var/www/app/public/potree/'.$workspace.'_'.$id_capa))
            return ['error'];

        return ['Ok'];


    }

    /*
     * FIN - Añadir capa a un proyecto
     */


    public function isWmsEmpty(Request $request)
    {
        //$url = $request->input('url').'&FEATURE_COUNT=10000';
        $url_arr = $request->input('url_arr');

        $pos_with_value = [];
        if ($url_arr)
            for ($x = 0; $x < count($url_arr); $x++) {
                $data = file_get_contents($url_arr[$x]);
                $data_a = json_decode($data, true);
                if (count($data_a['features']) > 0)
                    $pos_with_value[] = $x;
            }

        return $pos_with_value;
    }

    public function getFeatureInfo(Request $request)
    {
        $id_proyecto = $request->input('id_proyecto');

        $str_sql = "SELECT * FROM sch_aicedronesdi.project WHERE id = $id_proyecto";
        $res = DB::select($str_sql);
        $workspace = $res[0]->workspace;

        $url = $request->input('url');
        $data = file_get_contents($url);
        $results = json_decode($data, true);

        $name = $request->input('table_name');

        $str_sql = "SELECT * FROM $workspace.layers WHERE name = '$name'";
        $capa = DB::select($str_sql);

        $id_capa = $capa[0]->id;
        $origen = $capa[0]->origen;

        $attributes = [];
        if ($origen === "shp") {
            $str_sql = "SELECT * FROM $workspace.attributes WHERE id_capa = $id_capa";
            $attributes = DB::select($str_sql);
        }

        return [$results, $attributes];
    }

    public function queryFotovoltaica(Request $request)
    {
        $url_arr = $request->input('url_arr');
        $query_tables = $request->input('query_tables');

        $foto_data = [];
        
        //$own_east_data = [];
        //$own_up_data = [];
        //echo "<script>console.log('". $query_tables[0]."');</script>";
        for ($x=0;$x<count($query_tables);$x++) {
            $table = $query_tables[$x];
            $url = $url_arr[$x];

            $url1 = str_replace ("http://212.128.192.147/proxy.php?url=","",$url);
            $url1 = str_replace ("https://212.128.192.147/proxy.php?url=","",$url1);
            $url1 = str_replace ("%3A",":",$url1);
            $url1 = str_replace ("%2F","/",$url1);
            $url1 = str_replace ("%3F&","?",$url1);
            $url1 = str_replace("?","&",$url1);
            $url1 = str_replace("/wms&","/wms?",$url1);
            $url1 = str_replace("/ows&","/ows?",$url1);
            $data = file_get_contents($url1);
            $results = json_decode($data, true);

            if ($table === "fotovoltaica") {
                if (count($results['features']) > 0) {
                    $foto_properties = $results["features"][0]["properties"];
                    $id = $results["features"][0]["properties"]["Id"];
                    $str_sql = "SELECT * FROM public.fotovoltaica WHERE Id = '$id'";
                    $foto_data = DB::select($str_sql);
                }
            }
        }
        return $foto_data;
       
    }        


    /*
    public function getFeatureInfo(Request $request) {
        $url_arr = $request->input('url_arr');

        $results_arr = [];
        for ($x=0;$x<count($url_arr);$x++) {
            $data = file_get_contents($url_arr[$x]);
            $results = json_decode($data,true);
            $results_arr[] = $results;
        }

        return $results_arr;
    }
*/

}
