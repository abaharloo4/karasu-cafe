<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\FulfillmentSetting;
use Illuminate\Http\Request;

class FulfillmentSettingController extends Controller
{
    public function edit()
    {
        $cafeId = auth()->user()->cafe_id ?? 1;
        $methods = FulfillmentSetting::getForCafe($cafeId);

        return view('settings.fulfillment', compact('methods'));
    }

    public function update(Request $request)
    {
        $cafeId = auth()->user()->cafe_id ?? 1;

        $request->validate([
            'methods' => 'required|array',
            'methods.*.title' => 'required|string|max:100',
            'methods.*.subtitle' => 'nullable|string|max:255',
            'methods.*.description' => 'nullable|string|max:1000',
            'methods.*.status' => 'required|in:active,inactive,coming_soon',
            'methods.*.coming_soon_note' => 'nullable|string|max:255',
            'methods.*.base_fee' => 'nullable|numeric|min:0',
            'methods.*.min_order_amount' => 'nullable|numeric|min:0',
        ]);

        foreach ($request->input('methods') as $key => $data) {
            FulfillmentSetting::updateOrCreate(
                ['cafe_id' => $cafeId, 'key' => $key],
                [
                    'title' => $data['title'],
                    'subtitle' => $data['subtitle'] ?? null,
                    'description' => $data['description'] ?? null,
                    'status' => $data['status'],
                    'coming_soon_note' => $data['coming_soon_note'] ?? null,
                    'base_fee' => $data['base_fee'] ?? 0,
                    'min_order_amount' => $data['min_order_amount'] ?? 0,
                ]
            );
        }

        return redirect()->route('settings.fulfillment.edit')->with('success', 'تنظیمات روش‌های تحویل و ارسال با موفقیت ذخیره شد.');
    }
}
