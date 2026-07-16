<?php

namespace App\Http\Middleware;

use App\Http\Controllers\Admin\SettingsController;
use App\Models\HeroSlide;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user(),
            ],
            'flash' => fn () => [
                'success' => $request->session()->get('success'),
                'error'   => $request->session()->get('error'),
            ],
            'storageUrl' => rtrim(config('app.url'), '/') . '/storage',
            'appUrl'     => rtrim(config('app.url'), '/'),
            // Partagés globalement pour le footer (formulaire de contact + réseaux
            // sociaux), présent sur toutes les pages boutique — évite de dupliquer
            // cette requête dans chaque contrôleur.
            'settings' => fn () => SettingsController::current(),
            'footerImage' => function () {
                // Priorité à l'image dédiée au footer (Admin → Paramètres) ; à défaut,
                // on retombe sur le 1er Hero Slide actif pour ne jamais casser un footer déjà configuré.
                $footerImage = SettingsController::footerImageUrl();
                if ($footerImage) {
                    return $footerImage;
                }
                $path = HeroSlide::active()->orderBy('sort_order')->value('image_path');
                return $path ? asset('storage/' . $path) : null;
            },
        ];
    }
}
