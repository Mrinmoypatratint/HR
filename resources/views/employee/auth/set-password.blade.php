@extends('layouts.app')

@section('title', 'Set Your Password — IntraEats & Talisha Software')

@section('content')
<div class="min-h-screen bg-[#FAFAF8] flex flex-col justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <!-- Logo & Branding -->
        <div class="flex items-center justify-center gap-3">
            <div class="w-12 h-12 rounded-2xl bg-[#FF6B1A] flex items-center justify-center text-white shadow-lg shadow-[#FF6B1A]/20">
                <i data-lucide="shield-check" class="w-6 h-6"></i>
            </div>
            <div>
                <span class="font-extrabold text-2xl text-[#1C1C1E] tracking-tight block leading-tight">IntraEats X Talisha Software</span>
                <span class="text-[11px] font-bold text-[#FF6B1A] uppercase tracking-wider block">Employee Attendance Portal</span>
            </div>
        </div>

        <h2 class="mt-6 text-center text-2xl font-extrabold text-[#1C1C1E] tracking-tight">
            {{ $isNew ? 'Set Your Account Password' : 'Reset Your Password' }}
        </h2>
        <p class="mt-2 text-center text-xs sm:text-sm text-[#64748B]">
            Configure your secure password to access your employee dashboard &amp; attendance portal.
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white py-8 px-6 sm:px-10 shadow-xl rounded-2xl border border-[#E2E8F0]">
            <!-- Employee Identity Summary Pill -->
            <div class="mb-6 p-4 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0] flex items-center gap-3.5">
                @if($employee->photo_url)
                    <img src="{{ $employee->photo_url }}" class="w-12 h-12 rounded-xl object-cover ring-2 ring-[#FF6B1A]/20" alt="Avatar">
                @else
                    <div class="w-12 h-12 rounded-xl bg-[#FF6B1A] text-white font-extrabold flex items-center justify-center text-base">
                        {{ $employee->initials }}
                    </div>
                @endif
                <div class="flex-1 min-w-0">
                    <h3 class="font-bold text-sm text-[#1C1C1E] truncate">{{ $employee->full_name }}</h3>
                    <div class="flex items-center gap-2 mt-0.5 text-xs text-[#64748B] font-mono">
                        <span class="text-[#FF6B1A] font-bold">{{ $employee->employee_code }}</span>
                        <span>&bull;</span>
                        <span>{{ $employee->department }}</span>
                    </div>
                    <div class="text-[11px] text-[#94A3B8] truncate mt-0.5">{{ $employee->email }}</div>
                </div>
            </div>

            @if($errors->any())
                <div class="mb-5 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 text-xs space-y-1">
                    <div class="font-bold flex items-center gap-1.5 text-red-800">
                        <i data-lucide="alert-triangle" class="w-4 h-4"></i>
                        <span>Validation Error</span>
                    </div>
                    @foreach($errors->all() as $error)
                        <p class="pl-5 text-red-600">{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form action="{{ route('employee.password.set.submit') }}" method="POST" class="space-y-5" x-data="{
                password: '',
                password_confirmation: '',
                showPass: false,
                get hasMinLength() { return this.password.length >= 8; },
                get passwordsMatch() { return this.password && this.password === this.password_confirmation; }
            }">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <input type="hidden" name="email" value="{{ $email }}">

                <!-- New Password -->
                <div>
                    <label for="password" class="block text-xs font-bold uppercase tracking-wider text-[#64748B] mb-1.5">
                        New Password (Min 8 characters)
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#94A3B8]">
                            <i data-lucide="lock" class="w-5 h-5"></i>
                        </div>
                        <input id="password" name="password" x-model="password" :type="showPass ? 'text' : 'password'" required
                            placeholder="Create a strong password"
                            class="w-full bg-[#F8FAFC] border border-[#CBD5E1] text-[#1C1C1E] text-sm rounded-xl pl-11 pr-11 py-3 focus:border-[#FF6B1A] focus:ring-2 focus:ring-[#FF6B1A]/20 transition-all">
                        <button type="button" @click="showPass = !showPass" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-[#94A3B8] hover:text-[#1C1C1E]">
                            <i :data-lucide="showPass ? 'eye-off' : 'eye'" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="password_confirmation" class="block text-xs font-bold uppercase tracking-wider text-[#64748B] mb-1.5">
                        Confirm New Password
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#94A3B8]">
                            <i data-lucide="check-check" class="w-5 h-5"></i>
                        </div>
                        <input id="password_confirmation" name="password_confirmation" x-model="password_confirmation" :type="showPass ? 'text' : 'password'" required
                            placeholder="Re-enter your password"
                            class="w-full bg-[#F8FAFC] border border-[#CBD5E1] text-[#1C1C1E] text-sm rounded-xl pl-11 pr-4 py-3 focus:border-[#FF6B1A] focus:ring-2 focus:ring-[#FF6B1A]/20 transition-all">
                    </div>
                </div>

                <!-- Live Password Helper -->
                <div class="p-3 bg-[#F8FAFC] rounded-xl border border-[#E2E8F0] space-y-1.5 text-xs">
                    <div class="flex items-center gap-2" :class="hasMinLength ? 'text-emerald-700' : 'text-[#64748B]'">
                        <i :data-lucide="hasMinLength ? 'check-circle' : 'circle'" class="w-3.5 h-3.5"></i>
                        <span>At least 8 characters length</span>
                    </div>
                    <div class="flex items-center gap-2" :class="passwordsMatch ? 'text-emerald-700' : 'text-[#64748B]'">
                        <i :data-lucide="passwordsMatch ? 'check-circle' : 'circle'" class="w-3.5 h-3.5"></i>
                        <span>Passwords match</span>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" :disabled="!hasMinLength || !passwordsMatch" class="w-full py-3.5 px-4 rounded-xl bg-[#16A34A] hover:bg-[#15803D] text-white font-extrabold text-sm tracking-wide shadow-lg shadow-[#16A34A]/25 hover:shadow-xl transition-all flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                    <i data-lucide="shield-check" class="w-4 h-4"></i>
                    <span>{{ $isNew ? 'Activate Account & Sign In' : 'Update Password & Sign In' }}</span>
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
