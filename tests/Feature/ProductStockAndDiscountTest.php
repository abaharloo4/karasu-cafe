<?php

namespace Tests\Feature;

use App\Models\Cafe;
use App\Models\Category;
use App\Models\Customer;
use App\Models\DeliveryZone;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductStockAndDiscountTest extends TestCase
{
    use RefreshDatabase;

    protected Cafe $cafe;
    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cafe = Cafe::create([
            'name' => 'بی کافه',
            'slug' => 'bcafe',
            'is_open' => true,
            'status' => 'open',
            'address' => 'شیراز، خیابان ارم',
            'phone' => '09170000000',
        ]);

        $this->category = Category::create([
            'cafe_id' => $this->cafe->id,
            'name' => 'قهوه تخصصی',
            'slug' => 'specialty-coffee',
        ]);
    }

    public function test_product_stock_helpers(): void
    {
        $unlimited = Product::create([
            'cafe_id' => $this->cafe->id,
            'category_id' => $this->category->id,
            'name' => 'کورتادو نامحدود',
            'type' => 'hot',
            'price' => 70000,
            'stock' => null,
            'status' => 'active',
        ]);

        $this->assertTrue($unlimited->isInStock());
        $this->assertFalse($unlimited->isOutOfStock());

        $inStock = Product::create([
            'cafe_id' => $this->cafe->id,
            'category_id' => $this->category->id,
            'name' => 'چیزکیک محدود',
            'type' => 'cake',
            'price' => 85000,
            'stock' => 3,
            'status' => 'active',
        ]);

        $this->assertTrue($inStock->isInStock());
        $this->assertFalse($inStock->isOutOfStock());

        $outOfStock = Product::create([
            'cafe_id' => $this->cafe->id,
            'category_id' => $this->category->id,
            'name' => 'کوکی تمام شده',
            'type' => 'cake',
            'price' => 40000,
            'stock' => 0,
            'status' => 'active',
        ]);

        $this->assertFalse($outOfStock->isInStock());
        $this->assertTrue($outOfStock->isOutOfStock());
    }

    public function test_discount_all_day_and_hourly(): void
    {
        // 1. All-day discount (is_hourly_discount_active = true, start/end = null)
        $allDayProduct = Product::create([
            'cafe_id' => $this->cafe->id,
            'category_id' => $this->category->id,
            'name' => 'قهوه با تخفیف تمام‌وقت',
            'type' => 'hot',
            'price' => 100000,
            'is_hourly_discount_active' => true,
            'discount_percent' => 20,
            'discount_start_time' => null,
            'discount_end_time' => null,
            'status' => 'active',
        ]);

        $this->assertTrue($allDayProduct->isHourlyDiscountActive());
        $this->assertEquals(80000, $allDayProduct->effective_price);

        // 2. Window discount covering 00:00 to 23:59
        $wideProduct = Product::create([
            'cafe_id' => $this->cafe->id,
            'category_id' => $this->category->id,
            'name' => 'تخفیف ۲۴ ساعته بازه کامل',
            'type' => 'hot',
            'price' => 100000,
            'is_hourly_discount_active' => true,
            'discount_percent' => 15,
            'discount_start_time' => '00:00',
            'discount_end_time' => '23:59',
            'status' => 'active',
        ]);

        $this->assertTrue($wideProduct->isHourlyDiscountActive());
        $this->assertEquals(85000, $wideProduct->effective_price);
        $this->assertNotNull($wideProduct->discountRemainingSeconds());
    }

    public function test_checkout_blocks_overselling_and_decrements_stock(): void
    {
        $product = Product::create([
            'cafe_id' => $this->cafe->id,
            'category_id' => $this->category->id,
            'name' => 'کوکی دست‌ساز',
            'type' => 'cake',
            'price' => 50000,
            'stock' => 2,
            'status' => 'active',
        ]);

        $customer = Customer::create([
            'cafe_id' => $this->cafe->id,
            'phone' => '09171234567',
            'name' => 'رضا رضایی',
        ]);

        // Attempting to buy 3 while only 2 in stock should fail
        $response = $this->actingAs($customer, 'customer')
            ->post(route('cart.order'), [
                'customer_name' => 'رضا رضایی',
                'customer_phone' => '09171234567',
                'order_type' => 'takeaway',
                'fulfillment_type' => 'pickup',
                'payment_method' => 'cash',
                'items' => [
                    [
                        'type' => 'product',
                        'product_id' => $product->id,
                        'quantity' => 3,
                    ],
                ],
            ]);

        $response->assertSessionHas('error');
        $this->assertEquals(2, $product->fresh()->stock);

        // Buying 2 should succeed and decrement stock to 0
        $responseSuccess = $this->actingAs($customer, 'customer')
            ->post(route('cart.order'), [
                'customer_name' => 'رضا رضایی',
                'customer_phone' => '09171234567',
                'order_type' => 'takeaway',
                'fulfillment_type' => 'pickup',
                'payment_method' => 'cash',
                'items' => [
                    [
                        'type' => 'product',
                        'product_id' => $product->id,
                        'quantity' => 2,
                    ],
                ],
            ]);

        $this->assertEquals(0, $product->fresh()->stock);
        $this->assertTrue($product->fresh()->isOutOfStock());
    }
}
