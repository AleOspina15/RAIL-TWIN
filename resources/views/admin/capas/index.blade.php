@section('plugins.Datatables', true)
@section('plugins.datatablesPlugins', true)
@extends('adminlte::page')

@section('title', '')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-1">
            <div class="col-12 col-sm-12 col-md-12 col-lg-10 col-xl-10">
                <h4 class="text-sidap text-bold m-0"><i class="fas fa-fw fa-square" ></i> Capas</h4>
            </div>
            <div class="col-lg-2 col-xl-2 d-none d-lg-inline-block">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">pGIS</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Capas</a></li>
                    <li class="breadcrumb-item active">Capas</li>
                </ol>
            </div>
        </div>
    </div>
@stop

@section('content')
    <input type="hidden" id="_token" name="_token" value="{{ csrf_token() }}" />
    <?php
        $create = false;
        if (\Gate::allows('Capas (Crear, Editar y Eliminar)'))
            $create = true;
    ?>
    <input type="hidden" id="create" name="create" value="{{ $create }}" />


    <input id="ids_capas" type="hidden" value="@foreach($capas as $key => $capa){{ $capa->id }},@endforeach">


    @if($errors->any())
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <h5><i class="icon fas fa-ban"></i> Error</h5>
            {{$errors->first()}}
        </div>
    @endif


    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover datatable datatable-Capa">
            <thead>
            <tr class="text-center">
                <th>
                    Id
                </th>
                <th>
                    Título
                </th>
                <th>
                    Grupo
                </th>
                <th>
                    Simbología
                </th>
                <th>
                    Visible
                </th>
                <th>
                    Zoom max.
                </th>
                <th>
                    Zoom min.
                </th>
                 <th>
                    &nbsp;
                </th>
            </tr>
            </thead>
        </table>
    </div>







@stop

@section('css')
    <link rel="stylesheet" href="{{ asset('css/admin_custom.css') }}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap_4_extend.css') }}">
    <style>
        .table td, .table th {
            padding: .3rem;
        }
    </style>
@stop

@section('js')
    <script src="https://unpkg.com/@popperjs/core@2"></script>
    <script>
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        var dt;

        function anadir() {
            window.location = "capas/create";
        }

        function editar(id) {
            window.location = "capas/"+id+"/edit";
        }

        function ver(id) {
            window.location = "capas/"+id;
        }


        $(function () {

            dt = $('.datatable-Capa').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('obtenerCapas') }}",
                dom: '<"row"<"col-lg-2"B><"col-lg-2"l><"col-lg-2"f><"col-lg-6"p><"col-lg-12"rt><"col-lg-6"i><"col-lg-6"p>>',
                columns: [
                    { "data": "id" },
                    { "data": "titulo" },
                    { "data": "grupo" },
                    { "data": "simbologia" },
                    { "data": "visible_inicial" },
                    { "data": "max_zoom" },
                    { "data": "min_zoom" },
                    { "data": 'action', name: 'action', orderable: false, searchable: false}
                ],
                columnDefs: [
                    {
                        targets: 0,
                        className: 'dt-center',
                        width: 25
                    },
                    {
                        targets: 3,
                        className: 'dt-center p-1 m-0',
                        orderable: false
                    },
                    {
                        targets: 4,
                        className: 'dt-center',
                        width: 25,
                        orderable: false
                    },
                    {
                        targets: 5,
                        className: 'dt-center',
                        width: 80,
                        orderable: false
                    },
                    {
                        targets: 6,
                        className: 'dt-center',
                        width: 80,
                        orderable: false
                    },
                    {
                        targets: 7,
                        className: 'dt-center p-1 m-0'
                    }
                ],
                order: [[1, 'asc']],
                lengthMenu: [ 15, 30, 50, 75, 100 ],
                pageLength: 15,
                language: {
                    url: '/js/datatables/lang/' + document.getElementsByTagName("html")[0].getAttribute("lang") + '.json'
                },
                buttons: [
                    {
                        text: '<i class="fas fa-plus mr-1"></i> Añadir capa',
                        className: 'btn-sm btn-success btn-anadir-capa text-bold',
                        action: function ( e, dt, node, config ) {
                            anadir();
                        }
                    }
                ],
                "initComplete": function(settings, json) {
                    $(".btn-anadir-capa").removeClass("btn-secondary");
                    var create = $("#create").val();
                    if (!create)
                        $(".btn-anadir-capa").remove();
                },
                "drawCallback": function() {
                    var ids_capas = $("#ids_capas").val();
                    var ids_capas_arr = ids_capas.split(",");
                    for (var x=0;x<ids_capas_arr.length-1;x++) {
                        //console.log($('#button_' + ids_capas_arr[x]));
                        //console.log($("#tooltip_" + ids_capas_arr[x]));
                        $('#button_' + ids_capas_arr[x]).popover({
                            content: $("#tooltip_" + ids_capas_arr[x]).html(),
                            html: true,
                            trigger: 'hover'
                        })
                    }
                }
            });

        })


    </script>

@stop
