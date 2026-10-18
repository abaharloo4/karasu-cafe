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
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Resolve date range from request parameters.
     * Returns: [$startDate, $endDate, $periodLabel, $periodKey, $rawStartDate, $rawEndDate]
     */
    protected function resolveDateRange(Request $request): array
    {
        $period = $request->input('period', '30days');
        $rawStart = $request->input('start_date');
        $rawEnd = $request->input('end_date');
        $startDate = null;
        $endDate = null;
        $periodLabel = '۳۰ روز گذشته';

        switch ($period) {
            case 'today':
                $startDate = Carbon::today()->startOfDay();
                $endDate = Carbon::now()->endOfDay();
                $periodLabel = 'امروز (' . JalaliHelper::format(now(), 'Y/m/d') . ')';
                break;

            case 'yesterday':
                $startDate = Carbon::yesterday()->startOfDay();
                $endDate = Carbon::yesterday()->endOfDay();
                $periodLabel = 'دیروز (' . JalaliHelper::format(Carbon::yesterday(), 'Y/m/d') . ')';
                break;

            case '7days':
                $startDate = Carbon::now()->subDays(6)->startOfDay();
                $endDate = Carbon::now()->endOfDay();
                $periodLabel = '۷ روز گذشته';
                break;

            case '30days':
                $startDate = Carbon::now()->subDays(29)->startOfDay();
                $endDate = Carbon::now()->endOfDay();
                $periodLabel = '۳۰ روز گذشته';
                break;

            case 'this_month':
                $startDate = Carbon::now()->startOfMonth();
                $endDate = Carbon::now()->endOfDay();
                $periodLabel = 'این ماه (تا امروز)';
                break;

            case 'last_month':
                $startDate = Carbon::now()->subMonth()->startOfMonth();
                $endDate = Carbon::now()->subMonth()->endOfMonth();
                $periodLabel = 'ماه گذشته';
                break;

            case 'custom':
                if (!empty($rawStart)) {
                    try {
                        if (preg_match('/^14\d\d/', trim($rawStart))) {
                            $startDate = \Morilog\Jalali\Jalalian::fromFormat('Y/m/d', str_replace('-', '/', trim($rawStart)))->toCarbon()->startOfDay();
                        } else {
                            $startDate = Carbon::parse(trim($rawStart))->startOfDay();
                        }
                    } catch (\Exception $e) {
                        $startDate = Carbon::now()->subDays(29)->startOfDay();
                    }
                }
                if (!empty($rawEnd)) {
                    try {
                        if (preg_match('/^14\d\d/', trim($rawEnd))) {
                            $endDate = \Morilog\Jalali\Jalalian::fromFormat('Y/m/d', str_replace('-', '/', trim($rawEnd)))->toCarbon()->endOfDay();
                        } else {
                            $endDate = Carbon::parse(trim($rawEnd))->endOfDay();
                        }
                    } catch (\Exception $e) {
                        $endDate = Carbon::now()->endOfDay();
                    }
                }
                $periodLabel = 'بازه انتخابی (' . ($startDate ? JalaliHelper::format($startDate, 'Y/m/d') : 'ابتدا') . ' تا ' . ($endDate ? JalaliHelper::format($endDate, 'Y/m/d') : 'امروز') . ')';
                break;

            case 'all':
                $startDate = null;
                $endDate = null;
                $periodLabel = 'کل دوره‌ها (همه‌زمان)';
                break;

            default:
                $period = '30days';
                $startDate = Carbon::now()->subDays(29)->startOfDay();
                $endDate = Carbon::now()->endOfDay();
                $periodLabel = '۳۰ روز گذشته';
                break;
        }

        return [$startDate, $endDate, $periodLabel, $period, $rawStart, $rawEnd];
    }

    public function index(Request $request)
    {
        $cafeId = auth()->user()->cafe_id ?? 1;

        [$startDate, $endDate, $periodLabel, $periodKey, $rawStart, $rawEnd] = $this->resolveDateRange($request);

        // 1. Direct Paid Orders Query (Scope by date)
        $paid_orders_query = Order::where('cafe_id', $cafeId)->where('type', 'normal');
        if ($startDate && $endDate) {
            $paid_orders_query->whereBetween('created_at', [$startDate, $endDate]);
        } elseif ($startDate) {
            $paid_orders_query->where('created_at', '>=', $startDate);
        } elseif ($endDate) {
            $paid_orders_query->where('created_at', '<=', $endDate);
        }

        // Exclude cancelled for revenue calculation, but keep for order count
        $paid_orders_count = (clone $paid_orders_query)->count();
        $successful_orders_count = (clone $paid_orders_query)->whereNotIn('status', ['cancelled'])->count();
        $cancelled_orders_count = (clone $paid_orders_query)->where('status', 'cancelled')->count();

        $paid_order_revenue = (float) (clone $paid_orders_query)
            ->whereNotIn('status', ['cancelled'])
            ->sum(DB::raw('COALESCE(final_payable, total_price)'));

        $total_discount_amount = (float) (clone $paid_orders_query)
            ->whereNotIn('status', ['cancelled'])
            ->sum('discount_amount');

        $total_delivery_fees = (float) (clone $paid_orders_query)
            ->whereNotIn('status', ['cancelled'])
            ->sum('delivery_fee');

        $avg_order_value = $successful_orders_count > 0 ? ($paid_order_revenue / $successful_orders_count) : 0;

        // 2. Subscription Revenue Query (Scope by date)
        $subscriptions_query = Subscription::where('subscriptions.cafe_id', $cafeId)
            ->join('subscription_plans', 'subscriptions.subscription_plan_id', '=', 'subscription_plans.id');
        if ($startDate && $endDate) {
            $subscriptions_query->whereBetween('subscriptions.created_at', [$startDate, $endDate]);
        } elseif ($startDate) {
            $subscriptions_query->where('subscriptions.created_at', '>=', $startDate);
        } elseif ($endDate) {
            $subscriptions_query->where('subscriptions.created_at', '<=', $endDate);
        }

        $subscription_revenue = (float) $subscriptions_query->sum('subscription_plans.price');
        $total_subscriptions_count = (int) $subscriptions_query->count();

        // 3. Subscription Usages (Free coffee delivered in timeframe)
        $usages_query = SubscriptionUsage::where('cafe_id', $cafeId);
        if ($startDate && $endDate) {
            $usages_query->whereBetween('used_date', [$startDate->toDateString(), $endDate->toDateString()]);
        } elseif ($startDate) {
            $usages_query->where('used_date', '>=', $startDate->toDateString());
        } elseif ($endDate) {
            $usages_query->where('used_date', '<=', $endDate->toDateString());
        }

        $total_free_coffees_delivered = (int) $usages_query->count();
        $estimated_free_coffee_value = (float) SubscriptionUsage::where('subscription_usages.cafe_id', $cafeId)
            ->when($startDate && $endDate, fn ($q) => $q->whereBetween('subscription_usages.used_date', [$startDate->toDateString(), $endDate->toDateString()]))
            ->when($startDate && !$endDate, fn ($q) => $q->where('subscription_usages.used_date', '>=', $startDate->toDateString()))
            ->when(!$startDate && $endDate, fn ($q) => $q->where('subscription_usages.used_date', '<=', $endDate->toDateString()))
            ->join('products', 'subscription_usages.product_id', '=', 'products.id')
            ->sum('products.price');

        // Total combined revenue
        $total_revenue = $subscription_revenue + $paid_order_revenue;

        // 4. Payment Method Breakdown (Count and Sum)
        $paymentBreakdownRaw = (clone $paid_orders_query)
            ->whereNotIn('status', ['cancelled'])
            ->selectRaw('payment_method, COUNT(*) as count, SUM(COALESCE(final_payable, total_price)) as sum_amount')
            ->groupBy('payment_method')
            ->get();

        $paymentStats = [
            'card_to_card' => [
                'label' => 'کارت به کارت',
                'count' => 0,
                'sum' => 0,
                'color' => '#3C6E71',
                'bg_badge' => 'border-emerald-500/30 bg-emerald-500/10 text-emerald-800',
            ],
            'zarinpal' => [
                'label' => 'درگاه آنلاین (زرین‌پال)',
                'count' => 0,
                'sum' => 0,
                'color' => '#284B63',
                'bg_badge' => 'border-blue-500/30 bg-blue-500/10 text-blue-800',
            ],
            'cash' => [
                'label' => 'نقدی و پوز حضوری',
                'count' => 0,
                'sum' => 0,
                'color' => '#9B561B',
                'bg_badge' => 'border-amber-500/30 bg-amber-500/10 text-amber-800',
            ],
            'subscription_credit' => [
                'label' => 'سهمیه اشتراک',
                'count' => 0,
                'sum' => 0,
                'color' => '#353535',
                'bg_badge' => 'border-purple-500/30 bg-purple-500/10 text-purple-800',
            ],
        ];

        foreach ($paymentBreakdownRaw as $item) {
            $key = $item->payment_method ?? 'cash';
            if (isset($paymentStats[$key])) {
                $paymentStats[$key]['count'] = (int) $item->count;
                $paymentStats[$key]['sum'] = (float) $item->sum_amount;
            } else {
                $paymentStats[$key] = [
                    'label' => $key,
                    'count' => (int) $item->count,
                    'sum' => (float) $item->sum_amount,
                    'color' => '#64748B',
                    'bg_badge' => 'border-slate-500/30 bg-slate-500/10 text-slate-800',
                ];
            }
        }

        // 5. Fulfillment Type Breakdown
        $fulfillmentBreakdownRaw = (clone $paid_orders_query)
            ->whereNotIn('status', ['cancelled'])
            ->selectRaw('order_type, COUNT(*) as count, SUM(COALESCE(final_payable, total_price)) as sum_amount')
            ->groupBy('order_type')
            ->get();

        $fulfillmentStats = [
            'dine_in' => ['label' => 'سرو در سالن کافه', 'count' => 0, 'sum' => 0],
            'courier' => ['label' => 'ارسال با پیک (شیراز)', 'count' => 0, 'sum' => 0],
            'takeaway' => ['label' => 'بیرون‌بر حضوری', 'count' => 0, 'sum' => 0],
        ];
        foreach ($fulfillmentBreakdownRaw as $item) {
            $key = $item->order_type ?? 'dine_in';
            if (isset($fulfillmentStats[$key])) {
                $fulfillmentStats[$key]['count'] = (int) $item->count;
                $fulfillmentStats[$key]['sum'] = (float) $item->sum_amount;
            }
        }

        // 6. Product Performance Breakdown
        $paidItemCounts = OrderItem::whereHas('order', function ($q) use ($cafeId, $startDate, $endDate) {
            $q->where('cafe_id', $cafeId)->where('type', 'normal')->whereNotIn('status', ['cancelled']);
            if ($startDate && $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate]);
            } elseif ($startDate) {
                $q->where('created_at', '>=', $startDate);
            } elseif ($endDate) {
                $q->where('created_at', '<=', $endDate);
            }
        })->groupBy('product_id')->selectRaw('product_id, SUM(quantity) as total_qty')->pluck('total_qty', 'product_id');

        $freeItemCounts = SubscriptionUsage::where('cafe_id', $cafeId)
            ->when($startDate && $endDate, fn ($q) => $q->whereBetween('used_date', [$startDate->toDateString(), $endDate->toDateString()]))
            ->when($startDate && !$endDate, fn ($q) => $q->where('used_date', '>=', $startDate->toDateString()))
            ->when(!$startDate && $endDate, fn ($q) => $q->where('used_date', '<=', $endDate->toDateString()))
            ->groupBy('product_id')->selectRaw('product_id, COUNT(*) as total_qty')->pluck('total_qty', 'product_id');

        $products = Product::where('cafe_id', $cafeId)->with('category')->get();
        $product_stats = $products->map(function ($product) use ($paidItemCounts, $freeItemCounts) {
            $paid_qty = (int) ($paidItemCounts[$product->id] ?? 0);
            $free_qty = (int) ($freeItemCounts[$product->id] ?? 0);
            $total_qty = $paid_qty + $free_qty;
            $revenue = $paid_qty * (float) $product->price;

            return (object) [
                'id' => $product->id,
                'name' => $product->name,
                'category_name' => $product->category?->name ?? 'عمومی',
                'type' => $product->type,
                'price' => $product->price,
                'paid_qty' => $paid_qty,
                'free_qty' => $free_qty,
                'total_qty' => $total_qty,
                'revenue' => $revenue,
            ];
        })->sortByDesc('total_qty')->values();

        $top_selling_products = $product_stats->where('total_qty', '>', 0)->take(5)->values();

        // 7. Trend Chart Data (Last 14 days or filtered period)
        $chartDays = 14;
        $chartStart = $startDate ? (clone $startDate)->startOfDay() : Carbon::now()->subDays(13)->startOfDay();
        $chartEnd = $endDate ? (clone $endDate)->endOfDay() : Carbon::now()->endOfDay();

        // If span is more than 31 days or period is all, default chart to last 14 days for neatness
        if ($periodKey === 'all' || $chartStart->diffInDays($chartEnd) > 31) {
            $chartStart = Carbon::now()->subDays(13)->startOfDay();
            $chartEnd = Carbon::now()->endOfDay();
        }

        $dailyRevenues = Order::where('cafe_id', $cafeId)
            ->where('type', 'normal')
            ->whereNotIn('status', ['cancelled'])
            ->whereBetween('created_at', [$chartStart, $chartEnd])
            ->selectRaw('DATE(created_at) as order_date, SUM(COALESCE(final_payable, total_price)) as sum_price')
            ->groupBy('order_date')
            ->pluck('sum_price', 'order_date');

        $dailyFreeCounts = SubscriptionUsage::where('cafe_id', $cafeId)
            ->whereBetween('used_date', [$chartStart->toDateString(), $chartEnd->toDateString()])
            ->selectRaw('used_date, COUNT(*) as count_usage')
            ->groupBy('used_date')
            ->pluck('count_usage', 'used_date');

        $chart_labels = [];
        $chart_revenue_data = [];
        $chart_free_data = [];

        $diffDays = (int) $chartStart->diffInDays($chartEnd);
        for ($i = 0; $i <= $diffDays; ++$i) {
            $currentDate = (clone $chartStart)->addDays($i);
            $dateStr = $currentDate->format('Y-m-d');
            $chart_labels[] = JalaliHelper::format($dateStr, 'Y/m/d');
            $chart_revenue_data[] = (float) ($dailyRevenues[$dateStr] ?? 0);
            $chart_free_data[] = (int) ($dailyFreeCounts[$dateStr] ?? 0);
        }

        // 8. Monthly Performance Breakdown (Past 12 months)
        $twelveMonthsAgo = Carbon::now()->subMonths(11)->startOfMonth();
        $allPaidOrders = Order::where('cafe_id', $cafeId)
            ->where('type', 'normal')
            ->whereNotIn('status', ['cancelled'])
            ->where('created_at', '>=', $twelveMonthsAgo)
            ->get(['created_at', 'total_price', 'final_payable']);

        $monthlyPaidRevenues = $allPaidOrders->groupBy(fn ($order) => Carbon::parse($order->created_at)->format('Y-m'))
            ->map(fn ($orders) => (float) $orders->sum(fn ($o) => $o->final_payable ?: $o->total_price));

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
            'periodKey',
            'periodLabel',
            'rawStart',
            'rawEnd',
            'total_revenue',
            'subscription_revenue',
            'paid_order_revenue',
            'total_subscriptions_count',
            'paid_orders_count',
            'successful_orders_count',
            'cancelled_orders_count',
            'total_discount_amount',
            'total_delivery_fees',
            'avg_order_value',
            'total_free_coffees_delivered',
            'estimated_free_coffee_value',
            'paymentStats',
            'fulfillmentStats',
            'product_stats',
            'top_selling_products',
            'chart_labels',
            'chart_revenue_data',
            'chart_free_data',
            'monthly_usage'
        ));
    }

    /**
     * Export Orders to CSV (Excel-compatible with UTF-8 BOM, supports date filtering)
     */
    public function exportOrders(Request $request)
    {
        $cafeId = auth()->user()->cafe_id ?? 1;

        [$startDate, $endDate, $periodLabel] = $this->resolveDateRange($request);

        $ordersQuery = Order::where('cafe_id', $cafeId)
            ->where('type', 'normal')
            ->with(['customer', 'latestPayment', 'deliveryAddress'])
            ->latest('id');

        if ($startDate && $endDate) {
            $ordersQuery->whereBetween('created_at', [$startDate, $endDate]);
        } elseif ($startDate) {
            $ordersQuery->where('created_at', '>=', $startDate);
        } elseif ($endDate) {
            $ordersQuery->where('created_at', '<=', $endDate);
        }

        $statusLabels = [
            'pending' => 'در انتظار تایید',
            'awaiting_payment_verification' => 'در انتظار تایید واریز کارت به کارت',
            'preparing' => 'در حال آماده‌سازی',
            'processing' => 'در حال پردازش',
            'ready' => 'آماده تحویل/ارسال',
            'shipping' => 'تحویل به پیک',
            'out_for_delivery' => 'در مسیر ارسال',
            'completed' => 'تکمیل و تحویل شده',
            'cancelled' => 'لغو شده',
        ];

        $orderTypeLabels = [
            'dine_in' => 'حضوری در سالن کافه',
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
     * Export Products Sales Breakdown to CSV (supports date filtering)
     */
    public function exportProducts(Request $request)
    {
        $cafeId = auth()->user()->cafe_id ?? 1;

        [$startDate, $endDate, $periodLabel] = $this->resolveDateRange($request);

        $paidItemCounts = OrderItem::whereHas('order', function ($q) use ($cafeId, $startDate, $endDate) {
            $q->where('cafe_id', $cafeId)->where('type', 'normal')->whereNotIn('status', ['cancelled']);
            if ($startDate && $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate]);
            } elseif ($startDate) {
                $q->where('created_at', '>=', $startDate);
            } elseif ($endDate) {
                $q->where('created_at', '<=', $endDate);
            }
        })->groupBy('product_id')->selectRaw('product_id, SUM(quantity) as total_qty')->pluck('total_qty', 'product_id');

        $freeItemCounts = SubscriptionUsage::where('cafe_id', $cafeId)
            ->when($startDate && $endDate, fn ($q) => $q->whereBetween('used_date', [$startDate->toDateString(), $endDate->toDateString()]))
            ->when($startDate && !$endDate, fn ($q) => $q->where('used_date', '>=', $startDate->toDateString()))
            ->when(!$startDate && $endDate, fn ($q) => $q->where('used_date', '<=', $endDate->toDateString()))
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
