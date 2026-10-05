@extends('layouts.app')

@section('title', 'Admin Login — IntraEats HR')

@section('content')
<div class="min-h-screen flex items-center justify-center p-4 sm:p-6 bg-[#FAFAF8]">
    <div class="w-full max-w-4xl bg-white rounded-3xl border border-[#E2E8F0] shadow-2xl overflow-hidden grid grid-cols-1 md:grid-cols-2">
        <!-- Left Panel: Branding & Security Overview -->
        <div class="bg-gradient-to-br from-[#1C1C1E] to-[#2E2E33] text-white p-8 sm:p-10 flex flex-col justify-between relative overflow-hidden">
            <div class="absolute -right-16 -top-16 w-56 h-56 bg-[#FF6B1A]/10 rounded-full blur-3xl pointer-events-none"></div>

            <div>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-[#FF6B1A] flex items-center justify-center text-white shadow-md font-extrabold text-lg">
                        <i data-lucide="utensils" class="w-5 h-5"></i>
                    </div>
                    <div class="flex flex-col">
                        <span class="font-extrabold text-lg text-white leading-tight">IntraEats Admin</span>
                        <span class="text-[11px] font-bold text-[#FF6B1A] uppercase tracking-wider">HR &amp; Attendance Management</span>
                    </div>
                </div>

                <div class="mt-12 space-y-4">
                    <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white leading-tight">
                        Workforce Security &amp; Attendance Control Center
                    </h2>
                    <p class="text-xs sm:text-sm text-[#D4D4D8] leading-relaxed">
                        Enterprise-grade shift management, biometric-grade attendance validation, and real-time food-tech workforce telemetry.
                    </p>
                </div>

                <div class="mt-8 space-y-3">
                    <div class="flex items-center gap-3 text-xs text-[#E4E4E7]">
                        <span class="w-5 h-5 rounded-full bg-[#16A34A]/20 text-[#4ADE80] flex items-center justify-center shrink-0">
                            <i data-lucide="shield-check" class="w-3.5 h-3.5"></i>
                        </span>
                        <span>Multi-Factor OTP Cryptographic Verification</span>
                    </div>
                    <div class="flex items-center gap-3 text-xs text-[#E4E4E7]">
                        <span class="w-5 h-5 rounded-full bg-[#16A34A]/20 text-[#4ADE80] flex items-center justify-center shrink-0">
                            <i data-lucide="history" class="w-3.5 h-3.5"></i>
                        </span>
                        <span>Complete Immutable Action Audit Logs</span>
                    </div>
                    <div class="flex items-center gap-3 text-xs text-[#E4E4E7]">
                        <span class="w-5 h-5 rounded-full bg-[#16A34A]/20 text-[#4ADE80] flex items-center justify-center shrink-0">
                            <i data-lucide="server" class="w-3.5 h-3.5"></i>
                        </span>
                        <span>Hostinger-Ready Optimized Architecture</span>
                    </div>
                </div>
            </div>

            <div class="pt-8 border-t border-[#3E3E46] text-[11px] text-[#A1A1AA] flex items-center justify-between">
                <span>IntraEats &bull; Talisha Software</span>
                <a href="{{ route('employee.portal') }}" class="text-[#FF6B1A] hover:underline font-semibold flex items-center gap-1">
                    <span>Employee Kiosk</span>
                    <i data-lucide="arrow-right" class="w-3 h-3"></i>
                </a>
            </div>
        </div>

        <!-- Right Panel: Sign-In Form -->
        <div class="p-8 sm:p-10 flex flex-col justify-center bg-white">
            <div class="mb-6">
                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-[#FFF3EB] text-[#FF6B1A] border border-[#FFD4BD]">Step 1 of 2</span>
                <h3 class="text-2xl font-extrabold text-[#1C1C1E] mt-2">Sign in to Admin Hub</h3>
                <p class="text-xs text-[#64748B] mt-1">Enter your administrative credentials to request a 2FA passcode.</p>
            </div>

            <!-- Error Banner -->
            @if($errors->any())
            <div class="mb-5 p-3.5 rounded-xl bg-red-50 border border-red-200 text-red-700 text-xs flex items-center gap-2.5">
                <i data-lucide="alert-circle" class="w-4 h-4 text-red-600 shrink-0"></i>
                <span>{{ $errors->first() }}</span>
            </div>
            @endif

            @if(session('status'))
            <div class="mb-5 p-3.5 rounded-xl bg-[#ECFDF5] border border-[#A7F3D0] text-[#065F46] text-xs flex items-center gap-2.5">
                <i data-lucide="check-circle" class="w-4 h-4 text-[#10B981] shrink-0"></i>
                <span>{{ session('status') }}</span>
            </div>
            @endif

            <form method="POST" action="{{ route('admin.login.submit') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-[#64748B] mb-1.5" for="email">Admin Email Address</label>
                    <div class="relative">
                        <div class="absolute left-3.5 top-1/2 -translate-y-1/2 text-[#94A3B8]">
                            <i data-lucide="mail" class="w-4 h-4"></i>
                        </div>
                        <input id="email" type="email" name="email" value="{{ old('email', 'hr@intraeats.com') }}" required autofocus placeholder="hr@intraeats.com" class="w-full bg-[#F8FAFC] text-[#1C1C1E] text-sm pl-10 pr-4 py-3 rounded-xl border border-[#CBD5E1] focus:border-[#FF6B1A] focus:ring-2 focus:ring-[#FF6B1A]/20">
                    </div>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-xs font-bold uppercase tracking-wider text-[#64748B]" for="password">Password</label>
                        <a href="{{ route('admin.password.forgot') }}" class="text-xs font-semibold text-[#FF6B1A] hover:underline">Forgot password?</a>
                    </div>
                    <div class="relative">
                        <div class="absolute left-3.5 top-1/2 -translate-y-1/2 text-[#94A3B8]">
                            <i data-lucide="lock" class="w-4 h-4"></i>
                        </div>
                        <input id="password" type="password" name="password" value="Admin@IntraEats2026!" required placeholder="••••••••••••" class="w-full bg-[#F8FAFC] text-[#1C1C1E] text-sm pl-10 pr-4 py-3 rounded-xl border border-[#CBD5E1] focus:border-[#FF6B1A] focus:ring-2 focus:ring-[#FF6B1A]/20 font-mono">
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full py-3.5 rounded-xl bg-[#FF6B1A] hover:bg-[#E55607] text-white font-extrabold text-sm shadow-lg shadow-[#FF6B1A]/25 hover:shadow-xl transition-all flex items-center justify-center gap-2">
                        <i data-lucide="arrow-right" class="w-4 h-4"></i>
                        <span>Continue to 2FA Passcode</span>
                    </button>
                </div>
            </form>

            <div class="mt-6 pt-4 border-t border-[#F1F5F9] text-center text-xs text-[#94A3B8]">
                Default demo credential: <strong class="text-[#1C1C1E]">hr@intraeats.com</strong> / <strong class="text-[#1C1C1E]">Admin@IntraEats2026!</strong>
            </div>
        </div>
    </div>
</div>
@endsection
