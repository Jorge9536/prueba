@extends('adminlte::page')

@section('title', 'Calendario')

@section('content_header')
    <h1>Calendario de Eventos</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Mi Agenda</h3>
                    <div class="card-tools">
                        <a href="{{ route('events.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Nuevo Evento
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div id="calendar"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para ver evento -->
    <div class="modal fade" id="eventModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="eventModalTitle">Evento</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="eventModalBody">
                    <!-- Contenido dinámico -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    <a href="#" class="btn btn-primary" id="eventModalEdit" style="display: none;">Editar</a>
                    <form id="eventModalDelete" method="POST" style="display: none;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" onclick="return confirm('¿Estás seguro?')">Eliminar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.css" rel="stylesheet">
    <style>
        #calendar {
            max-width: 100%;
            margin: 0 auto;
        }
        .fc-event {
            cursor: pointer;
        }
    </style>
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/locales/es.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'es',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            events: '{{ route("agenda.events") }}',
            dateClick: function(info) {
                window.location.href = '{{ route("events.create") }}?date=' + info.dateStr;
            },
            eventClick: function(info) {
                var event = info.event;
                var modal = new bootstrap.Modal(document.getElementById('eventModal'));
                
                // Contenido del modal
                document.getElementById('eventModalTitle').textContent = event.title;
                document.getElementById('eventModalBody').innerHTML = `
                    <p><strong>Descripción:</strong> ${event.extendedProps.description || 'Sin descripción'}</p>
                    <p><strong>Ubicación:</strong> ${event.extendedProps.location || 'No especificada'}</p>
                    <p><strong>Inicio:</strong> ${event.start ? event.start.toLocaleString() : ''}</p>
                    <p><strong>Fin:</strong> ${event.end ? event.end.toLocaleString() : 'No definido'}</p>
                    ${event.extendedProps.has_reminder ? `<p><strong>Recordatorio:</strong> ${event.extendedProps.reminder_minutes} minutos antes</p>` : ''}
                `;
                
                // Mostrar botones solo si tiene permisos
                const editBtn = document.getElementById('eventModalEdit');
                const deleteForm = document.getElementById('eventModalDelete');
                
                if (event.extendedProps.editable) {
                    editBtn.href = '/agenda/events/' + event.id + '/edit';
                    editBtn.style.display = 'inline-block';
                    deleteForm.action = '/agenda/events/' + event.id;
                    deleteForm.style.display = 'inline-block';
                } else {
                    editBtn.style.display = 'none';
                    deleteForm.style.display = 'none';
                }
                
                modal.show();
            }
        });
        
        calendar.render();
    });
    </script>
@stop