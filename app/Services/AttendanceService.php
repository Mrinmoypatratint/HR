<?php

namespace App\Services;

use App\Models\Setting;
use Carbon\Carbon;

class AttendanceService
{
    /**
     * Calculate difference in minutes between two time strings like "09:32 AM" and "06:18 PM"
     */
    public static function calculateMinutes(string $checkInStr, string $checkOutStr): int
    {
        try {
            $in = Carbon::createFromFormat('h:i A', trim($checkInStr));
            $out = Carbon::createFromFormat('h:i A', trim($checkOutStr));

            if ($out->greaterThanOrEqualTo($in)) {
                return $in->diffInMinutes($out);
            }
            // Overnight shift
            return (1440 - $in->diffInMinutes(Carbon::createFromFormat('h:i A', '12:00 AM'))) + $out->diffInMinutes(Carbon::createFromFormat('h:i A', '12:00 AM'));
        } catch (\Throwable $e) {
            return 0;
        }
    }

    /**
     * Format minutes into "08h 46m"
     */
    public static function formatHours(int $totalMinutes): string
    {
        if ($totalMinutes <= 0) return '00h 00m';
        $hours = floor($totalMinutes / 60);
        $mins = $totalMinutes % 60;
        return sprintf('%02dh %02dm', $hours, $mins);
    }

    /**
     * Determine status (PRESENT or LATE) based on office settings
     */
    public static function determineStatus(string $checkInTimeStr): string
    {
        try {
            $officeStartStr = Setting::get('office_start_time', '09:30 AM');
            $graceMinutes = (int) Setting::get('late_grace_minutes', 15);

            $in = Carbon::createFromFormat('h:i A', trim($checkInTimeStr));
            $threshold = Carbon::createFromFormat('h:i A', trim($officeStartStr))->addMinutes($graceMinutes);

            if ($in->greaterThan($threshold)) {
                return 'LATE';
            }
            return 'PRESENT';
        } catch (\Throwable $e) {
            return 'PRESENT';
        }
    }
}
