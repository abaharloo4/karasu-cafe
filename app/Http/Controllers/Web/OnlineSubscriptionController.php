<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\SubscriptionPlan;
use App\Services\Payment\ZarinpalGatewayService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OnlineSubscriptionController extends Controller
{
    /**
     * Add subscription plan to cart and redirect to shopping cart.
     */
    public function purchase(SubscriptionPlan $plan, Request $request)
    {
        return redirect()->route('cart.index', ['add_plan' => $plan->id])
            ->with('success', 'پلن «' . $plan->name . '» به سبد خرید شما افزوده شد.');
    }
}
