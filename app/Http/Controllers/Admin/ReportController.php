<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Project;
use App\Models\AuditLog;
use App\Exports\AttendanceReportExport;
use App\Exports\AttendanceCsvExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $reportType = $request->input('report_type', 'all');
        $startDate = $request->input('start_date', Carbon::today()->subDays(30)->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::today()->format('Y-m-d'));
        $employeeId = $request->input('employee_id');
        $department = $request->input('department');
        $status = $request->input('status');
        $project = $request->input('project');

        // Quick presets
        if ($preset = $request->input('preset')) {
            if ($preset === 'today') {
                $startDate = $endDate = Carbon::today()->format('Y-m-d');
            } elseif ($preset === '7days') {
                $startDate = Carbon::today()->subDays(6)->format('Y-m-d');
                $endDate = Carbon::today()->format('Y-m-d');
            } elseif ($preset === 'month') {
                $startDate = Carbon::today()->startOfMonth()->format('Y-m-d');
                $endDate = Carbon::today()->endOfMonth()->format('Y-m-d');
            }
        }

        $query = Attendance::with('employee')->whereBetween('date', [$startDate, $endDate]);

        if ($employeeId) {
            $query->where('employee_id', $employeeId);
        }

        if ($department) {
            $query->whereHas('employee', function ($q) use ($department) {
                $q->where('department', $department);
            });
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($project) {
            $query->where('work_category', $project);
        }

        // Specific report types
        if ($reportType === 'late') {
            $query->where('status', 'LATE');
        } elseif ($reportType === 'absent') {
            $query->where('status', 'ABSENT');
        } elseif ($reportType === 'leave') {
            $query->where('status', 'LEAVE');
        }

        $records = $query->orderBy('date', 'desc')->get();

        // Calculate summary statistics
        $totalRecords = $records->count();
        $presentCount = $records->where('status', 'PRESENT')->count();
        $lateCount = $records->where('status', 'LATE')->count();
        $absentCount = $records->where('status', 'ABSENT')->count();
        $leaveCount = $records->where('status', 'LEAVE')->count();
        $rate = $totalRecords > 0 ? round((($presentCount + $lateCount) / $totalRecords) * 100) : 0;
        $totalMinutes = $records->sum('working_minutes');
        $totalHours = round($totalMinutes / 60, 1);

        $summary = [
            'period' => Carbon::parse($startDate)->format('d M Y') . ' to ' . Carbon::parse($endDate)->format('d M Y'),
            'total' => $totalRecords,
            'present' => $presentCount,
            'late' => $lateCount,
            'absent' => $absentCount,
            'leave' => $leaveCount,
            'rate' => $rate,
            'totalHours' => $totalHours,
            'generated_by' => auth()->user()->name,
        ];

        // Export handles
        if ($request->input('export') === 'pdf') {
            ini_set('memory_limit', '512M');
            ini_set('max_execution_time', '300');
            AuditLog::log('EXPORT_PDF', 'REPORTS', null, ['period' => $summary['period'], 'count' => $totalRecords]);

            $pdf = Pdf::loadView('admin.reports.pdf', compact('records', 'summary', 'startDate', 'endDate'))
                ->setPaper('a4', 'landscape');
            return $pdf->download("IntraEats_HR_Attendance_Report_{$startDate}_to_{$endDate}.pdf");
        }

        if ($request->input('export') === 'excel') {
            AuditLog::log('EXPORT_EXCEL', 'REPORTS', null, ['period' => $summary['period'], 'count' => $totalRecords]);

            return Excel::download(
                new AttendanceReportExport($records, $summary),
                "IntraEats_HR_Attendance_Report_{$startDate}_to_{$endDate}.xlsx"
            );
        }

        if ($request->input('export') === 'csv') {
            AuditLog::log('EXPORT_CSV', 'REPORTS', null, ['period' => $summary['period'], 'count' => $totalRecords]);

            return Excel::download(
                new AttendanceCsvExport($records),
                "IntraEats_HR_Attendance_Report_{$startDate}_to_{$endDate}.csv"
            );
        }

        $allEmployees = Employee::active()->orderBy('full_name')->get();
        $departments = Employee::distinct()->pluck('department')->filter();
        $projects = Project::orderBy('name')->pluck('name');

        return view('admin.reports.index', compact(
            'records',
            'summary',
            'startDate',
            'endDate',
            'reportType',
            'allEmployees',
            'departments',
            'projects'
        ));
    }
}
