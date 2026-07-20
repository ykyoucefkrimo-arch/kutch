<?php
namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\HeroSlide;
use App\Models\NewArrival;
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

        // Nouveautés : entrées independantes des produits, saisies par l'admin
        // (nom/categorie/image propres, aucun lien avec la table products).
        $newArrivals = NewArrival::with('category')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $categories = Category::where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->map(fn($c) => array_merge($c->toArray(), ['image_url' => $c->image_url]));

        $featuredProducts = Product::with('category')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->paginate(20);

        return Inertia::render('Shop/Home', [
            'heroSlides'       => $heroSlides,
            'featuredProducts' => $featuredProducts,
            'categories'       => $categories,
            'newArrivals'      => $newArrivals,
        ]);
    }
}
