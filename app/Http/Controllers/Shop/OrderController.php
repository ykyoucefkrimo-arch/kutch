<?php
namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Models\Order;
use App\Services\AntifraudService;
use App\Services\OrderService;
use Inertia\Inertia;

class OrderController extends Controller
{
    public function __construct(
        private AntifraudService $antifraud,
        private OrderService $orderService,
    ) {}

    public function checkout()
    {
        return Inertia::render('Shop/Checkout');
    }

    public function store(StoreOrderRequest $request)
    {
        $data = $request->validated();
        $fraudScore = $this->antifraud->score($request, $data);

        $order = $this->orderService->createOrder(
            $data,
            $fraudScore,
            $request->ip(),
            $request->userAgent() ?? ''
        );

        return Inertia::render('Shop/OrderSuccess', [
            'orderNumber' => $order->order_number,
            'total' => $order->total,
        ]);
    }

    public function track(string $orderNumber)
    {
        $order = Order::with(['wilaya', 'commune', 'items', 'logs'])
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        return Inertia::render('Shop/OrderTracking', [
            'order' => $order,
        ]);
    }
}
