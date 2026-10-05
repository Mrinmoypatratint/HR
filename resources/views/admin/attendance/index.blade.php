@extends('layouts.admin')

@section('title', 'Attendance Management Ledger')

@section('content')
<div class="space-y-6" x-data="{ manualModal: false, editModal: false, bulkModal: false, activeEdit: {} }">

    <!-- Top Action Header -->
    <div class="bg-white p-5 rounded-2xl border border-[#E2E8F0] shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-2xl font-extrabold text-[#1C1C1E] tracking-tight">Attendance Management Ledger</h1>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-[#ECFDF5] text-[#059669] border border-[#A7F3D0]">Real-Time Sync</span>
            </div>
            <p class="text-xs text-[#64748B] mt-0.5">Comprehensive workforce punch ledger, time audits, and manual adjustment logs.</p>
        </div>

        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 w-full md:w-auto">
            <button type="button" @click="bulkModal = true" class="w-full sm:w-auto justify-center px-3.5 py-2.5 rounded-xl bg-white hover:bg-[#F8FAFC] text-[#1E293B] border border-[#CBD5E1] text-xs font-bold shadow-sm transition-all flex items-center gap-1.5">
                <i data-lucide="upload-cloud" class="w-4 h-4 text-[#2563EB]"></i>
                <span>Bulk Upload (CSV/Excel)</span>
            </button>

            <button type="button" @click="manualModal = true" class="w-full sm:w-auto justify-center px-3.5 py-2.5 rounded-xl bg-[#FF6B1A] hover:bg-[#E55607] text-white text-xs font-bold shadow-md shadow-[#FF6B1A]/20 transition-all flex items-center gap-1.5">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>+ Manual Attendance</span>
            </button>
        </div>
    </div>

    <!-- Bulk Upload Results Panel (If just uploaded) -->
    @if(session('bulk_result'))
    @php $res = session('bulk_result'); @endphp
    <div class="bg-white rounded-2xl border border-[#E2E8F0] p-5 shadow-sm space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="font-extrabold text-sm text-[#1C1C1E] flex items-center gap-2">
                <i data-lucide="file-check" class="w-4 h-4 text-[#16A34A]"></i>
                <span>Bulk Upload Processing Summary</span>
            </h3>
            <span class="text-xs text-[#64748B]">Audit Stamp Created</span>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 text-center">
            <div class="p-3 rounded-xl bg-[#ECFDF5] border border-[#A7F3D0]">
                <span class="text-[10px] uppercase font-bold text-[#059669]">Successful</span>
                <p class="text-lg font-extrabold text-[#059669]">{{ count($res['successful']) }}</p>
            </div>
            <div class="p-3 rounded-xl bg-[#FFFBEB] border border-[#FDE68A]">
                <span class="text-[10px] uppercase font-bold text-[#D97706]">Duplicates</span>
                <p class="text-lg font-extrabold text-[#D97706]">{{ count($res['duplicates']) }}</p>
            </div>
            <div class="p-3 rounded-xl bg-[#FEF2F2] border border-[#FECACA]">
                <span class="text-[10px] uppercase font-bold text-[#DC2626]">Invalid Codes</span>
                <p class="text-lg font-extrabold text-[#DC2626]">{{ count($res['invalidCodes']) }}</p>
            </div>
            <div class="p-3 rounded-xl bg-[#FEF2F2] border border-[#FECACA]">
                <span class="text-[10px] uppercase font-bold text-[#DC2626]">Invalid Dates</span>
                <p class="text-lg font-extrabold text-[#DC2626]">{{ count($res['invalidDates']) }}</p>
            </div>
            <div class="p-3 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0]">
                <span class="text-[10px] uppercase font-bold text-[#64748B]">Other Errors</span>
                <p class="text-lg font-extrabold text-[#64748B]">{{ count($res['errors']) }}</p>
            </div>
        </div>
    </div>
    @endif

    <!-- Search & Multi-Filter Bar -->
    <div class="bg-white p-5 rounded-2xl border border-[#E2E8F0] shadow-sm">
        <form method="GET" action="{{ route('admin.attendance.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3">
            <!-- Search -->
            <div class="lg:col-span-2 relative">
                <div class="absolute left-3 top-1/2 -translate-y-1/2 text-[#94A3B8]">
                    <i data-lucide="search" class="w-4 h-4"></i>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search employee, ID, code, role..." class="w-full text-xs pl-9 pr-3 py-2.5 rounded-xl bg-[#F8FAFC] border border-[#CBD5E1] text-[#1C1C1E] focus:border-[#FF6B1A] focus:ring-2 focus:ring-[#FF6B1A]/20">
            </div>

            <!-- Department Filter -->
            <div>
                <select name="department" class="w-full text-xs px-3 py-2.5 rounded-xl bg-[#F8FAFC] border border-[#CBD5E1] text-[#1C1C1E] focus:border-[#FF6B1A]">
                    <option value="">All Departments</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept }}" {{ request('department') == $dept ? 'selected' : '' }}>{{ $dept }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Status Filter -->
            <div>
                <select name="status" class="w-full text-xs px-3 py-2.5 rounded-xl bg-[#F8FAFC] border border-[#CBD5E1] text-[#1C1C1E] focus:border-[#FF6B1A]">
                    <option value="">All Statuses</option>
                    <option value="PRESENT" {{ request('status') == 'PRESENT' ? 'selected' : '' }}>Present (Green)</option>
                    <option value="LATE" {{ request('status') == 'LATE' ? 'selected' : '' }}>Late (Orange)</option>
                    <option value="ABSENT" {{ request('status') == 'ABSENT' ? 'selected' : '' }}>Absent (Red)</option>
                    <option value="LEAVE" {{ request('status') == 'LEAVE' ? 'selected' : '' }}>Leave (Blue)</option>
                </select>
            </div>

            <!-- Date Range -->
            <div class="flex items-center gap-1.5">
                <input type="date" name="start_date" value="{{ request('start_date') }}" title="Start Date" class="w-full text-xs px-2 py-2 rounded-xl bg-[#F8FAFC] border border-[#CBD5E1] text-[#1C1C1E]">
                <span class="text-xs text-[#94A3B8]">&ndash;</span>
                <input type="date" name="end_date" value="{{ request('end_date') }}" title="End Date" class="w-full text-xs px-2 py-2 rounded-xl bg-[#F8FAFC] border border-[#CBD5E1] text-[#1C1C1E]">
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 py-2.5 rounded-xl bg-[#1C1C1E] hover:bg-black text-white text-xs font-bold transition-all shadow-sm">
                    Filter
                </button>
                <a href="{{ route('admin.attendance.index') }}" class="p-2.5 rounded-xl bg-[#F1F5F9] hover:bg-[#E2E8F0] text-[#64748B] transition-all" title="Reset Filters">
                    <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Attendance Table -->
    <div class="bg-white rounded-2xl border border-[#E2E8F0] shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[900px] whitespace-nowrap">
                <thead>
                    <tr class="bg-[#F8FAFC] border-b border-[#E2E8F0] text-[10px] uppercase font-bold text-[#64748B]">
                        <th class="py-3.5 px-4">Date</th>
                        <th class="py-3.5 px-4">Employee</th>
                        <th class="py-3.5 px-4">Department &amp; Role</th>
                        <th class="py-3.5 px-4">Check-in</th>
                        <th class="py-3.5 px-4">Check-out</th>
                        <th class="py-3.5 px-4">Working Hours</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-4">Project / Task</th>
                        <th class="py-3.5 px-4">Audit Stamp</th>
                        <th class="py-3.5 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#F1F5F9] text-xs">
                    @forelse($attendances as $row)
                    <tr class="hover:bg-[#FFF3EB]/30 transition-colors">
                        <td class="py-3.5 px-4 font-mono font-bold text-[#1C1C1E] whitespace-nowrap">
                            {{ $row->date->format('Y-m-d') }}
                            <span class="block text-[10px] font-normal text-[#64748B]">{{ $row->date->format('D, d M') }}</span>
                        </td>

                        <td class="py-3.5 px-4 flex items-center gap-3">
                            <img src="{{ $row->employee->photo_url ?: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=256&q=80' }}" class="w-8 h-8 rounded-full object-cover ring-1 ring-[#CBD5E1]" alt="Avatar">
                            <div>
                                <a href="{{ route('admin.employees.show', $row->employee_id) }}" class="font-bold text-[#1C1C1E] hover:text-[#FF6B1A] block">
                                    {{ $row->employee->full_name }}
                                </a>
                                <span class="text-[10px] font-mono text-[#FF6B1A]">{{ $row->employee->employee_code }}</span>
                            </div>
                        </td>

                        <td class="py-3.5 px-4">
                            <span class="font-semibold text-[#1C1C1E] block">{{ $row->employee->department }}</span>
                            <span class="text-[11px] text-[#64748B]">{{ $row->employee->role }}</span>
                        </td>

                        <td class="py-3.5 px-4 font-mono font-medium text-[#1E293B]">
                            {{ $row->check_in_time }}
                        </td>

                        <td class="py-3.5 px-4 font-mono font-medium text-[#1E293B]">
                            {{ $row->check_out_time ?: '-' }}
                        </td>

                        <td class="py-3.5 px-4 font-mono font-bold text-[#1C1C1E]">
                            {{ $row->working_hours_formatted }}
                        </td>

                        <td class="py-3.5 px-4">
                            @if($row->status === 'PRESENT')
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-[#ECFDF5] text-[#059669]">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#10B981]"></span> Present
                                </span>
                            @elseif($row->status === 'LATE')
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-[#FFFBEB] text-[#D97706]">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#F59E0B]"></span> Late
                                </span>
                            @elseif($row->status === 'ABSENT')
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-[#FEF2F2] text-[#DC2626]">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#EF4444]"></span> Absent
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-[#EFF6FF] text-[#2563EB]">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#3B82F6]"></span> Leave
                                </span>
                            @endif
                        </td>

                        <td class="py-3.5 px-4 max-w-[200px]">
                            <strong class="text-[11px] text-[#1E293B] block">[{{ $row->work_category }}]</strong>
                            @if($row->task_description)
                                <span class="text-[11px] text-[#64748B] truncate block" title="{{ $row->task_description }}">{{ Str::limit($row->task_description, 35) }}</span>
                            @endif
                        </td>

                        <td class="py-3.5 px-4 text-[10px] text-[#64748B]">
                            @if($row->modified_by)
                                <span>{{ $row->modified_by }}</span>
                                <span class="block text-[#94A3B8]">{{ $row->modified_at?->diffForHumans() }}</span>
                            @else
                                <span>Kiosk Terminal</span>
                            @endif
                        </td>

                        <td class="py-3.5 px-4 text-right whitespace-nowrap">
                            <div class="flex items-center justify-end gap-1.5">
                                <button type="button" @click="activeEdit = {
                                    id: '{{ $row->id }}',
                                    name: '{{ addslashes($row->employee->full_name) }}',
                                    code: '{{ $row->employee->employee_code }}',
                                    date: '{{ $row->date->format('Y-m-d') }}',
                                    check_in: '{{ $row->check_in_time }}',
                                    check_out: '{{ $row->check_out_time }}',
                                    status: '{{ $row->status }}',
                                    category: '{{ $row->work_category }}',
                                    task: '{{ addslashes($row->task_description) }}',
                                    remarks: '{{ addslashes($row->remarks) }}'
                                }; editModal = true" class="p-1.5 rounded-lg text-[#64748B] hover:text-[#1C1C1E] hover:bg-[#F1F5F9]" title="Edit Record">
                                    <i data-lucide="edit-3" class="w-4 h-4"></i>
                                </button>

                                <form method="POST" action="{{ route('admin.attendance.destroy', $row->id) }}" onsubmit="return confirm('Are you sure you want to delete this attendance record?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 rounded-lg text-red-500 hover:text-red-700 hover:bg-red-50" title="Delete">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center py-12 text-[#94A3B8]">
                            <div class="w-12 h-12 mx-auto rounded-full bg-[#F1F5F9] flex items-center justify-center mb-2">
                                <i data-lucide="clipboard-x" class="w-6 h-6 text-[#94A3B8]"></i>
                            </div>
                            <p class="font-bold text-sm text-[#1C1C1E]">No Attendance Records Found</p>
                            <p class="text-xs text-[#64748B] mt-1">No attendance records found for the selected period or filters.</p>
                            <a href="{{ route('admin.attendance.index') }}" class="mt-3 inline-block px-3 py-1.5 rounded-lg bg-[#FF6B1A] text-white text-xs font-bold">Clear Filters</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($attendances->hasPages())
        <div class="p-4 border-t border-[#E2E8F0]">
            {{ $attendances->links() }}
        </div>
        @endif
    </div>

    <!-- Modal: Manual Attendance Add -->
    <div x-show="manualModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div @click="manualModal = false" class="fixed inset-0 bg-black/60 backdrop-blur-sm"></div>
        <div class="relative w-full max-w-lg bg-white rounded-2xl sm:rounded-3xl shadow-2xl border border-[#E2E8F0] p-5 sm:p-6 z-10 space-y-4 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between pb-3 border-b border-[#E2E8F0]">
                <h3 class="font-extrabold text-base text-[#1C1C1E] flex items-center gap-2">
                    <i data-lucide="plus-circle" class="w-5 h-5 text-[#FF6B1A]"></i>
                    <span>+ Add Manual Attendance</span>
                </h3>
                <button @click="manualModal = false" class="text-[#94A3B8] hover:text-[#1C1C1E]">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form method="POST" action="{{ route('admin.attendance.store') }}" class="space-y-4 text-xs">
                @csrf

                <div>
                    <label class="block font-bold text-[#64748B] uppercase mb-1">Select Employee *</label>
                    <select name="employee_id" required class="w-full px-3 py-2.5 rounded-xl bg-[#F8FAFC] border border-[#CBD5E1] text-[#1C1C1E]">
                        <option value="">-- Choose Employee --</option>
                        @foreach($allEmployees as $emp)
                            <option value="{{ $emp->id }}">{{ $emp->full_name }} ({{ $emp->employee_code }} &bull; {{ $emp->department }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-[#64748B] uppercase mb-1">Date *</label>
                        <input type="date" name="date" value="{{ date('Y-m-d') }}" required class="w-full px-3 py-2 rounded-xl bg-[#F8FAFC] border border-[#CBD5E1] text-[#1C1C1E]">
                    </div>

                    <div>
                        <label class="block font-bold text-[#64748B] uppercase mb-1">Status *</label>
                        <select name="status" required class="w-full px-3 py-2 rounded-xl bg-[#F8FAFC] border border-[#CBD5E1] text-[#1C1C1E]">
                            <option value="PRESENT">PRESENT (On-Time)</option>
                            <option value="LATE">LATE (Delay)</option>
                            <option value="ABSENT">ABSENT</option>
                            <option value="LEAVE">ON LEAVE</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-[#64748B] uppercase mb-1">Check-in Time *</label>
                        <input type="text" name="check_in_time" value="09:30 AM" required placeholder="09:30 AM" class="w-full font-mono px-3 py-2 rounded-xl bg-[#F8FAFC] border border-[#CBD5E1] text-[#1C1C1E]">
                    </div>

                    <div>
                        <label class="block font-bold text-[#64748B] uppercase mb-1">Check-out Time</label>
                        <input type="text" name="check_out_time" placeholder="06:30 PM" class="w-full font-mono px-3 py-2 rounded-xl bg-[#F8FAFC] border border-[#CBD5E1] text-[#1C1C1E]">
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-[#64748B] uppercase mb-1">Work Category</label>
                    <select name="work_category" class="w-full px-3 py-2 rounded-xl bg-[#F8FAFC] border border-[#CBD5E1] text-[#1C1C1E]">
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}">{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-[#64748B] uppercase mb-1">Task Description / Details</label>
                    <textarea name="task_description" rows="2" placeholder="Manual attendance addition note..." class="w-full p-2.5 rounded-xl bg-[#F8FAFC] border border-[#CBD5E1] text-[#1C1C1E]"></textarea>
                </div>

                <div>
                    <label class="block font-bold text-[#64748B] uppercase mb-1">Administrative Remarks</label>
                    <input type="text" name="remarks" placeholder="Approved by HR Manager" class="w-full px-3 py-2 rounded-xl bg-[#F8FAFC] border border-[#CBD5E1] text-[#1C1C1E]">
                </div>

                <div class="pt-2 flex gap-3">
                    <button type="button" @click="manualModal = false" class="flex-1 py-2.5 rounded-xl border border-[#CBD5E1] font-bold text-[#64748B]">Cancel</button>
                    <button type="submit" class="flex-1 py-2.5 rounded-xl bg-[#FF6B1A] text-white font-bold shadow-md">Save Record</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Edit Attendance -->
    <div x-show="editModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div @click="editModal = false" class="fixed inset-0 bg-black/60 backdrop-blur-sm"></div>
        <div class="relative w-full max-w-lg bg-white rounded-2xl sm:rounded-3xl shadow-2xl border border-[#E2E8F0] p-5 sm:p-6 z-10 space-y-4 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between pb-3 border-b border-[#E2E8F0]">
                <h3 class="font-extrabold text-base text-[#1C1C1E] flex items-center gap-2">
                    <i data-lucide="edit-3" class="w-5 h-5 text-[#FF6B1A]"></i>
                    <span>Edit Attendance Record</span>
                </h3>
                <button @click="editModal = false" class="text-[#94A3B8] hover:text-[#1C1C1E]">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form :action="'{{ url('admin/attendance') }}/' + activeEdit.id" method="POST" class="space-y-4 text-xs">
                @csrf
                @method('PUT')

                <div class="p-3 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0] flex items-center justify-between">
                    <div>
                        <strong class="text-sm text-[#1C1C1E] block" x-text="activeEdit.name"></strong>
                        <span class="text-[#64748B] font-mono" x-text="activeEdit.code"></span>
                    </div>
                    <span class="font-mono text-xs font-bold text-[#1C1C1E]" x-text="activeEdit.date"></span>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-[#64748B] uppercase mb-1">Check-in Time *</label>
                        <input type="text" name="check_in_time" x-model="activeEdit.check_in" required class="w-full font-mono px-3 py-2 rounded-xl bg-[#F8FAFC] border border-[#CBD5E1] text-[#1C1C1E]">
                    </div>

                    <div>
                        <label class="block font-bold text-[#64748B] uppercase mb-1">Check-out Time</label>
                        <input type="text" name="check_out_time" x-model="activeEdit.check_out" class="w-full font-mono px-3 py-2 rounded-xl bg-[#F8FAFC] border border-[#CBD5E1] text-[#1C1C1E]">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-[#64748B] uppercase mb-1">Status *</label>
                        <select name="status" x-model="activeEdit.status" required class="w-full px-3 py-2 rounded-xl bg-[#F8FAFC] border border-[#CBD5E1] text-[#1C1C1E]">
                            <option value="PRESENT">PRESENT</option>
                            <option value="LATE">LATE</option>
                            <option value="ABSENT">ABSENT</option>
                            <option value="LEAVE">ON LEAVE</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-[#64748B] uppercase mb-1">Work Category</label>
                        <select name="work_category" x-model="activeEdit.category" class="w-full px-3 py-2 rounded-xl bg-[#F8FAFC] border border-[#CBD5E1] text-[#1C1C1E]">
                            @foreach($categories as $cat)
                                <option value="{{ $cat }}">{{ $cat }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-[#64748B] uppercase mb-1">Task Description</label>
                    <textarea name="task_description" x-model="activeEdit.task" rows="2" class="w-full p-2.5 rounded-xl bg-[#F8FAFC] border border-[#CBD5E1] text-[#1C1C1E]"></textarea>
                </div>

                <div>
                    <label class="block font-bold text-[#64748B] uppercase mb-1">Reason / Remarks</label>
                    <input type="text" name="remarks" x-model="activeEdit.remarks" placeholder="Adjustment reason..." class="w-full px-3 py-2 rounded-xl bg-[#F8FAFC] border border-[#CBD5E1] text-[#1C1C1E]">
                </div>

                <div class="pt-2 flex gap-3">
                    <button type="button" @click="editModal = false" class="flex-1 py-2.5 rounded-xl border border-[#CBD5E1] font-bold text-[#64748B]">Cancel</button>
                    <button type="submit" class="flex-1 py-2.5 rounded-xl bg-[#FF6B1A] text-white font-bold shadow-md">Update &amp; Audit</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Bulk Upload CSV / Excel -->
    <div x-show="bulkModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div @click="bulkModal = false" class="fixed inset-0 bg-black/60 backdrop-blur-sm"></div>
        <div class="relative w-full max-w-lg bg-white rounded-2xl sm:rounded-3xl shadow-2xl border border-[#E2E8F0] p-5 sm:p-6 z-10 space-y-4 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between pb-3 border-b border-[#E2E8F0]">
                <h3 class="font-extrabold text-base text-[#1C1C1E] flex items-center gap-2">
                    <i data-lucide="upload-cloud" class="w-5 h-5 text-[#2563EB]"></i>
                    <span>Bulk Upload Attendance (CSV / Excel)</span>
                </h3>
                <button @click="bulkModal = false" class="text-[#94A3B8] hover:text-[#1C1C1E]">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <p class="text-xs text-[#64748B] leading-relaxed">
                Upload a <code>.csv</code> or <code>.xlsx</code> file containing employee shift punches. The system validates employee codes, validates dates (<code>YYYY-MM-DD</code>), prevents duplicate punches, and generates a breakdown report.
            </p>

            <div class="p-3 bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl text-xs space-y-1 text-[#475569]">
                <span class="font-bold text-[#1C1C1E] block">Required Column Headers:</span>
                <code class="text-[11px] text-[#2563EB] block">Employee Code, Date, Check-in, Check-out, Status, Project, Remarks</code>
            </div>

            <form method="POST" action="{{ route('admin.attendance.bulk') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div class="border-2 border-dashed border-[#CBD5E1] rounded-2xl p-6 text-center hover:border-[#FF6B1A] transition-all bg-[#FAFAF8]">
                    <i data-lucide="file-spreadsheet" class="w-10 h-10 mx-auto text-[#94A3B8] mb-2"></i>
                    <label class="block text-xs font-bold text-[#1C1C1E] cursor-pointer">
                        <span>Select file or drag &amp; drop</span>
                        <input type="file" name="file" accept=".csv,.xlsx,.xls" required class="hidden" onchange="document.getElementById('fileNameDisplay').innerText = this.files[0]?.name || ''">
                    </label>
                    <span id="fileNameDisplay" class="font-mono text-xs text-[#FF6B1A] font-bold block mt-2"></span>
                </div>

                <div class="flex gap-3">
                    <button type="button" @click="bulkModal = false" class="flex-1 py-2.5 rounded-xl border border-[#CBD5E1] font-bold text-xs text-[#64748B]">Cancel</button>
                    <button type="submit" class="flex-1 py-2.5 rounded-xl bg-[#2563EB] hover:bg-[#1D4ED8] text-white font-bold text-xs shadow-md">Process Upload</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
