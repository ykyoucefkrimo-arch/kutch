<?php
namespace App\Http\Middleware;
use App\Models\BannedIp;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BlockBannedIp {
    public function handle(Request $request, Closure $next): Response {
        $ip = $request->ip();
        $banned = BannedIp::where('ip_address', $ip)
            ->where(fn($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()))
            ->exists();
        if ($banned) {
            abort(403, 'Votre accès a été restreint.');
        }
        return $next($request);
    }
}
