<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\User;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\Project;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Hash;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->pluck('value', 'key');
        $user = auth()->user();

        $demoStats = [
            'employees' => Employee::where('is_demo', true)->count(),
            'attendances' => Attendance::where('is_demo', true)->count(),
            'projects' => Project::where('is_demo', true)->count(),
        ];
        $hasDemoData = ($demoStats['employees'] + $demoStats['attendances'] + $demoStats['projects']) > 0;

        return view('admin.settings.index', compact('settings', 'user', 'demoStats', 'hasDemoData'));
    }

    public function update(Request $request)
    {
        $category = $request->input('category', 'GENERAL');
        $inputs = $request->except(['_token', 'category']);

        foreach ($inputs as $key => $value) {
            Setting::set($key, is_array($value) ? json_encode($value) : (string) $value, $category);
        }

        AuditLog::log('UPDATE_SETTINGS', 'SETTINGS', null, ['category' => $category]);

        return redirect()->back()->with('success', "Settings updated successfully.");
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'mobile' => 'nullable|string',
            'avatar_url' => 'nullable|string',
        ]);

        $user->update([
            'name' => trim($request->input('name')),
            'mobile' => $request->input('mobile'),
            'avatar_url' => $request->input('avatar_url') ?: $user->avatar_url,
        ]);

        AuditLog::log('PROFILE_UPDATE', 'SETTINGS', (string) $user->id, 'Admin updated personal profile details.');

        return redirect()->back()->with('success', "Profile details updated.");
    }

    public function updatePassword(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'current_password' => 'required',
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/[A-Z]/',
                'regex:/[a-z]/',
                'regex:/[0-9]/',
                'regex:/[@$!%*#?&]/',
            ],
        ], [
            'password.regex' => 'Password must include uppercase, lowercase, numbers, and special symbols (@$!%*#?&).',
        ]);

        if (!Hash::check($request->input('current_password'), $user->password)) {
            return back()->withErrors(['current_password' => 'Your current password is incorrect.']);
        }

        $user->update([
            'password' => Hash::make($request->input('password')),
        ]);

        AuditLog::log('PASSWORD_CHANGE', 'SETTINGS', (string) $user->id, 'Admin changed account password.');

        return redirect()->back()->with('success', "Password changed successfully.");
    }

    /**
     * Clear all demo data so system is completely ready for real company data
     */
    public function clearDemoData()
    {
        $empCount = Employee::where('is_demo', true)->count();
        $attCount = Attendance::where('is_demo', true)->count();
        $prjCount = Project::where('is_demo', true)->count();

        Attendance::where('is_demo', true)->delete();
        Employee::where('is_demo', true)->delete();
        Project::where('is_demo', true)->delete();

        AuditLog::log('CLEAR_DEMO', 'SYSTEM', null, [
            'cleared_employees' => $empCount,
            'cleared_attendances' => $attCount,
            'cleared_projects' => $prjCount,
        ]);

        return redirect()->back()->with('success', "All DEMO DATA cleared cleanly ({$empCount} employees, {$attCount} attendance logs, {$prjCount} projects). The system is now ready for production!");
    }
}
