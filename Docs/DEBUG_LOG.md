# DEBUG_LOG — Cafe Management System

این فایل ثبت مشکلات فنی حین توسعه است. هر مشکل باید بلافاصله بعد از حل شدن،
با فرمت زیر ثبت شود — نه صرفاً برای مشکلات بزرگ، بلکه هر باگی که راه‌حلش
ممکن است دوباره لازم شود.

## فرمت هر رکورد

```
### [YYYY-MM-DD] Component — خلاصه یک‌خطی مشکل

- **Version:** شماره نسخه پروژه در زمان بروز مشکل
- **Component:** کدام بخش (مثلاً SubscriptionService, Migration, WooCommerce Webhook)
- **Problem:** توضیح مشکل از دید کاربر/سیستم
- **Error Message:** پیام خطای دقیق (در صورت وجود)
- **Root Cause:** علت واقعی، نه فقط علامت ظاهری
- **Solution:** چه تغییری مشکل را حل کرد
- **Prevention:** چطور از تکرارش جلوگیری کنیم (تست، قید دیتابیس، و غیره)
```

---

## نمونه (مثال آموزشی — واقعی نیست)

### [2026-08-05] SubscriptionService — مصرف تکراری روزانه ثبت شد

- **Version:** 0.3.0
- **Component:** `SubscriptionService::consumeDailyItem()`
- **Problem:** در یک مورد، یک مشتری دو بار در یک روز قهوه رایگان دریافت کرد.
- **Error Message:** خطایی رخ نداد — مشکل سکوت‌آمیز بود (double insert موفق).
- **Root Cause:** چک `hasUsedToday()` در کد انجام می‌شد اما قید یکتا در سطح
  دیتابیس روی `subscription_usages` تعریف نشده بود؛ دو درخواست هم‌زمان از
  race condition عبور کردند.
- **Solution:** افزودن migration برای `unique(['subscription_id', 'used_date'])`
  و پوشاندن عملیات در `DB::transaction()`.
- **Prevention:** از این پس هر قانون کسب‌وکاری "یکتا بودن" باید هم در کد و
  هم به‌صورت اجباری در سطح دیتابیس پیاده شود — صرف چک در کد کافی نیست.

---
*این فایل DEBUG_LOG.md است. منتظر تأیید شما برای ساخت Agents.md می‌مانم.*
