<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\OtpVerification;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class SystemSmokeTest extends TestCase
{
    /**
     * Test public employee portal renders cleanly.
     */
    public function test_employee_portal_loads_successfully(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('IntraEats');
        $response->assertSee('Employee Attendance Portal');
    }

    /**
     * Test employee code verification endpoint.
     */
    public function test_employee_verification_endpoint(): void
    {
        $employee = Employee::first();
        $this->assertNotNull($employee, 'Seeded employee must exist.');

        $response = $this->postJson('/employee/verify', [
            'employee_code' => $employee->employee_code,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'valid' => true,
            'employee' => [
                'employee_code' => $employee->employee_code,
                'full_name' => $employee->full_name,
            ],
        ]);
    }

    /**
     * Test admin login page renders.
     */
    public function test_admin_login_page_loads(): void
    {
        $response = $this->get('/admin/login');
        $response->assertStatus(200);
        $response->assertSee('Sign in to Admin Hub');
    }

    /**
     * Test 2-step admin login authentication flow.
     */
    public function test_admin_two_factor_auth_flow(): void
    {
        $user = User::where('email', 'hr@intraeats.com')->first();
        $this->assertNotNull($user, 'Super admin user must exist.');

        // Step 1: Submit email & password
        $response = $this->post('/admin/login', [
            'email' => 'hr@intraeats.com',
            'password' => 'Admin@IntraEats2026!',
        ]);

        $response->assertRedirect('/admin/verify-otp');
        $this->assertEquals($user->email, session('otp_email'));

        // Retrieve generated OTP from session (dev preview)
        $otp = session('otp_dev_preview');
        $this->assertNotNull($otp, 'OTP preview must have been generated.');

        // Step 2: Submit valid OTP
        $response = $this->post('/admin/verify-otp', [
            'otp' => $otp,
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    /**
     * Test all authenticated admin suite pages load without error.
     */
    public function test_admin_pages_render_for_authenticated_super_admin(): void
    {
        $user = User::where('email', 'hr@intraeats.com')->first();
        $employee = Employee::first();

        // Dashboard
        $this->actingAs($user)->get('/admin/dashboard')
            ->assertStatus(200)
            ->assertSee('Dashboard Overview');

        // Attendance Ledger
        $this->actingAs($user)->get('/admin/attendance')
            ->assertStatus(200)
            ->assertSee('Attendance Management Ledger');

        // Employees Directory
        $this->actingAs($user)->get('/admin/employees')
            ->assertStatus(200)
            ->assertSee('Employee Directory');

        // Employee Show / Profile
        $this->actingAs($user)->get('/admin/employees/' . $employee->id)
            ->assertStatus(200)
            ->assertSee($employee->full_name);

        // Projects & Tasks
        $this->actingAs($user)->get('/admin/projects')
            ->assertStatus(200)
            ->assertSee('Work Allocation');

        // Reports Center
        $this->actingAs($user)->get('/admin/reports')
            ->assertStatus(200)
            ->assertSee('Reports & Workforce Analytics');

        // Admin Users & RBAC
        $this->actingAs($user)->get('/admin/users')
            ->assertStatus(200)
            ->assertSee('RBAC Access Control');

        // System Settings
        $this->actingAs($user)->get('/admin/settings')
            ->assertStatus(200)
            ->assertSee('System Settings');

        // Audit Trail
        $this->actingAs($user)->get('/admin/audit')
            ->assertStatus(200)
            ->assertSee('System Audit Trail');
    }

    /**
     * Test Export endpoints (PDF, Excel, CSV)
     */
    public function test_reports_export_formats(): void
    {
        $user = User::where('email', 'hr@intraeats.com')->first();

        // PDF Export
        $pdfRes = $this->actingAs($user)->get('/admin/reports?export=pdf');
        $pdfRes->assertStatus(200);
        $this->assertStringContainsString('application/pdf', $pdfRes->headers->get('content-type'));

        // Excel Export
        $excelRes = $this->actingAs($user)->get('/admin/reports?export=excel');
        $excelRes->assertStatus(200);
        $this->assertStringContainsString('spreadsheet', $excelRes->headers->get('content-type'));

        // CSV Export
        $csvRes = $this->actingAs($user)->get('/admin/reports?export=csv');
        $csvRes->assertStatus(200);
        $this->assertStringContainsString('.csv', $csvRes->headers->get('content-disposition'));
    }

    /**
     * Test Employee Punch In, Duplicate Protection, and Punch Out
     */
    public function test_employee_punch_flow_and_duplicate_protection(): void
    {
        $employee = Employee::first();
        $this->assertNotNull($employee);

        $today = Carbon::today()->format('Y-m-d');
        Attendance::where('employee_id', $employee->id)->whereDate('date', $today)->delete();

        // 1. Initial Punch In
        $punchInRes = $this->postJson('/employee/punch', [
            'employee_code' => $employee->employee_code,
            'action' => 'checkin',
            'work_category' => 'Core Platform',
            'task_description' => 'Morning sprint tasks',
        ]);

        $punchInRes->assertStatus(200);
        $punchInRes->assertJson([
            'success' => true,
            'action' => 'checkin',
        ]);

        // 2. Duplicate Punch In Protection (Same Day)
        $dupRes = $this->postJson('/employee/punch', [
            'employee_code' => $employee->employee_code,
            'action' => 'checkin',
            'work_category' => 'Core Platform',
        ]);

        $dupRes->assertStatus(409);
        $dupRes->assertJson([
            'success' => false,
            'is_duplicate' => true,
        ]);

        // 3. Punch Out
        $punchOutRes = $this->postJson('/employee/punch', [
            'employee_code' => $employee->employee_code,
            'action' => 'checkout',
        ]);

        $punchOutRes->assertStatus(200);
        $punchOutRes->assertJson([
            'success' => true,
            'action' => 'checkout',
        ]);
    }

    /**
     * Test Clear Demo Data endpoint
     */
    public function test_clear_demo_data_endpoint(): void
    {
        $user = User::where('email', 'hr@intraeats.com')->first();

        $res = $this->actingAs($user)->post('/admin/clear-demo-data');
        $res->assertRedirect();

        // Verify demo employees and attendances are removed
        $this->assertEquals(0, Employee::where('is_demo', true)->count());
        $this->assertEquals(0, Attendance::where('is_demo', true)->count());

        // Verify Super Admin account is untouched
        $this->assertDatabaseHas('users', [
            'email' => 'hr@intraeats.com',
        ]);

        // Re-seed for local development session
        \Illuminate\Support\Facades\Artisan::call('db:seed');
    }
}
