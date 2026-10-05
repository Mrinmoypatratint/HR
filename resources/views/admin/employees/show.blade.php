@extends('layouts.admin')

@section('title', $employee->full_name . ' — Employee Profile')

@section('content')
<div class="space-y-6" x-data="{ currentTab: 'attendance' }">

    <!-- Back to Directory Navigation -->
    <div>
        <a href="{{ route('admin.employees.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#64748B] hover:text-[#FF6B1A] transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span>Back to Employee Directory</span>
        </a>
    </div>

    <!-- Employee Profile Hero Card -->
    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-[#E2E8F0] shadow-sm flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
        <div class="flex items-center gap-5">
            <img src="{{ $employee->photo_url ?: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=256&q=80' }}" class="w-20 h-20 sm:w-24 sm:h-24 rounded-3xl object-cover ring-4 ring-[#FF6B1A]/20 shadow-md" alt="Avatar">
            <div>
                <div class="flex flex-wrap items-center gap-3">
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-[#1C1C1E] tracking-tight">{{ $employee->full_name }}</h1>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider {{ $employee->status === 'ACTIVE' ? 'bg-[#ECFDF5] text-[#059669] border border-[#A7F3D0]' : 'bg-gray-100 text-gray-700' }}">
                        {{ $employee->status }}
                    </span>
                    @if($employee->is_demo)
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-[#EFF6FF] text-[#2563EB] border border-[#BFDBFE]">DEMO EMPLOYEE</span>
                    @endif
                </div>

                <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-[#64748B] mt-2 font-mono">
                    <span>ID: <strong class="text-[#1C1C1E]">{{ $employee->employee_id }}</strong></span>
                    <span>&bull;</span>
                    <span>Code: <strong class="text-[#FF6B1A] font-bold">{{ $employee->employee_code }}</strong></span>
                    <span>&bull;</span>
                    <span>Dept: <strong class="text-[#1C1C1E]">{{ $employee->department }}</strong></span>
                </div>

                <div class="text-xs text-[#64748B] mt-1.5 flex flex-wrap items-center gap-x-4">
                    <span>Role: <strong class="text-[#1C1C1E]">{{ $employee->role }}</strong></span>
                    <span>&bull;</span>
                    <span>Designation: <span class="text-[#1C1C1E]">{{ $employee->designation }}</span></span>
                    <span>&bull;</span>
                    <span>Joined: <span class="text-[#1C1C1E]">{{ $employee->joining_date->format('d M Y') }}</span></span>
                </div>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2.5 self-stretch sm:self-auto justify-end">
            @if($employee->email)
            <form method="POST" action="{{ route('admin.employees.send-password-link', $employee->id) }}" onsubmit="return confirm('Send activation/password setup link to {{ $employee->email }}?');">
                @csrf
                <button type="submit" class="px-4 py-2.5 rounded-xl bg-indigo-50 border border-indigo-200 text-xs font-bold text-indigo-700 hover:bg-indigo-100 transition-all flex items-center gap-1.5 shadow-sm">
                    <i data-lucide="send" class="w-4 h-4"></i>
                    <span>Email Password Link</span>
                </button>
            </form>
            @endif

            <form method="POST" action="{{ route('admin.employees.toggle', $employee->id) }}">
                @csrf
                <button type="submit" class="px-4 py-2.5 rounded-xl border border-[#CBD5E1] text-xs font-bold {{ $employee->status === 'ACTIVE' ? 'text-amber-700 hover:bg-amber-50' : 'text-emerald-700 hover:bg-emerald-50' }} transition-all flex items-center gap-1.5">
                    <i data-lucide="{{ $employee->status === 'ACTIVE' ? 'user-minus' : 'user-check' }}" class="w-4 h-4"></i>
                    <span>{{ $employee->status === 'ACTIVE' ? 'Deactivate' : 'Activate' }}</span>
                </button>
            </form>
        </div>
    </div>

    @if(session('sent_activation_url'))
        <div class="p-4 rounded-xl bg-amber-50 border border-amber-300 text-amber-900 text-xs flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-sm">
            <div class="flex items-center gap-2">
                <i data-lucide="key" class="w-4 h-4 text-amber-600 shrink-0"></i>
                <span><strong>Employee Setup Link:</strong> <code class="font-mono text-[11px] text-amber-800 break-all">{{ session('sent_activation_url') }}</code></span>
            </div>
            <button type="button" onclick="navigator.clipboard.writeText('{{ session('sent_activation_url') }}'); alert('Activation Link Copied to Clipboard!');" class="px-3 py-1.5 rounded-lg bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs shrink-0 flex items-center gap-1">
                <i data-lucide="copy" class="w-3.5 h-3.5"></i>
                <span>Copy Link</span>
            </button>
        </div>
    @endif

    <!-- Stat KPI Tiles -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
        <div class="p-4 rounded-2xl bg-white border border-[#E2E8F0] shadow-sm text-center">
            <span class="text-[10px] font-bold uppercase text-[#64748B]">Logged Shifts</span>
            <p class="text-xl font-extrabold text-[#1C1C1E] mt-1">{{ $stats['totalDays'] }}</p>
        </div>
        <div class="p-4 rounded-2xl bg-white border border-[#E2E8F0] shadow-sm text-center">
            <span class="text-[10px] font-bold uppercase text-[#059669]">Present</span>
            <p class="text-xl font-extrabold text-[#059669] mt-1">{{ $stats['presentCount'] }}</p>
        </div>
        <div class="p-4 rounded-2xl bg-white border border-[#E2E8F0] shadow-sm text-center">
            <span class="text-[10px] font-bold uppercase text-[#D97706]">Late</span>
            <p class="text-xl font-extrabold text-[#D97706] mt-1">{{ $stats['lateCount'] }}</p>
        </div>
        <div class="p-4 rounded-2xl bg-white border border-[#E2E8F0] shadow-sm text-center">
            <span class="text-[10px] font-bold uppercase text-[#DC2626]">Absent</span>
            <p class="text-xl font-extrabold text-[#DC2626] mt-1">{{ $stats['absentCount'] }}</p>
        </div>
        <div class="p-4 rounded-2xl bg-white border border-[#E2E8F0] shadow-sm text-center">
            <span class="text-[10px] font-bold uppercase text-[#2563EB]">Leave</span>
            <p class="text-xl font-extrabold text-[#2563EB] mt-1">{{ $stats['leaveCount'] }}</p>
        </div>
        <div class="p-4 rounded-2xl bg-[#FFF3EB] border border-[#FFD4BD] shadow-sm text-center">
            <span class="text-[10px] font-bold uppercase text-[#FF6B1A]">Rate %</span>
            <p class="text-xl font-extrabold text-[#FF6B1A] mt-1">{{ $stats['rate'] }}%</p>
        </div>
    </div>

    <!-- Tabbed Content Section -->
    <div class="bg-white rounded-3xl border border-[#E2E8F0] shadow-sm overflow-hidden">
        <!-- Tab Navigation Buttons -->
        <div class="flex items-center border-b border-[#E2E8F0] px-6 bg-[#F8FAFC]">
            <button type="button" @click="currentTab = 'attendance'" :class="currentTab === 'attendance' ? 'border-[#FF6B1A] text-[#FF6B1A] font-bold' : 'border-transparent text-[#64748B] hover:text-[#1C1C1E]'" class="py-4 px-4 text-xs font-semibold border-b-2 transition-all flex items-center gap-2">
                <i data-lucide="calendar" class="w-4 h-4"></i>
                <span>Attendance Log (Last 60 Days)</span>
            </button>

            <button type="button" @click="currentTab = 'projects'" :class="currentTab === 'projects' ? 'border-[#FF6B1A] text-[#FF6B1A] font-bold' : 'border-transparent text-[#64748B] hover:text-[#1C1C1E]'" class="py-4 px-4 text-xs font-semibold border-b-2 transition-all flex items-center gap-2">
                <i data-lucide="briefcase" class="w-4 h-4"></i>
                <span>Assigned Projects ({{ $employee->projects->count() }})</span>
            </button>

            <button type="button" @click="currentTab = 'edit'" :class="currentTab === 'edit' ? 'border-[#FF6B1A] text-[#FF6B1A] font-bold' : 'border-transparent text-[#64748B] hover:text-[#1C1C1E]'" class="py-4 px-4 text-xs font-semibold border-b-2 transition-all flex items-center gap-2">
                <i data-lucide="edit-3" class="w-4 h-4"></i>
                <span>Edit Profile Information</span>
            </button>
        </div>

        <!-- TAB 1: Attendance History Table -->
        <div x-show="currentTab === 'attendance'" class="p-6">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-[#F8FAFC] border-b border-[#E2E8F0] text-[10px] uppercase font-bold text-[#64748B]">
                            <th class="py-3 px-4">Date</th>
                            <th class="py-3 px-4">Check-in</th>
                            <th class="py-3 px-4">Check-out</th>
                            <th class="py-3 px-4">Duration</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4">Project Category</th>
                            <th class="py-3 px-4">Task Description</th>
                            <th class="py-3 px-4">Audit Stamp</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#F1F5F9]">
                        @forelse($employee->attendances as $att)
                        <tr class="hover:bg-[#FFF3EB]/30 transition-colors">
                            <td class="py-3 px-4 font-mono font-bold text-[#1C1C1E]">{{ $att->date->format('Y-m-d') }}</td>
                            <td class="py-3 px-4 font-mono">{{ $att->check_in_time }}</td>
                            <td class="py-3 px-4 font-mono">{{ $att->check_out_time ?: '-' }}</td>
                            <td class="py-3 px-4 font-mono font-bold">{{ $att->working_hours_formatted }}</td>
                            <td class="py-3 px-4">
                                @if($att->status === 'PRESENT')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-[#ECFDF5] text-[#059669]">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#10B981]"></span> Present
                                    </span>
                                @elseif($att->status === 'LATE')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-[#FFFBEB] text-[#D97706]">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#F59E0B]"></span> Late
                                    </span>
                                @elseif($att->status === 'ABSENT')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-[#FEF2F2] text-[#DC2626]">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#EF4444]"></span> Absent
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-[#EFF6FF] text-[#2563EB]">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#3B82F6]"></span> Leave
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4 font-semibold text-[#1C1C1E]">{{ $att->work_category }}</td>
                            <td class="py-3 px-4 text-[#64748B] max-w-[200px] truncate">{{ $att->task_description ?: '-' }}</td>
                            <td class="py-3 px-4 text-[10px] text-[#94A3B8]">{{ $att->modified_by ?: 'Kiosk Terminal' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-8 text-[#94A3B8]">No shift records found for this employee.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- TAB 2: Assigned Projects -->
        <div x-show="currentTab === 'projects'" x-cloak class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @forelse($employee->projects as $prj)
                <div class="p-4 rounded-2xl bg-[#F8FAFC] border border-[#E2E8F0] space-y-2">
                    <div class="flex items-center justify-between">
                        <strong class="text-sm text-[#1C1C1E]">{{ $prj->name }}</strong>
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-[#FFF3EB] text-[#FF6B1A]">{{ $prj->status }}</span>
                    </div>
                    <p class="text-xs text-[#64748B]">{{ $prj->description }}</p>
                    <div class="pt-2 border-t border-[#E2E8F0] flex items-center justify-between text-xs">
                        <span class="text-[#64748B]">Assigned Role: <strong class="text-[#1C1C1E]">{{ $prj->pivot->role }}</strong></span>
                        <span class="text-[#94A3B8] font-mono text-[11px]">{{ $prj->project_id }}</span>
                    </div>
                </div>
                @empty
                <div class="col-span-full text-center py-8 text-[#94A3B8]">
                    <p>No project assignments recorded for this employee.</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- TAB 3: Edit Profile Form -->
        <div x-show="currentTab === 'edit'" x-cloak class="p-6">
            <form method="POST" action="{{ route('admin.employees.update', $employee->id) }}" class="space-y-4 text-xs max-w-2xl">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-[#64748B] uppercase mb-1">Full Name *</label>
                        <input type="text" name="full_name" value="{{ $employee->full_name }}" required class="w-full px-3 py-2 rounded-xl bg-[#F8FAFC] border border-[#CBD5E1] text-[#1C1C1E]">
                    </div>

                    <div>
                        <label class="block font-bold text-[#64748B] uppercase mb-1">Email Address</label>
                        <input type="email" name="email" value="{{ $employee->email }}" class="w-full px-3 py-2 rounded-xl bg-[#F8FAFC] border border-[#CBD5E1] text-[#1C1C1E]">
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="block font-bold text-[#64748B] uppercase mb-1">Department *</label>
                        <input type="text" name="department" value="{{ $employee->department }}" required class="w-full px-3 py-2 rounded-xl bg-[#F8FAFC] border border-[#CBD5E1] text-[#1C1C1E]">
                    </div>

                    <div>
                        <label class="block font-bold text-[#64748B] uppercase mb-1">Designation *</label>
                        <input type="text" name="designation" value="{{ $employee->designation }}" required class="w-full px-3 py-2 rounded-xl bg-[#F8FAFC] border border-[#CBD5E1] text-[#1C1C1E]">
                    </div>

                    <div>
                        <label class="block font-bold text-[#64748B] uppercase mb-1">Role *</label>
                        <input type="text" name="role" value="{{ $employee->role }}" required class="w-full px-3 py-2 rounded-xl bg-[#F8FAFC] border border-[#CBD5E1] text-[#1C1C1E]">
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="block font-bold text-[#64748B] uppercase mb-1">Joining Date *</label>
                        <input type="date" name="joining_date" value="{{ $employee->joining_date->format('Y-m-d') }}" required class="w-full px-3 py-2 rounded-xl bg-[#F8FAFC] border border-[#CBD5E1] text-[#1C1C1E]">
                    </div>

                    <div>
                        <label class="block font-bold text-[#64748B] uppercase mb-1">Employment Type *</label>
                        <select name="employment_type" required class="w-full px-3 py-2 rounded-xl bg-[#F8FAFC] border border-[#CBD5E1] text-[#1C1C1E]">
                            <option value="Full-time" {{ $employee->employment_type == 'Full-time' ? 'selected' : '' }}>Full-time</option>
                            <option value="Part-time" {{ $employee->employment_type == 'Part-time' ? 'selected' : '' }}>Part-time</option>
                            <option value="Contract" {{ $employee->employment_type == 'Contract' ? 'selected' : '' }}>Contract</option>
                            <option value="Intern" {{ $employee->employment_type == 'Intern' ? 'selected' : '' }}>Intern</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-[#64748B] uppercase mb-1">Status *</label>
                        <select name="status" required class="w-full px-3 py-2 rounded-xl bg-[#F8FAFC] border border-[#CBD5E1] text-[#1C1C1E]">
                            <option value="ACTIVE" {{ $employee->status == 'ACTIVE' ? 'selected' : '' }}>ACTIVE</option>
                            <option value="INACTIVE" {{ $employee->status == 'INACTIVE' ? 'selected' : '' }}>INACTIVE</option>
                            <option value="ON_NOTICE" {{ $employee->status == 'ON_NOTICE' ? 'selected' : '' }}>ON NOTICE</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-[#64748B] uppercase mb-1">Assigned Project</label>
                        <select name="assigned_project" class="w-full px-3 py-2 rounded-xl bg-[#F8FAFC] border border-[#CBD5E1] text-[#1C1C1E]">
                            <option value="">-- None / General --</option>
                            @foreach($projects as $p)
                                <option value="{{ $p->name }}" {{ $employee->assigned_project == $p->name ? 'selected' : '' }}>{{ $p->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-[#64748B] uppercase mb-1">Mobile Number</label>
                        <input type="text" name="mobile" value="{{ $employee->mobile }}" class="w-full px-3 py-2 rounded-xl bg-[#F8FAFC] border border-[#CBD5E1] text-[#1C1C1E]">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-[#64748B] uppercase mb-1">Emergency Contact</label>
                        <input type="text" name="emergency_contact" value="{{ $employee->emergency_contact }}" class="w-full px-3 py-2 rounded-xl bg-[#F8FAFC] border border-[#CBD5E1] text-[#1C1C1E]">
                    </div>

                    <div>
                        <label class="block font-bold text-[#64748B] uppercase mb-1">Photo URL</label>
                        <input type="text" name="photo_url" value="{{ $employee->photo_url }}" class="w-full px-3 py-2 rounded-xl bg-[#F8FAFC] border border-[#CBD5E1] text-[#1C1C1E]">
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-[#64748B] uppercase mb-1">Address</label>
                    <input type="text" name="address" value="{{ $employee->address }}" class="w-full px-3 py-2 rounded-xl bg-[#F8FAFC] border border-[#CBD5E1] text-[#1C1C1E]">
                </div>

                <div>
                    <label class="block font-bold text-[#64748B] uppercase mb-1">Internal Notes</label>
                    <textarea name="notes" rows="2" class="w-full p-2.5 rounded-xl bg-[#F8FAFC] border border-[#CBD5E1] text-[#1C1C1E]">{{ $employee->notes }}</textarea>
                </div>

                <div class="pt-3">
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#FF6B1A] text-white font-bold shadow-md">Save Profile Updates</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
