<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Subscription;

class CustomerService
{
    /**
     * Create a new customer record.
     */
    public function createCustomer(int $cafeId, array $data): Customer
    {
        return Customer::create([
            'cafe_id' => $cafeId,
            'name' => $data['name'],
            'phone' => $data['phone'],
            'email' => $data['email'] ?? null,
            'birthdate' => $data['birthdate'] ?? null,
            'address' => $data['address'] ?? null,
        ]);
    }

    /**
     * Update an existing customer.
     */
    public function updateCustomer(Customer $customer, array $data): Customer
    {
        $payload = [
            'name' => $data['name'] ?? $customer->name,
            'phone' => $data['phone'] ?? $customer->phone,
            'email' => $data['email'] ?? $customer->email,
            'address' => $data['address'] ?? $customer->address,
        ];

        if (array_key_exists('birthdate', $data)) {
            $payload['birthdate'] = $data['birthdate'];
        }

        $customer->update($payload);

        return $customer;
    }

    /**
     * Normalize Persian text (convert Arabic Yeh/Kaf to Persian, standard spaces, digits).
     */
    public static function normalizePersianText(?string $text): string
    {
        if (empty($text)) {
            return '';
        }

        $persianDigits = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        $arabicDigits = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
        $englishDigits = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];

        $text = str_replace($persianDigits, $englishDigits, $text);
        $text = str_replace($arabicDigits, $englishDigits, $text);

        // Normalize Yeh and Kaf and Zero-width space
        $text = str_replace(['ي', 'ى'], 'ی', $text);
        $text = str_replace('ك', 'ک', $text);
        $text = str_replace('ة', 'ه', $text);
        $text = str_replace(["\u{200C}", '‌'], ' ', $text); // Half-space to space

        return trim(preg_replace('/\s+/', ' ', $text));
    }

    /**
     * Search customer by query (phone number or name) within a specific cafe.
     */
    public function searchCustomer(int $cafeId, string $term): ?Customer
    {
        $cleanTerm = trim($term);
        $cleanPhone = preg_replace('/[^0-9]/', '', $cleanTerm);
        $normalizedTerm = self::normalizePersianText($cleanTerm);

        return Customer::where('cafe_id', $cafeId)
            ->where(function ($query) use ($cleanTerm, $cleanPhone, $normalizedTerm) {
                if (!empty($cleanPhone)) {
                    $query->where('phone', $cleanTerm)
                        ->orWhere('phone', $cleanPhone)
                        ->orWhere('phone', 'like', "%{$cleanPhone}%");
                }
                $query->orWhere('name', 'like', "%{$cleanTerm}%")
                    ->orWhere('name', 'like', "%{$normalizedTerm}%");
            })
            ->with(['subscriptions.plan', 'subscriptionUsages.product'])
            ->first();
    }

    /**
     * Search customer by phone number within a specific cafe (Backward compatible alias).
     */
    public function searchCustomerByPhone(int $cafeId, string $phone): ?Customer
    {
        return $this->searchCustomer($cafeId, $phone);
    }

    /**
     * Get detailed profile data for a customer.
     */
    public function getCustomerProfile(Customer $customer): array
    {
        $customer->load(['tier', 'addresses.deliveryZone', 'subscriptions.plan.products', 'subscriptionUsages.product', 'orders.items.product', 'loyaltyPoints']);

        $activeSubscription = $customer->subscriptions()
            ->where('status', 'active')
            ->where('expire_date', '>=', now()->toDateString())
            ->where('remaining_items', '>', 0)
            ->with(['plan.products'])
            ->latest()
            ->first();

        return [
            'customer' => $customer,
            'active_subscription' => $activeSubscription,
            'all_subscriptions' => $customer->subscriptions()->with('plan')->latest()->get(),
            'usage_history' => $customer->subscriptionUsages()->with('product')->latest()->take(30)->get(),
            'recent_orders' => $customer->orders()->with('items.product')->latest()->take(20)->get(),
            'loyalty_history' => $customer->loyaltyPoints()->latest()->take(20)->get(),
        ];
    }
}
