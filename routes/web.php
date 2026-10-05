<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmployeePortalController;
use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\AuditController;

/*
|--------------------------------------------------------------------------
| Web Routes - IntraEats & Talisha Software HR Attendance System
|--------------------------------------------------------------------------
*/

// ==========================================
// 1. Employee Portal (Mobile-First / Kiosk)
// ==========================================
Route::get('/', [EmployeePortalController::class, 'index'])->name('employee.portal');
Route::get('/attendance', function () {
    return redirect()->route('employee.portal');
});
Route::post('/employee/verify', [EmployeePortalController::class, 'verify'])->name('employee.verify');
Route::post('/employee/punch', [EmployeePortalController::class, 'punch'])->name('employee.punch');

// ==========================================
// 2. Admin Authentication Flow (2FA with OTP)
// ==========================================
Route::prefix('admin')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('admin.login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');

    Route::get('/verify-otp', [AdminAuthController::class, 'showOtp'])->name('admin.otp.show');
    Route::post('/verify-otp', [AdminAuthController::class, 'verifyOtp'])->name('admin.otp.verify');
    Route::post('/resend-otp', [AdminAuthController::class, 'resendOtp'])->name('admin.otp.resend');

    Route::get('/forgot-password', [AdminAuthController::class, 'showForgotPassword'])->name('admin.password.forgot');
    Route::post('/forgot-password', [AdminAuthController::class, 'sendResetOtp'])->name('admin.password.email');
    Route::get('/reset-password', [AdminAuthController::class, 'showResetPassword'])->name('admin.password.reset.show');
    Route::post('/reset-password', [AdminAuthController::class, 'resetPassword'])->name('admin.password.reset.submit');

    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');
});

// ==========================================
// 3. Admin Protected Management Suite
// ==========================================
Route::prefix('admin')->middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index']);
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    // Attendance Management
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('admin.attendance.index');
    Route::post('/attendance', [AttendanceController::class, 'store'])->name('admin.attendance.store');
    Route::put('/attendance/{id}', [AttendanceController::class, 'update'])->name('admin.attendance.update');
    Route::delete('/attendance/{id}', [AttendanceController::class, 'destroy'])->name('admin.attendance.destroy');
    Route::post('/attendance/bulk', [AttendanceController::class, 'bulkUpload'])->name('admin.attendance.bulk');

    // Employee Management
    Route::get('/employees', [EmployeeController::class, 'index'])->name('admin.employees.index');
    Route::post('/employees', [EmployeeController::class, 'store'])->name('admin.employees.store');
    Route::get('/employees/{id}', [EmployeeController::class, 'show'])->name('admin.employees.show');
    Route::put('/employees/{id}', [EmployeeController::class, 'update'])->name('admin.employees.update');
    Route::post('/employees/{id}/toggle-status', [EmployeeController::class, 'toggleStatus'])->name('admin.employees.toggle');
    Route::delete('/employees/{id}', [EmployeeController::class, 'destroy'])->name('admin.employees.destroy');

    // Project Management
    Route::get('/projects', [ProjectController::class, 'index'])->name('admin.projects.index');
    Route::post('/projects', [ProjectController::class, 'store'])->name('admin.projects.store');
    Route::put('/projects/{id}', [ProjectController::class, 'update'])->name('admin.projects.update');
    Route::post('/projects/{id}/assign', [ProjectController::class, 'assignEmployee'])->name('admin.projects.assign');
    Route::delete('/projects/{projectId}/employee/{employeeId}', [ProjectController::class, 'removeEmployee'])->name('admin.projects.removeEmployee');
    Route::delete('/projects/{id}', [ProjectController::class, 'destroy'])->name('admin.projects.destroy');

    // Reports Center
    Route::get('/reports', [ReportController::class, 'index'])->name('admin.reports.index');

    // Admin Users & RBAC
    Route::get('/users', [AdminUserController::class, 'index'])->name('admin.users.index');
    Route::post('/users', [AdminUserController::class, 'store'])->name('admin.users.store');
    Route::post('/users/{id}/toggle', [AdminUserController::class, 'toggleStatus'])->name('admin.users.toggle');
    Route::put('/users/{id}/role', [AdminUserController::class, 'updateRole'])->name('admin.users.role');
    Route::delete('/users/{id}', [AdminUserController::class, 'destroy'])->name('admin.users.destroy');

    // System Settings & Profile
    Route::get('/settings', [SettingController::class, 'index'])->name('admin.settings.index');
    Route::post('/settings', [SettingController::class, 'update'])->name('admin.settings.update');
    Route::post('/profile', [SettingController::class, 'updateProfile'])->name('admin.profile.update');
    Route::post('/password', [SettingController::class, 'updatePassword'])->name('admin.password.update');
    Route::post('/clear-demo-data', [SettingController::class, 'clearDemoData'])->name('admin.clear-demo');

    // Audit Logs
    Route::get('/audit', [AuditController::class, 'index'])->name('admin.audit.index');
});
