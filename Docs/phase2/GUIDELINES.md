# GUIDELINES — Cafe Management System

## Laravel Standards

### PSR & Laravel Coding Standards
- PSR-12 برای فرمت کد؛ از `laravel/pint` برای فرمت خودکار استفاده شود.
- از Laravel best-practices پیش‌فرض پیروی شود (Eloquent به‌جای Query Builder خام
  مگر برای query های گزارش‌گیری پیچیده).

### Naming Convention
- Model: singular, PascalCase → `Subscription`, `SubscriptionUsage`
- Controller: `{Model}Controller` → `SubscriptionController` (در `Controllers/Web`
  یا `Controllers/Api` بسته به مسیر)
- Service: `{Domain}Service` → `SubscriptionService`, `CustomerService`, `OrderService`
- Form Request: `{Action}{Model}Request` → `CreateSubscriptionRequest`
- API Resource: `{Model}Resource` → `SubscriptionResource`
- Migration: استاندارد Laravel (`create_subscriptions_table`)
- Policy: `{Model}Policy`

پروژه پرفیکس اختصاصی کلاس ندارد (برخلاف پرامپت اولیه) چون این یک codebase
تک‌پروژه‌ای Laravel است، نه پکیج قابل توزیع؛ namespace استاندارد `App\` کافی است.

### Service Layer Rules
- **تنها محل منطق کسب‌وکاری.** Controller ها هرگز نباید مستقیم روی مدل‌ها
  منطق تصمیم‌گیری بنویسند (مثلاً چک "آیا امروز مصرف شده؟" باید در
  `SubscriptionService::hasUsedToday()` باشد، نه در Controller).
- هر متد Service که به بیش از یک جدول می‌نویسد باید در `DB::transaction()`
  بسته شود.
- Service ها مستقل از HTTP هستند (بدون `Request` به‌عنوان پارامتر) تا هم از
  Web Controller و هم از API Controller قابل فراخوانی باشند.

### Controller Rules
- Controller های Web مستقیماً Service را صدا می‌زنند و View برمی‌گردانند.
- Controller های API فقط validate (از طریق Form Request) → Service →
  Resource. هیچ منطقی داخل خود Controller نوشته نمی‌شود.
- حداکثر یک Controller به‌ازای یک Resource (RESTful conventions).

### Model Rules
- Relationship ها صریح تعریف شوند (`belongsTo`, `hasMany`) با return type hint.
- از `$fillable` استفاده شود، نه `$guarded = []`.
- Scope هایی مثل `active()` روی Subscription برای خوانایی query ها تعریف شوند.
- Repository pattern استفاده نشود — این تصمیم آگاهانه پروژه است (نگاه کنید
  به ARCHITECTURE.md بخش ۳).

### Migration Rules
- هر جدول کسب‌وکاری شامل `cafe_id` (foreign key، nullable نه — همیشه پر است).
- Index های یکتا صراحتاً نوشته شوند، به‌خصوص
  `unique(['subscription_id', 'used_date'])` روی `subscription_usages`.
- از `foreignId()->constrained()` برای خوانایی relationship استفاده شود.

### API Resource Rules
- فقط فیلدهایی که کلاینت واقعاً نیاز دارد expose شود (مثلاً `password` هرگز).
- ساختار پاسخ مطابق API Contract در ARCHITECTURE.md بخش ۱۱.

---

## Laravel Storefront & Delivery Standards (فاز ۲ — قطعی، بدون WordPress)

پروژه WordPress/WooCommerce ندارد. فاز ۲ همان اپلیکیشن Laravel است با بخش
مشتری-محور و پنل راننده اضافه. قوانین Laravel Standards بالا (Service Layer,
Controller, Model, Migration) عیناً برای این بخش‌ها هم اعمال می‌شوند.

نکات اختصاصی این بخش:
- Controller های مشتری در `Controllers/Customer/`، Controller های راننده در
  `Controllers/Driver/` قرار می‌گیرند — جدا از `Controllers/Web` (پرسنل).
- Guard های جدا (`customer`, `driver`) در `config/auth.php` تعریف می‌شوند؛
  هرگز از guard پرسنل برای این‌ها استفاده نشود.
- View های مشتری در `resources/views/customer/`، راننده در
  `resources/views/driver/`، مجزا از `resources/views/admin/`؛ همه از همان
  کامپوننت‌های Blade/Tailwind مشترک استفاده می‌کنند.

### قوانین امنیتی الزامی (از بازبینی کد فاز ۱ استخراج شده)

بازبینی امنیتی کد تحویلی فاز ۱ چند نقض واقعی این قوانین رو پیدا کرد؛ در فاز
۲ این‌ها باید از ابتدا رعایت شوند، نه به‌عنوان بازبینی نهایی:

- **هیچ endpointـی که هویت رو از پارامتر خام بگیرد مجاز نیست.** مثلاً یک
  route عمومی که `customer_id` رو از body می‌گیرد و بدون احراز هویت به آن
  اعتماد می‌کند، یک آسیب‌پذیری IDOR است — هویت همیشه باید از
  `auth('customer')->user()` یا معادلش گرفته شود، نه از ورودی کاربر.
- **`throttle` middleware روی هر endpoint عمومی حساس** (OTP، لاگین، callback
  پرداخت) از همون commit اول اضافه شود.
- **role/guard middleware باید در `bootstrap/app.php` رجیستر و روی گروه
  route ها اعمال شود** — تکیه صرف به `$this->authorize()` پراکنده در بعضی
  controller ها کافی نیست؛ اگر یک Controller این خط رو فراموش کند (مثل اتفاقی
  که برای گزارش مالی در فاز ۱ افتاد)، هیچ لایه دومی برای جلوگیری وجود نخواهد
  داشت.
- **توکن‌های احراز هویت باید واقعی باشند** — هرگز یک رشته ساختگی به‌جای
  `Sanctum::createToken()` یا session واقعی Laravel برگردانده نشود.
- **کد فراتر از اسکوپ مصوب ننویسید** — اگر فیچری در ROADMAP.md برای این فاز
  علامت نخورده، پیاده‌سازی (حتی نیمه‌کاره) آن قابل قبول نیست.

### قوانین پرداخت و نقشه (فاز ۲)

- **مبلغ همیشه سمت سرور محاسبه می‌شود** — از سبد خرید + قیمت منطقه ارسال؛
  هرگز از فیلد مخفی یا پارامتر کلاینت خوانده نشود.
- **merchant id زرین‌پال و شماره‌های کارت‌به‌کارت** در `payment_settings`
  و به‌صورت encrypted ذخیره شوند (از `casts` نوع `encrypted` لاراول استفاده
  شود)، نه plain text در دیتابیس یا هاردکد در کد.
- **تأیید کارت‌به‌کارت همیشه دستی است** — هیچ منطق خودکاری برای تشخیص صحت
  رسید آپلودشده پیاده‌سازی نشود؛ فقط تغییر وضعیت توسط ادمین مجاز است.
- **چک منطقه ارسال (point-in-polygon) همیشه سمت سرور اجرا می‌شود** —
  شناسه منطقه‌ای که کلاینت ادعا می‌کند هرگز مستقیم پذیرفته نمی‌شود.
- کلید API نشان باید در `.env` باشد (`NESHAN_API_KEY`)، نه در کد frontend
  به‌صورت hardcode.

## cPanel Deployment Standards (فاز ۲ — قید فنی الزامی، هاست از قبل تأیید شده)

- **Queue:** فقط درایور `database` مجاز است؛ فرض هیچ `queue:work` دائمی نشود.
- **Cache/Session:** فقط `file` یا `database`؛ Redis/Memcached فرض نشود.
- **Cron:** تمام کارهای زمان‌بندی‌شده از طریق Laravel Scheduler و یک Cron Job
  واحد (`schedule:run` هر دقیقه) اجرا می‌شوند — نه daemon جدا؛ این Cron همچنین
  job روزانه تخفیف تولد باشگاه مشتریان را هم اجرا می‌کند.
- **مشخصات هاست تأیید شده‌اند** (نسخه PHP، document root، دسترسی Terminal) —
  این جزئیات باید در راهنمای نصب مستند شوند، نه دوباره فرض شوند.
- **.env:** هرگز در مسیر public قابل دسترسی از وب نباشد؛ تست مستقیم با یک
  درخواست HTTP باید 403/404 برگرداند.

---
*این فایل GUIDELINES.md — فاز ۱ و فاز ۲ را پوشش می‌دهد.*
