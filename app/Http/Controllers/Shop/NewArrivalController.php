<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\NewArrival;
use Inertia\Inertia;

// Page publique dediee a chaque nouveauté (comme une fiche produit), meme si
// l'entree n'est pas un vrai produit du catalogue.
class NewArrivalController extends Controller
{
    public function show(string $slug)
    {
        $newArrival = NewArrival::with('category')
            ->where('slug', $slug)
            ->firstOrFail();

        return Inertia::render('Shop/NewArrivals/Show', [
            'newArrival' => $newArrival,
        ]);
    }
}
