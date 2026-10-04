<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\CustomerTier;

class LoyaltyTierService
{
    /**
     * Re-evaluate customer tier based on total spent.
     */
    public function evaluateTier(Customer $customer): ?CustomerTier
    {
        $totalSpent = (float) $customer->total_spent;

        $eligibleTier = CustomerTier::where('min_spend', '<=', $totalSpent)
            ->orderByDesc('min_spend')
            ->first();

        if ($eligibleTier && $customer->tier_id !== $eligibleTier->id) {
            $customer->update(['tier_id' => $eligibleTier->id]);
        }

        return $eligibleTier;
    }
}
