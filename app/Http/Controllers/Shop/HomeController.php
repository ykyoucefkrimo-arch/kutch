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
        $newProducts = Product::with('category')
            ->where('is_active', true)
            ->latest()
            ->take(4)
            ->get();

        $categories = Category::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $newProductIds = $newProducts->pluck('id');

        $featuredProducts = Product::with('category')
            ->where('is_active', true)
            ->whereNotIn('id', $newProductIds)
            ->orderBy('sort_order')
            ->paginate(20);

        return Inertia::render('Shop/Home', [
            'featuredProducts' => $featuredProducts,
            'categories' => $categories,
            'newProducts' => $newProducts,
        ]);
    }
}
