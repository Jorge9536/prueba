@extends('adminlte::page')

@section('title', 'Eventos Administrativos')

@section('content_header')
    <h1>Eventos Administrativos</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Todos los Eventos del Sistema</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.events.create') }}" class="btn btn-success btn-sm">
                            <i class="fas fa-bullhorn"></i> Crear Evento Global
                        </a>
                        <a href="{{ route('agenda.calendar') }}" class="btn btn-primary btn-sm ml-2">
                            <i class="fas fa-calendar"></i> Ver Calendario
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Título</th>
                                <th>Descripción</th>
                                <th>Fecha Inicio</th>
                                <th>Fecha Fin</th>
                                <th>Tipo</th>
                                <th>Creado por</th>
                                <th>Usuarios Asignados</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($events as $event)
                            <tr>
                                <td>{{ $event->id }}</td>
                                <td>
                                    <strong>{{ $event->title }}</strong>
                                    @if($event->is_admin_event)
                                        <span class="badge badge-danger ml-1">Admin</span>
                                    @endif
                                    @if($event->has_reminder)
                                        <span class="badge badge-warning ml-1">Recordatorio</span>
                                    @endif
                                </td>
                                <td>{{ Str::limit($event->description, 50) }}</td>
                                <td>{{ $event->start_date->format('d/m/Y H:i') }}</td>
                                <td>{{ $event->end_date ? $event->end_date->format('d/m/Y H:i') : 'No definido' }}</td>
                                <td>
                                    @if($event->is_admin_event)
                                        <span class="badge badge-danger">Administrativo</span>
                                    @else
                                        <span class="badge badge-primary">Personal</span>
                                    @endif
                                </td>
                                <td>{{ $event->user->name }}</td>
                                <td>
                                    @if($event->is_admin_event && $event->assignedUsers->count() > 0)
                                        <span class="badge badge-info">{{ $event->assignedUsers->count() }} usuarios</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('events.show', $event) }}" class="btn btn-info btn-sm">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('events.edit', $event) }}" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('events.destroy', $event) }}" method="POST" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" 
                                                onclick="return confirm('¿Estás seguro de eliminar este evento?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .table td {
            vertical-align: middle;
        }
    </style>
@stop