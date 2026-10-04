<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @php
        $setting = $setting ?? \App\Models\ReceiptSetting::getForCafe($order->cafe_id ?? 1);
        $paperWidth = $setting->paper_width === '58mm' ? '58mm' : '80mm';
        $containerWidth = $setting->paper_width === '58mm' ? '54mm' : '76mm';
        $fontCss = $setting->font_family_css;
        $fontSize = $setting->base_font_size_px;
    @endphp
    <title>{{ $setting->invoice_title }} #{{ $order->order_number }} - {{ $setting->header_title }}</title>
    
    @vite(['resources/css/app.css'])

    <style>
        @page {
            size: {{ $paperWidth }} auto;
            margin: 0;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        body {
            font-family: {!! $fontCss !!};
            font-size: {{ $fontSize }}px;
            line-height: 1.45;
            color: #000;
            background-color: #fff;
            padding: 3mm 2mm;
            width: {{ $containerWidth }};
            margin: 0 auto;
        }

        .receipt-container {
            width: 100%;
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
                padding: 20px;
                width: auto;
            }
            .receipt-container {
                background: #fff;
                width: {{ $paperWidth }};
                margin: 0 auto;
                padding: 6mm 4mm;
                box-shadow: 0 4px 20px rgba(0,0,0,0.15);
                border-radius: 6px;
            }
            .print-actions {
                width: {{ $paperWidth }};
                margin: 0 auto 15px auto;
                display: flex;
                gap: 8px;
            }
            .btn {
                flex: 1;
                padding: 10px;
                border: none;
                border-radius: 8px;
                font-family: inherit;
                font-size: 12px;
                font-weight: bold;
                cursor: pointer;
                text-align: center;
            }
            .btn-primary {
                background-color: #387780;
                color: #fff;
            }
            .btn-secondary {
                background-color: #e2e8f0;
                color: #334155;
            }
        }

        @media print {
            .print-actions {
                display: none !important;
            }
        }
    </style>
</head>
<body>

    <!-- On-screen Action Toolbar -->
    <div class="print-actions">
        <button onclick="window.print()" class="btn btn-primary"><svg class="w-4 h-4 inline-block align-middle ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg> چاپ فیش حرارتی ({{ $paperWidth }})</button>
        <button onclick="window.close()" class="btn btn-secondary">بستن</button>
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

    @if($setting->auto_print)
    <script>
        // Automatic print trigger on receipt open
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 350);
        };
    </script>
    @endif
</body>
</html>
