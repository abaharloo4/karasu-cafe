<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @php
        $setting = $setting ?? \App\Models\ReceiptSetting::getForCafe($order->cafe_id ?? 1);
        $paperWidth = in_array($setting->paper_width, ['58mm', 'a4']) ? $setting->paper_width : '80mm';
        $fontCss = $setting->font_family_css;
        $fontSize = $setting->base_font_size_px;
    @endphp
    <title>{{ $setting->invoice_title }} #{{ $order->order_number }} - {{ $setting->header_title }}</title>

    <style>
        /* Local Offline Fonts (Zero External Dependencies) */
        @font-face {
            font-family: 'Vazirmatn';
            src: url('{{ asset('fonts/vazirmatn/vazirmatn-arabic-400-normal.woff2') }}') format('woff2');
            font-weight: 400;
            font-style: normal;
            font-display: swap;
        }
        @font-face {
            font-family: 'Vazirmatn';
            src: url('{{ asset('fonts/vazirmatn/vazirmatn-arabic-700-normal.woff2') }}') format('woff2');
            font-weight: 700;
            font-style: normal;
            font-display: swap;
        }
        @font-face {
            font-family: 'Vazirmatn';
            src: url('{{ asset('fonts/vazirmatn/vazirmatn-arabic-900-normal.woff2') }}') format('woff2');
            font-weight: 900;
            font-style: normal;
            font-display: swap;
        }
        @font-face {
            font-family: 'PostFont';
            src: url('{{ asset('fonts/Post-Regular.ttf') }}') format('truetype');
            font-weight: 400;
            font-style: normal;
            font-display: swap;
        }
        @font-face {
            font-family: 'PostFont';
            src: url('{{ asset('fonts/Post-Bold.otf') }}') format('opentype');
            font-weight: 700;
            font-style: normal;
            font-display: swap;
        }

        *, html, body, table, tr, th, td, div, span, p, b, strong, button, input {
            font-family: {!! $fontCss !!} !important;
            box-sizing: border-box;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        body {
            font-size: {{ $fontSize }}px !important;
            line-height: 1.45;
            color: #000 !important;
            background-color: #fff !important;
            margin: 0;
            padding: 0;
        }

        .receipt-container {
            width: 100%;
            margin: 0 auto;
            background: #fff;
            padding: 4mm 3mm;
        }

        .text-center { text-align: center; }
        .text-start { text-align: right; }
        .text-end { text-align: left; }
        .font-bold { font-weight: bold; }
        .font-black { font-weight: 900; }
        .tabular-nums { font-variant-numeric: tabular-nums; }

        .cafe-title {
            font-size: {{ $fontSize + 4 }}px;
            font-weight: 900;
            letter-spacing: 0.2px;
            line-height: 1.2;
        }

        .cafe-sub {
            font-size: {{ max(9, $fontSize - 1) }}px;
            color: #222;
            margin-top: 1px;
        }

        .invoice-type {
            display: inline-block;
            border: 1px solid #000;
            padding: 1px 8px;
            font-weight: 900;
            font-size: {{ max(9, $fontSize - 1) }}px;
            margin-top: 3px;
            border-radius: 3px;
        }

        .separator-dashed {
            border-top: 1px dashed #000;
            margin: 5px 0;
        }

        .separator-dotted {
            border-top: 1px dotted #333;
            margin: 4px 0;
        }

        .separator-double {
            border-top: 2px solid #000;
            margin: 6px 0;
        }

        .meta-table {
            width: 100%;
            font-size: {{ max(9, $fontSize - 1) }}px;
            border-collapse: collapse;
        }

        .meta-table td {
            padding: 1.5px 0;
            vertical-align: top;
        }

        .items-table {
            width: 100%;
            font-size: {{ max(9, $fontSize - 1) }}px;
            border-collapse: collapse;
            margin: 4px 0;
        }

        .items-table th {
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
            padding: 3px 0;
            font-weight: bold;
        }

        .items-table td {
            padding: 3px 0;
            vertical-align: top;
        }

        .item-note {
            font-size: 8.5px;
            color: #444;
            padding-right: 4px;
        }

        .totals-table {
            width: 100%;
            font-size: {{ $fontSize }}px;
            border-collapse: collapse;
        }

        .totals-table td {
            padding: 2px 0;
        }

        .grand-total {
            font-size: {{ $fontSize + 2 }}px;
            font-weight: 900;
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
            padding: 4px 0 !important;
        }

        .words-total {
            font-size: 9.5px;
            font-weight: bold;
            background-color: #f2f2f2;
            padding: 3px 4px;
            border-radius: 3px;
            margin: 4px 0;
            text-align: center;
            border: 1px dashed #bbb;
        }

        .footer-note {
            font-size: 9px;
            color: #111;
            text-align: center;
            margin-top: 5px;
            line-height: 1.4;
        }

        .barcode-box {
            text-align: center;
            margin: 5px 0 2px 0;
            font-family: monospace;
            font-size: 9.5px;
            letter-spacing: 2px;
            font-weight: bold;
        }

        .qr-box {
            text-align: center;
            margin: 6px 0 3px 0;
        }

        .qr-box img {
            width: 70px;
            height: 70px;
            margin: 0 auto;
            display: block;
        }

        /* Screen only action bar & styling */
        @media screen {
            body {
                background-color: #f1f5f9;
                padding: 24px 16px;
                min-height: 100vh;
            }
            .receipt-container {
                box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1), 0 8px 10px -6px rgba(0,0,0,0.1);
                border-radius: 12px;
                border: 1px solid #e2e8f0;
                transition: max-width 0.2s ease;
            }
            body.format-80mm .receipt-container {
                max-width: 80mm;
            }
            body.format-58mm .receipt-container {
                max-width: 58mm;
            }
            body.format-a4 .receipt-container {
                max-width: 210mm;
                padding: 12mm 15mm;
            }
            body.format-a4 .cafe-title {
                font-size: {{ $fontSize + 8 }}px;
            }
            .print-actions {
                max-width: 80mm;
                margin: 0 auto 16px auto;
                display: flex;
                flex-direction: column;
                gap: 10px;
                transition: max-width 0.2s ease;
            }
            body.format-a4 .print-actions {
                max-width: 210mm;
            }
            body.format-58mm .print-actions {
                max-width: 58mm;
            }
        }

        /* Standard Browser Print & Thermal Print Engine */
        @media print {
            @page {
                size: auto;
                margin: 0mm;
            }
            body.format-80mm {
                width: 80mm !important;
            }
            body.format-58mm {
                width: 58mm !important;
            }
            body.format-a4 {
                width: 100% !important;
            }
            html, body {
                margin: 0 !important;
                padding: 0 !important;
                background: #fff !important;
            }
            .print-actions {
                display: none !important;
            }
            .receipt-container {
                margin: 0 auto !important;
                box-shadow: none !important;
                border: none !important;
                border-radius: 0 !important;
                background: #fff !important;
            }
            body.format-80mm .receipt-container {
                width: 76mm !important;
                max-width: 76mm !important;
                padding: 2mm 1.5mm !important;
            }
            body.format-58mm .receipt-container {
                width: 54mm !important;
                max-width: 54mm !important;
                padding: 1.5mm 1mm !important;
            }
            body.format-a4 .receipt-container {
                width: 100% !important;
                max-width: 195mm !important;
                padding: 8mm 12mm !important;
                border: 1px solid #cbd5e1 !important;
                border-radius: 6px !important;
                font-size: {{ $fontSize + 2 }}px !important;
            }
            body.format-a4 .cafe-title {
                font-size: {{ $fontSize + 8 }}px !important;
            }
        }
    </style>
</head>
<body class="format-{{ $paperWidth }}">

    <!-- On-screen Action Toolbar with Format Switcher & Browser Print Hints -->
    <div class="print-actions">
        <div style="display: flex; gap: 6px; background: #fff; padding: 6px; border-radius: 12px; border: 1px solid #cbd5e1; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
            <button type="button" onclick="setPaperFormat('80mm')" id="btn-80mm" style="flex: 1; padding: 6px 4px; border-radius: 8px; font-size: 11px; font-weight: bold; cursor: pointer; border: none; transition: all 0.15s; {{ $paperWidth === '80mm' ? 'background: #284B63; color: #fff;' : 'background: #f1f5f9; color: #475569;' }}">
                رول ۸۰mm
            </button>
            <button type="button" onclick="setPaperFormat('58mm')" id="btn-58mm" style="flex: 1; padding: 6px 4px; border-radius: 8px; font-size: 11px; font-weight: bold; cursor: pointer; border: none; transition: all 0.15s; {{ $paperWidth === '58mm' ? 'background: #284B63; color: #fff;' : 'background: #f1f5f9; color: #475569;' }}">
                رول ۵۸mm
            </button>
            <button type="button" onclick="setPaperFormat('a4')" id="btn-a4" style="flex: 1; padding: 6px 4px; border-radius: 8px; font-size: 11px; font-weight: bold; cursor: pointer; border: none; transition: all 0.15s; {{ $paperWidth === 'a4' ? 'background: #284B63; color: #fff;' : 'background: #f1f5f9; color: #475569;' }}">
                کاغذ A4/A5
            </button>
        </div>

        <div style="display: flex; gap: 8px;">
            <button type="button" onclick="triggerPrint()" style="flex: 2; padding: 11px; border-radius: 12px; font-size: 12px; font-weight: 900; background: #3C6E71; color: #fff; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 6px; box-shadow: 0 4px 12px rgba(60,110,113,0.25);">
                <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>چاپ فیش (Print)</span>
            </button>
            <button type="button" onclick="window.close()" style="flex: 1; padding: 11px; border-radius: 12px; font-size: 11px; font-weight: bold; background: #e2e8f0; color: #334155; border: none; cursor: pointer;">
                بستن
            </button>
        </div>

        <div style="font-size: 10px; color: #64748B; text-align: center; line-height: 1.4; background: #fff; padding: 6px 10px; border-radius: 10px; border: 1px dashed #cbd5e1;">
            💡 <strong style="color: #1e293b;">راهنمای چاپ مرورگر:</strong> در کادر پرینت، حاشیه (Margins) را روی <strong>None (صفر)</strong> بگذارید تا فیش دقیقاً بدون سفیدی لبه‌ها چاپ شود.
        </div>
    </div>

    <!-- Thermal Receipt Content (Zafaran / Saffron Inspired Standard) -->
    <div class="receipt-container">

        <!-- Header -->
        <div class="text-center">
            @if($setting->show_logo)
                <div style="margin-bottom: 4px;">
                    <img src="{{ asset('images/logo.jpeg') }}" alt="Logo" style="width: 42px; height: 42px; border-radius: 50%; object-fit: cover; margin: 0 auto; display: block; filter: grayscale(100%) contrast(150%);">
                </div>
            @endif

            <div class="cafe-title">{{ $setting->header_title }}</div>
            @if($setting->header_subtitle)
                <div class="cafe-sub">{{ $setting->header_subtitle }}</div>
            @endif
            <div class="invoice-type">{{ $setting->invoice_title }}</div>
        </div>

        <div class="separator-dashed"></div>

        <!-- Meta Info -->
        <table class="meta-table">
            @if($setting->show_order_number)
            <tr>
                <td class="text-start" style="width: 40%;">شماره فاکتور:</td>
                <td class="text-end font-black tabular-nums font-mono" style="width: 60%;">#{{ $order->order_number }}</td>
            </tr>
            @endif

            @if($setting->show_date_time)
            <tr>
                <td class="text-start">تاریخ و ساعت:</td>
                <td class="text-end tabular-nums">{{ \App\Helpers\JalaliHelper::format($order->created_at, 'Y/m/d - H:i') }}</td>
            </tr>
            @endif

            @if($setting->show_cashier_name)
            <tr>
                <td class="text-start">صندوق‌دار:</td>
                <td class="text-end">{{ $order->creator->name ?? 'امیر محمد بهارلو' }}</td>
            </tr>
            @endif

            @if($setting->show_customer_name)
            <tr>
                <td class="text-start">مشتری:</td>
                <td class="text-end font-bold">{{ $order->customer->name ?? ($order->customer_name ?? 'مشتری حضوری') }}</td>
            </tr>
            @endif

            @if($setting->show_customer_phone && ($order->customer?->phone || $order->customer_phone))
            <tr>
                <td class="text-start">تلفن همراه:</td>
                <td class="text-end tabular-nums font-mono" dir="ltr">{{ $order->customer?->phone ?? $order->customer_phone }}</td>
            </tr>
            @endif

            @if($setting->show_customer_club_info && $order->customer)
            <tr>
                <td class="text-start">باشگاه مشتریان:</td>
                <td class="text-end">
                    <span>{{ $order->customer->tier?->name ?? 'عضو عادی' }}</span>
                    @if($order->customer->wallet_balance > 0)
                        <span class="tabular-nums">({{ number_format($order->customer->wallet_balance) }} ت اعتبار)</span>
                    @endif
                </td>
            </tr>
            @endif

            @if($setting->show_order_type)
            <tr>
                <td class="text-start">نوع سفارش:</td>
                <td class="text-end font-bold">
                    @if($order->isDineIn())
                        سرو سالن ({{ $order->table_number ? 'میز شماره ' . $order->table_number : 'کانتر' }})
                    @elseif($order->isCourier() || $order->isDelivery())
                        ارسال پیک در شیراز
                    @else
                        بیرون‌بر / تحویل حضوری
                    @endif
                </td>
            </tr>
            @endif

            @if($setting->show_delivery_address && ($order->isDelivery() || $order->isCourier()))
                @php
                    $deliveryAddr = $order->deliveryAddress?->address_line ?? $order->shipping_address ?? $order->delivery_address;
                @endphp
                @if($deliveryAddr)
                <tr>
                    <td class="text-start" style="vertical-align: top;">آدرس تحویل:</td>
                    <td class="text-end" style="word-break: break-word;">
                        {{ $deliveryAddr }}
                        @if($order->deliveryAddress?->building_unit) - پلاک/واحد: {{ $order->deliveryAddress->building_unit }} @endif
                    </td>
                </tr>
                @endif
            @endif
        </table>

        <!-- Order Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    @if($setting->show_item_row_number)
                        <th style="width: 8%;" class="text-center">#</th>
                    @endif
                    <th class="text-start">شرح کالا</th>
                    <th style="width: 15%;" class="text-center">تعداد</th>
                    @if($setting->show_item_unit_price)
                        <th style="width: 22%;" class="text-end">فی</th>
                    @endif
                    <th style="width: 25%;" class="text-end">مبلغ</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $idx => $item)
                @php
                    $unitPrice = $item->unit_price ?? ($item->quantity > 0 ? round($item->total_price / $item->quantity) : 0);
                    $lineTotal = $item->total_price ?? ($item->quantity * $unitPrice);
                @endphp
                <tr>
                    @if($setting->show_item_row_number)
                        <td class="text-center tabular-nums">{{ $idx + 1 }}</td>
                    @endif
                    <td class="text-start font-bold">
                        {{ $item->product->name ?? 'کالا' }}
                        @if($setting->show_item_notes && $item->notes)
                            <div class="item-note">({{ $item->notes }})</div>
                        @endif
                    </td>
                    <td class="text-center tabular-nums font-bold">{{ $item->quantity }}</td>
                    @if($setting->show_item_unit_price)
                        <td class="text-end tabular-nums">{{ number_format($unitPrice) }}</td>
                    @endif
                    <td class="text-end tabular-nums font-bold">{{ number_format($lineTotal) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="separator-dashed"></div>

        <!-- Totals & Calculations -->
        <table class="totals-table">
            @if($setting->show_subtotal)
            <tr>
                <td class="text-start">جمع کل اقلام:</td>
                <td class="text-end tabular-nums font-bold">{{ number_format($order->total_price) }} تومان</td>
            </tr>
            @endif

            @if($setting->show_discount && $order->discount_amount > 0)
            <tr>
                <td class="text-start">تخفیف فاکتور:</td>
                <td class="text-end tabular-nums font-bold">- {{ number_format($order->discount_amount) }} تومان</td>
            </tr>
            @endif

            @if($setting->show_delivery_fee && $order->delivery_fee > 0)
            <tr>
                <td class="text-start">هزینه ارسال پیک:</td>
                <td class="text-end tabular-nums font-bold">{{ number_format($order->delivery_fee) }} تومان</td>
            </tr>
            @endif

            @if($setting->show_tax && $setting->tax_percent > 0)
            @php
                $taxAmount = round(($order->total_price - $order->discount_amount) * ($setting->tax_percent / 100));
            @endphp
            <tr>
                <td class="text-start">مالیات ارزش افزوده ({{ $setting->tax_percent }}٪):</td>
                <td class="text-end tabular-nums font-bold">{{ number_format($taxAmount) }} تومان</td>
            </tr>
            @endif

            <tr class="grand-total">
                <td class="text-start">مبلغ قابل پرداخت:</td>
                <td class="text-end tabular-nums">{{ number_format($order->final_payable) }} تومان</td>
            </tr>
        </table>

        <!-- Amount In Persian Words -->
        @if($setting->show_amount_in_words)
        <div class="words-total">
            {{ \App\Helpers\NumberToWordsHelper::toWords($order->final_payable) }} تومان
        </div>
        @endif

        <!-- Payment Settlement Info -->
        @if($setting->show_payment_method || $setting->show_payment_status)
        <table class="meta-table" style="font-size: 9.5px; margin-top: 3px;">
            @if($setting->show_payment_method)
            <tr>
                <td class="text-start">روش تسویه:</td>
                <td class="text-end font-bold">
                    @php
                        $methodTitle = match($order->payment_method) {
                            'card_to_card' => 'کارت به کارت',
                            'zarinpal' => 'درگاه پرداخت آنلاین شاپرک',
                            'cash', 'cod' => 'پرداخت نقدی / پوز سیار',
                            'subscription', 'subscription_credit' => 'سهمیه اعتبار اشتراک روزانه',
                            default => $order->payment_method ?? 'تسویه در صندوق کافه',
                        };
                    @endphp
                    {{ $methodTitle }}
                </td>
            </tr>
            @endif

            @if($setting->show_payment_status)
            <tr>
                <td class="text-start">وضعیت تسویه:</td>
                <td class="text-end font-bold">
                    {{ in_array($order->status, ['completed', 'preparing', 'ready', 'shipping', 'processing']) || $order->payment_status === 'paid' ? 'تسویه شده' : 'در انتظار تسویه' }}
                </td>
            </tr>
            @endif
        </table>
        @endif

        <!-- Tracking Barcode Representation -->
        @if($setting->show_barcode)
        <div class="barcode-box">
            * {{ $order->order_number }} *
        </div>
        @endif

        <!-- QR Code -->
        @if($setting->show_qr_code)
            @php
                $qrTargetUrl = match($setting->qr_code_type) {
                    'instagram' => 'https://instagram.com/' . ltrim($setting->instagram_handle ?? 'bcafe.shiraz', '@'),
                    'website' => url('/'),
                    'custom' => $setting->custom_qr_url ?? url('/'),
                    default => route('storefront.order-status', $order->order_number),
                };
            @endphp
            <div class="qr-box">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=120x120&margin=0&data={{ urlencode($qrTargetUrl) }}" alt="QR Code">
                <span style="font-size: 8px; color: #555; display: block; margin-top: 2px;">جهت پیگیری آنلاین یا باشگاه اسکن کنید</span>
            </div>
        @endif

        <!-- Wi-Fi & Social Media -->
        @if($setting->show_wifi && $setting->wifi_ssid)
        <div class="separator-dotted"></div>
        <div class="footer-note" style="font-size: 8.5px;">
            <span><svg class="w-4 h-4 inline-block align-middle ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"/></svg> وای‌فای مهمان: <b>{{ $setting->wifi_ssid }}</b></span>
            @if($setting->wifi_password)
                <span style="margin-right: 6px;">رمز: <b dir="ltr">{{ $setting->wifi_password }}</b></span>
            @endif
        </div>
        @endif

        @if($setting->show_instagram && $setting->instagram_handle)
        <div class="footer-note" style="font-size: 8.5px; margin-top: 2px;">
            <span><svg class="w-4 h-4 inline-block align-middle ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg> اینستاگرام: <b dir="ltr">{{ $setting->instagram_handle }}</b></span>
        </div>
        @endif

        <div class="separator-double"></div>

        <!-- Footer Note -->
        @if($setting->footer_message)
        <div class="footer-note" style="white-space: pre-line;">
            {{ $setting->footer_message }}
        </div>
        @endif

    </div>

    <script>
        function setPaperFormat(format) {
            document.body.classList.remove('format-80mm', 'format-58mm', 'format-a4');
            document.body.classList.add('format-' + format);
            ['80mm', '58mm', 'a4'].forEach(function(f) {
                var el = document.getElementById('btn-' + f);
                if (el) {
                    if (f === format) {
                        el.style.backgroundColor = '#284B63';
                        el.style.color = '#fff';
                    } else {
                        el.style.backgroundColor = '#f1f5f9';
                        el.style.color = '#475569';
                    }
                }
            });
        }

        function triggerPrint() {
            if (document.fonts && document.fonts.ready) {
                document.fonts.ready.then(function() {
                    window.print();
                });
            } else {
                window.print();
            }
        }

        @if($setting->auto_print)
        window.addEventListener('load', function() {
            if (document.fonts && document.fonts.ready) {
                document.fonts.ready.then(function() {
                    setTimeout(function() { window.print(); }, 250);
                });
            } else {
                setTimeout(function() { window.print(); }, 400);
            }
        });
        @endif
    </script>
</body>
</html>
