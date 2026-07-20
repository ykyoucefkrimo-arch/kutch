<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\NewArrival;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;

// Section « Nouveautés » : jusqu'à 3 entrées independantes des produits,
// avec le meme schema de champs qu'une fiche produit (prix, stock, images,
// matiere...), mais totalement dediees a l'affichage "Nouveautés" (aucun
// lien avec la table products).
class NewArrivalController extends Controller
{
    public const MAX = 3;

    public function index()
    {
        $newArrivals = NewArrival::with('category')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $categories = Category::where('is_active', true)->orderBy('name')->get();

        return Inertia::render('Admin/NewArrivals/Index', [
            'newArrivals' => $newArrivals,
            'max'         => self::MAX,
        ]);
    }

    public function create()
    {
        if (NewArrival::count() >= self::MAX) {
            return redirect()->route('admin.new-arrivals.index')
                ->with('error', 'Vous ne pouvez créer que '.self::MAX.' nouveautés. Retirez-en une d\'abord.');
        }

        $categories = Category::where('is_active', true)->orderBy('name')->get();

        return Inertia::render('Admin/NewArrivals/Form', ['categories' => $categories]);
    }

    public function store(Request $request)
    {
        if (NewArrival::count() >= self::MAX) {
            return back()->with('error', 'Vous ne pouvez créer que '.self::MAX.' nouveautés. Retirez-en une d\'abord.');
        }

        $data = $request->validate([
            'name'              => 'required|string|max:255',
            'category_id'       => 'nullable|exists:categories,id',
            'description'       => 'nullable|string',
            'short_description' => 'nullable|string|max:500',
            'price'             => 'required|integer|min:0',
            'price_promo'       => 'nullable|integer|min:0',
            'sku'               => 'nullable|string|max:100|unique:new_arrivals,sku',
            'stock'             => 'required|integer|min:0',
            'in_stock'          => 'boolean',
            'is_custom'         => 'boolean',
            'material'          => 'nullable|string|max:255',
            'color'             => 'nullable|string|max:255',
            'delivery_days'     => 'integer|min:1',
            'sort_order'        => 'integer',
            'main_image'        => 'required|image|max:2048',
            'images.*'          => 'nullable|image|max:2048',
        ]);

        $data['slug'] = Str::slug($data['name']).'-'.uniqid();

        $data['main_image'] = $request->file('main_image')->store('new-arrivals', 'public');
        if ($request->hasFile('images')) {
            $data['images'] = collect($request->file('images'))->map(
                fn ($f) => $f->store('new-arrivals', 'public')
            )->toArray();
        }

        NewArrival::create($data);

        return redirect()->route('admin.new-arrivals.index')->with('success', 'Nouveauté créée avec succès.');
    }

    public function edit(NewArrival $newArrival)
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();

        return Inertia::render('Admin/NewArrivals/Form', [
            'newArrival' => $newArrival,
            'categories' => $categories,
        ]);
    }

    public function update(Request $request, NewArrival $newArrival)
    {
        $data = $request->validate([
            'name'              => 'required|string|max:255',
            'category_id'       => 'nullable|exists:categories,id',
            'description'       => 'nullable|string',
            'short_description' => 'nullable|string|max:500',
            'price'             => 'required|integer|min:0',
            'price_promo'       => 'nullable|integer|min:0',
            'sku'               => 'nullable|string|max:100|unique:new_arrivals,sku,'.$newArrival->id,
            'stock'             => 'required|integer|min:0',
            'in_stock'          => 'boolean',
            'is_custom'         => 'boolean',
            'material'          => 'nullable|string|max:255',
            'color'             => 'nullable|string|max:255',
            'delivery_days'     => 'integer|min:1',
            'sort_order'        => 'integer',
            'main_image'        => 'nullable|image|max:2048',
            'images.*'          => 'nullable|image|max:2048',
            'delete_images'     => 'nullable',
        ]);

        if ($request->hasFile('main_image')) {
            if ($newArrival->main_image) {
                Storage::disk('public')->delete($newArrival->main_image);
            }
            $data['main_image'] = $request->file('main_image')->store('new-arrivals', 'public');
        } else {
            unset($data['main_image']);
        }

        // Conserver les images existantes par defaut.
        unset($data['images']);

        if ($request->filled('delete_images')) {
            $toDelete = is_array($request->delete_images) ? $request->delete_images : json_decode($request->delete_images, true);
            $existing = $newArrival->images ?? [];
            foreach ($toDelete as $path) {
                Storage::disk('public')->delete($path);
            }
            $data['images'] = array_values(array_filter($existing, fn ($p) => ! in_array($p, $toDelete)));
        }

        if ($request->hasFile('images')) {
            $newImages = collect($request->file('images'))->map(
                fn ($f) => $f->store('new-arrivals', 'public')
            )->toArray();
            $existing = $data['images'] ?? ($newArrival->images ?? []);
            $data['images'] = array_merge($existing, $newImages);
        }

        $newArrival->update($data);

        return redirect()->route('admin.new-arrivals.index')->with('success', 'Nouveauté mise à jour.');
    }

    public function destroy(NewArrival $newArrival)
    {
        if ($newArrival->main_image) {
            Storage::disk('public')->delete($newArrival->main_image);
        }
        foreach ($newArrival->images ?? [] as $img) {
            Storage::disk('public')->delete($img);
        }
        $newArrival->delete();

        return back()->with('success', 'Nouveauté retirée.');
    }
}
