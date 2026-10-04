# Phase 1 — Cafe Management System (Laravel Core)

You are a senior Laravel architect and full-stack engineer.

Build a production-ready internal management system for a single, real cafe.
This is a client project (real business), not a demo. Correctness of the daily-usage
business rule matters more than architectural sophistication — keep the code simple
and maintainable, not over-engineered.

## Context & Non-Goals (read first)

- This is Phase 1 only. Do NOT build WordPress, WooCommerce, OTP login, or any
  customer-facing website in this phase — that is a separate, not-yet-committed
  Phase 2. Design the data model to be compatible with it later, but do not build it now.
- The cafe currently has a single physical location and a single register/POS in use
  at any given time. Do not build multi-branch UI or concurrency handling for multiple
  simultaneous terminals. However, keep `cafe_id` on every business table (see below)
  so a second cafe or SaaS use case can be added later without a schema rewrite.
- Staff/owner authentication is simple: phone number OR email + password. No OTP,
  no 2FA. Customers never log in or authenticate in Phase 1 — they are just records
  staff look up by phone.
- Skip the Repository pattern entirely unless a specific model genuinely needs one
  (e.g. complex report queries). Don't wrap simple Eloquent CRUD in repositories
  just for the pattern's sake — it adds a layer with no benefit at this scale.

## Tech Stack

- Laravel 12, PHP 8.3+, MySQL
- Laravel Sanctum (session auth for the web panel now; token auth reserved for the
  future Phase 2 API — implement the routes so this transition needs no rework)
- Laravel Blade + Tailwind CSS + Alpine.js
- Architecture: MVC + Service Layer, Form Requests for validation, Policies/Gates
  for authorization

## System Purpose

Internal cafe management system. Core feature: coffee subscriptions with a daily
free-item credit.

Example: "100 Days Coffee Subscription" — 100 total credits, 100-day duration,
max 1 free item per day, redeemable only for a plan-specific list of allowed
products (e.g. Espresso, Americano, Iced Americano).

Manages: customers, subscriptions, daily usage, products, staff, reports, internal
(walk-in) orders.

## Roles & Permissions

**Owner/Admin:** full access — manage staff, customers, subscription plans, products;
create/activate/cancel subscriptions; view all reports and all orders.

**Staff:** search customers, view customer subscription, register daily usage,
create manual (walk-in) orders. Cannot manage plans, staff, or view financial reports.

## Database Design

Every business table includes `cafe_id` (future-proofing only — do not build
cross-cafe UI).

- **cafes**: id, name, phone, address, timestamps
- **users** (staff/owner accounts): id, cafe_id, name, phone, email, password, role (admin|staff)
- **customers**: id, cafe_id, name, phone (unique per cafe), email (nullable), address (nullable), timestamps
- **subscription_plans**: id, cafe_id, name, total_items, duration_days, price, status
- **subscription_plan_products** (pivot — allowed products per plan): plan_id, product_id
- **subscriptions**: id, cafe_id, customer_id, subscription_plan_id, total_items, remaining_items, start_date, expire_date, status (active|expired|cancelled)
- **products**: id, cafe_id, name, type, price, status — seed Espresso, Americano, Iced Americano
- **subscription_usages**: id, cafe_id, customer_id, subscription_id, product_id, used_date, created_by (staff user_id)
  - **Critical**: add a unique composite index on (subscription_id, used_date) at the
    database level — not just application logic — so a duplicate daily redemption is
    impossible even under a race condition or retried request.
- **orders**: id, cafe_id, customer_id (nullable), type (subscription|normal), status (pending|processing|completed|cancelled), total_price
- **order_items**: id, order_id, product_id, quantity, price

## Core Features

**1. Customer Management** — create/edit customer, search by phone, profile page
showing active subscription, remaining credits, expiration date, usage history.

**2. Subscription Management** — admin creates plans (with allowed-product list),
assigns a plan to a customer, activates/cancels subscriptions.

**3. Daily Free Coffee Usage (the core workflow)**
1. Staff searches customer by phone.
2. System shows: name, subscription status, remaining items, today's usage status.
3. If not yet used today: show only the plan's allowed products as buttons. On
   selection: create `subscription_usage`, decrement `remaining_items`, create an
   order with type=subscription and total_price=0 — all inside a single DB
   transaction, guarded by the unique index above.
4. If already used today: show "Customer already used today's subscription item"
   and do not show product buttons.
5. Reject redemption (with a clear error) if `remaining_items` is 0 or the
   subscription is expired/cancelled, even if the unique-daily check would pass.

**4. Normal (walk-in) Orders** — staff creates a paid order for a customer with or
without a subscription, at normal product price.

**5. Dashboard** — stats: total customers, active subscriptions, today's free
coffees, today's orders; tables: recent subscriptions, today's usage, recent orders.

## Business Logic Services

- **SubscriptionService**: createSubscription(), activateSubscription(),
  checkActiveSubscription(), consumeDailyItem() (wraps the transaction + unique
  check + validations above), hasUsedToday(), getRemainingCredits()
- **CustomerService**: createCustomer(), searchCustomerByPhone(), getCustomerProfile()
- **OrderService**: createSubscriptionOrder(), createNormalOrder()

## API Foundation (for future Phase 2 — build now, don't wire up a frontend to it)

Routes: `/api/customers`, `/api/customers/{id}`, `/api/subscriptions`,
`/api/subscriptions/check`, `/api/subscription/use`, `/api/orders`.
Use Laravel API Resources, Form Requests, and Sanctum token middleware so Phase 2
can be added by pointing a client at these routes without changing them.

## Internal Admin Panel

Blade + Tailwind + Alpine.js. Persian RTL UI, responsive, clean cafe-dashboard style.
Pages: Dashboard; Customers (list/create/edit/profile); Subscriptions
(plans/active list/create); Products; Orders (internal); Reports (daily usage,
subscriptions).

## Security

Session auth (phone-or-email + password) for staff/owner, role middleware,
policies per model, CSRF protection, Form Request validation, password hashing.

## Project Structure

```
app/
  Models/
  Http/Controllers/Web/
  Http/Controllers/Api/
  Http/Requests/
  Http/Resources/
  Services/
  Policies/
resources/
  views/
  css/
  js/
```

## Deliverables

1. Laravel project structure
2. Migrations (with the unique index specified above)
3. Models + relationships (incl. `subscription_plan_products` pivot)
4. Seeders (cafe, admin user, 3 products, one sample subscription plan)
5. Session-based staff authentication + role middleware
6. Blade admin panel with Tailwind, Persian RTL
7. The three business services above, fully implemented (not stubs)
8. API route/controller/resource skeletons for Phase 2 (no frontend consuming them)
9. `.env.example` and a step-by-step installation guide (migrate, seed, storage link, build assets)

Keep the implementation lean and correct rather than exhaustively feature-complete —
this is for a single real cafe going live, not a SaaS platform yet.
