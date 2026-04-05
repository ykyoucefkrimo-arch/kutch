<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;

class SettingsController extends Controller
{
    private string $cacheKey = 'shop_settings';

    public function index()
    {
        $settings = Cache::get($this->cacheKey, $this->defaults());
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
        ]);
        Cache::forever($this->cacheKey, $data);
        return back()->with('success', 'Paramètres enregistrés.');
    }

    private function defaults(): array
    {
        return [
            'shop_name'    => 'Ébéniste Algérie',
            'shop_phone'   => '+213 555 000 000',
            'shop_email'   => '',
            'shop_address' => 'Algérie',
            'footer_text'  => 'Artisan ébéniste — Meubles sur mesure de qualité.',
            'whatsapp'     => '',
            'facebook'     => '',
            'instagram'    => '',
        ];
    }
}
