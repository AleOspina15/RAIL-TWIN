@section('plugins.Datatables', true)
@section('plugins.datatablesPlugins', true)
@extends('adminlte::page')

@section('title', '')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-1">
            <div class="col-12 col-sm-12 col-md-12 col-lg-10 col-xl-10">
                <h4 class="text-sidap text-bold m-0"><i class="fa-solid fa-toolbox"></i> Proyectos</h4>
            </div>
            <div class="col-lg-2 col-xl-2 d-none d-lg-inline-block">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">RAIL TWIN</li>
                    <li class="breadcrumb-item active">Proyectos</li>
                </ol>
            </div>
        </div>
    </div>
@stop

@section('content')
    <input type="hidden" id="_token" name="_token" value="{{ csrf_token() }}" />

    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover datatable datatable-Proyectos w-100">
            <thead>
            <tr class="text-center">
                <th>
                    Id
                </th>
                <th>
                    Nombre
                </th>
                <th>
                    Inicio
                </th>
                <th>
                    Fin
                </th>
                <th>
                    Configuración
                </th>
                <th>
                    Acciones
                </th>
            </tr>
            </thead>
        </table>
    </div>








@stop

@section('css')
    <link rel="stylesheet" href="{{ asset('css/bootstrap_4_extend.css') }}">
    <style>
        .table td, .table th {
            padding: .3rem;
        }
    </style>
    <link href="{{ asset('css/all.min.css') }}" rel="stylesheet" />
@stop

@section('js')
    <script>
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        var dt;

        function visor(id) {
            window.location = "visorProyecto/"+id;
        }

        function anadir() {
            window.location = "proyectos/create";
        }

        function editar(id) {
            window.location = "editarProyecto/"+id;
        }

        function vehiculosProyecto(id) {
            window.location = "vehiculosProyecto/"+id;
        }

        function pk(id) {
            window.location = "pk/"+id;
        }

        function vias(id) {
            window.location = "vias/"+id;
        }

        function viasProyecto(id) {
            window.location = "viasProyecto/"+id;
        }

        function gestor_de_archivos(id) {
            window.location = "gestorDeArchivos/"+id;
        }

        function canteras(id) {
            window.location = "canteras/"+id;
        }

        function vertederos(id) {
            window.location = "vertederos/"+id;
        }

        $(function () {



            dt = $('.datatable-Proyectos').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                maintainAspectRatio: false,
                autoWidth: true,
                ajax: "obtenerProyectos",
                dom: '<"row"<"col-lg-2"B><"col-lg-2"l><"col-lg-2"f><"col-lg-6"p><"col-lg-12"rt><"col-lg-6"i><"col-lg-6"p>>',
                columns: [
                    { "data": "id" },
                    { "data": "nombre" },
                    { "data": "inicio" },
                    { "data": "fin" },
                    { "data": 'configuration', name: 'configuration', orderable: false, searchable: false},
                    { "data": 'action', name: 'action', orderable: false, searchable: false}
                ],
                columnDefs: [
                    {
                        targets: 0,
                        className: 'dt-center text-bold'
                    },
                    {
                        targets: 1
                    },
                    {
                        targets: 2,
                        className: 'dt-center'
                    },
                    {
                        targets: 3,
                        className: 'dt-center'
                    },
                    {
                        targets: 4,
                        className: 'dt-center'
                    },
                    {
                        targets: 5,
                        className: 'dt-center'
                    }
                ],
                order: [[0, 'asc']],
                lengthMenu: [ 15, 30, 50, 75, 100 ],
                pageLength: 15,
                language: {
                    url: '/js/datatables/lang/' + document.getElementsByTagName("html")[0].getAttribute("lang") + '.json'
                },
                buttons: [
                    {
                        text: '<i class="fas fa-plus mr-1"></i> Nuevo Proyecto',
                        className: 'btn-sm btn-success btn-nuevo text-bold',
                        action: function ( e, dt, node, config ) {
                            anadir();
                        }
                    }
                ],
                "initComplete": function(settings, json) {
                    $(".btn-nuevo").removeClass("btn-secondary");
                    var create = $("#create").val();
                    if (!create)
                        $(".btn-nuevo").remove();
                },
                "initComplete": function(settings, json) {

                }
            });

            //window.addEventListener("resize", dt.force_redraw() );



        })


    </script>

@stop
