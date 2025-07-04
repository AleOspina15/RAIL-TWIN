@extends('adminlte::page')

@section('title', 'RAIL TWIN')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-1">
            <div class="col-12 col-sm-12 col-md-12 col-lg-10 col-xl-10">
                <h4 class="text-sidap text-bold m-0"><i class="fa-solid fa-train"></i> BIM vs. As Built</h4>
            </div>
        </div>
    </div>
@stop

@section('content')
<div class="container">
  <iframe class="responsive-iframe" src="http://212.128.193.56/?HoveringMouse=true&MatchViewportRes=false&KeyboardInput=true"></iframe>
</div>
@stop

@section('css')
    <link rel="stylesheet" href="{{ asset('css/bootstrap_4_extend.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin_custom.css') }}">
@stop
