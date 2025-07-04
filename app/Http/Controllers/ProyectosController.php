<?php

namespace App\Http\Controllers;

//use App\Models\Proyecto;
use App\Models\Proyecto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Redirect;
use JeroenNoten\LaravelAdminLte\Events\BuildingMenu;
use Yajra\DataTables\Facades\DataTables;

class ProyectosController extends Controller
{
    private $isMobile;

    public function __construct()
    {
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

    public function index()
    {
        if (! Gate::allows('Proyectos')) {
            return abort(401);
        }

        if ($this->isMobile)
            return view('admin.proyectos.index');
        else
            return view('admin.proyectos.index');
    }

    public function obtenerProyectos(Request $request) {
        $str_sql = "SELECT *";
        $str_sql .= " FROM sch_aicedronesdi.project";
        $data = DB::select($str_sql);

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('configuration', function ($data) {
                $res = '';
                /*
                $res .= '<button class="btn btn-sm btn-datatable bg-orange ml-2 d-xl-inline d-none" onclick="visor(\''.$data->id.'\')"><i class="fa-solid fa-map mr-1"></i> Visor</button>';
                $res .= '<button class="btn btn-sm btn-datatable bg-orange ml-2 d-xl-none d-inline" onclick="visor(\''.$data->id.'\')"><i class="fa-solid fa-map"></i></button>';
                */
                /*
                $res .= '<button class="btn btn-sm btn-datatable bg-blue ml-2 d-xl-inline d-none" onclick="canteras(\''.$data->id.'\')"><i class="fa-solid fa-hill-rockslide mr-1"></i> Canteras</button>';
                $res .= '<button class="btn btn-sm btn-datatable bg-blue ml-2 d-xl-none d-inline" onclick="canteras(\''.$data->id.'\')"><i class="fa-solid fa-hill-rockslide"></i></button>';
                */
                $res .= '<button class="btn btn-sm btn-datatable bg-green ml-2 d-xl-inline d-none" onclick="gestor_de_archivos(\''.$data->id.'\')"><i class="fa-solid fa-folder-open mr-1"></i> Gestor de archivos</button>';
                $res .= '<button class="btn btn-sm btn-datatable bg-green ml-2 d-xl-none d-inline" onclick="gestor_de_archivos(\''.$data->id.'\')"><i class="fa-solid fa-folder-open"></i></button>';
                /*
                $res .= '<button class="btn btn-sm btn-datatable bg-black ml-2 d-xl-inline d-none" onclick="pk(\''.$data->id.'\')"><i class="far fa-square mr-1"></i> PK</button>';
                $res .= '<button class="btn btn-sm btn-datatable bg-black ml-2 d-xl-none d-inline" onclick="pk(\''.$data->id.'\')"><i class="far fa-square"></i></button>';

                $res .= '<button class="btn btn-sm btn-datatable bg-indigo ml-2 d-xl-inline d-none" onclick="vehiculosProyecto(\''.$data->id.'\')"><i class="fa-solid fa-truck mr-1"></i> Vehículos</button>';
                $res .= '<button class="btn btn-sm btn-datatable bg-indigo ml-2 d-xl-none d-inline" onclick="vehiculosProyecto(\''.$data->id.'\')"><i class="fa-solid fa-truck"></i></button>';

                $res .= '<button class="btn btn-sm btn-datatable bg-red ml-2 d-xl-inline d-none" onclick="vertederos(\''.$data->id.'\')"><i class="fa-solid fa-dumpster mr-1"></i> Vertederos</button>';
                $res .= '<button class="btn btn-sm btn-datatable bg-red ml-2 d-xl-none d-inline" onclick="vertederos(\''.$data->id.'\')"><i class="fa-solid fa-dumpster"></i></button>';

                $res .= '<button class="btn btn-sm btn-datatable bg-pink ml-2 d-xl-inline d-none" onclick="viasProyecto(\''.$data->id.'\')"><i class="fa-solid fa-road mr-1"></i> Vías</button>';
                $res .= '<button class="btn btn-sm btn-datatable bg-pink ml-2 d-xl-none d-inline" onclick="viasProyecto(\''.$data->id.'\')"><i class="fa-solid fa-road"></i></button>';
                */
                return $res;
            })
            ->addColumn('action', function ($data) {
                $res = '';
                $res .= '<button class="btn btn-sm btn-datatable btn-primary d-xl-inline d-none" onclick="editar(\''.$data->id.'\')"><i class="fa-solid fa-pen-to-square mr-1"></i> Editar</button>';
                $res .= '<button class="btn btn-sm btn-datatable btn-primary d-xl-none d-inline" onclick="editar(\''.$data->id.'\')"><i class="fa-solid fa-pen-to-square"></i></button>';

                $res .= '<form action="/proyectos/' . $data->id . '" method="POST" onsubmit="return confirm(\'¿ Está seguro ?\');" style="display: inline-block;">';
                $res .= '<input type="hidden" name="_method" value="DELETE">';
                $res .= '<input type="hidden" name="_token" value="' . csrf_token() . '">';

                $res .= '<button type="submit" class="btn btn-sm btn-datatable btn-danger ml-2 d-xl-inline d-none"><i class="fa-solid fa-trash mr-1"></i> Eliminar</button>';
                $res .= '<button type="submit" class="btn btn-sm btn-datatable btn-danger ml-2 d-xl-none d-inline"><i class="fa-solid fa-trash"></i></button>';

                $res .= '</form>';
                return $res;
            })
            ->rawColumns(['configuration','action'])
            ->make();
    }

    public function create()
    {
        if (! Gate::allows('Proyectos')) {
            return abort(401);
        }
        return view('admin.proyectos.create');
    }

    public function destroy($id)
    {
        if (! Gate::allows('Proyectos')) {
            return abort(401);
        }

        $str_sql = "SELECT * FROM sch_aicedronesdi.project WHERE id = $id";
        $res = DB::select($str_sql);
        $workspace = $res[0]->workspace;
        $nombre = $res[0]->nombre;

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

        // Eliminar espacio de trabajo de Geoserver
        $command = 'curl -u admin:' . env('DB_PASSWORD') . ' -XDELETE http://' . env('APP_HOST') . ':8080/geoserver/rest/workspaces/' . $workspace . '?recurse=true -H  "accept: application/json" -H  "content-type: application/json"';
        exec($command, $out, $ret);

        // Eliminar carpeta de Potree
        $command = 'chmod -R 777 /var/www/app/public/potree';
        exec($command, $out, $ret);
        $command = 'chown -R www-data:www-data /var/www/app/public/potree';
        exec($command, $out, $ret);
        $str_sql = "SELECT name FROM $workspace.layers WHERE tipo = 'potree'";
        $res = DB::select($str_sql);
        for ($x = 0; $x < count($res); $x++) {
            $name = $res[$x]->name;
            $command = 'rm -r /var/www/app/public/potree/' . $workspace . '_' . $name;
            exec($command, $out, $ret);
        }
        $command = 'rm /var/www/app/public/potree/' . $workspace . '*';
        exec($command, $out, $ret);

        // Eliminar esquema de PostgreSQL
        $str_sql = "DROP SCHEMA IF EXISTS \"$workspace\" CASCADE";
        $drop = DB::select($str_sql);

        // Eliminar carpeta de datos de Geoserver
        $command = 'rm -r /var/www/app/docker/geoserver/data/proyectos/'.$workspace;
        exec($command, $out, $ret);

        // Eliminar carpeta de datos del Gestor de archivos
        $command = 'rm -r /var/www/app/docker/php/aicedronesdi_filemanager/Proyectos/'.$carpeta_proyecto;
        exec($command, $out, $ret);


        $str_sql = "DELETE FROM sch_aicedronesdi.project WHERE id = $id";
        $res = DB::select($str_sql);

        return redirect()->route('proyectos.index');
    }

    public function editar($id) {
        if (! Gate::allows('Proyectos')) {
            return abort(401);
        }

        $str_sql = "SELECT ST_AsText(ST_Transform(geom,3857)) AS coord, * FROM sch_aicedronesdi.project WHERE id = $id";
        $proyecto = DB::select($str_sql)[0];

        return view('admin.proyectos.edit',compact('proyecto'));


    }




    public function obtenerCapas(Request $request) {
        $ids_capas = $request->input('ids_capas');

        $ids_str = '';
        for($x=0;$x<count($ids_capas);$x++) {
            $ids_str .= $ids_capas[$x];
            if ($x != count($ids_capas) - 1)
                $ids_str .= ',';
        }
        $ids_str .= '';

        $str_sql = "SELECT groups.id as id_grupo, layers.id as id_capa, groups.titulo as titulo_grupo, layers.titulo as titulo_capa, layers.nombre as nombre_capa,
                      layers.posicion AS posicion_capa, groups.posicion AS posicion_grupo, layers.visible AS visible_capa, groups.visible AS visible_grupo,
                      layers.url, layers.zoom_max AS zoom_max_capas, groups.zoom_max AS zoom_max_grupos, layers.zoom_min AS zoom_min_capas, groups.zoom_min AS zoom_min_grupos
                      FROM sch_viewer.layers, sch_viewer.groups
                      WHERE groups.id = layers.id_grupo AND layers.id IN ($ids_str)
                      ORDER BY groups.posicion DESC, groups.titulo ASC, layers.posicion DESC";
        $res = DB::select($str_sql);

        return $res;
    }

    public function update(Request $request, $idTipoMision)
    {
        if (! Gate::allows('Misiones')) {
            return abort(401);
        }

        $tipoMision = TipoMision::find($idTipoMision);
        $tipoMision->update($request->all());

        $str_sql = "DELETE FROM sch_viewer.tipo_mision_capa WHERE id_tipo_mision = '$idTipoMision'";
        $delete = DB::select($str_sql);
        $capas = $request->input('capa');
        for($x=0;$x<count($capas);$x++) {
            $id_capa = $capas[$x];
            $str_sql = "INSERT INTO sch_viewer.tipo_mision_capa VALUES ('$idTipoMision',$id_capa)";
            $insert = DB::select($str_sql);
        }

        $str_sql = "DELETE FROM sch_viewer.tipo_mision_herramienta WHERE id_tipo_mision = '$idTipoMision'";
        $delete = DB::select($str_sql);
        $herramientas = $request->input('herramienta');
        for($x=0;$x<count($herramientas);$x++) {
            $id_herramienta = $herramientas[$x];
            $str_sql = "INSERT INTO sch_viewer.tipo_mision_herramienta VALUES ('$idTipoMision','$id_herramienta')";
            $insert = DB::select($str_sql);
        }

        return redirect()->route('tiposMision.index');
    }




    public function store(Request $request)
    {
        if (! Gate::allows('Misiones')) {
            return abort(401);
        }
        return redirect()->route('tiposMision.index');
    }

    public function edit(Proyecto $proyecto)
    {
        if (! Gate::allows('Proyectos')) {
            return abort(401);
        }
        return redirect()->route('proyectos.index');
    }

    public function show(TipoMision $tipoMision)
    {
        if (! Gate::allows('Misiones')) {
            return abort(401);
        }
        return redirect()->route('tiposMision.index');
    }





}
