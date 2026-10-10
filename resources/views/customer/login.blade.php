<!DOCTYPE html>
<html lang="fa" dir="rtl" class="h-full bg-[#F7F9FA]/35 text-[#353535]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ورود مشتریان - بی کافه (B Cafe)</title>

    <link rel="icon" type="image/svg+xml" href="{{ asset('images/logo.svg') }}?v=2.0.2">
    <link rel="alternate icon" type="image/x-icon" href="{{ asset('favicon.ico') }}?v=2.0.2">
    <link rel="apple-touch-icon" href="{{ asset('favicon.png') }}?v=2.0.2">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body x-data="customerLoginFlow()" class="h-full flex items-center justify-center p-4 antialiased selection:bg-[#3C6E71] selection:text-[#F7F9FA]">
    <div class="w-full max-w-md bg-white border border-[#353535]/15 p-6 sm:p-8 rounded-3xl shadow-xl space-y-6">
        <!-- Header -->
        <div class="text-center space-y-3">
            <img src="{{ asset('images/logo.jpeg') }}" alt="بی کافه (B Cafe)" class="w-18 h-18 rounded-2xl object-cover border border-[#353535]/15 mx-auto shadow-2xs">
            <h2 class="text-xl font-black text-[#353535] tracking-tight">ورود یا ثبت‌نام مشتریان بی کافه</h2>
            <p class="text-xs text-[#353535]/70">ورود سریع با شماره موبایل؛ در صورت عدم عضویت قبلی، حساب کاربری شما به صورت خودکار ایجاد می‌گردد.</p>
        </div>

        @if(session('success'))
            <div class="p-3.5 rounded-2xl bg-[#3C6E71]/15 border border-[#3C6E71]/30 text-[#353535] text-xs font-bold flex items-center gap-2">
                <svg class="w-4 h-4 text-[#3C6E71] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                <span>{{ session('success') }}</span>
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

        <!-- Step 1: Request OTP -->
        <div x-show="step === 1" x-transition class="space-y-4">
            <form method="POST" action="{{ route('customer.send-otp') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-[#353535] mb-1.5">شماره موبایل <span class="text-[#B44A4A]">*</span></label>
                    <input type="tel" name="phone" x-model="phone" required autofocus placeholder="09171234567" dir="ltr" autocomplete="tel" inputmode="numeric"
                           class="w-full px-4 py-3.5 rounded-xl bg-[#F7F9FA]/30 border border-[#353535]/15 text-[#353535] placeholder-[#353535]/40 text-base font-mono text-start tabular-nums focus:outline-none focus:border-[#3C6E71] focus:bg-white transition-all min-h-[48px]">
                    <span class="text-[11px] text-[#353535]/60 block mt-1">کد تایید ۶ رقمی برای این شماره پیامک خواهد شد.</span>
                </div>

                <button type="submit" class="w-full py-3.5 bg-[#284B63] hover:bg-[#9B561B] text-[#F7F9FA] font-black rounded-xl text-xs sm:text-sm btn-tactile shadow-md min-h-[48px] flex items-center justify-center gap-2">
                    <span>دریافت کد تایید پیامکی</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                </button>
            </form>
        </div>

        <!-- Step 2: Verify OTP -->
        <div x-show="step === 2" x-transition class="space-y-4" style="display: none;">
            <div class="p-3 rounded-xl bg-[#F7F9FA]/60 border border-[#3C6E71]/40 flex items-center justify-between text-xs">
                <span class="text-[#353535]/70">ارسال شده به: <strong class="font-mono tabular-nums text-[#353535]" dir="ltr" x-text="phone"></strong></span>
                <button type="button" @click="step = 1" class="text-xs text-[#284B63] font-bold hover:underline">ویرایش شماره</button>
            </div>

            <form method="POST" action="{{ route('customer.verify-otp') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="phone" :value="phone">

                <div>
                    <label class="block text-xs font-bold text-[#353535] mb-1.5">کد تایید پیامک‌شده <span class="text-[#B44A4A]">*</span></label>
                    <input type="text" name="code" required autofocus placeholder="123456" dir="ltr" maxlength="6" autocomplete="one-time-code" inputmode="numeric"
                           class="w-full px-4 py-3.5 rounded-xl bg-[#F7F9FA]/30 border border-[#353535]/15 text-[#353535] placeholder-[#353535]/40 text-lg font-mono text-center tracking-widest tabular-nums focus:outline-none focus:border-[#3C6E71] focus:bg-white transition-all min-h-[48px]">
                </div>

                <button type="submit" class="w-full py-3.5 bg-[#353535] hover:bg-[#1E2912] text-[#F7F9FA] font-black rounded-xl text-xs sm:text-sm btn-tactile shadow-md min-h-[48px]">
                    ورود به حساب کاربری
                </button>
            </form>

            <form method="POST" action="{{ route('customer.send-otp') }}" class="text-center pt-1">
                @csrf
                <input type="hidden" name="phone" :value="phone">
                <button type="submit" class="text-xs text-[#3C6E71] font-bold hover:underline">
                    کد را دریافت نکردید؟ ارسال مجدد
                </button>
            </form>
        </div>

        <div class="text-center pt-2 border-t border-[#353535]/10">
            <a href="{{ route('storefront.menu') }}" class="inline-flex items-center gap-1.5 text-xs text-[#353535]/70 hover:text-[#284B63] font-bold">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span>بازگشت به منوی بی کافه</span>
            </a>
        </div>
    </div>

    <script>
    function customerLoginFlow() {
        return {
            step: {{ session('otp_sent') ? 2 : 1 }},
            phone: '{{ old('phone', session('phone', '')) }}',
        }
    }
    </script>
</body>
</html>
