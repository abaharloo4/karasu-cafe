# Phase 2 — Online Customer Platform (All-Laravel, cPanel Deployment)

You are a senior Laravel architect and full-stack engineer.

**Status: this phase is not currently ordered by the client.** It is a possible
future extension of an already-completed Phase 1 (Laravel core system managing
customers, subscriptions, products, orders, and daily usage, used internally by
cafe staff via a Blade admin panel).

**Architecture change from earlier planning: there is no WordPress or WooCommerce
anywhere in this project.** Phase 2 is the *same Laravel monolith* as Phase 1,
extended with a customer-facing storefront and account area, also built in
Laravel Blade. Do not introduce a second framework, a second codebase, or a
second database. One Laravel app, one deployment, one source of truth.

This app must run on **cPanel shared hosting** — not a VPS, not Forge, not
Docker. Every technical decision below is made with that constraint in mind.

## Why this matters (read before building anything)

Typical cPanel shared hosting means:
- No SSH root access, no Supervisor, no systemd — you cannot run a persistent
  queue worker process (`php artisan queue:work` as a daemon).
- No Redis or Memcached available by default.
- No control over the web server config beyond `.htaccess`.
- The document root is usually the account's `public_html/`, not automatically
  Laravel's `public/` folder — this needs an explicit deployment strategy.
- Background jobs only run via **cPanel Cron Jobs**, not long-running workers.
- Symlink support (`php artisan storage:link`) varies by host — must be verified,
  with a documented fallback.

Design every feature below to work within these constraints, not around them.

## Tech Stack (unchanged core, extended scope)

- Laravel 12, PHP 8.3+ (confirm the cPanel account's MultiPHP Manager offers
  8.3+; if not, this must be flagged to the client before any code is written)
- MySQL (same database as Phase 1 — same app, same connection)
- Laravel Blade + Tailwind CSS + Alpine.js for the customer storefront (same
  stack as the Phase 1 admin panel — no separate frontend framework)
- Queue driver: `database` (not Redis) — jobs are processed by cron, not a daemon
- Cache/session driver: `file` or `database` (not Redis)
- Laravel Sanctum for customer API/token auth if a future mobile app is ever
  needed; session auth is enough for the Blade storefront itself

## cPanel Deployment Strategy (must be documented explicitly)

Pick and document one approach in the installation guide:
1. **Preferred:** point the subdomain/domain's document root directly at the
   Laravel `public/` folder (most cPanel hosts allow this in "Domains" settings).
2. **Fallback** (if the host only allows `public_html/` as document root): move
   the contents of `public/` into `public_html/`, and adjust the `index.php`
   `require` paths to point up one extra directory to `../bootstrap/app.php`
   and the vendor autoloader. Document this clearly since it's a common source
   of "white screen" deployment bugs.

Also document:
- Deployment method: cPanel Git Version Control feature (pull from the repo
  below) or manual zip upload — pick one and be consistent.
- `.env` must sit outside or alongside the app but never be web-accessible;
  verify via a direct URL request that `.env` returns 403/404.
- Run `composer install --no-dev --optimize-autoloader` on deploy (cPanel
  Terminal if available, otherwise Composer must be run locally and the
  `vendor/` folder uploaded).
- Verify `php artisan storage:link` works on the target host; if symlinks are
  blocked, fall back to serving files through a controller route instead.

## Repository

https://github.com/abaharloo4/karasu-cafe.git

## Features (same business scope as before, now Laravel-only)

**1. Customer Online Account** — phone number + OTP login via an Iranian SMS
gateway (provider not yet chosen — isolate behind a single `SmsGateway`
interface). Session-based auth (Laravel `customer` guard), not a separate
system. Dashboard: active subscription, remaining credits, expiration date,
usage history, order history — all rendered server-side with Blade, no SPA.

**2. Online Subscription Purchase** — customer selects a subscription plan on
a Blade storefront page → redirected to an Iranian payment gateway (Shaparak-
compliant, e.g. Zarinpal or IDPay — not yet chosen) → on the gateway's callback
route, verify the transaction server-side, then call the **same**
`SubscriptionService::createSubscription()` from Phase 1. No webhook from a
third-party CMS is needed — this is a direct payment-gateway callback handled
inside the same app.

**3. Subscription Free Order (Online)** — reuses
`SubscriptionService::consumeDailyItem()` exactly as Phase 1's in-person flow
does: same checks (active? already used today? credits remaining?), same
database-level unique constraint on `(subscription_id, used_date)`. Online and
in-person usage share one code path — there is no duplicate implementation to
keep in sync.

**4. Normal Online Orders** — customer without an eligible subscription pays
normally through the same payment gateway integration; `OrderService::createNormalOrder()`
is reused from Phase 1.

**5. Delivery Management (build only if the client actually offers delivery)**
— `delivery_zones`, `drivers`, `delivery_orders` tables and a driver Blade
panel with its own guard (phone + OTP, same SMS gateway). Status updates write
directly to the same database — no cross-system sync needed since it's one app.

**6. Customer Order Tracking** — Blade page showing order status progression
(Received → Preparing → Ready → [Out for delivery → Delivered, if delivery is
enabled). Since there's one database, this is a simple query, not a
synchronized read from a remote system — no polling-vs-webhook trade-off to
design around.

## Background Jobs via Cron (not queue workers)

Anything that would normally be a queued job (OTP expiry cleanup, subscription
expiration sweep, SMS sending) should be implemented as a scheduled Artisan
command, driven by a single cPanel Cron Job entry running
`php artisan schedule:run` every minute. Do not design anything that assumes a
persistent background worker process.

## Security

- Rate limit OTP endpoints aggressively (shared hosting has no fancy WAF —
  this is the main defense against SMS-bombing abuse).
- Payment gateway callback route must verify the transaction with the gateway's
  server-to-server verification API before trusting it — never trust the
  callback query string alone.
- Form Request validation on every write endpoint, same as Phase 1.

## Assumptions to confirm with the client before building

- **SMS/OTP provider**: not yet chosen (Mellipayamak, Kavenegar, FarazSMS, or
  similar).
- **Payment gateway**: not yet chosen (Zarinpal, IDPay, or similar Shaparak-
  compliant provider).
- **Delivery**: build only if the cafe actually offers delivery; otherwise skip
  Feature 5 entirely.
- **cPanel account details**: PHP version available, whether the document root
  can be pointed at `public/`, whether symlinks are permitted, whether a
  Terminal/SSH feature is available for running Composer and Artisan commands
  remotely (many cPanel plans do offer a web Terminal even without full SSH).

## Deliverables

1. Extended Laravel app (same repo/codebase as Phase 1) with customer auth,
   storefront, and account area
2. Payment gateway integration + verification flow
3. Delivery management (only if applicable)
4. Cron-driven scheduled commands replacing any queue-worker assumption
5. A written, cPanel-specific installation guide covering document root
   mapping, `.env` placement, Composer deployment, and the storage symlink
   fallback
6. Confirmation of the four open assumptions above before implementation begins
