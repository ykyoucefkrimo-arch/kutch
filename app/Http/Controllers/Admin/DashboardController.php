<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Carbon\Carbon;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();

        $stats = [
            'orders_today'    => Order::whereDate('created_at', $today)->count(),
            'orders_month'    => Order::where('created_at', '>=', $thisMonth)->count(),
            'revenue_today'   => Order::whereDate('created_at', $today)->where('status', 'delivered')->sum('total'),
            'revenue_month'   => Order::where('created_at', '>=', $thisMonth)->where('status', 'delivered')->sum('total'),
            'pending_orders'  => Order::where('status', 'pending')->count(),
            'flagged_orders'  => Order::where('is_flagged', true)->whereIn('status', ['pending', 'confirmed'])->count(),
            'total_products'  => Product::where('is_active', true)->count(),
            'out_of_stock'    => Product::where('in_stock', false)->where('is_active', true)->count(),
        ];

        $recentOrders = Order::with(['wilaya'])
            ->latest()
            ->take(10)
            ->get();

        $ordersByStatus = Order::selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        return Inertia::render('Admin/Dashboard', [
            'stats' => $stats,
            'recentOrders' => $recentOrders,
            'ordersByStatus' => $ordersByStatus,
        ]);
    }
}
