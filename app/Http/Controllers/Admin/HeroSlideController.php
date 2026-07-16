<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HeroSlide;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class HeroSlideController extends Controller
{
    public function index()
    {
        $slides = HeroSlide::orderBy('sort_order')->orderBy('id')->get()
            ->map(fn($s) => array_merge($s->toArray(), ['image_url' => $s->image_url]));

        return Inertia::render('Admin/HeroSlides/Index', [
            'slides' => $slides,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'image'    => 'required|image|max:5120',
            'caption'  => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'link'     => 'nullable|string|max:255',
        ]);

        $path = $request->file('image')->store('hero', 'public');

        HeroSlide::create([
            'image_path' => $path,
            'caption'    => $data['caption'] ?? null,
            'subtitle'   => $data['subtitle'] ?? null,
            'link'       => $data['link'] ?? null,
            'sort_order' => HeroSlide::max('sort_order') + 1,
            'is_active'  => true,
        ]);

        return back()->with('success', 'Slide ajoutée avec succès.');
    }

    public function update(Request $request, HeroSlide $heroSlide)
    {
        $data = $request->validate([
            'caption'  => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'link'     => 'nullable|string|max:255',
            'image'    => 'nullable|image|max:5120',
        ]);

        if ($request->hasFile('image')) {
            Storage::disk('public')->delete($heroSlide->image_path);
            $data['image_path'] = $request->file('image')->store('hero', 'public');
            unset($data['image']);
        }

        $heroSlide->update($data);

        return back()->with('success', 'Slide mise à jour.');
    }

    public function toggleActive(HeroSlide $heroSlide)
    {
        $heroSlide->update(['is_active' => !$heroSlide->is_active]);
        return back()->with('success', $heroSlide->is_active ? 'Slide activée.' : 'Slide désactivée.');
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'ids'   => 'required|array',
            'ids.*' => 'integer|exists:hero_slides,id',
        ]);

        foreach ($request->ids as $order => $id) {
            HeroSlide::where('id', $id)->update(['sort_order' => $order]);
        }

        return response()->json(['success' => true]);
    }

    public function destroy(HeroSlide $heroSlide)
    {
        Storage::disk('public')->delete($heroSlide->image_path);
        $heroSlide->delete();
        return back()->with('success', 'Slide supprimée.');
    }
}
