<!DOCTYPE html>
<html lang="fa" dir="rtl" class="h-full bg-[#F7F9FA] text-[#353535]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>ورود یا ثبت‌نام در بی کافه (B Cafe)</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/logo.svg') }}?v=2.0.2">
    <link rel="alternate icon" type="image/x-icon" href="{{ asset('favicon.ico') }}?v=2.0.2">
    <link rel="apple-touch-icon" href="{{ asset('favicon.png') }}?v=2.0.2">
    <link rel="manifest" href="/manifest.webmanifest">
    <meta name="theme-color" content="#353535">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body x-data="unifiedAuthFlow()" class="h-full flex items-center justify-center p-4 antialiased selection:bg-[#3C6E71] selection:text-[#F7F9FA]">
    <div class="w-full max-w-md bg-white border border-[#353535]/15 p-6 sm:p-8 rounded-3xl shadow-xl space-y-6">
        
        <!-- Header -->
        <div class="text-center space-y-3">
            <a href="{{ route('storefront.menu') }}" class="inline-block hover:opacity-90 transition-opacity">
                <img src="{{ asset('images/logo.jpeg') }}" alt="بی کافه (B Cafe)" class="w-20 h-20 rounded-2xl object-cover border border-[#353535]/15 mx-auto shadow-sm">
            </a>
            <div>
                <h1 class="text-xl font-black text-[#353535] tracking-tight">بی کافه (B Cafe)</h1>
                <p class="text-xs text-[#353535]/70 mt-1">ورود به حساب کاربری یا عضویت سریع</p>
            </div>
        </div>

        <!-- Global Session Messages -->
        @if(session('success'))
            <div class="p-3.5 rounded-2xl bg-[#3C6E71]/15 border border-[#3C6E71]/30 text-[#353535] text-xs font-bold flex items-center gap-2">
                <svg class="w-4 h-4 text-[#3C6E71] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('info'))
            <div class="p-3.5 rounded-2xl bg-[#284B63]/15 border border-[#284B63]/30 text-[#284B63] text-xs font-bold flex items-center gap-2">
                <svg class="w-4 h-4 text-[#284B63] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>{{ session('info') }}</span>
            </div>
        @endif

        @if(session('error') || $errors->any())
            <div class="p-3.5 rounded-2xl bg-[#B44A4A]/10 border border-[#B44A4A]/30 text-[#B44A4A] text-xs space-y-1 font-bold">
                @if(session('error'))
                    <div>{{ session('error') }}</div>
                @endif
                @foreach($errors->all() as $error)
                    <div>• {{ $error }}</div>
                @endforeach
            </div>
        @endif

        <!-- Tab Switcher: OTP SMS vs Password -->
        <div class="grid grid-cols-2 gap-1.5 p-1 bg-[#F7F9FA] rounded-2xl border border-[#353535]/10 text-xs font-bold">
            <button type="button" @click="activeTab = 'otp'"
                    :class="activeTab === 'otp' ? 'bg-white text-[#353535] shadow-xs' : 'text-[#353535]/60 hover:text-[#353535]'"
                    class="py-2.5 px-3 rounded-xl transition-all flex items-center justify-center gap-1.5 min-h-[42px]">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                <span>کد پیامکی (سریع)</span>
            </button>
            <button type="button" @click="activeTab = 'password'"
                    :class="activeTab === 'password' ? 'bg-white text-[#353535] shadow-xs' : 'text-[#353535]/60 hover:text-[#353535]'"
                    class="py-2.5 px-3 rounded-xl transition-all flex items-center justify-center gap-1.5 min-h-[42px]">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                <span>کلمه عبور</span>
            </button>
        </div>

        <!-- TAB 1: OTP SMS FLOW -->
        <div x-show="activeTab === 'otp'" class="space-y-4">
            
            <!-- Dynamic Inline Message -->
            <div x-show="errorMessage" x-cloak class="p-3.5 rounded-2xl bg-[#B44A4A]/10 border border-[#B44A4A]/30 text-[#B44A4A] text-xs font-bold flex items-center gap-2">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <span x-text="errorMessage"></span>
            </div>

            <div x-show="successMessage" x-cloak class="p-3.5 rounded-2xl bg-[#3C6E71]/15 border border-[#3C6E71]/30 text-[#3C6E71] text-xs font-bold flex items-center gap-2">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                <span x-text="successMessage"></span>
            </div>

            <!-- Step 1: Request OTP -->
            <div x-show="otpStep === 1" class="space-y-4">
                <form @submit.prevent="submitSendOtp()" class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-[#353535] mb-1.5">شماره موبایل <span class="text-[#B44A4A]">*</span></label>
                        <input type="tel" name="phone" x-model="phone" required autofocus placeholder="09171234567" dir="ltr" autocomplete="tel" inputmode="numeric"
                               :disabled="loading"
                               class="w-full px-4 py-3.5 rounded-xl bg-[#F7F9FA]/60 border border-[#353535]/15 text-[#353535] placeholder-[#353535]/40 text-base font-mono text-start tabular-nums focus:outline-none focus:border-[#3C6E71] focus:bg-white transition-all min-h-[48px] disabled:opacity-50">
                        <span class="text-[11px] text-[#353535]/60 block mt-1.5">کد تایید یکبارمصرف به این شماره پیامک خواهد شد.</span>
                    </div>

                    <button type="submit" :disabled="loading"
                            class="w-full py-3.5 bg-[#3C6E71] hover:bg-[#284B63] text-white font-black rounded-xl text-xs sm:text-sm btn-tactile shadow-md min-h-[48px] flex items-center justify-center gap-2 transition-all disabled:opacity-60 disabled:cursor-not-allowed">
                        <template x-if="!loading">
                            <span class="inline-flex items-center gap-2">
                                <span>دریافت کد تایید پیامکی</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                            </span>
                        </template>
                        <template x-if="loading">
                            <span class="inline-flex items-center gap-2">
                                <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                                <span>در حال ارسال کد...</span>
                            </span>
                        </template>
                    </button>
                </form>
            </div>

            <!-- Step 2: Verify OTP -->
            <div x-show="otpStep === 2" class="space-y-4" style="display: none;">
                <div class="p-3 rounded-xl bg-[#F7F9FA] border border-[#3C6E71]/40 flex items-center justify-between text-xs">
                    <span class="text-[#353535]/70">ارسال شده به: <strong class="font-mono tabular-nums text-[#353535]" dir="ltr" x-text="phone"></strong></span>
                    <button type="button" @click="otpStep = 1; errorMessage = ''; successMessage = ''" class="text-xs text-[#284B63] font-bold hover:underline">ویرایش شماره</button>
                </div>

                <form method="POST" action="{{ route('customer.verify-otp') }}" class="space-y-4">
                    @csrf
                    <input type="hidden" name="phone" :value="phone">

                    <div>
                        <label class="block text-xs font-bold text-[#353535] mb-1.5">کد تایید پیامک‌شده <span class="text-[#B44A4A]">*</span></label>
                        <input type="text" name="code" required autofocus placeholder="123456" dir="ltr" maxlength="6" autocomplete="one-time-code" inputmode="numeric"
                               class="w-full px-4 py-3.5 rounded-xl bg-[#F7F9FA]/60 border border-[#353535]/15 text-[#353535] placeholder-[#353535]/40 text-lg font-mono text-center tracking-widest tabular-nums focus:outline-none focus:border-[#3C6E71] focus:bg-white transition-all min-h-[48px]">
                    </div>

                    <button type="submit" class="w-full py-3.5 bg-[#353535] hover:bg-[#284B63] text-white font-black rounded-xl text-xs sm:text-sm btn-tactile shadow-md min-h-[48px]">
                        تایید و ورود به حساب
                    </button>
                </form>

                <div class="text-center pt-1">
                    <button type="button" @click="resendOtp()" :disabled="cooldown > 0 || loading"
                            class="text-xs text-[#3C6E71] font-bold hover:underline disabled:text-[#353535]/40 disabled:no-underline disabled:cursor-not-allowed">
                        <span x-show="cooldown === 0">کد را دریافت نکردید؟ ارسال مجدد پیامک</span>
                        <span x-show="cooldown > 0" x-cloak>ارسال مجدد تا <span class="font-mono font-bold" x-text="cooldown"></span> ثانیه دیگر</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- TAB 2: PASSWORD FLOW -->
        <div x-show="activeTab === 'password'" class="space-y-4" style="display: none;">
            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-bold text-[#353535] mb-1.5">شماره موبایل یا ایمیل</label>
                    <input type="text" name="login" value="{{ old('login') }}" required autofocus dir="ltr" placeholder="0917... یا ایمیل کاربری"
                           class="w-full px-4 py-3.5 rounded-xl bg-[#F7F9FA]/60 border border-[#353535]/15 text-[#353535] placeholder-[#353535]/40 text-xs sm:text-sm font-mono focus:outline-none focus:border-[#3C6E71] focus:bg-white transition-all min-h-[48px]">
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#353535] mb-1.5">کلمه عبور</label>
                    <input type="password" name="password" required dir="ltr" placeholder="••••••••"
                           class="w-full px-4 py-3.5 rounded-xl bg-[#F7F9FA]/60 border border-[#353535]/15 text-[#353535] placeholder-[#353535]/40 text-xs sm:text-sm focus:outline-none focus:border-[#3C6E71] focus:bg-white transition-all min-h-[48px]">
                </div>

                <div class="flex items-center justify-between text-xs">
                    <label class="flex items-center gap-2 cursor-pointer text-[#353535]/70">
                        <input type="checkbox" name="remember" class="rounded bg-white border-[#353535]/20 text-[#3C6E71] focus:ring-[#3C6E71]">
                        <span>مرا به خاطر بسپار</span>
                    </label>
                </div>

                <button type="submit" class="w-full py-3.5 bg-[#284B63] hover:bg-[#353535] text-white font-black rounded-xl text-xs sm:text-sm btn-tactile shadow-md min-h-[48px]">
                    ورود به حساب کاربری
                </button>
            </form>
        </div>

        <!-- Footer Navigation -->
        <div class="text-center pt-2 border-t border-[#353535]/10">
            <a href="{{ route('storefront.menu') }}" class="inline-flex items-center gap-1.5 text-xs text-[#353535]/70 hover:text-[#284B63] font-bold">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span>بازگشت به منوی سفارش آنلاین بی کافه</span>
            </a>
        </div>
    </div>

    <script>
    function unifiedAuthFlow() {
        return {
            activeTab: '{{ old("login") || $errors->has("login") || $errors->has("password") ? "password" : "otp" }}',
            otpStep: {{ session('otp_sent') ? 2 : 1 }},
            phone: '{{ session("phone") ?? old("phone") ?? "" }}',
            loading: false,
            errorMessage: '',
            successMessage: '{{ session("success") ?? "" }}',
            cooldown: 0,
            timerInterval: null,

            startCooldown(seconds = 60) {
                this.cooldown = seconds;
                if (this.timerInterval) clearInterval(this.timerInterval);
                this.timerInterval = setInterval(() => {
                    if (this.cooldown > 0) {
                        this.cooldown--;
                    } else {
                        clearInterval(this.timerInterval);
                    }
                }, 1000);
            },

            async submitSendOtp() {
                this.errorMessage = '';
                this.successMessage = '';

                const cleanPhone = (this.phone || '').trim();
                if (!/^09\d{9}$/.test(cleanPhone)) {
                    this.errorMessage = 'فرمت شماره موبایل باید با ۰۹ شروع شده و ۱۱ رقم باشد.';
                    return;
                }

                this.loading = true;
                try {
                    const response = await fetch('{{ route("customer.send-otp") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        },
                        body: JSON.stringify({ phone: cleanPhone })
                    });

                    const data = await response.json().catch(() => ({}));

                    if (response.ok && data.success) {
                        this.phone = cleanPhone;
                        this.otpStep = 2;
                        this.successMessage = data.message || 'کد تایید پیامک شد. لطفاً کد دریافتی را وارد فرمایید.';
                        this.startCooldown(60);
                        if (window.BCafeToast) {
                            window.BCafeToast.show(this.successMessage, 'success');
                        }
                    } else {
                        const errMsg = data.message || (data.errors ? Object.values(data.errors).flat().join(' ') : 'خطا در ارسال کد تایید.');
                        this.errorMessage = errMsg;
                        if (window.BCafeToast) {
                            window.BCafeToast.show(errMsg, 'error');
                        }
                    }
                } catch (err) {
                    this.errorMessage = 'خطای ارتباط با سرور. لطفاً اتصال اینترنت خود را بررسی نمایید.';
                } finally {
                    this.loading = false;
                }
            },

            async resendOtp() {
                if (this.cooldown > 0 || this.loading) return;
                await this.submitSendOtp();
            }
        }
    }
    </script>
</body>
</html>
