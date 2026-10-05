<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $isNewAccount ? 'Welcome to IntraEats — Activate Your Account' : 'Reset Your IntraEats Password' }}</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #FAFAF8; margin: 0; padding: 24px; color: #1C1C1E; }
        .wrapper { max-width: 560px; margin: 0 auto; background: #ffffff; border-radius: 16px; border: 1px solid #E2E8F0; overflow: hidden; box-shadow: 0 4px 14px rgba(0,0,0,0.06); }
        .header { background: #1C1C1E; padding: 32px 24px; text-align: center; color: #ffffff; }
        .logo-badge { display: inline-block; background: #FF6B1A; color: #ffffff; font-weight: 800; font-size: 14px; padding: 6px 14px; border-radius: 8px; margin-bottom: 10px; letter-spacing: 0.5px; }
        .title { margin: 0; font-size: 22px; font-weight: 800; color: #ffffff; letter-spacing: -0.3px; }
        .subtitle { margin: 6px 0 0 0; font-size: 13px; color: #A1A1AA; }
        .content { padding: 32px 28px; }
        .greeting { font-size: 18px; font-weight: 700; color: #1C1C1E; margin-bottom: 12px; }
        .text { font-size: 14px; line-height: 1.6; color: #52525B; margin: 0 0 16px 0; }
        .emp-card { background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 12px; padding: 18px 20px; margin: 20px 0; }
        .emp-card-title { font-size: 11px; font-weight: 800; text-transform: uppercase; color: #64748B; letter-spacing: 0.8px; margin-bottom: 12px; }
        .emp-grid { width: 100%; border-collapse: collapse; font-size: 13px; }
        .emp-grid td { padding: 6px 0; vertical-align: top; }
        .emp-grid td.label { width: 40%; color: #64748B; font-weight: 600; }
        .emp-grid td.val { width: 60%; color: #1C1C1E; font-weight: 700; }
        .btn-wrapper { text-align: center; margin: 28px 0; }
        .btn-primary { display: inline-block; background: #FF6B1A; color: #ffffff !important; font-weight: 800; font-size: 15px; padding: 14px 32px; border-radius: 12px; text-decoration: none; box-shadow: 0 4px 12px rgba(255, 107, 26, 0.35); }
        .steps { background: #FFF9F5; border: 1px solid #FFE4D6; border-radius: 12px; padding: 16px 20px; margin: 24px 0; }
        .steps h4 { margin: 0 0 10px 0; font-size: 13px; font-weight: 800; color: #C2410C; }
        .steps ol { margin: 0; padding-left: 20px; font-size: 12px; color: #9A3412; line-height: 1.6; }
        .url-fallback { font-size: 11px; color: #71717A; word-break: break-all; margin-top: 20px; line-height: 1.5; padding: 12px; background: #F4F4F5; border-radius: 8px; }
        .alert { font-size: 12px; color: #475569; margin-top: 20px; background: #F1F5F9; padding: 12px; border-radius: 8px; border-left: 4px solid #94A3B8; }
        .footer { background: #FAFAF8; padding: 20px; text-align: center; font-size: 11px; color: #71717A; border-top: 1px solid #EDEDEA; line-height: 1.6; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <div class="logo-badge">IntraEats &amp; Talisha Software</div>
            <h1 class="title">{{ $isNewAccount ? 'Welcome to the Team!' : 'Password Reset Request' }}</h1>
            <p class="subtitle">Official Workforce Attendance &amp; Operations Portal</p>
        </div>

        <div class="content">
            <div class="greeting">Hello {{ $employee->full_name }},</div>

            @if($isNewAccount)
                <p class="text">
                    You have been officially registered as a team member at <strong>IntraEats &amp; Talisha Software</strong>.
                    To start marking your daily attendance, logging work shifts, and viewing your personal workforce records, please set your account password below.
                </p>
            @else
                <p class="text">
                    We received a request to reset the password for your employee account at <strong>IntraEats &amp; Talisha Software</strong>.
                    Please click the button below to choose your new secure password.
                </p>
            @endif

            <div class="emp-card">
                <div class="emp-card-title">Employee Profile Details</div>
                <table class="emp-grid">
                    <tr>
                        <td class="label">Full Name:</td>
                        <td class="val">{{ $employee->full_name }}</td>
                    </tr>
                    <tr>
                        <td class="label">Employee Code:</td>
                        <td class="val" style="color: #FF6B1A; font-family: monospace;">{{ $employee->employee_code }}</td>
                    </tr>
                    <tr>
                        <td class="label">Official Email:</td>
                        <td class="val">{{ $employee->email }}</td>
                    </tr>
                    <tr>
                        <td class="label">Department:</td>
                        <td class="val">{{ $employee->department }}</td>
                    </tr>
                    <tr>
                        <td class="label">Designation / Role:</td>
                        <td class="val">{{ $employee->designation }} ({{ $employee->role }})</td>
                    </tr>
                </table>
            </div>

            <div class="btn-wrapper">
                <a href="{{ $setPasswordUrl }}" class="btn-primary" target="_blank">
                    {{ $isNewAccount ? 'Set Your Password & Activate' : 'Reset My Password' }} &rarr;
                </a>
            </div>

            <div class="steps">
                <h4>Next Steps After Setting Password:</h4>
                <ol>
                    <li>Log into your portal at <a href="{{ url('/employee/login') }}" style="color: #C2410C; font-weight: bold;">{{ url('/employee/login') }}</a> using your <strong>Employee Code</strong> or <strong>Email</strong>.</li>
                    <li>Clock in (Punch In) upon starting your shift with your designated project.</li>
                    <li>Clock out (Punch Out) at the end of the day to auto-calculate your logged hours.</li>
                    <li>View your personal attendance history, punctuality rates, and digital ID card.</li>
                </ol>
            </div>

            <div class="url-fallback">
                If the button above does not work, copy and paste this URL into your browser:<br>
                <a href="{{ $setPasswordUrl }}" style="color: #FF6B1A;">{{ $setPasswordUrl }}</a>
            </div>

            <div class="alert">
                🔒 <strong>Notice:</strong> This secure activation link expires in <strong>72 hours</strong>. If you did not expect this email, please contact HR immediately at <a href="mailto:hr@intraeats.com" style="color: #1C1C1E; font-weight: bold;">hr@intraeats.com</a>.
            </div>
        </div>

        <div class="footer">
            &copy; {{ date('Y') }} IntraEats &amp; Talisha Software. All rights reserved.<br>
            Level 4, Food-Tech Hub, Cyber Park, Bangalore, India
        </div>
    </div>
</body>
</html>
