<?php
namespace App\Services;

use App\Models\BannedIp;
use App\Models\Order;
use Illuminate\Http\Request;

class AntifraudService
{
    public function score(Request $request, array $data): int
    {
        $score = 0;
        $ip = $request->ip();

        // IP bannie
        if (BannedIp::where('ip_address', $ip)
            ->where(fn($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()))
            ->exists()) {
            return 100;
        }

        // Trop de commandes depuis cette IP dans les dernières 24h
        $recentOrders = Order::where('ip_address', $ip)
            ->where('created_at', '>', now()->subDay())
            ->count();
        if ($recentOrders >= 5) $score += 40;
        elseif ($recentOrders >= 3) $score += 20;

        // Même numéro de téléphone, plusieurs commandes pending
        $phone = $data['customer_phone'] ?? '';
        $pendingByPhone = Order::where('customer_phone', $phone)
            ->whereIn('status', ['pending', 'confirmed'])
            ->count();
        if ($pendingByPhone >= 3) $score += 30;
        elseif ($pendingByPhone >= 2) $score += 15;

        // Montant suspect (très élevé)
        $total = $data['total'] ?? 0;
        if ($total > 500000) $score += 10;

        // User-agent absent ou suspect
        $ua = $request->userAgent() ?? '';
        if (empty($ua) || strlen($ua) < 10) $score += 15;

        // Honeypot
        if (!empty($data['_hp_field'])) $score += 50;

        return min($score, 100);
    }
}
