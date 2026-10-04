<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\PaymentGateway;
use Illuminate\Http\Request;

class PaymentSettingController extends Controller
{
    public function edit()
    {
        $zarinpal = PaymentGateway::firstOrCreate(
            ['key' => 'zarinpal'],
            [
                'title' => 'پرداخت اینترنتی زرین‌پال',
                'is_active' => true,
                'settings' => [
                    'merchant_id' => '',
                    'sandbox' => true,
                    'currency' => 'IRT',
                    'description' => 'پرداخت سفارش آنلاین کافه بی',
                ],
            ]
        );

        $cardToCard = PaymentGateway::firstOrCreate(
            ['key' => 'card_to_card'],
            [
                'title' => 'انتقال کارت به کارت',
                'is_active' => true,
                'settings' => [
                    'bank_name' => '',
                    'card_number' => '',
                    'sheba_number' => '',
                    'account_owner' => '',
                    'instructions' => '',
                ],
            ]
        );

        $cash = PaymentGateway::firstOrCreate(
            ['key' => 'cash'],
            [
                'title' => 'پرداخت در محل / کارتخوان سیار و حضوری',
                'is_active' => true,
                'settings' => [
                    'instructions' => 'امکان پرداخت نقدی یا از طریق دستگاه کارتخوان سیار هنگام تحویل سفارش در شیراز یا روی کانتر کافه.',
                ],
            ]
        );

        return view('settings.payment', compact('zarinpal', 'cardToCard', 'cash'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'zarinpal_is_active' => 'nullable|boolean',
            'zarinpal_merchant_id' => 'nullable|string|max:100',
            'zarinpal_sandbox' => 'nullable|boolean',
            'zarinpal_currency' => 'required|in:IRT,IRR',
            'card_to_card_is_active' => 'nullable|boolean',
            'card_bank_name' => 'nullable|string|max:100',
            'card_number' => 'nullable|string|max:30',
            'card_sheba_number' => 'nullable|string|max:40',
            'card_account_owner' => 'nullable|string|max:100',
            'card_instructions' => 'nullable|string',
            'cash_is_active' => 'nullable|boolean',
            'cash_instructions' => 'nullable|string',
        ]);

        // Update Zarinpal
        $zarinpal = PaymentGateway::firstOrCreate(
            ['key' => 'zarinpal'],
            ['title' => 'پرداخت اینترنتی زرین‌پال']
        );
        $zarinpal->update([
            'is_active' => $request->boolean('zarinpal_is_active'),
            'settings' => [
                'merchant_id' => $request->input('zarinpal_merchant_id', ''),
                'sandbox' => $request->boolean('zarinpal_sandbox'),
                'currency' => $request->input('zarinpal_currency', 'IRT'),
                'description' => 'پرداخت آنلاین سفارش کافه بی',
            ],
        ]);

        // Update Card to Card
        $cardToCard = PaymentGateway::firstOrCreate(
            ['key' => 'card_to_card'],
            ['title' => 'انتقال کارت به کارت']
        );
        $cardToCard->update([
            'is_active' => $request->boolean('card_to_card_is_active'),
            'settings' => [
                'bank_name' => $request->input('card_bank_name', ''),
                'card_number' => $request->input('card_number', ''),
                'sheba_number' => $request->input('card_sheba_number', ''),
                'account_owner' => $request->input('card_account_owner', ''),
                'instructions' => $request->input('card_instructions', ''),
            ],
        ]);

        // Update Cash on Delivery / POS
        $cash = PaymentGateway::firstOrCreate(
            ['key' => 'cash'],
            ['title' => 'پرداخت در محل / کارتخوان سیار و حضوری']
        );
        $cash->update([
            'is_active' => $request->boolean('cash_is_active'),
            'settings' => [
                'instructions' => $request->input('cash_instructions', ''),
            ],
        ]);

        return redirect()->route('settings.payment.edit')->with('success', 'تنظیمات روش‌های پرداخت با موفقیت به‌روزرسانی شد.');
    }
}
