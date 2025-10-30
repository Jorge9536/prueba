@extends('layouts.app')

@section('title', 'Nuevo Evento')
@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">Nuevo Evento</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('events.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="title" class="form-label">Título *</label>
                        <input type="text" class="form-control" id="title" name="title" value="{{ old('title') }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Descripción</label>
                        <textarea class="form-control" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="start_date" class="form-label">Fecha y Hora de Inicio *</label>
                                <input type="datetime-local" class="form-control" id="start_date" name="start_date" 
                                       value="{{ old('start_date', request('date') ? request('date') . 'T09:00' : '') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="end_date" class="form-label">Fecha y Hora de Fin</label>
                                <input type="datetime-local" class="form-control" id="end_date" name="end_date" value="{{ old('end_date') }}">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="location" class="form-label">Ubicación</label>
                        <input type="text" class="form-control" id="location" name="location" value="{{ old('location') }}">
                    </div>

                    <div class="mb-3">
                        <label for="color" class="form-label">Color</label>
                        <input type="color" class="form-control form-control-color" id="color" name="color" value="{{ old('color', '#3498db') }}" title="Elige un color">
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="all_day" name="all_day" {{ old('all_day') ? 'checked' : '' }}>
                                <label class="form-check-label" for="all_day">Todo el día</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="has_reminder" name="has_reminder" {{ old('has_reminder') ? 'checked' : '' }}>
                                <label class="form-check-label" for="has_reminder">Activar recordatorio</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="visibility" class="form-label">Visibilidad</label>
                                <select class="form-select" id="visibility" name="visibility">
                                    <option value="private" {{ old('visibility') == 'private' ? 'selected' : '' }}>Privado</option>
                                    <option value="public" {{ old('visibility') == 'public' ? 'selected' : '' }}>Público</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3" id="reminder_minutes_container" style="display: none;">
                        <label for="reminder_minutes" class="form-label">Minutos antes del recordatorio</label>
                        <select class="form-select" id="reminder_minutes" name="reminder_minutes">
                            <option value="5">5 minutos</option>
                            <option value="15">15 minutos</option>
                            <option value="30">30 minutos</option>
                            <option value="60">1 hora</option>
                            <option value="1440">1 día</option>
                        </select>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="{{ route('agenda.calendar') }}" class="btn btn-secondary me-md-2">Cancelar</a>
                        <button type="submit" class="btn btn-primary">Guardar Evento</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const hasReminderCheckbox = document.getElementById('has_reminder');
    const reminderContainer = document.getElementById('reminder_minutes_container');

    function toggleReminderField() {
        reminderContainer.style.display = hasReminderCheckbox.checked ? 'block' : 'none';
    }

    hasReminderCheckbox.addEventListener('change', toggleReminderField);
    toggleReminderField(); // Estado inicial
});
</script>
@endpush