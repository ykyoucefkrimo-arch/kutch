<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category')->withTrashed();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('name', 'like', "%$s%")->orWhere('sku', 'like', "%$s%"));
        }
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->filter === 'deleted') {
            $query->onlyTrashed();
        } elseif ($request->filter !== 'all') {
            $query->whereNull('deleted_at');
        }

        $products = $query->orderBy('sort_order')->orderBy('name')->paginate(20)->withQueryString();
        $categories = Category::where('is_active', true)->orderBy('name')->get();

        return Inertia::render('Admin/Products/Index', [
            'products' => $products,
            'categories' => $categories,
            'filters' => $request->only(['search', 'category_id', 'filter']),
        ]);
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        return Inertia::render('Admin/Products/Form', ['categories' => $categories]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'              => 'required|string|max:255',
            'category_id'       => 'nullable|exists:categories,id',
            'description'       => 'nullable|string',
            'short_description' => 'nullable|string|max:500',
            'price'             => 'required|integer|min:0',
            'price_promo'       => 'nullable|integer|min:0',
            'sku'               => 'nullable|string|max:100|unique:products,sku',
            'stock'             => 'required|integer|min:0',
            'in_stock'          => 'boolean',
            'is_custom'         => 'boolean',
            'is_active'         => 'boolean',
            'is_featured'       => 'boolean',
            'material'          => 'nullable|string|max:255',
            'color'             => 'nullable|string|max:255',
            'delivery_days'     => 'integer|min:1',
            'sort_order'        => 'integer',
            'main_image'        => 'nullable|image|max:2048',
            'images.*'          => 'nullable|image|max:2048',
        ]);

        $data['slug'] = Str::slug($data['name']) . '-' . uniqid();

        if ($request->hasFile('main_image')) {
            $data['main_image'] = $request->file('main_image')->store('products', 'public');
        }
        if ($request->hasFile('images')) {
            $data['images'] = collect($request->file('images'))->map(
                fn($f) => $f->store('products', 'public')
            )->toArray();
        }

        Product::create($data);
        return redirect()->route('admin.products.index')->with('success', 'Produit créé avec succès.');
    }

    public function edit(Product $product)
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        return Inertia::render('Admin/Products/Form', [
            'product' => $product,
            'categories' => $categories,
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'name'              => 'required|string|max:255',
            'category_id'       => 'nullable|exists:categories,id',
            'description'       => 'nullable|string',
            'short_description' => 'nullable|string|max:500',
            'price'             => 'required|integer|min:0',
            'price_promo'       => 'nullable|integer|min:0',
            'sku'               => 'nullable|string|max:100|unique:products,sku,'.$product->id,
            'stock'             => 'required|integer|min:0',
            'in_stock'          => 'boolean',
            'is_custom'         => 'boolean',
            'is_active'         => 'boolean',
            'is_featured'       => 'boolean',
            'material'          => 'nullable|string|max:255',
            'color'             => 'nullable|string|max:255',
            'delivery_days'     => 'integer|min:1',
            'sort_order'        => 'integer',
            'main_image'        => 'nullable|image|max:2048',
            'images.*'          => 'nullable|image|max:2048',
            'delete_images'     => 'nullable',
        ]);

        // Ne pas écraser main_image si aucun fichier envoyé et pas de suppression demandée
        if (!$request->hasFile('main_image') && !$request->boolean('delete_main_image')) {
            unset($data['main_image']);
        } elseif ($request->boolean('delete_main_image') && $product->main_image) {
            Storage::disk('public')->delete($product->main_image);
            $data['main_image'] = null;
        } elseif ($request->hasFile('main_image')) {
            if ($product->main_image) Storage::disk('public')->delete($product->main_image);
            $data['main_image'] = $request->file('main_image')->store('products', 'public');
        }

        // Conserver les images existantes par défaut
        unset($data['images']);

        // Supprimer les images individuelles cochées
        if ($request->filled('delete_images')) {
            $toDelete = is_array($request->delete_images) ? $request->delete_images : json_decode($request->delete_images, true);
            $existing = $product->images ?? [];
            foreach ($toDelete as $path) {
                Storage::disk('public')->delete($path);
            }
            $data['images'] = array_values(array_filter($existing, fn($p) => !in_array($p, $toDelete)));
        }

        if ($request->hasFile('images')) {
            $newImages = collect($request->file('images'))->map(
                fn($f) => $f->store('products', 'public')
            )->toArray();
            $existing = $data['images'] ?? ($product->images ?? []);
            $data['images'] = array_merge($existing, $newImages);
        }

        $product->update($data);
        return redirect()->route('admin.products.index')->with('success', 'Produit mis à jour.');
    }

    public function destroy(Product $product)
    {
        $product->delete(); // soft delete
        return back()->with('success', 'Produit archivé.');
    }

    public function restore(int $id)
    {
        Product::withTrashed()->findOrFail($id)->restore();
        return back()->with('success', 'Produit restauré.');
    }
}
