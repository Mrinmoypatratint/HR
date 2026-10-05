# IntraEats & Talisha Software — HR Attendance & Workforce Management System

A production-ready, enterprise-grade HR Attendance, Time-Tracking, and Workforce Management web application built specifically for **IntraEats & Talisha Software** (food-tech operations and logistics).

Designed and fully optimized for **Hostinger Shared Hosting** (cPanel / hPanel), requiring **zero Node.js server in production**.

---

## 1. Hostinger-Compatible Tech Stack

| Layer | Technology | Hostinger Compatibility Details |
|---|---|---|
| **Backend** | PHP 8.2+ / 8.4, Laravel 11.x | Standard Hostinger PHP runtime with required extensions (`pdo_mysql`, `mbstring`, `openssl`, `gd`, `zip`, `xml`). |
| **Database** | MySQL 8.0 / MariaDB 10.5+ (`utf8mb4`) | Local development runs SQLite/MySQL; production seamlessly uses Hostinger MySQL with full migrations and seeders. |
| **Frontend** | Blade templates + Tailwind CSS 3.4 + Alpine.js | Assets are pre-compiled locally with Vite into `public/build` (`manifest.json`, `app.css`, `app.js`). **No Node.js daemon is required on Hostinger**. |
| **Charts** | Chart.js 4.x | Rounded-bar 7-day attendance trends and donut status distribution. |
| **Iconography** | Lucide Icons | Single unified icon library throughout the entire system. **Zero emojis used as icons**. |
| **Authentication** | Custom 2-Factor Flow (Password + 6-digit Email OTP) | Passwords hashed with Bcrypt (cost 12); 6-digit OTPs dispatched via Hostinger SMTP with rate limiting and expiration. |
| **RBAC** | `spatie/laravel-permission` (v6) | 4 granular roles: `Super Admin`, `HR Admin`, `Attendance Manager`, `Report Manager`. |
| **Document Exports** | `barryvdh/laravel-dompdf` + `maatwebsite/excel` | Executive PDF attendance reports (A4 Landscape) and multi-sheet Excel (.xlsx) / CSV (.csv) exports. |
| **Email Service** | Laravel Mail via SMTP | Configured for Hostinger Business Email (`smtp.hostinger.com:465` SSL) with queued jobs and custom email templates. |
| **Job Queue** | Database Queue Driver (`queue:work` via Cron) | Handled by scheduled Hostinger cron job; **no always-on supervisor/daemon process needed**. |
| **Timezone** | Configurable via Settings (Default: `Asia/Kolkata`) | All attendance records, punch calculations, and audit logs use server-synchronized timestamps. |

---

## 2. Seeded Default Credentials

### Administrative Accounts (2FA Protected)
| Role | Email | Password | 2FA Flow |
|---|---|---|---|
| **Super Admin** | `hr@intraeats.com` | `Admin@IntraEats2026!` | Password &rarr; 6-digit Email OTP |
| **Attendance Manager** | `manager@intraeats.com` | `Admin@IntraEats2026!` | Password &rarr; 6-digit Email OTP |

> **Development Note**: When running locally or in testing, the generated 6-digit OTP is automatically displayed on the verification screen and logged to `storage/logs/laravel.log` for frictionless testing.

### Sample Employee Codes (Kiosk / Mobile Portal)
- `INTRA-EMP-001` — Rahul Das (Lead Engineer, Technology)
- `INTRA-EMP-002` — Priya Sharma (Product Designer, Design)
- `INTRA-EMP-003` — Arjun Singh (Fleet Manager, Operations)
- `INTRA-EMP-004` — Sneha Patel (Frontend Developer, Technology)
- `INTRA-EMP-005` — Vikram Malhotra (Backend Developer, Technology)
- `INTRA-EMP-006` through `INTRA-EMP-010` (QA, Logistics, HR, Infrastructure, Marketing)

---

## 3. Key Modules & Features

### A. Employee Attendance Portal (`/`)
- **Mobile-First & Kiosk Optimized**: Clean interface for tablet wall-mounts and employee smartphones.
- **Precision Live Clock**: Synchronized with server time in `Asia/Kolkata`.
- **Digital Employee ID Card**: Instant verification showing employee photo, department, designation, and today's status upon entering employee code.
- **Duplicate Punch Protection**: Prevents multiple check-ins on the same day with immediate warning and current status display.
- **Automatic Duration Calculation**: Computes exact working hours and minutes upon check-out.
- **Punch Confirmation Modal**: Printable/downloadable receipt with complete audit metadata.
- **Email Receipt**: Dispatches attendance receipt to employee upon check-in and check-out.

### B. Admin Management Suite (`/admin`)
1. **2FA Login & Password Reset**: 2-step verification enforcing email OTPs with countdown timer and resend protection.
2. **Dashboard Overview**:
   - 5 Live KPI Tiles: Total Staff, Present Today, Late Arrivals, Absences, Active Delivery Projects.
   - 7-Day Workforce Bar Chart (Present, Late, Leave, Absent).
   - Donut Chart showing workforce distribution.
   - Today's live punch stream with real-time status badges.
3. **Attendance Management Ledger (`/admin/attendance`)**:
   - Multi-field search, department filter, status filter, date range picker.
   - Manual punch modal (+ Create Manual Attendance).
   - Bulk CSV/Excel attendance upload with row-by-row error categorization (Duplicates, Invalid Codes, Invalid Dates).
   - Edit punch modal and audit-logged deletion.
4. **Employee Directory (`/admin/employees`)**:
   - Directory with department filtering, search, and active/inactive toggle.
   - Add new employee modal with auto-generated code (`INTRA-EMP-XXX`).
   - Detailed tabbed employee profile (`/admin/employees/{id}`) with monthly statistics, 30-day timeline, and edit modal.
5. **Projects & Task Allocations (`/admin/projects`)**:
   - Initiative tracking with progress meters, budget, priority badges, and active team members.
   - Team assignment modal with customizable role designation.
6. **Reports & Analytics Center (`/admin/reports`)**:
   - Quick date presets: *Today*, *Last 7 Days*, *This Month*, *Custom*.
   - Filter by report category (Late arrivals only, Absences only, Leaves only, Department, Employee).
   - Visual progress bar showing workforce distribution percentages.
   - One-click **PDF Export** (`barryvdh/laravel-dompdf`, A4 Landscape).
   - One-click **Excel Export** (`maatwebsite/excel`, 4-sheet formatted workbook).
   - One-click **CSV Export**.
7. **Admin Users & RBAC (`/admin/users`)**:
   - Spatie RBAC permission matrix (`Super Admin`, `HR Admin`, `Attendance Manager`, `Report Manager`).
   - Add admin modal, Spatie role switcher modal, and account active/inactive toggle.
8. **System Settings & Rules (`/admin/settings`)**:
   - Organization details (Company name, HR email, Timezone, Address).
   - Attendance policies (Shift start/end times, Grace period in minutes, Half-day threshold, Full-day minimum hours).
   - 2FA & Security policies (OTP expiry, max verification attempts, session lifetime).
   - Personal admin profile editor & password change form.
   - **Production Transition ("Clear Demo Data")**: One-click purge of all mock sample employees and attendance logs while retaining admin accounts and system configurations.
9. **Tamper-Evident Audit Trail (`/admin/audit`)**:
   - Immutable log capturing every punch, administrative adjustment, export download, and login event with IP address and operator stamp.

---

## 4. Local Development Setup

### Prerequisites
- PHP 8.2 or 8.4
- Composer 2.x
- SQLite or MySQL
- Node.js 18+ (only needed for compiling assets with `npm run build`)

### Installation Steps
```bash
# 1. Clone repository and navigate to workspace
cd d:/Projects/stitch_intraeats_hr_attendance_system

# 2. Install Composer dependencies
composer install

# 3. Setup environment configuration
cp .env.example .env
php artisan key:generate

# 4. Run database migrations & seed demo data
php artisan migrate:fresh --seed

# 5. Build frontend production assets
npm install
npm run build

# 6. Start development server
php artisan serve
```
Visit `http://localhost:8000` for the Employee Kiosk and `http://localhost:8000/admin/login` for the Admin Suite.

### Running Automated Test Suite
A comprehensive test suite is included in `tests/Feature/SystemSmokeTest.php`:
```bash
php -d memory_limit=512M vendor/bin/phpunit tests/Feature/SystemSmokeTest.php
```
All 8 automated tests verify:
- Employee portal loading and live clock
- Employee code verification API
- Admin 2-step login and OTP verification
- Authenticated admin pages (Dashboard, Ledger, Directory, Profile, Projects, Reports, Users, Settings, Audit)
- PDF, Excel, and CSV report export generation
- Employee punch in, duplicate punch protection (409 Conflict), and punch out
- Demo data reset and restoration

---

## 5. Hostinger Production Deployment Guide

Hostinger shared hosting does not run a continuous Node.js daemon or supervisor worker. This system was engineered specifically to adhere to Hostinger constraints:

### Step 1: Pre-Build Assets Locally
All CSS, JavaScript, and Lucide icons are bundled into `public/build`. Run:
```bash
npm run build
```
Ensure the `public/build/` directory is committed to Git or uploaded with your files.

### Step 2: Upload Files to Hostinger
Upload all project files to Hostinger via Git or FTP into your web root (e.g. `/home/uXXXXXX/public_html` or `/domains/yourdomain.com/public_html`).

### Step 3: Configure Web Root
In Hostinger **hPanel** &rarr; **Websites** &rarr; **Website Configuration**:
- Set the document root to `/public` (e.g. `public_html/public`).
- Ensure PHP version is set to **PHP 8.2** or **PHP 8.3/8.4**.

### Step 4: Configure Database (`.env`)
In Hostinger **hPanel** &rarr; **Databases**, create a MySQL database:
```env
APP_NAME="IntraEats HR"
APP_ENV=production
APP_KEY=base64:YOUR_GENERATED_KEY_HERE
APP_DEBUG=false
APP_URL=https://your-domain.com
APP_TIMEZONE=Asia/Kolkata

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=uXXXXXX_intraeats
DB_USERNAME=uXXXXXX_dbuser
DB_PASSWORD=YourStrongDatabasePassword

SESSION_DRIVER=database
QUEUE_CONNECTION=database
CACHE_STORE=database

MAIL_MAILER=smtp
MAIL_HOST=smtp.hostinger.com
MAIL_PORT=465
MAIL_USERNAME=hr@intraeats.com
MAIL_PASSWORD=YourHostingerEmailPassword
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS="hr@intraeats.com"
MAIL_FROM_NAME="IntraEats HR"
```

### Step 5: Run Migrations on Hostinger
Connect via SSH to your Hostinger account or run through Terminal in hPanel:
```bash
php artisan migrate --force
php artisan db:seed --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Step 6: Set Directory Permissions
Ensure Laravel storage and bootstrap cache directories are writable:
```bash
chmod -R 775 storage bootstrap/cache
```

### Step 7: Configure Hostinger Scheduled Cron Job
In Hostinger **hPanel** &rarr; **Advanced** &rarr; **Cron Jobs**:
- Choose **Custom**.
- Set schedule to **Every minute** (`* * * * *`).
- Command:
```bash
cd /home/uXXXXXX/public_html && php artisan schedule:run >> /dev/null 2>&1
```
This handles queued email notifications, database queue jobs, and attendance midnight maintenance without needing supervisor or always-on background daemons.

### Step 8: Transition from Demo to Live Operations
When ready to begin real employee tracking:
1. Log in as Super Admin (`hr@intraeats.com`).
2. Navigate to **Settings** &rarr; **Hostinger & Maintenance**.
3. Click **Purge Demo Data**.
4. The system will delete all mock employees and attendance records, leaving the Super Admin credentials, Spatie RBAC roles, and company configuration intact for live operation.

---

## 6. Architecture & File Structure

```
├── app/
│   ├── Exports/               # Maatwebsite Excel & CSV export definitions
│   │   ├── AttendanceCsvExport.php
│   │   └── AttendanceReportExport.php (4 formatted sheets)
│   ├── Http/Controllers/
│   │   ├── Admin/             # Admin management suite controllers
│   │   │   ├── AdminUserController.php
│   │   │   ├── AttendanceController.php
│   │   │   ├── AuditController.php
│   │   │   ├── DashboardController.php
│   │   │   ├── EmployeeController.php
│   │   │   ├── ProjectController.php
│   │   │   ├── ReportController.php
│   │   │   └── SettingController.php
│   │   ├── Auth/              # 2-step OTP authentication controllers
│   │   │   └── AdminAuthController.php
│   │   └── EmployeePortalController.php  # Public kiosk check-in/out controller
│   ├── Imports/               # Bulk attendance file parser with error categorization
│   │   └── AttendanceImport.php
│   ├── Mail/                  # Mailable classes for OTP & attendance receipts
│   │   ├── AttendanceReceiptMail.php
│   │   └── OtpMail.php
│   ├── Models/                # Eloquent models with relations and scopes
│   │   ├── Attendance.php
│   │   ├── AuditLog.php
│   │   ├── Employee.php
│   │   ├── OtpVerification.php
│   │   ├── Project.php
│   │   ├── ProjectAssignment.php
│   │   ├── ProjectCategory.php
│   │   ├── Setting.php
│   │   └── User.php (with HasRoles)
│   └── Services/
│       └── AttendanceService.php  # Punctuality calculation & time formatting engine
├── database/
│   ├── migrations/            # System tables & Spatie RBAC migrations
│   └── seeders/               # 10 employees, 254 attendance records, 5 projects
├── public/build/              # Pre-compiled production assets (CSS, JS, manifest)
├── resources/
│   ├── css/app.css            # Tailwind CSS 3.4 base & typography
│   ├── js/app.js              # Alpine.js, Chart.js, Lucide icon initializers
│   └── views/
│       ├── admin/             # Dashboard, Attendance, Employees, Projects, Reports, Users, Settings, Audit
│       ├── auth/              # 2FA Login, OTP Verification, Password Reset
│       ├── emails/            # Email OTP and Attendance confirmation templates
│       ├── employee/          # Mobile-first punch portal
│       └── layouts/           # App and Admin layouts with live header clocks
└── tests/Feature/
    └── SystemSmokeTest.php    # 8 comprehensive automated end-to-end feature tests
```

---

## 7. License & Compliance
Built for **IntraEats & Talisha Software**. All rights reserved.
For operational assistance or technical inquiries, contact `hr@intraeats.com` or `support@talishasoftware.tech`.
