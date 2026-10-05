@extends('layouts.app')

@section('title', 'Forgot Password — IntraEats Employee Portal')

@section('content')
<div class="min-h-screen bg-[#FAFAF8] flex flex-col justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <!-- Logo & Branding -->
        <div class="flex items-center justify-center gap-3">
            <div class="w-12 h-12 rounded-2xl bg-[#FF6B1A] flex items-center justify-center text-white shadow-lg shadow-[#FF6B1A]/20">
                <i data-lucide="key-round" class="w-6 h-6"></i>
            </div>
            <div>
                <span class="font-extrabold text-2xl text-[#1C1C1E] tracking-tight block leading-tight">IntraEats</span>
                <span class="text-[11px] font-bold text-[#FF6B1A] uppercase tracking-wider block">Password Assistance</span>
            </div>
        </div>

        <h2 class="mt-6 text-center text-2xl font-extrabold text-[#1C1C1E] tracking-tight">
            Reset Your Employee Password
        </h2>
        <p class="mt-2 text-center text-xs sm:text-sm text-[#64748B]">
            Enter your registered Email or Employee Code to receive a password activation link.
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white py-8 px-6 sm:px-10 shadow-xl rounded-2xl border border-[#E2E8F0]">
            @if(session('status'))
                <div class="mb-5 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs flex items-start gap-3">
                    <i data-lucide="mail-check" class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5"></i>
                    <div>
                        <div class="font-bold text-emerald-900">Email Dispatched</div>
                        <p class="mt-0.5">{{ session('status') }}</p>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-5 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 text-xs flex items-center gap-3">
                    <i data-lucide="alert-circle" class="w-5 h-5 text-red-600 shrink-0"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <!-- Dev preview if in dev/local -->
            @if(session('dev_reset_url'))
                <div class="mb-5 p-3.5 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 text-xs">
                    <div class="font-bold flex items-center gap-1.5">
                        <i data-lucide="sparkles" class="w-4 h-4 text-amber-600"></i>
                        <span>Testing Preview Link:</span>
                    </div>
                    <a href="{{ session('dev_reset_url') }}" class="mt-1 block font-mono text-[11px] text-amber-700 underline break-all">
                        {{ session('dev_reset_url') }}
                    </a>
                </div>
            @endif

            <form action="{{ route('employee.password.email') }}" method="POST" class="space-y-5">
                @csrf

                <div>
                    <label for="login" class="block text-xs font-bold uppercase tracking-wider text-[#64748B] mb-1.5">
                        Employee Code or Email Address
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#94A3B8]">
                            <i data-lucide="badge-check" class="w-5 h-5"></i>
                        </div>
                        <input id="login" name="login" type="text" value="{{ old('login') }}" required autofocus
                            placeholder="e.g. INTRA-EMP-001 or name@intraeats.com"
                            class="w-full bg-[#F8FAFC] border border-[#CBD5E1] text-[#1C1C1E] text-sm rounded-xl pl-11 pr-4 py-3 focus:border-[#FF6B1A] focus:ring-2 focus:ring-[#FF6B1A]/20 transition-all font-mono">
                    </div>
                </div>

                <button type="submit" class="w-full py-3.5 px-4 rounded-xl bg-[#FF6B1A] hover:bg-[#E55607] text-white font-extrabold text-sm tracking-wide shadow-lg shadow-[#FF6B1A]/25 hover:shadow-xl transition-all flex items-center justify-center gap-2">
                    <i data-lucide="send" class="w-4 h-4"></i>
                    <span>Send Password Reset Link</span>
                </button>
            </form>

            <div class="mt-6 pt-5 border-t border-[#E2E8F0] text-center text-xs">
                <a href="{{ route('employee.login') }}" class="font-semibold text-[#64748B] hover:text-[#1C1C1E] flex items-center justify-center gap-1">
                    <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
                    <span>Back to Employee Login</span>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
