<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;


class FileManagerController extends Controller
{


    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        if (! Gate::allows('Gestor de archivos')) {
            return abort(401);
        }

        return view('admin.fileManager.elfinder');
    }


    public function gestorDeArchivos($id)
    {
        if (! Gate::allows('Gestor de archivos')) {
            return abort(401);
        }

        $str_sql = "SELECT * FROM sch_aicedronesdi.project WHERE id = $id";
        $res = DB::select($str_sql);
        $nombre = $res[0]->nombre;

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

        return view('admin.proyectos.elfinder',compact('carpeta_proyecto','nombre','id'));
    }


    public function outputFiles($path,$extension_arr){
        $allow_file_extensions = $extension_arr;
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
                        // Display filename
                        $extension = explode('.',$file)[ count(explode('.',$file))-1 ];
                        if (in_array($extension,$allow_file_extensions) || in_array('*',$allow_file_extensions))
                            echo '<li data-jstree=\'{"icon":"far fa-file"}\'> '.$file.'</li>';
                        //echo $file . "*";
                        //echo $path."\n";
                    } else if(is_dir("$path/$file")){
                        // Recursively call the function if directories found
                        echo '<li data-jstree=\'{"icon":"fas fa-folder"}\'> '.$file.'<ul>';
                        $this->outputFiles("$path/$file",$extension_arr);
                        echo '</ul></li>';
                    }
                }
            } else{
                //echo "ERROR: No files found in the directory.";
            }
        } else {
            //echo "ERROR: The directory does not exist.";
        }
    }

    public function getFiles(Request $request) {
        $id_proyecto = $request->input('id_proyecto');
        $carpeta = $request->input('carpeta');

        $str_sql = "SELECT *";
        $str_sql .= " FROM sch_aicedronesdi.project WHERE id = $id_proyecto";
        $proyecto = DB::select($str_sql)[0];
        $workspace = $proyecto->workspace;
        $nombre = $proyecto->nombre;

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

        $extension_arr = $request->input('extension_arr');
        return $this->outputFiles('/var/www/app/docker/php/aicedronesdi_filemanager/Proyectos/'.$carpeta_proyecto.'/'.$carpeta,$extension_arr);
    }


}

