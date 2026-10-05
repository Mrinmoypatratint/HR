@extends('layouts.admin')

@section('title', 'Employee Directory')

@section('content')
<div class="space-y-6" x-data="{ addModal: false, editModal: false, activeEdit: {} }">

    <!-- Top Action Header -->
    <div class="bg-white p-5 rounded-2xl border border-[#E2E8F0] shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-2xl font-extrabold text-[#1C1C1E] tracking-tight">Employee Directory &amp; Roster</h1>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-[#FFF3EB] text-[#FF6B1A] border border-[#FFD4BD]">Active Workforce</span>
            </div>
            <p class="text-xs text-[#64748B] mt-0.5">Manage digital employee profiles, department roles, shift assignments, and credentials.</p>
        </div>

        <button type="button" @click="addModal = true" class="px-4 py-2.5 rounded-xl bg-[#FF6B1A] hover:bg-[#E55607] text-white text-xs font-bold shadow-md shadow-[#FF6B1A]/20 transition-all flex items-center gap-1.5 self-start md:self-auto">
            <i data-lucide="user-plus" class="w-4 h-4"></i>
            <span>+ Add New Employee</span>
        </button>
    </div>

    @if(session('sent_activation_url'))
        <div class="p-4 rounded-xl bg-amber-50 border border-amber-300 text-amber-900 text-xs flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-sm">
            <div class="flex items-center gap-2">
                <i data-lucide="key" class="w-4 h-4 text-amber-600 shrink-0"></i>
                <span><strong>Employee Activation Link Dispatched:</strong> <code class="font-mono text-[11px] text-amber-800 break-all">{{ session('sent_activation_url') }}</code></span>
            </div>
            <button type="button" onclick="navigator.clipboard.writeText('{{ session('sent_activation_url') }}'); alert('Activation Link Copied to Clipboard!');" class="px-3 py-1.5 rounded-lg bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs shrink-0 flex items-center gap-1">
                <i data-lucide="copy" class="w-3.5 h-3.5"></i>
                <span>Copy Setup Link</span>
            </button>
        </div>
    @endif

    <!-- Filter Bar -->
    <div class="bg-white p-5 rounded-2xl border border-[#E2E8F0] shadow-sm">
        <form method="GET" action="{{ route('admin.employees.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <div class="lg:col-span-2 relative">
                <div class="absolute left-3 top-1/2 -translate-y-1/2 text-[#94A3B8]">
                    <i data-lucide="search" class="w-4 h-4"></i>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name, code (INTRA-EMP-001), ID, role..." class="w-full text-xs pl-9 pr-3 py-2.5 rounded-xl bg-[#F8FAFC] border border-[#CBD5E1] text-[#1C1C1E] focus:border-[#FF6B1A]">
            </div>

            <div>
                <select name="department" class="w-full text-xs px-3 py-2.5 rounded-xl bg-[#F8FAFC] border border-[#CBD5E1] text-[#1C1C1E]">
                    <option value="">All Departments</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept }}" {{ request('department') == $dept ? 'selected' : '' }}>{{ $dept }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <select name="status" class="w-full text-xs px-3 py-2.5 rounded-xl bg-[#F8FAFC] border border-[#CBD5E1] text-[#1C1C1E]">
                    <option value="">All Statuses</option>
                    <option value="ACTIVE" {{ request('status') == 'ACTIVE' ? 'selected' : '' }}>Active</option>
                    <option value="INACTIVE" {{ request('status') == 'INACTIVE' ? 'selected' : '' }}>Inactive</option>
                    <option value="ON_NOTICE" {{ request('status') == 'ON_NOTICE' ? 'selected' : '' }}>On Notice</option>
                </select>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 py-2.5 rounded-xl bg-[#1C1C1E] hover:bg-black text-white text-xs font-bold transition-all shadow-sm">
                    Filter
                </button>
                <a href="{{ route('admin.employees.index') }}" class="p-2.5 rounded-xl bg-[#F1F5F9] hover:bg-[#E2E8F0] text-[#64748B] transition-all" title="Reset">
                    <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Employee Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        @forelse($employees as $emp)
        <div class="bg-white rounded-2xl border border-[#E2E8F0] shadow-sm hover:shadow-md transition-shadow p-5 flex flex-col justify-between">
            <div>
                <div class="flex items-start justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <img src="{{ $emp->photo_url ?: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=256&q=80' }}" class="w-12 h-12 rounded-2xl object-cover ring-2 ring-[#FF6B1A]/20 shadow-sm" alt="Avatar">
                        <div>
                            <a href="{{ route('admin.employees.show', $emp->id) }}" class="font-extrabold text-sm text-[#1C1C1E] hover:text-[#FF6B1A] transition-colors block">
                                {{ $emp->full_name }}
                            </a>
                            <span class="text-xs font-mono font-bold text-[#FF6B1A]">{{ $emp->employee_code }}</span>
                            <span class="text-[10px] text-[#94A3B8]">({{ $emp->employee_id }})</span>
                        </div>
                    </div>

                    <!-- Status Pill -->
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $emp->status === 'ACTIVE' ? 'bg-[#ECFDF5] text-[#059669] border border-[#A7F3D0]' : ($emp->status === 'ON_NOTICE' ? 'bg-[#FFFBEB] text-[#D97706] border border-[#FDE68A]' : 'bg-gray-100 text-gray-600') }}">
                        {{ $emp->status }}
                    </span>
                </div>

                <div class="mt-4 pt-3 border-t border-[#F1F5F9] space-y-1.5 text-xs text-[#64748B]">
                    <div class="flex justify-between">
                        <span>Department:</span>
                        <strong class="text-[#1C1C1E]">{{ $emp->department }}</strong>
                    </div>
                    <div class="flex justify-between">
                        <span>Role &bull; Designation:</span>
                        <span class="text-[#1C1C1E] truncate max-w-[170px]">{{ $emp->role }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Employment Type:</span>
                        <span class="text-[#1C1C1E]">{{ $emp->employment_type }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Assigned Project:</span>
                        <strong class="text-[#2563EB] truncate max-w-[170px]">{{ $emp->assigned_project ?: 'Operations' }}</strong>
                    </div>
                </div>
            </div>

            <!-- Card Bottom Bar -->
            <div class="mt-4 pt-3 border-t border-[#E2E8F0] flex items-center justify-between">
                <span class="text-[11px] text-[#94A3B8] font-mono">
                    <strong>{{ $emp->attendances_count }}</strong> shifts logged
                </span>

                <div class="flex items-center gap-1.5">
                    <a href="{{ route('admin.employees.show', $emp->id) }}" class="p-1.5 rounded-lg text-[#64748B] hover:text-[#FF6B1A] hover:bg-[#FFF3EB]" title="View Full Profile">
                        <i data-lucide="eye" class="w-4 h-4"></i>
                    </a>

                    @if($emp->email)
                    <form method="POST" action="{{ route('admin.employees.send-password-link', $emp->id) }}" class="inline" onsubmit="return confirm('Send activation/password reset link to {{ $emp->email }}?');">
                        @csrf
                        <button type="submit" class="p-1.5 rounded-lg text-indigo-600 hover:text-indigo-800 hover:bg-indigo-50" title="Send Setup/Reset Password Email">
                            <i data-lucide="send" class="w-4 h-4"></i>
                        </button>
                    </form>
                    @endif

                    <form method="POST" action="{{ route('admin.employees.toggle', $emp->id) }}" class="inline">
                        @csrf
                        <button type="submit" class="p-1.5 rounded-lg {{ $emp->status === 'ACTIVE' ? 'text-amber-600 hover:bg-amber-50' : 'text-emerald-600 hover:bg-emerald-50' }}" title="{{ $emp->status === 'ACTIVE' ? 'Deactivate' : 'Activate' }}">
                            <i data-lucide="{{ $emp->status === 'ACTIVE' ? 'user-minus' : 'user-check' }}" class="w-4 h-4"></i>
                        </button>
                    </form>

                    <form method="POST" action="{{ route('admin.employees.destroy', $emp->id) }}" onsubmit="return confirm('Are you sure you want to delete employee {{ $emp->full_name }}?');" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="p-1.5 rounded-lg text-red-500 hover:text-red-700 hover:bg-red-50" title="Delete">
                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full bg-white rounded-2xl border border-[#E2E8F0] p-12 text-center text-[#94A3B8]">
            <i data-lucide="users" class="w-12 h-12 mx-auto text-[#CBD5E1] mb-2"></i>
            <p class="font-bold text-sm text-[#1C1C1E]">No Employees Found</p>
            <p class="text-xs text-[#64748B] mt-1">Try adjusting your search criteria or add a new employee.</p>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($employees->hasPages())
    <div class="bg-white p-4 rounded-2xl border border-[#E2E8F0] shadow-sm">
        {{ $employees->links() }}
    </div>
    @endif

    <!-- Modal: Add Employee -->
    <div x-show="addModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div @click="addModal = false" class="fixed inset-0 bg-black/60 backdrop-blur-sm"></div>
        <div class="relative w-full max-w-2xl bg-white rounded-3xl shadow-2xl border border-[#E2E8F0] p-6 sm:p-8 z-10 space-y-4 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between pb-3 border-b border-[#E2E8F0]">
                <h3 class="font-extrabold text-base text-[#1C1C1E] flex items-center gap-2">
                    <i data-lucide="user-plus" class="w-5 h-5 text-[#FF6B1A]"></i>
                    <span>+ Enroll New Employee</span>
                </h3>
                <button @click="addModal = false" class="text-[#94A3B8] hover:text-[#1C1C1E]">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form method="POST" action="{{ route('admin.employees.store') }}" class="space-y-4 text-xs">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-[#64748B] uppercase mb-1">Employee ID * (e.g. IE011)</label>
                        <input type="text" name="employee_id" required placeholder="IE011" class="w-full px-3 py-2 rounded-xl bg-[#F8FAFC] border border-[#CBD5E1] text-[#1C1C1E] uppercase">
                    </div>

                    <div>
                        <label class="block font-bold text-[#64748B] uppercase mb-1">Employee Code * (INTRA-EMP-011)</label>
                        <input type="text" name="employee_code" required placeholder="INTRA-EMP-011" class="w-full px-3 py-2 rounded-xl bg-[#F8FAFC] border border-[#CBD5E1] text-[#1C1C1E] uppercase font-mono">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-[#64748B] uppercase mb-1">Full Name *</label>
                        <input type="text" name="full_name" required placeholder="Tanmay Verma" class="w-full px-3 py-2 rounded-xl bg-[#F8FAFC] border border-[#CBD5E1] text-[#1C1C1E]">
                    </div>

                    <div>
                        <label class="block font-bold text-[#64748B] uppercase mb-1">Email Address * (For Password Setup Mail)</label>
                        <input type="email" name="email" required placeholder="tanmay@intraeats.com" class="w-full px-3 py-2 rounded-xl bg-[#F8FAFC] border border-[#CBD5E1] text-[#1C1C1E]">
                        <span class="text-[10px] text-[#FF6B1A] mt-0.5 block">Employee receives an activation link to set their password.</span>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block font-bold text-[#64748B] uppercase mb-1">Department *</label>
                        <input type="text" name="department" required placeholder="Technology" class="w-full px-3 py-2 rounded-xl bg-[#F8FAFC] border border-[#CBD5E1] text-[#1C1C1E]">
                    </div>

                    <div>
                        <label class="block font-bold text-[#64748B] uppercase mb-1">Designation *</label>
                        <input type="text" name="designation" required placeholder="Software Engineer" class="w-full px-3 py-2 rounded-xl bg-[#F8FAFC] border border-[#CBD5E1] text-[#1C1C1E]">
                    </div>

                    <div>
                        <label class="block font-bold text-[#64748B] uppercase mb-1">Role *</label>
                        <input type="text" name="role" required placeholder="Backend Engineer" class="w-full px-3 py-2 rounded-xl bg-[#F8FAFC] border border-[#CBD5E1] text-[#1C1C1E]">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block font-bold text-[#64748B] uppercase mb-1">Joining Date *</label>
                        <input type="date" name="joining_date" value="{{ date('Y-m-d') }}" required class="w-full px-3 py-2 rounded-xl bg-[#F8FAFC] border border-[#CBD5E1] text-[#1C1C1E]">
                    </div>

                    <div>
                        <label class="block font-bold text-[#64748B] uppercase mb-1">Employment Type *</label>
                        <select name="employment_type" required class="w-full px-3 py-2 rounded-xl bg-[#F8FAFC] border border-[#CBD5E1] text-[#1C1C1E]">
                            <option value="Full-time">Full-time</option>
                            <option value="Part-time">Part-time</option>
                            <option value="Contract">Contract</option>
                            <option value="Intern">Intern</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-[#64748B] uppercase mb-1">Status *</label>
                        <select name="status" required class="w-full px-3 py-2 rounded-xl bg-[#F8FAFC] border border-[#CBD5E1] text-[#1C1C1E]">
                            <option value="ACTIVE">ACTIVE</option>
                            <option value="INACTIVE">INACTIVE</option>
                            <option value="ON_NOTICE">ON NOTICE</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-[#64748B] uppercase mb-1">Assigned Project</label>
                        <select name="assigned_project" class="w-full px-3 py-2 rounded-xl bg-[#F8FAFC] border border-[#CBD5E1] text-[#1C1C1E]">
                            <option value="">-- None / General --</option>
                            @foreach($projects as $p)
                                <option value="{{ $p->name }}">{{ $p->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-[#64748B] uppercase mb-1">Mobile Number</label>
                        <input type="text" name="mobile" placeholder="+91 98765 00000" class="w-full px-3 py-2 rounded-xl bg-[#F8FAFC] border border-[#CBD5E1] text-[#1C1C1E]">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-[#64748B] uppercase mb-1">Emergency Contact</label>
                        <input type="text" name="emergency_contact" placeholder="+91 98765 11111 (Father)" class="w-full px-3 py-2 rounded-xl bg-[#F8FAFC] border border-[#CBD5E1] text-[#1C1C1E]">
                    </div>

                    <div>
                        <label class="block font-bold text-[#64748B] uppercase mb-1">Avatar / Photo URL</label>
                        <input type="text" name="photo_url" placeholder="https://images.unsplash.com/..." class="w-full px-3 py-2 rounded-xl bg-[#F8FAFC] border border-[#CBD5E1] text-[#1C1C1E]">
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-[#64748B] uppercase mb-1">Address &bull; Location</label>
                    <input type="text" name="address" placeholder="Indiranagar, Bangalore" class="w-full px-3 py-2 rounded-xl bg-[#F8FAFC] border border-[#CBD5E1] text-[#1C1C1E]">
                </div>

                <div>
                    <label class="block font-bold text-[#64748B] uppercase mb-1">Internal Notes</label>
                    <textarea name="notes" rows="2" placeholder="Onboarding notes or performance comments..." class="w-full p-2.5 rounded-xl bg-[#F8FAFC] border border-[#CBD5E1] text-[#1C1C1E]"></textarea>
                </div>

                <div class="pt-3 flex gap-3 border-t border-[#E2E8F0]">
                    <button type="button" @click="addModal = false" class="flex-1 py-2.5 rounded-xl border border-[#CBD5E1] font-bold text-[#64748B]">Cancel</button>
                    <button type="submit" class="flex-1 py-2.5 rounded-xl bg-[#FF6B1A] text-white font-bold shadow-md">Create Employee</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
