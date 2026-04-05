<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BannedIp;
use App\Models\Order;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AntifraudController extends Controller
{
    public function index()
    {
        $flaggedOrders = Order::with(['wilaya'])
            ->where('is_flagged', true)
            ->latest()
            ->paginate(20);

        $bannedIps = BannedIp::latest()->paginate(20);

        return Inertia::render('Admin/Antifraud/Index', [
            'flaggedOrders' => $flaggedOrders,
            'bannedIps' => $bannedIps,
        ]);
    }

    public function banIp(Request $request)
    {
        $data = $request->validate([
            'ip_address' => 'required|ip',
            'reason'     => 'nullable|string|max:255',
            'expires_at' => 'nullable|date|after:now',
        ]);
        BannedIp::updateOrCreate(['ip_address' => $data['ip_address']], $data);
        return back()->with('success', 'IP bannie avec succès.');
    }

    public function unbanIp(BannedIp $bannedIp)
    {
        $bannedIp->delete();
        return back()->with('success', 'IP débannie.');
    }

    public function unflagOrder(Order $order)
    {
        $order->update(['is_flagged' => false]);
        return back()->with('success', 'Commande démarquée.');
    }
}
