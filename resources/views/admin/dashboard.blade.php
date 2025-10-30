@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Dashboard de Administración</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ \App\Models\User::count() }}</h3>
                    <p>Usuarios Registrados</p>
                </div>
                <div class="icon">
                    <i class="fas fa-users"></i>
                </div>
                <a href="{{ route('admin.users.index') }}" class="small-box-footer">
                    Más info <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ \App\Models\Event::count() }}</h3>
                    <p>Total Eventos</p>
                </div>
                <div class="icon">
                    <i class="far fa-calendar-alt"></i>
                </div>
                <a href="{{ route('agenda.calendar') }}" class="small-box-footer">
                    Más info <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ \App\Models\Event::where('is_admin_event', true)->count() }}</h3>
                    <p>Eventos Globales</p>
                </div>
                <div class="icon">
                    <i class="fas fa-bullhorn"></i>
                </div>
                <a href="{{ route('admin.events.create') }}" class="small-box-footer">
                    Crear nuevo <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ \Spatie\Permission\Models\Role::count() }}</h3>
                    <p>Roles del Sistema</p>
                </div>
                <div class="icon">
                    <i class="fas fa-user-shield"></i>
                </div>
                <a href="{{ route('admin.roles.index') }}" class="small-box-footer">
                    Gestionar <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Bienvenido al Panel de Administración</h3>
                </div>
                <div class="card-body">
                    <p>Desde aquí puedes gestionar todos los aspectos de la aplicación de agenda.</p>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="info-box bg-info">
                                <span class="info-box-icon"><i class="fas fa-users"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Gestión de Usuarios</span>
                                    <span class="info-box-number">{{ \App\Models\User::count() }}</span>
                                    <div class="progress">
                                        <div class="progress-bar" style="width: 100%"></div>
                                    </div>
                                    <span class="progress-description">
                                        <a href="{{ route('admin.users.index') }}" class="text-white">Ver usuarios</a>
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="info-box bg-success">
                                <span class="info-box-icon"><i class="far fa-calendar-check"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Eventos Activos</span>
                                    <span class="info-box-number">{{ \App\Models\Event::count() }}</span>
                                    <div class="progress">
                                        <div class="progress-bar" style="width: 100%"></div>
                                    </div>
                                    <span class="progress-description">
                                        <a href="{{ route('agenda.calendar') }}" class="text-white">Ver calendario</a>
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="info-box bg-warning">
                                <span class="info-box-icon"><i class="fas fa-user-shield"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Roles del Sistema</span>
                                    <span class="info-box-number">{{ \Spatie\Permission\Models\Role::count() }}</span>
                                    <div class="progress">
                                        <div class="progress-bar" style="width: 100%"></div>
                                    </div>
                                    <span class="progress-description">
                                        <a href="{{ route('admin.roles.index') }}" class="text-white">Gestionar roles</a>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop