<?php

namespace App\Http\Controllers\Web;

use App\Helpers\JalaliHelper;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Subscription;
use App\Models\SubscriptionUsage;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $cafeId = auth()->user()->cafe_id ?? 1;

        // 1. Subscription Revenue (Aggregated in SQL engine)
        $subscription_revenue = (float) Subscription::where('subscriptions.cafe_id', $cafeId)
            ->join('subscription_plans', 'subscriptions.subscription_plan_id', '=', 'subscription_plans.id')
            ->sum('subscription_plans.price');
        $total_subscriptions_count = Subscription::where('cafe_id', $cafeId)->count();

        // 2. Direct Paid Orders Revenue
        $paid_orders_query = Order::where('cafe_id', $cafeId)->where('type', 'normal');
        $paid_order_revenue = (float) $paid_orders_query->sum('total_price');
        $paid_orders_count = $paid_orders_query->count();

        // 3. Total Revenue & Key Financial Indicators
        $total_revenue = $subscription_revenue + $paid_order_revenue;
        $avg_order_value = $paid_orders_count > 0 ? ($paid_order_revenue / $paid_orders_count) : 0;

        // 4. Free Coffees Delivered & Estimated Value (Aggregated in SQL engine)
        $total_free_coffees_delivered = SubscriptionUsage::where('cafe_id', $cafeId)->count();
        $estimated_free_coffee_value = (float) SubscriptionUsage::where('subscription_usages.cafe_id', $cafeId)
            ->join('products', 'subscription_usages.product_id', '=', 'products.id')
            ->sum('products.price');

        // 5. Product Performance Breakdown (Optimized with grouped aggregations)
        $paidItemCounts = OrderItem::whereHas('order', function ($q) use ($cafeId) {
            $q->where('cafe_id', $cafeId)->where('type', 'normal');
        })->groupBy('product_id')->selectRaw('product_id, SUM(quantity) as total_qty')->pluck('total_qty', 'product_id');

        $freeItemCounts = SubscriptionUsage::where('cafe_id', $cafeId)
            ->groupBy('product_id')->selectRaw('product_id, COUNT(*) as total_qty')->pluck('total_qty', 'product_id');

        $products = Product::where('cafe_id', $cafeId)->get();
        $product_stats = $products->map(function ($product) use ($paidItemCounts, $freeItemCounts) {
            $paid_qty = (int) ($paidItemCounts[$product->id] ?? 0);
            $free_qty = (int) ($freeItemCounts[$product->id] ?? 0);
            $total_qty = $paid_qty + $free_qty;
            $revenue = $paid_qty * $product->price;

            return (object) [
                'name' => $product->name,
                'type' => $product->type,
                'price' => $product->price,
                'paid_qty' => $paid_qty,
                'free_qty' => $free_qty,
                'total_qty' => $total_qty,
                'revenue' => $revenue,
            ];
        })->sortByDesc('total_qty')->values();

        // 6. Past 14 Days Daily Chart Data (Optimized with single grouped queries)
        $fourteenDaysAgo = Carbon::now()->subDays(13)->startOfDay();

        $dailyRevenues = Order::where('cafe_id', $cafeId)
            ->where('type', 'normal')
            ->where('created_at', '>=', $fourteenDaysAgo)
            ->selectRaw('DATE(created_at) as order_date, SUM(total_price) as sum_price')
            ->groupBy('order_date')
            ->pluck('sum_price', 'order_date');

        $dailyFreeCounts = SubscriptionUsage::where('cafe_id', $cafeId)
            ->where('used_date', '>=', $fourteenDaysAgo->toDateString())
            ->selectRaw('used_date, COUNT(*) as count_usage')
            ->groupBy('used_date')
            ->pluck('count_usage', 'used_date');

        $chart_labels = [];
        $chart_revenue_data = [];
        $chart_free_data = [];

        for ($i = 13; $i >= 0; --$i) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $jalaliDate = JalaliHelper::format($date, 'Y/m/d');

            $chart_labels[] = $jalaliDate;
            $chart_revenue_data[] = (float) ($dailyRevenues[$date] ?? 0);
            $chart_free_data[] = (int) ($dailyFreeCounts[$date] ?? 0);
        }

        // 7. Monthly Performance Breakdown (Recent 12 months for memory efficiency)
        $twelveMonthsAgo = Carbon::now()->subMonths(12)->startOfMonth();
        $allPaidOrders = Order::where('cafe_id', $cafeId)
            ->where('type', 'normal')
            ->where('created_at', '>=', $twelveMonthsAgo)
            ->get(['created_at', 'total_price']);

        $monthlyPaidRevenues = $allPaidOrders->groupBy(fn ($order) => Carbon::parse($order->created_at)->format('Y-m'))
            ->map(fn ($orders) => (float) $orders->sum('total_price'));

        $usageItems = SubscriptionUsage::where('subscription_usages.cafe_id', $cafeId)
            ->where('subscription_usages.used_date', '>=', $twelveMonthsAgo->toDateString())
            ->join('products', 'subscription_usages.product_id', '=', 'products.id')
            ->select(['subscription_usages.used_date', 'products.price'])
            ->get();

        $monthly_usage = $usageItems->groupBy(function ($usage) {
            return Carbon::parse($usage->used_date)->format('Y-m');
        })->map(function ($items, $yearMonth) use ($monthlyPaidRevenues) {
            $free_count = $items->count();
            $free_val = (float) $items->sum('price');
            $monthly_paid = (float) ($monthlyPaidRevenues[$yearMonth] ?? 0.0);

            return (object) [
                'year_month' => $yearMonth,
                'jalali_month' => JalaliHelper::format($yearMonth . '-01', 'Y/m'),
                'total_usage' => $free_count,
                'free_value' => $free_val,
                'paid_revenue' => $monthly_paid,
                'total_combined' => $monthly_paid + $free_val,
            ];
        })->sortByDesc('year_month')->values();

        return view('reports.index', compact(
            'subscription_revenue',
            'paid_order_revenue',
            'total_revenue',
            'total_subscriptions_count',
            'paid_orders_count',
            'avg_order_value',
            'total_free_coffees_delivered',
            'estimated_free_coffee_value',
            'product_stats',
            'chart_labels',
            'chart_revenue_data',
            'chart_free_data',
            'monthly_usage'
        ));
    }

    /**
     * Export Orders to CSV (Excel-compatible with UTF-8 BOM)
     */
    public function exportOrders(Request $request)
    {
        $cafeId = auth()->user()->cafe_id ?? 1;

        $ordersQuery = Order::where('cafe_id', $cafeId)
            ->with(['customer', 'latestPayment', 'deliveryAddress'])
            ->latest('id');

        $statusLabels = [
            'pending' => 'در انتظار تایید',
            'awaiting_payment_verification' => 'در انتظار تایید واریز',
            'preparing' => 'در حال آماده‌سازی',
            'processing' => 'در حال پردازش',
            'ready' => 'آماده تحویل/ارسال',
            'shipping' => 'تحویل به پیک',
            'out_for_delivery' => 'در مسیر ارسال',
            'completed' => 'تکمیل و تحویل شده',
            'cancelled' => 'لغو شده',
        ];

        $orderTypeLabels = [
            'dine_in' => 'حضوری در سالن',
            'courier' => 'ارسال با پیک (شیراز)',
            'takeaway' => 'بیرون‌بر حضوری',
        ];

        $paymentMethodLabels = [
            'zarinpal' => 'آنلاین زرین‌پال',
            'card_to_card' => 'کارت به کارت',
            'cash' => 'پرداخت در محل / حضوری',
            'subscription_credit' => 'سهمیه اشتراک روزانه',
        ];

        $filename = 'orders-report-' . JalaliHelper::format(now(), 'Y-m-d') . '.csv';

        $callback = function () use ($ordersQuery, $statusLabels, $orderTypeLabels, $paymentMethodLabels) {
            $handle = fopen('php://output', 'w');
            // Write UTF-8 BOM for Microsoft Excel on Windows
            fwrite($handle, "\xEF\xBB\xBF");

            // CSV Header Row
            fputcsv($handle, [
                'شناسه',
                'شماره سفارش',
                'تاریخ ثبت (شمسی)',
                'نام مشتری',
                'شماره تماس',
                'نوع سفارش',
                'میز / آدرس',
                'روش پرداخت',
                'وضعیت فاکتور',
                'وضعیت پرداخت',
                'مبلغ اقلام (تومان)',
                'تخفیف (تومان)',
                'هزینه ارسال (تومان)',
                'مبلغ نهایی پرداختی (تومان)',
                'یادداشت سفارش'
            ]);

            $ordersQuery->lazy(200)->each(function ($order) use ($handle, $statusLabels, $orderTypeLabels, $paymentMethodLabels) {
                $location = $order->order_type === 'dine_in'
                    ? ($order->table_number ? 'میز ' . $order->table_number : 'سالن')
                    : ($order->deliveryAddress ? $order->deliveryAddress->address_line : 'بیرون‌بر');

                $paymentStatus = $order->latestPayment?->status === 'successful' || $order->status === 'completed'
                    ? 'پرداخت شده'
                    : ($order->latestPayment?->status === 'pending' ? 'در انتظار تایید' : 'تسویه نشده');

                fputcsv($handle, [
                    $order->id,
                    $order->order_number ?? ('#' . $order->id),
                    JalaliHelper::format($order->created_at, 'Y/m/d H:i'),
                    $order->customer?->name ?? 'مشتری آزاد / ناشناس',
                    $order->customer?->phone ?? '-',
                    $orderTypeLabels[$order->order_type] ?? $order->order_type,
                    $location,
                    $paymentMethodLabels[$order->payment_method] ?? $order->payment_method,
                    $statusLabels[$order->status] ?? $order->status,
                    $paymentStatus,
                    (int) $order->total_price,
                    (int) $order->discount_amount,
                    (int) $order->delivery_fee,
                    (int) ($order->final_payable ?: $order->total_price),
                    $order->notes ?? '-'
                ]);
            });

            fclose($handle);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ]);
    }

    /**
     * Export Products Sales Breakdown to CSV
     */
    public function exportProducts(Request $request)
    {
        $cafeId = auth()->user()->cafe_id ?? 1;

        $paidItemCounts = OrderItem::whereHas('order', function ($q) use ($cafeId) {
            $q->where('cafe_id', $cafeId)->where('type', 'normal');
        })->groupBy('product_id')->selectRaw('product_id, SUM(quantity) as total_qty')->pluck('total_qty', 'product_id');

        $freeItemCounts = SubscriptionUsage::where('cafe_id', $cafeId)
            ->groupBy('product_id')->selectRaw('product_id, COUNT(*) as total_qty')->pluck('total_qty', 'product_id');

        $products = Product::where('cafe_id', $cafeId)->with('category')->get();

        $filename = 'products-sales-report-' . JalaliHelper::format(now(), 'Y-m-d') . '.csv';

        $callback = function () use ($products, $paidItemCounts, $freeItemCounts) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'شناسه محصول',
                'نام محصول',
                'دسته‌بندی',
                'قیمت واحد (تومان)',
                'تعداد فروش نقدی',
                'تعداد مصرف اشتراکی',
                'مجموع تعداد کل',
                'کل فروش ناخالص نقدی (تومان)',
                'وضعیت'
            ]);

            foreach ($products as $product) {
                $paidQty = (int) ($paidItemCounts[$product->id] ?? 0);
                $freeQty = (int) ($freeItemCounts[$product->id] ?? 0);
                $totalQty = $paidQty + $freeQty;
                $revenue = $paidQty * (float) $product->price;

                fputcsv($handle, [
                    $product->id,
                    $product->name,
                    $product->category?->name ?? 'عمومی',
                    (int) $product->price,
                    $paidQty,
                    $freeQty,
                    $totalQty,
                    (int) $revenue,
                    $product->status === 'active' ? 'فعال' : 'غیرفعال'
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ]);
    }

    /**
     * Export Customers & Loyalty Data to CSV
     */
    public function exportCustomers(Request $request)
    {
        $cafeId = auth()->user()->cafe_id ?? 1;

        $customers = \App\Models\Customer::where('cafe_id', $cafeId)
            ->with(['tier'])
            ->withSum('loyaltyPoints as total_points', 'points')
            ->get();

        $filename = 'customers-loyalty-report-' . JalaliHelper::format(now(), 'Y-m-d') . '.csv';

        $callback = function () use ($customers) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'شناسه مشتری',
                'نام و نام خانوادگی',
                'شماره موبایل',
                'ایمیل',
                'سطح وفاداری',
                'موجودی کیف پول (تومان)',
                'مجموع امتیازات باشگاه',
                'مجموع خرید کل (تومان)',
                'تاریخ عضویت (شمسی)'
            ]);

            foreach ($customers as $customer) {
                fputcsv($handle, [
                    $customer->id,
                    $customer->name,
                    $customer->phone,
                    $customer->email ?? '-',
                    $customer->tier?->name ?? 'عادی (برنز)',
                    (int) $customer->wallet_balance,
                    (int) ($customer->total_points ?? 0),
                    (int) $customer->total_spent,
                    JalaliHelper::format($customer->created_at, 'Y/m/d H:i')
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ]);
    }
}

