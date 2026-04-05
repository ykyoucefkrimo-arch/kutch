<?php
namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Commune;
use App\Models\Wilaya;
use Illuminate\Http\JsonResponse;

class LocationController extends Controller
{
    public function wilayas(): JsonResponse
    {
        $wilayas = Wilaya::orderBy('code')->get(['id', 'code', 'name']);
        return response()->json($wilayas);
    }

    public function communes(int $wilayaId): JsonResponse
    {
        $communes = Commune::where('wilaya_id', $wilayaId)
            ->orderBy('name')
            ->get(['id', 'name']);
        return response()->json($communes);
    }
}
