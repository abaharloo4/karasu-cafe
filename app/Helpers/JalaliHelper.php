<?php

namespace App\Helpers;

use DateTime;
use Exception;
use Morilog\Jalali\Jalalian;

class JalaliHelper
{
    /**
     * Convert any DateTime/string date to Shamsi / Jalali format with Asia/Tehran timezone.
     */
    public static function format($date, string $format = 'Y/m/d - H:i'): string
    {
        if (empty($date)) {
            return '-';
        }

        try {
            if (!$date instanceof DateTime) {
                $date = new DateTime($date);
            }

            $date->setTimezone(new \DateTimeZone('Asia/Tehran'));

            if (class_exists(Jalalian::class)) {
                return Jalalian::fromDateTime($date)->format($format);
            }

            return self::manualFormat($date, $format);
        } catch (Exception $e) {
            return (string) $date;
        }
    }

    private static function manualFormat(DateTime $date, string $format): string
    {
        $g_y = (int) $date->format('Y');
        $g_m = (int) $date->format('m');
        $g_d = (int) $date->format('d');

        $j = self::gregorianToJalali($g_y, $g_m, $g_d);

        $year = sprintf('%04d', $j[0]);
        $month = sprintf('%02d', $j[1]);
        $day = sprintf('%02d', $j[2]);
        $hour = $date->format('H');
        $minute = $date->format('i');

        if ($format === 'Y/m/d') {
            return "{$year}/{$month}/{$day}";
        }

        return "{$year}/{$month}/{$day} - {$hour}:{$minute}";
    }

    public static function gregorianToJalali($g_y, $g_m, $g_d): array
    {
        $g_days_in_month = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
        $j_days_in_month = [31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29];

        $gy = $g_y - 1600;
        $gm = $g_m - 1;
        $gd = $g_d - 1;

        $g_day_no = 365 * $gy + intdiv($gy + 3, 4) - intdiv($gy + 99, 100) + intdiv($gy + 399, 400);

        for ($i = 0; $i < $gm; ++$i) {
            $g_day_no += $g_days_in_month[$i];
        }

        if ($gm > 1 && (($gy % 4 == 0 && $gy % 100 != 0) || ($gy % 400 == 0))) {
            ++$g_day_no;
        }

        $g_day_no += $gd;
        $j_day_no = $g_day_no - 79;
        $j_np = intdiv($j_day_no, 12053);
        $j_day_no %= 12053;

        $jy = 979 + 33 * $j_np + 4 * intdiv($j_day_no, 1461);
        $j_day_no %= 1461;

        if ($j_day_no >= 366) {
            $jy += intdiv($j_day_no - 1, 365);
            $j_day_no = ($j_day_no - 1) % 365;
        }

        for ($i = 0; $i < 11 && $j_day_no >= $j_days_in_month[$i]; ++$i) {
            $j_day_no -= $j_days_in_month[$i];
        }

        $jm = $i + 1;
        $jd = $j_day_no + 1;

        return [$jy, $jm, $jd];
    }

    /**
     * Convert Jalali (Shamsi) date to Gregorian.
     */
    public static function jalaliToGregorian($jy, $jm, $jd): array
    {
        if (class_exists(Jalalian::class)) {
            $gregorian = Jalalian::fromFormat('Y/m/d', sprintf('%04d/%02d/%02d', $jy, $jm, $jd))->toCarbon();
            return [(int) $gregorian->format('Y'), (int) $gregorian->format('m'), (int) $gregorian->format('d')];
        }

        $jy -= 979;
        $jm -= 1;
        $jd -= 1;

        $j_day_no = 365 * $jy + intdiv($jy, 33) * 8 + intdiv(($jy % 33) + 3, 4);

        for ($i = 0; $i < $jm; ++$i) {
            $j_day_no += ($i < 6) ? 31 : 30;
        }

        $j_day_no += $jd;

        $g_day_no = $j_day_no + 79;

        $gy = 1600 + 400 * intdiv($g_day_no, 146097);
        $g_day_no %= 146097;

        if ($g_day_no >= 36525) {
            $g_day_no--;
            $gy += 100 * intdiv($g_day_no, 36524);
            $g_day_no %= 36524;

            if ($g_day_no >= 365) {
                $g_day_no++;
            }
        }

        $gy += 4 * intdiv($g_day_no, 1461);
        $g_day_no %= 1461;

        if ($g_day_no >= 366) {
            $gy += intdiv($g_day_no - 1, 365);
            $g_day_no = ($g_day_no - 1) % 365;
        }

        $g_days_in_month = [31, 28 + (($gy % 4 == 0 && $gy % 100 != 0) || ($gy % 400 == 0) ? 1 : 0), 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];

        $gm = 0;
        for ($gm = 0; $gm < 11 && $g_day_no >= $g_days_in_month[$gm]; ++$gm) {
            $g_day_no -= $g_days_in_month[$gm];
        }

        return [$gy, $gm + 1, $g_day_no + 1];
    }
}
