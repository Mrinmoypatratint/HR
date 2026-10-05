@extends('layouts.admin')

@section('title', 'Attendance Reports & Workforce Analytics')

@section('content')
<div class="space-y-6">

    <!-- Top Action Header -->
    <div class="bg-white p-5 rounded-2xl border border-[#E2E8F0] shadow-sm flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-2xl font-extrabold text-[#1C1C1E] tracking-tight">Reports & Workforce Analytics</h1>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-[#EFF6FF] text-[#2563EB] border border-[#BFDBFE]">Verified Records</span>
            </div>
            <p class="text-xs text-[#64748B] mt-0.5">Comprehensive time & attendance reporting, department audits, compliance rates, and export suite.</p>
        </div>

        <!-- Export Action Buttons -->
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('admin.reports.index', array_merge(request()->query(), ['export' => 'pdf'])) }}" 
               class="px-3.5 py-2 rounded-xl bg-white hover:bg-[#FEF2F2] text-[#DC2626] border border-[#FECACA] text-xs font-bold shadow-sm transition-all flex items-center gap-1.5 hover:border-[#DC2626]">
                <i data-lucide="file-text" class="w-4 h-4"></i>
                <span>Download PDF</span>
            </a>

            <a href="{{ route('admin.reports.index', array_merge(request()->query(), ['export' => 'excel'])) }}" 
               class="px-3.5 py-2 rounded-xl bg-white hover:bg-[#ECFDF5] text-[#059669] border border-[#A7F3D0] text-xs font-bold shadow-sm transition-all flex items-center gap-1.5 hover:border-[#059669]">
                <i data-lucide="file-spreadsheet" class="w-4 h-4"></i>
                <span>Export Excel (.xlsx)</span>
            </a>

            <a href="{{ route('admin.reports.index', array_merge(request()->query(), ['export' => 'csv'])) }}" 
               class="px-3.5 py-2 rounded-xl bg-white hover:bg-[#F8FAFC] text-[#1E293B] border border-[#CBD5E1] text-xs font-bold shadow-sm transition-all flex items-center gap-1.5 hover:border-[#94A3B8]">
                <i data-lucide="download" class="w-4 h-4 text-[#64748B]"></i>
                <span>Export CSV</span>
            </a>

            <button type="button" onclick="window.print()" class="px-3.5 py-2 rounded-xl bg-[#F8FAFC] hover:bg-[#F1F5F9] text-[#475569] border border-[#E2E8F0] text-xs font-bold transition-all flex items-center gap-1.5">
                <i data-lucide="printer" class="w-4 h-4"></i>
                <span>Print</span>
            </button>
        </div>
    </div>

    <!-- Quick Presets & Filter Panel -->
    <div class="bg-white p-5 rounded-2xl border border-[#E2E8F0] shadow-sm space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-[#F1F5F9]">
            <div class="flex items-center gap-2">
                <i data-lucide="filter" class="w-4 h-4 text-[#FF6B1A]"></i>
                <span class="text-xs font-extrabold uppercase tracking-wider text-[#1C1C1E]">Report Filters & Parameters</span>
            </div>

            <!-- Quick Presets -->
            <div class="flex flex-wrap items-center gap-1.5">
                <span class="text-[11px] font-bold text-[#94A3B8] mr-1">Quick Range:</span>
                <a href="{{ route('admin.reports.index', array_merge(request()->except(['start_date', 'end_date', 'preset']), ['preset' => 'today'])) }}"
                   class="px-2.5 py-1 rounded-lg text-xs font-bold transition-all {{ request('preset') === 'today' ? 'bg-[#FF6B1A] text-white' : 'bg-[#F8FAFC] text-[#64748B] hover:bg-[#E2E8F0]' }}">
                    Today
                </a>
                <a href="{{ route('admin.reports.index', array_merge(request()->except(['start_date', 'end_date', 'preset']), ['preset' => '7days'])) }}"
                   class="px-2.5 py-1 rounded-lg text-xs font-bold transition-all {{ request('preset') === '7days' ? 'bg-[#FF6B1A] text-white' : 'bg-[#F8FAFC] text-[#64748B] hover:bg-[#E2E8F0]' }}">
                    Last 7 Days
                </a>
                <a href="{{ route('admin.reports.index', array_merge(request()->except(['start_date', 'end_date', 'preset']), ['preset' => 'month'])) }}"
                   class="px-2.5 py-1 rounded-lg text-xs font-bold transition-all {{ request('preset') === 'month' ? 'bg-[#FF6B1A] text-white' : 'bg-[#F8FAFC] text-[#64748B] hover:bg-[#E2E8F0]' }}">
                    This Month
                </a>
                <a href="{{ route('admin.reports.index') }}"
                   class="px-2.5 py-1 rounded-lg text-xs font-bold bg-[#F1F5F9] text-[#475569] hover:bg-[#E2E8F0]">
                    Clear
                </a>
            </div>
        </div>

        <form method="GET" action="{{ route('admin.reports.index') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
            <!-- Start Date -->
            <div>
                <label class="block text-[11px] font-bold text-[#475569] uppercase tracking-wider mb-1">Start Date</label>
                <input type="date" name="start_date" value="{{ $startDate }}" class="w-full text-xs px-3 py-2 rounded-xl bg-[#F8FAFC] border border-[#CBD5E1] text-[#1C1C1E] focus:border-[#FF6B1A] focus:ring-2 focus:ring-[#FF6B1A]/20">
            </div>

            <!-- End Date -->
            <div>
                <label class="block text-[11px] font-bold text-[#475569] uppercase tracking-wider mb-1">End Date</label>
                <input type="date" name="end_date" value="{{ $endDate }}" class="w-full text-xs px-3 py-2 rounded-xl bg-[#F8FAFC] border border-[#CBD5E1] text-[#1C1C1E] focus:border-[#FF6B1A] focus:ring-2 focus:ring-[#FF6B1A]/20">
            </div>

            <!-- Report Category / Type -->
            <div>
                <label class="block text-[11px] font-bold text-[#475569] uppercase tracking-wider mb-1">Report Type</label>
                <select name="report_type" class="w-full text-xs px-3 py-2 rounded-xl bg-[#F8FAFC] border border-[#CBD5E1] text-[#1C1C1E] focus:border-[#FF6B1A] focus:ring-2 focus:ring-[#FF6B1A]/20">
                    <option value="all" {{ $reportType === 'all' ? 'selected' : '' }}>All Records</option>
                    <option value="late" {{ $reportType === 'late' ? 'selected' : '' }}>Late Arrivals Only</option>
                    <option value="absent" {{ $reportType === 'absent' ? 'selected' : '' }}>Absences Only</option>
                    <option value="leave" {{ $reportType === 'leave' ? 'selected' : '' }}>On Leave Only</option>
                </select>
            </div>

            <!-- Department -->
            <div>
                <label class="block text-[11px] font-bold text-[#475569] uppercase tracking-wider mb-1">Department</label>
                <select name="department" class="w-full text-xs px-3 py-2 rounded-xl bg-[#F8FAFC] border border-[#CBD5E1] text-[#1C1C1E] focus:border-[#FF6B1A] focus:ring-2 focus:ring-[#FF6B1A]/20">
                    <option value="">All Departments</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept }}" {{ request('department') === $dept ? 'selected' : '' }}>{{ $dept }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Employee Dropdown -->
            <div>
                <label class="block text-[11px] font-bold text-[#475569] uppercase tracking-wider mb-1">Employee</label>
                <select name="employee_id" class="w-full text-xs px-3 py-2 rounded-xl bg-[#F8FAFC] border border-[#CBD5E1] text-[#1C1C1E] focus:border-[#FF6B1A] focus:ring-2 focus:ring-[#FF6B1A]/20">
                    <option value="">All Employees</option>
                    @foreach($allEmployees as $emp)
                        <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>
                            {{ $emp->full_name }} ({{ $emp->employee_code }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Submit Button -->
            <div class="flex items-end">
                <button type="submit" class="w-full py-2 px-3 rounded-xl bg-[#FF6B1A] hover:bg-[#E55607] text-white text-xs font-bold shadow-md shadow-[#FF6B1A]/20 transition-all flex items-center justify-center gap-1.5 h-[38px]">
                    <i data-lucide="check" class="w-4 h-4"></i>
                    <span>Apply Filters</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Summary KPI Metric Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
        <!-- Total Logs -->
        <div class="bg-white p-4 rounded-2xl border border-[#E2E8F0] shadow-sm">
            <div class="flex items-center justify-between text-[#64748B] mb-1">
                <span class="text-[11px] font-bold uppercase tracking-wider">Total Records</span>
                <i data-lucide="calendar" class="w-4 h-4 text-[#2563EB]"></i>
            </div>
            <p class="text-2xl font-black text-[#1C1C1E]">{{ number_format($summary['total']) }}</p>
            <span class="text-[10px] text-[#94A3B8] font-medium">{{ $summary['period'] }}</span>
        </div>

        <!-- Present on Time -->
        <div class="bg-white p-4 rounded-2xl border border-[#E2E8F0] shadow-sm">
            <div class="flex items-center justify-between text-[#64748B] mb-1">
                <span class="text-[11px] font-bold uppercase tracking-wider text-[#059669]">Present</span>
                <i data-lucide="check-circle" class="w-4 h-4 text-[#059669]"></i>
            </div>
            <p class="text-2xl font-black text-[#059669]">{{ number_format($summary['present']) }}</p>
            <span class="text-[10px] text-[#059669] font-medium">On-time punches</span>
        </div>

        <!-- Late Arrivals -->
        <div class="bg-white p-4 rounded-2xl border border-[#E2E8F0] shadow-sm">
            <div class="flex items-center justify-between text-[#64748B] mb-1">
                <span class="text-[11px] font-bold uppercase tracking-wider text-[#D97706]">Late Arrivals</span>
                <i data-lucide="clock" class="w-4 h-4 text-[#D97706]"></i>
            </div>
            <p class="text-2xl font-black text-[#D97706]">{{ number_format($summary['late']) }}</p>
            <span class="text-[10px] text-[#D97706] font-medium">Past grace period</span>
        </div>

        <!-- Absent -->
        <div class="bg-white p-4 rounded-2xl border border-[#E2E8F0] shadow-sm">
            <div class="flex items-center justify-between text-[#64748B] mb-1">
                <span class="text-[11px] font-bold uppercase tracking-wider text-[#DC2626]">Absences</span>
                <i data-lucide="x-circle" class="w-4 h-4 text-[#DC2626]"></i>
            </div>
            <p class="text-2xl font-black text-[#DC2626]">{{ number_format($summary['absent']) }}</p>
            <span class="text-[10px] text-[#DC2626] font-medium">Unexcused</span>
        </div>

        <!-- Compliance Rate -->
        <div class="bg-white p-4 rounded-2xl border border-[#E2E8F0] shadow-sm">
            <div class="flex items-center justify-between text-[#64748B] mb-1">
                <span class="text-[11px] font-bold uppercase tracking-wider text-[#7C3AED]">Punctuality Rate</span>
                <i data-lucide="percent" class="w-4 h-4 text-[#7C3AED]"></i>
            </div>
            <p class="text-2xl font-black text-[#7C3AED]">{{ $summary['rate'] }}%</p>
            <span class="text-[10px] text-[#7C3AED] font-medium">Present / Total ratio</span>
        </div>

        <!-- Total Hours -->
        <div class="bg-white p-4 rounded-2xl border border-[#E2E8F0] shadow-sm">
            <div class="flex items-center justify-between text-[#64748B] mb-1">
                <span class="text-[11px] font-bold uppercase tracking-wider text-[#0284C7]">Work Hours</span>
                <i data-lucide="activity" class="w-4 h-4 text-[#0284C7]"></i>
            </div>
            <p class="text-2xl font-black text-[#0284C7]">{{ number_format($summary['totalHours'], 1) }}h</p>
            <span class="text-[10px] text-[#0284C7] font-medium">Total logged hours</span>
        </div>
    </div>

    <!-- Attendance Breakdown Progress Bar -->
    @if($summary['total'] > 0)
    <div class="bg-white p-4 rounded-2xl border border-[#E2E8F0] shadow-sm space-y-2">
        <div class="flex items-center justify-between text-xs font-bold text-[#64748B]">
            <span>Workforce Distribution Breakdown ({{ $summary['period'] }})</span>
            <span>{{ $summary['total'] }} Total Logs Analyzed</span>
        </div>
        @php
            $pPct = round(($summary['present'] / $summary['total']) * 100);
            $lPct = round(($summary['late'] / $summary['total']) * 100);
            $aPct = round(($summary['absent'] / $summary['total']) * 100);
            $lvPct = round(($summary['leave'] / $summary['total']) * 100);
        @endphp
        <div class="h-3 w-full bg-[#F1F5F9] rounded-full overflow-hidden flex">
            <div style="width: {{ $pPct }}%" class="bg-[#10B981] transition-all" title="Present: {{ $pPct }}%"></div>
            <div style="width: {{ $lPct }}%" class="bg-[#F59E0B] transition-all" title="Late: {{ $lPct }}%"></div>
            <div style="width: {{ $aPct }}%" class="bg-[#EF4444] transition-all" title="Absent: {{ $aPct }}%"></div>
            <div style="width: {{ $lvPct }}%" class="bg-[#6366F1] transition-all" title="Leave: {{ $lvPct }}%"></div>
        </div>
        <div class="flex flex-wrap items-center gap-4 text-xs font-semibold pt-1">
            <div class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-[#10B981]"></span><span class="text-[#1C1C1E]">Present: {{ $summary['present'] }} ({{ $pPct }}%)</span></div>
            <div class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-[#F59E0B]"></span><span class="text-[#1C1C1E]">Late: {{ $summary['late'] }} ({{ $lPct }}%)</span></div>
            <div class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-[#EF4444]"></span><span class="text-[#1C1C1E]">Absent: {{ $summary['absent'] }} ({{ $aPct }}%)</span></div>
            <div class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-[#6366F1]"></span><span class="text-[#1C1C1E]">Leave: {{ $summary['leave'] }} ({{ $lvPct }}%)</span></div>
        </div>
    </div>
    @endif

    <!-- Data Records Table -->
    <div class="bg-white rounded-2xl border border-[#E2E8F0] shadow-sm overflow-hidden">
        <div class="p-4 border-b border-[#E2E8F0] flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-[#F8FAFC]">
            <div class="flex items-center gap-2">
                <i data-lucide="table" class="w-4 h-4 text-[#64748B]"></i>
                <h3 class="text-sm font-extrabold text-[#1C1C1E]">Detailed Attendance Records</h3>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-[#E2E8F0] text-[#475569]">{{ $records->count() }} Records</span>
            </div>

            <span class="text-xs text-[#64748B]">Generated by: <strong class="text-[#1C1C1E]">{{ $summary['generated_by'] }}</strong> ({{ now()->timezone(config('app.timezone', 'Asia/Kolkata'))->format('d M Y, h:i A') }})</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-[#F8FAFC] border-b border-[#E2E8F0] text-[11px] font-bold uppercase tracking-wider text-[#64748B]">
                        <th class="py-3 px-4">Date</th>
                        <th class="py-3 px-4">Employee</th>
                        <th class="py-3 px-4">In Time</th>
                        <th class="py-3 px-4">Out Time</th>
                        <th class="py-3 px-4">Duration</th>
                        <th class="py-3 px-4">Work Category</th>
                        <th class="py-3 px-4">Mode</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4">Notes</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#F1F5F9] text-xs">
                    @forelse($records as $rec)
                    <tr class="hover:bg-[#F8FAFC] transition-colors">
                        <!-- Date -->
                        <td class="py-3 px-4 whitespace-nowrap">
                            <span class="font-extrabold text-[#1C1C1E]">{{ \Carbon\Carbon::parse($rec->date)->format('d M Y') }}</span>
                            <span class="block text-[10px] text-[#94A3B8]">{{ \Carbon\Carbon::parse($rec->date)->format('l') }}</span>
                        </td>

                        <!-- Employee -->
                        <td class="py-3 px-4">
                            @if($rec->employee)
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-full bg-[#FF6B1A]/10 text-[#FF6B1A] flex items-center justify-center font-black text-[11px] shrink-0">
                                        {{ substr($rec->employee->full_name, 0, 1) }}
                                    </div>
                                    <div>
                                        <a href="{{ route('admin.employees.show', $rec->employee->id) }}" class="font-bold text-[#1C1C1E] hover:text-[#FF6B1A] transition-colors">
                                            {{ $rec->employee->full_name }}
                                        </a>
                                        <div class="flex items-center gap-1.5 text-[10px] text-[#64748B]">
                                            <span class="font-mono">{{ $rec->employee->employee_code }}</span>
                                            <span>•</span>
                                            <span>{{ $rec->employee->department }}</span>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <span class="text-[#94A3B8] italic">Unassigned</span>
                            @endif
                        </td>

                        <!-- In Time -->
                        <td class="py-3 px-4 whitespace-nowrap">
                            @if($rec->punch_in)
                                <span class="font-bold text-[#1C1C1E]">{{ \Carbon\Carbon::parse($rec->punch_in)->format('h:i A') }}</span>
                            @else
                                <span class="text-[#94A3B8]">—</span>
                            @endif
                        </td>

                        <!-- Out Time -->
                        <td class="py-3 px-4 whitespace-nowrap">
                            @if($rec->punch_out)
                                <span class="font-bold text-[#1C1C1E]">{{ \Carbon\Carbon::parse($rec->punch_out)->format('h:i A') }}</span>
                            @else
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-[#FEF3C7] text-[#D97706]">Ongoing</span>
                            @endif
                        </td>

                        <!-- Duration -->
                        <td class="py-3 px-4 whitespace-nowrap font-bold text-[#1C1C1E]">
                            {{ $rec->formatted_hours }}
                        </td>

                        <!-- Work Category / Project -->
                        <td class="py-3 px-4 whitespace-nowrap">
                            <span class="px-2.5 py-1 rounded-lg text-[11px] font-semibold bg-[#F1F5F9] text-[#334155] border border-[#E2E8F0]">
                                {{ $rec->work_category ?? 'General Office' }}
                            </span>
                        </td>

                        <!-- Work Mode -->
                        <td class="py-3 px-4 whitespace-nowrap">
                            <span class="text-[10px] uppercase font-bold text-[#64748B]">{{ $rec->work_mode ?? 'OFFICE' }}</span>
                        </td>

                        <!-- Status Badge -->
                        <td class="py-3 px-4 whitespace-nowrap">
                            @if($rec->status === 'PRESENT')
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-[#ECFDF5] text-[#059669] border border-[#A7F3D0]">
                                    PRESENT
                                </span>
                            @elseif($rec->status === 'LATE')
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-[#FFFBEB] text-[#D97706] border border-[#FDE68A]">
                                    LATE
                                </span>
                            @elseif($rec->status === 'ABSENT')
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-[#FEF2F2] text-[#DC2626] border border-[#FECACA]">
                                    ABSENT
                                </span>
                            @elseif($rec->status === 'LEAVE')
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-[#EEF2FF] text-[#4F46E5] border border-[#C7D2FE]">
                                    LEAVE
                                </span>
                            @else
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-[#F1F5F9] text-[#475569]">
                                    {{ $rec->status }}
                                </span>
                            @endif
                        </td>

                        <!-- Notes / Flag -->
                        <td class="py-3 px-4 max-w-xs truncate text-[11px] text-[#64748B]">
                            {{ $rec->notes ?: '—' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="py-12 text-center">
                            <div class="max-w-sm mx-auto text-center space-y-2">
                                <i data-lucide="clipboard-x" class="w-10 h-10 text-[#CBD5E1] mx-auto"></i>
                                <h4 class="font-extrabold text-[#1C1C1E] text-sm">No Attendance Records Found</h4>
                                <p class="text-xs text-[#64748B]">There are no attendance records matching your chosen filters and date range.</p>
                                <a href="{{ route('admin.reports.index') }}" class="inline-block mt-2 text-xs font-bold text-[#FF6B1A] hover:underline">Clear all filters</a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Table Summary Footer -->
        <div class="p-4 bg-[#F8FAFC] border-t border-[#E2E8F0] flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs text-[#64748B]">
            <span>Displaying <strong>{{ $records->count() }}</strong> verified records for <strong>{{ $summary['period'] }}</strong></span>
            <div class="flex items-center gap-4">
                <span>Present: <strong class="text-[#059669]">{{ $summary['present'] }}</strong></span>
                <span>Late: <strong class="text-[#D97706]">{{ $summary['late'] }}</strong></span>
                <span>Absent: <strong class="text-[#DC2626]">{{ $summary['absent'] }}</strong></span>
                <span>Productive Hours: <strong class="text-[#0284C7]">{{ $summary['totalHours'] }}h</strong></span>
            </div>
        </div>
    </div>

</div>
@endsection
