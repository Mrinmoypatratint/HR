<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\ProjectCategory;
use App\Models\Setting;
use App\Services\AttendanceService;
use App\Mail\AttendanceReceiptMail;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class EmployeePortalController extends Controller
{
    /**
     * Show the Mobile-First Employee Attendance Portal
     */
    public function index()
    {
        if (\Illuminate\Support\Facades\Auth::guard('employee')->check()) {
            return redirect()->route('employee.dashboard');
        }

        $categories = ProjectCategory::orderBy('is_default', 'desc')->orderBy('name')->get();
        $currentTime = Carbon::now()->format('d F Y | h:i:s A');
        $officeStart = Setting::get('office_start_time', '09:30 AM');

        return view('employee.portal', compact('categories', 'currentTime', 'officeStart'));
    }

    /**
     * Verify Employee Code (e.g. INTRA-EMP-001)
     */
    public function verify(Request $request)
    {
        $request->validate([
            'employee_code' => 'required|string',
        ]);

        $code = trim($request->input('employee_code'));
        $employee = Employee::where('employee_code', $code)->first();

        if (!$employee) {
            return response()->json([
                'valid' => false,
                'message' => 'Employee Code not found. Please check your code and try again.',
            ], 404);
        }

        if ($employee->status !== 'ACTIVE') {
            return response()->json([
                'valid' => false,
                'message' => "Employee account is currently {$employee->status}. Please contact HR.",
            ], 403);
        }

        $today = Carbon::today()->format('Y-m-d');
        $todayAttendance = Attendance::where('employee_id', $employee->id)
            ->whereDate('date', $today)
            ->first();

        return response()->json([
            'valid' => true,
            'employee' => [
                'id' => $employee->id,
                'employee_id' => $employee->employee_id,
                'employee_code' => $employee->employee_code,
                'full_name' => $employee->full_name,
                'initials' => $employee->initials,
                'department' => $employee->department,
                'designation' => $employee->designation,
                'role' => $employee->role,
                'joining_date' => $employee->joining_date->format('d M Y'),
                'employment_type' => $employee->employment_type,
                'status' => $employee->status,
                'assigned_project' => $employee->assigned_project ?? 'General Operations',
                'photo_url' => $employee->photo_url,
                'email' => $employee->email,
            ],
            'todayAttendance' => $todayAttendance ? [
                'id' => $todayAttendance->id,
                'date' => $todayAttendance->date->format('Y-m-d'),
                'check_in_time' => $todayAttendance->check_in_time,
                'check_out_time' => $todayAttendance->check_out_time,
                'working_minutes' => $todayAttendance->working_minutes,
                'working_hours_formatted' => $todayAttendance->working_hours_formatted,
                'status' => $todayAttendance->status,
                'work_category' => $todayAttendance->work_category,
                'task_description' => $todayAttendance->task_description,
            ] : null,
            'server_time' => Carbon::now()->format('h:i:s A'),
            'server_date' => Carbon::now()->format('d F Y'),
        ]);
    }

    /**
     * Mark Attendance (Check-in or Check-out)
     */
    public function punch(Request $request)
    {
        $authEmployee = \Illuminate\Support\Facades\Auth::guard('employee')->user();

        if ($authEmployee) {
            $employee = $authEmployee;
            $request->validate([
                'action' => 'required|in:checkin,checkout',
                'work_category' => 'nullable|string',
                'task_description' => 'nullable|string',
            ]);
        } else {
            $request->validate([
                'employee_code' => 'required|string',
                'action' => 'required|in:checkin,checkout',
                'work_category' => 'nullable|string',
                'task_description' => 'nullable|string',
            ]);

            $code = trim($request->input('employee_code'));
            $employee = Employee::where('employee_code', $code)->first();

            if (!$employee) {
                return response()->json([
                    'success' => false,
                    'message' => 'Employee Code not found.',
                ], 404);
            }
        }

        $today = Carbon::today()->format('Y-m-d');
        $currentTime = Carbon::now()->format('h:i A');

        $existing = Attendance::where('employee_id', $employee->id)
            ->whereDate('date', $today)
            ->first();

        // -----------------------------------------------------------------
        // CHECK-OUT ACTION
        // -----------------------------------------------------------------
        if ($request->input('action') === 'checkout') {
            if (!$existing) {
                return response()->json([
                    'success' => false,
                    'message' => 'No check-in record found for today. Please punch in first.',
                ], 400);
            }

            if ($existing->check_out_time) {
                return response()->json([
                    'success' => false,
                    'message' => "Already checked out today at {$existing->check_out_time}.",
                    'attendance' => $existing,
                ], 400);
            }

            $workingMinutes = AttendanceService::calculateMinutes($existing->check_in_time, $currentTime);

            $existing->update([
                'check_out_time' => $currentTime,
                'working_minutes' => $workingMinutes,
            ]);

            return response()->json([
                'success' => true,
                'action' => 'checkout',
                'message' => "Check-out successfully recorded at {$currentTime}! Working duration: {$existing->working_hours_formatted}.",
                'attendance' => [
                    'id' => $existing->id,
                    'date' => $existing->date->format('d M Y'),
                    'check_in_time' => $existing->check_in_time,
                    'check_out_time' => $existing->check_out_time,
                    'working_hours_formatted' => $existing->working_hours_formatted,
                    'status' => $existing->status,
                    'work_category' => $existing->work_category,
                    'employee_name' => $employee->full_name,
                    'employee_code' => $employee->employee_code,
                ],
            ]);
        }

        // -----------------------------------------------------------------
        // CHECK-IN ACTION (DUPLICATE PROTECTION)
        // -----------------------------------------------------------------
        if ($existing) {
            return response()->json([
                'success' => false,
                'is_duplicate' => true,
                'message' => "Attendance Already Marked for Today — Check-in recorded at {$existing->check_in_time} for Project [{$existing->work_category}].",
                'attendance' => [
                    'id' => $existing->id,
                    'date' => $existing->date->format('d M Y'),
                    'check_in_time' => $existing->check_in_time,
                    'check_out_time' => $existing->check_out_time,
                    'working_hours_formatted' => $existing->working_hours_formatted,
                    'status' => $existing->status,
                    'work_category' => $existing->work_category,
                    'employee_name' => $employee->full_name,
                    'employee_code' => $employee->employee_code,
                ],
            ], 409);
        }

        // Determine status (PRESENT or LATE)
        $status = AttendanceService::determineStatus($currentTime);

        $attendance = Attendance::create([
            'employee_id' => $employee->id,
            'date' => $today,
            'check_in_time' => $currentTime,
            'check_out_time' => null,
            'working_minutes' => 0,
            'status' => $status,
            'work_category' => $request->input('work_category', 'Internal Project'),
            'task_description' => $request->input('task_description'),
            'is_demo' => $employee->is_demo,
        ]);

        // Send confirmation email if employee has email and notification enabled
        $notifEnabled = Setting::get('attendance_confirmation_email', 'true') === 'true';
        if ($employee->email && $notifEnabled) {
            try {
                Mail::to($employee->email)->queue(new AttendanceReceiptMail($attendance));
            } catch (\Throwable $e) {
                // Keep robust, log without breaking UI
                logger()->warning("Could not queue receipt email: " . $e->getMessage());
            }
        }

        return response()->json([
            'success' => true,
            'action' => 'checkin',
            'message' => "Attendance marked successfully! Check-in captured at {$currentTime}.",
            'attendance' => [
                'id' => $attendance->id,
                'date' => Carbon::parse($today)->format('d M Y'),
                'check_in_time' => $attendance->check_in_time,
                'check_out_time' => null,
                'working_hours_formatted' => '00h 00m',
                'status' => $attendance->status,
                'work_category' => $attendance->work_category,
                'task_description' => $attendance->task_description,
                'employee_name' => $employee->full_name,
                'employee_code' => $employee->employee_code,
                'email' => $employee->email,
            ],
        ]);
    }
}
