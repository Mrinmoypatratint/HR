@extends('layouts.app')

@section('title', 'Forgot Password — IntraEats HR')

@section('content')
<div class="min-h-screen flex items-center justify-center p-4 sm:p-6 bg-[#FAFAF8]">
    <div class="w-full max-w-md bg-white rounded-3xl border border-[#E2E8F0] shadow-xl p-8 sm:p-10 space-y-6">
        <div class="text-center space-y-2">
            <div class="w-14 h-14 mx-auto rounded-2xl bg-[#FFF3EB] text-[#FF6B1A] flex items-center justify-center shadow-sm">
                <i data-lucide="key-round" class="w-7 h-7"></i>
            </div>
            <h1 class="text-2xl font-extrabold text-[#1C1C1E]">Forgot Password</h1>
            <p class="text-xs text-[#64748B]">Enter your registered administrator email address and we'll dispatch a 2FA reset passcode.</p>
        </div>

        @if($errors->any())
        <div class="p-3.5 rounded-xl bg-red-50 border border-red-200 text-red-700 text-xs flex items-center gap-2">
            <i data-lucide="alert-circle" class="w-4 h-4 text-red-600 shrink-0"></i>
            <span>{{ $errors->first() }}</span>
        </div>
        @endif

        @if(session('status'))
        <div class="p-3.5 rounded-xl bg-[#ECFDF5] border border-[#A7F3D0] text-[#065F46] text-xs flex items-center gap-2">
            <i data-lucide="check-circle" class="w-4 h-4 text-[#10B981] shrink-0"></i>
            <span>{{ session('status') }}</span>
        </div>
        @endif

        <form method="POST" action="{{ route('admin.password.email') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-[#64748B] mb-1.5" for="email">Admin Email Address</label>
                <div class="relative">
                    <div class="absolute left-3.5 top-1/2 -translate-y-1/2 text-[#94A3B8]">
                        <i data-lucide="mail" class="w-4 h-4"></i>
                    </div>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="hr@intraeats.com" class="w-full bg-[#F8FAFC] text-[#1C1C1E] text-sm pl-10 pr-4 py-3 rounded-xl border border-[#CBD5E1] focus:border-[#FF6B1A] focus:ring-2 focus:ring-[#FF6B1A]/20">
                </div>
            </div>

            <button type="submit" class="w-full py-3.5 rounded-xl bg-[#FF6B1A] hover:bg-[#E55607] text-white font-extrabold text-sm shadow-md transition-all flex items-center justify-center gap-2">
                <i data-lucide="send" class="w-4 h-4"></i>
                <span>Send Reset Code</span>
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
