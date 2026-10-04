# CHANGELOG — Karasu Cafe Management System

تمام تغییرات کلیدی پروژه در این فایل ثبت می‌شوند.

## [2.0.1] - 2026-10-04

### Fixed & Hardened (cPanel Production Release)
- **Fix Incomplete Class Cache Serialization**: برطرف‌سازی خطای فیتال `__PHP_Incomplete_Class` مدل `Cafe` و `SubscriptionPlan` در درایور کش فایل و هاست اشتراکی cPanel با بازنویسی کوئری‌های مستقیم و پاکسازی کلیدهای کش قدیمی.
- **Defensive Type Checking**: افزودن اعتبارسنجی‌های دفاعی `instanceof \App\Models\Cafe` و بررسی تایپ آبجکت پلن‌های اشتراک در ویوهای Blade منو جهت جلوگیری قطعی از بروز هرگونه استثنا در رندر.
- **Updated Developer Branding**: به‌روزرسانی متن پاورقی و کپی‌رایت صفحات عمومی به «طراحی و توسعه توسط امیرمحمد بهارلو» به جای برند پیش‌فرض.
- **cPanel Beta Deployment Package**: همگام‌سازی کامل پکیج استقرار `beta/` و فشرده‌سازی خودکار بهینه‌شده جهت نصب آسان در مسیر `public_html/beta`.
- **GitHub Update Architecture**: راه‌اندازی ساختار کامل به‌روزرسانی خودکار با مخزن گیت‌هاب و ورک‌فلوهای Actions جهت انتشار نسخه‌ها.

## [2.1.0] - 2026-09-30

### Added & Fixed (Phase 2 - All-Laravel Ordering Architecture, Delivery Zones & Security Hardening)
- **BlueCup Design System Overhaul (White Background)**: بازطراحی کامل رابط کاربری با پس‌زمینه سفید تمیز (`bg-[#F8FAFC]`) و کارت‌های سفید (`#FFFFFF`) با حاشیه‌های ظریف `border-slate-200/80` منطبق بر تصویر مرجع BlueCup:
  - **سایدبار زغالی تیره (`#19242B`)**: لوگوی فنجان قهوه نئونی درخشان با بخار، متن BlueCup Café Management، پیل فعال تیل (`#387780`)، متن روشن و کارت پروفایل کاربر در پایین.
  - **تاپ‌بار مدرن و شفاف**: فیلد جستجوی گرد با میانبر `Ctrl + K`، زنگوله اعلان با بج قرمز، ساعت زنده جلالی، دکمه «ثبت سفارش» و آواتار کاربر.
  - **داشبورد مدیریتی (Screen 1)**: ۴ کارت KPI آماری (فروش امروز، تعداد سفارشات، مشتریان امروز، میانگین سفارش) همراه با بج درصد رشد سبز (`+۱۸٪ ↑`) و خطوط نمودار ظریف اسپارک‌لاین SVG، نمودار ستونی فروش هفتگی شنبه تا جمعه با فیلتر بازه زمانی و جدول سفارش‌های اخیر با بج‌های وضعیتی رنگی ملایم.
  - **صندوق و ثبت سفارش POS (Screen 2)**: چیدمان دو ستونه با فیلتر دسته‌بندی پیل، کارت‌های تصویری محصولات با دکمه دایره‌ای `+` تیل و پنل فاکتور جاری با استپر تعداد، محاسبه سرجمع و دکمه تمام‌عرض ثبت سفارش.
  - **مانیتور زنده آشپزخانه (Screen 3)**: بازطراحی تابلوی کانبان زنده با ستون‌های رنگی (سفارشات جدید زرد، در حال آماده‌سازی آبی، آماده تحویل سبز) و هشدار صوتی.
- **SafeEncryptedArray Cast**: پیاده‌سازی Cast سفارشی `SafeEncryptedArray` جهت رمزنگاری فیلدهای حساس با پشتیبانی ۱۰۰٪ سازگار با ستون‌های نوع `json` در MySQL و رمزگشایی ایمن داده‌های قدیمی بدون ایجاد `DecryptException`.
- **Customer Order Show Route**: ثبت روت نام‌گذاری‌شده `customer.orders.show` در `routes/web.php` جهت مشاهده و چاپ فاکتور در داشبورد مشتریان.
- **Unified Design System Across 23 Views**: تبدیل سراسری توکن‌های رنگی قدیمی در تمامی ۲۳ صفحه Blade به پالت مصوب Phase 2 (گرافیت `#353535`، تیل `#3C6E71`، ییل بلو `#284B63`، آلاباستر `#D9D9D9`).
- **Neshan Maps & Server-Side Point-in-Polygon**: پیاده‌سازی سرویس `PointInPolygonService` با الگوریتم Ray-Casting جهت اعتبارسنجی مختصات جغرافیایی شیراز بر اساس چندضلعی‌های GeoJSON در جدول `delivery_zones`.
- **Card-to-Card & Order Lifecycle**: پیاده‌سازی وضعیت `awaiting_payment_verification` در تسویه‌حساب و تایید دستی توسط ادمین در بورد سفارشات زنده.
- **Dedicated Driver System**: پیکربندی گارد `driver`، سیستم احراز هویت OTP رانندگان با ملی‌پیامک و پنل واکنش‌گرای مدیریت سفارشات پیک در مسیر `/driver`.
- **Zero-IDOR Security**: حذف وابستگی‌های ارسال شناسه کاربر در کوئری API باشگاه مشتریان و الصاق کوپن‌های تخفیف به مالک حقیقی (`CustomerCoupon`).
- **Jalali Calendar Synchronization**: محاسبه تولد و ارسال پاداش‌ها بر اساس تقویم هجری شمسی با پکیج `morilog/jalali`.
- **Online Coffee Subscriptions**: ایجاد مسیر مستقیم خرید آنلاین اشتراک‌های ماهانه قهوه بدون نیاز به ووکامرس.
- **Staff Order Details View**: ساخت صفحه اختصاصی مدیریت جزئیات سفارش برای پرسنل در `resources/views/orders/show.blade.php`.

## [3.0.0] - 2026-08-01

### Added (Phase 3 - Loyalty, Gamification & Lucky Wheel)
- **Loyalty Points System**: مایگریشن `loyalty_points` و سرویس `LoyaltyService` جهت مدیریت کسب و خرج امتیازات وفاداری.
- **Lucky Wheel System**: مایگریشن‌های `lucky_wheel_prizes` و `lucky_wheel_spins` به همراه قید یکتای دیتابیس `unique(['customer_id', 'spin_date'])` جهت محدودیت ۱ بار چرخش گردونه در روز.
- **Coupons & Rewards**: مایگریشن‌های `coupons` و `customer_coupons` جهت تبدیل امتیازها به کوپن‌های تخفیف.
- **Loyalty REST API**: کنترلر `LoyaltyApiController` شامل روت‌های `/api/loyalty/wheel/spin`, `/api/loyalty/wheel/prizes`, `/api/loyalty/points`, و `/api/loyalty/redeem-coupon`.
- **Loyalty Admin Panel**: پنل مدیریت باشگاه مشتریان در روت `/admin/loyalty` با طراحی لوکس و آیکون‌های SVG.
- **Automated Testing Suite**: کلاس تست `LoyaltyServiceTest` با پوشش ۱۰۰٪ تمام سناریوهای فاز ۳.

---

## [2.0.0] - 2026-08-01

### Added (Phase 2 - Online Platform & WooCommerce Integration)
- **SMS Gateway Abstraction**: اینترفیس واحد `SmsGatewayInterface` با پشتیبانی از درایورهای `log`, `kavenegar`, و `farazsms`.
- **Customer OTP Authentication**: مایگریشن `customer_otps` و سرویس `CustomerAuthService` به همراه روت‌های عمومی REST API (`/api/auth/send-otp` و `/api/auth/verify-otp`).
- **WooCommerce Webhook Integration**: ایجاد `WooCommerceWebhookController` جهت اعتبارسنجی امضای HMAC SHA256 و فعال‌سازی خودکار اشتراک خریداران آنلاین در لاراول.
- **Database Schema Extensions**: مایگریشن‌های `payments`, `delivery_zones`, `drivers`, `delivery_orders`.
- **WordPress Plugin**: ساخت پلاگین اختصاصی `karasu-cafe-connector` در مسیر `integrations/wordpress-plugin/karasu-cafe-connector`.

---

## [1.0.0] - 2026-07-30

### Added (Phase 1 - Core Internal System)
- **Laravel 12 Framework Core**: راه‌اندازی و پیکربندی پروژه لاراول ۱۲.
- **Database Architecture**: مایگریشن‌های ۱۰ گانه شامل `cafes`, `users`, `customers`, `subscription_plans`, `subscriptions`, `products`, `subscription_usages`, `orders`.
- **Service Layer**: سرویس‌های `SubscriptionService`, `CustomerService`, `OrderService`.
- **Internal Blade Admin Panel (Persian RTL)**: پنل مدیریت، صفحه صندوق‌دار جهت ثبت ۱-کلیکی مصرف، مدیریت مشتریان، پلن‌ها، محصولات و گزارش‌ها.
