<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OrderController extends Controller
{
    public function __construct(private OrderService $orderService) {}

    public function index(Request $request)
    {
        $query = Order::with(['wilaya', 'commune'])->withTrashed();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('order_number', 'like', "%$s%")
                ->orWhere('customer_name', 'like', "%$s%")
                ->orWhere('customer_phone', 'like', "%$s%"));
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filter === 'flagged') {
            $query->where('is_flagged', true);
        }
        if ($request->filter === 'deleted') {
            $query->onlyTrashed();
        } else {
            $query->whereNull('orders.deleted_at');
        }

        $orders = $query->latest()->paginate(20)->withQueryString();

        return Inertia::render('Admin/Orders/Index', [
            'orders' => $orders,
            'filters' => $request->only(['search', 'status', 'filter']),
            'statusOptions' => ['pending','confirmed','processing','shipped','delivered','cancelled'],
        ]);
    }

    public function show(Order $order)
    {
        $order->load(['wilaya', 'commune', 'items.product', 'logs.user']);
        return Inertia::render('Admin/Orders/Show', ['order' => $order]);
    }

    public function updateStatus(Request $request, Order $order)
    {
        $data = $request->validate([
            'status'  => 'required|in:pending,confirmed,processing,shipped,delivered,cancelled',
            'comment' => 'nullable|string|max:500',
        ]);
        $this->orderService->updateStatus($order, $data['status'], $data['comment'] ?? null, auth()->id());
        return back()->with('success', 'Statut mis à jour.');
    }

    public function destroy(Order $order)
    {
        $order->delete();
        return back()->with('success', 'Commande archivée.');
    }
}
