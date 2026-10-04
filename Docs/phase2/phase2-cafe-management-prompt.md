# Phase 2 — Online Ordering Platform for B Cafe (All-Laravel, cPanel Deployment)

You are a senior Laravel architect and full-stack engineer.

Repository: https://github.com/abaharloo4/karasu-cafe.git
Hosting: **cPanel shared hosting** (already purchased and ready). No VPS, no
Docker, no Supervisor, no Redis by default — every technical decision below
respects that.

## Before writing any code

The Phase 1 codebase already contains **partial, broken implementations** of
several Phase 2 features (customer OTP auth, loyalty points, coupons, lucky
wheel) that were built without authorization and shipped with serious bugs:
- The OTP login returns a fake token (`'mock_token_' . random string`) that is
  never stored as a real Sanctum token — the login flow does not actually work.
- `/api/loyalty/redeem-coupon`, `/spin-wheel`, and `/points` have **no
  authentication at all** — any caller can pass any `customer_id` and act on
  behalf of any customer (IDOR).
- No rate limiting exists on `send-otp`, `verify-otp`, or the staff login form.
- A `RoleMiddleware` class exists but is never registered in `bootstrap/app.php`
  or applied to any route — `ReportController` and the loyalty web controller
  have no role/authorization check at all, so any staff account can view
  financial reports or manage coupons.

**Do not build Phase 2 on top of this broken code as-is.** First audit and fix
these four issues (real Sanctum tokens, auth + ownership checks on every
loyalty endpoint, rate limiting on auth endpoints, and actually registering
`RoleMiddleware` and applying it to every admin-only route) before adding any
new feature. Treat this as Task 0, not optional cleanup.

## What Phase 2 actually is

A dedicated online ordering platform for B Cafe (بی کافه) — conceptually like
Snapp Food's ordering UX, but a single-restaurant, in-house system, not a
multi-vendor marketplace. Same Laravel codebase and database as Phase 1 — one
app, one source of truth, no WordPress, no second system.

## 1. Online Menu

Public-facing (no login required to browse) menu built from the existing
`products` table (add `category`, `description`, `image`, `is_available` if
not already present). Categorized display, Persian RTL, mobile-first (most
food-ordering traffic is on phones).

## 2. Cart & Two Order Modes

Customer builds a cart, then chooses one of two fulfillment modes:

**Pickup (حضوری):** customer will collect the order at the cafe. No address or
delivery fee needed. On submit, the order is created with status `pending` and
immediately visible on the admin panel; **admin manually starts fulfillment**
(status → `preparing`) — the customer does not need to be present or confirmed
at pickup time, the order simply queues for staff like an internal order does
today.

**Delivery (ارسال با پیک):** customer must provide a delivery location. This
is validated against real delivery zones before checkout can proceed (see
Neshan Maps integration below). If the address falls outside every active
zone, block checkout with a clear "خارج از محدوده ارسال" message — do not
silently accept an unservable order.

## 3. Delivery Zones via Neshan Maps (Shiraz service area)

Replace the earlier flat "0–3km / 3–7km" text-based zone idea with **real
polygon zones drawn on a map**, since delivery coverage in a real city is
never a clean circle.

- **Admin side:** a page using the Neshan Maps JavaScript SDK where the admin
  draws one or more polygons directly on a map of Shiraz. Each polygon is
  saved as GeoJSON on `delivery_zones` (add a `boundary` JSON column; keep
  `name`, `delivery_price`, `status` from the existing schema). Multiple zones
  can have different delivery prices.
- **Customer side:** at checkout, the customer either searches their address
  (Neshan geocoding/autocomplete API) or drops a pin on a Neshan map. The
  resulting lat/lng is sent to the backend.
- **Backend:** run a point-in-polygon check against all active zones for this
  cafe. If the point matches a zone, attach that zone's `delivery_price` to
  the order. If it matches no zone, reject with the out-of-range message
  above — this check must happen server-side, never trust a zone id the
  client claims to be in.
- Requires a Neshan API key — get this from the client/admin before building
  (same "confirm before implementing" discipline as the SMS/payment providers).

## 4. Order Fulfillment Workflow (Admin-Driven)

Order status state machine (shared by both pickup and delivery orders):

```
pending → preparing → ready → [pickup: completed | delivery: out_for_delivery → delivered]
                                                              (cancelled at any point before ready)
```

New online orders land in the existing admin order screen (extend, don't
duplicate, the Phase 1 internal-orders UI) with a visibly distinct "online"
badge. Admin explicitly transitions status — nothing auto-advances except the
payment-confirmation step described below. Delivery orders, once `ready`,
follow the driver-assignment flow already specced for the delivery panel
(Phase 1 doc's delivery tables — `drivers`, `delivery_orders`).

## 5. Payment — Two Methods, Both Admin-Configurable

Build a `payment_settings` (or similar) admin panel section where the owner
can independently enable/disable each method and edit its configuration
without a code deploy:

**Zarinpal (automatic):** standard redirect-to-gateway flow. On return,
verify the transaction server-side with Zarinpal's verification API before
marking the order paid — never trust the callback query string alone. Store
the merchant ID in the admin settings (encrypted at rest), not hardcoded.

**Card-to-card (manual):** admin enters one or more destination card numbers
in settings; these are shown to the customer at checkout. After transferring,
the customer uploads a receipt image and/or a tracking reference. The order
enters a `awaiting_payment_verification` status — it does **not** move to
`preparing` until an admin manually approves the payment in the admin panel.
This is a manual trust step by design; do not try to auto-verify card-to-card
transfers.

The order total shown to the customer, and the amount actually charged/verified,
must always be recalculated server-side from the cart + delivery zone price —
never accept a client-submitted total.

## 6. Customer Account Panel

Full account area behind the (now properly fixed) OTP login:
- Profile: name, phone, saved addresses (reusable at checkout, tied to the
  Neshan-validated zone)
- Order history: every past order, its items, status, and payment method
- Active subscription info (reused from Phase 1 — same `SubscriptionService`)
- Loyalty status (see below)

## 7. Loyalty Club

Extends (after the security fixes in Task 0) the existing loyalty tables
rather than rebuilding them:
- **Birthday discount:** capture customer birthday (store as a Shamsi/Jalali
  date, consistent with the rest of the admin panel's calendar) at signup or
  from the account panel. A scheduled command (cron-driven, see Phase 1's
  cPanel scheduling approach) generates a time-limited discount code for each
  customer whose birthday is today (or the coming few days) and, if an SMS
  gateway is confirmed for marketing use, notifies them.
- **Spend-based discount tiers:** track cumulative completed-order spend per
  customer; when a threshold is crossed, issue a discount coupon automatically
  (e.g. via a `LoyaltyService::evaluateSpendTiers()` call after each completed
  order — reuse the existing `LoyaltyService`, fix its gaps, don't rewrite it).
- Every coupon-redeem action must check that the coupon belongs to the
  authenticated customer making the request — this is exactly the IDOR fixed
  in Task 0; do not reintroduce it here.

## 8. Security Requirements (non-negotiable, re-stated from Task 0)

- Every customer-facing API route that reads or writes customer-specific data
  requires real Sanctum authentication tied to that specific customer — never
  a `customer_id` taken from the request body.
- Rate limit: login, OTP send/verify, and payment-related endpoints.
- Payment amounts and delivery-zone prices are always computed server-side.
- `RoleMiddleware` is registered and applied to every admin-only route
  (reports, settings, coupon management) — verified by manually testing as a
  `staff` account that each of these returns 403.

## Deliverables

1. Task 0: security fixes to the existing OTP/loyalty code (real tokens, auth
   + ownership checks, rate limiting, RoleMiddleware wired up)
2. Public online menu
3. Cart + pickup/delivery order flow with the status state machine above
4. Neshan Maps integration: admin zone-drawing UI + customer address picker +
   server-side point-in-polygon validation
5. Zarinpal integration (server-verified) + card-to-card manual-verification
   flow, both toggleable and configurable from the admin panel
6. Customer account panel (profile, addresses, order history, loyalty status)
7. Loyalty club: Shamsi-date birthday discount codes (cron-driven) + spend-tier
   coupons, built on the existing (now-fixed) loyalty tables
8. Extended admin order screen showing online orders alongside internal ones
9. Updated cPanel installation guide covering the Neshan API key and Zarinpal
   merchant credentials as additional required environment configuration

## Open items to confirm before implementation

- Neshan Maps API key (not yet provided)
- Zarinpal merchant ID (not yet provided)
- Exact card-to-card destination card number(s) the admin wants preloaded
- Whether birthday-discount SMS notifications are in scope for Phase 2 or a
  later addition (the discount code itself is in scope either way — only the
  proactive SMS ping is in question)
