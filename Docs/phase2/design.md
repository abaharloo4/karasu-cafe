# Café Management System --- Design System

## 1. Design Direction

The visual direction of this café management system is:

-   Modern
-   Professional
-   Premium
-   Calm
-   Clean
-   Technology-oriented
-   Café-inspired without becoming decorative
-   Optimized for fast operational use

The interface must feel like a professional SaaS/POS product designed
specifically for a real café.

The design must **not** look like a generic admin template.

------------------------------------------------------------------------

# 2. Core Color Palette

## Brand Colors

  ----------------------------------------------------------------------------------------------
  Token              Name        HEX         RGB               HSL               Primary Role
  ------------------ ----------- ----------- ----------------- ----------------- ---------------
  `graphite`         Graphite    `#353535`   `53, 53, 53`      `0, 0%, 21%`      Dark surfaces,
                                                                                 text,
                                                                                 navigation

  `stormy-teal`      Stormy Teal `#3C6E71`   `60, 110, 113`    `183, 31%, 34%`   Primary brand,
                                                                                 active states

  `white`            White       `#FFFFFF`   `255, 255, 255`   `0, 0%, 100%`     Cards and
                                                                                 high-contrast
                                                                                 surfaces

  `alabaster-grey`   Alabaster   `#D9D9D9`   `217, 217, 217`   `0, 0%, 85%`      Borders,
                     Grey                                                        dividers, muted
                                                                                 surfaces

  `yale-blue`        Yale Blue   `#284B63`   `40, 75, 99`      `204, 42%, 27%`   Strong actions,
                                                                                 accents, charts
  ----------------------------------------------------------------------------------------------

------------------------------------------------------------------------

# 3. IMPORTANT: Application Background

## Background must NOT be pure white

The overall application background must reproduce the **cool blue/teal
atmospheric background visible in the approved reference design**.

Do not replace it with:

``` text
#FFFFFF
```

and do not use plain grey as the main application background.

The reference background is a soft, desaturated blue/teal environment
surrounding the white application surfaces.

### Background tokens

``` css
--background-base: #6A8C95;
--background-deep: #2C5466;
--background-teal: #3C6E71;
--background-light: #739AA3;
```

### Recommended application background

``` css
background:
  radial-gradient(
    circle at 50% 35%,
    rgba(60, 110, 113, 0.95) 0%,
    rgba(60, 110, 113, 0.55) 32%,
    rgba(40, 75, 99, 0.75) 68%,
    rgba(40, 75, 99, 0.95) 100%
  ),
  linear-gradient(
    135deg,
    #6A8C95 0%,
    #3C6E71 48%,
    #284B63 100%
  );
```

This background is part of the visual identity and must remain visible
around the main white surfaces.

### Background behavior

-   Fixed to the application viewport.
-   Do not use it behind dense text directly.
-   Main content sits on light surfaces above it.
-   Use a subtle atmospheric depth effect.
-   Never introduce neon blue, purple, green, or unrelated gradients.
-   Do not use the five-color gradient supplied by the palette generator
    as the main background.

------------------------------------------------------------------------

# 4. Surface System

The UI uses a layered surface system.

## Level 0 --- Application Environment

``` css
background: var(--background-base);
```

Use the approved blue/teal atmospheric gradient.

## Level 1 --- Main Surface

``` css
background: #FFFFFF;
```

Used for:

-   Main dashboard panels
-   Tables
-   Forms
-   Product areas
-   Order panels

## Level 2 --- Secondary Surface

``` css
background: #F7F9FA;
```

Used for:

-   Nested cards
-   Secondary sections
-   Search areas
-   Filters

## Level 3 --- Muted Surface

``` css
background: #EEF2F3;
```

Used for:

-   Disabled areas
-   Skeletons
-   Secondary information
-   Empty spaces
-   Non-critical containers

## Level 4 --- Brand Surface

``` css
background: #3C6E71;
```

Used for:

-   Active navigation
-   Important status
-   Selected components
-   Brand blocks

------------------------------------------------------------------------

# 5. Color Roles

## Graphite --- `#353535`

Use for:

-   Primary text
-   Sidebar
-   Dark navigation
-   Headings
-   Important labels
-   Dark icons
-   Footer
-   Secondary buttons

Do not use Graphite as the entire page background.

------------------------------------------------------------------------

## Stormy Teal --- `#3C6E71`

Use as the primary brand color.

Use for:

-   Primary buttons
-   Active navigation
-   Selected tabs
-   Success/ready states
-   Primary icons
-   Links
-   Focus states
-   Order actions
-   Positive operational indicators

------------------------------------------------------------------------

## White --- `#FFFFFF`

Use as the dominant UI surface.

Use for:

-   Cards
-   Tables
-   Forms
-   Product panels
-   Order panels
-   Main content surfaces
-   Modals
-   Drawers

White should remain visually dominant inside the application.

------------------------------------------------------------------------

## Alabaster Grey --- `#D9D9D9`

Use for:

-   Borders
-   Dividers
-   Input borders
-   Disabled states
-   Skeleton backgrounds
-   Secondary separators

Avoid using it as large dark-looking blocks.

------------------------------------------------------------------------

## Yale Blue --- `#284B63`

Use as the secondary strong accent.

Use for:

-   Important CTA buttons
-   Financial KPIs
-   Charts
-   Order totals
-   Strong headings in selected contexts
-   Secondary navigation emphasis
-   Important data visualization

------------------------------------------------------------------------

# 6. Color Distribution

Recommended visual distribution:

``` text
White / near-white surfaces     50–60%
Background blue/teal atmosphere 15–20%
Graphite                         8–12%
Stormy Teal                     10–15%
Yale Blue                        5–10%
Alabaster Grey                   5–10%
```

The exact percentage can change per screen, but the visual hierarchy
must remain calm.

Do not give equal visual weight to all five brand colors.

------------------------------------------------------------------------

# 7. Semantic Colors

Brand colors should not be forced to represent every semantic state.

Use additional semantic colors only where necessary.

``` css
--success: #3C6E71;
--info: #284B63;
--warning: #B9822B;
--danger: #B44A4A;
```

Semantic colors must be muted and professional.

Never use saturated neon red, green, yellow, or blue.

Always combine semantic color with:

-   icon
-   text label
-   state indicator

Color alone must never communicate critical information.

------------------------------------------------------------------------

# 8. Typography

## Primary Font

Use:

``` text
Vazirmatn
```

The entire application is Persian-first and RTL.

Recommended weights:

``` text
400 — Regular
500 — Medium
600 — SemiBold
700 — Bold
```

Avoid decorative Persian fonts.

------------------------------------------------------------------------

# 9. Type Scale

``` css
--text-xs: 12px;
--text-sm: 13px;
--text-md: 14px;
--text-base: 15px;
--text-lg: 16px;
--text-xl: 18px;
--text-2xl: 20px;
--text-3xl: 24px;
--text-4xl: 28px;
--text-5xl: 32px;
--text-6xl: 40px;
```

Recommended usage:

  Element               Size     Weight
  --------------- ---------- ----------
  Display           32--40px        700
  Page title        24--28px        700
  Section title     18--20px        600
  Card title        16--18px        600
  Body              14--15px   400--500
  Caption           12--13px        400
  KPI number        24--32px        700

------------------------------------------------------------------------

# 10. RTL

The entire application must use:

``` html
dir="rtl"
lang="fa"
```

RTL applies to:

-   Navigation
-   Forms
-   Tables
-   Cards
-   Modals
-   Drawers
-   Dropdowns
-   Order details
-   Charts labels
-   Pagination
-   Breadcrumbs

Do not simply mirror the interface mechanically.

Logical properties must be preferred:

``` css
margin-inline
padding-inline
inset-inline
border-inline
```

instead of unnecessary left/right-specific rules.

------------------------------------------------------------------------

# 11. Spacing System

Use a 4px spacing scale.

``` css
--space-1: 4px;
--space-2: 8px;
--space-3: 12px;
--space-4: 16px;
--space-5: 20px;
--space-6: 24px;
--space-8: 32px;
--space-10: 40px;
--space-12: 48px;
--space-16: 64px;
```

Avoid arbitrary spacing values unless a component specifically requires
them.

------------------------------------------------------------------------

# 12. Border Radius

``` css
--radius-sm: 8px;
--radius-md: 12px;
--radius-lg: 16px;
--radius-xl: 20px;
--radius-2xl: 24px;
```

Recommended:

-   Inputs: 10--12px
-   Buttons: 10--12px
-   Cards: 16px
-   Large panels: 20px
-   Modal: 20px
-   Floating action button: 50%

Do not make every component extremely rounded.

------------------------------------------------------------------------

# 13. Shadows

The design should be light and clean.

``` css
--shadow-xs:
  0 1px 3px rgba(40, 75, 99, 0.06);

--shadow-sm:
  0 2px 8px rgba(40, 75, 99, 0.08);

--shadow-md:
  0 6px 20px rgba(40, 75, 99, 0.10);

--shadow-lg:
  0 12px 36px rgba(40, 75, 99, 0.14);
```

Avoid black shadows.

Avoid heavy floating-card effects.

------------------------------------------------------------------------

# 14. Borders

Default:

``` css
border: 1px solid #D9D9D9;
```

Subtle border:

``` css
border-color: rgba(53, 53, 53, 0.08);
```

Active:

``` css
border-color: #3C6E71;
```

Focus:

``` css
border-color: #284B63;
```

------------------------------------------------------------------------

# 15. Main Layout

Desktop:

``` text
┌─────────────────────────────────────────────┐
│                 Topbar                      │
├──────────────┬──────────────────────────────┤
│              │                              │
│   Sidebar    │        Main Content          │
│              │                              │
│              │                              │
└──────────────┴──────────────────────────────┘
```

RTL layout:

-   Sidebar on the right.
-   Main content on the left.
-   Topbar spans the content area.
-   Application background remains visible around the shell where
    appropriate.

------------------------------------------------------------------------

# 16. Sidebar

Background:

``` css
#353535
```

Text:

``` css
#FFFFFF
```

Muted text:

``` css
#D9D9D9
```

Active item:

``` css
background: #3C6E71;
color: #FFFFFF;
```

Hover:

``` css
background: rgba(60, 110, 113, 0.18);
```

Sidebar should contain:

-   Logo
-   Dashboard
-   Orders
-   Menu
-   Products
-   Customers
-   Tables
-   Inventory
-   Reports
-   Staff
-   Settings

Bottom:

-   Current user
-   Role
-   Logout

Sidebar must support:

-   expanded
-   collapsed
-   mobile drawer

------------------------------------------------------------------------

# 17. Topbar

Topbar:

``` css
background: rgba(255,255,255,0.94);
border-bottom: 1px solid #D9D9D9;
```

Include:

-   Global search
-   Notifications
-   Theme toggle
-   User profile
-   Current role

Use subtle backdrop blur only if performance allows.

------------------------------------------------------------------------

# 18. Dashboard

Dashboard background remains the approved atmospheric application
background.

Main dashboard panels are white.

Primary KPI cards:

``` text
Sales
Orders
Customers
Average Order
```

KPI values use:

-   Yale Blue
-   Stormy Teal
-   Graphite

Avoid assigning five different colors to five KPI cards.

------------------------------------------------------------------------

# 19. Product Cards

Product card:

``` css
background: #FFFFFF;
border: 1px solid #D9D9D9;
border-radius: 16px;
box-shadow: var(--shadow-sm);
```

Product image:

-   1:1 or 4:3
-   natural café photography
-   consistent lighting
-   rounded top corners

Product name:

``` css
color: #353535;
font-weight: 600;
```

Price:

``` css
color: #284B63;
font-weight: 700;
```

Add button:

``` css
background: #3C6E71;
color: #FFFFFF;
```

Hover:

``` css
border-color: #3C6E71;
transform: translateY(-1px);
```

Do not over-animate product cards.

------------------------------------------------------------------------

# 20. POS / New Order

The POS screen is an operational priority.

Desktop:

``` text
┌──────────────────────────────┬────────────────────┐
│                              │                    │
│      Product Catalog         │    Current Order   │
│                              │                    │
│      Categories              │    Items           │
│                              │    Quantity        │
│      Product Grid            │    Total           │
│                              │                    │
│                              │   [ثبت سفارش]      │
└──────────────────────────────┴────────────────────┘
```

The order cart must remain visible while browsing products.

Primary order CTA:

``` css
background: #284B63;
color: #FFFFFF;
```

Secondary actions:

``` css
background: #3C6E71;
color: #FFFFFF;
```

------------------------------------------------------------------------

# 21. Order Status Colors

Recommended:

  Status             Color
  ------------------ -------------
  ثبت شده            Graphite
  تأیید شده          Yale Blue
  در حال آماده‌سازی   Yale Blue
  آماده              Stormy Teal
  تحویل شده          Stormy Teal
  تکمیل شده          Graphite
  لغو شده            Danger

Every status requires:

``` text
Icon + Label + Color
```

------------------------------------------------------------------------

# 22. Order Kanban

Columns:

``` text
ثبت شده
در حال آماده‌سازی
آماده تحویل
تحویل شده
```

Column header should have a small state indicator.

Cards should include:

-   Order ID
-   Time
-   Table
-   Customer
-   Items
-   Total
-   Status
-   Action menu

Avoid oversized cards.

------------------------------------------------------------------------

# 23. Order Details

Order details should use two main sections:

### Order Information

-   Customer
-   Table
-   Items
-   Quantities
-   Subtotal
-   Discount
-   Tax
-   Final amount

### Order Timeline

``` text
✓ ثبت سفارش
✓ پرداخت
● در حال آماده‌سازی
○ آماده تحویل
○ تحویل شده
```

Current state uses Stormy Teal.

Completed states use muted teal.

Future states use Alabaster Grey.

------------------------------------------------------------------------

# 24. Menu Management

Menu screen:

-   Categories
-   Products
-   Availability
-   Pricing
-   Search
-   Filters

Category tabs:

``` css
Active:
#3C6E71
```

Inactive:

``` css
background: #FFFFFF;
border: 1px solid #D9D9D9;
color: #353535;
```

------------------------------------------------------------------------

# 25. Tables

Table management uses a visual grid.

States:

``` text
Free
Occupied
Reserved
Waiting for payment
```

Use:

-   Icon
-   Label
-   Border
-   Small color indicator

Do not fill the entire table card with saturated color.

------------------------------------------------------------------------

# 26. Inventory

Inventory table:

``` text
Product
Current Stock
Minimum Stock
Unit
Last Updated
Status
```

Low-stock status uses warning color.

Critical stock uses danger color.

The design must remain readable even with hundreds of rows.

------------------------------------------------------------------------

# 27. Reports

Reports should use:

-   clean charts
-   white surfaces
-   teal/blue data series
-   Graphite labels
-   Alabaster grid lines

Primary chart colors:

``` css
--chart-primary: #3C6E71;
--chart-secondary: #284B63;
--chart-grid: #D9D9D9;
```

Do not use rainbow charts.

------------------------------------------------------------------------

# 28. Tables

Table header:

``` css
background: #F7F9FA;
color: #353535;
```

Rows:

``` css
background: #FFFFFF;
```

Hover:

``` css
background: rgba(60, 110, 113, 0.04);
```

Selected row:

``` css
background: rgba(60, 110, 113, 0.08);
```

Borders:

``` css
#D9D9D9
```

------------------------------------------------------------------------

# 29. Forms

Input:

``` css
background: #FFFFFF;
border: 1px solid #D9D9D9;
color: #353535;
```

Focus:

``` css
border-color: #284B63;
box-shadow: 0 0 0 3px rgba(40, 75, 99, 0.10);
```

Placeholder:

``` css
color: #7A7A7A;
```

Error:

``` css
border-color: #B44A4A;
```

Labels must always be visible.

Do not rely on placeholders as labels.

------------------------------------------------------------------------

# 30. Buttons

## Primary

``` css
background: #3C6E71;
color: #FFFFFF;
```

## Strong CTA

``` css
background: #284B63;
color: #FFFFFF;
```

## Secondary

``` css
background: #FFFFFF;
color: #284B63;
border: 1px solid #D9D9D9;
```

## Ghost

``` css
background: transparent;
color: #3C6E71;
```

## Danger

``` css
background: #B44A4A;
color: #FFFFFF;
```

Buttons must clearly communicate hierarchy.

------------------------------------------------------------------------

# 31. Icon System

Use one consistent icon family.

Recommended:

``` text
Lucide Icons
```

Icon characteristics:

-   outline
-   clean
-   minimal
-   consistent stroke
-   RTL compatible

Default icon size:

``` text
18–20px
```

Small:

``` text
16px
```

Large:

``` text
24px
```

Do not mix filled, outlined, 3D, and emoji icons.

------------------------------------------------------------------------

# 32. Search

Global search:

``` text
Ctrl + K
```

Search should support:

-   Orders
-   Customers
-   Products
-   Tables

Search input:

``` css
background: #F7F9FA;
border: 1px solid #D9D9D9;
```

Focus uses Yale Blue.

------------------------------------------------------------------------

# 33. Modal

Modal:

``` css
background: #FFFFFF;
border-radius: 20px;
box-shadow: var(--shadow-lg);
```

Overlay:

``` css
background: rgba(40, 75, 99, 0.42);
```

Modal should not occupy the entire screen unless the operation requires
it.

------------------------------------------------------------------------

# 34. Drawer

Use Drawer for:

-   quick order details
-   quick customer details
-   filters
-   quick product editing

Drawer background:

``` css
#FFFFFF
```

Header:

``` css
border-bottom: 1px solid #D9D9D9;
```

------------------------------------------------------------------------

# 35. Toast

Success:

``` css
background: #3C6E71;
color: #FFFFFF;
```

Information:

``` css
background: #284B63;
color: #FFFFFF;
```

Warning:

``` css
background: #B9822B;
color: #FFFFFF;
```

Error:

``` css
background: #B44A4A;
color: #FFFFFF;
```

Toast must be concise.

------------------------------------------------------------------------

# 36. Empty State

Use:

-   small illustration or icon
-   short title
-   one-line explanation
-   primary action

Example:

``` text
هنوز سفارشی ثبت نشده

اولین سفارش را ثبت کنید تا اینجا نمایش داده شود.

[ثبت سفارش جدید]
```

Avoid oversized empty-state illustrations.

------------------------------------------------------------------------

# 37. Loading

Use skeletons rather than large spinners.

Skeleton:

``` css
background: #EEF2F3;
```

Animation:

``` text
1500–2000ms
```

Keep loading animations subtle.

------------------------------------------------------------------------

# 38. Error State

Example:

``` text
بارگذاری سفارش‌ها انجام نشد

اطلاعات سفارش‌ها در حال حاضر در دسترس نیست.

[تلاش مجدد]
```

Do not expose technical errors to normal users.

------------------------------------------------------------------------

# 39. Responsive Breakpoints

``` css
--breakpoint-sm: 640px;
--breakpoint-md: 768px;
--breakpoint-lg: 1024px;
--breakpoint-xl: 1280px;
--breakpoint-2xl: 1440px;
```

### Desktop

Full sidebar + topbar.

### Tablet

Collapsed sidebar or compact navigation.

### Mobile

Drawer + bottom navigation.

------------------------------------------------------------------------

# 40. Mobile Navigation

Use:

``` text
خانه
سفارشات
+
منو
بیشتر
```

The center `+` action is the main quick action.

Touch targets must be at least:

``` text
44 × 44px
```

Prefer 48px where space permits.

------------------------------------------------------------------------

# 41. Dark Mode

Dark mode is optional but should use the same brand identity.

``` css
--dark-bg: #232A2D;
--dark-surface: #2C3437;
--dark-surface-2: #353535;
--dark-primary: #3C6E71;
--dark-accent: #284B63;
--dark-text: #FFFFFF;
--dark-muted: #D9D9D9;
```

Do not use pure black as the primary dark background.

------------------------------------------------------------------------

# 42. Animation

Animations should communicate state or hierarchy.

Use:

``` text
150ms — micro interaction
200ms — hover/focus
250ms — modal/drawer
300ms — page/component transition
```

Preferred easing:

``` css
cubic-bezier(0.2, 0.8, 0.2, 1)
```

Avoid:

-   bounce-heavy animations
-   long transitions
-   decorative animations
-   excessive parallax

------------------------------------------------------------------------

# 43. Accessibility

Requirements:

-   WCAG-aware contrast
-   visible keyboard focus
-   keyboard navigation
-   semantic HTML
-   accessible labels
-   correct form associations
-   minimum 44px touch target
-   color must not be the only state indicator
-   reduced-motion support

For reduced motion:

``` css
@media (prefers-reduced-motion: reduce) {
  * {
    animation-duration: 0.01ms !important;
    transition-duration: 0.01ms !important;
  }
}
```

------------------------------------------------------------------------

# 44. Image Direction

Product photography should be:

-   natural
-   warm-neutral
-   realistic
-   high quality
-   consistent
-   café-oriented

Avoid:

-   excessive saturation
-   fake-looking AI food photography
-   inconsistent lighting
-   unrelated stock photography

Product images should visually support the interface instead of
overpowering it.

------------------------------------------------------------------------

# 45. Gradient Rules

The original five-color gradient:

``` css
linear-gradient(
  90deg,
  #353535,
  #3C6E71,
  #FFFFFF,
  #D9D9D9,
  #284B63
);
```

must NOT be used as a generic background.

The preferred brand gradient is:

``` css
linear-gradient(
  135deg,
  #3C6E71 0%,
  #284B63 100%
);
```

For the atmospheric application environment, use the approved cool
blue/teal background gradient defined in Section 3.

Gradients are secondary visual tools, not the main UI language.

------------------------------------------------------------------------

# 46. Design Tokens

``` css
:root {
  /* Brand */
  --color-graphite: #353535;
  --color-stormy-teal: #3C6E71;
  --color-white: #FFFFFF;
  --color-alabaster-grey: #D9D9D9;
  --color-yale-blue: #284B63;

  /* Application Background */
  --background-base: #6A8C95;
  --background-deep: #2C5466;
  --background-teal: #3C6E71;
  --background-light: #739AA3;

  /* Surfaces */
  --surface-primary: #FFFFFF;
  --surface-secondary: #F7F9FA;
  --surface-muted: #EEF2F3;

  /* Text */
  --text-primary: #353535;
  --text-secondary: #5F6669;
  --text-muted: #7A8083;
  --text-on-dark: #FFFFFF;

  /* Semantic */
  --success: #3C6E71;
  --info: #284B63;
  --warning: #B9822B;
  --danger: #B44A4A;

  /* Spacing */
  --space-1: 4px;
  --space-2: 8px;
  --space-3: 12px;
  --space-4: 16px;
  --space-5: 20px;
  --space-6: 24px;
  --space-8: 32px;
  --space-10: 40px;
  --space-12: 48px;
  --space-16: 64px;

  /* Radius */
  --radius-sm: 8px;
  --radius-md: 12px;
  --radius-lg: 16px;
  --radius-xl: 20px;
  --radius-2xl: 24px;

  /* Shadows */
  --shadow-xs: 0 1px 3px rgba(40, 75, 99, 0.06);
  --shadow-sm: 0 2px 8px rgba(40, 75, 99, 0.08);
  --shadow-md: 0 6px 20px rgba(40, 75, 99, 0.10);
  --shadow-lg: 0 12px 36px rgba(40, 75, 99, 0.14);

  /* Motion */
  --duration-fast: 150ms;
  --duration-normal: 250ms;
  --duration-slow: 300ms;
}
```

------------------------------------------------------------------------

# 47. Core Component Inventory

The component system must contain reusable versions of:

``` text
Button
IconButton
Input
Textarea
Select
SearchInput
DatePicker
Checkbox
Radio
Switch
Tabs
Badge
StatusBadge
Tooltip
Dropdown
Menu
Modal
Drawer
Toast
Alert
Avatar
Card
StatCard
ProductCard
OrderCard
OrderStatus
OrderTimeline
DataTable
Pagination
EmptyState
ErrorState
Skeleton
Sidebar
Topbar
BottomNavigation
Breadcrumb
Chart
KPI
```

------------------------------------------------------------------------

# 48. Main Screens

The initial design system must support:

``` text
01. Login
02. Dashboard
03. Orders
04. Order Details
05. New Order / POS
06. Menu
07. Product Details
08. Create Product
09. Edit Product
10. Categories
11. Customers
12. Customer Details
13. Tables
14. Inventory
15. Reports
16. Staff
17. Notifications
18. Settings
19. Profile
```

------------------------------------------------------------------------

# 49. UX Priority

Every interface decision must follow this order:

``` text
1. Speed
2. Clarity
3. Consistency
4. Accessibility
5. Reliability
6. Visual quality
```

If visual decoration conflicts with usability, remove the decoration.

------------------------------------------------------------------------

# 50. Café Operational Context

The system will be used while the café is busy.

Assume:

-   multiple orders arrive simultaneously
-   employees are under time pressure
-   the interface may be used on tablets
-   users may only glance at the screen for a few seconds
-   product search must be immediate
-   order status must be obvious
-   important actions must be easy to reach

Therefore:

-   keep primary actions visible
-   keep categories accessible
-   minimize unnecessary dialogs
-   keep order cart visible
-   use strong visual hierarchy
-   avoid information overload
-   use large enough touch targets

------------------------------------------------------------------------

# 51. Final Visual Rule

The approved reference visual language is:

**cool blue/teal atmospheric background + white application surfaces +
graphite navigation + Stormy Teal primary actions + Yale Blue strong
accents + restrained grey borders.**

The **cool blue/teal background surrounding the interface is intentional
and must remain**.

Do not reinterpret the background as plain white.

Do not replace it with a generic dashboard grey.

Do not introduce unrelated colors.

The final result should feel like:

> A premium, modern café management product operating inside a calm
> blue/teal environment.

It should look professional enough for a manager, fast enough for a
cashier, and clear enough for a barista during a busy shift.

------------------------------------------------------------------------

# 52. Final Design Principle

**The interface should feel calm even when the café is busy.**

Every component must earn its place.

Every color must have a role.

Every animation must have a reason.

Every screen must prioritize the user's next action.

The final UI should not look like a template with a café logo attached
to it. It should feel like a purpose-built café management platform.
