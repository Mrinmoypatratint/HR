<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #FAFAF8; margin: 0; padding: 24px; color: #1C1C1E; }
        .wrapper { max-width: 520px; margin: 0 auto; background: #ffffff; border-radius: 16px; border: 1px solid #E2E8F0; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .header { background: #1C1C1E; padding: 28px 24px; text-align: center; color: #ffffff; }
        .logo-badge { display: inline-block; background: #FF6B1A; color: #ffffff; font-weight: 800; font-size: 15px; padding: 6px 14px; border-radius: 8px; margin-bottom: 8px; letter-spacing: 0.5px; }
        .title { margin: 0; font-size: 20px; font-weight: 700; color: #ffffff; }
        .subtitle { margin: 4px 0 0 0; font-size: 13px; color: #A1A1AA; }
        .content { padding: 32px 24px; text-align: center; }
        .otp-box { display: inline-block; background: #FFF3EB; border: 2px dashed #FF6B1A; border-radius: 12px; padding: 16px 36px; font-size: 34px; font-weight: 800; letter-spacing: 8px; color: #FF6B1A; font-family: monospace; margin: 24px 0; }
        .text { font-size: 14px; line-height: 1.6; color: #52525B; text-align: left; margin: 0 0 12px 0; }
        .alert { font-size: 12px; color: #DC2626; margin-top: 20px; background: #FEF2F2; padding: 12px; border-radius: 8px; border-left: 4px solid #DC2626; text-align: left; }
        .footer { background: #FAFAF8; padding: 16px; text-align: center; font-size: 11px; color: #71717A; border-top: 1px solid #EDEDEA; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <div class="logo-badge">IntraEats &amp; Talisha</div>
            <h1 class="title">Security Verification Code</h1>
            <p class="subtitle">HR &amp; Attendance Management System</p>
        </div>
        <div class="content">
            <p class="text">Hello Administrator,</p>
            <p class="text">A request was made for <strong>{{ $purpose }}</strong>. Please use the following 6-digit one-time passcode to complete verification:</p>
            
            <div class="otp-box">{{ $otp }}</div>
            
            <p class="text">This passcode is strictly valid for <strong>10 minutes</strong> and can only be used once.</p>
            
            <div class="alert">
                🔒 <strong>Security Warning:</strong> Never disclose this code. IntraEats or Talisha Software IT will never ask for your verification code.
            </div>
        </div>
        <div class="footer">
            &copy; IntraEats &amp; Talisha Software &bull; Confidential Workforce System
        </div>
    </div>
</body>
</html>
