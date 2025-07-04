@extends('adminlte::page')

@section('title', 'RAIL TWIN')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-1">
            <div class="col-12 col-sm-12 col-md-12 col-lg-10 col-xl-10">
                <h4 class="text-sidap text-bold m-0"><i class="fa-solid fa-fire"></i> Simulación Incendio</h4>
            </div>
        </div>
    </div>
@stop

@section('content')
<div class="card">
    <div class="card-body" id="video-body1">
        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="videoinun" role="tabpanel">
                <video width="60%" controls>
                    <source src="{{ asset('videos/Incendio.mp4') }}" type="video/mp4">
                    Tu navegador no soporta la reproducción de video.
                </video>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
    <link rel="stylesheet" href="{{ asset('css/bootstrap_4_extend.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin_custom.css') }}">
@stop
