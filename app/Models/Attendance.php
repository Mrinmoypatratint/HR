<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'date',
        'check_in_time',
        'check_out_time',
        'working_minutes',
        'status',
        'work_category',
        'task_description',
        'remarks',
        'modified_by',
        'modified_at',
        'is_demo',
    ];

    protected $casts = [
        'date' => 'date',
        'modified_at' => 'datetime',
        'working_minutes' => 'integer',
        'is_demo' => 'boolean',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function getWorkingHoursFormattedAttribute(): string
    {
        if (!$this->working_minutes || $this->working_minutes <= 0) {
            return '00h 00m';
        }
        $hours = floor($this->working_minutes / 60);
        $minutes = $this->working_minutes % 60;
        return sprintf('%02dh %02dm', $hours, $minutes);
    }
}
