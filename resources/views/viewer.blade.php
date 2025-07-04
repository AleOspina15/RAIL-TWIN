
<?php
config([
    'adminlte.sidebar_collapse' => true,
    'adminlte.sidebar_collapse_remember' => false,
]);
?>
@extends('adminlte::page')

@section('content')
    <div class="row m-0 p-0">
        <div class="col-12 m-0 p-0">
            <div id="map" class="m-0 p-0" style="width:100%;height: 800px;overflow: hidden;background-color: #fff"></div>
        </div>
    </div>

    <div id="infoDiv" class="p-2" style="display:none; cursor: default">
        <h4>Openlayers - Viewer 1</h4>
        <p class="text-justify">Basic example of creating a viewer with Openlayers.</p>
        <input class="btn btn-sm btn-info" type="button" id="infoButton" value="Close" />
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="{{ asset('css/visor.css') }}">
@stop

@section('js')
    <script src="{{ asset('js/viewer.js') }}"></script>
@stop