@section('plugins.Datatables', true)
@section('plugins.datatablesPlugins', true)
@extends('adminlte::page')

@section('title', '')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-1">
            <div class="col-12 col-sm-12 col-md-12 col-lg-10 col-xl-10">
                <h4 class="text-sidap text-bold m-0"><i class="fas fa-fw fa-layer-group" ></i> Grupos</h4>
            </div>
            <div class="col-lg-2 col-xl-2 d-none d-lg-inline-block">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">pGIS</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Capas</a></li>
                    <li class="breadcrumb-item active">Grupos</li>
                </ol>
            </div>
        </div>
    </div>
@stop

@section('content')
    <input type="hidden" id="_token" name="_token" value="{{ csrf_token() }}" />
    <?php
    $create = false;
    if (\Gate::allows('Grupos (Crear, Editar y Eliminar)'))
        $create = true;
    ?>
    <input type="hidden" id="create" name="create" value="{{ $create }}" />


    @if($errors->any())
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <h5><i class="icon fas fa-ban"></i> Error</h5>
            {{$errors->first()}}
        </div>
    @endif

    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover datatable compact datatable-Grupos">
            <thead>
            <tr class="text-center">
                <th>
                    Id
                </th>
                <th>
                    Título
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
    <script>
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        var dt;

        function anadir() {
            window.location = "/grupos/create";
        }

        function editar(id) {
            window.location = "/grupos/"+id+"/edit";
        }

        function mostrar(id) {
            window.location = "/grupos/"+id;
        }


        $(function () {

            dt = $('.datatable-Grupos').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('obtenerGrupos') }}",
                dom: '<"row"<"col-lg-2"B><"col-lg-2"l><"col-lg-2"f><"col-lg-6"p><"col-lg-12"rt><"col-lg-6"i><"col-lg-6"p>>',
                columns: [
                    { "data": "id" },
                    { "data": "titulo" },
                    { "data": "visibilidad_inicial" },
                    { "data": "zoom_max" },
                    { "data": "zoom_min" },
                    { "data": 'action', name: 'action', orderable: false, searchable: false}
                ],
                columnDefs: [
                    {
                        targets: 0,
                        className: 'dt-center',
                        width: 25
                    },
                    {
                        targets: 2,
                        className: 'dt-center',
                        width: 25,
                        orderable: false
                    },
                    {
                        targets: 3,
                        className: 'dt-center',
                        width: 80,
                        orderable: false
                    },
                    {
                        targets: 4,
                        className: 'dt-center',
                        width: 80,
                        orderable: false
                    },
                    {
                        targets: 5,
                        className: 'dt-center p-1 m-0',
                        width: 250
                    }
                ],
                buttons: [
                    {
                        text: '<i class="fas fa-plus mr-1"></i> Añadir grupo',
                        className: 'btn-sm btn-success btn-anadir-grupo text-bold',
                        action: function ( e, dt, node, config ) {
                            anadir();
                        }
                    }
                ],
                order: [[1, 'asc']],
                lengthMenu: [ 15, 30, 50, 75, 100 ],
                pageLength: 15,
                language: {
                    url: '/js/datatables/lang/' + document.getElementsByTagName("html")[0].getAttribute("lang") + '.json'
                },
                "initComplete": function(settings, json) {
                    $(".btn-anadir-grupo").removeClass("btn-secondary");
                    var create = $("#create").val();
                    if (!create)
                        $(".btn-anadir-grupo").remove();
                }
            });

        })


    </script>
@stop

