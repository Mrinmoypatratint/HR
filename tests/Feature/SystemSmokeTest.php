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
        $response->assertSee('IntraEats X Talisha Software');
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

        // 1. Unauthenticated punch is rejected (Login Required)
        $guestRes = $this->postJson('/employee/punch', [
            'employee_code' => $employee->employee_code,
            'action' => 'checkin',
            'work_category' => 'Core Platform',
            'task_description' => 'Morning sprint tasks',
        ]);
        $guestRes->assertStatus(401);
        $guestRes->assertJson([
            'success' => false,
            'requires_auth' => true,
        ]);

        // 2. Initial Punch In with authenticated employee
        $punchInRes = $this->actingAs($employee, 'employee')->postJson('/employee/punch', [
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

        // 3. Duplicate Punch In Protection (Same Day)
        $dupRes = $this->actingAs($employee, 'employee')->postJson('/employee/punch', [
            'employee_code' => $employee->employee_code,
            'action' => 'checkin',
            'work_category' => 'Core Platform',
        ]);

        $dupRes->assertStatus(409);
        $dupRes->assertJson([
            'success' => false,
            'is_duplicate' => true,
        ]);

        // 4. Punch Out
        $punchOutRes = $this->actingAs($employee, 'employee')->postJson('/employee/punch', [
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

    /**
     * Test admin adds employee and password setup email is dispatched.
     */
    public function test_admin_adds_employee_and_password_setup_mail_dispatched(): void
    {
        \Illuminate\Support\Facades\Mail::fake();

        $admin = User::where('email', 'hr@intraeats.com')->first();
        $this->assertNotNull($admin);

        Employee::where('employee_code', 'INTRA-EMP-999')->orWhere('employee_id', 'IE999')->delete();

        $employeeData = [
            'employee_id' => 'IE999',
            'employee_code' => 'INTRA-EMP-999',
            'full_name' => 'Aarav Mehta',
            'email' => 'aarav.mehta@intraeats.com',
            'mobile' => '+91 98765 88999',
            'department' => 'Technology',
            'designation' => 'Frontend Architect',
            'role' => 'Principal UI Engineer',
            'employment_type' => 'Full-time',
            'joining_date' => '2026-10-01',
            'status' => 'ACTIVE',
        ];

        $response = $this->actingAs($admin)->post('/admin/employees', $employeeData);
        $response->assertRedirect('/admin/employees');

        $employee = Employee::where('employee_code', 'INTRA-EMP-999')->first();
        $this->assertNotNull($employee, 'Employee must be created.');
        $this->assertNotNull($employee->password_reset_token, 'Reset token must be generated.');
        $this->assertNotNull($employee->password_reset_sent_at);

        \Illuminate\Support\Facades\Mail::assertSent(\App\Mail\EmployeeSetPasswordMail::class, function ($mail) use ($employee) {
            return $mail->hasTo('aarav.mehta@intraeats.com')
                && $mail->token === $employee->password_reset_token
                && $mail->isNewAccount === true;
        });
    }

    /**
     * Test employee sets password via token link and is automatically logged in.
     */
    public function test_employee_sets_password_via_token_link(): void
    {
        $employee = Employee::where('employee_code', 'INTRA-EMP-999')->first();
        $this->assertNotNull($employee);

        // 1. Visit Set Password Page with token
        $viewRes = $this->get('/employee/set-password?token=' . $employee->password_reset_token . '&email=' . urlencode($employee->email));
        $viewRes->assertStatus(200);
        $viewRes->assertSee('Set Your Account Password');
        $viewRes->assertSee($employee->full_name);

        // 2. Submit new password
        $setRes = $this->post('/employee/set-password', [
            'token' => $employee->password_reset_token,
            'email' => $employee->email,
            'password' => 'NewSecurePassword123!',
            'password_confirmation' => 'NewSecurePassword123!',
        ]);

        $setRes->assertRedirect(route('employee.dashboard'));
        $this->assertAuthenticatedAs($employee, 'employee');

        // Verify token cleared and password hashed in DB
        $employee->refresh();
        $this->assertNull($employee->password_reset_token);
        $this->assertTrue(Hash::check('NewSecurePassword123!', $employee->password));
    }

    /**
     * Test employee login with credentials and access to dashboard & personal data.
     */
    public function test_employee_login_and_dashboard_data_visibility(): void
    {
        $employee = Employee::where('employee_code', 'INTRA-EMP-999')->first();
        $this->assertNotNull($employee);

        // Log out first
        $this->post('/employee/logout');
        $this->assertGuest('employee');

        // Test login page renders
        $this->get('/employee/login')
            ->assertStatus(200)
            ->assertSee('Sign In to Employee Portal');

        // Login with Employee Code
        $loginRes = $this->post('/employee/login', [
            'login' => $employee->employee_code,
            'password' => 'NewSecurePassword123!',
        ]);

        $loginRes->assertRedirect(route('employee.dashboard'));
        $this->assertAuthenticatedAs($employee, 'employee');

        // View Employee Dashboard (Their own data only)
        $dashRes = $this->actingAs($employee, 'employee')->get('/employee/dashboard');
        $dashRes->assertStatus(200);
        $dashRes->assertSee($employee->full_name);
        $dashRes->assertSee($employee->employee_code);
        $dashRes->assertSee("Punch Station");
        $dashRes->assertSee("My Personal Attendance Ledger");
        $dashRes->assertSee("Digital Employee ID Card");
    }

    /**
     * Test authenticated employee marks attendance and views ledger updates.
     */
    public function test_authenticated_employee_punch_attendance_lifecycle(): void
    {
        $employee = Employee::where('employee_code', 'INTRA-EMP-999')->first();
        $this->assertNotNull($employee);

        $today = Carbon::today()->format('Y-m-d');
        Attendance::where('employee_id', $employee->id)->whereDate('date', $today)->delete();

        // 1. Authenticated Punch In
        $punchIn = $this->actingAs($employee, 'employee')->postJson('/employee/punch', [
            'action' => 'checkin',
            'work_category' => 'Technology',
            'task_description' => 'Implementing new employee onboarding flow',
        ]);

        $punchIn->assertStatus(200);
        $punchIn->assertJson([
            'success' => true,
            'action' => 'checkin',
        ]);

        // Verify record in DB
        $this->assertDatabaseHas('attendances', [
            'employee_id' => $employee->id,
            'work_category' => 'Technology',
        ]);

        // 2. Authenticated Punch Out
        $punchOut = $this->actingAs($employee, 'employee')->postJson('/employee/punch', [
            'action' => 'checkout',
        ]);

        $punchOut->assertStatus(200);
        $punchOut->assertJson([
            'success' => true,
            'action' => 'checkout',
        ]);

        // 3. Dashboard shows completed shift
        $dashRes = $this->actingAs($employee, 'employee')->get('/employee/dashboard');
        $dashRes->assertStatus(200);
        $dashRes->assertSee('Implementing new employee onboarding flow');

        // 4. Employee Logout
        $logoutRes = $this->actingAs($employee, 'employee')->post('/employee/logout');
        $logoutRes->assertRedirect(route('employee.login'));
        $this->assertGuest('employee');

        // Cleanup created test employee
        $employee->delete();
    }
}
