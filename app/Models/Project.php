<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'name',
        'client',
        'manager',
        'status',
        'start_date',
        'end_date',
        'description',
        'is_demo',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_demo' => 'boolean',
    ];

    public function assignments()
    {
        return $this->hasMany(ProjectAssignment::class);
    }

    public function employees()
    {
        return $this->belongsToMany(Employee::class, 'project_assignments')->withPivot('role', 'assigned_at')->withTimestamps();
    }
}
