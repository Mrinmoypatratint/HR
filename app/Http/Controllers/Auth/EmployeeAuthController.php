<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Mail\EmployeeSetPasswordMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;

class EmployeeAuthController extends Controller
{
    /**
     * Display the employee login form.
     */
    public function showLogin()
    {
        if (Auth::guard('employee')->check()) {
            return redirect()->route('employee.dashboard');
        }

        return view('employee.auth.login');
    }

    /**
     * Handle employee login request.
     */
    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        $loginInput = trim($request->input('login'));

        // Search by employee_code OR email
        $employee = Employee::where('employee_code', $loginInput)
            ->orWhere('email', $loginInput)
            ->first();

        if (!$employee) {
            return back()->withInput($request->only('login'))
                ->withErrors(['login' => 'No employee record found for that Employee Code or Email.']);
        }

        if ($employee->status !== 'ACTIVE') {
            return back()->withInput($request->only('login'))
                ->withErrors(['login' => "Your employee account is currently {$employee->status}. Please contact HR at hr@intraeats.com."]);
        }

        if (empty($employee->password)) {
            return back()->withInput($request->only('login'))
                ->withErrors(['login' => 'Your password has not been activated yet. Please click the setup link sent to your email or request a reset link below.']);
        }

        if (!Hash::check($request->input('password'), $employee->password)) {
            return back()->withInput($request->only('login'))
                ->withErrors(['password' => 'Incorrect password provided. Please verify and try again.']);
        }

        // Authenticate via employee session guard
        Auth::guard('employee')->login($employee, $request->boolean('remember'));

        $employee->update([
            'last_login_at' => Carbon::now(),
        ]);

        $request->session()->regenerate();

        return redirect()->intended(route('employee.dashboard'))
            ->with('success', "Welcome back, {$employee->full_name}!");
    }

    /**
     * Display the Set / Reset Password view with token.
     */
    public function showSetPassword(Request $request)
    {
        $token = $request->query('token');
        $email = $request->query('email');

        if (!$token || !$email) {
            return redirect()->route('employee.password.forgot')
                ->with('error', 'Invalid password setup link. Please request a new activation link.');
        }

        $employee = Employee::where('email', $email)
            ->where('password_reset_token', $token)
            ->first();

        if (!$employee) {
            return redirect()->route('employee.password.forgot')
                ->with('error', 'This password setup link is invalid or has already been used. Please request a new one.');
        }

        // Token validity: 72 hours
        if ($employee->password_reset_sent_at && Carbon::parse($employee->password_reset_sent_at)->addHours(72)->isPast()) {
            return redirect()->route('employee.password.forgot')
                ->with('error', 'This password activation link has expired (72h limit). Please request a new link.');
        }

        return view('employee.auth.set-password', [
            'token' => $token,
            'email' => $email,
            'employee' => $employee,
            'isNew' => empty($employee->password),
        ]);
    }

    /**
     * Save the new employee password and automatically sign them in.
     */
    public function setPassword(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $employee = Employee::where('email', $request->input('email'))
            ->where('password_reset_token', $request->input('token'))
            ->first();

        if (!$employee) {
            return redirect()->route('employee.password.forgot')
                ->with('error', 'Invalid or expired setup token. Please request a new link.');
        }

        // Update password and clear reset token
        $employee->password = Hash::make($request->input('password'));
        $employee->password_reset_token = null;
        $employee->password_reset_sent_at = null;
        $employee->last_login_at = Carbon::now();
        $employee->save();

        // Automatically log employee in
        Auth::guard('employee')->login($employee);
        $request->session()->regenerate();

        return redirect()->route('employee.dashboard')
            ->with('success', "Your account password has been successfully configured! Welcome to IntraEats, {$employee->full_name}.");
    }

    /**
     * Show Forgot Password request form.
     */
    public function showForgotPassword()
    {
        return view('employee.auth.forgot-password');
    }

    /**
     * Dispatch a reset password email.
     */
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'login' => 'required|string',
        ]);

        $login = trim($request->input('login'));
        $employee = Employee::where('email', $login)
            ->orWhere('employee_code', $login)
            ->first();

        if ($employee && $employee->email) {
            $token = Str::random(64);
            $employee->update([
                'password_reset_token' => $token,
                'password_reset_sent_at' => Carbon::now(),
            ]);

            $url = url('/employee/set-password?token=' . $token . '&email=' . urlencode($employee->email));

            try {
                Mail::to($employee->email)->send(new EmployeeSetPasswordMail($employee, $token, false));
                logger()->info("EMPLOYEE RESET LINK DISPATCHED to {$employee->email}: " . $url);
            } catch (\Throwable $e) {
                logger()->warning("Could not send password reset mail: " . $e->getMessage());
            }

            session()->flash('dev_reset_url', $url);
        }

        // Always show positive message to prevent user enumeration
        return back()->with('status', 'If your account is registered with an email address, password reset instructions have been dispatched.');
    }

    /**
     * Log employee out of session.
     */
    public function logout(Request $request)
    {
        Auth::guard('employee')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('employee.portal')
            ->with('info', 'You have been safely signed out.');
    }
}
