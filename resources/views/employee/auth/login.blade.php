@extends('layouts.app')

@section('title', 'Employee Sign In — IntraEats & Talisha Software')

@section('content')
<div class="min-h-screen bg-[#FAFAF8] flex flex-col justify-center py-8 sm:py-12 px-3 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <!-- Logo & Branding -->
        <div class="flex items-center justify-center gap-2.5 sm:gap-3">
            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-2xl bg-[#FF6B1A] flex items-center justify-center text-white shadow-lg shadow-[#FF6B1A]/20 shrink-0">
                <i data-lucide="utensils" class="w-5 h-5 sm:w-6 sm:h-6"></i>
            </div>
            <div>
                <span class="font-extrabold text-xl sm:text-2xl text-[#1C1C1E] tracking-tight block leading-tight">IntraEats X Talisha Software</span>
                <span class="text-[10px] sm:text-[11px] font-bold text-[#FF6B1A] uppercase tracking-wider block">Employee Attendance Portal</span>
            </div>
        </div>

        <h2 class="mt-4 sm:mt-6 text-center text-xl sm:text-2xl font-extrabold text-[#1C1C1E] tracking-tight">
            Sign In to Employee Portal
        </h2>
        <p class="mt-1.5 sm:mt-2 text-center text-xs sm:text-sm text-[#64748B]">
            Punch attendance, record project work, and view your personal ledger.
        </p>
    </div>

    <div class="mt-6 sm:mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white py-6 sm:py-8 px-5 sm:px-10 shadow-xl rounded-2xl border border-[#E2E8F0]">
            <!-- Status Notifications -->
            @if(session('success'))
                <div class="mb-5 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs flex items-center gap-3">
                    <i data-lucide="check-circle-2" class="w-5 h-5 text-emerald-600 shrink-0"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('info'))
                <div class="mb-5 p-4 rounded-xl bg-blue-50 border border-blue-200 text-blue-800 text-xs flex items-center gap-3">
                    <i data-lucide="info" class="w-5 h-5 text-blue-600 shrink-0"></i>
                    <span>{{ session('info') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-5 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 text-xs space-y-1">
                    <div class="font-bold flex items-center gap-1.5 text-red-800">
                        <i data-lucide="alert-triangle" class="w-4 h-4"></i>
                        <span>Authentication Failed</span>
                    </div>
                    @foreach($errors->all() as $error)
                        <p class="pl-5 text-red-600">{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form action="{{ route('employee.login.submit') }}" method="POST" class="space-y-5" x-data="{ showPass: false }">
                @csrf

                <!-- Employee Code or Email -->
                <div>
                    <label for="login" class="block text-xs font-bold uppercase tracking-wider text-[#64748B] mb-1.5">
                        Employee Code or Email Address
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#94A3B8]">
                            <i data-lucide="badge-check" class="w-5 h-5"></i>
                        </div>
                        <input id="login" name="login" type="text" value="{{ old('login', request('code')) }}" required autofocus
                            placeholder="e.g. INTRA-EMP-001 or name@intraeats.com"
                            class="w-full bg-[#F8FAFC] border border-[#CBD5E1] text-[#1C1C1E] text-sm rounded-xl pl-11 pr-4 py-3 focus:border-[#FF6B1A] focus:ring-2 focus:ring-[#FF6B1A]/20 transition-all font-mono">
                    </div>
                </div>

                <!-- Password -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="password" class="block text-xs font-bold uppercase tracking-wider text-[#64748B]">
                            Password
                        </label>
                        <a href="{{ route('employee.password.forgot') }}" class="text-xs font-semibold text-[#FF6B1A] hover:underline">
                            Forgot Password?
                        </a>
                    </div>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#94A3B8]">
                            <i data-lucide="lock" class="w-5 h-5"></i>
                        </div>
                        <input id="password" name="password" :type="showPass ? 'text' : 'password'" required
                            placeholder="Enter your employee account password"
                            class="w-full bg-[#F8FAFC] border border-[#CBD5E1] text-[#1C1C1E] text-sm rounded-xl pl-11 pr-11 py-3 focus:border-[#FF6B1A] focus:ring-2 focus:ring-[#FF6B1A]/20 transition-all">
                        <button type="button" @click="showPass = !showPass" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-[#94A3B8] hover:text-[#1C1C1E]">
                            <i :data-lucide="showPass ? 'eye-off' : 'eye'" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded text-[#FF6B1A] focus:ring-[#FF6B1A]/20 border-slate-300">
                        <span class="text-xs text-[#64748B] font-medium select-none">Remember on this device</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full py-3.5 px-4 rounded-xl bg-[#FF6B1A] hover:bg-[#E55607] text-white font-extrabold text-sm tracking-wide shadow-lg shadow-[#FF6B1A]/25 hover:shadow-xl transition-all flex items-center justify-center gap-2">
                    <i data-lucide="log-in" class="w-4 h-4"></i>
                    <span>Sign In to Portal</span>
                </button>
            </form>

            <!-- Quick Demo Credentials Box -->
            <div class="mt-6 pt-5 border-t border-[#E2E8F0]">
                <div class="bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl p-3.5 text-xs text-[#64748B]">
                    <div class="font-bold text-[#1C1C1E] flex items-center gap-1.5 mb-1.5">
                        <i data-lucide="key-round" class="w-3.5 h-3.5 text-[#FF6B1A]"></i>
                        <span>Testing / Demo Staff Credentials:</span>
                    </div>
                    <div class="space-y-1 font-mono text-[11px]">
                        <div>Code: <strong class="text-[#1C1C1E]">INTRA-EMP-001</strong> &bull; Pass: <strong class="text-[#FF6B1A]">Password123!</strong></div>
                        <div class="text-[#94A3B8] text-[10px] font-sans">Or use the password set link sent to newly created employees.</div>
                    </div>
                </div>
            </div>

            <!-- Alternative Links -->
            <div class="mt-6 flex flex-col gap-2 text-center text-xs">
                <a href="{{ route('employee.portal') }}" class="font-semibold text-[#64748B] hover:text-[#1C1C1E] flex items-center justify-center gap-1">
                    <i data-lucide="tablet" class="w-3.5 h-3.5"></i>
                    <span>Switch to Kiosk Quick Punch Mode</span>
                </a>
                <a href="{{ route('admin.login') }}" class="font-semibold text-[#FF6B1A] hover:underline flex items-center justify-center gap-1">
                    <i data-lucide="shield" class="w-3.5 h-3.5"></i>
                    <span>Admin &amp; HR Manager Sign In &rarr;</span>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
