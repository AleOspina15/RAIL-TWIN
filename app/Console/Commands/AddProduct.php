<?php

namespace App\Console\Commands;

use App\Notifications\AlertaIncendio;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class AddProduct extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'add:product {project_id} {path}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Añade un producto a un proyecto';

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

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $project_id = $this->argument('project_id');
        $path = $this->argument('path');

        $nombreArchivo = explode("/",$path)[ count(explode("/",$path)) -1 ];
        $changed_complete_file_name = $this->sanitizeString( explode(".",$nombreArchivo)[0] ).".zip";

        $str_sql = "SELECT * FROM sch_aicedronesdi.project WHERE id = $project_id";
        $res = DB::select($str_sql);
        $workspace = $res[0]->workspace;
        $nombre = $res[0]->nombre;

        $carpeta_proyecto = $this->sanitizeString($nombre);

        $command = 'rm -r /var/www/app/docker/geoserver/data/proyectos/'.$workspace.'/uploads';
        exec($command, $out, $ret);

        $command = 'rm /var/www/app/public/potree/'.$workspace.'*';
        exec($command, $out, $ret);
        $command = 'rm /var/www/app/public/potree/*.laz';
        exec($command, $out, $ret);
        $command = 'rm /var/www/app/public/potree/*.las';
        exec($command, $out, $ret);



        $command = 'mkdir -p /var/www/app/docker/geoserver/data/proyectos/'.$workspace.'/uploads';
        exec($command, $out, $ret);

        $command = 'chmod -R 777 /var/www/app/docker/geoserver/data';
        exec($command, $out, $ret);

        $command = 'cp "/var/www/app/docker/php/aicedronesdi_filemanager/Proyectos/'.$carpeta_proyecto.'/Productos/'.$path.'" /var/www/app/docker/geoserver/data/proyectos/'.$workspace.'/uploads/'.$changed_complete_file_name;
        exec($command, $out, $ret);

        $command = 'chmod -R 777 /var/www/app/docker/geoserver/data';
        exec($command, $out, $ret);



    }
}
