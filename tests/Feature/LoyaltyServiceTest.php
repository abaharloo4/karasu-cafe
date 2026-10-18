<?php

namespace Tests\Feature;

use App\Models\Cafe;
use App\Models\Coupon;
use App\Models\Customer;
use App\Services\LoyaltyService;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoyaltyServiceTest extends TestCase
{
    use RefreshDatabase;

    protected Cafe $cafe;
    protected Customer $customer;
    protected LoyaltyService $loyaltyService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cafe = Cafe::create([
            'name' => 'تست کافه کراسو',
            'phone' => '02111111111',
        ]);

        $this->customer = Customer::create([
            'cafe_id' => $this->cafe->id,
            'name' => 'مشتری تست وفاداری',
            'phone' => '09123333333',
        ]);

        $this->loyaltyService = app(LoyaltyService::class);
    }

    public function test_can_add_and_calculate_loyalty_points(): void
    {
        $this->loyaltyService->addPoints($this->cafe->id, $this->customer->id, 50, 'earned', 'امتیاز خرید اول');
        $this->loyaltyService->addPoints($this->cafe->id, $this->customer->id, 30, 'bonus', 'امتیاز هدیه');

        $totalPoints = $this->loyaltyService->getTotalPoints($this->customer->id);

        $this->assertEquals(80, $totalPoints);
    }

    public function test_can_redeem_coupon_with_points(): void
    {
        // Give 100 points
        $this->loyaltyService->addPoints($this->cafe->id, $this->customer->id, 100, 'bonus', 'اعتبار اول');

        $coupon = Coupon::create([
            'cafe_id' => $this->cafe->id,
            'code' => 'DISCOUNT50',
            'title' => 'کارت تخفیف ۵۰ هزار تومانی',
            'discount_type' => 'fixed',
            'discount_value' => 50000,
            'points_required' => 60,
            'status' => 'active',
        ]);

        $redeemed = $this->loyaltyService->redeemCoupon($this->customer->id, $coupon->id);

        $this->assertNotNull($redeemed);
        $this->assertEquals(40, $this->loyaltyService->getTotalPoints($this->customer->id));
        $this->assertDatabaseHas('customer_coupons', [
            'customer_id' => $this->customer->id,
            'coupon_id' => $coupon->id,
        ]);
    }
}
