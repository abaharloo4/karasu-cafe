<?php

namespace Tests\Feature;

use App\Models\Cafe;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\SubscriptionUsage;
use App\Models\User;
use App\Services\SubscriptionService;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class SubscriptionServiceTest extends TestCase
{
    use RefreshDatabase;

    protected SubscriptionService $subscriptionService;
    protected Cafe $cafe;
    protected User $staff;
    protected Customer $customer;
    protected Product $allowedProduct;
    protected Product $disallowedProduct;
    protected SubscriptionPlan $plan;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subscriptionService = new SubscriptionService();

        $this->cafe = Cafe::create([
            'name' => 'تست کافه',
            'phone' => '02111111111',
        ]);

        $this->staff = User::create([
            'cafe_id' => $this->cafe->id,
            'name' => 'پرسنل تست',
            'email' => 'staff@test.local',
            'password' => bcrypt('password'),
            'role' => 'staff',
        ]);

        $this->customer = Customer::create([
            'cafe_id' => $this->cafe->id,
            'name' => 'مشتری تست',
            'phone' => '09121111111',
        ]);

        $this->allowedProduct = Product::create([
            'cafe_id' => $this->cafe->id,
            'name' => 'اسپرسو مجاز',
            'type' => 'coffee',
            'price' => 50000,
        ]);

        $this->disallowedProduct = Product::create([
            'cafe_id' => $this->cafe->id,
            'name' => 'کیک غیرمجاز',
            'type' => 'pastry',
            'price' => 100000,
        ]);

        $this->plan = SubscriptionPlan::create([
            'cafe_id' => $this->cafe->id,
            'name' => 'پلن تست ۱۰۰ روزه',
            'total_items' => 100,
            'duration_days' => 100,
            'price' => 5000000,
        ]);

        $this->plan->products()->attach($this->allowedProduct->id);
    }

    public function test_can_consume_daily_free_item_successfully(): void
    {
        $subscription = $this->subscriptionService->createSubscription(
            $this->cafe->id,
            $this->customer->id,
            $this->plan->id
        );

        $this->assertEquals(100, $subscription->remaining_items);

        $result = $this->subscriptionService->consumeDailyItem(
            $this->cafe->id,
            $subscription->id,
            $this->allowedProduct->id,
            $this->staff->id
        );

        $this->assertTrue($result['success']);
        $this->assertEquals(99, $subscription->fresh()->remaining_items);

        $this->assertDatabaseHas('subscription_usages', [
            'subscription_id' => $subscription->id,
            'product_id' => $this->allowedProduct->id,
        ]);

        $this->assertDatabaseHas('orders', [
            'customer_id' => $this->customer->id,
            'type' => 'subscription',
            'total_price' => 0,
        ]);
    }

    public function test_cannot_consume_twice_in_same_day(): void
    {
        $subscription = $this->subscriptionService->createSubscription(
            $this->cafe->id,
            $this->customer->id,
            $this->plan->id
        );

        // First redemption succeeds
        $this->subscriptionService->consumeDailyItem(
            $this->cafe->id,
            $subscription->id,
            $this->allowedProduct->id,
            $this->staff->id
        );

        // Second redemption attempt in the same day must fail
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('امروز قبلاً از سهمیه رایگان روزانه استفاده کرده است.');

        $this->subscriptionService->consumeDailyItem(
            $this->cafe->id,
            $subscription->id,
            $this->allowedProduct->id,
            $this->staff->id
        );
    }

    public function test_cannot_consume_disallowed_product(): void
    {
        $subscription = $this->subscriptionService->createSubscription(
            $this->cafe->id,
            $this->customer->id,
            $this->plan->id
        );

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('محصول انتخاب شده جزو لیست محصولات مجاز این پلن اشتراک نیست.');

        $this->subscriptionService->consumeDailyItem(
            $this->cafe->id,
            $subscription->id,
            $this->disallowedProduct->id,
            $this->staff->id
        );
    }

    public function test_database_composite_unique_constraint_prevents_duplicate_usage(): void
    {
        $subscription = $this->subscriptionService->createSubscription(
            $this->cafe->id,
            $this->customer->id,
            $this->plan->id
        );

        // Insert first usage record directly
        SubscriptionUsage::create([
            'cafe_id' => $this->cafe->id,
            'customer_id' => $this->customer->id,
            'subscription_id' => $subscription->id,
            'product_id' => $this->allowedProduct->id,
            'used_date' => Carbon::today()->toDateString(),
            'created_by' => $this->staff->id,
        ]);

        // Attempt second direct insert with same subscription_id and used_date must trigger DB exception
        $this->expectException(\Illuminate\Database\QueryException::class);

        SubscriptionUsage::create([
            'cafe_id' => $this->cafe->id,
            'customer_id' => $this->customer->id,
            'subscription_id' => $subscription->id,
            'product_id' => $this->allowedProduct->id,
            'used_date' => Carbon::today()->toDateString(),
            'created_by' => $this->staff->id,
        ]);
    }

    public function test_missed_days_automatically_forfeit_credits(): void
    {
        $subscription = $this->subscriptionService->createSubscription(
            $this->cafe->id,
            $this->customer->id,
            $this->plan->id
        );

        // Manually simulate 10 days passing (start_date: 10 days ago, expire_date: 90 days from today)
        $subscription->update([
            'start_date' => Carbon::today()->subDays(10)->toDateString(),
            'expire_date' => Carbon::today()->addDays(89)->toDateString(),
            'remaining_items' => 100, // DB still says 100
        ]);

        // Effective remaining items must now be capped at 90 days remaining!
        $effective = $subscription->syncMissedDaysCredit();
        $this->assertEquals(90, $effective);
        $this->assertEquals(90, $subscription->fresh()->remaining_items);
    }
}
