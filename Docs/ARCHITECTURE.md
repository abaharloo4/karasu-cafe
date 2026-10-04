# ARCHITECTURE — Cafe Management System

## 1. معماری کلی سیستم

فاز ۱ (تنها فاز قطعی فعلی) یک اپلیکیشن **Laravel monolith** است: هسته کسب‌وکار،
پنل مدیریت داخلی (Blade) و API skeleton، همگی در یک codebase.

**تصمیم معماری تغییر کرده:** فاز ۲ (احتمالی، غیرقطعی) دیگر WordPress/WooCommerce
نیست. فاز ۲ همان codebase و همان دیتابیس فاز ۱ است، صرفاً با یک بخش
مشتری-محور (storefront + حساب کاربری) که با همان Blade/Tailwind/Alpine ساخته
می‌شود:

```
Customer → Laravel Blade Storefront (همان اپلیکیشن فاز ۱) → Service Layer → MySQL
```

یک اپلیکیشن، یک دیتابیس، یک منبع حقیقت — بدون sync، بدون webhook بین دو
سیستم، بدون ریسک ناهماهنگی بین Laravel و یک CMS جدا.

**قید استقرار مهم:** فاز ۲ باید روی **هاست اشتراکی cPanel** اجرا شود، نه VPS
یا Forge یا Docker. این قید مستقیماً روی تصمیمات queue/cache/cron در ادامه
همین فایل اثر می‌گذارد (بخش ۹).

## 2. مسئولیت هر بخش

**Laravel Core:** مشتریان، پلن‌ها و اشتراک‌ها، منطق مصرف روزانه، سفارش‌ها،
گزارش‌گیری، احراز هویت پرسنل و مشتری، درگاه پرداخت، ارسال/پیک (مشروط). همه‌چیز
— بدون تفکیک به یک سیستم بیرونی.

**Laravel Admin Panel (Blade):** مسیر `/admin/*` (یا مشابه) — پرسنل/مالک.

**Laravel Customer Storefront (Blade، فاز ۲ احتمالی):** مسیر `/`، `/account/*`
— مشتریان. همان اپلیکیشن، guard جدا (`customer`)، بدون CMS یا فریم‌ورک دوم.

## 3. ساختار Backend Laravel

```
app/
  Models/                    Customer, Subscription, SubscriptionPlan,
                             SubscriptionUsage, Product, Order, OrderItem, User
  Http/
    Controllers/Web/         مصرف‌کننده مستقیم Service Layer برای پنل Blade
    Controllers/Api/         نازک — فقط validate + delegate به Service + Resource
    Requests/                یک Form Request به‌ازای هر عملیات نوشتنی
    Resources/               فقط برای مسیرهای API (Web controllers نیازی ندارند)
  Services/
    SubscriptionService.php  تنها محل منطق مصرف روزانه/اعتبار — هم Web و هم API
                             از همین سرویس استفاده می‌کنند، نه پیاده‌سازی موازی
    CustomerService.php
    OrderService.php
  Policies/                  یک Policy به‌ازای هر مدل حساس (Subscription, Order, User)
```

**تصمیم معماری مهم:** Repository pattern استفاده نمی‌شود. Eloquent مستقیماً در
Service Layer فراخوانی می‌شود؛ این پروژه به اندازه‌ای نیست که لایه انتزاعی
اضافه ارزش بیافریند.

## 4. ساختار API

Endpoint های پایه در فاز ۱ ساخته می‌شوند اما **بدون کلاینت واقعی** —
صرفاً skeleton آماده برای آینده (مثلاً اپلیکیشن موبایل احتمالی در فاز ۳؛ چون
فاز ۲ دیگر یک سیستم بیرونی نیست، خودِ Storefront مستقیماً از Service Layer
استفاده می‌کند، نه از این API):

| Method | Route | مصرف‌کننده فعلی |
|---|---|---|
| GET/POST | `/api/customers`, `/api/customers/{id}` | هیچ (آماده برای آینده) |
| GET/POST | `/api/subscriptions`, `/api/subscriptions/check` | هیچ |
| POST | `/api/subscription/use` | هیچ |
| POST | `/api/orders` | هیچ |

احراز هویت API: Laravel Sanctum (token-based) — از روز اول همین‌طور ساخته
می‌شود تا در صورت فعال شدن فاز ۲، تغییری در auth لازم نباشد.

## 5. Database Architecture

هر جدول کسب‌وکاری شامل `cafe_id` است — **فقط برای آماده‌سازی آینده**، بدون
UI یا منطق چند‌کافه‌ای فعال در حال حاضر (کافه فعلی تک‌شعبه است).

جدول‌های اصلی: `cafes`, `users` (پرسنل), `customers`, `subscription_plans`,
`subscription_plan_products` (pivot — محصولات مجاز هر پلن), `subscriptions`,
`products`, `subscription_usages`, `orders`, `order_items`.

**قید حیاتی:** `subscription_usages` دارای unique composite index روی
`(subscription_id, used_date)` است — این قید در سطح دیتابیس اجرا می‌شود، نه
فقط در کد، تا حتی در آینده (چند ترمینال هم‌زمان یا فاز آنلاین) مصرف تکراری
غیرممکن باشد.

## 6. طراحی Multi-Tenant برای آینده

فعلاً **غیرفعال**. وجود `cafe_id` امکان گذار به SaaS را فراهم می‌کند، اما:
- هیچ query‌ای فعلاً روی چند `cafe_id` فیلتر نمی‌شود (چون فقط یک کافه وجود دارد)
- در صورت نیاز واقعی به چند کافه، باید یک global scope روی `cafe_id` اضافه شود
  و middleware تشخیص کافه فعلی نوشته شود — این یک تصمیم آگاهانه برای آینده است،
  نه بخشی از فاز ۱

## 7. Authentication Flow

**پرسنل (فاز ۱):** شماره موبایل یا ایمیل + رمز عبور → Laravel session (پنل
Blade) یا Sanctum token (در صورت API). **بدون OTP.**

**مشتری (فاز ۲ — غیرقطعی):** شماره موبایل + OTP از طریق یک SMS gateway
ایرانی (provider هنوز انتخاب نشده — پیشنهاد: پشت یک اینترفیس واحد
`SmsGateway` قرار بگیرد تا تعویض provider هزینه‌ای نداشته باشد). یک guard
جداگانه Laravel (`customer`) — نه یک سیستم هویتی بیرونی.

## 8. Authorization Flow

Policy به‌ازای هر مدل حساس + role middleware (`admin`, `staff`). مثال:
`SubscriptionPolicy::create()` فقط برای `admin` مجاز است؛ `SubscriptionPolicy::use()`
برای هر دو نقش.

## 9. cPanel Deployment Architecture (فاز ۲ — قید فنی مهم)

هاست هدف: **cPanel اشتراکی**، نه VPS. این یعنی:

- **بدون queue worker دائمی:** صف‌ها با `queue driver = database` پیاده‌سازی
  می‌شوند و پردازش آن‌ها با یک **cPanel Cron Job** انجام می‌شود، نه یک process
  همیشه در حال اجرا (`supervisor` در دسترس نیست).
- **بدون Redis/Memcached:** cache و session با درایور `file` یا `database`.
- **Cron واحد:** یک Cron Job در cPanel که هر دقیقه
  `php artisan schedule:run` را اجرا می‌کند؛ هر job دوره‌ای (پاکسازی OTP،
  بررسی انقضای اشتراک) از طریق Laravel Scheduler تعریف می‌شود، نه یک daemon جدا.
- **Document root:** یا مستقیماً روی پوشه `public/` تنظیم می‌شود (ترجیح اول)،
  یا محتوای `public/` به `public_html/` منتقل و مسیرهای `index.php` اصلاح
  می‌شود (fallback، در صورتی که هاست اجازه تغییر document root ندهد).
- **Symlink:** باید پیش از توسعه تأیید شود که هاست از `php artisan storage:link`
  پشتیبانی می‌کند؛ در غیر این صورت فایل‌ها از طریق یک route/controller سرو
  می‌شوند نه symlink مستقیم.

## 10. Payment Gateway Flow (فاز ۲ — احتمالی، بدون CMS واسط)

```
Customer selects plan on Blade storefront
  → Redirect to Iranian payment gateway (Zarinpal/IDPay — انتخاب نشده)
    → Gateway callback route (server-to-server verification, نه اعتماد به query string)
      → SubscriptionService::createSubscription() [همان سرویس فاز ۱، نه پیاده‌سازی جدید]
```

چون دیگر CMS واسطی وجود ندارد، این یک callback مستقیم داخل همان اپلیکیشن
است، نه webhook بین دو سیستم — نیازی به shared secret بین سیستم‌ها نیست، اما
تأیید تراکنش با API خود درگاه پرداخت همچنان الزامی است.

## 11. API Contract

فرمت پاسخ استاندارد (فاز ۲):
```json
{ "success": true, "data": { }, "message": null }
```
خطاها همیشه با HTTP status code معنادار (401, 403, 422, 409) و پیام فارسی
قابل نمایش به کاربر بازمی‌گردند.

## 12. Data Synchronization Strategy

نیازی به synchronization بین سیستم‌ها نیست — یک دیتابیس، یک اپلیکیشن. صفحه
داشبورد مشتری مستقیماً از همان جداولی می‌خواند که پنل ادمین می‌نویسد؛ هیچ
cache میانی یا تأخیر همگام‌سازی وجود ندارد.

## 13. مدیریت خطاها

- خطاهای Validation → Form Request + پیام فارسی
- خطای race condition روی مصرف روزانه (قید یکتا در دیتابیس) → گرفته شده در
  Service Layer و تبدیل به پیام کاربرپسند «امروز قبلاً استفاده شده»
- خطای callback درگاه پرداخت (تأیید ناموفق) → رد با پیام مناسب، بدون ساخت
  اشتراک؛ تراکنش باید جداگانه برای پیگیری دستی لاگ شود

## 14. Logging Strategy

Laravel default logging (`storage/logs`) برای فاز ۱ کافی است. لاگ جداگانه‌ای
برای مصرف روزانه (audit trail) در نظر گرفته می‌شود چون این حساس‌ترین عملیات
مالی/کسب‌وکاری سیستم است — هر رکورد `subscription_usages` شامل `created_by`
(کدام پرسنل ثبت کرده) است.
