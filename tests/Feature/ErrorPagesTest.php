<?php

namespace Tests\Feature;

use Tests\TestCase;

class ErrorPagesTest extends TestCase
{
    public function test_404_page_renders_custom_coffee_error_view(): void
    {
        $response = $this->get('/a-completely-non-existent-url-404-check');

        $response->assertStatus(404);
        $response->assertSee('این فنجان قهوه در منو پیدا نشد!');
        $response->assertSee('۴۰۴');
        $response->assertSee('بی کافه (B Cafe)');
        $response->assertSee('صفحه اصلی و منوی کافه');
    }

    public function test_403_view_renders_properly(): void
    {
        $view = $this->view('errors.403');

        $view->assertSee('ورود به این بخش کافه مجاز نیست!');
        $view->assertSee('۴۰۳');
        $view->assertSee('بی کافه | دسترسی غیرمجاز');
    }

    public function test_500_view_renders_properly(): void
    {
        $view = $this->view('errors.500');

        $view->assertSee('دستگاه اسپرسوساز سرور کمی داغ کرده!');
        $view->assertSee('۵۰۰');
        $view->assertSee('تلاش مجدد (Reload)');
    }

    public function test_419_view_renders_properly(): void
    {
        $view = $this->view('errors.419');

        $view->assertSee('قهوه کمی سرد شد! نشست شما منقضی شده است');
        $view->assertSee('۴۱۹');
        $view->assertSee('بارگذاری مجدد صفحه');
    }

    public function test_429_view_renders_properly(): void
    {
        $view = $this->view('errors.429');

        $view->assertSee('باریستا چند ثانیه به استراحت نیاز دارد!');
        $view->assertSee('۴۲۹');
    }

    public function test_503_view_renders_properly(): void
    {
        $view = $this->view('errors.503');

        $view->assertSee('کافه موقتاً در حال استراحت و آماده‌سازی است');
        $view->assertSee('۵۰۳');
    }
}
