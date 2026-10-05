@extends('layouts.app')

@section('title', 'Set New Password — IntraEats HR')

@section('content')
<div class="min-h-screen flex items-center justify-center p-4 sm:p-6 bg-[#FAFAF8]" x-data="{ devOtp: '{{ $devOtp }}' }">
    <div class="w-full max-w-md bg-white rounded-3xl border border-[#E2E8F0] shadow-xl p-8 sm:p-10 space-y-6">
        <div class="text-center space-y-2">
            <div class="w-14 h-14 mx-auto rounded-2xl bg-[#FFF3EB] text-[#FF6B1A] flex items-center justify-center shadow-sm">
                <i data-lucide="lock" class="w-7 h-7"></i>
            </div>
            <h1 class="text-2xl font-extrabold text-[#1C1C1E]">Set New Password</h1>
            <p class="text-xs text-[#64748B]">Verify with the 6-digit code sent to <strong class="text-[#1C1C1E]">{{ $email }}</strong> and choose a new secure password.</p>
        </div>

        @if($devOtp)
        <div class="p-3 rounded-xl bg-[#ECFDF5] border border-[#A7F3D0] text-[#065F46] text-xs flex items-center justify-between">
            <span>Dev OTP: <strong class="font-mono text-sm tracking-wider text-[#047857]">{{ $devOtp }}</strong></span>
            <button type="button" @click="document.getElementById('otp').value = '{{ $devOtp }}'" class="underline font-bold text-[11px] text-[#047857]">Auto-fill</button>
        </div>
        @endif

        @if($errors->any())
        <div class="p-3.5 rounded-xl bg-red-50 border border-red-200 text-red-700 text-xs flex items-center gap-2">
            <i data-lucide="alert-circle" class="w-4 h-4 text-red-600 shrink-0"></i>
            <span>{{ $errors->first() }}</span>
        </div>
        @endif

        <form method="POST" action="{{ route('admin.password.reset.submit') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-[#64748B] mb-1.5" for="otp">6-Digit Verification Code</label>
                <input id="otp" type="text" name="otp" maxlength="6" required autofocus placeholder="123456" class="w-full bg-[#F8FAFC] text-[#1C1C1E] text-center font-mono text-xl tracking-[6px] py-3 rounded-xl border border-[#CBD5E1] focus:border-[#FF6B1A] focus:ring-2 focus:ring-[#FF6B1A]/20">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-[#64748B] mb-1.5" for="password">New Password</label>
                <input id="password" type="password" name="password" required placeholder="Min 8 chars with upper, lower, number, special" class="w-full bg-[#F8FAFC] text-[#1C1C1E] text-sm px-4 py-3 rounded-xl border border-[#CBD5E1] focus:border-[#FF6B1A] focus:ring-2 focus:ring-[#FF6B1A]/20">
                <p class="text-[10px] text-[#94A3B8] mt-1">Min 8 chars, 1 uppercase, 1 lowercase, 1 number, 1 special character.</p>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-[#64748B] mb-1.5" for="password_confirmation">Confirm New Password</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required placeholder="Re-enter password" class="w-full bg-[#F8FAFC] text-[#1C1C1E] text-sm px-4 py-3 rounded-xl border border-[#CBD5E1] focus:border-[#FF6B1A] focus:ring-2 focus:ring-[#FF6B1A]/20">
            </div>

            <button type="submit" class="w-full py-3.5 rounded-xl bg-[#FF6B1A] hover:bg-[#E55607] text-white font-extrabold text-sm shadow-md transition-all flex items-center justify-center gap-2">
                <i data-lucide="check" class="w-4 h-4"></i>
                <span>Reset Password &amp; Login</span>
            </button>
        </form>

        <div class="text-center pt-2">
            <a href="{{ route('admin.login') }}" class="text-xs text-[#64748B] hover:text-[#1C1C1E] inline-flex items-center gap-1 font-semibold">
                <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
                <span>Back to login</span>
            </a>
        </div>
    </div>
</div>
@endsection
