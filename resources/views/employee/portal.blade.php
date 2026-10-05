@extends('layouts.app')

@section('title', 'IntraEats X Talisha Software — Employee Attendance Portal')

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
                    <span class="font-extrabold text-lg text-[#1C1C1E] leading-tight tracking-tight">IntraEats X Talisha Software</span>
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
                    <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white">Daily Attendance &amp; Shift Status</h1>
                    <p class="text-xs sm:text-sm text-[#D4D4D8] mt-1">Verify employee credentials and check today's real-time attendance status.</p>
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

                @if(session('info'))
                    <div class="p-4 rounded-xl bg-blue-50 border border-blue-200 text-blue-800 flex items-center gap-3">
                        <i data-lucide="info" class="w-5 h-5 text-blue-600 shrink-0"></i>
                        <span class="text-xs font-semibold">{{ session('info') }}</span>
                    </div>
                @endif

                @if(session('success'))
                    <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center gap-3">
                        <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600 shrink-0"></i>
                        <span class="text-xs font-semibold">{{ session('success') }}</span>
                    </div>
                @endif

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

                        <!-- Login Required to Punch Out -->
                        <template x-if="!todayAttendance?.check_out_time">
                            <div class="pt-2 flex flex-col sm:flex-row items-center justify-between gap-3 border-t border-amber-200/60">
                                <span class="text-xs text-amber-800 font-medium">Ready to end your shift? Login to your employee workspace to punch out.</span>
                                <a :href="'{{ route('employee.login') }}?code=' + encodeURIComponent(employeeCode)" class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-[#1C1C1E] hover:bg-black text-white font-bold text-xs uppercase tracking-wider flex items-center justify-center gap-2 shadow-md transition-all">
                                    <i data-lucide="lock" class="w-4 h-4 text-[#FF6B1A]"></i>
                                    <span>Sign In to Punch Out &rarr;</span>
                                </a>
                            </div>
                        </template>
                    </div>
                </template>

                <!-- STEP 1: Enter Employee Code -->
                <div class="bg-[#F8FAFC] border border-[#E2E8F0] p-6 rounded-2xl space-y-4">
                    <div class="flex items-center justify-between">
                        <label class="font-bold text-sm text-[#1C1C1E] flex items-center gap-2">
                            <span class="w-6 h-6 rounded-full bg-[#FF6B1A] text-white flex items-center justify-center text-xs font-bold">1</span>
                            <span>Enter Employee Code to Check Status</span>
                        </label>
                        <span class="text-xs text-[#64748B]">ID &amp; Shift Verification</span>
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
                            <span x-text="verifying ? 'Checking...' : 'Check Status'"></span>
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

                <!-- Shift Status: Not Marked Yet + Login Required to Give Attendance -->
                <div x-show="verified && !isDuplicate" x-cloak class="bg-white border border-[#E2E8F0] p-6 rounded-2xl space-y-5">
                    <div class="flex items-center justify-between border-b border-[#F1F5F9] pb-4">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center font-bold">
                                <i data-lucide="clock" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <h2 class="font-extrabold text-base text-[#1C1C1E]">Today's Shift Status: Not Marked Yet</h2>
                                <p class="text-xs text-[#64748B]">Official check-in record has not been logged for today</p>
                            </div>
                        </div>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                            <span class="w-2 h-2 rounded-full bg-amber-500"></span> Awaiting Punch-In
                        </span>
                    </div>

                    <!-- Security & Login Required Notice -->
                    <div class="p-6 rounded-2xl bg-gradient-to-br from-[#FFF8F3] to-[#FFF1E8] border border-[#FFE0CC] space-y-4">
                        <div class="flex items-start gap-3.5">
                            <div class="w-11 h-11 rounded-xl bg-[#FF6B1A] text-white flex items-center justify-center shrink-0 shadow-md">
                                <i data-lucide="lock" class="w-5 h-5"></i>
                            </div>
                            <div class="space-y-1">
                                <h3 class="font-extrabold text-base text-[#1C1C1E]">Employee Login Required to Give Attendance</h3>
                                <p class="text-xs text-[#475569] leading-relaxed">
                                    Attendance cannot be given without logging in. To ensure verified tracking and accurate task logging, employees must sign in to their personal workspace.
                                </p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-3 text-xs border-t border-[#FFE0CC]">
                            <div class="flex items-center gap-2 text-[#475569]">
                                <i data-lucide="clock" class="w-4 h-4 text-[#FF6B1A]"></i>
                                <span>Official Shift Start: <strong class="text-[#1C1C1E]">{{ $officeStart }}</strong></span>
                            </div>
                            <div class="flex items-center gap-2 text-[#475569]">
                                <i data-lucide="shield-check" class="w-4 h-4 text-[#16A34A]"></i>
                                <span>Verified Employee: <strong class="text-[#1C1C1E]" x-text="employee?.full_name"></strong></span>
                            </div>
                        </div>

                        <!-- Sign In to Give Attendance CTA -->
                        <div class="pt-2">
                            <a :href="'{{ route('employee.login') }}?code=' + encodeURIComponent(employeeCode)" class="w-full py-4 rounded-xl bg-[#FF6B1A] hover:bg-[#E55607] text-white font-extrabold text-base tracking-wide flex items-center justify-center gap-2 shadow-lg shadow-[#FF6B1A]/20 hover:shadow-xl transition-all">
                                <i data-lucide="log-in" class="w-5 h-5"></i>
                                <span>Sign In to Give Attendance &rarr;</span>
                            </a>
                            <p class="text-[11px] text-center text-[#94A3B8] mt-2">
                                Your Employee Code (<span class="font-mono text-[#FF6B1A] font-semibold" x-text="employeeCode"></span>) will be automatically prefilled.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Help Modal -->
    <div x-show="helpModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div @click="helpModal = false" class="fixed inset-0 bg-black/60 backdrop-blur-sm"></div>
        <div class="relative w-full max-w-lg bg-white rounded-2xl shadow-2xl border border-[#E2E8F0] p-6 z-10 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-[#E2E8F0]">
                <h3 class="font-extrabold text-base text-[#1C1C1E] flex items-center gap-2">
                    <i data-lucide="help-circle" class="w-5 h-5 text-[#FF6B1A]"></i>
                    <span>Attendance &amp; Shift Guide</span>
                </h3>
                <button @click="helpModal = false" class="text-[#94A3B8] hover:text-[#1C1C1E]">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <div class="space-y-3 text-xs text-[#475569] leading-relaxed">
                <p><strong>1. Check Status:</strong> Enter your unique Employee Code (e.g. <code>INTRA-EMP-001</code>) and tap <strong>Check Status</strong> to verify your digital ID and check whether you have already punched in today.</p>
                <p><strong>2. Login Required to Give Attendance:</strong> Employees cannot mark attendance without logging in. Tap <strong>Sign In to Give Attendance</strong> to open your secure employee workspace.</p>
                <p><strong>3. Punch In &amp; Shift Management:</strong> In your employee dashboard, select your project category, log daily goals, and record server-locked timestamps.</p>
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

        resetPortal() {
            this.employeeCode = '';
            this.verified = false;
            this.employee = null;
            this.todayAttendance = null;
            this.isDuplicate = false;
        }
    };
}
</script>
@endpush
