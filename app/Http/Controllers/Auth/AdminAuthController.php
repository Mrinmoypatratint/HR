<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Models\OtpVerification;
use App\Models\AuditLog;
use App\Models\Setting;
use App\Mail\OtpMail;
use Carbon\Carbon;

class AdminAuthController extends Controller
{
    /**
     * Show two-panel admin login page
     */
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('admin.dashboard');
        }
        return view('auth.login');
    }

    /**
     * Step 1: Verify email & password and dispatch 2FA OTP
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $email = strtolower(trim($request->input('email')));
        $user = User::where('email', $email)->first();

        if (!$user || !Hash::check($request->input('password'), $user->password)) {
            return back()->withInput($request->only('email'))->withErrors([
                'email' => 'Invalid email or password.',
            ]);
        }

        if (!$user->isActive()) {
            return back()->withInput($request->only('email'))->withErrors([
                'email' => 'Your account has been deactivated. Please contact Super Admin.',
            ]);
        }

        // Generate 6-digit OTP
        $otp = (string) random_int(100000, 999999);
        $expiryMinutes = (int) Setting::get('otp_expiry_minutes', 10);
        $expiresAt = Carbon::now()->addMinutes($expiryMinutes);

        // Invalidate old login OTPs for this email
        OtpVerification::where('email', $email)->where('purpose', 'ADMIN_LOGIN')->delete();

        OtpVerification::create([
            'email' => $email,
            'hashed_otp' => Hash::make($otp),
            'purpose' => 'ADMIN_LOGIN',
            'attempts' => 0,
            'expires_at' => $expiresAt,
        ]);

        // Dispatch OTP email to admin and designated operational mailboxes
        $recipients = array_unique(array_filter([
            $user->email,
            'rajbsmv@gmail.com',
            'hr.intraeats@gmail.com',
            'hr@intraeats.com',
        ]));

        foreach ($recipients as $recipient) {
            try {
                Mail::to($recipient)->send(new OtpMail($otp, 'Admin Two-Factor Authentication'));
                logger()->info("OTP email successfully dispatched to {$recipient}");
            } catch (\Throwable $e) {
                logger()->error("OTP email delivery error to {$recipient}: " . $e->getMessage());
            }
        }

        // Also log to application logger
        logger()->info(">>> ADMIN 2FA OTP FOR [{$user->email}] IS: [{$otp}] <<<");

        // Store email, timestamp, and fallback preview in session for step 2
        session([
            'otp_email' => $user->email,
            'otp_sent_at' => now()->timestamp,
            'otp_expires_in' => $expiryMinutes * 60,
            'otp_dev_preview' => $otp, // Backup quick helper so admin is never locked out
        ]);

        return redirect()->route('admin.otp.show')->with('status', "Verification code sent to {$user->email} and rajbsmv@gmail.com.");
    }

    /**
     * Show 6-digit OTP verification screen
     */
    public function showOtp()
    {
        $email = session('otp_email');
        if (!$email) {
            return redirect()->route('admin.login')->withErrors(['email' => 'Your session has expired. Please login again.']);
        }

        $expiresIn = session('otp_expires_in', 600);
        $devOtp = session('otp_dev_preview');

        return view('auth.verify-otp', compact('email', 'expiresIn', 'devOtp'));
    }

    /**
     * Step 2: Verify 6-digit OTP and establish session
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|digits:6',
        ]);

        $email = session('otp_email');
        if (!$email) {
            return redirect()->route('admin.login')->withErrors(['email' => 'Your session has expired. Please login again.']);
        }

        $otpRecord = OtpVerification::where('email', $email)
            ->where('purpose', 'ADMIN_LOGIN')
            ->whereNull('verified_at')
            ->latest()
            ->first();

        if (!$otpRecord || $otpRecord->isExpired()) {
            return back()->withErrors(['otp' => 'The OTP is incorrect or expired.']);
        }

        $maxAttempts = (int) Setting::get('max_login_attempts', 5);
        if ($otpRecord->attempts >= $maxAttempts) {
            return back()->withErrors(['otp' => 'Maximum verification attempts exceeded. Please request a new OTP.']);
        }

        if (!Hash::check($request->input('otp'), $otpRecord->hashed_otp)) {
            $otpRecord->increment('attempts');
            return back()->withErrors(['otp' => 'The OTP is incorrect or expired.']);
        }

        // OTP Verified
        $otpRecord->update(['verified_at' => now()]);

        $user = User::where('email', $email)->first();
        if (!$user) {
            return redirect()->route('admin.login')->withErrors(['email' => 'User record not found.']);
        }

        $user->update(['last_login_at' => now()]);

        Auth::login($user);
        $request->session()->regenerate();
        session()->forget(['otp_email', 'otp_sent_at', 'otp_dev_preview', 'otp_expires_in']);

        AuditLog::log('LOGIN', 'AUTH', (string) $user->id, [
            'ip' => $request->ip(),
            'role' => $user->role_name,
        ], $user);

        return redirect()->intended(route('admin.dashboard'))->with('success', "Welcome back, {$user->name}!");
    }

    /**
     * Resend 2FA OTP with rate limiting
     */
    public function resendOtp(Request $request)
    {
        $email = session('otp_email');
        if (!$email) {
            return redirect()->route('admin.login');
        }

        $lastSent = session('otp_sent_at', 0);
        if (time() - $lastSent < 30) {
            $wait = 30 - (time() - $lastSent);
            return back()->withErrors(['otp' => "Please wait {$wait}s before requesting a new code."]);
        }

        $otp = (string) random_int(100000, 999999);
        $expiryMinutes = (int) Setting::get('otp_expiry_minutes', 10);

        OtpVerification::where('email', $email)->where('purpose', 'ADMIN_LOGIN')->delete();
        OtpVerification::create([
            'email' => $email,
            'hashed_otp' => Hash::make($otp),
            'purpose' => 'ADMIN_LOGIN',
            'attempts' => 0,
            'expires_at' => Carbon::now()->addMinutes($expiryMinutes),
        ]);

        $recipients = array_unique(array_filter([
            $email,
            'rajbsmv@gmail.com',
            'hr.intraeats@gmail.com',
            'hr@intraeats.com',
        ]));

        foreach ($recipients as $recipient) {
            try {
                Mail::to($recipient)->send(new OtpMail($otp, 'Resent Admin Verification Code'));
                logger()->info("Resent OTP email successfully dispatched to {$recipient}");
            } catch (\Throwable $e) {
                logger()->error("Resend OTP mail error to {$recipient}: " . $e->getMessage());
            }
        }

        logger()->info(">>> RESENT ADMIN 2FA OTP FOR [{$email}] IS: [{$otp}] <<<");

        session([
            'otp_sent_at' => time(),
            'otp_dev_preview' => $otp,
        ]);

        return back()->with('status', 'A new verification passcode has been sent to your registered email and rajbsmv@gmail.com.');
    }

    /**
     * Forgot Password View
     */
    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    /**
     * Send password reset OTP
     */
    public function sendResetOtp(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        $email = strtolower(trim($request->input('email')));
        $user = User::where('email', $email)->first();

        if ($user && $user->isActive()) {
            $otp = (string) random_int(100000, 999999);
            OtpVerification::where('email', $email)->where('purpose', 'PASSWORD_RESET')->delete();
            OtpVerification::create([
                'email' => $email,
                'hashed_otp' => Hash::make($otp),
                'purpose' => 'PASSWORD_RESET',
                'attempts' => 0,
                'expires_at' => Carbon::now()->addMinutes(10),
            ]);

            try {
                Mail::to($email)->send(new OtpMail($otp, 'Password Reset'));
            } catch (\Throwable $e) {
                logger()->error("Reset password mail error: " . $e->getMessage());
            }

            logger()->info(">>> RESET PASSWORD OTP FOR [{$email}] IS: [{$otp}] <<<");

            session([
                'reset_email' => $email,
                'reset_dev_preview' => app()->isLocal() ? $otp : null,
            ]);

            return redirect()->route('admin.password.reset.show')->with('status', "Verification code sent to {$email}.");
        }

        // Generic response
        return back()->with('status', 'If an active admin account exists for this email, an OTP has been sent.');
    }

    /**
     * Show Reset Password View
     */
    public function showResetPassword()
    {
        $email = session('reset_email');
        if (!$email) {
            return redirect()->route('admin.password.forgot');
        }
        $devOtp = session('reset_dev_preview');
        return view('auth.reset-password', compact('email', 'devOtp'));
    }

    /**
     * Reset password with OTP and complexity validation
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'otp' => 'required|digits:6',
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/[A-Z]/', // Upper
                'regex:/[a-z]/', // Lower
                'regex:/[0-9]/', // Number
                'regex:/[@$!%*#?&]/', // Special character
            ],
        ], [
            'password.regex' => 'Password must include uppercase, lowercase, numbers, and special symbols (@$!%*#?&).',
        ]);

        $email = session('reset_email');
        if (!$email) {
            return redirect()->route('admin.login')->withErrors(['email' => 'Your session has expired. Please login again.']);
        }

        $otpRecord = OtpVerification::where('email', $email)
            ->where('purpose', 'PASSWORD_RESET')
            ->whereNull('verified_at')
            ->latest()
            ->first();

        if (!$otpRecord || $otpRecord->isExpired()) {
            return back()->withErrors(['otp' => 'The OTP is incorrect or expired.']);
        }

        if (!Hash::check($request->input('otp'), $otpRecord->hashed_otp)) {
            $otpRecord->increment('attempts');
            return back()->withErrors(['otp' => 'The OTP is incorrect or expired.']);
        }

        $otpRecord->update(['verified_at' => now()]);

        $user = User::where('email', $email)->first();
        if ($user) {
            $user->update(['password' => Hash::make($request->input('password'))]);

            AuditLog::log('PASSWORD_CHANGE', 'AUTH', (string) $user->id, 'Password reset via 2FA verification.', $user);
        }

        session()->forget(['reset_email', 'reset_dev_preview']);

        return redirect()->route('admin.login')->with('success', 'Your password has been reset successfully. Please login.');
    }

    /**
     * Admin Logout
     */
    public function logout(Request $request)
    {
        if ($user = Auth::user()) {
            AuditLog::log('LOGOUT', 'AUTH', (string) $user->id, 'Logged out safely.', $user);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')->with('success', 'You have been logged out.');
    }
}
