<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Subscription;
use App\Models\SubscriptionUsage;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $cafeId = auth()->user()->cafe_id;

        $todayOrdersCount = Order::where('cafe_id', $cafeId)
            ->whereDate('created_at', $today)
            ->count();

        $todayRevenue = (float) Order::where('cafe_id', $cafeId)
            ->where('type', 'normal')
            ->whereDate('created_at', $today)
            ->sum('total_price');

        $avgOrderValue = $todayOrdersCount > 0 ? round($todayRevenue / $todayOrdersCount) : 0;

        $todayNewCustomers = Customer::where('cafe_id', $cafeId)
            ->whereDate('created_at', $today)
            ->count();

        $stats = [
            'total_customers' => Customer::where('cafe_id', $cafeId)->count(),
            'today_new_customers' => $todayNewCustomers,
            'active_subscriptions' => Subscription::where('cafe_id', $cafeId)
                ->where('status', 'active')
                ->where('expire_date', '>=', $today->toDateString())
                ->where('remaining_items', '>', 0)
                ->count(),
            'today_free_coffees' => SubscriptionUsage::where('cafe_id', $cafeId)
                ->where('used_date', $today->toDateString())
                ->count(),
            'today_paid_revenue' => $todayRevenue,
            'today_orders_count' => $todayOrdersCount,
            'avg_order_value' => $avgOrderValue,
        ];

        // Weekly Sales Calculation (Single aggregated query)
        $sevenDaysAgo = Carbon::today()->subDays(6)->startOfDay();
        $dailyTotals = Order::where('cafe_id', $cafeId)
            ->where('type', 'normal')
            ->where('created_at', '>=', $sevenDaysAgo)
            ->get(['created_at', 'total_price'])
            ->groupBy(fn ($order) => Carbon::parse($order->created_at)->toDateString())
            ->map(fn ($orders) => (float) $orders->sum('total_price'));

        $weeklySales = [];
        $maxDailySales = 1;
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $dateStr = $date->toDateString();
            $jalaliDayName = match ($date->dayOfWeek) {
                Carbon::SATURDAY => 'شنبه',
                Carbon::SUNDAY => 'یکشنبه',
                Carbon::MONDAY => 'دوشنبه',
                Carbon::TUESDAY => 'سه‌شنبه',
                Carbon::WEDNESDAY => 'چهارشنبه',
                Carbon::THURSDAY => 'پنجشنبه',
                Carbon::FRIDAY => 'جمعه',
            };

            $dailyTotal = (float) ($dailyTotals[$dateStr] ?? 0.0);

            if ($dailyTotal > $maxDailySales) {
                $maxDailySales = $dailyTotal;
            }

            $weeklySales[] = [
                'day' => $jalaliDayName,
                'date' => $date->format('m/d'),
                'amount' => $dailyTotal,
                'is_today' => ($i === 0),
            ];
        }

        $todayUsages = SubscriptionUsage::where('cafe_id', $cafeId)
            ->where('used_date', $today->toDateString())
            ->with(['customer', 'subscription.plan', 'product', 'creator'])
            ->latest()
            ->take(10)
            ->get();

        $recentSubscriptions = Subscription::where('cafe_id', $cafeId)
            ->with(['customer', 'plan'])
            ->latest()
            ->take(5)
            ->get();

        $recentOrders = Order::where('cafe_id', $cafeId)
            ->with(['customer', 'items.product', 'creator'])
            ->latest()
            ->take(6)
            ->get();

        return view('dashboard', compact('stats', 'todayUsages', 'recentSubscriptions', 'recentOrders', 'weeklySales', 'maxDailySales'));
    }
}
