<?php
namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category')->where('is_active', true);

        if ($request->filled('category')) {
            $query->whereHas('category', fn($q) => $q->where('slug', $request->category));
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(fn($q) => $q->where('name', 'like', "%$search%")
                ->orWhere('description', 'like', "%$search%"));
        }
        if ($request->filled('min_price')) {
            $query->where('price', '>=', (int)$request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', (int)$request->max_price);
        }
        if ($request->filter === 'promo') {
            $query->whereNotNull('price_promo');
        }
        if ($request->filter === 'custom') {
            $query->where('is_custom', true);
        }

        $sort = $request->sort ?? 'default';
        match ($sort) {
            'price_asc'  => $query->orderBy('price'),
            'price_desc' => $query->orderByDesc('price'),
            'newest'     => $query->latest(),
            default      => $query->orderBy('sort_order')->orderBy('name'),
        };

        $products = $query->paginate(12)->withQueryString();
        $categories = Category::where('is_active', true)->orderBy('sort_order')->get();

        return Inertia::render('Shop/Products/Index', [
            'products' => $products,
            'categories' => $categories,
            'filters' => $request->only(['category', 'search', 'min_price', 'max_price', 'filter', 'sort']),
        ]);
    }

    public function show(string $slug)
    {
        $product = Product::with('category')
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $related = Product::with('category')
            ->where('is_active', true)
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->take(4)
            ->get();

        return Inertia::render('Shop/Products/Show', [
            'product' => $product,
            'relatedProducts' => $related,
        ]);
    }
}
