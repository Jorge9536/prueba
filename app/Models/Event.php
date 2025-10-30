<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'start_date',
        'end_date',
        'all_day',
        'has_reminder',
        'reminder_minutes',
        'color',
        'location',
        'visibility',
        'user_id',
        'is_admin_event',
        'target_users'
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'all_day' => 'boolean',
        'has_reminder' => 'boolean',
        'is_admin_event' => 'boolean',
        'target_users' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function assignedUsers()
    {
        return $this->belongsToMany(User::class, 'event_user')->withTimestamps();
    }

    public function shouldRemind()
    {
        return $this->has_reminder && $this->reminder_minutes;
    }

    public function getReminderTimeAttribute()
    {
        if ($this->shouldRemind()) {
            return $this->start_date->subMinutes($this->reminder_minutes);
        }
        return null;
    }

    public function isOwner($user)
    {
        return $this->user_id === $user->id;
    }

    public function isPublic()
    {
        return $this->visibility === 'public';
    }

    public function isAdminEvent()
    {
        return $this->is_admin_event;
    }

    // Scope para eventos visibles por el usuario
    public function scopeVisibleTo($query, User $user)
    {
        if ($user->hasRole(['super-admin', 'admin'])) {
            return $query; // Admin ve todos los eventos
        }

        return $query->where(function($q) use ($user) {
            $q->where('user_id', $user->id)
              ->orWhere('visibility', 'public')
              ->orWhere('is_admin_event', true)
              ->orWhereHas('assignedUsers', function($query) use ($user) {
                  $query->where('user_id', $user->id);
              });
        });
    }

    public function getAssignedUsersListAttribute()
    {
        if ($this->target_users) {
            return User::whereIn('id', $this->target_users)->get();
        }
        return collect();
    }
}