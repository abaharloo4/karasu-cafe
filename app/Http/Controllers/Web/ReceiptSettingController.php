<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Cafe;
use App\Models\Order;
use App\Models\ReceiptSetting;
use Illuminate\Http\Request;

class ReceiptSettingController extends Controller
{
    /**
     * Show the Receipt / Invoice Settings & Live Designer page.
     */
    public function edit()
    {
        $cafeId = auth()->user()->cafe_id ?? 1;
        $setting = ReceiptSetting::getForCafe($cafeId);
        $cafe = Cafe::find($cafeId) ?? Cafe::first();

        // Sample order for live preview in the designer
        $sampleOrder = Order::where('cafe_id', $cafeId)
            ->with(['items.product', 'customer', 'creator', 'deliveryAddress'])
            ->latest()
            ->first();

        return view('settings.receipt', compact('setting', 'cafe', 'sampleOrder'));
    }

    /**
     * Update Receipt / Invoice configuration.
     */
    public function update(Request $request)
    {
        $cafeId = auth()->user()->cafe_id ?? 1;
        $setting = ReceiptSetting::getForCafe($cafeId);

        $validated = $request->validate([
            'paper_width' => 'required|in:80mm,58mm',
            'font_family' => 'required|in:vazirmatn,shabnam,iransans,b_titr,courier',
            'font_size' => 'required|in:compact,medium,large',
            'header_title' => 'required|string|max:150',
            'header_subtitle' => 'nullable|string|max:200',
            'invoice_title' => 'required|string|max:100',
            'show_logo' => 'nullable|boolean',
            'logo_url' => 'nullable|string|max:255',
            'show_order_number' => 'nullable|boolean',
            'show_date_time' => 'nullable|boolean',
            'show_cashier_name' => 'nullable|boolean',
            'show_customer_name' => 'nullable|boolean',
            'show_customer_phone' => 'nullable|boolean',
            'show_customer_club_info' => 'nullable|boolean',
            'show_order_type' => 'nullable|boolean',
            'show_delivery_address' => 'nullable|boolean',
            'show_item_row_number' => 'nullable|boolean',
            'show_item_unit_price' => 'nullable|boolean',
            'show_item_notes' => 'nullable|boolean',
            'show_subtotal' => 'nullable|boolean',
            'show_discount' => 'nullable|boolean',
            'show_delivery_fee' => 'nullable|boolean',
            'show_tax' => 'nullable|boolean',
            'tax_percent' => 'nullable|numeric|min:0|max:100',
            'show_amount_in_words' => 'nullable|boolean',
            'show_payment_method' => 'nullable|boolean',
            'show_payment_status' => 'nullable|boolean',
            'show_barcode' => 'nullable|boolean',
            'show_qr_code' => 'nullable|boolean',
            'qr_code_type' => 'nullable|in:order_url,instagram,website,custom',
            'custom_qr_url' => 'nullable|string|max:255',
            'footer_message' => 'nullable|string|max:1000',
            'show_wifi' => 'nullable|boolean',
            'wifi_ssid' => 'nullable|string|max:100',
            'wifi_password' => 'nullable|string|max:100',
            'show_instagram' => 'nullable|boolean',
            'instagram_handle' => 'nullable|string|max:100',
            'auto_print' => 'nullable|boolean',
        ]);

        // Convert checkboxes to boolean
        $booleanFields = [
            'show_logo', 'show_order_number', 'show_date_time', 'show_cashier_name',
            'show_customer_name', 'show_customer_phone', 'show_customer_club_info',
            'show_order_type', 'show_delivery_address', 'show_item_row_number',
            'show_item_unit_price', 'show_item_notes', 'show_subtotal',
            'show_discount', 'show_delivery_fee', 'show_tax', 'show_amount_in_words',
            'show_payment_method', 'show_payment_status', 'show_barcode',
            'show_qr_code', 'show_wifi', 'show_instagram', 'auto_print',
        ];

        foreach ($booleanFields as $field) {
            $validated[$field] = $request->boolean($field);
        }

        $validated['tax_percent'] = (float) ($request->input('tax_percent', 0));

        $setting->update($validated);

        return redirect()->route('settings.receipt.edit')
            ->with('success', 'تنظیمات و قالب چاپ فاکتور حرارتی با موفقیت ذخیره گردید.');
    }
}
