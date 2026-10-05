<?php

namespace App\Imports;

use App\Models\Attendance;
use App\Models\Employee;
use App\Services\AttendanceService;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;

class AttendanceImport implements ToCollection, WithHeadingRow
{
    public array $successfulRows = [];
    public array $duplicateRows = [];
    public array $invalidCodeRows = [];
    public array $invalidDateRows = [];
    public array $errorRows = [];

    protected string $adminName;

    public function __construct(string $adminName = 'HR Admin')
    {
        $this->adminName = $adminName;
    }

    public function collection(Collection $rows)
    {
        $allEmployees = Employee::all();
        $byCode = $allEmployees->keyBy(fn($e) => strtoupper(trim($e->employee_code)));
        $byId = $allEmployees->keyBy(fn($e) => strtoupper(trim($e->employee_id)));

        foreach ($rows as $index => $row) {
            $rowNum = $index + 2; // header row is 1

            // Support various column naming styles
            $codeRaw = trim($row['employee_code'] ?? $row['code'] ?? $row['employee_id'] ?? $row['id'] ?? '');
            $dateRaw = trim($row['date'] ?? '');
            $inRaw = trim($row['check_in'] ?? $row['check_in_time'] ?? $row['checkin'] ?? '09:30 AM');
            $outRaw = trim($row['check_out'] ?? $row['check_out_time'] ?? $row['checkout'] ?? '');
            $statusRaw = strtoupper(trim($row['status'] ?? 'PRESENT'));
            $projectRaw = trim($row['project'] ?? $row['work_category'] ?? 'Internal Project');
            $remarksRaw = trim($row['remarks'] ?? '');

            // 1. Validate employee code / ID
            $employee = $byCode->get(strtoupper($codeRaw)) ?? $byId->get(strtoupper($codeRaw));
            if (!$employee) {
                $this->invalidCodeRows[] = [
                    'row' => $rowNum,
                    'code' => $codeRaw ?: 'Empty',
                    'reason' => "Employee Code '{$codeRaw}' was not found in the employee directory.",
                ];
                continue;
            }

            // 2. Validate date (YYYY-MM-DD)
            try {
                $parsedDate = Carbon::parse($dateRaw);
                $formattedDate = $parsedDate->format('Y-m-d');
            } catch (\Throwable $e) {
                $this->invalidDateRows[] = [
                    'row' => $rowNum,
                    'employee' => $employee->full_name,
                    'date' => $dateRaw ?: 'Empty',
                    'reason' => "Invalid date format. Expected YYYY-MM-DD.",
                ];
                continue;
            }

            // 3. Duplicate check
            $existing = Attendance::where('employee_id', $employee->id)
                ->where('date', $formattedDate)
                ->first();

            if ($existing) {
                $this->duplicateRows[] = [
                    'row' => $rowNum,
                    'employee' => $employee->full_name,
                    'date' => $formattedDate,
                    'reason' => "Attendance already exists for {$employee->full_name} on {$formattedDate}.",
                ];
                continue;
            }

            // 4. Calculate working minutes
            $workingMinutes = 0;
            if ($inRaw && $outRaw && $inRaw !== '-' && $outRaw !== '-') {
                $workingMinutes = AttendanceService::calculateMinutes($inRaw, $outRaw);
            }

            // 5. Valid status
            $validStatuses = ['PRESENT', 'LATE', 'ABSENT', 'LEAVE'];
            $finalStatus = in_array($statusRaw, $validStatuses) ? $statusRaw : 'PRESENT';

            try {
                $attendance = Attendance::create([
                    'employee_id' => $employee->id,
                    'date' => $formattedDate,
                    'check_in_time' => $inRaw,
                    'check_out_time' => $outRaw ?: null,
                    'working_minutes' => $workingMinutes,
                    'status' => $finalStatus,
                    'work_category' => $projectRaw ?: 'Internal Project',
                    'remarks' => $remarksRaw ?: 'Imported via Bulk Excel/CSV upload',
                    'modified_by' => "{$this->adminName} (Bulk Import)",
                    'modified_at' => now(),
                    'is_demo' => false,
                ]);

                $this->successfulRows[] = [
                    'row' => $rowNum,
                    'employee' => $employee->full_name,
                    'code' => $employee->employee_code,
                    'date' => $formattedDate,
                    'status' => $finalStatus,
                ];
            } catch (\Throwable $e) {
                $this->errorRows[] = [
                    'row' => $rowNum,
                    'employee' => $employee->full_name,
                    'reason' => $e->getMessage(),
                ];
            }
        }
    }
}
