<?php

// Preparar URL
$url = $_SERVER['REQUEST_URI'];
$url1 = str_replace ("/proxy.php?url=","",$url);

$url1 = str_replace ("%3A",":",$url1);
$url1 = str_replace ("%2F","/",$url1);
$url1 = str_replace ("%3F&","?",$url1);
$url1 = str_replace ("image/jpeg","image/png",$url1);

$origen = '/'.preg_quote( '&', '/' ).'/i';
$url1 = ''.preg_replace( $origen, '?', ''.$url1, 1 );

$url = $url1;



//$url = str_replace("//","/",$url1);
//$url = str_replace("/wms&","/wms?",$url);
// FIN - Preparar URL


//
// Si la petición es de tipo GetFeatureInfo, realizar petición
//
if ( strpos($url,'request=GetFeatureInfo') || strpos($url,'REQUEST=GetFeatureInfo') ) {

    $url = str_replace("?","&",$url1);
    $url = str_replace("/wms&","/wms?",$url);
    $url = str_replace("/ows&","/ows?",$url);

    $ch = curl_init( $url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $output = curl_exec($ch);
    curl_close($ch);

    header('Content-Type: application/json');
    echo $output;
    exit;
}
//
// FIN - Si la petición es de tipo GetFeatureInfo, realizar petición
//

//
// Si la petición es de tipo GetCapabilities, realizar petición
//
if ( strpos($url,'request=GetCapabilities') || strpos($url,'REQUEST=GetCapabilities') ) {

    $url = str_replace("?","&",$url1);
    $url = str_replace("/wms&","/wms?",$url);
    $url = str_replace("/ows&","/ows?",$url);

    $ch = curl_init( $url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $output = curl_exec($ch);
    curl_close($ch);

    header('Content-Type: application/xml');
    echo $output;
    exit;

}
//
// FIN - Si la petición es de tipo GetCapabilities, realizar petición
//





if ( strpos($url,'request=GetMap') || strpos($url,'REQUEST=GetMap') ) {
    $url = $url.'&transparent=true';

    $ch = curl_init( $url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $output = curl_exec($ch);
    curl_close($ch);

    header("Content-Type: image/png");
    echo $output;
}

if ( strpos($url,'Request=GetTile') ) {
    //$url = $url.'&transparent=true';
    //$url = str_replace("?","&",$url1);
    //$url = str_replace("/wmts&","/wmts?",$url);

    //echo $url;
    //exit;

    $ch = curl_init( $url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $output = curl_exec($ch);
    curl_close($ch);

    header("Content-Type: image/png");
    echo $output;
}

if ( strpos($url,'request=GetLegendGraphic') || strpos($url,'REQUEST=GetLegendGraphic') ) {
    $url = str_replace("/wms&","/wms?",$url);

    $ch = curl_init( $url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $output = curl_exec($ch);
    curl_close($ch);

    header("Content-Type: image/png");
    echo $output;
}


//
// FIN - Si la petición es de tipo GetMap, comprobar que el usuario ha iniciado sesión
//



