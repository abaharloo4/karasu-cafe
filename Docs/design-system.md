[9/25/2026 5:13 PM] Amir: دستورالعمل جامع طراحی UI/UX

سیستم مدیریت سفارشات، منو و عملیات کافه

تو یک Senior Product Designer + UI/UX Designer + Design System Architect هستی و باید رابط کاربری و تجربه کاربری یک سیستم حرفه‌ای مدیریت کافه را طراحی و در صورت فراهم بودن محیط توسعه، پیاده‌سازی کنی.

هدف این پروژه ساخت یک سیستم مدرن، سریع، کاربردی و حرفه‌ای برای مدیریت:

- سفارشات
- منوی کافه
- محصولات
- مشتریان
- میزها
- کارکنان
- موجودی
- گزارش‌ها
- تنظیمات
- عملیات روزانه کافه

است.

طراحی باید برای استفاده واقعی کارکنان کافه انجام شود، نه صرفاً یک Dashboard نمایشی.

---

1. هدف اصلی محصول

این سیستم باید به کارکنان و مدیر کافه اجازه دهد بدون پیچیدگی اضافی:

1. سفارش جدید ثبت کنند.
2. سفارش‌های فعال را ببینند.
3. وضعیت سفارش را تغییر دهند.
4. منو و محصولات را مدیریت کنند.
5. قیمت و موجودی محصولات را کنترل کنند.
6. مشتریان را مدیریت کنند.
7. میزها را مشاهده و مدیریت کنند.
8. فروش و عملکرد کافه را بررسی کنند.
9. گزارش‌های لازم را دریافت کنند.
10. عملیات روزانه را با کمترین تعداد کلیک انجام دهند.

اصل مهم:

«سرعت و خوانایی عملیاتی مهم‌تر از تزئینات بصری است.»

این سیستم در محیط واقعی کافه استفاده می‌شود، بنابراین UI باید حتی در زمان شلوغی کافه نیز قابل استفاده باشد.

---

2. زبان و جهت رابط کاربری

زبان اصلی:

فارسی

Direction:

RTL

تمام UI باید RTL باشد.

موارد زیر نیز باید RTL باشند:

- Navigation
- Sidebar
- Tables
- Forms
- Cards
- Modals
- Dropdowns
- Charts labels
- Notifications
- Order cards

اعداد، قیمت‌ها و زمان‌ها باید خوانا و استاندارد نمایش داده شوند.

مثال:

۲۷۵٬۰۰۰ تومان

از نمایش نامرتب اعداد و جداکننده‌های انگلیسی در متن فارسی خودداری کن.

---

3. شخصیت بصری برند

هویت بصری باید این ویژگی‌ها را منتقل کند:

- Warm
- Premium
- Natural
- Modern
- Calm
- Professional
- Café-oriented
- Minimal
- Friendly

طراحی نباید:

- بیش از حد Corporate باشد.
- شبیه پنل حسابداری قدیمی باشد.
- بیش از حد رنگارنگ باشد.
- بیش از حد Glassmorphism داشته باشد.
- از Shadowهای سنگین استفاده کند.
- شبیه Template آماده Admin Dashboard به نظر برسد.

هدف:

«یک سیستم مدیریت مدرن برای یک کافه واقعی، با هویت بصری گرم و طبیعی.»

---

4. پالت رنگ اصلی

از این رنگ‌ها به عنوان Design Tokens استفاده کن.

Primary Colors

--olive-leaf: #606C38;
--black-forest: #283618;
--cornsilk: #FEFAE0;
--sunlit-clay: #DDA15E;
--copperwood: #BC6C25;

---

نقش رنگ‌ها

Black Forest

#283618

کاربرد:

- Sidebar
- Headerهای مهم
- متن اصلی
- Navigation
- عناصر Dark
- Footer
- Primary dark surfaces

---

Olive Leaf

#606C38

کاربرد:

- Primary action
- Active navigation
- Success
- Statusهای مثبت
- Buttonهای اصلی
- Iconهای مهم

---

Cornsilk

#FEFAE0

کاربرد:

- Main background
- Card background
- Form background
- صفحات اصلی
- Surfaceهای روشن

این رنگ باید رنگ غالب UI روشن باشد.

---

Sunlit Clay

#DDA15E

کاربرد:

- Highlight
- Secondary CTA
- KPI cards
- Badge
- Accent
- Chart series

---

Copperwood

#BC6C25

کاربرد:

- CTA مهم
- سفارش جدید
- هشدارهای عملیاتی
- قیمت‌ها
- عناصر مهم مرتبط با فروش
- Active emphasis

---

5. قوانین استفاده از رنگ

رنگ‌ها نباید به صورت مساوی استفاده شوند.

Hierarchy:

Cornsilk      → 50-60%
Black Forest  → 15-20%
Olive Leaf    → 10-15%
Sunlit Clay   → 5-10%
Copperwood    → 5-10%

رنگ اصلی صفحه باید روشن و آرام باشد.

از Gradient پنج‌رنگی به عنوان Background اصلی استفاده نکن.

Gradient فقط برای موارد محدود استفاده شود:

- Hero
- Special KPI
- Promotional Banner
- Empty Stateهای خاص
- Splash Screen
- عناصر بصری برند

Gradient پیشنهادی:

linear-gradient(
  135deg,
  #283618,
  #606C38
);

یا:

linear-gradient(
  135deg,
  #606C38,
  #DDA15E
);

از ترکیب مستقیم تمام پنج رنگ در یک عنصر UI خودداری کن.

---

6. Typography

فونت اصلی:

Vazirmatn

Hierarchy:

Display
32-40px / 700

Page Title
24-28px / 700

Section Title
18-20px / 600

Card Title
15-17px / 600

Body
14-15px / 400-500

Caption
12-13px / 400

Numbers
20-28px / 700

قیمت‌ها و KPIهای مهم باید وزن بیشتری داشته باشند.

---

7. Layout اصلی

Desktop layout:
[9/25/2026 5:13 PM] Amir: ┌──────────────────────────────────────────────┐
│                    Topbar                    │
├───────────────┬──────────────────────────────┤
│               │                              │
│    Sidebar    │          Main Content        │
│               │                              │
│               │                              │
└───────────────┴──────────────────────────────┘

در RTL:

Sidebar در سمت راست قرار بگیرد.

اما در صورت نیاز به POS سریع، امکان استفاده از Navigation فشرده نیز در نظر گرفته شود.

---

8. Sidebar

Sidebar باید شامل:

- Logo
- نام سیستم
- داشبورد
- سفارشات
- منو
- محصولات
- مشتریان
- میزها
- انبار
- گزارش‌ها
- کارکنان
- تنظیمات

باشد.

پایین Sidebar:

- وضعیت سیستم
- نام کاربر
- نقش کاربر
- Logout

Active state:

Background: #606C38
Text: #FEFAE0

Hover:

Background: rgba(96,108,56,0.12)

Sidebar باید امکان Collapse داشته باشد.

---

9. Dashboard

Dashboard باید اطلاعات مهم را در اولین نگاه نمایش دهد.

KPI Cards

حداقل:

فروش امروز

۱۲٬۸۷۰٬۰۰۰ تومان
+22%

سفارش‌های امروز

۱۲۴
+18%

مشتریان امروز

۴۸
+12%

میانگین سفارش

۲۷۵٬۰۰۰ تومان

---

نمودار فروش

نمودار:

- فروش روزانه
- فروش هفتگی
- فروش ماهانه

Filter:

امروز
این هفته
این ماه

رنگ نمودار:

- Olive Leaf
- Copperwood

از تعداد زیاد رنگ استفاده نکن.

---

10. Recent Orders

نمایش آخرین سفارش‌ها:

شماره سفارش
مشتری
آیتم‌ها
مبلغ
زمان
وضعیت

مثال:

#1245
امیر محمد
کاپوچینو ×2
۲۷۵٬۰۰۰ تومان
14:32
در حال آماده‌سازی

Status Badge:

در انتظار
در حال آماده‌سازی
آماده
تحویل شده
لغو شده

---

11. Order Management

صفحه سفارشات باید یکی از مهم‌ترین بخش‌های سیستم باشد.

دو حالت ارائه شود:

Table View

برای مدیر.

Kanban / Queue View

برای کارکنان.

Kanban:

ثبت شده
      ↓
در حال آماده‌سازی
      ↓
آماده تحویل
      ↓
تحویل شده

هر کارت سفارش شامل:

- Order ID
- زمان ثبت
- میز
- مشتری
- محصولات
- تعداد
- مبلغ
- وضعیت
- زمان انتظار

باشد.

---

12. Order Details

صفحه جزئیات سفارش:

جزئیات سفارش #1245

مشتری
امیر محمد

میز
میز 04

────────────────

کاپوچینو ×2
۱۵۰٬۰۰۰

کیک شکلاتی ×1
۹۰٬۰۰۰

آمریکانو ×1
۵۵٬۰۰۰

────────────────

جمع:
۲۹۵٬۰۰۰

تخفیف:
۲۰٬۰۰۰

مبلغ نهایی:
۲۷۵٬۰۰۰

سمت دیگر:

Order Timeline

✓ ثبت سفارش
14:28

✓ پرداخت
14:29

● در حال آماده‌سازی
14:31

○ آماده تحویل

○ تحویل شده

---

13. New Order

ثبت سفارش باید بسیار سریع باشد.

Layout:

┌────────────────────────────┬──────────────────┐
│                            │                  │
│      Product Catalog       │   Current Order  │
│                            │                  │
│      Categories            │   Items          │
│                            │                  │
│      Product Grid          │   Total          │
│                            │                  │
│                            │   [ثبت سفارش]    │
└────────────────────────────┴──────────────────┘

---

14. Product Cards

هر محصول:

- تصویر
- نام
- قیمت
- وضعیت موجودی
- دکمه Add

مثال:

┌────────────────────┐
│                    │
│       IMAGE        │
│                    │
├────────────────────┤
│ کاپوچینو           │
│ ۷۵٬۰۰۰ تومان       │
│              [+]   │
└────────────────────┘

وقتی محصول انتخاب شد:

- تعداد
+ تعداد

نمایش داده شود.

---

15. Menu Management

صفحه مدیریت منو:

دسته‌بندی‌ها
محصولات
افزودن محصول

Categories:

- قهوه
- چای
- نوشیدنی سرد
- دسر
- غذا
- میان‌وعده

مدیر بتواند:

- افزودن دسته
- ویرایش
- حذف
- Sort
- فعال/غیرفعال کردن

را انجام دهد.

---

16. Product Management

Product fields:

نام محصول
دسته‌بندی
توضیحات
قیمت
قیمت تخفیف
تصویر
موجودی
وضعیت
مواد تشکیل‌دهنده
زمان آماده‌سازی

Status:

موجود
ناموجود
موقتاً ناموجود
غیرفعال

---

17. Customer Management

صفحه مشتریان:

نام
شماره موبایل
تعداد سفارش
مجموع خرید
آخرین سفارش
تاریخ عضویت

Customer Detail:

- اطلاعات مشتری
- سفارش‌های قبلی
- مجموع خرید
- میانگین سفارش
- یادداشت داخلی

---

18. Table Management

نمایش میزها به شکل Visual Grid.

مثال:

┌──────┐ ┌──────┐ ┌──────┐
│  01  │ │  02  │ │  03  │
│ آزاد │ │اشغال │ │ آزاد │
└──────┘ └──────┘ └──────┘

States:

آزاد
اشغال
رزرو
در انتظار تسویه

هر وضعیت باید رنگ و Icon مخصوص داشته باشد.

---

19. Inventory

Inventory باید شامل:
[9/25/2026 5:13 PM] Amir: نام کالا
موجودی فعلی
حداقل موجودی
واحد
آخرین بروزرسانی
وضعیت

Status:

موجود
کم
بحرانی
تمام شده

هشدار کمبود موجودی باید واضح اما غیرتهاجمی باشد.

---

20. Reports

گزارش‌ها:

Sales

- فروش روزانه
- فروش هفتگی
- فروش ماهانه
- مقایسه بازه‌ها

Products

- پرفروش‌ترین محصولات
- کم‌فروش‌ترین محصولات
- درآمد هر محصول

Orders

- تعداد سفارش
- میانگین سفارش
- میانگین زمان آماده‌سازی

Customers

- مشتریان پرتکرار
- مشتریان جدید
- مجموع خرید

---

21. Staff Management

مدیر بتواند:

- کارمند جدید ایجاد کند
- نقش تعیین کند
- دسترسی تعیین کند
- وضعیت فعال/غیرفعال کند

Roles:

Manager
Cashier
Barista
Staff

Permissionها باید قابل مدیریت باشند.

---

22. Notifications

Notification Center:

سفارش جدید
سفارش آماده شد
موجودی کم است
سفارش لغو شد
تغییر وضعیت

Notificationها باید timestamp داشته باشند.

---

23. Search

Global Search باید امکان جستجوی:

- سفارش
- مشتری
- محصول
- شماره میز

را فراهم کند.

Search باید سریع باشد.

Keyboard shortcut در Desktop در نظر گرفته شود:

Ctrl + K

---

24. Modal و Drawer

برای عملیات سریع از Modal / Drawer استفاده کن.

موارد مناسب:

- افزودن محصول
- ویرایش محصول
- مشاهده سفارش
- تغییر وضعیت
- افزودن مشتری
- تغییر میز

از باز کردن صفحه جدید برای عملیات کوچک خودداری کن.

---

25. Empty States

هر صفحه باید Empty State داشته باشد.

مثال:

هنوز سفارشی ثبت نشده

وقتی اولین سفارش ثبت شود
اینجا نمایش داده خواهد شد.

[ثبت سفارش جدید]

Empty State نباید فقط یک Icon بزرگ و متن باشد.

---

26. Loading States

از Skeleton Loading استفاده کن.

از Spinnerهای بزرگ و آزاردهنده خودداری کن.

برای:

- Table
- Cards
- Dashboard
- Orders
- Product Grid

Skeleton اختصاصی طراحی شود.

---

27. Error States

Error باید قابل فهم باشد.

مثلاً:

بارگذاری سفارش‌ها انجام نشد.

اطلاعات سفارش‌ها در حال حاضر
قابل دریافت نیست.

[تلاش مجدد]

از Error Messageهای فنی مثل:

500 Internal Server Error

در UI عادی استفاده نکن.

---

28. Confirmation

برای عملیات خطرناک:

- حذف محصول
- حذف مشتری
- لغو سفارش
- حذف دسته‌بندی

Confirmation Dialog نمایش داده شود.

مثال:

حذف محصول؟

این محصول از منوی فعال حذف خواهد شد.

[انصراف] [حذف محصول]

---

29. Toast

برای عملیات موفق:

✓ سفارش با موفقیت ثبت شد

یا:

✓ محصول بروزرسانی شد

Toast باید:

- کوچک
- واضح
- کوتاه
- غیرمزاحم

باشد.

---

30. Responsive Design

سیستم باید کاملاً Responsive باشد.

Desktop

Sidebar کامل.

Tablet

Sidebar فشرده.

Mobile

Bottom Navigation یا Sidebar Drawer.

Mobile navigation:

خانه
سفارشات
+
منو
بیشتر

دکمه "+" برای عملیات سریع باشد.

---

31. Mobile Order Screen

نسخه موبایل صفحه سفارش باید:

- Touch-friendly
- سریع
- خوانا
- دارای دکمه‌های بزرگ
- دارای Product Grid مناسب

باشد.

حداقل Touch Target:

44 × 44px

---

32. Dark Mode

Dark Mode نیز طراحی شود.

Dark palette باید بر اساس:

#283618
#606C38

ساخته شود.

اما در Dark Mode نباید:

#000000

به عنوان Background اصلی استفاده شود.

به جای آن از Dark Forestهای نرم استفاده کن.

مثلاً:

#18220F
#202B17
#283618

Cornsilk برای متن اصلی Dark Mode استفاده شود.

---

33. Accessibility

UI باید تا حد امکان مطابق WCAG طراحی شود.

موارد مهم:

- Contrast مناسب
- Focus State
- Keyboard Navigation
- Semantic HTML
- Accessible Labels
- Screen Reader support
- Touch target مناسب

رنگ نباید تنها راه انتقال وضعیت باشد.

مثلاً:

🟢 آماده

نباید تنها با رنگ سبز مشخص شود.

از:

Icon + Label + Color

استفاده کن.

---

34. Animation

Animation باید subtle باشد.

استفاده مناسب:

- Page transition
- Modal
- Drawer
- Dropdown
- Toast
- Order status change
- Add-to-cart
- Hover
- Skeleton

Duration:

150ms - 300ms

از Animationهای طولانی و نمایشی خودداری کن.

هدف:

«UI باید زنده باشد، نه اینکه هر کلیک کاربر را تبدیل به تریلر سینمایی کند.»

---

35. Icons

از یک Icon System واحد استفاده کن.

پیشنهاد:

Lucide Icons

Iconها:

- ساده
- خطی
- consistent
- RTL friendly

باشند.

از ترکیب چند سبک مختلف Icon خودداری کن.

---

36. Images

تصاویر محصولات باید:

- High quality
- Warm
- Natural
- Café-oriented
- Consistent

باشند.

Image ratio پیشنهادی:

4:3

یا:

1:1

برای Product Card.

از تصاویر بیش از حد اشباع یا مصنوعی استفاده نکن.

---

37. Cards

Card design:

background: #FEFAE0
border: rgba(40,54,24,0.08)
border-radius: 16px

Shadow:
[9/25/2026 5:13 PM] Amir: 0 4px 20px rgba(40,54,24,0.08)

Border باید ظریف باشد.

---

38. Buttons

Primary

Background: #606C38
Text: #FEFAE0

Important CTA

Background: #BC6C25
Text: #FEFAE0

Secondary

Background: transparent
Border: #606C38
Text: #283618

Danger

برای عملیات حذف از یک رنگ قرمز استاندارد و محدود استفاده کن.

قرمز نباید وارد Brand Palette اصلی شود.

---

39. Design Tokens

تمام مقادیر مهم را به Token تبدیل کن.

مثلاً:

:root {
  --color-olive: #606C38;
  --color-forest: #283618;
  --color-cornsilk: #FEFAE0;
  --color-clay: #DDA15E;
  --color-copper: #BC6C25;

  --radius-sm: 8px;
  --radius-md: 12px;
  --radius-lg: 16px;
  --radius-xl: 24px;

  --shadow-sm: 0 2px 8px rgba(40,54,24,.06);
  --shadow-md: 0 4px 20px rgba(40,54,24,.08);

  --transition-fast: 150ms;
  --transition-normal: 250ms;
}

---

40. Component System

کامپوننت‌ها باید Reusable باشند.

حداقل:

Button
Input
Select
SearchInput
Modal
Drawer
Dropdown
Tabs
Badge
Toast
Tooltip
Card
StatCard
ProductCard
OrderCard
OrderStatus
DataTable
Pagination
EmptyState
ErrorState
Skeleton
Avatar
DatePicker
Price
Sidebar
Topbar
BottomNavigation

---

41. UX Principles

همیشه این اولویت را حفظ کن:

Speed
↓
Clarity
↓
Consistency
↓
Accessibility
↓
Visual Beauty

زیبایی نباید باعث کاهش سرعت عملیات شود.

برای ثبت یک سفارش ساده، تعداد تعاملات باید تا حد ممکن کم باشد.

---

42. مهم‌ترین User Flow

Flow اصلی:

New Order
↓
Select Table / Customer
↓
Select Category
↓
Select Product
↓
Set Quantity
↓
Review Cart
↓
Confirm Order
↓
Payment
↓
Order Created
↓
Kitchen / Bar Queue
↓
Preparing
↓
Ready
↓
Delivered

این Flow باید با کمترین اصطکاک ممکن طراحی شود.

---

43. Order Status System

Statusها:

pending
confirmed
preparing
ready
served
completed
cancelled

هر Status:

- Label فارسی
- Icon
- Color
- State transition

داشته باشد.

---

44. Manager Dashboard UX

مدیر باید در کمتر از چند ثانیه بتواند بفهمد:

1. امروز چقدر فروش داشته‌ایم؟
2. چند سفارش داریم؟
3. چند سفارش در حال آماده‌سازی است؟
4. چه محصولاتی پرفروش هستند؟
5. آیا موجودی مهمی رو به اتمام است؟
6. وضعیت کلی کافه چگونه است؟

هر چیزی که برای پاسخ به این شش سؤال لازم نیست، نباید در بالای Dashboard قرار بگیرد.

---

45. Staff UX

کارکنان نباید اطلاعات مدیریتی غیرضروری ببینند.

Staff interface باید:

- ساده‌تر
- سریع‌تر
- Action-oriented

باشد.

مثلاً:

سفارش جدید
سفارش‌های فعال
آماده تحویل
میزها

باید در دسترس‌ترین قسمت قرار بگیرند.

---

46. Manager UX

Manager interface:

Dashboard
Orders
Menu
Customers
Tables
Inventory
Reports
Staff
Settings

دسترسی گسترده‌تر داشته باشد.

---

47. Security UX

برای عملیات حساس:

- تغییر قیمت
- حذف محصول
- لغو سفارش
- تغییر دسترسی
- تغییر نقش کاربر

Confirmation و در صورت نیاز Re-authentication در نظر گرفته شود.

---

48. Performance

UI باید سریع باشد.

از موارد زیر جلوگیری کن:

- تصاویر بدون optimization
- Animationهای سنگین
- Shadowهای متعدد
- DOM بیش از حد پیچیده
- Re-render غیرضروری
- Componentهای بزرگ و monolithic

Product Grid در منو باید برای تعداد زیاد محصولات نیز قابل استفاده باشد.

---

49. Design Deliverables

در پایان طراحی باید این موارد مشخص باشند:

Screens

01 Login
02 Dashboard
03 Orders
04 Order Details
05 New Order
06 Menu
07 Product Details
08 Product Create/Edit
09 Categories
10 Customers
11 Customer Details
12 Tables
13 Inventory
14 Reports
15 Staff
16 Notifications
17 Settings
18 Profile

---

50. Design System Deliverables

باید شامل:

Colors
Typography
Spacing
Radius
Shadows
Buttons
Inputs
Forms
Cards
Tables
Badges
Modals
Drawers
Navigation
Charts
Empty States
Loading States
Error States

باشد.

---

51. Spacing System

از سیستم 4px استفاده کن:

4
8
12
16
20
24
32
40
48
64

Spacing تصادفی استفاده نکن.

---

52. Grid

Desktop:

12-column grid

Tablet:

8-column grid

Mobile:

4-column grid

Container:

max-width: 1440px

---

53. UX برای حالت شلوغی کافه

این مورد بسیار مهم است.

فرض کن:

- چند سفارش همزمان ثبت می‌شود.
- کارمند عجله دارد.
- صفحه روی Tablet است.
- نور محیط زیاد است.
- کاربر فقط چند ثانیه برای پیدا کردن محصول دارد.

UI باید همچنان قابل استفاده باشد.

بنابراین:
[9/25/2026 5:13 PM] Amir: - دکمه‌های اصلی بزرگ باشند.
- اطلاعات مهم برجسته باشند.
- Product Search سریع باشد.
- دسته‌بندی‌ها همیشه در دسترس باشند.
- وضعیت سفارش واضح باشد.
- Modalهای غیرضروری حذف شوند.
- عملیات مهم با یک یا دو تعامل انجام شوند.

---

54. Design Philosophy

طراحی نهایی باید ترکیبی از:

Modern SaaS Dashboard
+
Premium Café POS
+
Warm Editorial Design
+
Minimal Scandinavian UI

باشد.

اما نتیجه نباید کپی هیچ یک از این سبک‌ها باشد.

---

55. چیزی که نباید انجام دهی

از موارد زیر خودداری کن:

- Gradientهای بیش از حد
- Glassmorphism سنگین
- Neon Colors
- Shadowهای شدید
- Borderهای زیاد
- Typography فانتزی
- Iconهای سه‌بعدی
- Animationهای طولانی
- Cardهای بیش از حد گرد
- Layoutهای شلوغ
- اطلاعات غیرضروری
- Dashboardهای صرفاً نمایشی
- استفاده افراطی از رنگ Copper
- استفاده افراطی از تصویر
- UI شبیه Templateهای آماده Admin

---

56. اولویت طراحی

هر تصمیم UI را با این ترتیب ارزیابی کن:

1. آیا استفاده از آن سریع است؟
2. آیا کاربر می‌فهمد چه کاری باید انجام دهد؟
3. آیا اطلاعات مهم به‌وضوح دیده می‌شوند؟
4. آیا در موبایل قابل استفاده است؟
5. آیا Accessible است؟
6. آیا با Design System هماهنگ است؟
7. آیا زیباست؟

اگر زیبایی با usability تضاد داشت:

Usability را انتخاب کن.

---

57. خروجی مورد انتظار از Agent

قبل از شروع implementation:

1. ساختار صفحات را مشخص کن.
2. User Flowها را مشخص کن.
3. Design Tokens را تعریف کن.
4. Component Architecture را مشخص کن.
5. Responsive behavior را مشخص کن.
6. Stateهای مختلف هر component را تعریف کن.
7. سپس UI را پیاده‌سازی کن.

هیچ صفحه‌ای نباید بدون تعریف:

- Loading
- Empty
- Error
- Success
- Disabled
- Hover
- Focus
- Active

طراحی شود.

---

58. کیفیت نهایی

محصول نهایی باید این احساس را ایجاد کند:

««این یک سیستم حرفه‌ای است که برای یک کافه واقعی ساخته شده، نه یک Template که اسمش را Café گذاشته‌اند.»»

طراحی باید:

- Premium
- Practical
- Fast
- Warm
- Consistent
- Responsive
- Accessible
- Scalable

باشد.

---

59. اصل نهایی

کمتر، اما بهتر.

هر عنصر UI باید دلیل وجود داشته باشد.

اگر یک Card، Button، Animation، Badge، رنگ یا اطلاعات هیچ ارزش عملی یا UX مشخصی ندارد، آن را حذف کن.

هدف ساخت یک UI زیبا نیست.

هدف ساخت یک سیستم مدیریت کافه است که استفاده از آن سریع، طبیعی و لذت‌بخش باشد.

طراحی را بر اساس این اصل اجرا کن:

«The interface should feel like a calm café, even when the café itself is busy.»