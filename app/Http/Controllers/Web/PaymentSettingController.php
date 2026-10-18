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
            'zarinpal_status' => 'nullable|in:active,inactive,coming_soon',
            'zarinpal_is_active' => 'nullable',
            'zarinpal_coming_soon_note' => 'nullable|string|max:255',
            'zarinpal_merchant_id' => 'nullable|string|max:100',
            'zarinpal_sandbox' => 'nullable|boolean',
            'zarinpal_currency' => 'nullable|in:IRT,IRR',

            'card_to_card_status' => 'nullable|in:active,inactive,coming_soon',
            'card_to_card_is_active' => 'nullable',
            'card_to_card_coming_soon_note' => 'nullable|string|max:255',
            'card_bank_name' => 'nullable|string|max:100',
            'card_number' => 'nullable|string|max:30',
            'card_sheba_number' => 'nullable|string|max:40',
            'card_account_owner' => 'nullable|string|max:100',
            'card_instructions' => 'nullable|string',

            'cash_status' => 'nullable|in:active,inactive,coming_soon',
            'cash_is_active' => 'nullable',
            'cash_coming_soon_note' => 'nullable|string|max:255',
            'cash_instructions' => 'nullable|string',
        ]);

        // Update Zarinpal
        $zarinpalStatus = $request->input('zarinpal_status') ?? ($request->boolean('zarinpal_is_active') ? 'active' : 'inactive');
        $zarinpal = PaymentGateway::firstOrCreate(
            ['key' => 'zarinpal'],
            ['title' => 'پرداخت اینترنتی زرین‌پال']
        );
        $zarinpal->update([
            'status' => $zarinpalStatus,
            'coming_soon_note' => $request->input('zarinpal_coming_soon_note'),
            'is_active' => ($zarinpalStatus === 'active'),
            'settings' => [
                'merchant_id' => $request->input('zarinpal_merchant_id', ''),
                'sandbox' => $request->boolean('zarinpal_sandbox'),
                'currency' => $request->input('zarinpal_currency', 'IRT'),
                'description' => 'پرداخت آنلاین سفارش کافه بی',
            ],
        ]);

        // Update Card to Card
        $cardStatus = $request->input('card_to_card_status') ?? ($request->boolean('card_to_card_is_active') ? 'active' : 'inactive');
        $cardToCard = PaymentGateway::firstOrCreate(
            ['key' => 'card_to_card'],
            ['title' => 'انتقال کارت به کارت']
        );
        $cardToCard->update([
            'status' => $cardStatus,
            'coming_soon_note' => $request->input('card_to_card_coming_soon_note'),
            'is_active' => ($cardStatus === 'active'),
            'settings' => [
                'bank_name' => $request->input('card_bank_name', ''),
                'card_number' => $request->input('card_number', ''),
                'sheba_number' => $request->input('card_sheba_number', ''),
                'account_owner' => $request->input('card_account_owner', ''),
                'instructions' => $request->input('card_instructions', ''),
            ],
        ]);

        // Update Cash on Delivery / POS
        $cashStatus = $request->input('cash_status') ?? ($request->boolean('cash_is_active') ? 'active' : 'inactive');
        $cash = PaymentGateway::firstOrCreate(
            ['key' => 'cash'],
            ['title' => 'پرداخت در محل / کارتخوان سیار و حضوری']
        );
        $cash->update([
            'status' => $cashStatus,
            'coming_soon_note' => $request->input('cash_coming_soon_note'),
            'is_active' => ($cashStatus === 'active'),
            'settings' => [
                'instructions' => $request->input('cash_instructions', ''),
            ],
        ]);

        return redirect()->route('settings.payment.edit')->with('success', 'تنظیمات روش‌های پرداخت با موفقیت به‌روزرسانی شد.');
    }
}
