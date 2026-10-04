<?php

namespace Tests\Feature;

use App\Models\Cafe;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

class ProductCardComponentTest extends TestCase
{
    use RefreshDatabase;

    protected Cafe $cafe;
    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cafe = Cafe::create([
            'name' => 'بی کافه تست',
            'slug' => 'b-cafe-test',
            'phone' => '07136280000',
            'address' => 'شیراز، خیابان ارم',
            'status' => 'active',
        ]);

        $this->category = Category::create([
            'cafe_id' => $this->cafe->id,
            'name' => 'قهوه اسپرسو',
            'slug' => 'espresso',
            'status' => 'active',
        ]);
    }

    protected function renderCard(mixed $product, array $props = []): string
    {
        $propsString = '';
        foreach ($props as $key => $val) {
            if (is_bool($val)) {
                $propsString .= ' :' . $key . '="' . ($val ? 'true' : 'false') . '"';
            } else {
                $propsString .= ' ' . $key . '="' . e($val) . '"';
            }
        }

        return Blade::render('<x-product-card :product="$product"' . $propsString . ' />', [
            'product' => $product,
        ]);
    }

    public function test_renders_normal_product_successfully(): void
    {
        $product = Product::create([
            'cafe_id' => $this->cafe->id,
            'category_id' => $this->category->id,
            'name' => 'لاته کلاسیک',
            'price' => 75000,
            'status' => 'active',
        ]);

        $html = $this->renderCard($product);

        $this->assertStringContainsString('لاته کلاسیک', $html);
        $this->assertStringContainsString('قهوه اسپرسو', $html);
        $this->assertStringContainsString('75,000', $html);
        $this->assertStringContainsString('تومان', $html);
        $this->assertStringContainsString('افزودن به سبد خرید', $html);
        $this->assertStringContainsString('aria-label="افزودن لاته کلاسیک به علاقه‌مندی‌ها"', $html);
    }

    public function test_renders_discounted_product_with_badge_and_strikethrough_price(): void
    {
        $product = Product::create([
            'cafe_id' => $this->cafe->id,
            'category_id' => $this->category->id,
            'name' => 'آمریکانو اسپشیال',
            'price' => 60000,
            'discount_percent' => 20,
            'is_hourly_discount_active' => true,
            'status' => 'active',
        ]);

        $html = $this->renderCard($product);

        $this->assertStringContainsString('آمریکانو اسپشیال', $html);
        $this->assertStringContainsString('20% تخفیف', $html);
        $this->assertStringContainsString('60,000', $html);
        $this->assertStringContainsString('48,000', $html);
    }

    public function test_renders_product_without_discount_cleanly(): void
    {
        $product = [
            'id' => 101,
            'name' => 'چای ماسالا',
            'price' => 50000,
            'original_price' => null,
            'category' => 'دمنوش و چای',
        ];

        $html = $this->renderCard($product);

        $this->assertStringContainsString('چای ماسالا', $html);
        $this->assertStringContainsString('50,000', $html);
        $this->assertStringNotContainsString('% تخفیف', $html);
    }

    public function test_renders_single_image_without_gallery_thumbnails(): void
    {
        $product = [
            'id' => 102,
            'name' => 'اسپرسو دوبل',
            'price' => 45000,
            'image' => '/images/espresso.jpg',
        ];

        $html = $this->renderCard($product);

        $this->assertStringContainsString('اسپرسو دوبل', $html);
        $this->assertStringNotContainsString('role="tablist"', $html);
    }

    public function test_renders_multiple_images_gallery_with_overflow_badge(): void
    {
        $product = [
            'id' => 103,
            'name' => 'قهوه تخصصی اتیوپی',
            'price' => 120000,
            'image' => '/images/ethiopia-1.jpg',
            'images' => [
                '/images/ethiopia-1.jpg',
                '/images/ethiopia-2.jpg',
                '/images/ethiopia-3.jpg',
                '/images/ethiopia-4.jpg',
                '/images/ethiopia-5.jpg',
                '/images/ethiopia-6.jpg',
            ],
        ];

        $html = $this->renderCard($product);

        $this->assertStringContainsString('role="tablist"', $html);
        $this->assertStringContainsString('نمایش تصویر شماره 1', $html);
        $this->assertStringContainsString('نمایش تصویر شماره 2', $html);
        $this->assertStringContainsString('+2', $html);
    }

    public function test_renders_long_product_title_without_layout_breakage(): void
    {
        $longName = 'قهوه دمی تخصصی اتیوپی یرگاچف شسته شده با یادداشت‌های طعمی ترنج و هلو به همراه کوکی دست‌ساز';
        $product = [
            'id' => 104,
            'name' => $longName,
            'price' => 180000,
        ];

        $html = $this->renderCard($product);

        $this->assertStringContainsString($longName, $html);
        $this->assertStringContainsString('line-clamp-1', $html);
    }

    public function test_renders_out_of_stock_state(): void
    {
        $product = [
            'id' => 105,
            'name' => 'چیزکیک سن سباستین',
            'price' => 95000,
            'stock' => 0,
        ];

        $html = $this->renderCard($product);

        $this->assertStringContainsString('ناموجود', $html);
        $this->assertStringContainsString('cursor-not-allowed', $html);
        $this->assertStringNotContainsString('افزودن به سبد خرید', $html);
    }

    public function test_renders_compact_horizontal_variant(): void
    {
        $product = [
            'id' => 106,
            'name' => 'کرواسان شکلاتی',
            'price' => 55000,
            'category' => 'شیرینی و بیکری',
        ];

        $html = $this->renderCard($product, ['compact' => true]);

        $this->assertStringContainsString('کرواسان شکلاتی', $html);
        $this->assertStringContainsString('شیرینی و بیکری', $html);
        $this->assertStringContainsString('55,000', $html);
        $this->assertStringContainsString('flex-row items-center gap-3', $html);
    }

    public function test_renders_dark_variant(): void
    {
        $product = [
            'id' => 107,
            'name' => 'موکا شکلات تلخ',
            'price' => 80000,
        ];

        $html = $this->renderCard($product, ['variant' => 'dark']);

        $this->assertStringContainsString('موکا شکلات تلخ', $html);
        $this->assertStringContainsString('bg-[#19242B]', $html);
        $this->assertStringContainsString('dark', $html);
    }

    public function test_gracefully_handles_missing_optional_fields(): void
    {
        $product = [
            'id' => 108,
            'name' => 'آب معدنی',
            'price' => 15000,
        ];

        $html = $this->renderCard($product);

        $this->assertStringContainsString('آب معدنی', $html);
        $this->assertStringContainsString('15,000', $html);
        $this->assertStringContainsString('افزودن به سبد خرید', $html);
    }

    public function test_respects_accessibility_and_screen_reader_attributes(): void
    {
        $product = [
            'id' => 109,
            'name' => 'کاپوچینو وانیل',
            'price' => 70000,
        ];

        $html = $this->renderCard($product);

        $this->assertStringContainsString('aria-label="کاهش تعداد"', $html);
        $this->assertStringContainsString('aria-label="افزایش تعداد"', $html);
        $this->assertStringContainsString('aria-label="افزودن کاپوچینو وانیل به علاقه‌مندی‌ها"', $html);
    }
}
