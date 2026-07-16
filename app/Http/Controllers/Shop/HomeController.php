<?php
namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\HeroSlide;
use App\Models\Product;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function index()
    {
        $heroSlides = HeroSlide::active()
            ->orderBy('sort_order')
            ->get()
            ->map(fn($s) => array_merge($s->toArray(), ['image_url' => $s->image_url]));

        // Nouveautés choisies par l'admin (max 3). Repli : les 3 produits les plus récents
        // tant qu'aucune nouveauté n'a été sélectionnée (la section reste jamais vide).
        $newProducts = Product::with('category')
            ->where('is_active', true)
            ->where('is_new', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->take(3)
            ->get();

        if ($newProducts->isEmpty()) {
            $newProducts = Product::with('category')
                ->where('is_active', true)
                ->latest()
                ->take(3)
                ->get();
        }

        $categories = Category::where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->map(fn($c) => array_merge($c->toArray(), ['image_url' => $c->image_url]));

        $newProductIds = $newProducts->pluck('id');

        $featuredProducts = Product::with('category')
            ->where('is_active', true)
            ->whereNotIn('id', $newProductIds)
            ->orderBy('sort_order')
            ->paginate(20);

        return Inertia::render('Shop/Home', [
            'heroSlides'       => $heroSlides,
            'featuredProducts' => $featuredProducts,
            'categories'       => $categories,
            'newProducts'      => $newProducts,
        ]);
    }
}
