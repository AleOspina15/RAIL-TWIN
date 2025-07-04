<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class AnadirProducto implements ShouldQueue
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
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $id_proyecto = $this->request['id_proyecto'];
        $nombreArchivo = $this->request['nombreArchivo'];
        $changed_complete_file_name = $this->request['changed_complete_file_name'];

        $str_sql = "SELECT * FROM sch_aicedronesdi.project WHERE id = $id_proyecto";
        $res = DB::select($str_sql);
        $workspace = $res[0]->workspace;

        $ip_server = env('APP_HOST');

        $command = 'unzip /var/www/app/docker/geoserver/data/proyectos/' . $workspace . '/uploads/' . $changed_complete_file_name . ' -d /var/www/app/docker/geoserver/data/proyectos/' . $workspace . '/uploads/';
        exec($command, $out, $ret);

        $command = 'rm /var/www/app/docker/geoserver/data/proyectos/'.$workspace.'/uploads/'.$changed_complete_file_name;
        exec($command, $out, $ret);

        $data = @file_get_contents('/var/www/app/docker/geoserver/data/proyectos/'.$workspace.'/uploads/product_descriptor.json');
        $results = json_decode($data,true);

        //var_dump($results);

        $has_Potree = false;

        foreach ($results as $clave=>$valor) {
            $position = $results[$clave]['position'];
            $name = $results[$clave]['name'];
            $name_o = $results[$clave]['name'];
            $filepath = $results[$clave]['filepath'];
            $sld_path = $results[$clave]['sld_path'];
            $crs = $results[$clave]['crs'];
            $visible = $results[$clave]['visible'];
            $attributes = $results[$clave]['attributes'];
            $links = $results[$clave]['links'];
            $product_date = $results[$clave]['product_date'];
            $init_date = $results[$clave]['init_date'];
            $end_date = $results[$clave]['end_date'];
            $uav = $results[$clave]['uav'];
            $sensors = $results[$clave]['sensors'];
            $description = $results[$clave]['description'];

            /* Atributo type
            •	orthomosaic
            •	dsm
            •	dtm
            •	chm
            •	point_cloud
            •	3d_model
            •	vector_layer
            •	spatial_database
            •	database
            •	attributes_table
             */
            $type = $results[$clave]['type'];

            $name = strtolower($name);
            $name = $this->sanitizeString($name);

            $ip_server = env('APP_HOST');

            echo "\n\n\n";
            echo "$clave - $filepath";
            echo "\n\n\n";

            $str_sql = "INSERT INTO sch_aicedronesdi.job(id_proyecto, nombre, mensaje, estado)";
            $str_sql .= " VALUES($id_proyecto,'$filepath','Procesando archivo $filepath','Activo')";
            $res = DB::select($str_sql);


            $id_capa = 0;


            if ($type === "point_cloud") {
                $str_sql = "SELECT * FROM $workspace.layers WHERE name = '$name'";
                $res_existe = DB::select($str_sql);
                if (count($res_existe) > 0) {
                    $str_sql = "UPDATE sch_aicedronesdi.job";
                    $str_sql .= " SET mensaje = 'Ya existe una capa con el nombre $name_o', estado='Error', notificado=FALSE, fechahora='NOW()'";
                    $str_sql .= " WHERE id_proyecto = $id_proyecto AND nombre = '$filepath'";
                    $res = DB::select($str_sql);
                } else {

                    $has_Potree = true;

                    $str_sql = "UPDATE sch_aicedronesdi.job";
                    $str_sql .= " SET mensaje = 'Publicando $name_o.', estado='Activo', notificado=FALSE, fechahora='NOW()'";
                    $str_sql .= " WHERE id_proyecto = $id_proyecto AND nombre = '$filepath'";
                    $res = DB::select($str_sql);

                    $extension_file = explode(".", $filepath)[count(explode(".", $filepath)) - 1];
                    $new_filepath = $workspace . "_" . $name . "." . $extension_file;

                    //$command = 'cp /var/www/app/docker/geoserver/data/proyectos/' . $workspace . '/uploads/' . $filepath . ' /var/www/app/public/potree/' . $workspace . '/' . $name . '/' . $filepath;
                    $command = 'cp /var/www/app/docker/geoserver/data/proyectos/' . $workspace . '/uploads/' . $filepath . ' /var/www/app/public/potree/' . $new_filepath;
                    exec($command, $out, $ret);

                    sleep(20);

                    /*
                    $command = 'chmod -R 777 /var/www/app/public/potree';
                    exec($command, $out, $ret);
                    $command = 'chown -R www-data:www-data /var/www/app/public/potree';
                    exec($command, $out, $ret);
                    */

                    $str_sql = "UPDATE sch_aicedronesdi.job";
                    $str_sql .= " SET mensaje = '$name_o publicada correctamente.', estado='Terminado', notificado=FALSE, fechahora='NOW()'";
                    $str_sql .= " WHERE id_proyecto = $id_proyecto AND nombre = '$filepath'";
                    $res = DB::select($str_sql);

                    $str_sql = "INSERT INTO $workspace.layers(id_grupo, titulo, workspace, name, style, visible, posicion, queryable, origen, min_zoom, max_zoom, capa_sistema, publica, displayinlayerswitcher, activa,inicio,fin,tipo,noswitcherdelete,descargable)";
                    $str_sql .= " VALUES (1,'$name_o','$workspace','$name',NULL,FALSE,$position,FALSE,'potree',0,25,FALSE,TRUE,TRUE,TRUE,'$init_date','$end_date','potree',FALSE,FALSE) returning id";
                    $res = DB::select($str_sql);

                    $id_capa = $res[0]->id;
                }
            }


            if ($type === "orthomosaic" || $type === "dsm" || $type === "dsm" || $type === "dtm" || $type === "vector_layer") {

                $str_sql = "SELECT * FROM $workspace.layers WHERE name = '$name'";
                $res_existe = DB::select($str_sql);
                if (count($res_existe) > 0) {
                    $str_sql = "UPDATE sch_aicedronesdi.job";
                    $str_sql .= " SET mensaje = 'Ya existe una capa con el nombre $name_o', estado='Error', notificado=FALSE, fechahora='NOW()'";
                    $str_sql .= " WHERE id_proyecto = $id_proyecto AND nombre = '$filepath'";
                    $res = DB::select($str_sql);
                } else {
                    $extension = explode(".", $filepath)[1];
                    //echo $extension . "\n";

                    if ($crs === 'internal') {
                        $command = 'gdalsrsinfo -e /var/www/app/docker/geoserver/data/proyectos/' . $workspace . '/uploads/' . $filepath;
                        $output = shell_exec($command);
                        $out_str = str_replace("\n", "", $output);
                        $out_str = explode(":", explode("PROJ.4", $out_str)[0])[1];
                        $crs = $out_str;
                    }

                    if ($extension === 'tif') {

                        $str_sql = "UPDATE sch_aicedronesdi.job";
                        $str_sql .= " SET mensaje = 'Publicando $name_o.', estado='Activo', notificado=FALSE, fechahora='NOW()'";
                        $str_sql .= " WHERE id_proyecto = $id_proyecto AND nombre = '$filepath'";
                        $res = DB::select($str_sql);

                        $command = 'cp /var/www/app/docker/geoserver/data/proyectos/' . $workspace . '/uploads/' . $filepath . ' /var/www/app/docker/geoserver/data/proyectos/' . $workspace . '/' . $name . '.tif';
                        exec($command, $out, $ret);

                        //
                        // Borra todos los imports
                        //
                        $service = env('GS_URL');
                        $request_gs = "/rest/imports";
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
                        $service = env('GS_URL');
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
                        $xmlStr .= '"file": "/opt/geoserver/data_dir/data/proyectos/' . $workspace . '/' . $name . '.tif"';
                        $xmlStr .= '}';
                        $xmlStr .= '}';
                        $xmlStr .= '}';
                        curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlStr);
                        $successCode = 201;
                        $buffer = curl_exec($ch);
                        curl_close($ch);
                        //
                        // FIN - Crea un nuevo import
                        //

                        //
                        // Obtener el import creado
                        //
                        $service = env('GS_URL');
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
                        $command = 'curl -u admin:' . env('DB_PASSWORD') . ' -X POST '.env('GS_URL').'/rest/imports/' . $id;
                        exec($command, $out, $ret);
                        //
                        // Fin Publicar el archivo definitivamente en Geoserver
                        //

                        //
                        // Borrar estilo creado automáticamente $geoserver_ws _ $only_name
                        //

                        $style_default = $workspace . '_' . $name;
                        $service = env('GS_URL');
                        $request_gs = "/rest/styles/" . $style_default . "?purge=true&recurse=true";
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
                        /*
                        $service = "http://$ip_server:8080/geoserver/";
                        $request_gs = "rest/workspaces/$workspace/coveragestores/$name/coverages/$name.xml";
                        $url = $service . $request_gs;
                        $ch = curl_init($url);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_VERBOSE, true);
                        curl_setopt($ch, CURLOPT_POST, True);
                        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
                        $passwordStr = "admin:".env('DB_PASSWORD');
                        curl_setopt($ch, CURLOPT_USERPWD, $passwordStr);
                        curl_setopt($ch, CURLOPT_HTTPHEADER,
                            array("Content-type: text/xml"));
                        $xmlStr = '<coverage><parameters><entry><string>InputTransparentColor</string><string>FFFFFF</string></entry></parameters><enabled>true</enabled></coverage>';
                        curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlStr);
                        $successCode = 201;
                        $buffer = curl_exec($ch);
                        curl_close($ch);
                        */

                        //
                        // Fin publicación geoserver
                        //

                        $str_sql = "UPDATE sch_aicedronesdi.job";
                        $str_sql .= " SET mensaje = 'Capa $name_o publicada correctamente.', estado='Terminado', notificado=FALSE, fechahora='NOW()'";
                        $str_sql .= " WHERE id_proyecto = $id_proyecto AND nombre = '$filepath'";
                        $res = DB::select($str_sql);

                        $str_sql = "INSERT INTO $workspace.layers(id_grupo, titulo, workspace, name, style, visible, posicion, queryable, origen, min_zoom, max_zoom, capa_sistema, publica, displayinlayerswitcher, activa,inicio,fin,tipo,noswitcherdelete,descargable)";
                        $str_sql .= " VALUES (1,'$name_o','$workspace','$name',NULL,$visible,$position,TRUE,'tif',0,25,FALSE,TRUE,TRUE,TRUE,'$init_date','$end_date','wms',FALSE,TRUE) returning id";
                        $res = DB::select($str_sql);

                        // id de la capa
                        $id_capa = $res[0]->id;
                    }

                    if ($extension === 'shp') {

                        $str_sql = "UPDATE sch_aicedronesdi.job";
                        $str_sql .= " SET mensaje = 'Publicando $name_o.', estado='Activo', notificado=FALSE, fechahora='NOW()'";
                        $str_sql .= " WHERE id_proyecto = $id_proyecto AND nombre = '$filepath'";
                        $res = DB::select($str_sql);

                        putenv('PGPASSWORD=' . env('DB_PASSWORD'));
                        $command = 'shp2pgsql -I -s ' . $crs . ' /var/www/app/docker/geoserver/data/proyectos/' . $workspace . '/uploads/' . $filepath . ' ' . $workspace . '.' . $name . ' | psql -h postgres -p 5432 -d aicedronesdi -U postgres';
                        exec($command, $out, $ret);
                        putenv('PGPASSWORD');

                        //Publicar tabla de Postgis en Geoserver (Postgis)
                        $service = env('GS_URL'); // replace with your URL
                        $request_gs = "/rest/workspaces/$workspace/datastores/$workspace/featuretypes";
                        $url = $service . $request_gs;
                        $ch = curl_init($url);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); //option to return string
                        curl_setopt($ch, CURLOPT_VERBOSE, true);
                        curl_setopt($ch, CURLOPT_POST, true);
                        $passwordStr = "admin:" . env('DB_PASSWORD');
                        curl_setopt($ch, CURLOPT_USERPWD, $passwordStr);
                        curl_setopt($ch, CURLOPT_HTTPHEADER,
                            array("Content-type: text/xml"));
                        $xmlStr = '<featureType><name>' . $name . '</name></featureType>';
                        curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlStr);
                        $buffer = curl_exec($ch); // Execute the curl request

                        $str_sql = "UPDATE sch_aicedronesdi.job";
                        $str_sql .= " SET mensaje = 'Capa $name_o publicada correctamente.', estado='Terminado', notificado=FALSE, fechahora='NOW()'";
                        $str_sql .= " WHERE id_proyecto = $id_proyecto AND nombre = '$filepath'";
                        $res = DB::select($str_sql);

                        $str_sql = "INSERT INTO $workspace.layers(id_grupo, titulo, workspace, name, style, visible, posicion, queryable, origen, min_zoom, max_zoom, capa_sistema, publica, displayinlayerswitcher, activa,inicio,fin,tipo,noswitcherdelete,descargable)";
                        $str_sql .= " VALUES (1,'$name_o','$workspace','$name',NULL,$visible,$position,TRUE,'shp',0,25,FALSE,TRUE,TRUE,TRUE,'$init_date','$end_date','wms',FALSE,TRUE) returning id";
                        $res = DB::select($str_sql);

                        // id de la capa
                        $id_capa = $res[0]->id;
                    }

                    if ($sld_path != "") {

                        // Path completo al fichero
                        $total_file_path = '/var/www/app/docker/geoserver/data/proyectos/' . $workspace . '/uploads/' . $sld_path;
                        //$total_file_path = '/opt/geoserver/data_dir/data/proyectos/' . $workspace . '/uploads/' . $sld_path;

                        $sld_version = "1.1.0";
                        if (strpos(file_get_contents($total_file_path), 'version="1.0.0"') !== false) {
                            $sld_version = "1.0.0";
                        }

                        // Crea estilo vacío
                        $style_name = $name;
                        $service = env('GS_URL'); // replace with your URL
                        $request_gs = "/rest/workspaces/$workspace/styles";
                        $url = $service . $request_gs;
                        $ch = curl_init($url);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); //option to return string
                        curl_setopt($ch, CURLOPT_VERBOSE, true);
                        curl_setopt($ch, CURLOPT_POST, true);
                        $passwordStr = "admin:" . env('DB_PASSWORD');
                        curl_setopt($ch, CURLOPT_USERPWD, $passwordStr);
                        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-type: text/xml"));
                        $xmlStr = '<style><name>' . $style_name . '</name><filename>' . $style_name . '.sld</filename></style><languageVersion><version>' . $sld_version . '</version></languageVersion>';
                        curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlStr);
                        $buffer = curl_exec($ch); // Execute the curl request
                        curl_close($ch);
                        // FIN - Crea estilo vacío

                        //if ($buffer == ":Style named '$style_name' already exists in workspace sidap")
                        //    return ["Error","El estilo $style_name ya existe."];


                        // Rellenar estilo vacío con el contenido del archivo .sld
                        $command = 'curl -v -u admin:' . env('DB_PASSWORD') . ' -X PUT --header "Content-type: application/vnd.ogc.se+xml" --data-binary "@' . $total_file_path . '" '.env('GS_URL').'/rest/workspaces/' . $workspace . '/styles/' . $style_name;
                        if ($sld_version === "1.0.0")
                            $command = 'curl -v -u admin:' . env('DB_PASSWORD') . ' -X PUT --header "Content-type: application/vnd.ogc.sld+xml" --data-binary "@' . $total_file_path . '" '.env('GS_URL').'/rest/workspaces/' . $workspace . '/styles/' . $style_name;
                        exec($command, $out, $ret);
                        // FIN - Rellenar estilo vacío con el contenido del archivo .sld


                        $params = '<layer><defaultStyle><name>' . $style_name . '</name></defaultStyle><enabled>true</enabled></layer>';
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, env('GS_URL')."/rest/layers/" . $workspace . ":" . $name);
                        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: text/xml"));
                        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
                        curl_setopt($ch, CURLOPT_USERPWD, "admin:" . env('DB_PASSWORD')); //geoserver.
                        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Receive server response ...
                        $response = curl_exec($ch);
                        curl_close($ch);

                        $str_sql = "UPDATE $workspace.layers SET style = '$style_name' WHERE id = $id_capa";
                        $res = DB::select($str_sql);

                    }

                    // Productos
                    $str_sql = "INSERT INTO $workspace.products VALUES ($id_capa, $position, '$name_o', '$crs', '$product_date', '$type', '$uav', '$sensors', '$description')";
                    $insert = DB::select($str_sql);

                    // Enlaces
                    for ($e = 0; $e < count($links); $e++) {
                        $name_link = $links[$e]['name'];
                        $type_link = $links[$e]['type'];
                        $url_link = $links[$e]['url'];

                        $str_sql = "INSERT INTO $workspace.links(id_capa, name, type, url) VALUES ($id_capa, '$name_link', '$type_link', '$url_link')";
                        $insert = DB::select($str_sql);
                    }

                    // Attributes
                    for ($e = 0; $e < count($attributes); $e++) {
                        $attribute = $attributes[$e];

                        $str_sql = "INSERT INTO $workspace.attributes(id_capa, attribute) VALUES ($id_capa, '$attribute')";
                        $insert = DB::select($str_sql);
                    }
                }

            }


        }


        $str_sql = "UPDATE sch_aicedronesdi.job";
        $str_sql .= " SET mensaje = 'El proceso de publicación del producto $nombreArchivo ha teminado.', estado='Finalizado', notificado=FALSE, fechahora='NOW()'";
        $str_sql .= " WHERE id_proyecto = $id_proyecto AND nombre = '$nombreArchivo'";
        $res = DB::select($str_sql);

        if ($has_Potree) {
            $command = 'docker start potreeconverter';
            exec($command, $out, $ret);
        }

    }





}
