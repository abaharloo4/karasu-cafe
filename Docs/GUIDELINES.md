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

## Laravel Storefront Standards (فاز ۲ — هنوز غیرقطعی، بدون WordPress)

**تصمیم تغییر کرده:** پروژه دیگر WordPress/WooCommerce ندارد. فاز ۲ همان
اپلیکیشن Laravel است با یک بخش مشتری-محور اضافه. قوانین Laravel Standards
بالا (Service Layer, Controller, Model, Migration) عیناً برای این بخش هم
اعمال می‌شوند — قانون جدا و متفاوتی لازم نیست.

نکات اختصاصی این بخش:
- Controller های مشتری در `Controllers/Customer/` قرار می‌گیرند، جدا از
  `Controllers/Web` (پرسنل) — تا مرز دسترسی هر guard واضح بماند.
- Guard جدا (`customer`) در `config/auth.php` تعریف می‌شود؛ هرگز از همان
  guard پرسنل استفاده نشود، حتی اگر جدول متفاوتی هم داشته باشند.
- View های مشتری در `resources/views/customer/` قرار می‌گیرند، مجزا از
  `resources/views/admin/`، اما هر دو از همان کامپوننت‌های Blade/Tailwind
  مشترک (مثل layout پایه، دکمه‌ها) استفاده می‌کنند.

## cPanel Deployment Standards (فاز ۲ — قید فنی الزامی)

- **Queue:** فقط درایور `database` مجاز است؛ فرض هیچ `queue:work` دائمی نشود.
- **Cache/Session:** فقط `file` یا `database`؛ Redis/Memcached فرض نشود.
- **Cron:** تمام کارهای زمان‌بندی‌شده از طریق Laravel Scheduler و یک Cron Job
  واحد (`schedule:run` هر دقیقه) اجرا می‌شوند — نه daemon جدا.
- **Environment check قبل از کد نویسی:** نسخه PHP (باید 8.3+)، امکان تغییر
  document root به `public/`، پشتیبانی از symlink، و دسترسی به Composer/Artisan
  (Terminal یا SSH) باید پیش از شروع پیاده‌سازی فاز ۲ تأیید شود — این‌ها
  می‌توانند طراحی deployment را عوض کنند.
- **.env:** هرگز در مسیر public قابل دسترسی از وب نباشد؛ تست مستقیم با یک
  درخواست HTTP باید 403/404 برگرداند.
