<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Cafe;
use Illuminate\Http\Request;

class CafeSettingController extends Controller
{
    public function edit()
    {
        $cafe = Cafe::firstOrCreate(
            ['id' => 1],
            [
                'name' => 'بی کافه (B Cafe)',
                'phone' => '07136360000',
                'address' => 'شیراز، بلوار ستارخان',
                'order_start_time' => '08:00',
                'order_end_time' => '22:00',
                'is_ordering_open' => true,
            ]
        );

        return view('settings.cafe', compact('cafe'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'order_start_time' => ['required', 'regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/'],
            'order_end_time' => ['required', 'regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/'],
            'is_ordering_open' => ['nullable', 'boolean'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:500'],
            'closed_message' => ['nullable', 'string', 'max:500'],
        ], [
            'order_start_time.required' => 'ساعت شروع سفارش‌گیری الزامی است.',
            'order_start_time.regex' => 'فرمت ساعت شروع نامعتبر است (مثال: 08:00).',
            'order_end_time.required' => 'ساعت پایان سفارش‌گیری الزامی است.',
            'order_end_time.regex' => 'فرمت ساعت پایان نامعتبر است (مثال: 22:00).',
        ]);

        $cafe = Cafe::firstOrCreate(['id' => 1]);
        $cafe->update([
            'order_start_time' => $validated['order_start_time'],
            'order_end_time' => $validated['order_end_time'],
            'is_ordering_open' => $request->boolean('is_ordering_open'),
            'phone' => $validated['phone'] ?? $cafe->phone,
            'address' => $validated['address'] ?? $cafe->address,
            'closed_message' => $validated['closed_message'] ?? null,
        ]);

        return redirect()->route('settings.cafe.edit')
            ->with('success', 'تنظیمات ساعات کاری و شیفت سفارش‌گیری با موفقیت ذخیره گردید.');
    }
}
