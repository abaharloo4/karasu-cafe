<?php

namespace Database\Seeders;

use App\Models\PaymentGateway;
use Illuminate\Database\Seeder;

class PaymentGatewaySeeder extends Seeder
{
    public function run(): void
    {
        $gateways = [
            [
                'key' => 'zarinpal',
                'title' => 'پرداخت اینترنتی زرین‌پال',
                'is_active' => true,
                'settings' => [
                    'merchant_id' => '00000000-0000-0000-0000-000000000000',
                    'sandbox' => true,
                    'currency' => 'IRT', // IRT = Toman, IRR = Rial
                    'description' => 'پرداخت آنلاین سفارش کافه بی (شیراز)',
                ],
            ],
            [
                'key' => 'card_to_card',
                'title' => 'انتقال کارت به کارت',
                'is_active' => true,
                'settings' => [
                    'bank_name' => 'بانک سامان',
                    'card_number' => '۶۲۱۹۸۶۱۰۰۰۰۰۰۰۰۰',
                    'sheba_number' => 'IR000560000000000000000000',
                    'account_owner' => 'کافه بی (B Cafe)',
                    'instructions' => 'لطفاً پس از واریز به شماره کارت فوق، عکس فیش یا کد رهگیری را در فرم زیر وارد نمایید.',
                ],
            ],
        ];

        foreach ($gateways as $gw) {
            PaymentGateway::updateOrCreate(
                ['key' => $gw['key']],
                $gw
            );
        }
    }
}
