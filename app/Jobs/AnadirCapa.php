<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class AnadirCapa implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $request;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($request)
    {
        $this->request = $request;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $id_proyecto = $this->request['id_proyecto'];
        $nombreCapa = $this->request['nombreCapa'];
        $nombreCapaGeoserver = $this->request['nombreCapaGeoserver'];
        $changed_complete_file_name = $this->request['changed_complete_file_name'];
        $extension = $this->request['extension'];


        $str_sql = "SELECT * FROM sch_aicedronesdi.project WHERE id = $id_proyecto";
        $res = DB::select($str_sql);
        $workspace = $res[0]->workspace;

        echo $id_proyecto . "\n";
        echo $nombreCapa . "\n";
        echo $nombreCapaGeoserver . "\n";
        echo $changed_complete_file_name . "\n";
        echo $extension . "\n";
        echo $workspace . "\n";

        $ip_server = env('APP_HOST');

        if ($extension === 'tif') {
            //******************************
            //Publicar archivo en Geoserver
            //******************************

            //
            // Borra todos los imports
            //
            $service = "http://$ip_server:8080/geoserver/";
            $request_gs = "rest/imports";
            $url = $service . $request_gs;
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_VERBOSE, true);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
            $passwordStr = "admin:" . env('DB_PASSWORD');
            curl_setopt($ch, CURLOPT_USERPWD, $passwordStr);
            $buffer = curl_exec($ch);
            curl_close($ch);
            //
            // FIN - Borra todos los imports
            //

            //
            // Crea un nuevo import
            //
            $service = "http://$ip_server:8080/geoserver/";
            $request_gs = "rest/imports";
            $url = $service . $request_gs;
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_VERBOSE, true);
            curl_setopt($ch, CURLOPT_POST, true);
            $passwordStr = "admin:" . env('DB_PASSWORD');
            curl_setopt($ch, CURLOPT_USERPWD, $passwordStr);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-type: application/json"));
            $xmlStr = '{';
            $xmlStr .= '"import": {';
            $xmlStr .= '"targetWorkspace": {';
            $xmlStr .= '"workspace": {';
            $xmlStr .= '"name": "' . $workspace . '"';
            $xmlStr .= '}';
            $xmlStr .= '},';
            $xmlStr .= '"data": {';
            $xmlStr .= '"type": "file",';
            $xmlStr .= '"file": "/opt/geoserver/data_dir/data/proyectos/' . $workspace . '/' . $changed_complete_file_name . '"';
            $xmlStr .= '}';
            $xmlStr .= '}';
            $xmlStr .= '}';
            curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlStr);
            $successCode = 201;
            $buffer = curl_exec($ch);
            curl_close($ch);

            echo $buffer;

            //
            // FIN - Crea un nuevo import
            //

            //
            // Obtener el import creado
            //
            $service = "http://$ip_server:8080/geoserver/";
            $request_gs = "rest/imports";
            $url = $service . $request_gs;
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_VERBOSE, true);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
            $passwordStr = "admin:" . env('DB_PASSWORD');
            curl_setopt($ch, CURLOPT_USERPWD, $passwordStr);
            $buffer = curl_exec($ch);
            $res_data = json_decode($buffer, true);
            $id = $res_data['imports'][0]['id'];
            curl_close($ch);
            //
            // FIN - Obtener el import creado
            //

            //
            // Publicar el archivo definitivamente en Geoserver
            //
            $command = 'curl -u admin:' . env('DB_PASSWORD') . ' -X POST http://' . $ip_server . ':8080/geoserver/rest/imports/' . $id;
            exec($command, $out, $ret);
            //
            // Fin Publicar el archivo definitivamente en Geoserver
            //

            //
            // Borrar estilo creado automáticamente $geoserver_ws _ $only_name
            //
            $style_default = $workspace . "_" . $nombreCapaGeoserver;
            $service = "http://$ip_server:8080/geoserver/";
            $request_gs = "rest/styles/$style_default?purge=true&recurse=true";
            $url = $service . $request_gs;
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_VERBOSE, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
            $passwordStr = "admin:" . env('DB_PASSWORD');
            curl_setopt($ch, CURLOPT_USERPWD, $passwordStr);
            $buffer_2 = curl_exec($ch);
            curl_close($ch);
            //
            // FIN - Borrar estilo creado automáticamente $geoserver_ws _ $only_name
            //

            // Modificar el valor InputTransparentColor
            $service = "http://$ip_server:8080/geoserver/";
            $request_gs = "rest/workspaces/$workspace/coveragestores/$nombreCapaGeoserver/coverages/$nombreCapaGeoserver.xml";
            $url = $service . $request_gs;
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_VERBOSE, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
            $passwordStr = "admin:" . env('DB_PASSWORD');
            curl_setopt($ch, CURLOPT_USERPWD, $passwordStr);
            curl_setopt($ch, CURLOPT_HTTPHEADER,
                array("Content-type: text/xml"));
            $xmlStr = '<coverage><parameters><entry><string>InputTransparentColor</string><string>FFFFFF</string></entry></parameters><enabled>true</enabled></coverage>';
            curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlStr);
            $successCode = 201;
            $buffer = curl_exec($ch);
            curl_close($ch);
            //
            // Fin publicación geoserver
            //

        }


        if ($extension === 'zip') {
            $command = 'unzip /var/www/app/docker/geoserver/data/proyectos/' . $workspace . '/' . $changed_complete_file_name . ' -d /var/www/app/docker/geoserver/data/proyectos/' . $workspace . '/' . $nombreCapaGeoserver;
            exec($command, $out, $ret);

            \Storage::disk('local_geoserver')->delete('proyectos/' . $workspace . '/' . $changed_complete_file_name);

            $files = \Storage::disk('local_geoserver')->files('proyectos/' . $workspace . '/' . $nombreCapaGeoserver);
            $fn_arr = [];
            $e_arr = [];
            for ($x = 0; $x < count($files); $x++) {
                $fn = explode("/", $files[$x])[count(explode("/", $files[$x])) - 1];
                $fn_arr[] = explode(".", $fn)[0];
                $e_arr[] = explode(".", $fn)[1];
            }
            $valida_shp = true;
            if (!in_array("shp", $e_arr) || !in_array("prj", $e_arr) || !in_array("dbf", $e_arr) || !in_array("shx", $e_arr))
                $valida_shp = false;
            $fn_0 = $fn_arr[0];
            $fn_arr_count = array_count_values($fn_arr);
            if ($fn_arr_count[$fn_0] != count($files))
                $valida_shp = false;

            if ($valida_shp) {

                //
                //  Generar tabla en Postgres a partir del shp almacenado

                //
                $only_name = explode(".", $changed_complete_file_name)[0];

                // Antes de importar a Postgis, comprobar si el nombre de tabla ya existe
                $str_sql = "SELECT EXISTS (
                           SELECT 1
                           FROM   information_schema.tables
                           WHERE  table_schema = '$workspace'
                           AND    table_name = '$only_name'
                       )";
                $res_exist = DB::select($str_sql);

                if (!$res_exist[0]->exists) {

                    putenv('PGPASSWORD=' . env('DB_PASSWORD'));
                    $command = 'shp2pgsql -I -s 4326 /var/www/app/docker/geoserver/data/proyectos/' . $workspace . '/' . $only_name . '/' . $fn_0 . '.shp ' . $workspace . '.' . $only_name . ' | psql -h ' . env('DB_HOST') . ' -p 5432 -d aicedronesdi -U postgres';
                    exec($command, $out, $ret);
                    putenv('PGPASSWORD');
                    //return [$command,$out,$ret];

                    // Elimina los archivos subidos ya que están en Postgis
                    $command = 'rm -r /var/www/app/docker/geoserver/data/proyectos/' . $workspace . '/' . $only_name;
                    exec($command, $out2, $ret);

                    // Si la última posición del array $out no es 'ANALYZE' es que ha ocurrido un error al importar a Postgres con shp2pgsql
                    // En caso contrario $out contendrá las operaciones realizadas. Una por cada posición del vector.
                    if ($out[count($out) - 1] != 'ANALYZE') {
                        /*
                        return ['Error', "Se han encontrado errores al incorporar el archivo al sistema. Edite el archivo y revise que:
                                 - Los caracteres válidos para el nombre de los campos sólo pueden ser letras, números y el símbolo de guión bajo '_'
                                 - El archivo debe tener codificación UTF-8
                                 - En la tabla de atributos no deben aparecer caracteres no válidos (representados por el símbolo '?')."];
                        */
                    } else {

                        //
                        //Publicar tabla de Postgis en Geoserver (Postgis)
                        //
                        $service = "http://$ip_server:8080/geoserver/";
                        $request_gs = "rest/workspaces/$workspace/datastores/$workspace/featuretypes";
                        $url = $service . $request_gs;
                        $ch = curl_init($url);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_VERBOSE, true);
                        curl_setopt($ch, CURLOPT_POST, True);
                        $passwordStr = "admin:" . env('DB_PASSWORD');
                        curl_setopt($ch, CURLOPT_USERPWD, $passwordStr);
                        curl_setopt($ch, CURLOPT_HTTPHEADER,
                            array("Content-type: text/xml"));
                        $xmlStr = '<featureType><name>' . $only_name . '</name></featureType>';
                        curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlStr);
                        $successCode = 201;
                        $buffer = curl_exec($ch);

                    }
                } else {
                    // Elimina los archivos copiados
                    $command = 'rm -r /var/www/app/docker/geoserver/data/proyectos/' . $workspace . '/' . $only_name;
                    exec($command, $out2, $ret);

                    //return ['Error', 'Ya existe una tabla en la base de datos con el nombre ' . $only_name];
                }

            }


        }




    }
}
