# Agents.md — قوانین توسعه برای AI Agent

Repository: https://github.com/abaharloo4/karasu-cafe.git

## قبل از شروع هر توسعه

1. پوشه `/docs` بررسی و تمام فایل‌های زیر خوانده شوند (به همین ترتیب اولویت):
   `PRD.md` → `ARCHITECTURE.md` → `GUIDELINES.md` → `ROADMAP.md`
2. مشخص شود کدام فاز از `ROADMAP.md` در حال توسعه است؛ Phase 2 و Phase 3
   **مشروط و غیرقطعی** هستند — پیش از شروع کدنویسی روی آن‌ها، تأیید صریح
   کارفرما لازم است، حتی اگر task مربوطه در backlog باشد.
3. هیچ تصمیمی که در `PRD.md` به‌عنوان "Out of Scope" یا "تصمیم باز" علامت
   خورده، بدون تأیید صریح پیاده‌سازی نشود (مثلاً OTP پرسنل، سیستم ارسال).

## ثبت Task ها

- تمام Task ها در `/docs/tasks.md` ثبت می‌شوند، با وضعیت (`todo` / `in-progress`
  / `done`) و ارجاع به بخش مربوطه در `ROADMAP.md`.
- برنامه پیاده‌سازی هر فاز در `/docs/implementation-plan.md` ثبت می‌شود، شامل
  ترتیب دقیق کارها و کدام Service/Model/Migration درگیر است.

## قبل از هر تغییر کد

- `GUIDELINES.md` بررسی شود — به‌خصوص قانون «بدون Repository pattern» و
  «منطق کسب‌وکاری فقط در Service Layer».
- اگر تغییری با تصمیم معماری مستندشده در `ARCHITECTURE.md` تناقض دارد، اول
  خود آن فایل باید آپدیت شود، نه اینکه کد بی‌سروصدا از مستندات منحرف شود.

## بعد از هر تغییر

پس از هر تغییر کد که merge می‌شود:

1. شماره نسخه پروژه (در `composer.json` یا فایل version مشخص) افزایش یابد
   (Semantic Versioning: patch برای باگ‌فیکس، minor برای فیچر جدید، major
   برای breaking change)
2. `CHANGELOG.md` آپدیت شود (چه چیزی اضافه/تغییر/حذف شد)
3. اگر مشکلی حین توسعه پیش آمد و حل شد، رکورد آن در `DEBUG_LOG.md` طبق فرمت
   استاندارد ثبت شود
4. Commit با پیام واضح و مرتبط با task انجام شود (Conventional Commits:
   `feat:`, `fix:`, `docs:`, `refactor:`)
5. Push به repository بالا انجام شود

## Release

هر Release باید شامل:
- شماره نسخه (مطابق semver)
- Release Notes (خلاصه‌ای از `CHANGELOG.md` مربوط به آن نسخه)
- فایل ZIP خروجی، ساخته‌شده در root پروژه (بدون `vendor/`, `node_modules/`, `.env`)

## قانون طلایی

هیچ تغییر کدی بدون به‌روزرسانی مستندات مرتبط (`PRD.md`, `ARCHITECTURE.md`,
`ROADMAP.md`, `CHANGELOG.md`) انجام نمی‌شود — مستندات باید همیشه بازتاب‌دهنده
وضعیت واقعی کد باشند، نه وضعیتی که در ابتدای پروژه تصور می‌شد.
