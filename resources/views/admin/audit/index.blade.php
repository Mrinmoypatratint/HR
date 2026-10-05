@extends('layouts.admin')

@section('title', 'System Audit Trail & Compliance Log')

@section('content')
<div class="space-y-6">

    <!-- Top Action Header -->
    <div class="bg-white p-5 rounded-2xl border border-[#E2E8F0] shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-2xl font-extrabold text-[#1C1C1E] tracking-tight">System Audit Trail</h1>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-[#ECFDF5] text-[#059669] border border-[#A7F3D0]">Tamper Evident</span>
            </div>
            <p class="text-xs text-[#64748B] mt-0.5">Immutable record of every employee punch, administrative adjustment, export download, and 2FA authentication event.</p>
        </div>

        <div class="flex items-center gap-2">
            <span class="px-3.5 py-2 rounded-xl bg-[#F8FAFC] border border-[#CBD5E1] text-xs font-bold text-[#475569] flex items-center gap-1.5">
                <i data-lucide="shield-check" class="w-4 h-4 text-[#059669]"></i>
                <span>{{ $logs->total() }} Total Log Entries</span>
            </span>
        </div>
    </div>

    <!-- Filter & Search Panel -->
    <div class="bg-white p-5 rounded-2xl border border-[#E2E8F0] shadow-sm">
        <form method="GET" action="{{ route('admin.audit.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <!-- Search Text -->
            <div class="lg:col-span-2 relative">
                <div class="absolute left-3 top-1/2 -translate-y-1/2 text-[#94A3B8]">
                    <i data-lucide="search" class="w-4 h-4"></i>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search actor, action, IP, details..." class="w-full text-xs pl-9 pr-3 py-2.5 rounded-xl bg-[#F8FAFC] border border-[#CBD5E1] text-[#1C1C1E] focus:border-[#FF6B1A] focus:ring-2 focus:ring-[#FF6B1A]/20">
            </div>

            <!-- Module Filter -->
            <div>
                <select name="module" class="w-full text-xs px-3 py-2.5 rounded-xl bg-[#F8FAFC] border border-[#CBD5E1] text-[#1C1C1E] focus:border-[#FF6B1A] focus:ring-2 focus:ring-[#FF6B1A]/20">
                    <option value="">All System Modules</option>
                    @foreach($modules as $mod)
                        <option value="{{ $mod }}" {{ request('module') === $mod ? 'selected' : '' }}>{{ $mod }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Action Filter -->
            <div>
                <select name="action" class="w-full text-xs px-3 py-2.5 rounded-xl bg-[#F8FAFC] border border-[#CBD5E1] text-[#1C1C1E] focus:border-[#FF6B1A] focus:ring-2 focus:ring-[#FF6B1A]/20">
                    <option value="">All Action Types</option>
                    @foreach($actions as $act)
                        <option value="{{ $act }}" {{ request('action') === $act ? 'selected' : '' }}>{{ $act }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Buttons -->
            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 py-2.5 px-3 rounded-xl bg-[#FF6B1A] hover:bg-[#E55607] text-white text-xs font-bold shadow-md shadow-[#FF6B1A]/20 transition-all flex items-center justify-center gap-1.5">
                    <i data-lucide="filter" class="w-4 h-4"></i>
                    <span>Filter</span>
                </button>
                @if(request()->hasAny(['search', 'module', 'action']))
                    <a href="{{ route('admin.audit.index') }}" class="py-2.5 px-3 rounded-xl bg-[#F1F5F9] hover:bg-[#E2E8F0] text-[#64748B] text-xs font-bold transition-all">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Audit Log Records Table -->
    <div class="bg-white rounded-2xl border border-[#E2E8F0] shadow-sm overflow-hidden">
        <div class="p-4 border-b border-[#E2E8F0] bg-[#F8FAFC] flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i data-lucide="activity" class="w-4 h-4 text-[#2563EB]"></i>
                <h3 class="text-sm font-extrabold text-[#1C1C1E]">Audit Records Ledger</h3>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-[#E2E8F0] text-[#475569]">Showing {{ $logs->firstItem() ?? 0 }}-{{ $logs->lastItem() ?? 0 }} of {{ $logs->total() }}</span>
            </div>
            <span class="text-xs text-[#64748B]">All timestamps recorded in <strong>{{ config('app.timezone', 'Asia/Kolkata') }}</strong></span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-[#F8FAFC] border-b border-[#E2E8F0] text-[11px] font-bold uppercase tracking-wider text-[#64748B]">
                        <th class="py-3 px-4">Timestamp</th>
                        <th class="py-3 px-4">Operator / Actor</th>
                        <th class="py-3 px-4">Action</th>
                        <th class="py-3 px-4">Module</th>
                        <th class="py-3 px-4">Target / Entity</th>
                        <th class="py-3 px-4">Network IP</th>
                        <th class="py-3 px-4">Event Details</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#F1F5F9] text-xs">
                    @forelse($logs as $log)
                    <tr class="hover:bg-[#F8FAFC] transition-colors">
                        <!-- Timestamp -->
                        <td class="py-3 px-4 whitespace-nowrap">
                            <span class="font-extrabold text-[#1C1C1E]">{{ $log->created_at->format('d M Y, h:i:s A') }}</span>
                            <span class="block text-[10px] text-[#94A3B8]">{{ $log->created_at->diffForHumans() }}</span>
                        </td>

                        <!-- Operator -->
                        <td class="py-3 px-4 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 rounded-full bg-[#F1F5F9] border border-[#CBD5E1] text-[#475569] flex items-center justify-center font-bold text-[10px]">
                                    {{ substr($log->user_name ?: 'S', 0, 1) }}
                                </div>
                                <span class="font-bold text-[#1C1C1E]">{{ $log->user_name ?: 'System' }}</span>
                            </div>
                        </td>

                        <!-- Action -->
                        <td class="py-3 px-4 whitespace-nowrap">
                            @php
                                $act = strtoupper($log->action);
                                $badgeClass = 'bg-[#F1F5F9] text-[#475569] border-[#E2E8F0]';
                                if (str_contains($act, 'LOGIN') || str_contains($act, 'AUTH')) {
                                    $badgeClass = 'bg-[#EFF6FF] text-[#2563EB] border-[#BFDBFE]';
                                } elseif (str_contains($act, 'CREATE') || str_contains($act, 'CHECK_IN') || str_contains($act, 'SUCCESS')) {
                                    $badgeClass = 'bg-[#ECFDF5] text-[#059669] border-[#A7F3D0]';
                                } elseif (str_contains($act, 'UPDATE') || str_contains($act, 'TOGGLE') || str_contains($act, 'PUNCH')) {
                                    $badgeClass = 'bg-[#FFFBEB] text-[#D97706] border-[#FDE68A]';
                                } elseif (str_contains($act, 'DELETE') || str_contains($act, 'CLEAR') || str_contains($act, 'FAIL')) {
                                    $badgeClass = 'bg-[#FEF2F2] text-[#DC2626] border-[#FECACA]';
                                } elseif (str_contains($act, 'EXPORT')) {
                                    $badgeClass = 'bg-[#F5F3FF] text-[#7C3AED] border-[#DDD6FE]';
                                }
                            @endphp
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold border {{ $badgeClass }}">
                                {{ $log->action }}
                            </span>
                        </td>

                        <!-- Module -->
                        <td class="py-3 px-4 whitespace-nowrap">
                            <span class="px-2 py-0.5 rounded-lg text-[10px] font-bold bg-[#F8FAFC] text-[#334155] border border-[#E2E8F0]">
                                {{ $log->module }}
                            </span>
                        </td>

                        <!-- Target ID -->
                        <td class="py-3 px-4 whitespace-nowrap font-mono text-[11px] text-[#64748B]">
                            {{ $log->target_id ?: '—' }}
                        </td>

                        <!-- Network IP -->
                        <td class="py-3 px-4 whitespace-nowrap font-mono text-[11px] text-[#64748B]">
                            {{ $log->ip_address ?: '127.0.0.1' }}
                        </td>

                        <!-- Event Details -->
                        <td class="py-3 px-4 max-w-sm truncate text-[11px] text-[#475569]" title="{{ $log->details }}">
                            {{ $log->details ?: '—' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-12 text-center text-[#94A3B8]">
                            <i data-lucide="shield-off" class="w-10 h-10 text-[#CBD5E1] mx-auto mb-2"></i>
                            <p class="font-bold text-[#1C1C1E]">No audit logs found matching criteria.</p>
                            <a href="{{ route('admin.audit.index') }}" class="text-xs text-[#FF6B1A] font-bold mt-1 inline-block hover:underline">Reset filters</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($logs->hasPages())
        <div class="p-4 bg-[#F8FAFC] border-t border-[#E2E8F0]">
            {{ $logs->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
