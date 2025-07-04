@extends('adminlte::page')

@section('title', '')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-1">
            <div class="col-12 col-sm-12 col-md-12 col-lg-8 col-xl-8">
                <h4 class="text-sidap text-bold m-0"><i class="fas fa-fw fa-user" ></i> Usuarios</h4>
            </div>
            <div class="col-lg-4 col-xl-4 d-none d-lg-inline-block">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">pGIS</a></li>
                    <li class="breadcrumb-item active">Usuarios</li>
                </ol>
            </div>
        </div>
    </div>
@stop

@section('content')
    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover datatable compact datatable-User">
            <thead>
            <tr class="text-center">
                <th width="40">
                    Id
                </th>
                <th>
                    Nombre
                </th>
                <th>
                    Correo electrónico
                </th>
                <th>
                    Roles
                </th>
                <th style="min-width: 110px">
                    &nbsp;
                </th>
            </tr>
            </thead>
            <tbody>
            @foreach($users as $key => $user)
                <tr data-entry-id="{{ $user->id }}">
                    <td class="text-center">
                        {{ $user->id ?? '' }}
                    </td>
                    <td>
                        {{ $user->name ?? '' }}
                    </td>
                    <td>
                        {{ $user->email ?? '' }}
                    </td>
                    <td>
                        @foreach($user->roles()->pluck('name') as $role)
                            <span class="badge badge-primary">{{ $role }}</span>
                        @endforeach
                    </td>
                    <td class="text-center">
                        <a class="btn btn-xs text-xs btn-primary" href="{{ route('users.edit', $user->id) }}">
                            Editar
                        </a>

                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('¿ Está seguro ?');" style="display: inline-block;">
                            <input type="hidden" name="_method" value="DELETE">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <input type="submit" class="btn btn-xs text-xs btn-danger" value="Eliminar">
                        </form>

                    </td>

                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@stop

@section('css')
    <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.11.0/css/jquery.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.0.0/css/buttons.dataTables.min.css">

    <link rel="stylesheet" href="{{ asset('css/admin_custom.css') }}">
<link rel="stylesheet" href="{{ asset('css/bootstrap_4_extend.css') }}">
@stop

@section('js')
    <script src="{{ asset('js/admin_custom.js') }}"></script>
    <script src="{{ asset('js/moment.min.js') }}"></script>
    <script src="{{ asset('js/moment-with-locales.js') }}"></script>
    <script type="text/javascript" charset="utf8" src="//cdn.datatables.net/1.11.0/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/plug-ins/1.11.0/sorting/datetime-moment.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.0/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.0/js/buttons.html5.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.0/js/buttons.print.min.js"></script>
    <script>
        $(function () {
            let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
            $.extend(true, $.fn.dataTable.defaults, {
                order: [[ 0, 'asc' ]],
                lengthMenu: [ 15, 30, 50, 75, 100 ],
                pageLength: 15,
                dom: 'Blpfrtip',
                language: {
                    url: '/js/datatables/lang/' + document.getElementsByTagName("html")[0].getAttribute("lang") + '.json'
                },
                buttons: [
                    {
                        text: 'Añadir Usuario',
                        action: function ( e, dt, node, config ) {
                            window.location = "users/create";
                        }
                    }
                ],
                "initComplete": function(settings, json) {
                    $(".dt-button").addClass("btn btn-sm btn-success mr-2");
                    $(".dt-button").removeClass("dt-button");
                }
            });
            $('.datatable-User:not(.ajaxTable)').DataTable()
            $('a[data-toggle="tab"]').on('shown.bs.tab', function(e){
                $($.fn.dataTable.tables(true)).DataTable()
                    .columns.adjust();
            });
        })
    </script>
@stop
