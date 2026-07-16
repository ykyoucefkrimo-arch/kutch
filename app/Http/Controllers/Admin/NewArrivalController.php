<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

// Section « Nouveautés » : l'admin choisit jusqu'à 3 produits mis en avant
// en tête de la page d'accueil (flag Product::is_new).
class NewArrivalController extends Controller
{
    public const MAX = 3;

    public function index()
    {
        $selected = Product::with('category')
            ->where('is_new', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        // Produits éligibles à l'ajout : actifs et pas déjà en nouveauté.
        $available = Product::where('is_active', true)
            ->where('is_new', false)
            ->orderBy('name')
            ->get(['id', 'name', 'main_image']);

        return Inertia::render('Admin/NewArrivals/Index', [
            'selected'  => $selected,
            'available' => $available,
            'max'       => self::MAX,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
        ]);

        $product = Product::findOrFail($data['product_id']);

        if ($product->is_new) {
            return back()->with('error', 'Ce produit est déjà en nouveauté.');
        }

        if (Product::where('is_new', true)->count() >= self::MAX) {
            return back()->with('error', 'Vous ne pouvez mettre que '.self::MAX.' produits en nouveauté. Retirez-en un d\'abord.');
        }

        if (! $product->is_active) {
            return back()->with('error', 'Un produit inactif ne peut pas être mis en nouveauté.');
        }

        $product->update(['is_new' => true]);

        return back()->with('success', 'Produit ajouté aux nouveautés.');
    }

    public function destroy(Product $product)
    {
        $product->update(['is_new' => false]);

        return back()->with('success', 'Produit retiré des nouveautés.');
    }
}
