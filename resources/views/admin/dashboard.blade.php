@extends('layouts.admin')

@section('title', 'Dashboard Overview')

@section('content')
<div class="space-y-6" x-data="{ clearDemoModal: false }">

    <!-- Top Action & Context Banner -->
    <div class="bg-white p-5 rounded-2xl border border-[#E2E8F0] shadow-sm flex flex-col xl:flex-row xl:items-center justify-between gap-4">
        <div class="flex flex-col sm:flex-row sm:items-center gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-2xl font-extrabold text-[#1C1C1E] tracking-tight">Dashboard Overview</h1>
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-[#FFF3EB] text-[#FF6B1A] text-xs font-bold border border-[#FFD4BD]">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#FF6B1A] animate-ping"></span>
                        Live Shift Cycle
                    </span>
                </div>
                <p class="text-xs text-[#64748B] mt-0.5">Central workforce telemetries, shift audits, and real-time attendance feeds.</p>
            </div>

            <div class="flex items-center gap-2 bg-[#F8FAFC] px-3 py-1.5 rounded-xl border border-[#E2E8F0] text-xs font-medium text-[#1E293B]">
                <i data-lucide="calendar" class="w-4 h-4 text-[#FF6B1A]"></i>
                <span class="font-bold">Today: {{ now()->format('d F Y') }}</span>
                <span class="text-[#94A3B8]">|</span>
                <span class="text-[#64748B]">Q4 Shift Cycle</span>
            </div>
        </div>

        <!-- Right Controls: Demo Data Status & Quick Action Buttons -->
        <div class="flex flex-wrap items-center gap-2">
            @if($hasDemoData)
            <div class="flex items-center gap-2 bg-[#EFF6FF] border border-[#BFDBFE] px-3 py-1.5 rounded-xl text-[#1E40AF] text-xs font-bold">
                <i data-lucide="sparkles" class="w-3.5 h-3.5 text-[#2563EB]"></i>
                <span>DEMO DATA ACTIVE</span>
                <button type="button" @click="clearDemoModal = true" class="text-red-600 hover:text-red-800 underline decoration-dotted ml-1 text-xs">
                    Clear Demo Data
                </button>
            </div>
            @endif

            <a href="{{ route('admin.employees.index') }}" class="inline-flex items-center gap-1.5 px-3 py-2 bg-white hover:bg-[#F8FAFC] text-[#1E293B] border border-[#CBD5E1] text-xs font-bold rounded-xl shadow-sm transition-all">
                <i data-lucide="user-plus" class="w-3.5 h-3.5 text-[#FF6B1A]"></i>
                <span>+ Add Employee</span>
            </a>

            <a href="{{ route('admin.attendance.index') }}" class="inline-flex items-center gap-1.5 px-3 py-2 bg-white hover:bg-[#F8FAFC] text-[#1E293B] border border-[#CBD5E1] text-xs font-bold rounded-xl shadow-sm transition-all">
                <i data-lucide="fingerprint" class="w-3.5 h-3.5 text-[#16A34A]"></i>
                <span>+ Manual Attendance</span>
            </a>

            <a href="{{ route('admin.projects.index') }}" class="inline-flex items-center gap-1.5 px-3 py-2 bg-white hover:bg-[#F8FAFC] text-[#1E293B] border border-[#CBD5E1] text-xs font-bold rounded-xl shadow-sm transition-all">
                <i data-lucide="folder-plus" class="w-3.5 h-3.5 text-[#2563EB]"></i>
                <span>+ Add Project</span>
            </a>

            <a href="{{ route('admin.reports.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-[#FF6B1A] hover:bg-[#E55607] text-white text-xs font-bold rounded-xl shadow-sm shadow-[#FF6B1A]/20 transition-all">
                <i data-lucide="download" class="w-3.5 h-3.5"></i>
                <span>Generate Report</span>
            </a>
        </div>
    </div>

    <!-- Row 1: Key Metric KPI Cards (5 distinct stylized SaaS tiles) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        <!-- Card 1: Total Employees -->
        <div class="bg-white p-5 rounded-2xl border border-[#E2E8F0] shadow-sm relative overflow-hidden flex flex-col justify-between hover:shadow-md transition-shadow">
            <div class="absolute -right-3 -top-3 w-14 h-14 bg-[#F1F5F9] rounded-full pointer-events-none opacity-60"></div>
            <div>
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-[#64748B]">Total Workforce</span>
                    <div class="w-8 h-8 rounded-lg bg-[#FFF3EB] text-[#FF6B1A] flex items-center justify-center">
                        <i data-lucide="users" class="w-4 h-4"></i>
                    </div>
                </div>
                <div class="flex items-baseline gap-2 mt-2">
                    <span class="text-3xl font-extrabold text-[#1C1C1E] tracking-tight">{{ $totalEmployees }}</span>
                    <span class="text-xs font-bold text-[#16A34A] flex items-center">
                        <i data-lucide="trending-up" class="w-3.5 h-3.5"></i> Active
                    </span>
                </div>
            </div>
            <div class="mt-4 pt-2 border-t border-[#F1F5F9] flex items-center justify-between text-xs text-[#64748B]">
                <span>Active: <strong class="text-[#1C1C1E]">{{ $activeEmployees }}</strong></span>
                <span>Inactive: <strong class="text-[#94A3B8]">{{ $inactiveEmployees }}</strong></span>
            </div>
        </div>

        <!-- Card 2: Present Today -->
        <div class="bg-white p-5 rounded-2xl border border-[#E2E8F0] shadow-sm relative overflow-hidden flex flex-col justify-between hover:shadow-md transition-shadow">
            <div class="absolute top-0 left-0 w-1.5 h-full bg-[#16A34A]"></div>
            <div>
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-[#16A34A]">Present Today</span>
                    <div class="w-8 h-8 rounded-lg bg-[#ECFDF5] text-[#16A34A] flex items-center justify-center">
                        <i data-lucide="check-circle" class="w-4 h-4"></i>
                    </div>
                </div>
                <div class="flex items-baseline gap-2 mt-2">
                    <span class="text-3xl font-extrabold text-[#1C1C1E] tracking-tight">{{ $presentToday }}</span>
                    <span class="text-xs font-bold text-[#059669] bg-[#ECFDF5] px-1.5 py-0.5 rounded">{{ $attendanceRateToday }}% rate</span>
                </div>
            </div>
            <div class="mt-4 pt-2 border-t border-[#F1F5F9] flex items-center justify-between text-xs text-[#64748B]">
                <span>Verified Check-in</span>
                <span class="font-bold text-[#16A34A]">{{ $presentToday }} Staff</span>
            </div>
        </div>

        <!-- Card 3: Late Today -->
        <div class="bg-white p-5 rounded-2xl border border-[#E2E8F0] shadow-sm relative overflow-hidden flex flex-col justify-between hover:shadow-md transition-shadow">
            <div class="absolute top-0 left-0 w-1.5 h-full bg-[#F59E0B]"></div>
            <div>
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-[#D97706]">Late Today</span>
                    <div class="w-8 h-8 rounded-lg bg-[#FFFBEB] text-[#D97706] flex items-center justify-center">
                        <i data-lucide="clock" class="w-4 h-4"></i>
                    </div>
                </div>
                <div class="flex items-baseline gap-2 mt-2">
                    <span class="text-3xl font-extrabold text-[#1C1C1E] tracking-tight">{{ $lateToday }}</span>
                    <span class="text-xs font-semibold text-[#D97706]">&gt; 15m Grace</span>
                </div>
            </div>
            <div class="mt-4 pt-2 border-t border-[#F1F5F9] flex items-center justify-between text-xs text-[#64748B]">
                <span>Threshold: 09:45 AM</span>
                <span class="font-bold text-[#D97706]">Alert Flagged</span>
            </div>
        </div>

        <!-- Card 4: Absent Today -->
        <div class="bg-white p-5 rounded-2xl border border-[#E2E8F0] shadow-sm relative overflow-hidden flex flex-col justify-between hover:shadow-md transition-shadow">
            <div class="absolute top-0 left-0 w-1.5 h-full bg-[#DC2626]"></div>
            <div>
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-[#DC2626]">Absent / Pending</span>
                    <div class="w-8 h-8 rounded-lg bg-[#FEF2F2] text-[#DC2626] flex items-center justify-center">
                        <i data-lucide="user-x" class="w-4 h-4"></i>
                    </div>
                </div>
                <div class="flex items-baseline gap-2 mt-2">
                    <span class="text-3xl font-extrabold text-[#1C1C1E] tracking-tight">{{ $absentToday }}</span>
                    <span class="text-xs text-[#64748B]">unpunched</span>
                </div>
            </div>
            <div class="mt-4 pt-2 border-t border-[#F1F5F9] flex items-center justify-between text-xs text-[#64748B]">
                <span>Roster Shift Gap</span>
                <span class="font-bold text-[#DC2626]">{{ $absentToday }} Staff</span>
            </div>
        </div>

        <!-- Card 5: On Leave -->
        <div class="bg-white p-5 rounded-2xl border border-[#E2E8F0] shadow-sm relative overflow-hidden flex flex-col justify-between hover:shadow-md transition-shadow">
            <div class="absolute top-0 left-0 w-1.5 h-full bg-[#2563EB]"></div>
            <div>
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-[#2563EB]">On Leave</span>
                    <div class="w-8 h-8 rounded-lg bg-[#EFF6FF] text-[#2563EB] flex items-center justify-center">
                        <i data-lucide="calendar-off" class="w-4 h-4"></i>
                    </div>
                </div>
                <div class="flex items-baseline gap-2 mt-2">
                    <span class="text-3xl font-extrabold text-[#1C1C1E] tracking-tight">{{ $leaveToday }}</span>
                    <span class="text-xs text-[#2563EB] font-semibold">Approved</span>
                </div>
            </div>
            <div class="mt-4 pt-2 border-t border-[#F1F5F9] flex items-center justify-between text-xs text-[#64748B]">
                <span>Today's Leave Log</span>
                <span class="font-bold text-[#2563EB]">{{ $leaveToday }} Staff</span>
            </div>
        </div>
    </div>

    <!-- Row 2: Charts (Chart.js) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- 7-Day Weekly Attendance Bar Chart -->
        <div class="lg:col-span-2 bg-white p-6 rounded-2xl border border-[#E2E8F0] shadow-sm flex flex-col justify-between">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="font-bold text-base text-[#1C1C1E]">Weekly Attendance Pulse (Last 7 Days)</h3>
                    <p class="text-xs text-[#64748B]">Daily distribution of staff check-in statuses across operational days.</p>
                </div>
                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-[#F8FAFC] border border-[#E2E8F0] text-[#475569]">
                    7-Day Overview
                </span>
            </div>
            <div class="h-64 w-full relative">
                <canvas id="weeklyAttendanceChart"></canvas>
            </div>
        </div>

        <!-- Attendance % Distribution Donut -->
        <div class="bg-white p-6 rounded-2xl border border-[#E2E8F0] shadow-sm flex flex-col justify-between">
            <div class="flex items-center justify-between mb-2">
                <div>
                    <h3 class="font-bold text-base text-[#1C1C1E]">Attendance Distribution</h3>
                    <p class="text-xs text-[#64748B]">Overall status breakdown across all logged shifts.</p>
                </div>
                <i data-lucide="pie-chart" class="w-4 h-4 text-[#FF6B1A]"></i>
            </div>
            <div class="h-56 w-full relative flex items-center justify-center">
                <canvas id="statusDonutChart"></canvas>
            </div>
            <div class="grid grid-cols-2 gap-2 text-xs pt-3 border-t border-[#F1F5F9]">
                <div class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-[#16A34A]"></span><span>Present: <strong>{{ $allPresent }}</strong></span></div>
                <div class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-[#F59E0B]"></span><span>Late: <strong>{{ $allLate }}</strong></span></div>
                <div class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-[#2563EB]"></span><span>Leave: <strong>{{ $allLeave }}</strong></span></div>
                <div class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-[#DC2626]"></span><span>Absent: <strong>{{ $allAbsent }}</strong></span></div>
            </div>
        </div>
    </div>

    <!-- Row 3: Recent Attendance & Recent Employees -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Recent Attendance Ledger (2 Cols) -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-[#E2E8F0] shadow-sm overflow-hidden flex flex-col justify-between">
            <div class="p-5 border-b border-[#E2E8F0] flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-base text-[#1C1C1E]">Live Attendance Stream</h3>
                    <p class="text-xs text-[#64748B]">Latest shift check-ins and check-outs logged in real-time.</p>
                </div>
                <a href="{{ route('admin.attendance.index') }}" class="text-xs font-bold text-[#FF6B1A] hover:underline flex items-center gap-1">
                    <span>View All Ledger</span>
                    <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[650px] whitespace-nowrap">
                    <thead>
                        <tr class="bg-[#F8FAFC] border-b border-[#E2E8F0] text-[10px] uppercase font-bold text-[#64748B]">
                            <th class="py-3 px-4">Employee</th>
                            <th class="py-3 px-4">Date &amp; Time</th>
                            <th class="py-3 px-4">Duration</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4">Project / Task</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#F1F5F9] text-xs">
                        @forelse($recentAttendance as $item)
                        <tr class="hover:bg-[#FFF3EB]/40 transition-colors">
                            <td class="py-3 px-4 flex items-center gap-3">
                                <img src="{{ $item->employee->photo_url ?: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=256&q=80' }}" class="w-8 h-8 rounded-full object-cover ring-1 ring-[#CBD5E1]" alt="Avatar">
                                <div>
                                    <strong class="text-[#1C1C1E] block">{{ $item->employee->full_name }}</strong>
                                    <span class="text-[11px] text-[#64748B] font-mono">{{ $item->employee->employee_code }}</span>
                                </div>
                            </td>
                            <td class="py-3 px-4 font-mono text-[11px]">
                                <div>{{ $item->date->format('d M') }} &bull; <strong class="text-[#1C1C1E]">{{ $item->check_in_time }}</strong></div>
                                @if($item->check_out_time)
                                    <span class="text-[#64748B]">Out: {{ $item->check_out_time }}</span>
                                @else
                                    <span class="text-[#FF6B1A] font-semibold">Active Shift</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 font-mono font-semibold text-[#1C1C1E]">
                                {{ $item->working_hours_formatted }}
                            </td>
                            <td class="py-3 px-4">
                                @if($item->status === 'PRESENT')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-[#ECFDF5] text-[#059669]">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#10B981]"></span> Present
                                    </span>
                                @elseif($item->status === 'LATE')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-[#FFFBEB] text-[#D97706]">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#F59E0B]"></span> Late
                                    </span>
                                @elseif($item->status === 'ABSENT')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-[#FEF2F2] text-[#DC2626]">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#EF4444]"></span> Absent
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-[#EFF6FF] text-[#2563EB]">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#3B82F6]"></span> Leave
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-[#475569] max-w-[180px] truncate">
                                <strong>[{{ $item->work_category }}]</strong>
                                @if($item->task_description)
                                    <span class="text-[#64748B] block truncate">{{ $item->task_description }}</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-8 text-[#94A3B8]">No attendance records found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Employees Added (1 Col) -->
        <div class="bg-white rounded-2xl border border-[#E2E8F0] shadow-sm overflow-hidden flex flex-col justify-between">
            <div class="p-5 border-b border-[#E2E8F0] flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-base text-[#1C1C1E]">Staff Roster</h3>
                    <p class="text-xs text-[#64748B]">Recently enrolled team members.</p>
                </div>
                <a href="{{ route('admin.employees.index') }}" class="text-xs font-bold text-[#FF6B1A] hover:underline">All Staff</a>
            </div>

            <div class="divide-y divide-[#F1F5F9] p-3">
                @foreach($recentEmployees as $emp)
                <div class="p-2.5 flex items-center justify-between hover:bg-[#F8FAFC] rounded-xl transition-colors">
                    <div class="flex items-center gap-3">
                        <img src="{{ $emp->photo_url ?: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=256&q=80' }}" class="w-9 h-9 rounded-full object-cover ring-1 ring-[#CBD5E1]" alt="Avatar">
                        <div>
                            <a href="{{ route('admin.employees.show', $emp->id) }}" class="font-bold text-xs text-[#1C1C1E] hover:text-[#FF6B1A]">{{ $emp->full_name }}</a>
                            <span class="text-[10px] text-[#64748B] block">{{ $emp->designation }} &bull; {{ $emp->department }}</span>
                        </div>
                    </div>
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $emp->status === 'ACTIVE' ? 'bg-[#ECFDF5] text-[#059669]' : 'bg-gray-100 text-gray-600' }}">
                        {{ $emp->status }}
                    </span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Row 4: Project Activity & Monthly Selector Summary -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Project Workload Card -->
        <div class="bg-white p-6 rounded-2xl border border-[#E2E8F0] shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="font-bold text-base text-[#1C1C1E]">Active Food-Tech Projects</h3>
                    <p class="text-xs text-[#64748B]">Allocation and status across development &amp; fleet pipelines.</p>
                </div>
                <a href="{{ route('admin.projects.index') }}" class="text-xs font-bold text-[#FF6B1A] hover:underline">Manage</a>
            </div>

            <div class="space-y-3">
                @foreach($projects as $proj)
                <div class="p-3.5 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0] flex items-center justify-between">
                    <div>
                        <div class="flex items-center gap-2">
                            <strong class="text-xs text-[#1C1C1E]">{{ $proj->name }}</strong>
                            <span class="font-mono text-[10px] text-[#64748B]">({{ $proj->project_id }})</span>
                        </div>
                        <span class="text-[11px] text-[#64748B]">Client: {{ $proj->client }} &bull; Lead: {{ $proj->manager }}</span>
                    </div>
                    <div class="text-right">
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-[#FFF3EB] text-[#FF6B1A]">{{ $proj->status }}</span>
                        <span class="block text-[10px] text-[#94A3B8] mt-1">{{ $proj->employees_count }} staff assigned</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Monthly Selector & Working Hours Summary -->
        <div class="bg-white p-6 rounded-2xl border border-[#E2E8F0] shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="font-bold text-base text-[#1C1C1E]">Monthly Shift Telemetry</h3>
                        <p class="text-xs text-[#64748B]">Select month to audit working days and attendance %.</p>
                    </div>

                    <form method="GET" action="{{ route('admin.dashboard') }}" class="flex items-center gap-2">
                        <input type="month" name="month" value="{{ $selectedMonth }}" onchange="this.form.submit()" class="text-xs bg-[#F8FAFC] border border-[#CBD5E1] rounded-lg px-2.5 py-1.5 text-[#1C1C1E] font-medium">
                    </form>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                    <div class="p-3 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0]">
                        <span class="text-[10px] font-bold uppercase text-[#64748B]">Working Days</span>
                        <p class="text-xl font-extrabold text-[#1C1C1E] mt-1">{{ $monthStats['working_days'] }}</p>
                    </div>
                    <div class="p-3 rounded-xl bg-[#ECFDF5] border border-[#A7F3D0]">
                        <span class="text-[10px] font-bold uppercase text-[#059669]">Present Count</span>
                        <p class="text-xl font-extrabold text-[#059669] mt-1">{{ $monthStats['present'] }}</p>
                    </div>
                    <div class="p-3 rounded-xl bg-[#FFFBEB] border border-[#FDE68A]">
                        <span class="text-[10px] font-bold uppercase text-[#D97706]">Late Count</span>
                        <p class="text-xl font-extrabold text-[#D97706] mt-1">{{ $monthStats['late'] }}</p>
                    </div>
                    <div class="p-3 rounded-xl bg-[#FEF2F2] border border-[#FECACA]">
                        <span class="text-[10px] font-bold uppercase text-[#DC2626]">Absent Count</span>
                        <p class="text-xl font-extrabold text-[#DC2626] mt-1">{{ $monthStats['absent'] }}</p>
                    </div>
                    <div class="p-3 rounded-xl bg-[#EFF6FF] border border-[#BFDBFE]">
                        <span class="text-[10px] font-bold uppercase text-[#2563EB]">On Leave</span>
                        <p class="text-xl font-extrabold text-[#2563EB] mt-1">{{ $monthStats['leave'] }}</p>
                    </div>
                    <div class="p-3 rounded-xl bg-[#FFF3EB] border border-[#FFD4BD]">
                        <span class="text-[10px] font-bold uppercase text-[#FF6B1A]">Monthly Rate</span>
                        <p class="text-xl font-extrabold text-[#FF6B1A] mt-1">
                            {{ $monthStats['total_punches'] > 0 ? round((($monthStats['present'] + $monthStats['late']) / $monthStats['total_punches']) * 100) : 0 }}%
                        </p>
                    </div>
                </div>
            </div>

            <!-- Quick Export Buttons in Card -->
            <div class="mt-4 pt-4 border-t border-[#F1F5F9] flex flex-wrap items-center justify-between gap-2">
                <span class="text-xs text-[#64748B]">Audited for: <strong>{{ $monthStats['month'] }}</strong></span>
                <div class="flex gap-2">
                    <a href="{{ route('admin.reports.index', ['preset' => 'month', 'export' => 'pdf']) }}" class="px-3 py-1.5 rounded-lg bg-[#1C1C1E] text-white font-bold text-xs flex items-center gap-1.5 shadow-sm">
                        <i data-lucide="file-text" class="w-3 h-3 text-[#FF6B1A]"></i> Export PDF
                    </a>
                    <a href="{{ route('admin.reports.index', ['preset' => 'month', 'export' => 'excel']) }}" class="px-3 py-1.5 rounded-lg bg-[#16A34A] text-white font-bold text-xs flex items-center gap-1.5 shadow-sm">
                        <i data-lucide="file-spreadsheet" class="w-3 h-3"></i> Export Excel
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Confirm Modal for "Clear Demo Data" -->
    <div x-show="clearDemoModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div @click="clearDemoModal = false" class="fixed inset-0 bg-black/60 backdrop-blur-sm"></div>
        <div class="relative w-full max-w-md bg-white rounded-2xl shadow-2xl border border-[#E2E8F0] p-6 z-10 space-y-4">
            <div class="w-12 h-12 rounded-full bg-red-100 text-red-600 flex items-center justify-center">
                <i data-lucide="alert-triangle" class="w-6 h-6"></i>
            </div>
            <div>
                <h3 class="font-extrabold text-base text-[#1C1C1E]">Clear Demo Data?</h3>
                <p class="text-xs text-[#64748B] mt-1 leading-relaxed">
                    This action will remove all simulated demo employees, demo projects, and 30-day simulated attendance records.
                    Your administrator accounts and settings will remain completely untouched.
                </p>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" @click="clearDemoModal = false" class="flex-1 py-2.5 rounded-xl border border-[#CBD5E1] text-[#64748B] font-bold text-xs hover:bg-[#F8FAFC]">Cancel</button>
                <form method="POST" action="{{ route('admin.clear-demo') }}" class="flex-1">
                    @csrf
                    <button type="submit" class="w-full py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white font-bold text-xs shadow-md">Yes, Clear Data</button>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    // 1. Weekly Attendance Bar Chart
    const weeklyCtx = document.getElementById('weeklyAttendanceChart');
    if (weeklyCtx && window.Chart) {
        new window.Chart(weeklyCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($weeklyLabels) !!},
                datasets: [
                    {
                        label: 'Present',
                        data: {!! json_encode($weeklyPresent) !!},
                        backgroundColor: '#16A34A',
                        borderRadius: 6,
                    },
                    {
                        label: 'Late',
                        data: {!! json_encode($weeklyLate) !!},
                        backgroundColor: '#F59E0B',
                        borderRadius: 6,
                    },
                    {
                        label: 'Leave',
                        data: {!! json_encode($weeklyLeave) !!},
                        backgroundColor: '#2563EB',
                        borderRadius: 6,
                    },
                    {
                        label: 'Absent',
                        data: {!! json_encode($weeklyAbsent) !!},
                        backgroundColor: '#DC2626',
                        borderRadius: 6,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: { font: { family: 'DM Sans', size: 11 }, boxWidth: 12 }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { family: 'DM Sans', size: 11 } }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 2, font: { family: 'DM Sans', size: 11 } }
                    }
                }
            }
        });
    }

    // 2. Attendance Status Donut Chart
    const donutCtx = document.getElementById('statusDonutChart');
    if (donutCtx && window.Chart) {
        new window.Chart(donutCtx, {
            type: 'doughnut',
            data: {
                labels: ['Present', 'Late', 'Leave', 'Absent'],
                datasets: [{
                    data: [{{ $allPresent }}, {{ $allLate }}, {{ $allLeave }}, {{ $allAbsent }}],
                    backgroundColor: ['#16A34A', '#F59E0B', '#2563EB', '#DC2626'],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: { display: false }
                }
            }
        });
    }
});
</script>
@endpush
