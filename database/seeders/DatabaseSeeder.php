<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use App\Models\User;
use App\Models\Setting;
use App\Models\ProjectCategory;
use App\Models\Employee;
use App\Models\Project;
use App\Models\ProjectAssignment;
use App\Models\Attendance;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command?->info("🌱 Seeding IntraEats & Talisha Software HR System...");

        // 1. Roles & Permissions via Spatie
        $roles = [
            'Super Admin',
            'HR Admin',
            'Attendance Manager',
            'Report Manager',
        ];

        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
        }
        $this->command?->info("✓ Roles created via Spatie.");

        // 2. System Settings
        $settings = [
            ['key' => 'company_name', 'value' => 'IntraEats & Talisha Software', 'category' => 'COMPANY'],
            ['key' => 'company_email', 'value' => 'hr@intraeats.com', 'category' => 'COMPANY'],
            ['key' => 'company_website', 'value' => 'https://talishasoftware.tech', 'category' => 'COMPANY'],
            ['key' => 'company_phone', 'value' => '+91 98765 43210', 'category' => 'COMPANY'],
            ['key' => 'company_address', 'value' => 'Level 4, Food-Tech Hub, Cyber Park, Bangalore, India', 'category' => 'COMPANY'],
            ['key' => 'timezone', 'value' => 'Asia/Kolkata', 'category' => 'COMPANY'],
            ['key' => 'office_start_time', 'value' => '09:30 AM', 'category' => 'ATTENDANCE'],
            ['key' => 'office_end_time', 'value' => '06:30 PM', 'category' => 'ATTENDANCE'],
            ['key' => 'late_grace_minutes', 'value' => '15', 'category' => 'ATTENDANCE'],
            ['key' => 'full_day_hours', 'value' => '8', 'category' => 'ATTENDANCE'],
            ['key' => 'weekend_config', 'value' => json_encode(['Saturday', 'Sunday']), 'category' => 'ATTENDANCE'],
            ['key' => 'otp_expiry_minutes', 'value' => '10', 'category' => 'SECURITY'],
            ['key' => 'max_login_attempts', 'value' => '5', 'category' => 'SECURITY'],
            ['key' => 'session_timeout_hours', 'value' => '24', 'category' => 'SECURITY'],
            ['key' => 'email_notifications_enabled', 'value' => 'true', 'category' => 'NOTIFICATION'],
            ['key' => 'attendance_confirmation_email', 'value' => 'true', 'category' => 'NOTIFICATION'],
        ];

        foreach ($settings as $s) {
            Setting::updateOrCreate(['key' => $s['key']], $s);
        }
        $this->command?->info("✓ System settings seeded.");

        // 3. Project Categories
        $categories = [
            ['name' => 'Internal Project', 'description' => 'Internal food-tech infrastructure', 'is_default' => true],
            ['name' => 'Client Project', 'description' => 'Commercial client deliverables', 'is_default' => false],
            ['name' => 'Development', 'description' => 'Engineering, backend, frontend tasks', 'is_default' => false],
            ['name' => 'Design', 'description' => 'UI/UX and brand design assets', 'is_default' => false],
            ['name' => 'Operations', 'description' => 'Fleet logistics and order dispatch', 'is_default' => false],
            ['name' => 'HR', 'description' => 'Human resources, culture and hiring', 'is_default' => false],
            ['name' => 'Marketing', 'description' => 'Growth and restaurant onboarding', 'is_default' => false],
            ['name' => 'Other', 'description' => 'General tasks and admin operations', 'is_default' => false],
        ];

        foreach ($categories as $cat) {
            ProjectCategory::updateOrCreate(['name' => $cat['name']], $cat);
        }
        $this->command?->info("✓ Project categories seeded.");

        // 4. Admin Users
        $adminEmail = env('ADMIN_INITIAL_EMAIL', 'hr@intraeats.com');
        $adminPassword = env('ADMIN_INITIAL_PASSWORD', 'Admin@IntraEats2026!');

        $superAdmin = User::updateOrCreate(
            ['email' => $adminEmail],
            [
                'name' => env('ADMIN_INITIAL_NAME', 'IntraEats HR Admin'),
                'mobile' => '+91 98765 43210',
                'password' => Hash::make($adminPassword),
                'status' => 'ACTIVE',
                'avatar_url' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=256&q=80',
            ]
        );
        $superAdmin->assignRole('Super Admin');

        $manager = User::updateOrCreate(
            ['email' => 'manager@intraeats.com'],
            [
                'name' => 'Kavita Nair (Attendance Ops)',
                'mobile' => '+91 98765 43219',
                'password' => Hash::make($adminPassword),
                'status' => 'ACTIVE',
                'avatar_url' => 'https://images.unsplash.com/photo-1580489944761-15a19d654956?auto=format&fit=crop&w=256&q=80',
            ]
        );
        $manager->assignRole('Attendance Manager');
        $this->command?->info("✓ Admins seeded: {$superAdmin->email} & {$manager->email}");

        // 5. 10 Employees
        $employeesData = [
            [
                'employee_id' => 'IE001',
                'employee_code' => 'INTRA-EMP-001',
                'full_name' => 'Rahul Das',
                'email' => 'rahul.das@intraeats.com',
                'mobile' => '+91 98765 00001',
                'department' => 'Technology',
                'designation' => 'Software Developer',
                'role' => 'Full-Stack Engineer',
                'employment_type' => 'Full-time',
                'joining_date' => '2025-01-15',
                'assigned_project' => 'IntraEats Fleet Rider App',
                'status' => 'ACTIVE',
                'address' => 'Indiranagar, Bangalore',
                'emergency_contact' => '+91 98765 99001 (Father)',
                'photo_url' => 'https://images.unsplash.com/photo-1539571696357-5a69c17a67c6?auto=format&fit=crop&w=256&q=80',
                'notes' => 'Key engineer on real-time kitchen and fleet telemetry.',
                'is_demo' => true,
            ],
            [
                'employee_id' => 'IE002',
                'employee_code' => 'INTRA-EMP-002',
                'full_name' => 'Priya Sharma',
                'email' => 'priya.sharma@intraeats.com',
                'mobile' => '+91 98765 00002',
                'department' => 'Design',
                'designation' => 'UI/UX Designer',
                'role' => 'Product Designer',
                'employment_type' => 'Full-time',
                'joining_date' => '2025-03-10',
                'assigned_project' => 'Kitchen Display System 2.0',
                'status' => 'ACTIVE',
                'address' => 'Koramangala 4th Block, Bangalore',
                'emergency_contact' => '+91 98765 99002 (Mother)',
                'photo_url' => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=256&q=80',
                'notes' => 'Author of the IntraEats Pulse Design System.',
                'is_demo' => true,
            ],
            [
                'employee_id' => 'IE003',
                'employee_code' => 'INTRA-EMP-003',
                'full_name' => 'Arjun Singh',
                'email' => 'arjun.singh@intraeats.com',
                'mobile' => '+91 98765 00003',
                'department' => 'Operations',
                'designation' => 'Project Coordinator',
                'role' => 'Scrum Master',
                'employment_type' => 'Full-time',
                'joining_date' => '2025-06-02',
                'assigned_project' => 'IntraEats Fleet Rider App',
                'status' => 'ACTIVE',
                'address' => 'HSR Layout Sector 2, Bangalore',
                'emergency_contact' => '+91 98765 99003 (Brother)',
                'photo_url' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=256&q=80',
                'notes' => 'Coordinates urban delivery dispatch hubs.',
                'is_demo' => true,
            ],
            [
                'employee_id' => 'IE004',
                'employee_code' => 'INTRA-EMP-004',
                'full_name' => 'Sneha Patel',
                'email' => 'sneha.patel@intraeats.com',
                'mobile' => '+91 98765 00004',
                'department' => 'Technology',
                'designation' => 'Frontend Engineer',
                'role' => 'Alpine & Blade Specialist',
                'employment_type' => 'Full-time',
                'joining_date' => '2025-01-12',
                'assigned_project' => 'Talisha POS Cloud Engine',
                'status' => 'ACTIVE',
                'address' => 'Whitefield, Bangalore',
                'emergency_contact' => '+91 98765 99004 (Spouse)',
                'photo_url' => 'https://images.unsplash.com/photo-1517841905240-472988babdf9?auto=format&fit=crop&w=256&q=80',
                'notes' => 'High performance frontend engineering.',
                'is_demo' => true,
            ],
            [
                'employee_id' => 'IE005',
                'employee_code' => 'INTRA-EMP-005',
                'full_name' => 'Vikram Malhotra',
                'email' => 'vikram.m@intraeats.com',
                'mobile' => '+91 98765 00005',
                'department' => 'Technology',
                'designation' => 'Backend Lead',
                'role' => 'Principal Architect',
                'employment_type' => 'Full-time',
                'joining_date' => '2025-02-01',
                'assigned_project' => 'Talisha POS Cloud Engine',
                'status' => 'ACTIVE',
                'address' => 'Bellandur, Bangalore',
                'emergency_contact' => '+91 98765 99005 (Wife)',
                'photo_url' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=256&q=80',
                'notes' => 'Architect of MySQL 8 multi-tenant DB structure.',
                'is_demo' => true,
            ],
            [
                'employee_id' => 'IE006',
                'employee_code' => 'INTRA-EMP-006',
                'full_name' => 'Ananya Roy',
                'email' => 'ananya.roy@intraeats.com',
                'mobile' => '+91 98765 00006',
                'department' => 'Quality Assurance',
                'designation' => 'QA Specialist',
                'role' => 'SDET Automation Lead',
                'employment_type' => 'Full-time',
                'joining_date' => '2025-02-18',
                'assigned_project' => 'Kitchen Display System 2.0',
                'status' => 'ACTIVE',
                'address' => 'BTM Layout 2nd Stage, Bangalore',
                'emergency_contact' => '+91 98765 99006 (Father)',
                'photo_url' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&w=256&q=80',
                'notes' => 'Automated test suite lead.',
                'is_demo' => true,
            ],
            [
                'employee_id' => 'IE007',
                'employee_code' => 'INTRA-EMP-007',
                'full_name' => 'Rohan Verma',
                'email' => 'rohan.v@intraeats.com',
                'mobile' => '+91 98765 00007',
                'department' => 'Logistics',
                'designation' => 'Delivery Fleet Lead',
                'role' => 'Operations Supervisor',
                'employment_type' => 'Full-time',
                'joining_date' => '2025-04-05',
                'assigned_project' => 'IntraEats Fleet Rider App',
                'status' => 'ACTIVE',
                'address' => 'Electronic City, Bangalore',
                'emergency_contact' => '+91 98765 99007 (Sister)',
                'photo_url' => 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?auto=format&fit=crop&w=256&q=80',
                'notes' => 'Supervises electric bike fleet riders.',
                'is_demo' => true,
            ],
            [
                'employee_id' => 'IE008',
                'employee_code' => 'INTRA-EMP-008',
                'full_name' => 'Kavita Nair',
                'email' => 'kavita.n@intraeats.com',
                'mobile' => '+91 98765 00008',
                'department' => 'Human Resources',
                'designation' => 'HR Executive',
                'role' => 'People Ops Associate',
                'employment_type' => 'Full-time',
                'joining_date' => '2025-05-20',
                'assigned_project' => 'HR Pulse & Biometric Gateway',
                'status' => 'ACTIVE',
                'address' => 'JP Nagar Phase 6, Bangalore',
                'emergency_contact' => '+91 98765 99008 (Husband)',
                'photo_url' => 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=256&q=80',
                'notes' => 'Manages employee documentation & compliance.',
                'is_demo' => true,
            ],
            [
                'employee_id' => 'IE009',
                'employee_code' => 'INTRA-EMP-009',
                'full_name' => 'Amit Joshi',
                'email' => 'amit.joshi@intraeats.com',
                'mobile' => '+91 98765 00009',
                'department' => 'Infrastructure',
                'designation' => 'DevOps Engineer',
                'role' => 'Cloud SRE',
                'employment_type' => 'Full-time',
                'joining_date' => '2025-06-14',
                'assigned_project' => 'Talisha POS Cloud Engine',
                'status' => 'ACTIVE',
                'address' => 'Marathahalli, Bangalore',
                'emergency_contact' => '+91 98765 99009 (Mother)',
                'photo_url' => 'https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?auto=format&fit=crop&w=256&q=80',
                'notes' => 'Manages deployment pipeline and server clusters.',
                'is_demo' => true,
            ],
            [
                'employee_id' => 'IE010',
                'employee_code' => 'INTRA-EMP-010',
                'full_name' => 'Neha Gupta',
                'email' => 'neha.gupta@intraeats.com',
                'mobile' => '+91 98765 00010',
                'department' => 'Marketing',
                'designation' => 'Product Marketing Lead',
                'role' => 'Growth Lead',
                'employment_type' => 'Full-time',
                'joining_date' => '2025-07-01',
                'assigned_project' => 'Loyalty Rewards & Subscriptions',
                'status' => 'ACTIVE',
                'address' => 'Sarjapur Road, Bangalore',
                'emergency_contact' => '+91 98765 99010 (Father)',
                'photo_url' => 'https://images.unsplash.com/photo-1580489944761-15a19d654956?auto=format&fit=crop&w=256&q=80',
                'notes' => 'Drives food tech customer acquisition campaigns.',
                'is_demo' => true,
            ],
        ];

        $createdEmployees = [];
        foreach ($employeesData as $emp) {
            $createdEmployees[] = Employee::updateOrCreate(['employee_code' => $emp['employee_code']], $emp);
        }
        $this->command?->info("✓ Seeded 10 employees.");

        // 6. 5 Projects
        $projectsData = [
            [
                'project_id' => 'PRJ-001',
                'name' => 'IntraEats Fleet Rider App',
                'client' => 'IntraEats Logistics Direct',
                'manager' => 'Arjun Singh',
                'status' => 'ACTIVE',
                'start_date' => '2025-06-01',
                'end_date' => '2026-12-31',
                'description' => 'Mobile dispatch and telemetry for 500+ electric delivery riders.',
                'is_demo' => true,
            ],
            [
                'project_id' => 'PRJ-002',
                'name' => 'Talisha POS Cloud Engine',
                'client' => 'Talisha Software Tech',
                'manager' => 'Vikram Malhotra',
                'status' => 'ACTIVE',
                'start_date' => '2025-02-15',
                'end_date' => '2026-11-30',
                'description' => 'Multi-tenant cloud billing and kitchen management POS engine.',
                'is_demo' => true,
            ],
            [
                'project_id' => 'PRJ-003',
                'name' => 'Kitchen Display System 2.0',
                'client' => 'IntraEats Cloud Kitchens',
                'manager' => 'Rahul Das',
                'status' => 'ACTIVE',
                'start_date' => '2025-03-20',
                'end_date' => '2026-10-15',
                'description' => 'Real-time kitchen order tickets (KOT) terminal with station timers.',
                'is_demo' => true,
            ],
            [
                'project_id' => 'PRJ-004',
                'name' => 'Loyalty Rewards & Subscriptions',
                'client' => 'IntraEats B2C Retail',
                'manager' => 'Neha Gupta',
                'status' => 'ACTIVE',
                'start_date' => '2025-07-10',
                'end_date' => '2026-08-31',
                'description' => 'Diner membership club, meal subscriptions, and rewards.',
                'is_demo' => true,
            ],
            [
                'project_id' => 'PRJ-005',
                'name' => 'HR Pulse & Biometric Gateway',
                'client' => 'Internal Workforce Ops',
                'manager' => 'Kavita Nair',
                'status' => 'COMPLETED',
                'start_date' => '2025-05-01',
                'end_date' => '2025-09-30',
                'description' => 'Enterprise biometric attendance hardware integration.',
                'is_demo' => true,
            ],
        ];

        foreach ($projectsData as $prj) {
            $project = Project::updateOrCreate(['project_id' => $prj['project_id']], $prj);

            if ($prj['project_id'] === 'PRJ-001') {
                ProjectAssignment::firstOrCreate(['project_id' => $project->id, 'employee_id' => $createdEmployees[0]->id], ['role' => 'Tech Lead']);
                ProjectAssignment::firstOrCreate(['project_id' => $project->id, 'employee_id' => $createdEmployees[2]->id], ['role' => 'Project Coordinator']);
                ProjectAssignment::firstOrCreate(['project_id' => $project->id, 'employee_id' => $createdEmployees[6]->id], ['role' => 'Fleet Lead']);
            }
        }
        $this->command?->info("✓ Seeded 5 projects and team assignments.");

        // 7. 30 Days Realistic Attendance
        $this->command?->info("Generating 30 days of realistic attendance history...");
        $now = Carbon::today();
        $tasksList = [
            "Refactored kitchen order routing socket connection.",
            "Designed rider profile flow for onboarding portal.",
            "Sprint backlog grooming and fleet capacity estimation.",
            "Integrated UPI payment gateway webhook listener.",
            "Added database indexing on high-traffic order tables.",
            "Created end-to-end regression tests for billing desk.",
            "Dispatched batch of 24 new electric cargo bikes.",
            "Conducted monthly performance review interviews.",
            "Configured Prometheus alerts for cloud cluster.",
            "Launched festive 20% discount marketing banner.",
        ];

        $totalRecords = 0;
        for ($dayOffset = 30; $dayOffset >= 0; $dayOffset--) {
            $date = $now->copy()->subDays($dayOffset);
            if ($date->isSunday()) continue; // Sunday off

            $dateStr = $date->format('Y-m-d');

            foreach ($createdEmployees as $i => $emp) {
                // If today, only first 4 have punched in so far
                if ($dayOffset === 0 && $i >= 4) continue;

                $rand = ($i * 19 + $dayOffset * 37) % 100;
                $status = 'PRESENT';
                $checkInTime = '09:18 AM';
                $checkOutTime = '06:34 PM';
                $workingMinutes = 556; // 9h 16m

                if ($rand >= 94) {
                    $status = 'ABSENT';
                    $checkInTime = '-';
                    $checkOutTime = null;
                    $workingMinutes = 0;
                } elseif ($rand >= 88) {
                    $status = 'LEAVE';
                    $checkInTime = '-';
                    $checkOutTime = null;
                    $workingMinutes = 0;
                } elseif ($rand >= 74) {
                    $status = 'LATE';
                    $lateMinute = 35 + (($rand * 3) % 25);
                    $checkInTime = sprintf('09:%02d AM', $lateMinute);
                    $checkOutTime = '06:45 PM';
                    $workingMinutes = 550 - ($lateMinute - 30);
                } else {
                    $inMinute = 10 + (($rand * 2) % 20);
                    $checkInTime = sprintf('09:%02d AM', $inMinute);
                    $outMinute = 25 + (($rand * 5) % 25);
                    $checkOutTime = sprintf('06:%02d PM', $outMinute);
                    $workingMinutes = 540 + ($outMinute - $inMinute);
                }

                // If today, leave check-out empty for active shifts
                if ($dayOffset === 0) {
                    if ($i === 0) {
                        $checkInTime = '09:32 AM';
                        $checkOutTime = null;
                        $workingMinutes = 0;
                        $status = 'LATE';
                    } elseif ($i === 1) {
                        $checkInTime = '09:14 AM';
                        $checkOutTime = null;
                        $workingMinutes = 0;
                        $status = 'PRESENT';
                    } elseif ($i === 2) {
                        $checkInTime = '09:20 AM';
                        $checkOutTime = '06:18 PM';
                        $workingMinutes = 538;
                        $status = 'PRESENT';
                    }
                }

                $task = $tasksList[$i % count($tasksList)];

                Attendance::updateOrCreate(
                    [
                        'employee_id' => $emp->id,
                        'date' => $dateStr,
                    ],
                    [
                        'check_in_time' => $checkInTime,
                        'check_out_time' => $checkOutTime,
                        'working_minutes' => $workingMinutes,
                        'status' => $status,
                        'work_category' => $i % 2 === 0 ? 'Internal Project' : 'Client Project',
                        'task_description' => !in_array($status, ['ABSENT', 'LEAVE']) ? $task : null,
                        'remarks' => $status === 'LATE' ? 'Late traffic delay' : null,
                        'is_demo' => true,
                    ]
                );
                $totalRecords++;
            }
        }

        $this->command?->info("✓ Seeded {$totalRecords} attendance records across past 30 days.");
        $this->command?->info("🌱 Database seeded successfully!");
    }
}
