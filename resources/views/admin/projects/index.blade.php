@extends('layouts.admin')

@section('title', 'Projects & Work Allocation')

@section('content')
<div class="space-y-6" x-data="{ addModal: false, assignModal: false, activeProjectId: null, activeProjectName: '' }">

    <!-- Top Action Header -->
    <div class="bg-white p-5 rounded-2xl border border-[#E2E8F0] shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-2xl font-extrabold text-[#1C1C1E] tracking-tight">Projects &amp; Work Allocation</h1>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-[#EFF6FF] text-[#2563EB] border border-[#BFDBFE]">Pipeline</span>
            </div>
            <p class="text-xs text-[#64748B] mt-0.5">Manage food-tech operational initiatives, client deliverables, and team sprint allocations.</p>
        </div>

        <button type="button" @click="addModal = true" class="px-4 py-2.5 rounded-xl bg-[#FF6B1A] hover:bg-[#E55607] text-white text-xs font-bold shadow-md shadow-[#FF6B1A]/20 transition-all flex items-center gap-1.5 self-start md:self-auto">
            <i data-lucide="folder-plus" class="w-4 h-4"></i>
            <span>+ Add New Project</span>
        </button>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white p-5 rounded-2xl border border-[#E2E8F0] shadow-sm">
        <form method="GET" action="{{ route('admin.projects.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <div class="lg:col-span-2 relative">
                <div class="absolute left-3 top-1/2 -translate-y-1/2 text-[#94A3B8]">
                    <i data-lucide="search" class="w-4 h-4"></i>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search project name, ID, client, manager..." class="w-full text-xs pl-9 pr-3 py-2.5 rounded-xl bg-[#F8FAFC] border border-[#CBD5E1] text-[#1C1C1E] focus:border-[#FF6B1A]">
            </div>

            <div>
                <select name="status" class="w-full text-xs px-3 py-2.5 rounded-xl bg-[#F8FAFC] border border-[#CBD5E1] text-[#1C1C1E]">
                    <option value="">All Statuses</option>
                    <option value="ACTIVE" {{ request('status') == 'ACTIVE' ? 'selected' : '' }}>Active</option>
                    <option value="COMPLETED" {{ request('status') == 'COMPLETED' ? 'selected' : '' }}>Completed</option>
                    <option value="ON_HOLD" {{ request('status') == 'ON_HOLD' ? 'selected' : '' }}>On Hold</option>
                    <option value="ARCHIVED" {{ request('status') == 'ARCHIVED' ? 'selected' : '' }}>Archived</option>
                </select>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 py-2.5 rounded-xl bg-[#1C1C1E] hover:bg-black text-white text-xs font-bold transition-all shadow-sm">
                    Filter
                </button>
                <a href="{{ route('admin.projects.index') }}" class="p-2.5 rounded-xl bg-[#F1F5F9] hover:bg-[#E2E8F0] text-[#64748B] transition-all" title="Reset">
                    <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Projects Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        @forelse($projects as $p)
        <div class="bg-white rounded-2xl border border-[#E2E8F0] shadow-sm hover:shadow-md transition-shadow p-5 flex flex-col justify-between space-y-4">
            <div>
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <span class="font-mono text-xs font-bold text-[#FF6B1A]">{{ $p->project_id }}</span>
                        <h3 class="text-base font-extrabold text-[#1C1C1E] mt-0.5">{{ $p->name }}</h3>
                        <p class="text-xs text-[#64748B] mt-0.5">Client: <strong>{{ $p->client }}</strong></p>
                    </div>

                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $p->status === 'ACTIVE' ? 'bg-[#ECFDF5] text-[#059669] border border-[#A7F3D0]' : ($p->status === 'COMPLETED' ? 'bg-[#EFF6FF] text-[#2563EB] border border-[#BFDBFE]' : 'bg-[#FFFBEB] text-[#D97706] border border-[#FDE68A]') }}">
                        {{ $p->status }}
                    </span>
                </div>

                <p class="text-xs text-[#475569] mt-3 line-clamp-2 leading-relaxed">
                    {{ $p->description ?: 'No detailed project brief provided.' }}
                </p>

                <div class="mt-4 pt-3 border-t border-[#F1F5F9] space-y-1.5 text-xs text-[#64748B]">
                    <div class="flex justify-between">
                        <span>Project Manager:</span>
                        <strong class="text-[#1C1C1E]">{{ $p->manager }}</strong>
                    </div>
                    <div class="flex justify-between">
                        <span>Timeline:</span>
                        <span>{{ $p->start_date?->format('M Y') ?: 'Start' }} &ndash; {{ $p->end_date?->format('M Y') ?: 'Ongoing' }}</span>
                    </div>
                </div>

                <!-- Assigned Team Preview -->
                <div class="mt-3 pt-3 border-t border-[#F1F5F9]">
                    <div class="flex items-center justify-between text-xs mb-2">
                        <span class="font-bold text-[#1C1C1E]">Assigned Team ({{ $p->employees->count() }})</span>
                        <button type="button" @click="activeProjectId = {{ $p->id }}; activeProjectName = '{{ addslashes($p->name) }}'; assignModal = true" class="text-[11px] font-bold text-[#FF6B1A] hover:underline flex items-center gap-1">
                            <i data-lucide="user-plus" class="w-3 h-3"></i> Assign
                        </button>
                    </div>

                    <div class="flex flex-wrap gap-1.5">
                        @forelse($p->employees as $member)
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-[#F1F5F9] text-[10px] text-[#1E293B]">
                            <span>{{ $member->full_name }} ({{ $member->pivot->role }})</span>
                            <form method="POST" action="{{ route('admin.projects.removeEmployee', [$p->id, $member->id]) }}" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700 ml-0.5">&times;</button>
                            </form>
                        </span>
                        @empty
                        <span class="text-[11px] text-[#94A3B8] italic">No employees assigned yet.</span>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Card Footer -->
            <div class="pt-3 border-t border-[#E2E8F0] flex items-center justify-between text-xs">
                <span class="text-[#94A3B8] text-[10px]">{{ $p->is_demo ? 'DEMO PROJECT' : 'PROJECT INITIATIVE' }}</span>

                <form method="POST" action="{{ route('admin.projects.destroy', $p->id) }}" onsubmit="return confirm('Delete project {{ $p->name }}?');" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-red-500 hover:text-red-700 font-semibold text-xs flex items-center gap-1">
                        <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                        <span>Delete</span>
                    </button>
                </form>
            </div>
        </div>
        @empty
        <div class="col-span-full bg-white rounded-2xl border border-[#E2E8F0] p-12 text-center text-[#94A3B8]">
            <i data-lucide="briefcase" class="w-12 h-12 mx-auto text-[#CBD5E1] mb-2"></i>
            <p class="font-bold text-sm text-[#1C1C1E]">No Projects Found</p>
        </div>
        @endforelse
    </div>

    <!-- Modal: Add Project -->
    <div x-show="addModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div @click="addModal = false" class="fixed inset-0 bg-black/60 backdrop-blur-sm"></div>
        <div class="relative w-full max-w-lg bg-white rounded-3xl shadow-2xl border border-[#E2E8F0] p-6 z-10 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-[#E2E8F0]">
                <h3 class="font-extrabold text-base text-[#1C1C1E] flex items-center gap-2">
                    <i data-lucide="folder-plus" class="w-5 h-5 text-[#FF6B1A]"></i>
                    <span>+ Add New Project</span>
                </h3>
                <button @click="addModal = false" class="text-[#94A3B8] hover:text-[#1C1C1E]">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form method="POST" action="{{ route('admin.projects.store') }}" class="space-y-4 text-xs">
                @csrf

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-[#64748B] uppercase mb-1">Project ID * (PRJ-006)</label>
                        <input type="text" name="project_id" required placeholder="PRJ-006" class="w-full px-3 py-2 rounded-xl bg-[#F8FAFC] border border-[#CBD5E1] text-[#1C1C1E] font-mono uppercase">
                    </div>

                    <div>
                        <label class="block font-bold text-[#64748B] uppercase mb-1">Status *</label>
                        <select name="status" required class="w-full px-3 py-2 rounded-xl bg-[#F8FAFC] border border-[#CBD5E1] text-[#1C1C1E]">
                            <option value="ACTIVE">ACTIVE</option>
                            <option value="COMPLETED">COMPLETED</option>
                            <option value="ON_HOLD">ON HOLD</option>
                            <option value="ARCHIVED">ARCHIVED</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-[#64748B] uppercase mb-1">Project Name *</label>
                    <input type="text" name="name" required placeholder="Restaurant Inventory Sync Engine" class="w-full px-3 py-2 rounded-xl bg-[#F8FAFC] border border-[#CBD5E1] text-[#1C1C1E]">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-[#64748B] uppercase mb-1">Client / Business Unit *</label>
                        <input type="text" name="client" required placeholder="Talisha Software Tech" class="w-full px-3 py-2 rounded-xl bg-[#F8FAFC] border border-[#CBD5E1] text-[#1C1C1E]">
                    </div>

                    <div>
                        <label class="block font-bold text-[#64748B] uppercase mb-1">Project Lead / Manager *</label>
                        <input type="text" name="manager" required placeholder="Rahul Das" class="w-full px-3 py-2 rounded-xl bg-[#F8FAFC] border border-[#CBD5E1] text-[#1C1C1E]">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-[#64748B] uppercase mb-1">Start Date</label>
                        <input type="date" name="start_date" value="{{ date('Y-m-d') }}" class="w-full px-3 py-2 rounded-xl bg-[#F8FAFC] border border-[#CBD5E1] text-[#1C1C1E]">
                    </div>

                    <div>
                        <label class="block font-bold text-[#64748B] uppercase mb-1">End Date</label>
                        <input type="date" name="end_date" class="w-full px-3 py-2 rounded-xl bg-[#F8FAFC] border border-[#CBD5E1] text-[#1C1C1E]">
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-[#64748B] uppercase mb-1">Project Description</label>
                    <textarea name="description" rows="2" placeholder="Brief outline of deliverables and milestones..." class="w-full p-2.5 rounded-xl bg-[#F8FAFC] border border-[#CBD5E1] text-[#1C1C1E]"></textarea>
                </div>

                <div class="pt-2 flex gap-3 border-t border-[#E2E8F0]">
                    <button type="button" @click="addModal = false" class="flex-1 py-2.5 rounded-xl border border-[#CBD5E1] font-bold text-[#64748B]">Cancel</button>
                    <button type="submit" class="flex-1 py-2.5 rounded-xl bg-[#FF6B1A] text-white font-bold shadow-md">Create Project</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Assign Employee -->
    <div x-show="assignModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div @click="assignModal = false" class="fixed inset-0 bg-black/60 backdrop-blur-sm"></div>
        <div class="relative w-full max-w-md bg-white rounded-3xl shadow-2xl border border-[#E2E8F0] p-6 z-10 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-[#E2E8F0]">
                <h3 class="font-extrabold text-base text-[#1C1C1E] flex items-center gap-2">
                    <i data-lucide="user-check" class="w-5 h-5 text-[#2563EB]"></i>
                    <span>Assign Staff to Project</span>
                </h3>
                <button @click="assignModal = false" class="text-[#94A3B8] hover:text-[#1C1C1E]">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <p class="text-xs text-[#64748B]">Assign an employee to <strong class="text-[#1C1C1E]" x-text="activeProjectName"></strong>.</p>

            <form :action="'{{ url('admin/projects') }}/' + activeProjectId + '/assign'" method="POST" class="space-y-4 text-xs">
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

                <div>
                    <label class="block font-bold text-[#64748B] uppercase mb-1">Project Role</label>
                    <input type="text" name="role" placeholder="e.g. Lead Engineer, QA, Contributor" value="Member" class="w-full px-3 py-2 rounded-xl bg-[#F8FAFC] border border-[#CBD5E1] text-[#1C1C1E]">
                </div>

                <div class="pt-2 flex gap-3">
                    <button type="button" @click="assignModal = false" class="flex-1 py-2.5 rounded-xl border border-[#CBD5E1] font-bold text-[#64748B]">Cancel</button>
                    <button type="submit" class="flex-1 py-2.5 rounded-xl bg-[#2563EB] text-white font-bold shadow-md">Assign</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
