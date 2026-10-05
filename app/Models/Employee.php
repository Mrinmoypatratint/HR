<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'employee_code',
        'full_name',
        'email',
        'mobile',
        'photo_url',
        'joining_date',
        'department',
        'designation',
        'role',
        'employment_type',
        'assigned_project',
        'status',
        'address',
        'emergency_contact',
        'notes',
        'is_demo',
    ];

    protected $casts = [
        'joining_date' => 'date',
        'is_demo' => 'boolean',
    ];

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function assignments()
    {
        return $this->hasMany(ProjectAssignment::class);
    }

    public function projects()
    {
        return $this->belongsToMany(Project::class, 'project_assignments')->withPivot('role', 'assigned_at')->withTimestamps();
    }

    public function getInitialsAttribute(): string
    {
        $words = explode(' ', trim($this->full_name));
        $initials = '';
        foreach ($words as $w) {
            if (!empty($w)) {
                $initials .= strtoupper($w[0]);
            }
        }
        return substr($initials, 0, 2) ?: 'EM';
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'ACTIVE');
    }
}
