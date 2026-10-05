<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Update users table with custom admin attributes
        Schema::table('users', function (Blueprint $table) {
            $table->string('mobile')->nullable()->after('email');
            $table->string('status')->default('ACTIVE')->after('password'); // ACTIVE, INACTIVE
            $table->string('avatar_url')->nullable()->after('status');
            $table->timestamp('last_login_at')->nullable()->after('avatar_url');
        });

        // 2. OTP Verifications table (for 2FA admin login, password reset, admin invite)
        Schema::create('otp_verifications', function (Blueprint $table) {
            $table->id();
            $table->string('email')->index();
            $table->string('hashed_otp');
            $table->string('purpose')->default('ADMIN_LOGIN'); // ADMIN_LOGIN, PASSWORD_RESET, CREATE_ADMIN
            $table->unsignedInteger('attempts')->default(0);
            $table->timestamp('expires_at');
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });

        // 3. Settings table
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value');
            $table->string('category')->default('GENERAL'); // COMPANY, ATTENDANCE, NOTIFICATION, SECURITY
            $table->timestamps();
        });

        // 4. Project Categories table
        Schema::create('project_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });

        // 5. Employees table
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('employee_id')->unique(); // e.g. IE001
            $table->string('employee_code')->unique(); // e.g. INTRA-EMP-001
            $table->string('full_name');
            $table->string('email')->nullable()->unique();
            $table->string('mobile')->nullable();
            $table->string('photo_url')->nullable();
            $table->date('joining_date');
            $table->string('department');
            $table->string('designation');
            $table->string('role');
            $table->string('employment_type')->default('Full-time'); // Full-time, Part-time, Contract, Intern
            $table->string('assigned_project')->nullable();
            $table->string('status')->default('ACTIVE'); // ACTIVE, INACTIVE, ON_NOTICE
            $table->text('address')->nullable();
            $table->string('emergency_contact')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_demo')->default(false);
            $table->timestamps();

            $table->index('status');
            $table->index('department');
        });

        // 6. Projects table
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('project_id')->unique(); // e.g. PRJ-001
            $table->string('name');
            $table->string('client');
            $table->string('manager');
            $table->string('status')->default('ACTIVE'); // ACTIVE, COMPLETED, ON_HOLD, ARCHIVED
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_demo')->default(false);
            $table->timestamps();
        });

        // 7. Project Assignments pivot table
        Schema::create('project_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->string('role')->default('Member');
            $table->timestamp('assigned_at')->useCurrent();
            $table->timestamps();

            $table->unique(['project_id', 'employee_id']);
        });

        // 8. Attendances table (unique per employee per date)
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->date('date');
            $table->string('check_in_time'); // e.g. "09:32 AM"
            $table->string('check_out_time')->nullable(); // e.g. "06:18 PM"
            $table->unsignedInteger('working_minutes')->default(0);
            $table->string('status')->default('PRESENT'); // PRESENT, LATE, ABSENT, LEAVE
            $table->string('work_category')->default('Internal Project');
            $table->text('task_description')->nullable();
            $table->text('remarks')->nullable();
            $table->string('modified_by')->nullable();
            $table->timestamp('modified_at')->nullable();
            $table->boolean('is_demo')->default(false);
            $table->timestamps();

            $table->unique(['employee_id', 'date']);
            $table->index('date');
            $table->index('status');
        });

        // 9. Audit Logs table
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('user_name');
            $table->string('action'); // LOGIN, LOGOUT, CREATE, UPDATE, DELETE, BULK_UPLOAD, EXPORT, CLEAR_DEMO, PASSWORD_CHANGE
            $table->string('module'); // AUTH, ATTENDANCE, EMPLOYEE, PROJECT, SETTINGS, SYSTEM, REPORTS
            $table->string('record_id')->nullable();
            $table->text('details')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('attendances');
        Schema::dropIfExists('project_assignments');
        Schema::dropIfExists('projects');
        Schema::dropIfExists('employees');
        Schema::dropIfExists('project_categories');
        Schema::dropIfExists('settings');
        Schema::dropIfExists('otp_verifications');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['mobile', 'status', 'avatar_url', 'last_login_at']);
        });
    }
};
