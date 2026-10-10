<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\SmsSetting;
use Illuminate\Http\Request;

class SmsSettingController extends Controller
{
    public const DEFAULT_WELCOME_TEXT = SmsSetting::DEFAULT_WELCOME_TEXT;
    public const DEFAULT_DAILY_USAGE_TEXT = SmsSetting::DEFAULT_DAILY_USAGE_TEXT;
    public const DEFAULT_REMINDER_TEXT = SmsSetting::DEFAULT_REMINDER_TEXT;
    public const DEFAULT_OTP_TEXT = SmsSetting::DEFAULT_OTP_TEXT;
    public const DEFAULT_ORDER_SUBMITTED_TEXT = SmsSetting::DEFAULT_ORDER_SUBMITTED_TEXT;
    public const DEFAULT_ORDER_PREPARING_TEXT = SmsSetting::DEFAULT_ORDER_PREPARING_TEXT;
    public const DEFAULT_ORDER_READY_TEXT = SmsSetting::DEFAULT_ORDER_READY_TEXT;
    public const DEFAULT_ORDER_ON_THE_WAY_TEXT = SmsSetting::DEFAULT_ORDER_ON_THE_WAY_TEXT;
    public const DEFAULT_ORDER_DELIVERED_TEXT = SmsSetting::DEFAULT_ORDER_DELIVERED_TEXT;
    public const DEFAULT_ORDER_CANCELLED_TEXT = SmsSetting::DEFAULT_ORDER_CANCELLED_TEXT;

    public function edit()
    {
        $cafeId = auth()->user()->cafe_id ?? 1;

        $setting = SmsSetting::firstOrCreate(
            ['cafe_id' => $cafeId],
            [
                'driver' => 'mellipayamak',
                'reminder_hour' => 18,
                'is_reminder_enabled' => true,
                'is_welcome_enabled' => true,
                'is_daily_usage_enabled' => true,
                'is_order_sms_enabled' => true,
                'template_text_welcome' => self::DEFAULT_WELCOME_TEXT,
                'template_text_daily_usage' => self::DEFAULT_DAILY_USAGE_TEXT,
                'template_text_reminder' => self::DEFAULT_REMINDER_TEXT,
                'template_text_otp' => self::DEFAULT_OTP_TEXT,
                'template_text_order_submitted' => self::DEFAULT_ORDER_SUBMITTED_TEXT,
                'template_text_order_preparing' => self::DEFAULT_ORDER_PREPARING_TEXT,
                'template_text_order_ready' => self::DEFAULT_ORDER_READY_TEXT,
                'template_text_order_on_the_way' => self::DEFAULT_ORDER_ON_THE_WAY_TEXT,
                'template_text_order_delivered' => self::DEFAULT_ORDER_DELIVERED_TEXT,
                'template_text_order_cancelled' => self::DEFAULT_ORDER_CANCELLED_TEXT,
            ]
        );

        // Ensure defaults are populated if null or empty
        $dirty = false;
        $defaults = [
            'template_text_welcome' => self::DEFAULT_WELCOME_TEXT,
            'template_text_daily_usage' => self::DEFAULT_DAILY_USAGE_TEXT,
            'template_text_reminder' => self::DEFAULT_REMINDER_TEXT,
            'template_text_otp' => self::DEFAULT_OTP_TEXT,
            'template_text_order_submitted' => self::DEFAULT_ORDER_SUBMITTED_TEXT,
            'template_text_order_preparing' => self::DEFAULT_ORDER_PREPARING_TEXT,
            'template_text_order_ready' => self::DEFAULT_ORDER_READY_TEXT,
            'template_text_order_on_the_way' => self::DEFAULT_ORDER_ON_THE_WAY_TEXT,
            'template_text_order_delivered' => self::DEFAULT_ORDER_DELIVERED_TEXT,
            'template_text_order_cancelled' => self::DEFAULT_ORDER_CANCELLED_TEXT,
        ];

        foreach ($defaults as $field => $defaultVal) {
            if (empty($setting->$field)) {
                $setting->$field = $defaultVal;
                $dirty = true;
            }
        }

        if ($dirty) {
            $setting->save();
        }

        return view('settings.sms', compact('setting'));
    }

    public function update(Request $request)
    {
        $cafeId = auth()->user()->cafe_id ?? 1;

        $setting = SmsSetting::firstOrCreate(['cafe_id' => $cafeId]);

        $validated = $request->validate([
            'username' => 'nullable|string|max:100',
            'password' => 'nullable|string|max:255',
            'from_number' => 'nullable|string|max:50',
            'reminder_hour' => 'required|integer|min:0|max:23',
            'is_reminder_enabled' => 'nullable|boolean',
            'is_welcome_enabled' => 'nullable|boolean',
            'is_daily_usage_enabled' => 'nullable|boolean',
            'is_order_sms_enabled' => 'nullable|boolean',
            'pattern_otp' => 'nullable|string|max:50',
            'pattern_welcome' => 'nullable|string|max:50',
            'pattern_daily_usage' => 'nullable|string|max:50',
            'pattern_reminder' => 'nullable|string|max:50',
            'pattern_expiry_warning' => 'nullable|string|max:50',
            'pattern_order_submitted' => 'nullable|string|max:50',
            'pattern_order_preparing' => 'nullable|string|max:50',
            'pattern_order_ready' => 'nullable|string|max:50',
            'pattern_order_on_the_way' => 'nullable|string|max:50',
            'pattern_order_delivered' => 'nullable|string|max:50',
            'pattern_order_cancelled' => 'nullable|string|max:50',
            'template_text_welcome' => 'nullable|string|max:1000',
            'template_text_daily_usage' => 'nullable|string|max:1000',
            'template_text_reminder' => 'nullable|string|max:1000',
            'template_text_otp' => 'nullable|string|max:1000',
            'template_text_order_submitted' => 'nullable|string|max:1000',
            'template_text_order_preparing' => 'nullable|string|max:1000',
            'template_text_order_ready' => 'nullable|string|max:1000',
            'template_text_order_on_the_way' => 'nullable|string|max:1000',
            'template_text_order_delivered' => 'nullable|string|max:1000',
            'template_text_order_cancelled' => 'nullable|string|max:1000',
        ]);

        $updateData = [
            'username' => $validated['username'] ?? null,
            'from_number' => $validated['from_number'] ?? null,
            'reminder_hour' => (int) $validated['reminder_hour'],
            'is_reminder_enabled' => $request->has('is_reminder_enabled'),
            'is_welcome_enabled' => $request->has('is_welcome_enabled'),
            'is_daily_usage_enabled' => $request->has('is_daily_usage_enabled'),
            'is_order_sms_enabled' => $request->has('is_order_sms_enabled'),
            'pattern_otp' => $validated['pattern_otp'] ?? null,
            'pattern_welcome' => $validated['pattern_welcome'] ?? null,
            'pattern_daily_usage' => $validated['pattern_daily_usage'] ?? null,
            'pattern_reminder' => $validated['pattern_reminder'] ?? null,
            'pattern_expiry_warning' => $validated['pattern_expiry_warning'] ?? null,
            'pattern_order_submitted' => $validated['pattern_order_submitted'] ?? null,
            'pattern_order_preparing' => $validated['pattern_order_preparing'] ?? null,
            'pattern_order_ready' => $validated['pattern_order_ready'] ?? null,
            'pattern_order_on_the_way' => $validated['pattern_order_on_the_way'] ?? null,
            'pattern_order_delivered' => $validated['pattern_order_delivered'] ?? null,
            'pattern_order_cancelled' => $validated['pattern_order_cancelled'] ?? null,
            'template_text_welcome' => $validated['template_text_welcome'] ?? self::DEFAULT_WELCOME_TEXT,
            'template_text_daily_usage' => $validated['template_text_daily_usage'] ?? self::DEFAULT_DAILY_USAGE_TEXT,
            'template_text_reminder' => $validated['template_text_reminder'] ?? self::DEFAULT_REMINDER_TEXT,
            'template_text_otp' => $validated['template_text_otp'] ?? self::DEFAULT_OTP_TEXT,
            'template_text_order_submitted' => $validated['template_text_order_submitted'] ?? self::DEFAULT_ORDER_SUBMITTED_TEXT,
            'template_text_order_preparing' => $validated['template_text_order_preparing'] ?? self::DEFAULT_ORDER_PREPARING_TEXT,
            'template_text_order_ready' => $validated['template_text_order_ready'] ?? self::DEFAULT_ORDER_READY_TEXT,
            'template_text_order_on_the_way' => $validated['template_text_order_on_the_way'] ?? self::DEFAULT_ORDER_ON_THE_WAY_TEXT,
            'template_text_order_delivered' => $validated['template_text_order_delivered'] ?? self::DEFAULT_ORDER_DELIVERED_TEXT,
            'template_text_order_cancelled' => $validated['template_text_order_cancelled'] ?? self::DEFAULT_ORDER_CANCELLED_TEXT,
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = $validated['password'];
        }

        $setting->update($updateData);

        return redirect()->back()->with('success', 'تنظیمات پیامک و تمامی قالب‌های وضعیت سفارش با موفقیت به‌روزرسانی شد.');
    }

    public function testSend(Request $request, \App\Services\Sms\SmsGatewayInterface $smsGateway)
    {
        $request->validate([
            'test_phone' => ['required', 'regex:/^09[0-9]{9}$/'],
            'test_type' => ['required', 'in:otp,notification'],
        ], [
            'test_phone.required' => 'شماره موبایل گیرنده پیامک تست الزامی است.',
            'test_phone.regex' => 'فرمت شماره موبایل باید با ۰۹ شروع شده و ۱۱ رقم باشد.',
        ]);

        $phone = preg_replace('/[^0-9]/', '', $request->input('test_phone'));
        $type = $request->input('test_type');

        try {
            if ($type === 'otp') {
                $code = (string) random_int(100000, 999999);
                $sent = $smsGateway->sendOtp($phone, $code);
                $msg = "پیامک تست کد ورود ({$code}) با موفقیت به شماره {$phone} ارسال گردید.";
            } else {
                $sent = $smsGateway->sendNotification($phone, "پیامک آزمایشی بی کافه شیراز: اتصال به سامانه پیامک با موفقیت برقرار شد.");
                $msg = "پیامک اعلان آزمایشی با موفقیت به شماره {$phone} ارسال گردید.";
            }

            if ($sent) {
                return back()->with('success', $msg);
            }

            return back()->with('error', 'ارسال پیامک با خطا مواجه شد. لطفاً نام‌کاربری و رمز عبور سامانه پیامک را بررسی فرمایید.');
        } catch (\Throwable $e) {
            return back()->with('error', 'خطا در برقراری ارتباط با پنل پیامک: ' . $e->getMessage());
        }
    }
}
