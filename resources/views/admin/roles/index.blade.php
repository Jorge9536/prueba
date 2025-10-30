@extends('adminlte::page')

@section('title', 'Gestión de Roles')

@section('content_header')
    <h1>Gestión de Roles</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Lista de Roles del Sistema</h3>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Permisos</th>
                                <th>Usuarios</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($roles as $role)
                            <tr>
                                <td>
                                    <span class="badge badge-{{ $role->name == 'super-admin' ? 'danger' : ($role->name == 'admin' ? 'warning' : 'secondary') }}">
                                        {{ $role->name }}
                                    </span>
                                </td>
                                <td>
                                    @foreach($role->permissions as $permission)
                                        <span class="badge badge-info mb-1">{{ $permission->name }}</span>
                                    @endforeach
                                    @if($role->permissions->count() == 0)
                                        <span class="text-muted">Sin permisos</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-primary">{{ $role->users_count ?? $role->users->count() }}</span>
                                </td>
                                <td>
                                    @if(!in_array($role->name, ['super-admin', 'admin', 'user']))
                                    <form action="{{ route('admin.roles.destroy', $role->id) }}" method="POST" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" 
                                                onclick="return confirm('¿Estás seguro de eliminar este rol?')">
                                            <i class="fas fa-trash"></i> Eliminar
                                        </button>
                                    </form>
                                    @else
                                    <span class="text-muted">Rol del sistema</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Crear Nuevo Rol</h3>
                </div>
                <form action="{{ route('admin.roles.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="form-group">
                            <label for="name">Nombre del Rol *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name') }}" required 
                                   placeholder="Ej: moderador, editor, etc.">
                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="permissions">Permisos</label>
                            <select class="form-control select2" multiple="multiple" id="permissions" 
                                    name="permissions[]" style="width: 100%;">
                                @foreach($permissions as $permission)
                                    <option value="{{ $permission->id }}" {{ in_array($permission->id, old('permissions', [])) ? 'selected' : '' }}>
                                        {{ $permission->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Crear Rol
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
    $(document).ready(function() {
        $('.select2').select2({
            placeholder: "Selecciona los permisos",
            allowClear: true
        });
    });
    </script>
@stop