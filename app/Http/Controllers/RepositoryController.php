<?php

namespace App\Http\Controllers;

use App\Models\RepositoryLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RepositoryController extends Controller
{

    /**
     * Create log record
     *
     * @param  string   $cmd       command name
     * @param  array    $result    command result
     * @param  array    $args      command arguments from client
     * @param  elFinder $elfinder  elFinder instance
     * @return void|true
     **/
    public static function log($cmd, $result, $args, $elfinder) {
        $instance = new RepositoryController();

        $str_sql = "DELETE FROM sch_filemanager.repository_log";
        $delete = DB::select($str_sql);

        if (!empty($result['error'])) {
            $log = new RepositoryLog;

            $log->user_id = Auth::user()->id;
            $log->command = $cmd;
            $log->result_raw = serialize($result);
            $log->result_type = 'error';
            $log->result_data = implode(' ', $result['error']);
            $log->save();
        }

        if (!empty($result['warning'])) {
            $log = new RepositoryLog;

            $log->user_id = Auth::user()->id;
            $log->command = $cmd;
            $log->result_raw = serialize($result);
            $log->result_type = 'warning';
            $log->result_data = implode(' ', $result['warning']);
            $log->save();
        }

        if (!empty($result['removed'])) {
            foreach ($result['removed'] as $file) {
                $log = new RepositoryLog;

                $log->user_id = Auth::user()->id;
                $log->command = $cmd;
                $log->result_raw = serialize($result);
                $log->result_type = 'removed';
                $log->result_data = $file['realpath'];
                $log->save();
            }
        }

        if (!empty($result['added'])) {
            foreach ($result['added'] as $file) {
                $log = new RepositoryLog;

                $log->user_id = Auth::user()->id;
                $log->command = $cmd;
                $log->result_raw = serialize($result);
                $log->result_type = 'added';
                $log->result_data = $elfinder->realpath($file['hash']);
                $log->save();
            }
        }

        if (!empty($result['changed'])) {
            foreach ($result['changed'] as $file) {
                $log = new RepositoryLog;

                $log->user_id = Auth::user()->id;
                $log->command = $cmd;
                $log->result_raw = serialize($result);
                $log->result_type = 'changed';
                $log->result_data = $elfinder->realpath($file['hash']);
                $log->save();
            }
        }

        $str_sql = "SELECT * FROM sch_filemanager.repository_log ORDER BY id ASC";
        $res = DB::select($str_sql);
        for ($x=0;$x<count($res);$x++) {
            $user_id = $res[$x]->user_id;
            $command = $res[$x]->command;
            $result_type = $res[$x]->result_type;
            $result_data = $res[$x]->result_data;

            if ($command === 'rm' || $command === 'rename') {
                if ($result_type === 'removed') {
                    $ruta = "/var/www/aicedronesdi_filemanager/".$result_data;
                    $str_sql = "DELETE FROM sch_filemanager.files WHERE ruta='$ruta'";
                    $delete = DB::select($str_sql);
                }
            }
            if ($command === 'upload' || $command === 'mkfile' || $command === 'duplicate' || $command === 'rename') {
                if ($result_type === 'added') {
                    //$instance->escanearDirectorio("/var/sidap_filemanager");
                }
            }

        }
        $instance->escanearDirectorio("/var/www/aicedronesdi_filemanager",$user_id);

        $str_sql = "SELECT * FROM sch_filemanager.files";
        $res = DB::select($str_sql);
        for ($x=0;$x<count($res);$x++) {
            $ruta = $res[$x]->ruta;
            if (!file_exists($ruta)) {
                $str_sql = "DELETE FROM sch_filemanager.files WHERE ruta='$ruta'";
                $delete = DB::select($str_sql);
            }
        }

        // Sincroniza la carpeta de Marcadores_SVG del Gestor de archivos con la carpeta svg
        /*
        $out = null;
        $command = 'rsync -azP --delete /var/sidap_filemanager/Simbología/Marcadores/ /var/www/html/sidap/public/markers';
        exec($command, $out, $ret);
        */
    }


    // Almacenar contenido del Gestor de Archivos en una tabla de la base de datos
    public function escanearDirectorio($path,$user_id){
        // Check directory exists or not
        if(file_exists($path) && is_dir($path)){
            // Scan the files in this directory
            $result = scandir($path);

            // Filter out the current (.) and parent (..) directories
            $files = array_diff($result, array('.', '..'));

            if(count($files) > 0){
                // Loop through retuned array
                foreach($files as $file){
                    if(is_file("$path/$file")){
                        $extension = explode('.',$file)[ count(explode('.',$file))-1 ];
                        $ruta = "$path/$file";
                        $str_sql = "SELECT * FROM sch_filemanager.files WHERE nombre = '$file' AND ruta = '$ruta'";
                        $res = DB::select($str_sql);
                        if (count($res) === 0) {
                            $str_sql = "INSERT INTO sch_filemanager.files(nombre,ruta,id_user)";
                            $str_sql .= " VALUES('$file','$ruta',$user_id)";
                            $insert = DB::select($str_sql);
                        }


                    } else if(is_dir("$path/$file")){
                        // Recursively call the function if directories found
                        $this->escanearDirectorio("$path/$file",$user_id);
                    }
                }
            } else{
                //echo "ERROR: No files found in the directory.<br>";
            }
        } else {
            //echo "ERROR: The directory does not exist.<br>";
        }
    }

    public function addExtra($cmd, &$result, $args, $elfinder) {
        foreach($result['files'] as $i => $file) {
            $ruta = "/var/www/aicedronesdi_filemanager/".base64_decode(substr($file['hash'], strrpos($file['hash'], '_')));
            $usuario = '';
            $proyecto = '';
            $material = '';
            $descripcion = '';

            $str_sql = "SELECT * FROM sch_filemanager.files WHERE ruta='$ruta'";
            $res = DB::select($str_sql);
            if (count($res) > 0) {
                $id_user = $res[0]->id_user;
                if ($id_user) {
                    $str_sql = "SELECT name FROM users WHERE id = $id_user";
                    $user = DB::select($str_sql);
                    if(count($user) > 0)
                        $usuario = $user[0]->name;
                }
                $id_proyecto = $res[0]->id_proyecto;
                if ($id_proyecto) {
                    $str_sql = "SELECT nombre FROM sch_aicedronesdi.project WHERE id = $id_proyecto";
                    $proyecto = DB::select($str_sql);
                    if(count($proyecto) > 0)
                        $proyecto = $proyecto[0]->nombre;
                }
            }

            $result['files'][$i]['usuario'] = $usuario;
            $result['files'][$i]['proyecto'] = $proyecto;
            $result['files'][$i]['material'] = $material;
            $result['files'][$i]['descripcion'] = $descripcion;
            $result['files'][$i]['ruta'] = $ruta;
            $result['files'][$i]['isfile'] = is_file($ruta);
        }
    }

}


