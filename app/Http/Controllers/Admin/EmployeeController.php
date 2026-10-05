<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Project;
use App\Models\AuditLog;
use Carbon\Carbon;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $query = Employee::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('employee_code', 'like', "%{$search}%")
                  ->orWhere('employee_id', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('role', 'like', "%{$search}%")
                  ->orWhere('department', 'like', "%{$search}%");
            });
        }

        if ($dept = $request->input('department')) {
            $query->where('department', $dept);
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($empType = $request->input('employment_type')) {
            $query->where('employment_type', $empType);
        }

        $employees = $query->withCount('attendances')->orderBy('created_at', 'desc')->paginate(12)->withQueryString();

        $departments = Employee::distinct()->pluck('department')->filter();
        $projects = Project::where('status', 'ACTIVE')->orderBy('name')->get();

        return view('admin.employees.index', compact('employees', 'departments', 'projects'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|string|unique:employees,employee_id',
            'employee_code' => 'required|string|unique:employees,employee_code',
            'full_name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:employees,email',
            'mobile' => 'nullable|string',
            'department' => 'required|string',
            'designation' => 'required|string',
            'role' => 'required|string',
            'employment_type' => 'required|string',
            'joining_date' => 'required|date',
            'assigned_project' => 'nullable|string',
            'status' => 'required|in:ACTIVE,INACTIVE,ON_NOTICE',
            'address' => 'nullable|string',
            'emergency_contact' => 'nullable|string',
            'notes' => 'nullable|string',
            'photo_url' => 'nullable|string',
        ]);

        $employee = Employee::create([
            'employee_id' => trim($request->input('employee_id')),
            'employee_code' => trim($request->input('employee_code')),
            'full_name' => trim($request->input('full_name')),
            'email' => $request->input('email') ? trim($request->input('email')) : null,
            'mobile' => $request->input('mobile'),
            'department' => $request->input('department'),
            'designation' => $request->input('designation'),
            'role' => $request->input('role'),
            'employment_type' => $request->input('employment_type', 'Full-time'),
            'joining_date' => Carbon::parse($request->input('joining_date'))->format('Y-m-d'),
            'assigned_project' => $request->input('assigned_project'),
            'status' => $request->input('status', 'ACTIVE'),
            'address' => $request->input('address'),
            'emergency_contact' => $request->input('emergency_contact'),
            'notes' => $request->input('notes'),
            'photo_url' => $request->input('photo_url') ?: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=256&q=80',
            'is_demo' => false,
        ]);

        AuditLog::log('CREATE', 'EMPLOYEE', (string) $employee->id, [
            'name' => $employee->full_name,
            'code' => $employee->employee_code,
            'dept' => $employee->department,
        ]);

        return redirect()->route('admin.employees.index')->with('success', "Employee {$employee->full_name} ({$employee->employee_code}) created successfully!");
    }

    public function show($id)
    {
        $employee = Employee::with(['attendances' => function ($q) {
            $q->orderBy('date', 'desc')->take(60);
        }, 'projects'])->findOrFail($id);

        $presentCount = $employee->attendances->where('status', 'PRESENT')->count();
        $lateCount = $employee->attendances->where('status', 'LATE')->count();
        $absentCount = $employee->attendances->where('status', 'ABSENT')->count();
        $leaveCount = $employee->attendances->where('status', 'LEAVE')->count();
        $totalDays = $employee->attendances->count();

        $rate = $totalDays > 0 ? round((($presentCount + $lateCount) / $totalDays) * 100) : 0;
        $totalMinutes = $employee->attendances->sum('working_minutes');
        $avgHours = ($presentCount + $lateCount) > 0 ? round(($totalMinutes / 60) / ($presentCount + $lateCount), 1) : 0;

        $stats = [
            'totalDays' => $totalDays,
            'presentCount' => $presentCount,
            'lateCount' => $lateCount,
            'absentCount' => $absentCount,
            'leaveCount' => $leaveCount,
            'rate' => $rate,
            'totalMinutes' => $totalMinutes,
            'avgHours' => $avgHours,
        ];

        $projects = Project::where('status', 'ACTIVE')->orderBy('name')->get();

        return view('admin.employees.show', compact('employee', 'stats', 'projects'));
    }

    public function update(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);

        $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:employees,email,' . $id,
            'mobile' => 'nullable|string',
            'department' => 'required|string',
            'designation' => 'required|string',
            'role' => 'required|string',
            'employment_type' => 'required|string',
            'joining_date' => 'required|date',
            'assigned_project' => 'nullable|string',
            'status' => 'required|in:ACTIVE,INACTIVE,ON_NOTICE',
            'address' => 'nullable|string',
            'emergency_contact' => 'nullable|string',
            'notes' => 'nullable|string',
            'photo_url' => 'nullable|string',
        ]);

        $employee->update([
            'full_name' => trim($request->input('full_name')),
            'email' => $request->input('email') ? trim($request->input('email')) : null,
            'mobile' => $request->input('mobile'),
            'department' => $request->input('department'),
            'designation' => $request->input('designation'),
            'role' => $request->input('role'),
            'employment_type' => $request->input('employment_type'),
            'joining_date' => Carbon::parse($request->input('joining_date'))->format('Y-m-d'),
            'assigned_project' => $request->input('assigned_project'),
            'status' => $request->input('status'),
            'address' => $request->input('address'),
            'emergency_contact' => $request->input('emergency_contact'),
            'notes' => $request->input('notes'),
            'photo_url' => $request->input('photo_url') ?: $employee->photo_url,
        ]);

        AuditLog::log('UPDATE', 'EMPLOYEE', (string) $employee->id, [
            'name' => $employee->full_name,
            'status' => $employee->status,
        ]);

        return redirect()->back()->with('success', "Employee {$employee->full_name} updated successfully.");
    }

    public function toggleStatus($id)
    {
        $employee = Employee::findOrFail($id);
        $newStatus = $employee->status === 'ACTIVE' ? 'INACTIVE' : 'ACTIVE';
        $employee->update(['status' => $newStatus]);

        AuditLog::log('STATUS_TOGGLE', 'EMPLOYEE', (string) $employee->id, [
            'name' => $employee->full_name,
            'new_status' => $newStatus,
        ]);

        return redirect()->back()->with('success', "Employee status changed to {$newStatus}.");
    }

    public function destroy($id)
    {
        $employee = Employee::findOrFail($id);
        $name = $employee->full_name;
        $code = $employee->employee_code;

        $employee->delete();

        AuditLog::log('DELETE', 'EMPLOYEE', (string) $id, [
            'name' => $name,
            'code' => $code,
        ]);

        return redirect()->route('admin.employees.index')->with('success', "Employee {$name} ({$code}) deleted.");
    }
}
