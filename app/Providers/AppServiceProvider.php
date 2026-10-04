<?php

namespace App\Providers;

use App\Models\Product;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Policies\ProductPolicy;
use App\Policies\SubscriptionPlanPolicy;
use App\Policies\SubscriptionPolicy;
use App\Services\Sms\FarazSmsGateway;
use App\Services\Sms\KavenegarSmsGateway;
use App\Services\Sms\LogSmsGateway;
use App\Services\Sms\MellipayamakSmsGateway;
use App\Services\Sms\SmsGatewayInterface;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(SmsGatewayInterface::class, function ($app) {
            $cafeId = auth()->check() ? auth()->user()->cafe_id : 1;
            $setting = \App\Models\SmsSetting::where('cafe_id', $cafeId)->first();
            $driver = $setting->driver ?? config('services.sms.driver', env('SMS_DRIVER', 'mellipayamak'));

            return match ($driver) {
                'mellipayamak' => new MellipayamakSmsGateway($cafeId),
                'kavenegar' => new KavenegarSmsGateway(),
                'farazsms' => new FarazSmsGateway(),
                default => new LogSmsGateway(),
            };
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);

        Gate::policy(SubscriptionPlan::class, SubscriptionPlanPolicy::class);
        Gate::policy(Subscription::class, SubscriptionPolicy::class);
        Gate::policy(Product::class, ProductPolicy::class);
    }
}
