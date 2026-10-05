<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class AttendanceCsvExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $attendances;

    public function __construct($attendances)
    {
        $this->attendances = $attendances;
    }

    public function collection()
    {
        return $this->attendances->map(function ($a) {
            return [
                $a->date->format('Y-m-d'),
                $a->employee->employee_id ?? 'N/A',
                $a->employee->employee_code ?? 'N/A',
                $a->employee->full_name ?? 'N/A',
                $a->employee->department ?? 'N/A',
                $a->employee->role ?? 'N/A',
                $a->check_in_time,
                $a->check_out_time ?? '-',
                $a->working_hours_formatted,
                $a->status,
                $a->work_category,
                $a->task_description ?? '-',
                $a->remarks ?? '-',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Date',
            'Employee ID',
            'Employee Code',
            'Employee Name',
            'Department',
            'Role',
            'Check-in',
            'Check-out',
            'Working Hours',
            'Status',
            'Work Category',
            'Task Description',
            'Remarks',
        ];
    }
}
