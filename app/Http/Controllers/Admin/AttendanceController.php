<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\ProjectCategory;
use App\Models\AuditLog;
use App\Services\AttendanceService;
use App\Imports\AttendanceImport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $query = Attendance::with('employee');

        // Search
        if ($search = $request->input('search')) {
            $query->whereHas('employee', function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('employee_code', 'like', "%{$search}%")
                  ->orWhere('employee_id', 'like', "%{$search}%")
                  ->orWhere('role', 'like', "%{$search}%")
                  ->orWhere('department', 'like', "%{$search}%");
            });
        }

        // Filters
        if ($dept = $request->input('department')) {
            $query->whereHas('employee', function ($q) use ($dept) {
                $q->where('department', $dept);
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($category = $request->input('work_category')) {
            $query->where('work_category', $category);
        }

        if ($startDate = $request->input('start_date')) {
            $query->where('date', '>=', $startDate);
        }

        if ($endDate = $request->input('end_date')) {
            $query->where('date', '<=', $endDate);
        }

        $attendances = $query->orderBy('date', 'desc')->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        $departments = Employee::distinct()->pluck('department')->filter();
        $categories = ProjectCategory::orderBy('name')->pluck('name');
        $allEmployees = Employee::active()->orderBy('full_name')->get();

        return view('admin.attendance.index', compact(
            'attendances',
            'departments',
            'categories',
            'allEmployees'
        ));
    }

    /**
     * Manual Attendance Add
     */
    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date',
            'check_in_time' => 'required|string',
            'check_out_time' => 'nullable|string',
            'status' => 'required|in:PRESENT,LATE,ABSENT,LEAVE',
            'work_category' => 'nullable|string',
            'task_description' => 'nullable|string',
            'remarks' => 'nullable|string',
        ]);

        $employee = Employee::findOrFail($request->input('employee_id'));
        $date = Carbon::parse($request->input('date'))->format('Y-m-d');

        // Check duplicate
        $existing = Attendance::where('employee_id', $employee->id)->where('date', $date)->first();
        if ($existing) {
            return back()->withErrors(['date' => "Attendance record already exists for {$employee->full_name} on {$date}."]);
        }

        $inTime = trim($request->input('check_in_time'));
        $outTime = trim($request->input('check_out_time'));
        $workingMinutes = ($inTime && $outTime && $inTime !== '-' && $outTime !== '-')
            ? AttendanceService::calculateMinutes($inTime, $outTime)
            : 0;

        $record = Attendance::create([
            'employee_id' => $employee->id,
            'date' => $date,
            'check_in_time' => $inTime,
            'check_out_time' => $outTime ?: null,
            'working_minutes' => $workingMinutes,
            'status' => $request->input('status'),
            'work_category' => $request->input('work_category', 'Internal Project'),
            'task_description' => $request->input('task_description'),
            'remarks' => $request->input('remarks'),
            'modified_by' => auth()->user()->name . ' (' . auth()->user()->role_name . ')',
            'modified_at' => now(),
            'is_demo' => false,
        ]);

        AuditLog::log('CREATE', 'ATTENDANCE', (string) $record->id, [
            'employee' => $employee->full_name,
            'date' => $date,
            'status' => $record->status,
        ]);

        return redirect()->route('admin.attendance.index')->with('success', "Manual attendance added for {$employee->full_name}.");
    }

    /**
     * Manual Attendance Update
     */
    public function update(Request $request, $id)
    {
        $attendance = Attendance::findOrFail($id);

        $request->validate([
            'check_in_time' => 'required|string',
            'check_out_time' => 'nullable|string',
            'status' => 'required|in:PRESENT,LATE,ABSENT,LEAVE',
            'work_category' => 'nullable|string',
            'task_description' => 'nullable|string',
            'remarks' => 'nullable|string',
        ]);

        $inTime = trim($request->input('check_in_time'));
        $outTime = trim($request->input('check_out_time'));
        $workingMinutes = ($inTime && $outTime && $inTime !== '-' && $outTime !== '-')
            ? AttendanceService::calculateMinutes($inTime, $outTime)
            : 0;

        $attendance->update([
            'check_in_time' => $inTime,
            'check_out_time' => $outTime ?: null,
            'working_minutes' => $workingMinutes,
            'status' => $request->input('status'),
            'work_category' => $request->input('work_category', $attendance->work_category),
            'task_description' => $request->input('task_description'),
            'remarks' => $request->input('remarks'),
            'modified_by' => auth()->user()->name . ' (' . auth()->user()->role_name . ')',
            'modified_at' => now(),
        ]);

        AuditLog::log('UPDATE', 'ATTENDANCE', (string) $attendance->id, [
            'employee' => $attendance->employee->full_name,
            'date' => $attendance->date->format('Y-m-d'),
            'status' => $attendance->status,
        ]);

        return redirect()->route('admin.attendance.index')->with('success', "Attendance record updated successfully.");
    }

    /**
     * Delete Attendance
     */
    public function destroy($id)
    {
        $attendance = Attendance::findOrFail($id);
        $empName = $attendance->employee->full_name;
        $date = $attendance->date->format('Y-m-d');

        $attendance->delete();

        AuditLog::log('DELETE', 'ATTENDANCE', (string) $id, [
            'employee' => $empName,
            'date' => $date,
        ]);

        return redirect()->route('admin.attendance.index')->with('success', "Attendance record for {$empName} ({$date}) deleted.");
    }

    /**
     * Bulk Upload CSV / Excel
     */
    public function bulkUpload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt,xlsx,xls',
        ]);

        $import = new AttendanceImport(auth()->user()->name);

        try {
            Excel::import($import, $request->file('file'));

            AuditLog::log('BULK_UPLOAD', 'ATTENDANCE', null, [
                'filename' => $request->file('file')->getClientOriginalName(),
                'successful' => count($import->successfulRows),
                'duplicates' => count($import->duplicateRows),
                'invalidCodes' => count($import->invalidCodeRows),
                'invalidDates' => count($import->invalidDateRows),
                'errors' => count($import->errorRows),
            ]);

            return redirect()->route('admin.attendance.index')->with('bulk_result', [
                'successful' => $import->successfulRows,
                'duplicates' => $import->duplicateRows,
                'invalidCodes' => $import->invalidCodeRows,
                'invalidDates' => $import->invalidDateRows,
                'errors' => $import->errorRows,
            ])->with('success', "Bulk upload processed! " . count($import->successfulRows) . " records saved.");
        } catch (\Throwable $e) {
            return back()->withErrors(['file' => 'Failed to process file: ' . $e->getMessage()]);
        }
    }
}
