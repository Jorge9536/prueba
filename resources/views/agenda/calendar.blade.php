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
                        @if(Auth::user()->hasRole(['super-admin', 'admin']))
                        <a href="{{ route('admin.events.create') }}" class="btn btn-success btn-sm ml-2">
                            <i class="fas fa-bullhorn"></i> Evento Global
                        </a>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <!-- Alertas de recordatorios -->
                    <div id="alerts-container" class="mb-3"></div>
                    
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

    <!-- Modal para alarmas -->
    <div class="modal fade" id="alarmModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title"><i class="fas fa-bell"></i> Recordatorio</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="alarmModalBody">
                    <!-- Contenido dinámico de alarmas -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
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
            border: none;
            font-size: 0.85em;
            font-weight: bold;
        }
        .fc-event-admin {
            background-color: #dc3545 !important;
            border-color: #dc3545 !important;
        }
        .fc-event-shared {
            background-color: #6c757d !important;
            border-color: #6c757d !important;
        }
        .fc-event-reminder {
            border-left: 4px solid #ffc107 !important;
        }
        .alarm-notification {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            min-width: 300px;
        }
        .event-badge {
            font-size: 0.7em;
            margin-left: 5px;
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
            events: {
                url: '{{ route("agenda.events") }}',
                failure: function() {
                    alert('Error al cargar los eventos');
                }
            },
            eventDidMount: function(info) {
                // Aplicar estilos según el tipo de evento
                if (info.event.extendedProps.is_admin_event) {
                    info.el.classList.add('fc-event-admin');
                } else if (!info.event.extendedProps.is_owner) {
                    info.el.classList.add('fc-event-shared');
                }
                
                if (info.event.extendedProps.has_reminder) {
                    info.el.classList.add('fc-event-reminder');
                }

                // Agregar tooltip con información adicional
                if (info.event.extendedProps.description) {
                    info.el.setAttribute('title', info.event.extendedProps.description);
                }
            },
            dateClick: function(info) {
                window.location.href = '{{ route("events.create") }}?date=' + info.dateStr;
            },
            eventClick: function(info) {
                var event = info.event;
                var modal = new bootstrap.Modal(document.getElementById('eventModal'));
                
                // Construir contenido del modal
                let modalContent = `
                    <p><strong>Descripción:</strong> ${event.extendedProps.description || 'Sin descripción'}</p>
                    <p><strong>Ubicación:</strong> ${event.extendedProps.location || 'No especificada'}</p>
                    <p><strong>Inicio:</strong> ${event.start ? event.start.toLocaleString('es-ES') : ''}</p>
                    <p><strong>Fin:</strong> ${event.end ? event.end.toLocaleString('es-ES') : 'No definido'}</p>
                    <p><strong>Creado por:</strong> ${event.extendedProps.user_name}</p>
                `;

                if (event.extendedProps.has_reminder) {
                    modalContent += `<p><strong>Recordatorio:</strong> ${event.extendedProps.reminder_minutes} minutos antes</p>`;
                }

                if (event.extendedProps.is_admin_event) {
                    modalContent += `<p><span class="badge badge-danger">Evento Administrativo</span></p>`;
                } else if (!event.extendedProps.is_owner) {
                    modalContent += `<p><span class="badge badge-secondary">Evento Compartido</span></p>`;
                }

                document.getElementById('eventModalTitle').textContent = event.title;
                document.getElementById('eventModalBody').innerHTML = modalContent;
                
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

        // ========== SISTEMA DE ALARMAS ==========
        
        // Función para verificar recordatorios
        function checkReminders() {
            fetch('/agenda/upcoming-events')
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Error en la respuesta');
                    }
                    return response.json();
                })
                .then(events => {
                    events.forEach(event => {
                        if (event.is_reminder_time && !event.notified) {
                            showAlarmNotification(event);
                            // Marcar como notificado para evitar notificaciones repetidas
                            event.notified = true;
                        }
                    });
                })
                .catch(error => {
                    console.error('Error al verificar recordatorios:', error);
                });
        }

        // Función para mostrar notificación de alarma
        function showAlarmNotification(event) {
            const alarmMessage = `
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <h5><i class="fas fa-bell"></i> ${event.title}</h5>
                    <p class="mb-1">El evento comienza en ${event.minutes_until} minutos</p>
                    <small>Hora de inicio: ${new Date(event.start_date).toLocaleString('es-ES')}</small>
                    <button type="button" class="close" data-dismiss="alert">
                        <span>&times;</span>
                    </button>
                </div>
            `;

            // Agregar al contenedor de alertas
            document.getElementById('alerts-container').insertAdjacentHTML('afterbegin', alarmMessage);

            // Mostrar notificación del navegador si está permitido
            if ("Notification" in window) {
                if (Notification.permission === "granted") {
                    new Notification(`🔔 ${event.title}`, {
                        body: `Comienza en ${event.minutes_until} minutos\n${new Date(event.start_date).toLocaleString('es-ES')}`,
                        icon: '/vendor/adminlte/dist/img/AdminLTELogo.png',
                        tag: 'event-reminder'
                    });
                } else if (Notification.permission !== "denied") {
                    Notification.requestPermission().then(permission => {
                        if (permission === "granted") {
                            new Notification(`🔔 ${event.title}`, {
                                body: `Comienza en ${event.minutes_until} minutos\n${new Date(event.start_date).toLocaleString('es-ES')}`,
                                icon: '/vendor/adminlte/dist/img/AdminLTELogo.png',
                                tag: 'event-reminder'
                            });
                        }
                    });
                }
            }

            // Reproducir sonido de notificación (opcional)
            playNotificationSound();
        }

        // Función para reproducir sonido de notificación
        function playNotificationSound() {
            try {
                const audioContext = new (window.AudioContext || window.webkitAudioContext)();
                const oscillator = audioContext.createOscillator();
                const gainNode = audioContext.createGain();
                
                oscillator.connect(gainNode);
                gainNode.connect(audioContext.destination);
                
                oscillator.frequency.value = 800;
                oscillator.type = 'sine';
                
                gainNode.gain.setValueAtTime(0, audioContext.currentTime);
                gainNode.gain.linearRampToValueAtTime(0.1, audioContext.currentTime + 0.01);
                gainNode.gain.linearRampToValueAtTime(0, audioContext.currentTime + 0.5);
                
                oscillator.start(audioContext.currentTime);
                oscillator.stop(audioContext.currentTime + 0.5);
            } catch (error) {
                console.log('No se pudo reproducir el sonido:', error);
            }
        }

        // Función para cargar eventos próximos (para el panel de alarmas)
        function loadUpcomingEvents() {
            fetch('/agenda/upcoming-events')
                .then(response => response.json())
                .then(events => {
                    const upcomingEvents = events.filter(event => 
                        event.minutes_until <= 60 && event.minutes_until > 0
                    );
                    
                    if (upcomingEvents.length > 0) {
                        showUpcomingEventsPanel(upcomingEvents);
                    }
                })
                .catch(error => {
                    console.error('Error al cargar eventos próximos:', error);
                });
        }

        // Función para mostrar panel de eventos próximos
        function showUpcomingEventsPanel(events) {
            let panelContent = '<h6>Eventos próximos:</h6>';
            
            events.forEach(event => {
                panelContent += `
                    <div class="border-bottom pb-2 mb-2">
                        <strong>${event.title}</strong><br>
                        <small>En ${event.minutes_until} minutos</small>
                    </div>
                `;
            });

            document.getElementById('alerts-container').innerHTML = `
                <div class="alert alert-info">
                    ${panelContent}
                </div>
            `;
        }

        // Inicializar sistema de alarmas
        function initializeAlarmSystem() {
            // Solicitar permisos para notificaciones
            if ("Notification" in window && Notification.permission === "default") {
                Notification.requestPermission();
            }

            // Verificar recordatorios cada 30 segundos
            setInterval(checkReminders, 30000);
            
            // Cargar eventos próximos al iniciar
            loadUpcomingEvents();
            
            // Verificar recordatorios inmediatamente
            checkReminders();
        }

        // Iniciar el sistema de alarmas cuando la página esté cargada
        initializeAlarmSystem();

        // ========== FUNCIONALIDADES ADICIONALES ==========

        // Actualizar calendario cada 2 minutos para eventos en tiempo real
        setInterval(() => {
            calendar.refetchEvents();
        }, 120000);

        // Manejar errores de carga de eventos
        calendar.setOption('eventSourceFailure', function(error) {
            console.error('Error cargando eventos:', error);
            showToast('Error al cargar eventos', 'error');
        });

        // Función para mostrar toasts (notificaciones emergentes)
        function showToast(message, type = 'info') {
            const toast = document.createElement('div');
            toast.className = `alert alert-${type} alert-dismissible fade show`;
            toast.innerHTML = `
                ${message}
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            `;
            
            const container = document.getElementById('alerts-container');
            container.appendChild(toast);
            
            // Auto-eliminar después de 5 segundos
            setTimeout(() => {
                if (toast.parentNode) {
                    toast.remove();
                }
            }, 5000);
        }

        // Exportar calendario (funcionalidad adicional)
        document.addEventListener('keydown', function(e) {
            // Ctrl+E para exportar eventos
            if (e.ctrlKey && e.key === 'e') {
                e.preventDefault();
                exportCalendar();
            }
        });

        function exportCalendar() {
            const events = calendar.getEvents();
            const exportData = events.map(event => ({
                title: event.title,
                start: event.start ? event.start.toISOString() : null,
                end: event.end ? event.end.toISOString() : null,
                description: event.extendedProps.description,
                location: event.extendedProps.location
            }));

            const dataStr = "data:text/json;charset=utf-8," + encodeURIComponent(JSON.stringify(exportData, null, 2));
            const downloadAnchorNode = document.createElement('a');
            downloadAnchorNode.setAttribute("href", dataStr);
            downloadAnchorNode.setAttribute("download", "eventos_calendario.json");
            document.body.appendChild(downloadAnchorNode);
            downloadAnchorNode.click();
            downloadAnchorNode.remove();
            
            showToast('Calendario exportado correctamente', 'success');
        }

    });
    </script>
@stop