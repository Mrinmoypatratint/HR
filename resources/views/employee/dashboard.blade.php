@extends('layouts.app')

@section('title', 'Employee Dashboard — IntraEats & Talisha Software')

@section('content')
<div class="min-h-screen bg-[#FAFAF8] flex flex-col justify-between" x-data="employeeDashboardApp()">

    <!-- Top Sticky Header -->
    <header class="w-full bg-white border-b border-[#E2E8F0] shadow-sm sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 h-16 flex items-center justify-between gap-2">
            <!-- Brand -->
            <div class="flex items-center gap-2.5 sm:gap-3 min-w-0">
                <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-[#FF6B1A] flex items-center justify-center text-white shadow-sm font-extrabold text-base sm:text-lg shrink-0">
                    <i data-lucide="utensils" class="w-4 h-4 sm:w-5 sm:h-5"></i>
                </div>
                <div class="flex flex-col min-w-0">
                    <span class="font-extrabold text-sm sm:text-base lg:text-lg text-[#1C1C1E] leading-tight tracking-tight truncate">IntraEats X Talisha Software</span>
                    <span class="text-[10px] font-bold text-[#FF6B1A] uppercase tracking-wider truncate">Employee Workspace</span>
                </div>
            </div>

            <!-- Header Nav Links -->
            <nav class="hidden md:flex items-center gap-1 text-xs font-bold text-[#64748B]">
                <a href="#punch" class="px-3 py-1.5 rounded-lg text-[#1C1C1E] bg-[#F1F5F9] hover:text-[#FF6B1A]">Punch Station</a>
                <a href="#ledger" class="px-3 py-1.5 rounded-lg hover:text-[#1C1C1E] hover:bg-[#F8FAFC]">My Ledger</a>
                <a href="#projects" class="px-3 py-1.5 rounded-lg hover:text-[#1C1C1E] hover:bg-[#F8FAFC]">My Projects</a>
                <a href="#idcard" class="px-3 py-1.5 rounded-lg hover:text-[#1C1C1E] hover:bg-[#F8FAFC]">Digital ID</a>
            </nav>

            <!-- User Profile & Logout -->
            <div class="flex items-center gap-2 sm:gap-3 shrink-0">
                <div class="flex items-center gap-2 bg-[#F8FAFC] border border-[#E2E8F0] py-1 px-2.5 sm:px-3 rounded-full">
                    @if($employee->photo_url)
                        <img src="{{ $employee->photo_url }}" class="w-7 h-7 rounded-full object-cover ring-1 ring-[#FF6B1A]/40" alt="Avatar">
                    @else
                        <div class="w-7 h-7 rounded-full bg-[#FF6B1A] text-white font-extrabold text-xs flex items-center justify-center">
                            {{ $employee->initials }}
                        </div>
                    @endif
                    <div class="hidden sm:flex flex-col text-left">
                        <span class="text-xs font-bold text-[#1C1C1E] leading-none">{{ $employee->full_name }}</span>
                        <span class="text-[10px] font-mono text-[#FF6B1A] font-semibold mt-0.5">{{ $employee->employee_code }}</span>
                    </div>
                </div>

                <!-- Sign Out Form -->
                <form action="{{ route('employee.logout') }}" method="POST">
                    @csrf
                    <button type="submit" title="Sign Out" class="inline-flex items-center gap-1.5 px-2.5 sm:px-3 py-1.5 rounded-xl border border-[#E2E8F0] hover:border-red-300 text-xs font-bold text-[#64748B] hover:text-red-600 hover:bg-red-50 transition-all shadow-sm">
                        <i data-lucide="log-out" class="w-3.5 h-3.5"></i>
                        <span class="hidden sm:inline">Sign Out</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- Mobile Horizontal Quick Anchor Scroller -->
        <div class="md:hidden border-t border-[#F1F5F9] bg-[#FAFAF8] px-3 py-2 overflow-x-auto no-scrollbar flex items-center gap-2 text-xs font-bold text-[#64748B]">
            <a href="#punch" class="px-3 py-1.5 rounded-lg bg-white border border-[#E2E8F0] text-[#1C1C1E] hover:text-[#FF6B1A] shadow-sm whitespace-nowrap flex items-center gap-1.5 shrink-0">
                <i data-lucide="fingerprint" class="w-3.5 h-3.5 text-[#FF6B1A]"></i>
                <span>Punch Station</span>
            </a>
            <a href="#ledger" class="px-3 py-1.5 rounded-lg bg-white border border-[#E2E8F0] hover:text-[#1C1C1E] shadow-sm whitespace-nowrap flex items-center gap-1.5 shrink-0">
                <i data-lucide="table-2" class="w-3.5 h-3.5 text-[#2563EB]"></i>
                <span>My Ledger</span>
            </a>
            <a href="#projects" class="px-3 py-1.5 rounded-lg bg-white border border-[#E2E8F0] hover:text-[#1C1C1E] shadow-sm whitespace-nowrap flex items-center gap-1.5 shrink-0">
                <i data-lucide="briefcase" class="w-3.5 h-3.5 text-[#16A34A]"></i>
                <span>My Projects</span>
            </a>
            <a href="#idcard" class="px-3 py-1.5 rounded-lg bg-white border border-[#E2E8F0] hover:text-[#1C1C1E] shadow-sm whitespace-nowrap flex items-center gap-1.5 shrink-0">
                <i data-lucide="contact-2" class="w-3.5 h-3.5 text-[#D97706]"></i>
                <span>Digital ID</span>
            </a>
        </div>
    </header>

    <!-- Main Container -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-3 sm:px-6 lg:px-8 py-5 sm:py-8 space-y-6 sm:space-y-8">

        <!-- Welcome Banner with Live Time -->
        <div class="bg-gradient-to-r from-[#1C1C1E] via-[#28282E] to-[#1C1C1E] text-white rounded-2xl p-4 sm:p-7 lg:p-8 shadow-xl flex flex-col lg:flex-row lg:items-center justify-between gap-5 sm:gap-6 border border-[#383842]">
            <div class="space-y-2">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#FF6B1A]/20 border border-[#FF6B1A]/40 text-[#FF8542] text-[11px] sm:text-xs font-bold tracking-wider uppercase">
                    <span class="w-2 h-2 rounded-full bg-[#FF6B1A] animate-pulse"></span>
                    Verified Employee Session
                </div>
                <h1 class="text-xl sm:text-2xl lg:text-3xl font-extrabold tracking-tight text-white">
                    Welcome back, {{ $employee->full_name }}!
                </h1>
                <p class="text-xs sm:text-sm text-[#A1A1AA] max-w-2xl">
                    {{ $employee->designation }} &bull; {{ $employee->department }} Department &bull;
                    <span class="text-[#FF6B1A] font-mono font-bold">{{ $employee->employee_code }}</span>
                </p>
            </div>

            <!-- Big Live Clock -->
            <div class="bg-[#121214] border border-[#3E3E46] p-4 sm:p-5 rounded-2xl shadow-inner flex flex-col lg:items-end w-full lg:w-auto lg:min-w-[260px] shrink-0">
                <span class="text-[10px] font-bold uppercase tracking-wider text-[#A1A1AA] flex items-center gap-1.5">
                    <i data-lucide="clock" class="w-3.5 h-3.5 text-[#FF6B1A]"></i>
                    Live Attendance Time (IST)
                </span>
                <div class="font-mono text-xl sm:text-2xl lg:text-3xl font-bold text-white tracking-tight mt-1" x-text="liveClock">
                    {{ $currentTime }}
                </div>
                <span class="text-[11px] text-[#4ADE80] font-semibold flex items-center gap-1 mt-1">
                    <i data-lucide="shield-check" class="w-3.5 h-3.5"></i> Asia/Kolkata Server Synchronized
                </span>
            </div>
        </div>

        <!-- Feedback Alerts -->
        @if(session('success'))
            <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center gap-3 shadow-sm">
                <i data-lucide="check-circle-2" class="w-5 h-5 text-emerald-600 shrink-0"></i>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        @endif

        <template x-if="punchMessage">
            <div class="p-4 rounded-xl text-sm flex items-center gap-3 shadow-sm transition-all"
                :class="punchSuccess ? 'bg-emerald-50 border border-emerald-200 text-emerald-800' : 'bg-red-50 border border-red-200 text-red-800'">
                <i :data-lucide="punchSuccess ? 'check-circle-2' : 'alert-circle'" class="w-5 h-5 shrink-0"></i>
                <span class="font-medium" x-text="punchMessage"></span>
            </div>
        </template>

        <!-- TOP GRID: 1. Punch Station (Left) & 2. KPI Metrics (Right) -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8" id="punch">

            <!-- 1. PUNCH STATION (7 COLS) -->
            <div class="lg:col-span-7 bg-white rounded-2xl border border-[#E2E8F0] shadow-sm p-6 sm:p-7 space-y-6">
                <div class="flex items-center justify-between border-b border-[#F1F5F9] pb-4">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-[#FF6B1A]/10 text-[#FF6B1A] flex items-center justify-center font-bold">
                            <i data-lucide="fingerprint" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <h2 class="font-extrabold text-base text-[#1C1C1E]">Today's Punch Station</h2>
                            <p class="text-xs text-[#64748B]">Official check-in / check-out terminal</p>
                        </div>
                    </div>

                    <!-- Today's Live Status Badge -->
                    <template x-if="shiftState === 'not_started'">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700">
                            <span class="w-2 h-2 rounded-full bg-slate-400"></span> Not Punched In
                        </span>
                    </template>
                    <template x-if="shiftState === 'active'">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 animate-pulse">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Shift In Progress
                        </span>
                    </template>
                    <template x-if="shiftState === 'completed'">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200">
                            <i data-lucide="check" class="w-3.5 h-3.5"></i> Shift Completed
                        </span>
                    </template>
                </div>

                <!-- STATE A: NOT STARTED -> Punch In Form -->
                <div x-show="shiftState === 'not_started'" class="space-y-4">
                    <div class="p-4 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0] text-xs text-[#64748B] flex items-start gap-3">
                        <i data-lucide="info" class="w-4 h-4 text-[#FF6B1A] shrink-0 mt-0.5"></i>
                        <div>
                            Ready to begin work? Select your target project and provide brief task notes to log your check-in timestamp.
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-[#64748B] mb-1.5">Project / Category</label>
                            <select x-model="workCategory" class="w-full bg-[#F8FAFC] border border-[#CBD5E1] text-[#1C1C1E] text-sm rounded-xl px-3.5 py-2.5 focus:border-[#FF6B1A] focus:ring-2 focus:ring-[#FF6B1A]/20">
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->name }}" {{ ($employee->assigned_project === $cat->name || $cat->is_default) ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-[#64748B] mb-1.5">Capture Timestamp</label>
                            <div class="w-full bg-[#F1F5F9] border border-[#E2E8F0] text-[#1E293B] font-mono text-sm rounded-xl px-3.5 py-2.5 flex items-center justify-between">
                                <span x-text="liveClockTime">Auto-capture</span>
                                <span class="text-[10px] font-bold text-[#16A34A] uppercase bg-[#ECFDF5] px-2 py-0.5 rounded">Server Locked</span>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-[#64748B] mb-1.5">Task Description / Daily Goal *</label>
                        <textarea x-model="taskDescription" rows="2" placeholder="e.g. Sprint backlog implementation, testing client order flow, daily sync." class="w-full bg-[#F8FAFC] border border-[#CBD5E1] text-[#1C1C1E] text-sm rounded-xl p-3 focus:border-[#FF6B1A] focus:ring-2 focus:ring-[#FF6B1A]/20"></textarea>
                    </div>

                    <button type="button" @click="submitPunch('checkin')" :disabled="submitting || !taskDescription.trim()" class="w-full py-4 rounded-xl bg-[#16A34A] hover:bg-[#15803D] text-white font-extrabold text-base tracking-wide flex items-center justify-center gap-2 shadow-lg shadow-[#16A34A]/25 hover:shadow-xl transition-all disabled:opacity-50">
                        <i data-lucide="check-circle" class="w-5 h-5"></i>
                        <span x-text="submitting ? 'Recording Punch In...' : 'Punch In Now'"></span>
                    </button>
                </div>

                <!-- STATE B: ACTIVE -> Show active check-in details + Punch Out button -->
                <div x-show="shiftState === 'active'" class="space-y-5">
                    <div class="p-5 rounded-2xl bg-gradient-to-br from-emerald-50 to-teal-50 border border-emerald-200">
                        <div class="flex items-start justify-between">
                            <div class="space-y-1">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-800">Check-In Recorded</span>
                                <div class="font-mono text-3xl font-extrabold text-emerald-950" x-text="currentAttendance?.check_in_time">
                                    {{ $todayAttendance?->check_in_time }}
                                </div>
                                <div class="text-xs text-emerald-800 flex items-center gap-2 pt-1 font-medium">
                                    <span>Project: <strong x-text="currentAttendance?.work_category || '{{ $todayAttendance?->work_category }}'"></strong></span>
                                    <span>&bull;</span>
                                    <span>Status: <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-200 text-emerald-900 uppercase" x-text="currentAttendance?.status || '{{ $todayAttendance?->status }}'"></span></span>
                                </div>
                            </div>
                            <div class="w-12 h-12 rounded-xl bg-emerald-500 text-white flex items-center justify-center shadow-md">
                                <i data-lucide="timer" class="w-6 h-6"></i>
                            </div>
                        </div>

                        @if($todayAttendance?->task_description)
                            <div class="mt-3 pt-3 border-t border-emerald-200 text-xs text-emerald-900">
                                <strong>Task:</strong> {{ $todayAttendance->task_description }}
                            </div>
                        @endif
                    </div>

                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 p-4 rounded-xl bg-amber-50 border border-amber-200">
                        <div class="text-xs text-amber-900">
                            <strong>Ready to end your shift?</strong>
                            Punching out will finalize your working hours for today.
                        </div>
                        <button type="button" @click="submitPunch('checkout')" :disabled="submitting" class="w-full sm:w-auto px-6 py-3 rounded-xl bg-[#1C1C1E] hover:bg-black text-white font-extrabold text-xs uppercase tracking-wider flex items-center justify-center gap-2 shadow-md transition-all shrink-0">
                            <i data-lucide="log-out" class="w-4 h-4 text-[#FF6B1A]"></i>
                            <span x-text="submitting ? 'Recording Check-out...' : 'Punch Out Now'"></span>
                        </button>
                    </div>
                </div>

                <!-- STATE C: COMPLETED -> Shift Summary Card -->
                <div x-show="shiftState === 'completed'" class="p-6 rounded-2xl bg-[#F8FAFC] border border-[#E2E8F0] space-y-4 text-center">
                    <div class="w-14 h-14 rounded-2xl bg-blue-50 text-blue-600 border border-blue-200 mx-auto flex items-center justify-center">
                        <i data-lucide="award" class="w-7 h-7"></i>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-lg text-[#1C1C1E]">Today's Shift Completed</h3>
                        <p class="text-xs text-[#64748B] mt-0.5">Your attendance has been fully registered for today.</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5 sm:gap-3 p-3.5 sm:p-4 rounded-xl bg-white border border-[#E2E8F0] text-center font-mono">
                        <div class="p-2 sm:p-0 bg-[#F8FAFC] sm:bg-transparent rounded-lg">
                            <span class="text-[10px] uppercase font-bold text-[#94A3B8] block">Check In</span>
                            <span class="text-sm font-bold text-[#1C1C1E]" x-text="currentAttendance?.check_in_time">{{ $todayAttendance?->check_in_time }}</span>
                        </div>
                        <div class="p-2 sm:p-0 bg-[#F8FAFC] sm:bg-transparent rounded-lg">
                            <span class="text-[10px] uppercase font-bold text-[#94A3B8] block">Check Out</span>
                            <span class="text-sm font-bold text-[#1C1C1E]" x-text="currentAttendance?.check_out_time">{{ $todayAttendance?->check_out_time }}</span>
                        </div>
                        <div class="p-2 sm:p-0 bg-[#F8FAFC] sm:bg-transparent rounded-lg">
                            <span class="text-[10px] uppercase font-bold text-[#94A3B8] block">Duration</span>
                            <span class="text-sm font-bold text-[#FF6B1A]" x-text="currentAttendance?.working_hours_formatted">{{ $todayAttendance?->working_hours_formatted }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. PERSONAL KPI METRICS (5 COLS) -->
            <div class="lg:col-span-5 space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="font-extrabold text-base text-[#1C1C1E] flex items-center gap-2">
                        <i data-lucide="activity" class="w-5 h-5 text-[#FF6B1A]"></i>
                        <span>This Month's Performance</span>
                    </h2>
                    <span class="text-xs text-[#64748B] font-mono">{{ date('F Y') }}</span>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-2 gap-3 sm:gap-3.5">
                    <!-- Present -->
                    <div class="p-3.5 sm:p-4 rounded-2xl bg-white border border-[#E2E8F0] shadow-sm">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold uppercase tracking-wider text-[#64748B]">Days Present</span>
                            <div class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold shrink-0">
                                <i data-lucide="user-check" class="w-4 h-4"></i>
                            </div>
                        </div>
                        <div class="text-xl sm:text-2xl font-extrabold text-[#1C1C1E] mt-2 font-mono">{{ $stats['presentCount'] }}</div>
                        <span class="text-[10px] sm:text-[11px] text-emerald-600 font-semibold mt-0.5 block truncate">&bull; Full shift attended</span>
                    </div>

                    <!-- Late -->
                    <div class="p-3.5 sm:p-4 rounded-2xl bg-white border border-[#E2E8F0] shadow-sm">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold uppercase tracking-wider text-[#64748B]">Late Check-ins</span>
                            <div class="w-7 h-7 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center font-bold shrink-0">
                                <i data-lucide="clock" class="w-4 h-4"></i>
                            </div>
                        </div>
                        <div class="text-xl sm:text-2xl font-extrabold text-[#1C1C1E] mt-2 font-mono">{{ $stats['lateCount'] }}</div>
                        <span class="text-[10px] sm:text-[11px] text-amber-600 font-semibold mt-0.5 block truncate">&bull; Past {{ $officeStart }}</span>
                    </div>

                    <!-- Hours -->
                    <div class="p-3.5 sm:p-4 rounded-2xl bg-white border border-[#E2E8F0] shadow-sm">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold uppercase tracking-wider text-[#64748B]">Hours Logged</span>
                            <div class="w-7 h-7 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold shrink-0">
                                <i data-lucide="hourglass" class="w-4 h-4"></i>
                            </div>
                        </div>
                        <div class="text-xl sm:text-2xl font-extrabold text-[#1C1C1E] mt-2 font-mono">{{ $stats['totalHours'] }}h</div>
                        <span class="text-[10px] sm:text-[11px] text-indigo-600 font-semibold mt-0.5 block truncate">&bull; Productive time</span>
                    </div>

                    <!-- Punctuality -->
                    <div class="p-3.5 sm:p-4 rounded-2xl bg-white border border-[#E2E8F0] shadow-sm">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold uppercase tracking-wider text-[#64748B]">Punctuality</span>
                            <div class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold shrink-0">
                                <i data-lucide="target" class="w-4 h-4"></i>
                            </div>
                        </div>
                        <div class="text-xl sm:text-2xl font-extrabold text-emerald-600 mt-2 font-mono">{{ $stats['punctualityRate'] }}%</div>
                        <span class="text-[10px] sm:text-[11px] text-[#64748B] font-semibold mt-0.5 block truncate">&bull; On-time score</span>
                    </div>
                </div>

                <!-- Digital ID Compact Pill Card -->
                <div class="p-4 sm:p-5 rounded-2xl bg-gradient-to-r from-[#1C1C1E] to-[#2E2E33] text-white shadow-md flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 sm:gap-4">
                    <div class="flex items-center gap-3 min-w-0">
                        @if($employee->photo_url)
                            <img src="{{ $employee->photo_url }}" class="w-11 h-11 sm:w-12 sm:h-12 rounded-xl object-cover ring-2 ring-[#FF6B1A]/40 shrink-0" alt="ID">
                        @else
                            <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-xl bg-[#FF6B1A] text-white font-extrabold flex items-center justify-center text-sm sm:text-base shrink-0">
                                {{ $employee->initials }}
                            </div>
                        @endif
                        <div class="min-w-0">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-[#FF6B1A]">Digital ID Verified</span>
                            <h4 class="font-bold text-sm text-white truncate">{{ $employee->full_name }}</h4>
                            <div class="text-[11px] text-[#A1A1AA] font-mono truncate">{{ $employee->employee_code }} &bull; {{ $employee->designation }}</div>
                        </div>
                    </div>
                    <a href="#idcard" class="w-full sm:w-auto text-center px-3.5 py-1.5 rounded-lg bg-white/10 hover:bg-white/20 text-white text-xs font-bold transition-all shrink-0">
                        View ID &rarr;
                    </a>
                </div>
            </div>
        </div>

        <!-- 3. PERSONAL ATTENDANCE LEDGER (DATA VIEWER) -->
        <div class="bg-white rounded-2xl border border-[#E2E8F0] shadow-sm overflow-hidden" id="ledger">
            <div class="p-6 border-b border-[#F1F5F9] flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h2 class="font-extrabold text-lg text-[#1C1C1E] flex items-center gap-2">
                        <i data-lucide="table-2" class="w-5 h-5 text-[#FF6B1A]"></i>
                        <span>My Personal Attendance Ledger</span>
                    </h2>
                    <p class="text-xs text-[#64748B] mt-0.5">Chronological record of your attendance punches and logged hours.</p>
                </div>

                <!-- Filter Controls -->
                <form method="GET" action="{{ route('employee.dashboard') }}#ledger" class="flex flex-wrap items-center gap-2.5">
                    <input type="month" name="month" value="{{ request('month') }}" class="bg-[#F8FAFC] border border-[#CBD5E1] text-[#1C1C1E] text-xs rounded-xl px-3 py-2">
                    <select name="status" class="bg-[#F8FAFC] border border-[#CBD5E1] text-[#1C1C1E] text-xs rounded-xl px-3 py-2">
                        <option value="">All Statuses</option>
                        <option value="PRESENT" {{ request('status') === 'PRESENT' ? 'selected' : '' }}>PRESENT</option>
                        <option value="LATE" {{ request('status') === 'LATE' ? 'selected' : '' }}>LATE</option>
                        <option value="ABSENT" {{ request('status') === 'ABSENT' ? 'selected' : '' }}>ABSENT</option>
                        <option value="LEAVE" {{ request('status') === 'LEAVE' ? 'selected' : '' }}>LEAVE</option>
                    </select>
                    <button type="submit" class="px-4 py-2 bg-[#1C1C1E] hover:bg-black text-white rounded-xl text-xs font-bold transition-all">
                        Filter
                    </button>
                    @if(request('month') || request('status'))
                        <a href="{{ route('employee.dashboard') }}#ledger" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold">
                            Reset
                        </a>
                    @endif
                </form>
            </div>

            <!-- Ledger Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[700px] whitespace-nowrap">
                    <thead>
                        <tr class="bg-[#F8FAFC] border-b border-[#E2E8F0] text-[11px] font-bold text-[#64748B] uppercase tracking-wider">
                            <th class="py-3.5 px-6">Date &amp; Day</th>
                            <th class="py-3.5 px-4">Punch In</th>
                            <th class="py-3.5 px-4">Punch Out</th>
                            <th class="py-3.5 px-4">Duration</th>
                            <th class="py-3.5 px-4">Status</th>
                            <th class="py-3.5 px-4">Project / Category</th>
                            <th class="py-3.5 px-6">Task Notes</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#F1F5F9] text-xs font-medium">
                        @forelse($attendances as $record)
                            <tr class="hover:bg-[#F8FAFC]/80 transition-colors">
                                <td class="py-3.5 px-6 font-mono text-[#1C1C1E]">
                                    <span class="font-bold">{{ $record->date->format('d M Y') }}</span>
                                    <span class="text-[10px] text-[#94A3B8] block">{{ $record->date->format('l') }}</span>
                                </td>
                                <td class="py-3.5 px-4 font-mono font-semibold text-emerald-700">
                                    {{ $record->check_in_time ?: '--' }}
                                </td>
                                <td class="py-3.5 px-4 font-mono font-semibold text-slate-700">
                                    {{ $record->check_out_time ?: '--' }}
                                </td>
                                <td class="py-3.5 px-4 font-mono font-bold text-[#1C1C1E]">
                                    {{ $record->working_hours_formatted ?: '00h 00m' }}
                                </td>
                                <td class="py-3.5 px-4">
                                    @if($record->status === 'PRESENT')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            PRESENT
                                        </span>
                                    @elseif($record->status === 'LATE')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                            LATE
                                        </span>
                                    @elseif($record->status === 'LEAVE')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                            LEAVE
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-red-50 text-red-700 border border-red-200">
                                            ABSENT
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-[#64748B]">
                                    <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-800 text-[11px] font-semibold">
                                        {{ $record->work_category ?: 'General' }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-6 text-[#64748B] max-w-xs truncate" title="{{ $record->task_description }}">
                                    {{ $record->task_description ?: '—' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-slate-400">
                                    <i data-lucide="calendar-x" class="w-8 h-8 mx-auto mb-2 text-slate-300"></i>
                                    <p class="text-sm font-semibold text-slate-600">No attendance entries found</p>
                                    <p class="text-xs text-slate-400">Punch in at the punch station to create your first record!</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($attendances->hasPages())
                <div class="p-4 border-t border-[#E2E8F0] bg-[#F8FAFC]">
                    {{ $attendances->links() }}
                </div>
            @endif
        </div>

        <!-- 4. BOTTOM GRID: Assigned Projects + Digital ID Card -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

            <!-- Assigned Projects Card -->
            <div class="bg-white rounded-2xl border border-[#E2E8F0] shadow-sm p-6 space-y-4" id="projects">
                <div class="flex items-center justify-between border-b border-[#F1F5F9] pb-3">
                    <h3 class="font-extrabold text-base text-[#1C1C1E] flex items-center gap-2">
                        <i data-lucide="briefcase" class="w-5 h-5 text-[#FF6B1A]"></i>
                        <span>My Assigned Projects</span>
                    </h3>
                    <span class="text-xs text-[#64748B] font-semibold">{{ $projects->count() }} Projects</span>
                </div>

                <div class="space-y-3">
                    @forelse($projects as $prj)
                        <div class="p-4 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0] flex items-start justify-between gap-4">
                            <div>
                                <h4 class="font-bold text-sm text-[#1C1C1E]">{{ $prj->name }}</h4>
                                <div class="text-xs text-[#64748B] mt-0.5 font-mono">
                                    <span>Client: <strong>{{ $prj->client }}</strong></span> &bull;
                                    <span>Lead: <strong>{{ $prj->manager }}</strong></span>
                                </div>
                                @if($prj->description)
                                    <p class="text-xs text-[#64748B] mt-1 line-clamp-1">{{ $prj->description }}</p>
                                @endif
                            </div>
                            <span class="px-2.5 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 uppercase shrink-0">
                                {{ $prj->status }}
                            </span>
                        </div>
                    @empty
                        <div class="p-6 text-center text-xs text-[#64748B] bg-[#F8FAFC] rounded-xl border border-dashed border-[#CBD5E1]">
                            No specific project assignments found. Defaulting to <strong>{{ $employee->assigned_project ?: 'Core IntraEats Platform' }}</strong>.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Digital ID Card Display -->
            <div class="bg-white rounded-2xl border border-[#E2E8F0] shadow-sm p-4 sm:p-6 space-y-4" id="idcard">
                <div class="flex items-center justify-between border-b border-[#F1F5F9] pb-3">
                    <h3 class="font-extrabold text-base text-[#1C1C1E] flex items-center gap-2">
                        <i data-lucide="contact-2" class="w-5 h-5 text-[#FF6B1A]"></i>
                        <span>Digital Employee ID Card</span>
                    </h3>
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-[#ECFDF5] text-[#059669]">OFFICIAL</span>
                </div>

                <div class="p-4 sm:p-5 rounded-2xl bg-gradient-to-br from-[#1C1C1E] via-[#2A2A30] to-[#1C1C1E] text-white shadow-xl border border-[#3E3E48] relative overflow-hidden">
                    <div class="flex flex-col sm:flex-row items-start justify-between gap-3 sm:gap-4">
                        <div class="flex items-center gap-3.5 min-w-0">
                            @if($employee->photo_url)
                                <img src="{{ $employee->photo_url }}" class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl object-cover ring-2 ring-[#FF6B1A] shadow-md shrink-0" alt="Employee Photo">
                            @else
                                <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-[#FF6B1A] text-white font-extrabold text-lg sm:text-xl flex items-center justify-center shadow-md shrink-0">
                                    {{ $employee->initials }}
                                </div>
                            @endif
                            <div class="min-w-0">
                                <h3 class="font-extrabold text-base text-white tracking-tight truncate">{{ $employee->full_name }}</h3>
                                <div class="font-mono text-xs font-bold text-[#FF6B1A]">{{ $employee->employee_code }}</div>
                                <div class="text-xs text-[#D4D4D8] mt-0.5 truncate">{{ $employee->designation }} &bull; {{ $employee->department }}</div>
                            </div>
                        </div>

                        <div class="w-full sm:w-auto flex sm:flex-col justify-between sm:text-right shrink-0 pt-2 sm:pt-0 border-t sm:border-t-0 border-[#3E3E48]">
                            <span class="text-[9px] uppercase font-bold text-[#A1A1AA] block">Joined Date</span>
                            <span class="text-xs font-mono font-semibold text-white">{{ $employee->joining_date->format('d M Y') }}</span>
                            <span class="inline-block mt-0.5 px-2 py-0.5 rounded text-[9px] font-bold bg-white/10 text-white uppercase">{{ $employee->employment_type }}</span>
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-t border-[#3E3E48] grid grid-cols-1 sm:grid-cols-2 gap-2 text-[11px] text-[#A1A1AA]">
                        <div class="truncate">Email: <span class="text-white">{{ $employee->email ?: 'N/A' }}</span></div>
                        <div class="truncate">Phone: <span class="text-white">{{ $employee->mobile ?: 'N/A' }}</span></div>
                        <div class="truncate">Emergency: <span class="text-white">{{ $employee->emergency_contact ?: 'HR Desk' }}</span></div>
                        <div>Status: <span class="text-emerald-400 font-bold">{{ $employee->status }}</span></div>
                    </div>
                </div>
            </div>
        </div>

    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-[#E2E8F0] py-6 text-center text-xs text-[#64748B]">
        <div class="max-w-7xl mx-auto px-4 flex flex-col sm:flex-row items-center justify-between gap-2">
            <span>&copy; {{ date('Y') }} IntraEats &amp; Talisha Software. All rights reserved.</span>
            <div class="flex items-center gap-4">
                <span>Employee Support: <a href="mailto:hr@intraeats.com" class="text-[#FF6B1A] font-semibold hover:underline">hr@intraeats.com</a></span>
            </div>
        </div>
    </footer>
</div>

<script>
function employeeDashboardApp() {
    return {
        shiftState: '{{ !$todayAttendance ? "not_started" : (!$todayAttendance->check_out_time ? "active" : "completed") }}',
        currentAttendance: @json($todayAttendance),
        workCategory: '{{ $todayAttendance?->work_category ?: ($employee->assigned_project ?: "Core IntraEats Platform") }}',
        taskDescription: '',
        submitting: false,
        punchMessage: '',
        punchSuccess: true,
        liveClock: '{{ $currentTime }}',
        liveClockTime: '{{ date("h:i:s A") }}',

        init() {
            setInterval(() => {
                const now = new Date();
                this.liveClock = now.toLocaleDateString('en-GB', { day: '2-digit', month: 'long', year: 'numeric' }) + ' | ' + now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true });
                this.liveClockTime = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true });
            }, 1000);
        },

        async submitPunch(action) {
            this.submitting = true;
            this.punchMessage = '';

            try {
                const res = await fetch('{{ route("employee.punch") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        action: action,
                        work_category: this.workCategory,
                        task_description: this.taskDescription,
                    })
                });

                const data = await res.json();

                if (res.ok && data.success) {
                    this.punchSuccess = true;
                    this.punchMessage = data.message;
                    this.currentAttendance = data.attendance;

                    if (action === 'checkin') {
                        this.shiftState = 'active';
                    } else if (action === 'checkout') {
                        this.shiftState = 'completed';
                    }

                    setTimeout(() => {
                        window.location.reload();
                    }, 1200);
                } else {
                    this.punchSuccess = false;
                    this.punchMessage = data.message || 'Could not register punch. Please try again.';
                }
            } catch (err) {
                this.punchSuccess = false;
                this.punchMessage = 'Network or server error while marking attendance.';
            } finally {
                this.submitting = false;
            }
        }
    }
}
</script>
@endsection
