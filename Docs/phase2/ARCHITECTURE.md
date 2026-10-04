# ARCHITECTURE — Cafe Management System

## 1. معماری کلی سیستم

فاز ۱ (تحویل‌شده) یک اپلیکیشن **Laravel monolith** است: هسته کسب‌وکار،
پنل مدیریت داخلی (Blade) و API skeleton، همگی در یک codebase.

**تصمیم معماری:** فاز ۲ (اکنون قطعی و سفارش‌گرفته‌شده) دیگر WordPress/WooCommerce
نیست. فاز ۲ همان codebase و همان دیتابیس فاز ۱ است، گسترش‌یافته به یک
**پلتفرم سفارش آنلاین اختصاصی** (شبیه تجربه اسنپ‌فود، تک‌فروشنده) با همان
Blade/Tailwind/Alpine. provider های نهایی: پیامک = ملی‌پیامک، پرداخت = زرین‌پال
+ کارت‌به‌کارت، ارسال = بله (محدوده شیراز، با نقشه نشان):

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

**Laravel Customer Storefront (Blade، فاز ۲):** مسیر `/`، `/account/*` —
مشتریان. همان اپلیکیشن، guard جدا (`customer`)، بدون CMS یا فریم‌ورک دوم.

**Laravel Driver Panel (Blade، فاز ۲):** مسیر `/driver/*` — رانندگان تحویل.
guard جدای `driver`، ورود با شماره + OTP از طریق همان ملی‌پیامک.

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

**جداول اضافه فاز ۲:**
- `delivery_zones` — با ستون `boundary` (GeoJSON چندضلعی رسم‌شده روی نقشه
  نشان، نه بازه کیلومتری متنی)، `name`, `delivery_price`, `status`
- `customer_addresses` — آدرس‌های ذخیره‌شده مشتری، شامل `lat`/`lng` معتبرشده
  توسط چک point-in-polygon، متصل به یک `delivery_zones.id`
- `payment_settings` — تنظیمات یک‌ردیفی یا کلید/مقدار برای فعال/غیرفعال بودن
  هر روش پرداخت + پیکربندی آن (merchant id زرین‌پال، شماره‌های کارت‌به‌کارت)؛
  مقادیر حساس (merchant id) باید encrypted ذخیره شوند، نه plain text
- `payments` — تراکنش‌ها؛ برای کارت‌به‌کارت شامل مسیر فایل رسید آپلودشده و
  وضعیت تأیید دستی
- `drivers`, `delivery_orders` — طبق طراحی اولیه فاز ۲
- جداول Loyalty (که در فاز ۱ خارج از اسکوپ ساخته شدند) اکنون رسماً بخشی از
  فاز ۲ هستند و باید پس از رفع نقص‌های بخش ۱۸ ادامه یابند، نه بازنویسی از صفر

**تغییر در `orders`:** فیلد `fulfillment_type` (`pickup` | `delivery`) و
`status` گسترش‌یافته طبق state machine بخش ۱۱.

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

**مشتری و راننده (فاز ۲):** شماره موبایل + OTP از طریق **ملی‌پیامک**، پشت
همان اینترفیس `SmsGatewayInterface` فاز ۱. هر کدام guard جداگانه Laravel
(`customer`, `driver`) با session/token واقعی. **تذکر از بازبینی فاز ۱:**
پیاده‌سازی قبلی این بخش یک توکن جعلی (`mock_token_...`) برمی‌گرداند که به
هیچ سشن یا Sanctum token واقعی متصل نبود — یعنی احراز هویت اصلاً کار
نمی‌کرد. در فاز ۲ باید یا session واقعی Laravel یا `$customer->createToken()`
واقعی Sanctum استفاده شود، نه یک رشته تصادفی ساختگی.

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

## 10. Order & Payment Flow (فاز ۲ — دو حالت سفارش، دو روش پرداخت)

```
Customer browses public menu → cart → chooses fulfillment:

  حضوری (pickup):
    order created (status=pending, fulfillment_type=pickup)
      → فوراً روی پنل ادمین → ادمین دستی وضعیت را preparing می‌کند

  ارسال (delivery):
    customer picks address on Neshan map (search or pin drop)
      → backend point-in-polygon check against active delivery_zones
        → in zone: attach zone price, continue
        → out of zone: reject with "خارج از محدوده ارسال"
      → order created (status=pending, fulfillment_type=delivery)

سپس پرداخت (هر دو حالت):

  زرین‌پال:
    → Redirect to Zarinpal
      → Zarinpal callback route (تأیید سمت سرور — الزامی، هرگز فقط query string)
        → order marked paid → SubscriptionService::createSubscription() اگر مربوط به اشتراک بود

  کارت‌به‌کارت:
    → نمایش شماره کارت از payment_settings
      → مشتری رسید/کد پیگیری آپلود می‌کند
        → order status = awaiting_payment_verification
          → ادمین دستی تأیید می‌کند → preparing
```

مبلغ نهایی همیشه سمت سرور از سبد خرید + `delivery_zones.delivery_price`
محاسبه می‌شود — هرگز از ورودی کلاینت پذیرفته نمی‌شود. Rate limiting روی مسیر
callback زرین‌پال هم باید اعمال شود (طبق بند بازبینی امنیتی فاز ۱ — بخش ۱۸).

## 11. Neshan Maps & Delivery Zone Validation (فاز ۲ — شیراز)

- **پنل ادمین:** صفحه‌ای با Neshan Maps JS SDK برای رسم چندضلعی(های) منطقه
  ارسال روی نقشه شیراز؛ هر چندضلعی با قیمت ارسال مستقل ذخیره می‌شود
  (`delivery_zones.boundary` + `delivery_price`).
- **سمت مشتری:** جستجوی آدرس (Neshan geocoding/autocomplete) یا زدن پین روی
  نقشه؛ فقط `lat`/`lng` نهایی به سرور ارسال می‌شود.
- **سمت سرور:** چک point-in-polygon روی تمام `delivery_zones` فعال کافه؛
  نتیجه (منطقه match‌شده یا رد سفارش) هرگز به کلاینت اعتماد نمی‌شود — کلاینت
  نمی‌تواند بگوید «من در منطقه X هستم».
- نیازمند Neshan API key — باید قبل از شروع پیاده‌سازی از مشتری/ادمین گرفته شود.

## 12. Delivery Fulfillment Flow (فاز ۲)

```
Order marked ready (از هر دو حالت پرداخت)
  → assigned to a driver (delivery_orders.driver_id)
    → driver (guard `driver`, OTP از ملی‌پیامک) updates status via Blade panel
      → pending → assigned → picked_up → on_the_way → delivered
```

همه این‌ها روی همان دیتابیس مشترک انجام می‌شود — بدون نیاز به sync خارجی.

## 13. Loyalty Club Architecture (فاز ۲)

- **کد تخفیف تولد:** یک Artisan command زمان‌بندی‌شده (Laravel Scheduler، از
  طریق همان Cron Job واحد بخش ۹) روزانه مشتریانی با تاریخ تولد شمسی امروز را
  پیدا کرده و کد تخفیف موقت صادر می‌کند — نه یک job صف‌دار جدا.
- **تخفیف پلکانی بر اساس خرید:** پس از تکمیل هر سفارش، `LoyaltyService`
  (موجود از فاز ۱، پس از رفع نقص) مجموع خرید مشتری را بررسی و در صورت عبور
  از آستانه، کوپن صادر می‌کند.
- **الزام امنیتی حیاتی:** هر مصرف کوپن باید تأیید کند کوپن متعلق به مشتری
  احراز-هویت‌شده درخواست‌دهنده است — رفع مستقیم آسیب‌پذیری IDOR که در بازبینی
  فاز ۱ پیدا شد (بخش ۱۸).

## 14. API Contract

فرمت پاسخ استاندارد (فاز ۲):
```json
{ "success": true, "data": { }, "message": null }
```
خطاها همیشه با HTTP status code معنادار (401, 403, 422, 409) و پیام فارسی
قابل نمایش به کاربر بازمی‌گردند.

## 15. Data Synchronization Strategy

نیازی به synchronization بین سیستم‌ها نیست — یک دیتابیس، یک اپلیکیشن. صفحه
داشبورد مشتری مستقیماً از همان جداولی می‌خواند که پنل ادمین می‌نویسد؛ هیچ
cache میانی یا تأخیر همگام‌سازی وجود ندارد.

## 16. مدیریت خطاها

- خطاهای Validation → Form Request + پیام فارسی
- خطای race condition روی مصرف روزانه (قید یکتا در دیتابیس) → گرفته شده در
  Service Layer و تبدیل به پیام کاربرپسند «امروز قبلاً استفاده شده»
- خطای callback زرین‌پال (تأیید ناموفق) → رد با پیام مناسب، بدون ساخت
  اشتراک؛ تراکنش باید جداگانه برای پیگیری دستی لاگ شود

## 17. Logging Strategy

Laravel default logging (`storage/logs`) کافی است. لاگ جداگانه‌ای برای مصرف
روزانه (audit trail) در نظر گرفته می‌شود چون این حساس‌ترین عملیات
مالی/کسب‌وکاری سیستم است — هر رکورد `subscription_usages` شامل `created_by`
(کدام پرسنل ثبت کرده) است.

## 18. تذکرهای بازبینی امنیتی فاز ۱ (باید در فاز ۲ رعایت شوند)

- **بدون توکن جعلی:** هرگز یک رشته تصادفی به‌جای session/Sanctum token واقعی
  برگردانده نشود (مشکل واقعی که در پیاده‌سازی قبلی OTP پیدا شد).
- **بدون ساخت فراتر از اسکوپ مصوب:** باشگاه مشتریان (تخفیف تولد، تخفیف
  پلکانی) اکنون رسماً بخشی از فاز ۲ است، اما فقط پس از رفع نقص‌های زیر ادامه
  می‌یابد نه بازنویسی از صفر. فیچرهای دیگر فاز ۳ (چند کافه، اپلیکیشن موبایل)
  کماکان خارج از اسکوپ‌اند و نباید حتی نیمه‌کاره شروع شوند.
- **Rate limiting از روز اول** روی send-otp، verify-otp، login، و callback
  زرین‌پال — نه یک بهبود بعدی.
- **role/guard middleware باید سراسری رجیستر شود** در `bootstrap/app.php` و
  روی گروه route ها اعمال شود، نه فقط با `$this->authorize()` پراکنده در
  بعضی کنترلرها (چون در فاز ۱ همین ناهماهنگی باعث شد صفحه گزارش مالی بدون
  کنترل دسترسی بماند).
- **هیچ endpoint عمومی نباید `customer_id`/`driver_id` خام از body/query
  به‌عنوان هویت بپذیرد** — هویت همیشه از session/token احراز-هویت‌شده گرفته شود.
- **`.env` و `vendor/` هرگز در deliverable نهایی نباشند.**

---
*این فایل ARCHITECTURE.md است.*
