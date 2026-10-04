<?php

namespace Tests\Feature;

use App\Models\Cafe;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerFavoritesTest extends TestCase
{
    use RefreshDatabase;

    protected Cafe $cafe;
    protected Customer $customer;
    protected Category $category;
    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cafe = Cafe::create([
            'name' => 'بی کافه شیراز',
            'status' => 'open',
            'opening_time' => '07:00',
            'closing_time' => '23:59',
        ]);

        $this->customer = Customer::create([
            'cafe_id' => $this->cafe->id,
            'name' => 'امیر رضایی',
            'phone' => '09171234567',
        ]);

        $this->category = Category::create([
            'cafe_id' => $this->cafe->id,
            'name' => 'قهوه تخصصی',
            'status' => 'active',
        ]);

        $this->product = Product::create([
            'cafe_id' => $this->cafe->id,
            'category_id' => $this->category->id,
            'name' => 'کورتادو',
            'price' => 65000,
            'status' => 'active',
        ]);
    }

    public function test_customer_can_toggle_favorite_via_ajax(): void
    {
        // 1. Add to favorites
        $response = $this->actingAs($this->customer, 'customer')
            ->postJson(route('customer.favorites.toggle'), [
                'product_id' => $this->product->id,
            ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'is_favorite' => true,
                'product_id' => $this->product->id,
            ]);

        $this->assertDatabaseHas('customer_favorites', [
            'customer_id' => $this->customer->id,
            'product_id' => $this->product->id,
        ]);

        // 2. Remove from favorites
        $response2 = $this->actingAs($this->customer, 'customer')
            ->postJson(route('customer.favorites.toggle'), [
                'product_id' => $this->product->id,
            ]);

        $response2->assertOk()
            ->assertJson([
                'success' => true,
                'is_favorite' => false,
            ]);

        $this->assertDatabaseMissing('customer_favorites', [
            'customer_id' => $this->customer->id,
            'product_id' => $this->product->id,
        ]);
    }

    public function test_customer_can_view_favorites_page(): void
    {
        $this->customer->favorites()->attach($this->product->id);

        $response = $this->actingAs($this->customer, 'customer')
            ->get(route('customer.favorites'));

        $response->assertOk();
        $response->assertSee('محصولات مورد علاقه من');
        $response->assertSee('کورتادو');
    }

    public function test_customer_dashboard_displays_favorite_products(): void
    {
        $this->customer->favorites()->attach($this->product->id);

        $response = $this->actingAs($this->customer, 'customer')
            ->get(route('customer.dashboard'));

        $response->assertOk();
        $response->assertSee('محصولات مورد علاقه شما');
        $response->assertSee('کورتادو');
    }
}
