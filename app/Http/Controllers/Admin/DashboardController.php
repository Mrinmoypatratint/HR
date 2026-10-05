<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\Project;
use App\Models\AuditLog;
use App\Models\Setting;
use App\Services\AttendanceService;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $today = Carbon::today()->format('Y-m-d');
        $selectedMonth = $request->input('month', Carbon::now()->format('Y-m'));

        // Row 1: Key Metric KPI Cards
        $totalEmployees = Employee::count();
        $activeEmployees = Employee::active()->count();
        $inactiveEmployees = $totalEmployees - $activeEmployees;

        $todayAttendances = Attendance::with('employee')->where('date', $today)->get();
        $presentToday = $todayAttendances->where('status', 'PRESENT')->count();
        $lateToday = $todayAttendances->where('status', 'LATE')->count();
        $leaveToday = $todayAttendances->where('status', 'LEAVE')->count();
        $punchedToday = $presentToday + $lateToday + $leaveToday;
        $absentToday = max(0, $activeEmployees - $punchedToday);

        $totalMinutesToday = $todayAttendances->sum('working_minutes');
        $attendanceRateToday = $activeEmployees > 0 ? round((($presentToday + $lateToday) / $activeEmployees) * 100) : 0;

        // Row 2: Weekly Trends (Past 7 Days)
        $weeklyLabels = [];
        $weeklyPresent = [];
        $weeklyLate = [];
        $weeklyAbsent = [];
        $weeklyLeave = [];

        for ($i = 6; $i >= 0; $i--) {
            $day = Carbon::today()->subDays($i);
            $dayStr = $day->format('Y-m-d');
            $weeklyLabels[] = $day->format('D d');

            $dayRecords = Attendance::where('date', $dayStr)->get();
            $p = $dayRecords->where('status', 'PRESENT')->count();
            $l = $dayRecords->where('status', 'LATE')->count();
            $lv = $dayRecords->where('status', 'LEAVE')->count();
            $ab = $dayRecords->where('status', 'ABSENT')->count();

            if (!$day->isSunday() && $i > 0 && $dayRecords->count() < $activeEmployees) {
                $ab += max(0, $activeEmployees - $dayRecords->count());
            }

            $weeklyPresent[] = $p;
            $weeklyLate[] = $l;
            $weeklyLeave[] = $lv;
            $weeklyAbsent[] = $ab;
        }

        // Row 2: Donut Distribution
        $allPresent = Attendance::where('status', 'PRESENT')->count();
        $allLate = Attendance::where('status', 'LATE')->count();
        $allLeave = Attendance::where('status', 'LEAVE')->count();
        $allAbsent = Attendance::where('status', 'ABSENT')->count();

        // Row 3: Recent Feeds
        $recentAttendance = Attendance::with('employee')
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->take(8)
            ->get();

        $recentEmployees = Employee::orderBy('created_at', 'desc')->take(5)->get();

        // Row 4: Projects & Hours
        $projects = Project::withCount('employees')->orderBy('updated_at', 'desc')->take(5)->get();
        $avgHoursToday = $punchedToday > 0 ? round(($totalMinutesToday / 60) / $punchedToday, 1) : 0;

        // Row 5: System Activity & Monthly Filter Stats
        $recentAudits = AuditLog::orderBy('created_at', 'desc')->take(6)->get();

        // Monthly statistics for the selected month
        $monthStart = Carbon::parse($selectedMonth . '-01')->startOfMonth();
        $monthEnd = $monthStart->copy()->endOfMonth();
        $monthRecords = Attendance::whereBetween('date', [$monthStart->format('Y-m-d'), $monthEnd->format('Y-m-d')])->get();
        $monthWorkingDays = 0;
        $tempDay = $monthStart->copy();
        while ($tempDay->lte($monthEnd)) {
            if (!$tempDay->isSunday()) $monthWorkingDays++;
            $tempDay->addDay();
        }

        $monthStats = [
            'month' => $monthStart->format('F Y'),
            'working_days' => $monthWorkingDays,
            'present' => $monthRecords->where('status', 'PRESENT')->count(),
            'late' => $monthRecords->where('status', 'LATE')->count(),
            'leave' => $monthRecords->where('status', 'LEAVE')->count(),
            'absent' => $monthRecords->where('status', 'ABSENT')->count(),
            'total_punches' => $monthRecords->count(),
        ];

        $hasDemoData = Employee::where('is_demo', true)->exists();

        return view('admin.dashboard', compact(
            'totalEmployees',
            'activeEmployees',
            'inactiveEmployees',
            'presentToday',
            'lateToday',
            'absentToday',
            'leaveToday',
            'attendanceRateToday',
            'totalMinutesToday',
            'weeklyLabels',
            'weeklyPresent',
            'weeklyLate',
            'weeklyAbsent',
            'weeklyLeave',
            'allPresent',
            'allLate',
            'allLeave',
            'allAbsent',
            'recentAttendance',
            'recentEmployees',
            'projects',
            'avgHoursToday',
            'recentAudits',
            'selectedMonth',
            'monthStats',
            'hasDemoData'
        ));
    }
}
