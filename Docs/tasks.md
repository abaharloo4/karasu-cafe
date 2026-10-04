# Tasks — Cafe Management System

مرجع: [ROADMAP.md](file:///c:/Amir/work/projects/karasu-cafe/Docs/ROADMAP.md)

## Phase 1 — Internal Cafe Management System (کامل شده)

| کد | عنوان Task | لایه / کامپوننت | وضعیت |
|---|---|---|---|
| TASK-101 | راه‌اندازی پروژه لاراول ۱۲ و کانفیگ اولیه `.env` | Setup | `done` |
| TASK-102 | ایجاد مایگریشن‌های ۱۰ گانه و قید یکتای `subscription_usages` | Database | `done` |
| TASK-103 | ایجاد Eloquent Models و روابط بین آن‌ها | Models | `done` |
| TASK-104 | ایجاد Seeders (کافه، ادمین، کارمند، محصولات، پلن نمونه) | Seeders | `done` |
| TASK-105 | پیاده‌سازی احراز هویت پرسنل (Session-based) و Middleware نقش‌ها | Auth | `done` |
| TASK-106 | پیاده‌سازی `SubscriptionService` (شامل مصرف روزانه در تراکنش) | Service | `done` |
| TASK-107 | پیاده‌سازی `CustomerService` و `OrderService` | Service | `done` |
| TASK-108 | ایجاد Policies برای مدل‌ها | Security | `done` |
| TASK-109 | پیاده‌سازی کنترلرهای Web و صفحات Blade (RTL/Tailwind) | UI | `done` |
| TASK-110 | پیاده‌سازی اسکلت API، Form Requestها و API Resourceها | API | `done` |
| TASK-111 | اجرای تست‌های اتوماتیک و تست دستی جریان کاربری | Testing | `done` |
| TASK-112 | ساخت فایل `.env.example` و مستندات نصب و راهنمای اجرای فاز ۱ | Docs | `done` |

---

---

## Phase 2 — Online Ordering Platform for B Cafe (All-Laravel, Shiraz)

مرجع: [Docs/phase2/ROADMAP.md](file:///c:/Amir/work/projects/karasu-cafe/Docs/phase2/ROADMAP.md)

| کد | عنوان Task | لایه / کامپوننت | وضعیت |
|---|---|---|---|
| TASK-001 | اصلاح دیتابیس: ستون تصویر محصولات و گسترش وضعیت‌های سفارش | Database | `done` |
| TASK-002 | بستن دسترسی کوپن‌ها با role:admin، تراتل کال‌بک و امن‌سازی API | Security / Auth | `done` |
| TASK-101 | افزودن ستون GeoJSON boundary به مناطق ارسال و الگوریتم Point-in-Polygon | Database / Geo | `done` |
| TASK-102 | بازنویسی پنل مدیریت مناطق ارسال با SDK نقشه نشان (شیراز) | UI / Admin | `done` |
| TASK-103 | اعتبارسنجی سروری محدوده ارسال در تسویه‌حساب (رد خودکار خارج از محدوده) | Checkout / Service | `done` |
| TASK-201 | چرخه وضعیت کارت‌به‌کارت (awaiting_payment_verification) و بورد زنده سفارشات | Order / LiveBoard | `done` |
| TASK-202 | ذخیره‌سازی رمزنگاری‌شده (Encrypted) مقادیر درگاه‌ها و شماره کارت‌ها | Security / Settings | `done` |
| TASK-203 | ایجاد ویوی اختصاصی جزئیات سفارش برای پرسنل کافه (`orders.show`) | UI / Staff | `done` |
| TASK-301 | پیکربندی گارد driver و ورود OTP رانندگان با ملی‌پیامک | Auth / Driver | `done` |
| TASK-302 | پیاده‌سازی پنل واکنش‌گرای رانندگان پیک (`/driver`) و چرخه تحویل | Panel / Driver | `done` |
| TASK-303 | ماژول تخصیص سفارشات آماده به راننده در پنل مدیریت | Orders / Admin | `done` |
| TASK-401 | ثبت و ذخیره‌سازی تاریخ تولد بر اساس تقویم شمسی جلالی | Profile / Customer | `done` |
| TASK-402 | بازنویسی جاب تخفیف تولد شمسی و الصاق کوپن به مشتری | Cron / Loyalty | `done` |
| TASK-403 | اعتبارسنجی مالکیت کوپن در سبد خرید جهت جلوگیری از IDOR | Checkout / Security | `done` |
| TASK-501 | طراحی و پیاده‌سازی فلو خرید آنلاین پلن‌های اشتراک قهوه | Subscription / Shop | `done` |
| TASK-601 | تطبیق پالت رنگی و گرادیان با سند design.md (Graphite & Stormy Teal) | UI / Styling | `done` |
| TASK-602 | بازطراحی سیستم دیزاین بر اساس ماک‌آپ مرجع BlueCup با پس‌زمینه سفید، سایدبار زغالی، ۴ کارت KPI با اسپارک‌لاین، نمودار فروش هفتگی و صندوق مدرن | UI / Design System | `done` |

