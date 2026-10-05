@extends('layouts.app')

@section('title', 'Admin Two-Factor Verification — IntraEats HR')

@section('content')
<div class="min-h-screen flex items-center justify-center p-3 sm:p-6 bg-[#FAFAF8]" x-data="otpVerificationHandler({{ $expiresIn }}, '{{ $devOtp }}')">
    <div class="w-full max-w-lg bg-white rounded-2xl sm:rounded-3xl border border-[#E2E8F0] shadow-2xl overflow-hidden relative">
        <!-- Top Expiry Progress Bar -->
        <div class="h-1.5 w-full bg-[#F1F5F9]">
            <div class="h-full bg-[#FF6B1A] transition-all duration-1000 ease-linear" :style="'width: ' + progressPercent + '%'"></div>
        </div>

        <div class="p-5 sm:p-10 flex flex-col items-center text-center">
            <!-- Icon Chip -->
            <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-[#FFF3EB] text-[#FF6B1A] flex items-center justify-center mb-3 sm:mb-4 shadow-sm shrink-0">
                <i data-lucide="shield-alert" class="w-7 h-7 sm:w-8 sm:h-8"></i>
            </div>

            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-[#F1F5F9] text-[#475569] mb-2">Admin Security Verification</span>
            <h1 class="text-xl sm:text-2xl font-extrabold text-[#1C1C1E] tracking-tight">Two-Factor Authentication</h1>
            <p class="text-xs sm:text-sm text-[#64748B] mt-1 max-w-sm">
                A 6-digit one-time password has been sent to your registered email:
                <strong class="text-[#1C1C1E] block font-mono mt-1 break-all">{{ $email }}</strong>
            </p>

            <!-- Quick Passcode Notice & Auto-Fill Helper -->
            @if($devOtp)
            <div class="mt-4 p-3 rounded-xl bg-[#ECFDF5] border border-[#A7F3D0] text-[#065F46] text-xs flex items-center justify-between w-full">
                <div class="flex items-center gap-2">
                    <i data-lucide="key" class="w-4 h-4 text-[#10B981] shrink-0"></i>
                    <span>Passcode: <strong class="font-mono text-sm tracking-wider text-[#047857]">{{ $devOtp }}</strong></span>
                </div>
                <button type="button" @click="autoFill('{{ $devOtp }}')" class="px-2.5 py-1 rounded-lg bg-[#10B981] hover:bg-[#059669] text-white font-bold text-[11px] transition-all">Auto-fill</button>
            </div>
            @endif

            <!-- Expiry Countdown Timer Box -->
            <div class="w-full mt-4 p-3 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0] flex items-center justify-between text-xs">
                <span class="text-[#64748B] font-medium flex items-center gap-1.5">
                    <i data-lucide="timer" class="w-4 h-4 text-[#FF6B1A]"></i>
                    Code Expiration
                </span>
                <span class="font-mono font-bold text-[#FF6B1A] bg-[#FFF3EB] px-2 py-0.5 rounded" x-text="formatTime(remainingSeconds)"></span>
            </div>

            <!-- Error Alerts -->
            @if($errors->any())
            <div class="w-full mt-4 p-3 rounded-xl bg-red-50 border border-red-200 text-red-700 text-xs flex items-center gap-2">
                <i data-lucide="alert-circle" class="w-4 h-4 text-red-600 shrink-0"></i>
                <span>{{ $errors->first() }}</span>
            </div>
            @endif

            @if(session('status'))
            <div class="w-full mt-4 p-3 rounded-xl bg-[#ECFDF5] border border-[#A7F3D0] text-[#065F46] text-xs flex items-center gap-2">
                <i data-lucide="check-circle" class="w-4 h-4 text-[#10B981] shrink-0"></i>
                <span>{{ session('status') }}</span>
            </div>
            @endif

            <!-- Verification Form -->
            <form method="POST" action="{{ route('admin.otp.verify') }}" class="w-full mt-6 space-y-4 sm:space-y-5">
                @csrf

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-[#64748B] mb-2">Enter 6-Digit Passcode</label>
                    <input type="text" name="otp" x-model="otpValue" maxlength="6" inputmode="numeric" autofocus placeholder="123456" required class="w-full text-center tracking-[6px] sm:tracking-[10px] font-mono text-2xl sm:text-3xl font-extrabold py-3 sm:py-3.5 rounded-2xl bg-[#F8FAFC] border-2 border-[#CBD5E1] text-[#1C1C1E] focus:border-[#FF6B1A] focus:ring-4 focus:ring-[#FF6B1A]/10 shadow-inner">
                </div>

                <button type="submit" class="w-full py-3.5 sm:py-4 rounded-xl bg-[#FF6B1A] hover:bg-[#E55607] text-white font-extrabold text-sm shadow-lg shadow-[#FF6B1A]/25 hover:shadow-xl transition-all flex items-center justify-center gap-2">
                    <i data-lucide="lock" class="w-4 h-4"></i>
                    <span>Verify &amp; Access HR Dashboard</span>
                </button>
            </form>

            <!-- Resend Section -->
            <div class="mt-6 flex items-center justify-between w-full text-xs text-[#64748B] pt-4 border-t border-[#F1F5F9]">
                <span>Didn't receive code?</span>
                <form method="POST" action="{{ route('admin.otp.resend') }}">
                    @csrf
                    <button type="submit" :disabled="resendCountdown > 0" class="font-bold text-[#FF6B1A] hover:underline disabled:text-[#94A3B8] disabled:no-underline flex items-center gap-1">
                        <i data-lucide="refresh-cw" class="w-3.5 h-3.5"></i>
                        <span x-text="resendCountdown > 0 ? 'Resend in ' + resendCountdown + 's' : 'Resend Code'"></span>
                    </button>
                </form>
            </div>

            <div class="mt-4">
                <a href="{{ route('admin.login') }}" class="text-xs text-[#64748B] hover:text-[#1C1C1E] flex items-center gap-1">
                    <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
                    <span>Return to standard login</span>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function otpVerificationHandler(totalSeconds, devOtp) {
    return {
        remainingSeconds: totalSeconds,
        totalSeconds: totalSeconds,
        progressPercent: 100,
        resendCountdown: 45,
        otpValue: devOtp || '',

        init() {
            const timer = setInterval(() => {
                if (this.remainingSeconds > 0) {
                    this.remainingSeconds--;
                    this.progressPercent = Math.max(0, (this.remainingSeconds / this.totalSeconds) * 100);
                } else {
                    clearInterval(timer);
                }

                if (this.resendCountdown > 0) {
                    this.resendCountdown--;
                }
            }, 1000);
        },

        autoFill(code) {
            this.otpValue = code;
        },

        formatTime(seconds) {
            const m = Math.floor(seconds / 60);
            const s = seconds % 60;
            return String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0') + ' remaining';
        }
    };
}
</script>
@endpush
