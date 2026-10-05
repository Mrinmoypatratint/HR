<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #FAFAF8; margin: 0; padding: 24px; color: #1C1C1E; }
        .wrapper { max-width: 520px; margin: 0 auto; background: #ffffff; border-radius: 16px; border: 1px solid #E2E8F0; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .header { background: #16A34A; padding: 24px; text-align: center; color: #ffffff; }
        .title { margin: 0; font-size: 20px; font-weight: 700; }
        .subtitle { margin: 4px 0 0 0; font-size: 13px; color: #D1FAE5; }
        .content { padding: 24px; }
        .card { background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 12px; padding: 16px; margin: 16px 0; }
        .row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #EDEDEA; font-size: 13px; }
        .row:last-child { border-bottom: none; }
        .label { color: #64748B; font-weight: 500; }
        .val { font-weight: 600; color: #0F172A; }
        .badge { background: #ECFDF5; color: #059669; padding: 2px 8px; border-radius: 9999px; font-size: 11px; font-weight: 700; }
        .footer { background: #FAFAF8; padding: 16px; text-align: center; font-size: 11px; color: #888888; border-top: 1px solid #EDEDEA; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <h1 class="title">Punch Receipt Recorded ✓</h1>
            <p class="subtitle">IntraEats Official Attendance Log</p>
        </div>
        <div class="content">
            <p style="font-size: 14px; margin-top: 0;">Hi <strong>{{ $attendance->employee->full_name }}</strong>,</p>
            <p style="font-size: 13px; color: #475569;">Your attendance for today has been logged successfully on the IntraEats portal.</p>
            
            <div class="card">
                <div class="row"><span class="label">Employee Code</span><span class="val">{{ $attendance->employee->employee_code }}</span></div>
                <div class="row"><span class="label">Punch Date</span><span class="val">{{ $attendance->date->format('d M Y') }}</span></div>
                <div class="row"><span class="label">Check-in Time</span><span class="val">{{ $attendance->check_in_time }}</span></div>
                @if($attendance->check_out_time)
                <div class="row"><span class="label">Check-out Time</span><span class="val">{{ $attendance->check_out_time }}</span></div>
                <div class="row"><span class="label">Working Duration</span><span class="val">{{ $attendance->working_hours_formatted }}</span></div>
                @endif
                <div class="row"><span class="label">Status</span><span class="badge">{{ $attendance->status }}</span></div>
                <div class="row"><span class="label">Work Category</span><span class="val">{{ $attendance->work_category }}</span></div>
                @if($attendance->task_description)
                <div class="row"><span class="label">Task Log</span><span class="val">{{ $attendance->task_description }}</span></div>
                @endif
            </div>
            
            <p style="font-size: 12px; color: #64748B;">Please ensure you punch out before concluding your work day.</p>
        </div>
        <div class="footer">
            &copy; IntraEats &amp; Talisha Software &bull; HR &amp; Attendance Management System
        </div>
    </div>
</body>
</html>
