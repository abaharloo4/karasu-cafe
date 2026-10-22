<?php

use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\CategoryController;
use App\Http\Controllers\Web\CustomerController;
use App\Http\Controllers\Web\DailyUsageController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\OrderController;
use App\Http\Controllers\Web\ProductController;
use App\Http\Controllers\Web\ReportController;
use App\Http\Controllers\Web\SubscriptionController;
use Illuminate\Support\Facades\Route;

// Unified Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');

// Storefront & Online Ordering Routes (Homepage is Menu)
Route::get('/', [\App\Http\Controllers\Web\StorefrontController::class, 'menu'])->name('storefront.menu');
Route::get('/menu', [\App\Http\Controllers\Web\StorefrontController::class, 'menu']);
Route::get('/menu/item/{product}', [\App\Http\Controllers\Web\StorefrontController::class, 'showItem'])->name('storefront.item');
Route::get('/cart', [\App\Http\Controllers\Web\CheckoutController::class, 'cart'])->name('cart.index');
Route::get('/cart/checkout', [\App\Http\Controllers\Web\CheckoutController::class, 'index'])->name('cart.checkout');
Route::redirect('/checkout', '/cart/checkout', 301);
Route::post('/cart/order', [\App\Http\Controllers\Web\CheckoutController::class, 'placeOrder'])
    ->middleware('throttle:15,1')
    ->name('cart.order');
Route::post('/cart/validate-coupon', [\App\Http\Controllers\Web\CheckoutController::class, 'validateCoupon'])
    ->middleware('throttle:30,1')
    ->name('cart.validate-coupon');
Route::get('/order-status/{order_number}', [\App\Http\Controllers\Web\CheckoutController::class, 'orderStatus'])
    ->middleware('throttle:30,1')
    ->name('storefront.order-status');
Route::get('/order-status/{order_number}/feed', [\App\Http\Controllers\Web\CheckoutController::class, 'orderStatusFeed'])
    ->middleware('throttle:60,1')
    ->name('storefront.order-status.feed');
Route::post('/order-status/{order_number}/reupload-receipt', [\App\Http\Controllers\Web\CheckoutController::class, 'reuploadReceipt'])
    ->middleware('throttle:10,1')
    ->name('storefront.order-status.reupload-receipt');
Route::get('/orders/{order}/receipt', [OrderController::class, 'printReceipt'])->name('orders.receipt.public');

// Dedicated Subscriptions Pages
Route::get('/subscriptions', [\App\Http\Controllers\Web\StorefrontController::class, 'subscriptions'])->name('storefront.subscriptions.index');
Route::get('/subscriptions/plan/{plan}', [\App\Http\Controllers\Web\StorefrontController::class, 'showSubscription'])->name('storefront.subscriptions.show');

// Dedicated About Us Page
Route::get('/about', [\App\Http\Controllers\Web\StorefrontController::class, 'about'])->name('storefront.about');
Route::get('/about-us', [\App\Http\Controllers\Web\StorefrontController::class, 'about']);

// Online Subscription Purchase Route
Route::match(['get', 'post'], '/subscriptions/purchase/{plan}', [\App\Http\Controllers\Web\OnlineSubscriptionController::class, 'purchase'])
    ->middleware('throttle:15,1')
    ->name('subscription.purchase');

// Payment Callback Routes
Route::get('/payment/zarinpal/callback', [\App\Http\Controllers\Web\PaymentController::class, 'zarinpalCallback'])
    ->middleware('throttle:15,1')
    ->name('payment.zarinpal.callback');

// Customer Portal Authentication Routes
Route::get('/customer/login', [AuthController::class, 'showLogin'])->name('customer.login');
Route::post('/customer/send-otp', [\App\Http\Controllers\Web\CustomerPanelController::class, 'sendOtp'])
    ->middleware('throttle:5,1')
    ->name('customer.send-otp');
Route::post('/customer/verify-otp', [\App\Http\Controllers\Web\CustomerPanelController::class, 'verifyOtp'])
    ->middleware('throttle:10,1')
    ->name('customer.verify-otp');
Route::post('/customer/login', [\App\Http\Controllers\Web\CustomerPanelController::class, 'verifyOtp'])->name('customer.login.submit');
Route::post('/customer/logout', [\App\Http\Controllers\Web\CustomerPanelController::class, 'logout'])->name('customer.logout');

// Customer Protected Portal Routes
Route::middleware('auth:customer')->prefix('panel')->name('customer.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Web\CustomerPanelController::class, 'dashboard'])->name('dashboard');
    Route::get('/orders', [\App\Http\Controllers\Web\CustomerPanelController::class, 'orders'])->name('orders');
    Route::get('/orders/{order}', [\App\Http\Controllers\Web\CustomerPanelController::class, 'showOrder'])->name('orders.show');
    Route::get('/subscriptions', [\App\Http\Controllers\Web\CustomerPanelController::class, 'subscriptions'])->name('subscriptions');
    Route::get('/club', [\App\Http\Controllers\Web\CustomerPanelController::class, 'club'])->name('club');
    Route::get('/addresses', [\App\Http\Controllers\Web\CustomerPanelController::class, 'addresses'])->name('addresses');
    Route::post('/addresses', [\App\Http\Controllers\Web\CustomerPanelController::class, 'storeAddress'])->name('addresses.store');
    Route::delete('/addresses/{address}', [\App\Http\Controllers\Web\CustomerPanelController::class, 'destroyAddress'])->name('addresses.destroy');
    Route::get('/profile', [\App\Http\Controllers\Web\CustomerPanelController::class, 'profile'])->name('profile');
    Route::post('/profile', [\App\Http\Controllers\Web\CustomerPanelController::class, 'updateProfile'])->name('profile.update');
    Route::get('/notifications', [\App\Http\Controllers\Web\CustomerPanelController::class, 'notifications'])->name('notifications');
    Route::post('/notifications', [\App\Http\Controllers\Web\CustomerPanelController::class, 'updateNotifications'])->name('notifications.update');
    Route::get('/favorites', [\App\Http\Controllers\Web\CustomerPanelController::class, 'favorites'])->name('favorites');
    Route::post('/favorites/toggle', [\App\Http\Controllers\Web\CustomerPanelController::class, 'toggleFavorite'])->name('favorites.toggle');
});

Route::post('/customer/favorites/toggle', [\App\Http\Controllers\Web\CustomerPanelController::class, 'toggleFavorite'])->name('customer.favorites.toggle');
Route::post('/customer/favorites/sync', [\App\Http\Controllers\Web\CustomerPanelController::class, 'syncFavorites'])->name('customer.favorites.sync');





// Authenticated Staff/Admin Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/admin', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // Core POS Daily Usage Flow
    Route::get('/admin/daily-usage', [DailyUsageController::class, 'index'])->name('daily-usage.index');
    Route::post('/admin/daily-usage', [DailyUsageController::class, 'store'])->name('daily-usage.store');
    Route::post('/admin/daily-usage/paid-order', [DailyUsageController::class, 'storePaidOrder'])->name('daily-usage.store-paid');

    // Customer Management
    Route::resource('admin/customers', CustomerController::class)->names('customers');
    Route::get('/customers', [CustomerController::class, 'index']);
    Route::get('/customers/create', [CustomerController::class, 'create']);
    Route::post('/customers', [CustomerController::class, 'store']);
    Route::get('/customers/{customer}', [CustomerController::class, 'show']);
    Route::get('/customers/{customer}/edit', [CustomerController::class, 'edit']);
    Route::put('/customers/{customer}', [CustomerController::class, 'update']);
    Route::delete('/customers/{customer}', [CustomerController::class, 'destroy']);

    // Subscriptions & Plans
    Route::get('/admin/subscriptions', [SubscriptionController::class, 'index'])->name('subscriptions.index');
    Route::get('/admin/subscriptions/plans/create', [SubscriptionController::class, 'createPlan'])->name('subscriptions.create-plan');
    Route::get('/subscriptions/plans/create', [SubscriptionController::class, 'createPlan']);
    Route::post('/admin/subscriptions/plans', [SubscriptionController::class, 'storePlan'])->name('subscriptions.store-plan');
    Route::post('/subscriptions/plans', [SubscriptionController::class, 'storePlan']);
    Route::get('/admin/subscriptions/plans/{plan}/edit', [SubscriptionController::class, 'editPlan'])->name('subscriptions.edit-plan');
    Route::get('/subscriptions/plans/{plan}/edit', [SubscriptionController::class, 'editPlan']);
    Route::put('/admin/subscriptions/plans/{plan}', [SubscriptionController::class, 'updatePlan'])->name('subscriptions.update-plan');
    Route::put('/subscriptions/plans/{plan}', [SubscriptionController::class, 'updatePlan']);
    Route::delete('/admin/subscriptions/plans/{plan}', [SubscriptionController::class, 'destroyPlan'])->name('subscriptions.destroy-plan');
    Route::delete('/subscriptions/plans/{plan}', [SubscriptionController::class, 'destroyPlan']);

    Route::get('/admin/subscriptions/assign', [SubscriptionController::class, 'assignForm'])->name('subscriptions.assign');
    Route::get('/subscriptions/assign', [SubscriptionController::class, 'assignForm']);
    Route::post('/admin/subscriptions/assign', [SubscriptionController::class, 'assignStore'])->name('subscriptions.assign.store');
    Route::post('/subscriptions/assign', [SubscriptionController::class, 'assignStore']);

    Route::get('/admin/subscriptions/{subscription}/edit', [SubscriptionController::class, 'edit'])->name('subscriptions.edit');
    Route::get('/subscriptions/{subscription}/edit', [SubscriptionController::class, 'edit']);
    Route::put('/admin/subscriptions/{subscription}', [SubscriptionController::class, 'update'])->name('subscriptions.update');
    Route::put('/subscriptions/{subscription}', [SubscriptionController::class, 'update']);
    Route::post('/admin/subscriptions/{subscription}/cancel', [SubscriptionController::class, 'cancel'])->name('subscriptions.cancel');
    Route::post('/subscriptions/{subscription}/cancel', [SubscriptionController::class, 'cancel']);
    Route::delete('/admin/subscriptions/{subscription}', [SubscriptionController::class, 'destroy'])->name('subscriptions.destroy');
    Route::delete('/subscriptions/{subscription}', [SubscriptionController::class, 'destroy']);
    Route::post('/admin/subscriptions/{subscription}/use', [SubscriptionController::class, 'recordUsage'])->name('subscriptions.use');
    Route::post('/subscriptions/{subscription}/use', [SubscriptionController::class, 'recordUsage']);

    // Loyalty Club Management
    Route::get('/admin/loyalty', [\App\Http\Controllers\Web\LoyaltyAdminController::class, 'index'])->name('admin.loyalty.index');
    Route::get('/admin/loyalty/tiers', [\App\Http\Controllers\Web\LoyaltyAdminController::class, 'tiers'])->name('admin.loyalty.tiers');
    Route::get('/admin/loyalty/tiers/create', [\App\Http\Controllers\Web\LoyaltyAdminController::class, 'createTier'])->name('admin.loyalty.tiers.create');
    Route::post('/admin/loyalty/tiers', [\App\Http\Controllers\Web\LoyaltyAdminController::class, 'storeTier'])->name('admin.loyalty.tiers.store');
    Route::get('/admin/loyalty/tiers/{tier}/edit', [\App\Http\Controllers\Web\LoyaltyAdminController::class, 'editTier'])->name('admin.loyalty.tiers.edit');
    Route::put('/admin/loyalty/tiers/{tier}', [\App\Http\Controllers\Web\LoyaltyAdminController::class, 'updateTier'])->name('admin.loyalty.tiers.update');
    Route::delete('/admin/loyalty/tiers/{tier}', [\App\Http\Controllers\Web\LoyaltyAdminController::class, 'deleteTier'])->name('admin.loyalty.tiers.delete');
    Route::get('/admin/loyalty/settings', [\App\Http\Controllers\Web\LoyaltyAdminController::class, 'settings'])->name('admin.loyalty.settings');
    Route::post('/admin/loyalty/settings', [\App\Http\Controllers\Web\LoyaltyAdminController::class, 'updateSettings'])->name('admin.loyalty.settings.update');
    Route::post('/loyalty/settings', [\App\Http\Controllers\Web\LoyaltyAdminController::class, 'updateSettings'])->name('loyalty.settings.update');

    // Coupons Management (WooCommerce Style)
    Route::get('/admin/coupons', [\App\Http\Controllers\Web\CouponAdminController::class, 'index'])->name('admin.coupons.index');
    Route::get('/admin/coupons/create', [\App\Http\Controllers\Web\CouponAdminController::class, 'create'])->name('admin.coupons.create');
    Route::post('/admin/coupons', [\App\Http\Controllers\Web\CouponAdminController::class, 'store'])->name('admin.coupons.store');
    Route::get('/admin/coupons/{coupon}/edit', [\App\Http\Controllers\Web\CouponAdminController::class, 'edit'])->name('admin.coupons.edit');
    Route::put('/admin/coupons/{coupon}', [\App\Http\Controllers\Web\CouponAdminController::class, 'update'])->name('admin.coupons.update');
    Route::post('/admin/coupons/{coupon}/toggle', [\App\Http\Controllers\Web\CouponAdminController::class, 'toggle'])->name('admin.coupons.toggle');
    Route::delete('/admin/coupons/{coupon}', [\App\Http\Controllers\Web\CouponAdminController::class, 'destroy'])->name('admin.coupons.destroy');

    // Categories Management
    Route::resource('admin/categories', CategoryController::class)->names('categories')->only(['index', 'store', 'update', 'destroy']);

    // Products Management
    Route::get('/admin/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/admin/products/create', [ProductController::class, 'create'])->name('products.create');
    Route::post('/admin/products', [ProductController::class, 'store'])->name('products.store');
    Route::get('/admin/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::put('/admin/products/{product}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/admin/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
    Route::delete('/products/{product}', [ProductController::class, 'destroy']);

    // Media Library Management
    Route::get('/admin/media', [\App\Http\Controllers\Web\MediaLibraryController::class, 'index'])->name('admin.media.index');
    Route::post('/admin/media', [\App\Http\Controllers\Web\MediaLibraryController::class, 'store'])->name('admin.media.store');
    Route::put('/admin/media/{mediaItem}', [\App\Http\Controllers\Web\MediaLibraryController::class, 'update'])->name('admin.media.update');
    Route::delete('/admin/media', [\App\Http\Controllers\Web\MediaLibraryController::class, 'destroy'])->name('admin.media.destroy');
    Route::get('/admin/media/list-json', [\App\Http\Controllers\Web\MediaLibraryController::class, 'listJson'])->name('admin.media.list-json');

    // Delivery Zones Management (Shiraz)
    Route::resource('/admin/delivery-zones', \App\Http\Controllers\Web\DeliveryZoneController::class)
        ->only(['index', 'store', 'update', 'destroy'])
        ->names('admin.delivery-zones');





    // Orders Management
    Route::get('/admin/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/admin/orders/create', [OrderController::class, 'create'])->name('orders.create');
    Route::post('/admin/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/admin/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::get('/orders/{order}', [OrderController::class, 'show']);
    Route::get('/admin/orders/{order}/receipt', [OrderController::class, 'printReceipt'])->name('orders.receipt');
    Route::get('/orders/{order}/receipt', [OrderController::class, 'printReceipt']);

    // Live Kitchen & Bar Orders Board (Phase 2)
    Route::get('/admin/live-orders', [\App\Http\Controllers\Web\LiveOrderController::class, 'index'])->name('admin.live-orders.index');
    Route::get('/orders/live', [\App\Http\Controllers\Web\LiveOrderController::class, 'index'])->name('orders.live');
    Route::get('/admin/live-orders/feed', [\App\Http\Controllers\Web\LiveOrderController::class, 'pollFeed'])->name('admin.live-orders.feed');
    Route::post('/admin/live-orders/{order}/status', [\App\Http\Controllers\Web\LiveOrderController::class, 'updateStatus'])->name('admin.live-orders.update-status');
    Route::post('/admin/live-orders/payment/{payment}/verify', [\App\Http\Controllers\Web\LiveOrderController::class, 'verifyCardPayment'])->name('admin.live-orders.verify-payment');
    Route::get('/admin/payments/{payment}/receipt/{filename?}', [\App\Http\Controllers\Web\PaymentController::class, 'showReceipt'])->where('filename', '.*')->name('admin.payments.receipt');

    // Alias for daily usage
    Route::get('/daily-usage', [DailyUsageController::class, 'index']);

    // Admin Only: Financial & Usage Reports and Settings
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports', [ReportController::class, 'index']);
        Route::get('/admin/reports/export/orders', [ReportController::class, 'exportOrders'])->name('reports.export.orders');
        Route::get('/reports/export/orders', [ReportController::class, 'exportOrders']);
        Route::get('/admin/reports/export/products', [ReportController::class, 'exportProducts'])->name('reports.export.products');
        Route::get('/reports/export/products', [ReportController::class, 'exportProducts']);
        Route::get('/admin/reports/export/customers', [ReportController::class, 'exportCustomers'])->name('reports.export.customers');
        Route::get('/reports/export/customers', [ReportController::class, 'exportCustomers']);

        // SMS Settings & Melli Payamak Patterns
        Route::get('/admin/settings/sms', [\App\Http\Controllers\Web\SmsSettingController::class, 'edit'])->name('settings.sms.edit');
        Route::get('/settings/sms', [\App\Http\Controllers\Web\SmsSettingController::class, 'edit']);
        Route::put('/admin/settings/sms', [\App\Http\Controllers\Web\SmsSettingController::class, 'update'])->name('settings.sms.update');
        Route::put('/settings/sms', [\App\Http\Controllers\Web\SmsSettingController::class, 'update']);
        Route::post('/admin/settings/sms/test', [\App\Http\Controllers\Web\SmsSettingController::class, 'testSend'])->name('settings.sms.test');

        // Payment Settings (Zarinpal & Card to Card)
        Route::get('/admin/settings/payment', [\App\Http\Controllers\Web\PaymentSettingController::class, 'edit'])->name('settings.payment.edit');
        Route::get('/settings/payment', [\App\Http\Controllers\Web\PaymentSettingController::class, 'edit']);
        Route::post('/admin/settings/payment', [\App\Http\Controllers\Web\PaymentSettingController::class, 'update'])->name('settings.payment.update');
        Route::post('/settings/payment', [\App\Http\Controllers\Web\PaymentSettingController::class, 'update']);

        // Fulfillment & Delivery Methods Settings
        Route::get('/admin/settings/fulfillment', [\App\Http\Controllers\Web\FulfillmentSettingController::class, 'edit'])->name('settings.fulfillment.edit');
        Route::get('/settings/fulfillment', [\App\Http\Controllers\Web\FulfillmentSettingController::class, 'edit']);
        Route::post('/admin/settings/fulfillment', [\App\Http\Controllers\Web\FulfillmentSettingController::class, 'update'])->name('settings.fulfillment.update');
        Route::post('/settings/fulfillment', [\App\Http\Controllers\Web\FulfillmentSettingController::class, 'update']);

        // Thermal Receipt & Invoice Designer Settings
        Route::get('/admin/settings/receipt', [\App\Http\Controllers\Web\ReceiptSettingController::class, 'edit'])->name('settings.receipt.edit');
        Route::get('/settings/receipt', [\App\Http\Controllers\Web\ReceiptSettingController::class, 'edit']);
        Route::post('/admin/settings/receipt', [\App\Http\Controllers\Web\ReceiptSettingController::class, 'update'])->name('settings.receipt.update');
        Route::post('/settings/receipt', [\App\Http\Controllers\Web\ReceiptSettingController::class, 'update']);

        // Cafe Shift & Operating Hours Settings
        Route::get('/admin/settings/cafe', [\App\Http\Controllers\Web\CafeSettingController::class, 'edit'])->name('settings.cafe.edit');
        Route::get('/settings/cafe', [\App\Http\Controllers\Web\CafeSettingController::class, 'edit']);
        Route::put('/admin/settings/cafe', [\App\Http\Controllers\Web\CafeSettingController::class, 'update'])->name('settings.cafe.update');
        Route::put('/settings/cafe', [\App\Http\Controllers\Web\CafeSettingController::class, 'update']);

        // Site Content CMS & Menu Image Dimensions Settings
        Route::get('/admin/settings/site-content', [\App\Http\Controllers\Web\SiteContentSettingController::class, 'edit'])->name('settings.site-content.edit');
        Route::get('/settings/site-content', [\App\Http\Controllers\Web\SiteContentSettingController::class, 'edit']);
        Route::post('/admin/settings/site-content', [\App\Http\Controllers\Web\SiteContentSettingController::class, 'update'])->name('settings.site-content.update');
        Route::post('/settings/site-content', [\App\Http\Controllers\Web\SiteContentSettingController::class, 'update']);

        // Safe Git Deployments & Update Manager (cPanel UAPI / Zero-Downtime)
        Route::get('/admin/deployments', [\App\Http\Controllers\Web\DeploymentController::class, 'index'])->name('admin.deployments.index');
        Route::post('/admin/deployments/check', [\App\Http\Controllers\Web\DeploymentController::class, 'check'])->name('admin.deployments.check');
        Route::post('/admin/deployments/trigger', [\App\Http\Controllers\Web\DeploymentController::class, 'trigger'])->name('admin.deployments.trigger');
        Route::get('/admin/deployments/{deployment}/status', [\App\Http\Controllers\Web\DeploymentController::class, 'status'])->name('admin.deployments.status');

        // System Staff & Admin Users Management (SEC-02 defense)
        Route::resource('admin/users', \App\Http\Controllers\Web\UserController::class)
            ->except(['show'])
            ->names('admin.users');

        // PWA Settings
        Route::get('/admin/pwa-settings', [\App\Http\Controllers\Web\PwaAdminController::class, 'settings'])->name('admin.pwa-settings');
        Route::post('/admin/pwa-settings', [\App\Http\Controllers\Web\PwaAdminController::class, 'updateSettings'])->name('admin.pwa-settings.update');
        Route::post('/admin/pwa-settings/test-push', [\App\Http\Controllers\Web\PwaAdminController::class, 'testPush'])->name('admin.pwa-settings.test-push');
    });
});

// PWA Manifest, Push Subscriptions, and Analytics Telemetry
Route::get('/manifest.webmanifest', [\App\Http\Controllers\Web\PwaController::class, 'manifest'])->name('pwa.manifest');
Route::get('/pwa/manifest/{panel}', [\App\Http\Controllers\Web\PwaController::class, 'manifest'])->name('pwa.manifest.panel');
Route::get('/pwa/vapid-public-key', [\App\Http\Controllers\Web\PwaController::class, 'vapidPublicKey'])->name('pwa.vapid-public-key');
Route::post('/pwa/subscribe', [\App\Http\Controllers\Web\PwaController::class, 'subscribe'])->name('pwa.subscribe');
Route::post('/pwa/unsubscribe', [\App\Http\Controllers\Web\PwaController::class, 'unsubscribe'])->name('pwa.unsubscribe');
Route::post('/pwa/event', [\App\Http\Controllers\Web\PwaController::class, 'logEvent'])->name('pwa.event');


// Internal Web OPcache Reset Hook (Protected by Secret Key, outside auth for deploy.sh curl)
Route::get('/system/opcache-clear', [\App\Http\Controllers\Web\DeploymentController::class, 'opcacheClear'])->name('system.opcache-clear');

// Direct & Fallback Storage Asset Server for shared hosting (cPanel when symlink is missing or blocked)
Route::get('/storage/{path}', function (string $path) {
    $path = ltrim(str_replace(['..', '\\'], ['', '/'], $path), '/');

    $publicPath = public_path('storage/' . $path);
    if (file_exists($publicPath) && is_file($publicPath)) {
        return response()->file($publicPath);
    }

    $storagePath = storage_path('app/public/' . $path);
    if (file_exists($storagePath) && is_file($storagePath)) {
        try {
            $destDir = dirname($publicPath);
            if (!is_dir($destDir)) {
                @mkdir($destDir, 0755, true);
            }
            if (!is_link(public_path('storage')) && !file_exists($publicPath)) {
                @copy($storagePath, $publicPath);
            }
        } catch (\Throwable $e) {}

        return response()->file($storagePath);
    }

    abort(404);
})->where('path', '.*')->name('storage.fallback');

// Production ready


