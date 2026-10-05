<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Attendance;
use App\Models\ProjectCategory;
use App\Models\Setting;
use App\Services\AttendanceService;
use App\Mail\AttendanceReceiptMail;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class EmployeeDashboardController extends Controller
{
    /**
     * Show the authenticated employee's dashboard, attendance station, and personal ledger.
     */
    public function index(Request $request)
    {
        /** @var \App\Models\Employee $employee */
        $employee = Auth::guard('employee')->user();

        $today = Carbon::today()->format('Y-m-d');
        $currentTime = Carbon::now()->format('d F Y | h:i:s A');
        $officeStart = Setting::get('office_start_time', '09:30 AM');
        $categories = ProjectCategory::orderBy('is_default', 'desc')->orderBy('name')->get();

        // 1. Today's attendance
        $todayAttendance = Attendance::where('employee_id', $employee->id)
            ->whereDate('date', $today)
            ->first();

        // 2. Personal Attendance Ledger Query (Employee's own records only)
        $query = Attendance::where('employee_id', $employee->id);

        if ($month = $request->input('month')) {
            $query->whereYear('date', substr($month, 0, 4))
                  ->whereMonth('date', substr($month, 5, 2));
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $attendances = $query->orderBy('date', 'desc')->paginate(15)->withQueryString();

        // 3. Employee Personal Metrics (All-time or This Month)
        $thisMonthStart = Carbon::now()->startOfMonth()->format('Y-m-d');
        $monthRecords = Attendance::where('employee_id', $employee->id)
            ->where('date', '>=', $thisMonthStart)
            ->get();

        $presentCount = $monthRecords->where('status', 'PRESENT')->count();
        $lateCount = $monthRecords->where('status', 'LATE')->count();
        $absentCount = $monthRecords->where('status', 'ABSENT')->count();
        $leaveCount = $monthRecords->where('status', 'LEAVE')->count();
        $totalDays = $monthRecords->count();

        $punctualityRate = $totalDays > 0 ? round(($presentCount / $totalDays) * 100) : 100;
        $totalMinutes = $monthRecords->sum('working_minutes');
        $totalHours = round($totalMinutes / 60, 1);

        $stats = [
            'presentCount' => $presentCount,
            'lateCount' => $lateCount,
            'absentCount' => $absentCount,
            'leaveCount' => $leaveCount,
            'totalDays' => $totalDays,
            'punctualityRate' => $punctualityRate,
            'totalMinutes' => $totalMinutes,
            'totalHours' => $totalHours,
        ];

        // 4. Assigned Projects
        $projects = $employee->projects()->get();

        return view('employee.dashboard', compact(
            'employee',
            'todayAttendance',
            'attendances',
            'categories',
            'currentTime',
            'officeStart',
            'stats',
            'projects'
        ));
    }

    /**
     * Mark Attendance (Punch In / Punch Out) for authenticated employee.
     */
    public function punch(Request $request)
    {
        $request->validate([
            'action' => 'required|in:checkin,checkout',
            'work_category' => 'nullable|string',
            'task_description' => 'nullable|string',
        ]);

        /** @var \App\Models\Employee $employee */
        $employee = Auth::guard('employee')->user();

        if (!$employee || $employee->status !== 'ACTIVE') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized or inactive employee account.',
            ], 403);
        }

        $today = Carbon::today()->format('Y-m-d');
        $currentTime = Carbon::now()->format('h:i A');

        $existing = Attendance::where('employee_id', $employee->id)
            ->whereDate('date', $today)
            ->first();

        // -------------------------------------------------------------
        // CHECK-OUT
        // -------------------------------------------------------------
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
                'message' => "Check-out successfully recorded at {$currentTime}! Total duration: {$existing->working_hours_formatted}.",
                'attendance' => $existing,
            ]);
        }

        // -------------------------------------------------------------
        // CHECK-IN (DUPLICATE PREVENTION)
        // -------------------------------------------------------------
        if ($existing) {
            return response()->json([
                'success' => false,
                'is_duplicate' => true,
                'message' => "Attendance Already Marked for Today — Check-in recorded at {$existing->check_in_time}.",
                'attendance' => $existing,
            ], 409);
        }

        $status = AttendanceService::determineStatus($currentTime);

        $attendance = Attendance::create([
            'employee_id' => $employee->id,
            'date' => $today,
            'check_in_time' => $currentTime,
            'check_out_time' => null,
            'working_minutes' => 0,
            'status' => $status,
            'work_category' => $request->input('work_category', $employee->assigned_project ?: 'Core Platform'),
            'task_description' => $request->input('task_description'),
            'is_demo' => $employee->is_demo,
        ]);

        // Send confirmation email if employee has email and notification enabled
        $notifEnabled = Setting::get('attendance_confirmation_email', 'true') === 'true';
        if ($employee->email && $notifEnabled) {
            try {
                Mail::to($employee->email)->queue(new AttendanceReceiptMail($attendance));
            } catch (\Throwable $e) {
                logger()->warning("Could not queue receipt email: " . $e->getMessage());
            }
        }

        return response()->json([
            'success' => true,
            'action' => 'checkin',
            'message' => "Punch In successful at {$currentTime}! Status: {$attendance->status}.",
            'attendance' => $attendance,
        ]);
    }

    /**
     * Update employee's own account password.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        /** @var \App\Models\Employee $employee */
        $employee = Auth::guard('employee')->user();

        if (!Hash::check($request->input('current_password'), $employee->password)) {
            return back()->withErrors(['current_password' => 'Current password does not match records.']);
        }

        $employee->password = Hash::make($request->input('password'));
        $employee->save();

        return back()->with('success', 'Your password has been successfully updated.');
    }
}
