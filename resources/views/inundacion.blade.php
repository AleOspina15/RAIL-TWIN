@extends('adminlte::page')

@section('title', 'RAIL TWIN')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-1">
            <div class="col-12 col-sm-12 col-md-12 col-lg-10 col-xl-10">
                <h4 class="text-sidap text-bold m-0"><i class="fa-solid fa-water"></i> Simulación Inundación</h4>
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
                    <source src="{{ asset('videos/Inundacion.mp4') }}" type="video/mp4">
                    Tu navegador no soporta la reproducción de video.
                </video>
            </div>
        </div>
    </div>
    <div class="card-footer text-body-secondary" id="video-footer">
        <div class="container text-center">
            <div class="row">
                <div class="col">
                    <div class="card">
                        <div class="card-header" id="video-header1">
                            Fase 1
                        </div>
                        <div class="card-body" id="video-body2">
                            <div class="tab-content" id="myTabContent2">
                                <div class="tab-pane fade show active" id="videoinun_fase1" role="tabpanel">
                                    <video width="100%" controls>
                                        <source src="{{ asset('videos/Fase1.mp4') }}" type="video/mp4">
                                        Tu navegador no soporta la reproducción de video.
                                    </video>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card">
                        <div class="card-header" id="video-header2">
                            Fase 2
                        </div>
                        <div class="card-body" id="video-body3">
                            <div class="tab-content" id="myTabContent2">
                                <div class="tab-pane fade show active" id="videoinun_fase1" role="tabpanel">
                                    <video width="100%" controls>
                                        <source src="{{ asset('videos/Fase1.mp4') }}" type="video/mp4">
                                        Tu navegador no soporta la reproducción de video.
                                    </video>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card">
                        <div class="card-header" id="video-header3">
                            Fase 3
                        </div>
                        <div class="card-body" id="video-body4">
                            <div class="tab-content" id="myTabContent2">
                                <div class="tab-pane fade show active" id="videoinun_fase1" role="tabpanel">
                                    <video width="100%" controls>
                                        <source src="{{ asset('videos/Fase1.mp4') }}" type="video/mp4">
                                        Tu navegador no soporta la reproducción de video.
                                    </video>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card">
                        <div class="card-header" id="video-header4">
                            Fase 4
                        </div>
                        <div class="card-body" id="video-body5">
                            <div class="tab-content" id="myTabContent2">
                                <div class="tab-pane fade show active" id="videoinun_fase1" role="tabpanel">
                                    <video width="100%" controls>
                                        <source src="{{ asset('videos/Fase1.mp4') }}" type="video/mp4">
                                        Tu navegador no soporta la reproducción de video.
                                    </video>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
    <link rel="stylesheet" href="{{ asset('css/bootstrap_4_extend.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin_custom.css') }}">
@stop
