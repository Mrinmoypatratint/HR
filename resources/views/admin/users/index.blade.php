@extends('layouts.admin')

@section('title', 'Admin Users & RBAC Management')

@section('content')
<div class="space-y-6" x-data="{ 
    createModal: false, 
    roleModal: false, 
    activeAdmin: { id: null, name: '', role: '' } 
}">

    <!-- Top Action Header -->
    <div class="bg-white p-5 rounded-2xl border border-[#E2E8F0] shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-2xl font-extrabold text-[#1C1C1E] tracking-tight">Admin & RBAC Access Control</h1>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-[#ECFDF5] text-[#059669] border border-[#A7F3D0]">{{ $admins->where('status', 'ACTIVE')->count() }} Active Admins</span>
            </div>
            <p class="text-xs text-[#64748B] mt-0.5">Manage back-office administrative staff, Spatie role assignments, security status, and 2FA authentication.</p>
        </div>

        <button type="button" @click="createModal = true" class="px-4 py-2.5 rounded-xl bg-[#FF6B1A] hover:bg-[#E55607] text-white text-xs font-bold shadow-md shadow-[#FF6B1A]/20 transition-all flex items-center gap-1.5 self-start md:self-auto">
            <i data-lucide="user-plus" class="w-4 h-4"></i>
            <span>+ Add New Admin</span>
        </button>
    </div>

    <!-- Security & RBAC Highlights -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded-2xl border border-[#E2E8F0] shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-black uppercase text-[#DC2626]">Super Admin</span>
                <i data-lucide="shield-alert" class="w-4 h-4 text-[#DC2626]"></i>
            </div>
            <p class="text-xs text-[#64748B]">Unrestricted root access, RBAC delegation, database purge, and system configuration.</p>
            <div class="mt-2 text-[11px] font-bold text-[#DC2626]">{{ $admins->filter(fn($u) => $u->hasRole('Super Admin'))->count() }} Assigned</div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-[#E2E8F0] shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-black uppercase text-[#2563EB]">HR Admin</span>
                <i data-lucide="users" class="w-4 h-4 text-[#2563EB]"></i>
            </div>
            <p class="text-xs text-[#64748B]">Employee directory CRUD, project assignments, manual punches, and attendance audits.</p>
            <div class="mt-2 text-[11px] font-bold text-[#2563EB]">{{ $admins->filter(fn($u) => $u->hasRole('HR Admin'))->count() }} Assigned</div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-[#E2E8F0] shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-black uppercase text-[#059669]">Attendance Mgr</span>
                <i data-lucide="clock" class="w-4 h-4 text-[#059669]"></i>
            </div>
            <p class="text-xs text-[#64748B]">Real-time punch verification, manual punch logging, and bulk attendance imports.</p>
            <div class="mt-2 text-[11px] font-bold text-[#059669]">{{ $admins->filter(fn($u) => $u->hasRole('Attendance Manager'))->count() }} Assigned</div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-[#E2E8F0] shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-black uppercase text-[#7C3AED]">Report Mgr</span>
                <i data-lucide="bar-chart-3" class="w-4 h-4 text-[#7C3AED]"></i>
            </div>
            <p class="text-xs text-[#64748B]">Read-only executive analytics, PDF exports, and Excel/CSV download rights.</p>
            <div class="mt-2 text-[11px] font-bold text-[#7C3AED]">{{ $admins->filter(fn($u) => $u->hasRole('Report Manager'))->count() }} Assigned</div>
        </div>
    </div>

    <!-- Admin Table -->
    <div class="bg-white rounded-2xl border border-[#E2E8F0] shadow-sm overflow-hidden">
        <div class="p-4 border-b border-[#E2E8F0] bg-[#F8FAFC] flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i data-lucide="shield-check" class="w-4 h-4 text-[#059669]"></i>
                <h3 class="text-sm font-extrabold text-[#1C1C1E]">Authorized Admin Accounts</h3>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-[#E2E8F0] text-[#475569]">{{ $admins->count() }} Total</span>
            </div>
            <span class="text-xs text-[#64748B]">Protected by <strong>2-Step Password + Email OTP Verification</strong></span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-[#F8FAFC] border-b border-[#E2E8F0] text-[11px] font-bold uppercase tracking-wider text-[#64748B]">
                        <th class="py-3 px-4">Admin User</th>
                        <th class="py-3 px-4">Contact Details</th>
                        <th class="py-3 px-4">Assigned Role</th>
                        <th class="py-3 px-4">2FA Security</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4">Created Date</th>
                        <th class="py-3 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#F1F5F9] text-xs">
                    @foreach($admins as $admin)
                    <tr class="hover:bg-[#F8FAFC] transition-colors">
                        <!-- Admin User -->
                        <td class="py-3.5 px-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl overflow-hidden bg-[#F1F5F9] border border-[#CBD5E1] shrink-0">
                                    <img src="{{ $admin->avatar_url }}" alt="{{ $admin->name }}" class="w-full h-full object-cover">
                                </div>
                                <div>
                                    <div class="font-extrabold text-[#1C1C1E] flex items-center gap-1.5">
                                        <span>{{ $admin->name }}</span>
                                        @if($admin->id === auth()->id())
                                            <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-[#EFF6FF] text-[#2563EB] border border-[#BFDBFE]">YOU</span>
                                        @endif
                                    </div>
                                    <span class="text-[11px] text-[#64748B] font-mono">{{ $admin->email }}</span>
                                </div>
                            </div>
                        </td>

                        <!-- Contact -->
                        <td class="py-3.5 px-4 text-[#64748B]">
                            <span>{{ $admin->mobile ?: '—' }}</span>
                        </td>

                        <!-- Role -->
                        <td class="py-3.5 px-4 whitespace-nowrap">
                            @php $roleName = $admin->roles->first()?->name ?? 'Unassigned'; @endphp
                            @if($roleName === 'Super Admin')
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-[#FEF2F2] text-[#DC2626] border border-[#FECACA]">
                                    {{ $roleName }}
                                </span>
                            @elseif($roleName === 'HR Admin')
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-[#EFF6FF] text-[#2563EB] border border-[#BFDBFE]">
                                    {{ $roleName }}
                                </span>
                            @elseif($roleName === 'Attendance Manager')
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-[#ECFDF5] text-[#059669] border border-[#A7F3D0]">
                                    {{ $roleName }}
                                </span>
                            @else
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-[#F5F3FF] text-[#7C3AED] border border-[#DDD6FE]">
                                    {{ $roleName }}
                                </span>
                            @endif
                        </td>

                        <!-- 2FA -->
                        <td class="py-3.5 px-4 whitespace-nowrap">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-[#ECFDF5] text-[#059669] border border-[#A7F3D0] flex items-center gap-1 w-max">
                                <i data-lucide="shield-check" class="w-3 h-3"></i>
                                <span>OTP Enforced</span>
                            </span>
                        </td>

                        <!-- Status -->
                        <td class="py-3.5 px-4 whitespace-nowrap">
                            @if($admin->status === 'ACTIVE')
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-[#ECFDF5] text-[#059669] border border-[#A7F3D0]">
                                    ACTIVE
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-[#FEF2F2] text-[#DC2626] border border-[#FECACA]">
                                    INACTIVE
                                </span>
                            @endif
                        </td>

                        <!-- Created -->
                        <td class="py-3.5 px-4 whitespace-nowrap text-[#64748B]">
                            {{ $admin->created_at->format('d M Y') }}
                        </td>

                        <!-- Actions -->
                        <td class="py-3.5 px-4 text-right whitespace-nowrap">
                            <div class="flex items-center justify-end gap-1.5">
                                <!-- Change Role Button -->
                                <button type="button" 
                                        @click="activeAdmin = { id: {{ $admin->id }}, name: '{{ addslashes($admin->name) }}', role: '{{ $roleName }}' }; roleModal = true;"
                                        class="p-1.5 rounded-lg hover:bg-[#F1F5F9] text-[#64748B] hover:text-[#2563EB] transition-colors"
                                        title="Change Spatie Role">
                                    <i data-lucide="shield" class="w-4 h-4"></i>
                                </button>

                                <!-- Toggle Active/Inactive -->
                                @if($admin->id !== auth()->id())
                                    <form method="POST" action="{{ route('admin.users.toggle', $admin->id) }}" class="inline">
                                        @csrf
                                        <button type="submit" 
                                                class="p-1.5 rounded-lg hover:bg-[#F1F5F9] {{ $admin->status === 'ACTIVE' ? 'text-[#D97706] hover:text-[#B45309]' : 'text-[#059669] hover:text-[#047857]' }} transition-colors"
                                                title="{{ $admin->status === 'ACTIVE' ? 'Deactivate account' : 'Activate account' }}"
                                                onclick="return confirm('Are you sure you want to {{ $admin->status === 'ACTIVE' ? 'deactivate' : 'activate' }} {{ $admin->name }}?')">
                                            <i data-lucide="{{ $admin->status === 'ACTIVE' ? 'user-x' : 'user-check' }}" class="w-4 h-4"></i>
                                        </button>
                                    </form>

                                    <!-- Delete Admin -->
                                    <form method="POST" action="{{ route('admin.users.destroy', $admin->id) }}" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="p-1.5 rounded-lg hover:bg-[#FEF2F2] text-[#94A3B8] hover:text-[#DC2626] transition-colors"
                                                title="Delete admin account"
                                                onclick="return confirm('Are you sure you want to permanently delete admin account for {{ $admin->name }}?')">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal: Add New Admin -->
    <div x-show="createModal" 
         x-cloak 
         class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="createModal = false" class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-[#E2E8F0] space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-[#F1F5F9]">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-xl bg-[#FF6B1A]/10 text-[#FF6B1A] flex items-center justify-center">
                        <i data-lucide="user-plus" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <h3 class="font-black text-base text-[#1C1C1E]">Add Administrative User</h3>
                        <p class="text-xs text-[#64748B]">Create a back-office account with 2FA email authentication</p>
                    </div>
                </div>
                <button type="button" @click="createModal = false" class="text-[#94A3B8] hover:text-[#1C1C1E]">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-4 text-xs">
                @csrf

                <div>
                    <label class="block font-bold text-[#475569] uppercase tracking-wider mb-1">Full Name *</label>
                    <input type="text" name="name" required placeholder="e.g. Meera Nambiar" class="w-full px-3 py-2.5 rounded-xl bg-[#F8FAFC] border border-[#CBD5E1] text-[#1C1C1E] focus:border-[#FF6B1A] focus:ring-2 focus:ring-[#FF6B1A]/20">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-[#475569] uppercase tracking-wider mb-1">Email Address *</label>
                        <input type="email" name="email" required placeholder="name@intraeats.com" class="w-full px-3 py-2.5 rounded-xl bg-[#F8FAFC] border border-[#CBD5E1] text-[#1C1C1E] focus:border-[#FF6B1A] focus:ring-2 focus:ring-[#FF6B1A]/20">
                    </div>

                    <div>
                        <label class="block font-bold text-[#475569] uppercase tracking-wider mb-1">Mobile Phone</label>
                        <input type="text" name="mobile" placeholder="+91 98765 43210" class="w-full px-3 py-2.5 rounded-xl bg-[#F8FAFC] border border-[#CBD5E1] text-[#1C1C1E] focus:border-[#FF6B1A] focus:ring-2 focus:ring-[#FF6B1A]/20">
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-[#475569] uppercase tracking-wider mb-1">Role / Permissions *</label>
                    <select name="role" required class="w-full px-3 py-2.5 rounded-xl bg-[#F8FAFC] border border-[#CBD5E1] text-[#1C1C1E] focus:border-[#FF6B1A] focus:ring-2 focus:ring-[#FF6B1A]/20">
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}">{{ $role->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-[#475569] uppercase tracking-wider mb-1">Initial Password * (Min 8 chars)</label>
                    <input type="password" name="password" required placeholder="••••••••••••" class="w-full px-3 py-2.5 rounded-xl bg-[#F8FAFC] border border-[#CBD5E1] text-[#1C1C1E] focus:border-[#FF6B1A] focus:ring-2 focus:ring-[#FF6B1A]/20">
                    <span class="text-[10px] text-[#64748B] mt-1 block">Account will require 6-digit email OTP upon login.</span>
                </div>

                <div class="pt-3 border-t border-[#F1F5F9] flex items-center justify-end gap-2">
                    <button type="button" @click="createModal = false" class="px-4 py-2 rounded-xl border border-[#CBD5E1] font-bold text-[#64748B] hover:bg-[#F8FAFC]">
                        Cancel
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-[#FF6B1A] hover:bg-[#E55607] font-bold text-white shadow-md shadow-[#FF6B1A]/20">
                        Create Admin Account
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Change Role -->
    <div x-show="roleModal" 
         x-cloak 
         class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="roleModal = false" class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-[#E2E8F0] space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-[#F1F5F9]">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-xl bg-[#2563EB]/10 text-[#2563EB] flex items-center justify-center">
                        <i data-lucide="shield" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <h3 class="font-black text-base text-[#1C1C1E]">Update Admin Role</h3>
                        <p class="text-xs text-[#64748B]" x-text="'Change permissions for ' + activeAdmin.name"></p>
                    </div>
                </div>
                <button type="button" @click="roleModal = false" class="text-[#94A3B8] hover:text-[#1C1C1E]">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form method="POST" :action="'{{ url('admin/users') }}/' + activeAdmin.id + '/role'" class="space-y-4 text-xs">
                @csrf
                @method('PUT')

                <div>
                    <label class="block font-bold text-[#475569] uppercase tracking-wider mb-1">Select New Role</label>
                    <select name="role" x-model="activeAdmin.role" class="w-full px-3 py-2.5 rounded-xl bg-[#F8FAFC] border border-[#CBD5E1] text-[#1C1C1E] focus:border-[#2563EB] focus:ring-2 focus:ring-[#2563EB]/20">
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}">{{ $role->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="pt-3 border-t border-[#F1F5F9] flex items-center justify-end gap-2">
                    <button type="button" @click="roleModal = false" class="px-4 py-2 rounded-xl border border-[#CBD5E1] font-bold text-[#64748B] hover:bg-[#F8FAFC]">
                        Cancel
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-[#2563EB] hover:bg-[#1D4ED8] font-bold text-white shadow-md shadow-[#2563EB]/20">
                        Save Role Assignment
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
