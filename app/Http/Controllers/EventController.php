<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class EventController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $events = Event::visibleTo(Auth::user())->get();
        return view('agenda.index', compact('events'));
    }

    public function calendar()
    {
        return view('agenda.calendar');
    }

    public function getEvents()
    {
        $events = Event::visibleTo(Auth::user())->get()->map(function ($event) {
            $isAssigned = $event->assignedUsers->contains(Auth::id());
            $isAdminEvent = $event->is_admin_event;
            
            return [
                'id' => $event->id,
                'title' => $event->title . 
                          ($isAdminEvent ? ' (Admin)' : '') . 
                          ($event->user_id !== Auth::id() && !$isAdminEvent ? ' (Compartido)' : ''),
                'start' => $event->start_date,
                'end' => $event->end_date,
                'allDay' => $event->all_day,
                'color' => $this->getEventColor($event),
                'description' => $event->description,
                'location' => $event->location,
                'has_reminder' => $event->has_reminder,
                'reminder_minutes' => $event->reminder_minutes,
                'visibility' => $event->visibility,
                'editable' => $this->canEditEvent($event),
                'user_name' => $event->user->name,
                'is_owner' => $event->user_id === Auth::id(),
                'is_admin_event' => $isAdminEvent,
                'is_assigned' => $isAssigned
            ];
        });

        return response()->json($events);
    }

    private function getEventColor($event)
    {
        if ($event->is_admin_event) {
            return '#dc3545'; // Rojo para eventos del admin
        }
        if ($event->user_id !== Auth::id()) {
            return '#6c757d'; // Gris para eventos compartidos
        }
        return $event->color; // Color personalizado para eventos propios
    }

    private function canEditEvent($event)
    {
        $user = Auth::user();
        return $event->user_id === $user->id || 
               $user->hasRole(['super-admin', 'admin']);
    }

    public function create()
    {
        $users = [];
        if (Auth::user()->hasRole(['super-admin', 'admin'])) {
            $users = User::role('user')->get();
        }
        
        return view('agenda.create', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'description' => 'nullable|string',
            'location' => 'nullable|string|max:255',
        ]);

        try {
            $eventData = [
                'title' => $request->title,
                'description' => $request->description,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'all_day' => $request->has('all_day'),
                'has_reminder' => $request->has('has_reminder'),
                'reminder_minutes' => $request->reminder_minutes,
                'color' => $request->color ?? '#3498db',
                'location' => $request->location,
                'visibility' => $request->visibility ?? 'private',
                'user_id' => Auth::id(),
                'is_admin_event' => $request->has('is_admin_event') && Auth::user()->hasRole(['super-admin', 'admin']),
            ];

            $event = Event::create($eventData);

            // Si es evento del admin y hay usuarios seleccionados
            if ($request->has('is_admin_event') && $request->has('target_users')) {
                $event->assignedUsers()->sync($request->target_users);
            }

            return redirect()->route('agenda.calendar')
                ->with('success', 'Evento creado exitosamente.');
                
        } catch (\Exception $e) {
            return back()->with('error', 'Error al crear el evento: ' . $e->getMessage());
        }
    }

    public function show(Event $event)
    {
        // Verificar permisos
        if (!$event->isOwner(Auth::user()) && !$event->isPublic() && !$event->isAdminEvent() && !Auth::user()->hasRole(['super-admin', 'admin'])) {
            return redirect()->route('agenda.calendar')
                ->with('error', 'No tienes permisos para ver este evento.');
        }

        return view('agenda.show', compact('event'));
    }

    public function edit(Event $event)
    {
        // Verificar permisos
        if (!$event->isOwner(Auth::user()) && !Auth::user()->hasRole(['super-admin', 'admin'])) {
            return redirect()->route('agenda.calendar')
                ->with('error', 'No tienes permisos para editar este evento.');
        }

        $users = [];
        if (Auth::user()->hasRole(['super-admin', 'admin'])) {
            $users = User::role('user')->get();
        }

        return view('agenda.edit', compact('event', 'users'));
    }

    public function update(Request $request, Event $event)
    {
        // Verificar permisos
        if (!$event->isOwner(Auth::user()) && !Auth::user()->hasRole(['super-admin', 'admin'])) {
            return redirect()->route('agenda.calendar')
                ->with('error', 'No tienes permisos para editar este evento.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'description' => 'nullable|string',
            'location' => 'nullable|string|max:255',
        ]);

        try {
            $eventData = [
                'title' => $request->title,
                'description' => $request->description,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'all_day' => $request->has('all_day'),
                'has_reminder' => $request->has('has_reminder'),
                'reminder_minutes' => $request->reminder_minutes,
                'color' => $request->color ?? $event->color,
                'location' => $request->location,
                'visibility' => $request->visibility ?? $event->visibility,
            ];

            // Solo admin puede cambiar is_admin_event
            if (Auth::user()->hasRole(['super-admin', 'admin'])) {
                $eventData['is_admin_event'] = $request->has('is_admin_event');
            }

            $event->update($eventData);

            // Si es evento del admin y hay usuarios seleccionados
            if ($request->has('is_admin_event') && $request->has('target_users') && Auth::user()->hasRole(['super-admin', 'admin'])) {
                $event->assignedUsers()->sync($request->target_users);
            }

            return redirect()->route('agenda.calendar')
                ->with('success', 'Evento actualizado exitosamente.');
                
        } catch (\Exception $e) {
            return back()->with('error', 'Error al actualizar el evento: ' . $e->getMessage());
        }
    }

    public function destroy(Event $event)
    {
        // Verificar permisos
        if (!$event->isOwner(Auth::user()) && !Auth::user()->hasRole(['super-admin', 'admin'])) {
            return redirect()->route('agenda.calendar')
                ->with('error', 'No tienes permisos para eliminar este evento.');
        }

        try {
            $event->delete();
            return redirect()->route('agenda.calendar')
                ->with('success', 'Evento eliminado exitosamente.');
                
        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar el evento: ' . $e->getMessage());
        }
    }

    // Métodos de administración
    public function adminEvents()
    {
        $events = Event::with(['user', 'assignedUsers'])->latest()->get();
        return view('admin.events.index', compact('events'));
    }

    public function createAdminEvent()
    {
        $users = User::role('user')->get();
        return view('admin.events.create', compact('users'));
    }

    public function storeAdminEvent(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'description' => 'required|string',
            'target_users' => 'required|array',
            'target_users.*' => 'exists:users,id'
        ]);

        try {
            $event = Event::create([
                'title' => $request->title,
                'description' => $request->description,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'all_day' => $request->has('all_day'),
                'has_reminder' => $request->has('has_reminder'),
                'reminder_minutes' => $request->reminder_minutes,
                'color' => '#dc3545', // Color rojo para eventos del admin
                'location' => $request->location,
                'user_id' => Auth::id(),
                'is_admin_event' => true,
                'visibility' => 'public'
            ]);

            // Asignar evento a usuarios seleccionados
            $event->assignedUsers()->sync($request->target_users);

            return redirect()->route('admin.events.index')
                ->with('success', 'Evento administrativo creado exitosamente para los usuarios seleccionados.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error al crear el evento administrativo: ' . $e->getMessage());
        }
    }

    // Sistema de alarmas
    public function getUpcomingEvents()
    {
        $now = now();
        $oneHourFromNow = now()->addHour();
        
        $events = Event::where('has_reminder', true)
            ->where('start_date', '>', $now)
            ->where('start_date', '<=', $oneHourFromNow)
            ->where(function($query) {
                $query->where('user_id', Auth::id())
                      ->orWhere('is_admin_event', true)
                      ->orWhere('visibility', 'public')
                      ->orWhereHas('assignedUsers', function($q) {
                          $q->where('user_id', Auth::id());
                      });
            })
            ->with('user')
            ->get()
            ->map(function ($event) {
                $minutesUntil = $event->start_date->diffInMinutes(now());
                return [
                    'id' => $event->id,
                    'title' => $event->title,
                    'start_date' => $event->start_date->toISOString(),
                    'minutes_until' => $minutesUntil,
                    'reminder_minutes' => $event->reminder_minutes,
                    'is_reminder_time' => $minutesUntil <= $event->reminder_minutes,
                    'notified' => false
                ];
            });

        return response()->json($events);
    }

    // Métodos adicionales
    public function toggleReminder(Request $request, Event $event)
    {
        if (!$event->isOwner(Auth::user()) && !Auth::user()->hasRole(['super-admin', 'admin'])) {
            return response()->json(['error' => 'No tienes permisos para modificar este evento'], 403);
        }

        try {
            $event->update([
                'has_reminder' => !$event->has_reminder,
                'reminder_minutes' => $request->reminder_minutes ?? $event->reminder_minutes
            ]);

            return response()->json([
                'success' => true,
                'has_reminder' => $event->has_reminder,
                'message' => $event->has_reminder ? 'Recordatorio activado' : 'Recordatorio desactivado'
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al actualizar el recordatorio'], 500);
        }
    }
}