<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class OrderService
{
    /**
     * Create a normal paid walk-in order.
     */
    public function createNormalOrder(int $cafeId, ?int $customerId, array $items, int $staffUserId, float $discountAmount = 0.00, string $status = 'preparing'): Order
    {
        return DB::transaction(function () use ($cafeId, $customerId, $items, $staffUserId, $discountAmount, $status) {
            $totalPrice = 0.00;

            $order = Order::create([
                'cafe_id' => $cafeId,
                'customer_id' => $customerId,
                'type' => 'normal',
                'order_type' => 'in_person',
                'payment_method' => 'cash',
                'status' => $status,
                'total_price' => 0.00,
                'discount_amount' => $discountAmount,
                'final_payable' => 0.00,
                'created_by' => $staffUserId,
            ]);

            foreach ($items as $item) {
                $product = Product::where('cafe_id', $cafeId)->findOrFail($item['product_id']);
                $quantity = max(1, (int) $item['quantity']);
                $linePrice = $product->price * $quantity;
                $totalPrice += $linePrice;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'price' => $product->price,
                ]);
            }

            $finalPayable = max(0.00, $totalPrice - $discountAmount);
            $order->update([
                'total_price' => $totalPrice,
                'discount_amount' => $discountAmount,
                'final_payable' => $finalPayable,
            ]);

            return $order->load('items.product');
        });
    }

    /**
     * Create a zero-price subscription order.
     */
    public function createSubscriptionOrder(int $cafeId, int $customerId, int $productId, int $staffUserId): Order
    {
        return DB::transaction(function () use ($cafeId, $customerId, $productId, $staffUserId) {
            $order = Order::create([
                'cafe_id' => $cafeId,
                'customer_id' => $customerId,
                'type' => 'subscription',
                'order_type' => 'in_person',
                'payment_method' => 'cash',
                'status' => 'preparing',
                'total_price' => 0.00,
                'created_by' => $staffUserId,
            ]);

            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $productId,
                'quantity' => 1,
                'price' => 0.00,
            ]);

            return $order->load('items.product');
        });
    }
}
