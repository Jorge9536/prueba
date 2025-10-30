@extends('adminlte::page')

@section('title', 'Crear Evento Global')

@section('content_header')
    <h1>Crear Evento Global</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Crear Evento para Múltiples Usuarios</h3>
                </div>
                <form action="{{ route('admin.events.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="form-group">
                            <label for="title">Título del Evento *</label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                   id="title" name="title" value="{{ old('title') }}" required 
                                   placeholder="Ingrese el título del evento">
                            @error('title')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="description">Descripción *</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3" 
                                      required placeholder="Describa el evento">{{ old('description') }}</textarea>
                            @error('description')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="start_date">Fecha y Hora de Inicio *</label>
                                    <input type="datetime-local" class="form-control @error('start_date') is-invalid @enderror" 
                                           id="start_date" name="start_date" value="{{ old('start_date') }}" required>
                                    @error('start_date')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="end_date">Fecha y Hora de Fin</label>
                                    <input type="datetime-local" class="form-control @error('end_date') is-invalid @enderror" 
                                           id="end_date" name="end_date" value="{{ old('end_date') }}">
                                    @error('end_date')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="location">Ubicación</label>
                            <input type="text" class="form-control @error('location') is-invalid @enderror" 
                                   id="location" name="location" value="{{ old('location') }}" 
                                   placeholder="Ubicación del evento">
                            @error('location')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="target_users">Seleccionar Usuarios *</label>
                            <select class="form-control select2" multiple="multiple" id="target_users" 
                                    name="target_users[]" required style="width: 100%;">
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ in_array($user->id, old('target_users', [])) ? 'selected' : '' }}>
                                        {{ $user->name }} ({{ $user->email }})
                                    </option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">
                                Selecciona los usuarios que recibirán este evento
                            </small>
                            @error('target_users')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="all_day" name="all_day" {{ old('all_day') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="all_day">Todo el día</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="has_reminder" name="has_reminder" {{ old('has_reminder') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="has_reminder">Activar recordatorio</label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group" id="reminder_minutes_container" style="display: none;">
                            <label for="reminder_minutes">Minutos antes del recordatorio</label>
                            <select class="form-control" id="reminder_minutes" name="reminder_minutes">
                                <option value="5" {{ old('reminder_minutes') == 5 ? 'selected' : '' }}>5 minutos</option>
                                <option value="15" {{ old('reminder_minutes') == 15 ? 'selected' : '' }}>15 minutos</option>
                                <option value="30" {{ old('reminder_minutes') == 30 ? 'selected' : '' }}>30 minutos</option>
                                <option value="60" {{ old('reminder_minutes') == 60 ? 'selected' : '' }}>1 hora</option>
                                <option value="1440" {{ old('reminder_minutes') == 1440 ? 'selected' : '' }}>1 día</option>
                            </select>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-bullhorn"></i> Crear Evento Global
                        </button>
                        <a href="{{ route('admin.events.index') }}" class="btn btn-default">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title">Información</h3>
                </div>
                <div class="card-body">
                    <p><strong>Eventos Globales:</strong></p>
                    <ul>
                        <li>Serán visibles para todos los usuarios seleccionados</li>
                        <li>Aparecerán en color rojo en el calendario</li>
                        <li>Solo administradores pueden crearlos</li>
                        <li>Los usuarios no pueden editarlos</li>
                    </ul>
                </div>
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
        // Inicializar Select2
        $('.select2').select2({
            placeholder: "Selecciona los usuarios",
            allowClear: true
        });

        // Toggle reminder field
        $('#has_reminder').change(function() {
            if(this.checked) {
                $('#reminder_minutes_container').show();
            } else {
                $('#reminder_minutes_container').hide();
            }
        });
        
        // Estado inicial
        if ($('#has_reminder').is(':checked')) {
            $('#reminder_minutes_container').show();
        }
    });
    </script>
@stop