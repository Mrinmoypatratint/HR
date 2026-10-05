@extends('layouts.app')

@section('title', 'IntraEats — Employee Attendance Portal')

@section('content')
<div class="min-h-screen flex flex-col justify-between bg-[#FAFAF8]" x-data="employeeAttendancePortal()">
    <!-- Header Navigation -->
    <header class="w-full bg-white border-b border-[#E2E8F0] shadow-sm sticky top-0 z-40">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-[#FF6B1A] flex items-center justify-center text-white shadow-sm font-extrabold text-lg">
                    <i data-lucide="utensils" class="w-5 h-5"></i>
                </div>
                <div class="flex flex-col">
                    <span class="font-extrabold text-lg text-[#1C1C1E] leading-tight tracking-tight">IntraEats</span>
                    <span class="text-[11px] font-bold text-[#FF6B1A] uppercase tracking-wider">Employee Attendance Portal</span>
                </div>
            </div>

            <nav class="flex items-center gap-2 sm:gap-4">
                <a href="#home" class="px-3 py-1.5 rounded-lg text-xs font-semibold text-[#1C1C1E] hover:bg-[#F1F5F9]">Home</a>
                <a href="#attendance" class="px-3 py-1.5 rounded-lg text-xs font-bold text-[#FF6B1A] bg-[#FFF3EB]">Attendance</a>
                <button @click="helpModal = true" class="px-3 py-1.5 rounded-lg text-xs font-semibold text-[#64748B] hover:bg-[#F1F5F9]">Help</button>
                <a href="{{ route('employee.login') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-[#FF6B1A] text-white text-xs font-bold hover:bg-[#E55607] transition-all shadow-sm">
                    <i data-lucide="log-in" class="w-3.5 h-3.5"></i>
                    <span>Employee Login</span>
                </a>
                <a href="{{ route('admin.login') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white border border-[#E2E8F0] text-xs font-bold text-[#1C1C1E] hover:border-[#FF6B1A] hover:text-[#FF6B1A] transition-all shadow-sm">
                    <i data-lucide="shield" class="w-3.5 h-3.5 text-[#FF6B1A]"></i>
                    <span>Admin Login</span>
                </a>
            </nav>
        </div>
    </header>

    <!-- Main Attendance Kiosk Container -->
    <main class="flex-1 max-w-4xl w-full mx-auto px-4 py-8 sm:py-12">
        <div class="bg-white rounded-2xl border border-[#E2E8F0] shadow-xl overflow-hidden">
            <!-- Terminal Header Banner -->
            <div class="bg-gradient-to-r from-[#1C1C1E] to-[#2E2E33] text-white p-6 sm:p-8 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#16A34A]/20 border border-[#16A34A]/40 text-[#4ADE80] text-xs font-bold uppercase tracking-wider mb-2">
                        <span class="w-2 h-2 rounded-full bg-[#4ADE80] animate-pulse"></span>
                        SYSTEM SERVER TIME SYNCHRONIZED
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white">Mark Your Attendance</h1>
                    <p class="text-xs sm:text-sm text-[#D4D4D8] mt-1">Geo-validated bi-directional workforce punch module for food &amp; logistics operations.</p>
                </div>

                <!-- Big Live Clock Widget -->
                <div class="bg-[#121214] border border-[#3E3E46] p-4 rounded-xl shadow-inner flex flex-col md:items-end min-w-[240px]">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-[#A1A1AA] flex items-center gap-1.5">
                        <i data-lucide="clock" class="w-3.5 h-3.5 text-[#FF6B1A]"></i>
                        Live Precision Clock
                    </span>
                    <div class="font-mono text-xl sm:text-2xl font-bold text-white tracking-tight mt-1" x-text="liveClock">
                        {{ $currentTime }}
                    </div>
                    <span class="text-[11px] text-[#4ADE80] font-semibold flex items-center gap-1 mt-0.5">
                        <i data-lucide="check" class="w-3 h-3"></i> IST (UTC +05:30) Active
                    </span>
                </div>
            </div>

            <!-- Portal Interaction Form -->
            <div class="p-6 sm:p-8 space-y-6">

                <!-- Verification Error Alert -->
                <template x-if="errorMessage">
                    <div class="p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 flex items-start gap-3">
                        <i data-lucide="alert-circle" class="w-5 h-5 text-red-600 shrink-0 mt-0.5"></i>
                        <div class="flex-1">
                            <h4 class="font-bold text-sm text-red-800">Verification Error</h4>
                            <p class="text-xs mt-0.5" x-text="errorMessage"></p>
                        </div>
                        <button @click="errorMessage = ''" class="text-red-600 hover:text-red-900">
                            <i data-lucide="x" class="w-4 h-4"></i>
                        </button>
                    </div>
                </template>

                <!-- Duplicate Check-in Notice / Check-out Panel -->
                <template x-if="isDuplicate">
                    <div class="p-5 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 space-y-3">
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-full bg-amber-500 text-white flex items-center justify-center shrink-0 shadow-sm">
                                <i data-lucide="clock" class="w-5 h-5"></i>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center gap-2">
                                    <h4 class="font-bold text-base text-amber-900">Attendance Already Marked Today</h4>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-200 text-amber-800 uppercase">Active Shift</span>
                                </div>
                                <p class="text-xs text-amber-800 mt-1 leading-relaxed">
                                    Check-in recorded at <strong class="text-amber-950 font-bold" x-text="todayAttendance?.check_in_time"></strong>
                                    for Project <span class="font-semibold" x-text="'[' + (todayAttendance?.work_category || 'General') + ']'"></span>.
                                    <template x-if="todayAttendance?.check_out_time">
                                        <span>Check-out was completed at <strong class="text-amber-950" x-text="todayAttendance?.check_out_time"></strong> (Duration: <span x-text="todayAttendance?.working_hours_formatted"></span>).</span>
                                    </template>
                                </p>
                            </div>
                        </div>

                        <!-- One-Click Check-out Button (if not yet checked out) -->
                        <template x-if="!todayAttendance?.check_out_time">
                            <div class="pt-2 flex flex-col sm:flex-row items-center justify-between gap-3 border-t border-amber-200/60">
                                <span class="text-xs text-amber-800 font-medium">Ready to end your shift? Punch out to auto-calculate your working hours.</span>
                                <button type="button" @click="submitPunch('checkout')" :disabled="submitting" class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-[#1C1C1E] hover:bg-black text-white font-bold text-xs uppercase tracking-wider flex items-center justify-center gap-2 shadow-md transition-all">
                                    <i data-lucide="log-out" class="w-4 h-4 text-[#FF6B1A]"></i>
                                    <span x-text="submitting ? 'Recording Check-out...' : 'Punch Check-Out Now'"></span>
                                </button>
                            </div>
                        </template>
                    </div>
                </template>

                <!-- STEP 1: Enter Employee Code -->
                <div class="bg-[#F8FAFC] border border-[#E2E8F0] p-6 rounded-2xl space-y-4">
                    <div class="flex items-center justify-between">
                        <label class="font-bold text-sm text-[#1C1C1E] flex items-center gap-2">
                            <span class="w-6 h-6 rounded-full bg-[#FF6B1A] text-white flex items-center justify-center text-xs font-bold">1</span>
                            <span>Enter Employee Code to Continue</span>
                        </label>
                        <span class="text-xs text-[#64748B]">Step 1 of 2: ID Match</span>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-3">
                        <div class="relative flex-1">
                            <div class="absolute left-3.5 top-1/2 -translate-y-1/2 text-[#94A3B8]">
                                <i data-lucide="badge-check" class="w-5 h-5"></i>
                            </div>
                            <input type="text" x-model="employeeCode" @keydown.enter.prevent="verifyEmployee()" placeholder="Enter Employee Code (e.g. INTRA-EMP-001)" class="w-full bg-white text-[#1C1C1E] font-mono text-sm pl-11 pr-4 py-3 rounded-xl border border-[#CBD5E1] shadow-sm focus:border-[#FF6B1A] focus:ring-2 focus:ring-[#FF6B1A]/20 uppercase">
                        </div>

                        <button type="button" @click="verifyEmployee()" :disabled="verifying" class="px-6 py-3 rounded-xl bg-[#FF6B1A] hover:bg-[#E55607] text-white font-bold text-sm shadow-md hover:shadow-lg transition-all flex items-center justify-center gap-2 shrink-0 disabled:opacity-50">
                            <i data-lucide="search" class="w-4 h-4"></i>
                            <span x-text="verifying ? 'Verifying...' : 'Continue'"></span>
                        </button>
                    </div>

                    <!-- Example Hints Pill -->
                    <div class="flex items-center gap-2 text-xs text-[#64748B] pt-1">
                        <span>Try demo codes:</span>
                        <button type="button" @click="employeeCode = 'INTRA-EMP-001'; verifyEmployee()" class="font-mono text-[#FF6B1A] font-semibold hover:underline">INTRA-EMP-001</button>,
                        <button type="button" @click="employeeCode = 'INTRA-EMP-002'; verifyEmployee()" class="font-mono text-[#FF6B1A] font-semibold hover:underline">INTRA-EMP-002</button>,
                        <button type="button" @click="employeeCode = 'INTRA-EMP-003'; verifyEmployee()" class="font-mono text-[#FF6B1A] font-semibold hover:underline">INTRA-EMP-003</button>
                    </div>

                    <!-- Digital ID Card (Appears upon verification) -->
                    <template x-if="verified && employee">
                        <div class="mt-4 pt-4 border-t border-[#E2E8F0] space-y-4">
                            <div class="p-4 rounded-xl bg-white border border-[#E2E8F0] shadow-sm flex flex-col sm:flex-row items-center sm:items-start justify-between gap-4">
                                <div class="flex items-center gap-4">
                                    <template x-if="employee.photo_url">
                                        <img :src="employee.photo_url" class="w-16 h-16 rounded-2xl object-cover ring-2 ring-[#FF6B1A]/20 shadow" alt="ID Photo">
                                    </template>
                                    <template x-if="!employee.photo_url">
                                        <div class="w-16 h-16 rounded-2xl bg-[#FF6B1A] text-white flex items-center justify-center font-extrabold text-xl shadow" x-text="employee.initials"></div>
                                    </template>
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <h3 class="font-extrabold text-base text-[#1C1C1E]" x-text="employee.full_name"></h3>
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-[#ECFDF5] text-[#059669] border border-[#A7F3D0]">
                                                <i data-lucide="check" class="w-3 h-3"></i> Employee Verified
                                            </span>
                                        </div>
                                        <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-[#64748B] mt-1 font-mono">
                                            <span>ID: <strong class="text-[#1C1C1E]" x-text="employee.employee_id"></strong></span>
                                            <span>&bull;</span>
                                            <span>Code: <strong class="text-[#FF6B1A]" x-text="employee.employee_code"></strong></span>
                                            <span>&bull;</span>
                                            <span>Dept: <strong class="text-[#1C1C1E]" x-text="employee.department"></strong></span>
                                        </div>
                                        <div class="text-xs text-[#64748B] mt-1">
                                            Role: <strong class="text-[#1C1C1E]" x-text="employee.role"></strong> &bull;
                                            Designation: <span x-text="employee.designation"></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="text-right sm:text-right shrink-0">
                                    <span class="text-[10px] uppercase font-bold text-[#94A3B8]">Joined Date</span>
                                    <p class="text-xs font-semibold text-[#1C1C1E]" x-text="employee.joining_date"></p>
                                    <span class="inline-block mt-1 px-2 py-0.5 rounded text-[10px] font-bold bg-[#F1F5F9] text-[#475569] uppercase" x-text="employee.employment_type"></span>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- STEP 2: Work Context & Punch Submission (Active when verified) -->
                <div x-show="verified && !isDuplicate" x-cloak class="bg-white border border-[#E2E8F0] p-6 rounded-2xl space-y-5">
                    <div class="flex items-center justify-between">
                        <label class="font-bold text-sm text-[#1C1C1E] flex items-center gap-2">
                            <span class="w-6 h-6 rounded-full bg-[#16A34A] text-white flex items-center justify-center text-xs font-bold">2</span>
                            <span>What are you working on today? (Required)</span>
                        </label>
                        <span class="text-xs text-[#64748B]">Step 2 of 2: Shift Context</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Category Dropdown -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-[#64748B] mb-1.5">Project Category</label>
                            <select x-model="workCategory" class="w-full bg-[#F8FAFC] border border-[#CBD5E1] text-[#1C1C1E] text-sm rounded-xl px-4 py-2.5 focus:border-[#FF6B1A] focus:ring-2 focus:ring-[#FF6B1A]/20">
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->name }}" {{ $cat->is_default ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Current Time Auto-captured -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-[#64748B] mb-1.5">Punch Timestamp (Auto-captured)</label>
                            <div class="w-full bg-[#F1F5F9] border border-[#E2E8F0] text-[#1E293B] font-mono text-sm rounded-xl px-4 py-2.5 flex items-center justify-between">
                                <span x-text="liveClock">{{ $currentTime }}</span>
                                <span class="text-[10px] font-bold text-[#16A34A] uppercase bg-[#ECFDF5] px-2 py-0.5 rounded">Server Locked</span>
                            </div>
                        </div>
                    </div>

                    <!-- Task Description Field -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-[#64748B] mb-1.5">Task Description &amp; Details *</label>
                        <textarea x-model="taskDescription" rows="2" placeholder="e.g. Completed KOT real-time socket connection, tested billing printout, sprint review." class="w-full bg-[#F8FAFC] border border-[#CBD5E1] text-[#1C1C1E] text-sm rounded-xl p-3 focus:border-[#FF6B1A] focus:ring-2 focus:ring-[#FF6B1A]/20"></textarea>
                    </div>

                    <!-- Submit / Punch Button -->
                    <button type="button" @click="openConfirmModal()" :disabled="submitting || !taskDescription.trim()" class="w-full py-4 rounded-xl bg-[#16A34A] hover:bg-[#15803D] text-white font-extrabold text-base tracking-wide flex items-center justify-center gap-2 shadow-lg shadow-[#16A34A]/20 hover:shadow-xl transition-all disabled:opacity-50">
                        <i data-lucide="check-circle" class="w-5 h-5"></i>
                        <span>Mark Attendance</span>
                    </button>
                </div>
            </div>
        </div>
    </main>

    <!-- Confirmation Modal -->
    <div x-show="confirmModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div @click="confirmModal = false" class="fixed inset-0 bg-black/60 backdrop-blur-sm"></div>
        <div class="relative w-full max-w-md bg-white rounded-2xl shadow-2xl border border-[#E2E8F0] p-6 z-10 space-y-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-[#FFF3EB] text-[#FF6B1A] flex items-center justify-center">
                    <i data-lucide="help-circle" class="w-6 h-6"></i>
                </div>
                <div>
                    <h3 class="font-extrabold text-base text-[#1C1C1E]">Confirm Attendance Punch</h3>
                    <p class="text-xs text-[#64748B]">Please verify your details before confirming.</p>
                </div>
            </div>

            <div class="bg-[#F8FAFC] rounded-xl p-4 border border-[#E2E8F0] space-y-2 text-xs">
                <div class="flex justify-between"><span class="text-[#64748B]">Employee:</span><strong class="text-[#1C1C1E]" x-text="employee?.full_name"></strong></div>
                <div class="flex justify-between"><span class="text-[#64748B]">Employee Code:</span><strong class="text-[#FF6B1A] font-mono" x-text="employee?.employee_code"></strong></div>
                <div class="flex justify-between"><span class="text-[#64748B]">Current Date:</span><strong class="text-[#1C1C1E]" x-text="currentDate"></strong></div>
                <div class="flex justify-between"><span class="text-[#64748B]">Punch Time:</span><strong class="text-[#16A34A] font-mono" x-text="liveClock"></strong></div>
                <div class="flex justify-between"><span class="text-[#64748B]">Category:</span><strong class="text-[#1C1C1E]" x-text="workCategory"></strong></div>
                <div class="border-t border-[#E2E8F0] pt-2">
                    <span class="text-[#64748B] block mb-1">Task Summary:</span>
                    <p class="text-[#1E293B] italic" x-text="taskDescription"></p>
                </div>
            </div>

            <div class="flex gap-3">
                <button type="button" @click="confirmModal = false" class="flex-1 py-2.5 rounded-xl border border-[#CBD5E1] text-[#64748B] font-bold text-xs hover:bg-[#F8FAFC]">Cancel</button>
                <button type="button" @click="submitPunch('checkin')" class="flex-1 py-2.5 rounded-xl bg-[#16A34A] hover:bg-[#15803D] text-white font-bold text-xs shadow-md">Confirm Attendance</button>
            </div>
        </div>
    </div>

    <!-- Punch Receipt Modal -->
    <div x-show="receiptModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm"></div>
        <div class="relative w-full max-w-lg bg-white rounded-3xl shadow-2xl border border-[#E2E8F0] overflow-hidden z-10">
            <!-- Receipt Top Header -->
            <div class="bg-[#16A34A] text-white p-6 text-center space-y-2">
                <div class="w-14 h-14 mx-auto rounded-full bg-white text-[#16A34A] flex items-center justify-center shadow-lg">
                    <i data-lucide="check" class="w-8 h-8 stroke-[3]"></i>
                </div>
                <h2 class="text-xl font-extrabold">Attendance Marked Successfully ✓</h2>
                <p class="text-xs text-[#DCFCE7]" x-text="receiptData?.message"></p>
            </div>

            <!-- Receipt Content -->
            <div class="p-6 space-y-4">
                <div class="bg-[#F8FAFC] border border-[#E2E8F0] rounded-2xl p-5 space-y-2.5 text-xs font-mono">
                    <div class="flex justify-between border-b border-[#E2E8F0] pb-2">
                        <span class="text-[#64748B]">Receipt Ref:</span>
                        <strong class="text-[#1C1C1E]" x-text="'IE-REC-' + (receiptData?.attendance?.id || '9842')"></strong>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-[#64748B]">Employee:</span>
                        <strong class="text-[#1C1C1E]" x-text="receiptData?.attendance?.employee_name"></strong>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-[#64748B]">Employee Code:</span>
                        <strong class="text-[#FF6B1A]" x-text="receiptData?.attendance?.employee_code"></strong>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-[#64748B]">Date:</span>
                        <span class="text-[#1C1C1E]" x-text="receiptData?.attendance?.date"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-[#64748B]">Punch Time:</span>
                        <strong class="text-[#16A34A]" x-text="receiptData?.attendance?.check_in_time"></strong>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-[#64748B]">Status:</span>
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-[#ECFDF5] text-[#059669]" x-text="receiptData?.attendance?.status"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-[#64748B]">Work Category:</span>
                        <span class="text-[#1C1C1E]" x-text="receiptData?.attendance?.work_category"></span>
                    </div>
                </div>

                <template x-if="receiptData?.attendance?.email">
                    <p class="text-xs text-[#64748B] text-center">
                        A confirmation punch receipt was sent to <strong x-text="receiptData?.attendance?.email"></strong>.
                    </p>
                </template>

                <div class="flex gap-3">
                    <button type="button" onclick="window.print()" class="flex-1 py-3 rounded-xl border border-[#CBD5E1] text-[#1C1C1E] font-bold text-xs hover:bg-[#F8FAFC] flex items-center justify-center gap-1.5">
                        <i data-lucide="printer" class="w-4 h-4"></i>
                        <span>Print Receipt</span>
                    </button>
                    <button type="button" @click="resetPortal()" class="flex-1 py-3 rounded-xl bg-[#1C1C1E] hover:bg-black text-white font-bold text-xs shadow-md">
                        Done / Next Punch
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Help Modal -->
    <div x-show="helpModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div @click="helpModal = false" class="fixed inset-0 bg-black/60 backdrop-blur-sm"></div>
        <div class="relative w-full max-w-lg bg-white rounded-2xl shadow-2xl border border-[#E2E8F0] p-6 z-10 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-[#E2E8F0]">
                <h3 class="font-extrabold text-base text-[#1C1C1E] flex items-center gap-2">
                    <i data-lucide="help-circle" class="w-5 h-5 text-[#FF6B1A]"></i>
                    <span>Attendance Kiosk Guide</span>
                </h3>
                <button @click="helpModal = false" class="text-[#94A3B8] hover:text-[#1C1C1E]">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <div class="space-y-3 text-xs text-[#475569] leading-relaxed">
                <p><strong>1. Punch In:</strong> Enter your unique Employee Code (e.g. <code>INTRA-EMP-001</code>) and tap <strong>Continue</strong>. Your digital ID card will verify instantly.</p>
                <p><strong>2. Work Context:</strong> Select your project category and briefly describe your daily tasks. Tap <strong>Mark Attendance</strong>.</p>
                <p><strong>3. Duplicate Protection:</strong> If you try to punch in twice in one day, the system protects against duplicates and provides a <strong>Check-Out</strong> option to auto-calculate your shift duration.</p>
                <p><strong>4. Questions or Issues?</strong> Contact HR Operations at <a href="mailto:hr@intraeats.com" class="text-[#FF6B1A] font-semibold underline">hr@intraeats.com</a>.</p>
            </div>

            <div class="pt-2 text-right">
                <button type="button" @click="helpModal = false" class="px-4 py-2 rounded-xl bg-[#FF6B1A] text-white text-xs font-bold">Got it</button>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="w-full bg-white border-t border-[#E2E8F0] py-6 text-center text-xs text-[#64748B]">
        <div class="max-w-6xl mx-auto px-4 flex flex-col sm:flex-row items-center justify-between gap-3">
            <span>&copy; {{ date('Y') }} <strong>IntraEats &amp; Talisha Software</strong> &bull; HR &amp; Attendance Management System</span>
            <span>Food-Tech Workforce Operations &bull; Hostinger-Ready Deployment</span>
        </div>
    </footer>
</div>
@endsection

@push('scripts')
<script>
function employeeAttendancePortal() {
    return {
        employeeCode: 'INTRA-EMP-001',
        verifying: false,
        verified: false,
        employee: null,
        todayAttendance: null,
        isDuplicate: false,
        errorMessage: '',
        workCategory: 'Internal Project',
        taskDescription: 'Refactored kitchen order routing socket connection.',
        submitting: false,
        confirmModal: false,
        receiptModal: false,
        receiptData: null,
        helpModal: false,
        liveClock: '{{ $currentTime }}',
        currentDate: '{{ now()->format('d M Y') }}',

        init() {
            // Live clock ticker
            setInterval(() => {
                const now = new Date();
                this.liveClock = now.toLocaleDateString('en-GB', { day: '2-digit', month: 'long', year: 'numeric' }) + ' | ' + now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true });
            }, 1000);

            // Re-render icons on state changes
            this.$watch('verified', () => { setTimeout(() => window.createIcons({ icons: window.lucideIcons }), 50); });
            this.$watch('isDuplicate', () => { setTimeout(() => window.createIcons({ icons: window.lucideIcons }), 50); });
            this.$watch('receiptModal', () => { setTimeout(() => window.createIcons({ icons: window.lucideIcons }), 50); });
        },

        async verifyEmployee() {
            if (!this.employeeCode.trim()) {
                this.errorMessage = 'Please enter an Employee Code.';
                return;
            }

            this.verifying = true;
            this.errorMessage = '';
            this.verified = false;
            this.isDuplicate = false;

            try {
                const res = await fetch('{{ route('employee.verify') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ employee_code: this.employeeCode.trim() })
                });

                const data = await res.json();

                if (res.ok && data.valid) {
                    this.verified = true;
                    this.employee = data.employee;
                    this.todayAttendance = data.todayAttendance;
                    if (data.todayAttendance) {
                        this.isDuplicate = true;
                    }
                } else {
                    this.errorMessage = data.message || 'Employee Code not found. Please check your code and try again.';
                }
            } catch (err) {
                this.errorMessage = 'Network error verifying employee code.';
            } finally {
                this.verifying = false;
                setTimeout(() => window.createIcons({ icons: window.lucideIcons }), 50);
            }
        },

        openConfirmModal() {
            if (!this.taskDescription.trim()) return;
            this.confirmModal = true;
            setTimeout(() => window.createIcons({ icons: window.lucideIcons }), 50);
        },

        async submitPunch(action = 'checkin') {
            this.confirmModal = false;
            this.submitting = true;
            this.errorMessage = '';

            try {
                const res = await fetch('{{ route('employee.punch') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        employee_code: this.employeeCode.trim(),
                        action: action,
                        work_category: this.workCategory,
                        task_description: this.taskDescription
                    })
                });

                const data = await res.json();

                if (res.ok && data.success) {
                    this.receiptData = data;
                    this.receiptModal = true;
                    if (action === 'checkout') {
                        this.todayAttendance.check_out_time = data.attendance.check_out_time;
                        this.todayAttendance.working_hours_formatted = data.attendance.working_hours_formatted;
                    }
                } else if (res.status === 409) {
                    this.isDuplicate = true;
                    this.todayAttendance = data.attendance;
                    this.errorMessage = data.message;
                } else {
                    this.errorMessage = data.message || 'Failed to submit attendance.';
                }
            } catch (err) {
                this.errorMessage = 'Network error while punching attendance.';
            } finally {
                this.submitting = false;
                setTimeout(() => window.createIcons({ icons: window.lucideIcons }), 50);
            }
        },

        resetPortal() {
            this.receiptModal = false;
            this.employeeCode = '';
            this.verified = false;
            this.employee = null;
            this.todayAttendance = null;
            this.isDuplicate = false;
            this.taskDescription = '';
        }
    };
}
</script>
@endpush
