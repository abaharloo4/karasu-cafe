<?php

namespace Tests\Unit;

use App\Helpers\JalaliHelper;
use DateTime;
use PHPUnit\Framework\TestCase;

class JalaliHelperTest extends TestCase
{
    public function test_it_formats_empty_dates_as_dash(): void
    {
        $this->assertEquals('-', JalaliHelper::format(null));
        $this->assertEquals('-', JalaliHelper::format(''));
    }

    public function test_it_converts_known_gregorian_date_to_jalali(): void
    {
        // 2026-03-21 is 1405-01-01 (Nowruz)
        $date = new DateTime('2026-03-21 12:00:00');
        $formatted = JalaliHelper::format($date, 'Y/m/d');

        $this->assertStringStartsWith('1405/01/01', $formatted);
    }

    public function test_gregorian_to_jalali_algorithm(): void
    {
        // 2024-03-20 -> [1403, 1, 1]
        $jalali = JalaliHelper::gregorianToJalali(2024, 3, 20);
        $this->assertEquals([1403, 1, 1], $jalali);

        // 2025-03-21 -> [1404, 1, 1]
        $jalali2025 = JalaliHelper::gregorianToJalali(2025, 3, 21);
        $this->assertEquals([1404, 1, 1], $jalali2025);
    }

    public function test_jalali_to_gregorian_algorithm(): void
    {
        // 1403/01/01 -> [2024, 3, 20]
        $gregorian = JalaliHelper::jalaliToGregorian(1403, 1, 1);
        $this->assertEquals([2024, 3, 20], $gregorian);
    }
}
