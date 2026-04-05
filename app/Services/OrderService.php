<?php
namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderLog;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function createOrder(array $data, int $fraudScore, string $ip, string $userAgent): Order
    {
        return DB::transaction(function () use ($data, $fraudScore, $ip, $userAgent) {
            $subtotal = 0;
            $cartItems = $data['items'];
            $productSnapshots = [];

            foreach ($cartItems as $item) {
                $product = Product::findOrFail($item['product_id']);
                $price = $product->price_promo ?? $product->price;
                $qty = (int) $item['quantity'];
                $sub = $price * $qty;
                $subtotal += $sub;
                $productSnapshots[] = [
                    'product' => $product,
                    'quantity' => $qty,
                    'unit_price' => $price,
                    'subtotal' => $sub,
                    'options' => $item['options'] ?? null,
                ];
            }

            $shippingCost = 0; // livraison gratuite ou à calculer plus tard
            $total = $subtotal + $shippingCost;

            $order = Order::create([
                'order_number'       => Order::generateOrderNumber(),
                'customer_name'      => $data['customer_name'],
                'customer_phone'     => $data['customer_phone'],
                'wilaya_id'          => $data['wilaya_id'],
                'commune_id'         => $data['commune_id'],
                'address'            => null,
                'payment_method'     => 'cash_on_delivery',
                'subtotal'           => $subtotal,
                'shipping_cost'      => $shippingCost,
                'total'              => $total,
                'ip_address'         => $ip,
                'user_agent'         => $userAgent,
                'fraud_score'        => $fraudScore,
                'is_flagged'         => $fraudScore >= 50,
                'honeypot_triggered' => !empty($data['_hp_field']),
                'status'             => 'pending',
            ]);

            foreach ($productSnapshots as $snap) {
                OrderItem::create([
                    'order_id'       => $order->id,
                    'product_id'     => $snap['product']->id,
                    'product_name'   => $snap['product']->name,
                    'unit_price'     => $snap['unit_price'],
                    'quantity'       => $snap['quantity'],
                    'subtotal'       => $snap['subtotal'],
                    'product_options'=> $snap['options'],
                ]);
            }

            OrderLog::create([
                'order_id'   => $order->id,
                'status_from'=> null,
                'status_to'  => 'pending',
                'comment'    => 'Commande créée',
            ]);

            return $order;
        });
    }

    public function updateStatus(Order $order, string $newStatus, ?string $comment = null, ?int $adminId = null): void
    {
        $oldStatus = $order->status;
        $order->update(['status' => $newStatus]);
        OrderLog::create([
            'order_id'    => $order->id,
            'status_from' => $oldStatus,
            'status_to'   => $newStatus,
            'comment'     => $comment,
            'user_id'     => $adminId,
        ]);
    }
}
