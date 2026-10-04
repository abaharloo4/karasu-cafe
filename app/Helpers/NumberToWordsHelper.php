<?php

namespace App\Helpers;

class NumberToWordsHelper
{
    private static array $ones = [
        0 => 'صفر', 1 => 'یک', 2 => 'دو', 3 => 'سه', 4 => 'چهار',
        5 => 'پنج', 6 => 'شش', 7 => 'هفت', 8 => 'هشت', 9 => 'نه',
        10 => 'ده', 11 => 'یازده', 12 => 'دوازده', 13 => 'سیزده', 14 => 'چهارده',
        15 => 'پانزده', 16 => 'شانزده', 17 => 'هفده', 18 => 'هجده', 19 => 'نوزده',
    ];

    private static array $tens = [
        2 => 'بیست', 3 => 'سی', 4 => 'چهل', 5 => 'پنجاه',
        6 => 'شصت', 7 => 'هفتاد', 8 => 'هشتاد', 9 => 'نود',
    ];

    private static array $hundreds = [
        1 => 'صد', 2 => 'دویست', 3 => 'سیصد', 4 => 'چهارصد', 5 => 'پانصد',
        6 => 'ششصد', 7 => 'هفتصد', 8 => 'هشتصد', 9 => 'نهصد',
    ];

    private static array $levels = ['', 'هزار', 'میلیون', 'میلیارد', 'تریلیون'];

    public static function toWords(int|float $number): string
    {
        $number = (int) round($number);
        if ($number === 0) {
            return self::$ones[0];
        }

        if ($number < 0) {
            return 'منفی ' . self::toWords(abs($number));
        }

        $parts = [];
        $level = 0;

        while ($number > 0) {
            $chunk = $number % 1000;
            if ($chunk > 0) {
                $chunkWords = self::chunkToWords($chunk);
                if ($level > 0) {
                    $chunkWords .= ' ' . self::$levels[$level];
                }
                array_unshift($parts, $chunkWords);
            }
            $number = (int) ($number / 1000);
            $level++;
        }

        return implode(' و ', $parts);
    }

    private static function chunkToWords(int $num): string
    {
        $res = [];
        $h = (int) ($num / 100);
        $rem = $num % 100;

        if ($h > 0) {
            $res[] = self::$hundreds[$h];
        }

        if ($rem > 0) {
            if ($rem < 20) {
                $res[] = self::$ones[$rem];
            } else {
                $t = (int) ($rem / 10);
                $o = $rem % 10;
                $res[] = self::$tens[$t];
                if ($o > 0) {
                    $res[] = self::$ones[$o];
                }
            }
        }

        return implode(' و ', $res);
    }
}
