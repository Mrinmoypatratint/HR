@extends('layouts.admin')

@section('title', 'System Settings & Configuration')

@section('content')
<div class="space-y-6" x-data="{ tab: 'company', clearModal: false }">

    <!-- Top Action Header -->
    <div class="bg-white p-5 rounded-2xl border border-[#E2E8F0] shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-2xl font-extrabold text-[#1C1C1E] tracking-tight">System Settings & Setup</h1>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-[#F8FAFC] text-[#64748B] border border-[#CBD5E1]">v1.0 Production</span>
            </div>
            <p class="text-xs text-[#64748B] mt-0.5">Configure organization profiles, attendance policies, 2FA security parameters, and production data reset.</p>
        </div>

        @if($hasDemoData)
        <button type="button" @click="clearModal = true" class="px-4 py-2 rounded-xl bg-[#FEF2F2] hover:bg-[#FEE2E2] text-[#DC2626] border border-[#FECACA] text-xs font-bold transition-all flex items-center gap-1.5">
            <i data-lucide="trash-2" class="w-4 h-4"></i>
            <span>Clear Demo Data ({{ $demoStats['employees'] + $demoStats['attendances'] + $demoStats['projects'] }} items)</span>
        </button>
        @else
        <div class="px-3.5 py-1.5 rounded-xl bg-[#ECFDF5] text-[#059669] border border-[#A7F3D0] text-xs font-bold flex items-center gap-1.5">
            <i data-lucide="check-circle" class="w-4 h-4"></i>
            <span>Live Data Mode</span>
        </div>
        @endif
    </div>

    <!-- Navigation Tabs -->
    <div class="bg-white rounded-2xl border border-[#E2E8F0] p-1.5 shadow-sm flex flex-wrap gap-1">
        <button type="button" @click="tab = 'company'" 
                :class="tab === 'company' ? 'bg-[#FF6B1A] text-white shadow-sm' : 'text-[#64748B] hover:text-[#1C1C1E] hover:bg-[#F8FAFC]'"
                class="px-4 py-2.5 rounded-xl text-xs font-bold transition-all flex items-center gap-2">
            <i data-lucide="building-2" class="w-4 h-4"></i>
            <span>Company Profile</span>
        </button>

        <button type="button" @click="tab = 'attendance'" 
                :class="tab === 'attendance' ? 'bg-[#FF6B1A] text-white shadow-sm' : 'text-[#64748B] hover:text-[#1C1C1E] hover:bg-[#F8FAFC]'"
                class="px-4 py-2.5 rounded-xl text-xs font-bold transition-all flex items-center gap-2">
            <i data-lucide="clock" class="w-4 h-4"></i>
            <span>Attendance Policies</span>
        </button>

        <button type="button" @click="tab = 'security'" 
                :class="tab === 'security' ? 'bg-[#FF6B1A] text-white shadow-sm' : 'text-[#64748B] hover:text-[#1C1C1E] hover:bg-[#F8FAFC]'"
                class="px-4 py-2.5 rounded-xl text-xs font-bold transition-all flex items-center gap-2">
            <i data-lucide="shield-check" class="w-4 h-4"></i>
            <span>2FA & Security</span>
        </button>

        <button type="button" @click="tab = 'profile'" 
                :class="tab === 'profile' ? 'bg-[#FF6B1A] text-white shadow-sm' : 'text-[#64748B] hover:text-[#1C1C1E] hover:bg-[#F8FAFC]'"
                class="px-4 py-2.5 rounded-xl text-xs font-bold transition-all flex items-center gap-2">
            <i data-lucide="user" class="w-4 h-4"></i>
            <span>My Profile</span>
        </button>

        <button type="button" @click="tab = 'maintenance'" 
                :class="tab === 'maintenance' ? 'bg-[#FF6B1A] text-white shadow-sm' : 'text-[#64748B] hover:text-[#1C1C1E] hover:bg-[#F8FAFC]'"
                class="px-4 py-2.5 rounded-xl text-xs font-bold transition-all flex items-center gap-2">
            <i data-lucide="server" class="w-4 h-4"></i>
            <span>Hostinger & Maintenance</span>
        </button>
    </div>

    <!-- TAB 1: Company Profile -->
    <div x-show="tab === 'company'" class="bg-white p-6 rounded-2xl border border-[#E2E8F0] shadow-sm space-y-6">
        <div>
            <h3 class="text-base font-extrabold text-[#1C1C1E]">Organization & Corporate Information</h3>
            <p class="text-xs text-[#64748B]">These details appear on PDF exports, employee punch receipts, and email notifications.</p>
        </div>

        <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-4 text-xs">
            @csrf
            <input type="hidden" name="category" value="COMPANY">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-[#475569] uppercase tracking-wider mb-1">Company Legal Name</label>
                    <input type="text" name="company_name" value="{{ $settings['company_name'] ?? 'IntraEats & Talisha Software' }}" class="w-full px-3 py-2.5 rounded-xl bg-[#F8FAFC] border border-[#CBD5E1] text-[#1C1C1E] focus:border-[#FF6B1A] focus:ring-2 focus:ring-[#FF6B1A]/20">
                </div>

                <div>
                    <label class="block font-bold text-[#475569] uppercase tracking-wider mb-1">Company Tagline / Subtitle</label>
                    <input type="text" name="company_tagline" value="{{ $settings['company_tagline'] ?? 'Food-Tech Logistics & Digital Enterprise' }}" class="w-full px-3 py-2.5 rounded-xl bg-[#F8FAFC] border border-[#CBD5E1] text-[#1C1C1E] focus:border-[#FF6B1A] focus:ring-2 focus:ring-[#FF6B1A]/20">
                </div>

                <div>
                    <label class="block font-bold text-[#475569] uppercase tracking-wider mb-1">Official HR Admin Email</label>
                    <input type="email" name="hr_email" value="{{ $settings['hr_email'] ?? 'hr@intraeats.com' }}" class="w-full px-3 py-2.5 rounded-xl bg-[#F8FAFC] border border-[#CBD5E1] text-[#1C1C1E] focus:border-[#FF6B1A] focus:ring-2 focus:ring-[#FF6B1A]/20">
                </div>

                <div>
                    <label class="block font-bold text-[#475569] uppercase tracking-wider mb-1">Technical Support Email</label>
                    <input type="email" name="support_email" value="{{ $settings['support_email'] ?? 'support@talishasoftware.tech' }}" class="w-full px-3 py-2.5 rounded-xl bg-[#F8FAFC] border border-[#CBD5E1] text-[#1C1C1E] focus:border-[#FF6B1A] focus:ring-2 focus:ring-[#FF6B1A]/20">
                </div>

                <div>
                    <label class="block font-bold text-[#475569] uppercase tracking-wider mb-1">System Timezone</label>
                    <select name="system_timezone" class="w-full px-3 py-2.5 rounded-xl bg-[#F8FAFC] border border-[#CBD5E1] text-[#1C1C1E] focus:border-[#FF6B1A] focus:ring-2 focus:ring-[#FF6B1A]/20">
                        <option value="Asia/Kolkata" {{ ($settings['system_timezone'] ?? 'Asia/Kolkata') === 'Asia/Kolkata' ? 'selected' : '' }}>Asia/Kolkata (IST +05:30) - Default</option>
                        <option value="UTC" {{ ($settings['system_timezone'] ?? '') === 'UTC' ? 'selected' : '' }}>UTC (+00:00)</option>
                        <option value="Asia/Dubai" {{ ($settings['system_timezone'] ?? '') === 'Asia/Dubai' ? 'selected' : '' }}>Asia/Dubai (GST +04:00)</option>
                        <option value="Asia/Singapore" {{ ($settings['system_timezone'] ?? '') === 'Asia/Singapore' ? 'selected' : '' }}>Asia/Singapore (SGT +08:00)</option>
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-[#475569] uppercase tracking-wider mb-1">Standard Work Week</label>
                    <input type="text" name="working_days" value="{{ $settings['working_days'] ?? 'Monday to Saturday' }}" class="w-full px-3 py-2.5 rounded-xl bg-[#F8FAFC] border border-[#CBD5E1] text-[#1C1C1E] focus:border-[#FF6B1A] focus:ring-2 focus:ring-[#FF6B1A]/20">
                </div>
            </div>

            <div>
                <label class="block font-bold text-[#475569] uppercase tracking-wider mb-1">Corporate Office Address</label>
                <textarea name="company_address" rows="2" class="w-full px-3 py-2 rounded-xl bg-[#F8FAFC] border border-[#CBD5E1] text-[#1C1C1E] focus:border-[#FF6B1A] focus:ring-2 focus:ring-[#FF6B1A]/20">{{ $settings['company_address'] ?? 'Tech Park Campus, Outer Ring Road, Bangalore, Karnataka 560103' }}</textarea>
            </div>

            <div class="flex justify-end pt-3 border-t border-[#F1F5F9]">
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#FF6B1A] hover:bg-[#E55607] text-white font-bold shadow-md shadow-[#FF6B1A]/20 transition-all">
                    Save Company Profile
                </button>
            </div>
        </form>
    </div>

    <!-- TAB 2: Attendance Policies -->
    <div x-show="tab === 'attendance'" class="bg-white p-6 rounded-2xl border border-[#E2E8F0] shadow-sm space-y-6">
        <div>
            <h3 class="text-base font-extrabold text-[#1C1C1E]">Attendance Rules, Shifts & Calculation Policies</h3>
            <p class="text-xs text-[#64748B]">These parameters dictate automatic late arrival marking, half-day qualifications, and punch limits.</p>
        </div>

        <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-4 text-xs">
            @csrf
            <input type="hidden" name="category" value="ATTENDANCE">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block font-bold text-[#475569] uppercase tracking-wider mb-1">Shift Start Time (HH:MM)</label>
                    <input type="time" name="work_start_time" value="{{ $settings['work_start_time'] ?? '09:30' }}" class="w-full px-3 py-2.5 rounded-xl bg-[#F8FAFC] border border-[#CBD5E1] text-[#1C1C1E] focus:border-[#FF6B1A] focus:ring-2 focus:ring-[#FF6B1A]/20">
                </div>

                <div>
                    <label class="block font-bold text-[#475569] uppercase tracking-wider mb-1">Shift End Time (HH:MM)</label>
                    <input type="time" name="work_end_time" value="{{ $settings['work_end_time'] ?? '18:30' }}" class="w-full px-3 py-2.5 rounded-xl bg-[#F8FAFC] border border-[#CBD5E1] text-[#1C1C1E] focus:border-[#FF6B1A] focus:ring-2 focus:ring-[#FF6B1A]/20">
                </div>

                <div>
                    <label class="block font-bold text-[#475569] uppercase tracking-wider mb-1">Grace Period (Minutes)</label>
                    <input type="number" name="grace_period_mins" value="{{ $settings['grace_period_mins'] ?? '15' }}" min="0" max="60" class="w-full px-3 py-2.5 rounded-xl bg-[#F8FAFC] border border-[#CBD5E1] text-[#1C1C1E] focus:border-[#FF6B1A] focus:ring-2 focus:ring-[#FF6B1A]/20">
                    <span class="text-[10px] text-[#64748B] mt-1 block">Punches after Shift Start + Grace are marked LATE.</span>
                </div>

                <div>
                    <label class="block font-bold text-[#475569] uppercase tracking-wider mb-1">Full-Day Hours Threshold</label>
                    <input type="number" step="0.5" name="full_day_hours" value="{{ $settings['full_day_hours'] ?? '8.0' }}" class="w-full px-3 py-2.5 rounded-xl bg-[#F8FAFC] border border-[#CBD5E1] text-[#1C1C1E] focus:border-[#FF6B1A] focus:ring-2 focus:ring-[#FF6B1A]/20">
                </div>

                <div>
                    <label class="block font-bold text-[#475569] uppercase tracking-wider mb-1">Half-Day Hours Threshold</label>
                    <input type="number" step="0.5" name="half_day_hours" value="{{ $settings['half_day_hours'] ?? '4.5' }}" class="w-full px-3 py-2.5 rounded-xl bg-[#F8FAFC] border border-[#CBD5E1] text-[#1C1C1E] focus:border-[#FF6B1A] focus:ring-2 focus:ring-[#FF6B1A]/20">
                </div>

                <div>
                    <label class="block font-bold text-[#475569] uppercase tracking-wider mb-1">Allow Remote / WFH Punches</label>
                    <select name="allow_remote_checkin" class="w-full px-3 py-2.5 rounded-xl bg-[#F8FAFC] border border-[#CBD5E1] text-[#1C1C1E] focus:border-[#FF6B1A] focus:ring-2 focus:ring-[#FF6B1A]/20">
                        <option value="yes" {{ ($settings['allow_remote_checkin'] ?? 'yes') === 'yes' ? 'selected' : '' }}>Yes - Allow Remote Punches</option>
                        <option value="no" {{ ($settings['allow_remote_checkin'] ?? '') === 'no' ? 'selected' : '' }}>No - Office Network Only</option>
                    </select>
                </div>
            </div>

            <div class="flex justify-end pt-3 border-t border-[#F1F5F9]">
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#FF6B1A] hover:bg-[#E55607] text-white font-bold shadow-md shadow-[#FF6B1A]/20 transition-all">
                    Save Attendance Policies
                </button>
            </div>
        </form>
    </div>

    <!-- TAB 3: 2FA & Security -->
    <div x-show="tab === 'security'" class="bg-white p-6 rounded-2xl border border-[#E2E8F0] shadow-sm space-y-6">
        <div>
            <h3 class="text-base font-extrabold text-[#1C1C1E]">Two-Factor Authentication & Access Security</h3>
            <p class="text-xs text-[#64748B]">All back-office admin accounts are guarded by mandatory 6-digit email OTPs sent via SMTP.</p>
        </div>

        <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-4 text-xs">
            @csrf
            <input type="hidden" name="category" value="SECURITY">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-[#475569] uppercase tracking-wider mb-1">Enforce Admin Email OTP</label>
                    <select name="require_admin_otp" class="w-full px-3 py-2.5 rounded-xl bg-[#F8FAFC] border border-[#CBD5E1] text-[#1C1C1E] focus:border-[#FF6B1A] focus:ring-2 focus:ring-[#FF6B1A]/20">
                        <option value="yes" {{ ($settings['require_admin_otp'] ?? 'yes') === 'yes' ? 'selected' : '' }}>Mandatory (Always Send OTP)</option>
                        <option value="no" {{ ($settings['require_admin_otp'] ?? '') === 'no' ? 'selected' : '' }}>Optional (Password Only)</option>
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-[#475569] uppercase tracking-wider mb-1">OTP Validity Duration (Minutes)</label>
                    <input type="number" name="otp_expiry_mins" value="{{ $settings['otp_expiry_mins'] ?? '10' }}" min="2" max="60" class="w-full px-3 py-2.5 rounded-xl bg-[#F8FAFC] border border-[#CBD5E1] text-[#1C1C1E] focus:border-[#FF6B1A] focus:ring-2 focus:ring-[#FF6B1A]/20">
                </div>

                <div>
                    <label class="block font-bold text-[#475569] uppercase tracking-wider mb-1">Maximum OTP Verification Attempts</label>
                    <input type="number" name="max_otp_attempts" value="{{ $settings['max_otp_attempts'] ?? '5' }}" min="3" max="10" class="w-full px-3 py-2.5 rounded-xl bg-[#F8FAFC] border border-[#CBD5E1] text-[#1C1C1E] focus:border-[#FF6B1A] focus:ring-2 focus:ring-[#FF6B1A]/20">
                </div>

                <div>
                    <label class="block font-bold text-[#475569] uppercase tracking-wider mb-1">Admin Session Inactivity Timeout (Minutes)</label>
                    <input type="number" name="session_lifetime" value="{{ $settings['session_lifetime'] ?? '120' }}" min="15" max="720" class="w-full px-3 py-2.5 rounded-xl bg-[#F8FAFC] border border-[#CBD5E1] text-[#1C1C1E] focus:border-[#FF6B1A] focus:ring-2 focus:ring-[#FF6B1A]/20">
                </div>
            </div>

            <div class="flex justify-end pt-3 border-t border-[#F1F5F9]">
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#FF6B1A] hover:bg-[#E55607] text-white font-bold shadow-md shadow-[#FF6B1A]/20 transition-all">
                    Save Security Settings
                </button>
            </div>
        </form>
    </div>

    <!-- TAB 4: My Profile -->
    <div x-show="tab === 'profile'" class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Profile Details -->
        <div class="bg-white p-6 rounded-2xl border border-[#E2E8F0] shadow-sm space-y-4">
            <h3 class="text-base font-extrabold text-[#1C1C1E]">My Admin Profile Details</h3>

            <form method="POST" action="{{ route('admin.profile.update') }}" class="space-y-4 text-xs">
                @csrf

                <div>
                    <label class="block font-bold text-[#475569] uppercase tracking-wider mb-1">Full Name</label>
                    <input type="text" name="name" value="{{ $user->name }}" required class="w-full px-3 py-2.5 rounded-xl bg-[#F8FAFC] border border-[#CBD5E1] text-[#1C1C1E] focus:border-[#FF6B1A] focus:ring-2 focus:ring-[#FF6B1A]/20">
                </div>

                <div>
                    <label class="block font-bold text-[#475569] uppercase tracking-wider mb-1">Email Address (Read-only)</label>
                    <input type="email" value="{{ $user->email }}" disabled class="w-full px-3 py-2.5 rounded-xl bg-[#F1F5F9] border border-[#CBD5E1] text-[#64748B] cursor-not-allowed">
                </div>

                <div>
                    <label class="block font-bold text-[#475569] uppercase tracking-wider mb-1">Mobile Phone</label>
                    <input type="text" name="mobile" value="{{ $user->mobile }}" placeholder="+91 98765 43210" class="w-full px-3 py-2.5 rounded-xl bg-[#F8FAFC] border border-[#CBD5E1] text-[#1C1C1E] focus:border-[#FF6B1A] focus:ring-2 focus:ring-[#FF6B1A]/20">
                </div>

                <div>
                    <label class="block font-bold text-[#475569] uppercase tracking-wider mb-1">Avatar Image URL</label>
                    <input type="url" name="avatar_url" value="{{ $user->avatar_url }}" class="w-full px-3 py-2.5 rounded-xl bg-[#F8FAFC] border border-[#CBD5E1] text-[#1C1C1E] focus:border-[#FF6B1A] focus:ring-2 focus:ring-[#FF6B1A]/20">
                </div>

                <div class="flex justify-end pt-2">
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#FF6B1A] hover:bg-[#E55607] text-white font-bold shadow-md shadow-[#FF6B1A]/20 transition-all">
                        Update Profile
                    </button>
                </div>
            </form>
        </div>

        <!-- Password Change -->
        <div class="bg-white p-6 rounded-2xl border border-[#E2E8F0] shadow-sm space-y-4">
            <h3 class="text-base font-extrabold text-[#1C1C1E]">Change Password</h3>

            <form method="POST" action="{{ route('admin.password.update') }}" class="space-y-4 text-xs">
                @csrf

                <div>
                    <label class="block font-bold text-[#475569] uppercase tracking-wider mb-1">Current Password *</label>
                    <input type="password" name="current_password" required class="w-full px-3 py-2.5 rounded-xl bg-[#F8FAFC] border border-[#CBD5E1] text-[#1C1C1E] focus:border-[#FF6B1A] focus:ring-2 focus:ring-[#FF6B1A]/20">
                </div>

                <div>
                    <label class="block font-bold text-[#475569] uppercase tracking-wider mb-1">New Password * (Min 8 chars, 1 uppercase, 1 symbol)</label>
                    <input type="password" name="password" required class="w-full px-3 py-2.5 rounded-xl bg-[#F8FAFC] border border-[#CBD5E1] text-[#1C1C1E] focus:border-[#FF6B1A] focus:ring-2 focus:ring-[#FF6B1A]/20">
                </div>

                <div>
                    <label class="block font-bold text-[#475569] uppercase tracking-wider mb-1">Confirm New Password *</label>
                    <input type="password" name="password_confirmation" required class="w-full px-3 py-2.5 rounded-xl bg-[#F8FAFC] border border-[#CBD5E1] text-[#1C1C1E] focus:border-[#FF6B1A] focus:ring-2 focus:ring-[#FF6B1A]/20">
                </div>

                <div class="flex justify-end pt-2">
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#1E293B] hover:bg-[#0F172A] text-white font-bold shadow-md transition-all">
                        Change Password
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- TAB 5: Hostinger & Maintenance -->
    <div x-show="tab === 'maintenance'" class="space-y-6">
        <!-- Clear Demo Data Card -->
        <div class="bg-white p-6 rounded-2xl border border-[#FECACA] shadow-sm space-y-4">
            <div class="flex items-start justify-between gap-4">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-[#FEF2F2] text-[#DC2626] flex items-center justify-center shrink-0">
                        <i data-lucide="alert-triangle" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-black text-[#1C1C1E]">Production Transition: Clear Demo Mock Data</h3>
                        <p class="text-xs text-[#64748B] mt-0.5">
                            Purge all sample seeded employees, mock attendance logs, and sample projects before enrolling real employees. 
                            <strong>Admin credentials, Spatie RBAC roles, and system settings will remain completely intact.</strong>
                        </p>
                    </div>
                </div>

                <div class="shrink-0">
                    @if($hasDemoData)
                        <button type="button" @click="clearModal = true" class="px-4 py-2.5 rounded-xl bg-[#DC2626] hover:bg-[#B91C1C] text-white text-xs font-bold shadow-md shadow-[#DC2626]/20 transition-all flex items-center gap-1.5">
                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                            <span>Purge Demo Data</span>
                        </button>
                    @else
                        <span class="px-3 py-1.5 rounded-xl bg-[#ECFDF5] text-[#059669] border border-[#A7F3D0] text-xs font-bold">
                            Clean Live Database
                        </span>
                    @endif
                </div>
            </div>

            <!-- Current Mock Stats Grid -->
            <div class="grid grid-cols-3 gap-3 pt-2 text-center text-xs">
                <div class="p-3 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0]">
                    <span class="text-[10px] uppercase font-bold text-[#64748B]">Demo Employees</span>
                    <p class="text-lg font-extrabold text-[#1C1C1E]">{{ $demoStats['employees'] }}</p>
                </div>
                <div class="p-3 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0]">
                    <span class="text-[10px] uppercase font-bold text-[#64748B]">Demo Attendance Logs</span>
                    <p class="text-lg font-extrabold text-[#1C1C1E]">{{ $demoStats['attendances'] }}</p>
                </div>
                <div class="p-3 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0]">
                    <span class="text-[10px] uppercase font-bold text-[#64748B]">Demo Projects</span>
                    <p class="text-lg font-extrabold text-[#1C1C1E]">{{ $demoStats['projects'] }}</p>
                </div>
            </div>
        </div>

        <!-- Hostinger Deployment Diagnostics -->
        <div class="bg-white p-6 rounded-2xl border border-[#E2E8F0] shadow-sm space-y-4">
            <h3 class="text-base font-extrabold text-[#1C1C1E] flex items-center gap-2">
                <i data-lucide="server" class="w-4 h-4 text-[#FF6B1A]"></i>
                <span>Hostinger Shared Hosting & Cron Environment</span>
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                <div class="p-4 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0] space-y-2">
                    <span class="font-bold text-[#1C1C1E] uppercase text-[11px] block">Runtime Diagnostics</span>
                    <div class="space-y-1 text-[#475569]">
                        <p><strong>PHP Version:</strong> {{ phpversion() }} (Required: 8.2+)</p>
                        <p><strong>Laravel Version:</strong> {{ app()->version() }}</p>
                        <p><strong>Active Timezone:</strong> {{ config('app.timezone') }}</p>
                        <p><strong>Database Driver:</strong> {{ config('database.default') }} (MySQL 8 / MariaDB ready)</p>
                        <p><strong>Pre-built Assets:</strong> Stored in <code class="font-mono bg-white px-1 py-0.5 rounded border border-[#CBD5E1]">public/build</code> (Zero Node needed in Hostinger)</p>
                    </div>
                </div>

                <div class="p-4 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0] space-y-2">
                    <span class="font-bold text-[#1C1C1E] uppercase text-[11px] block">Hostinger cPanel Cron Job Configuration</span>
                    <p class="text-[#64748B]">Set up this cron job in your Hostinger cPanel / hPanel every minute:</p>
                    <div class="p-2.5 rounded-lg bg-[#1E293B] text-[#38BDF8] font-mono text-[11px] select-all overflow-x-auto">
                        * * * * * cd /home/uXXXXXX/public_html && php artisan schedule:run >> /dev/null 2>&1
                    </div>
                    <span class="text-[10px] text-[#94A3B8]">Runs database queue jobs, automatic OTP cleanups, and attendance checks without daemon workers.</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal: Confirmation to Clear Demo Data -->
    <div x-show="clearModal" 
         x-cloak 
         class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="clearModal = false" class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-[#FECACA] space-y-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-[#FEF2F2] text-[#DC2626] flex items-center justify-center shrink-0">
                    <i data-lucide="alert-triangle" class="w-5 h-5"></i>
                </div>
                <div>
                    <h3 class="font-black text-base text-[#1C1C1E]">Clear All Demo Data?</h3>
                    <p class="text-xs text-[#DC2626] font-semibold">This action cannot be undone.</p>
                </div>
            </div>

            <p class="text-xs text-[#64748B] leading-relaxed">
                This will delete <strong>{{ $demoStats['employees'] }} sample employees</strong>, <strong>{{ $demoStats['attendances'] }} attendance records</strong>, and <strong>{{ $demoStats['projects'] }} mock projects</strong>. 
                Your Admin credentials (<code class="text-[#1C1C1E] font-mono">hr@intraeats.com</code>) and settings will stay untouched.
            </p>

            <form method="POST" action="{{ route('admin.clear-demo') }}" class="pt-2 flex items-center justify-end gap-2">
                @csrf
                <button type="button" @click="clearModal = false" class="px-4 py-2 rounded-xl border border-[#CBD5E1] font-bold text-xs text-[#64748B] hover:bg-[#F8FAFC]">
                    Cancel
                </button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-[#DC2626] hover:bg-[#B91C1C] font-bold text-xs text-white shadow-md shadow-[#DC2626]/20">
                    Yes, Purge Demo Data
                </button>
            </form>
        </div>
    </div>

</div>
@endsection
