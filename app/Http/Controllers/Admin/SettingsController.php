<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class SettingsController extends Controller
{
    private string $cacheKey = 'shop_settings';

    public function index()
    {
        $settings = Cache::get($this->cacheKey, self::defaults());
        $settings['footer_image_url'] = self::footerImageUrl($settings);
        return Inertia::render('Admin/Settings', ['settings' => $settings]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'shop_name'     => 'required|string|max:255',
            'shop_phone'    => 'required|string|max:50',
            'shop_email'    => 'nullable|email',
            'shop_address'  => 'nullable|string|max:500',
            'footer_text'   => 'nullable|string|max:1000',
            'whatsapp'      => 'nullable|string|max:50',
            'facebook'      => 'nullable|url|max:255',
            'instagram'     => 'nullable|url|max:255',
            'linkedin'      => 'nullable|url|max:255',
            'spotify'       => 'nullable|url|max:255',
            'footer_image'  => 'nullable|image|max:5120',
        ]);

        $current = Cache::get($this->cacheKey, self::defaults());
        $data['footer_image'] = $current['footer_image'] ?? '';

        if ($request->hasFile('footer_image')) {
            if (!empty($current['footer_image'])) {
                Storage::disk('public')->delete($current['footer_image']);
            }
            $data['footer_image'] = $request->file('footer_image')->store('footer', 'public');
        }

        Cache::forever($this->cacheKey, $data);
        return back()->with('success', 'Paramètres enregistrés.');
    }

    // Statique et publique : réutilisée par HandleInertiaRequests pour partager
    // les réglages (contact/réseaux sociaux) à TOUTES les pages boutique (footer).
    public static function defaults(): array
    {
        return [
            'shop_name'     => 'Ébéniste Algérie',
            'shop_phone'    => '+213 (0) 550 01 32 44',
            'shop_email'    => 'contact@kutch.dz',
            'shop_address'  => '20 Mohamed Tahar SEMANI, Hydra, Alger',
            'footer_text'   => 'Artisan ébéniste — Meubles sur mesure de qualité.',
            'whatsapp'      => '',
            'facebook'      => '',
            'instagram'     => '',
            'linkedin'      => '',
            'spotify'       => '',
            'footer_image'  => '',
        ];
    }

    public static function current(): array
    {
        return Cache::get('shop_settings', self::defaults());
    }

    public static function footerImageUrl(?array $settings = null): ?string
    {
        $settings ??= self::current();
        return !empty($settings['footer_image']) ? asset('storage/' . $settings['footer_image']) : null;
    }
}
