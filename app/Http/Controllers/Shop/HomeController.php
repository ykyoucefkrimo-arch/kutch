<?php
namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function index()
    {
        $featuredProducts = Product::with('category')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->paginate(20);

        $categories = Category::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $newProducts = Product::with('category')
            ->where('is_active', true)
            ->latest()
            ->take(4)
            ->get();

        return Inertia::render('Shop/Home', [
            'featuredProducts' => $featuredProducts,
            'categories' => $categories,
            'newProducts' => $newProducts,
        ]);
    }
}
