<?php

namespace App\Http\Controllers;

use illuminate\Support\Facades\Route;

use Illuminate\Http\Request;
use App\Models\City;
use App\Models\Province;
use App\Models\Show;
use App\Models\CarouselImage;
use App\Models\YoutubeVideo;
use App\Models\Setting;

use Inertia\Inertia;


class WelcomeController extends Controller
{
   public function index(Request $request)
{

    $heroImage = CarouselImage::where('is_active', true)->first();
    $imageUrl = $heroImage ? asset('storage/' . $heroImage->image_path) : asset('img/default.jpg');

    $query = \App\Models\Show::with(['city.province'])
        ->where('esta_publicado', true)
        ->where('fecha_hora', '>=', now())
        ->orderBy('fecha_hora', 'asc');

    // Filtro por Provincia (a través de la relación con City)
    if ($request->filled('province')) {
        $query->whereHas('city', function($q) use ($request) {
            $q->where('province_id', $request->province);
        });
    }

    // Filtro por Ciudad
    if ($request->filled('city')) {
        $query->where('city_id', $request->city);
    }

    // Filtro por Fecha específica
    if ($request->filled('date')) {
        $query->whereDate('fecha_hora', $request->date);
    }

    // Obtenemos solo las imágenes activas
    $carouselImages = CarouselImage::where('is_active', true)->get();

    return Inertia::render('Welcome', [
        'shows' => $query->paginate(10)->withQueryString(),
        'provinces' => Province::with('cities')->get(),
        'filters' => $request->only(['province', 'city', 'date']),
        'carouselImages' => $carouselImages,
        'videos' => YoutubeVideo::latest()->take(3)->get(),
        'hero_title' => Setting::where('key', 'hero_title')->value('value') ?? 'STAND UP',
        'hero_subtitle' => Setting::where('key', 'hero_subtitle')->value('value') ?? 'GIRA 2026'
    ])->withViewData([
        'meta_title' => 'Manu Horazzi - Stand Up | Gira 2026',
        'meta_description' => 'Conseguí tus entradas para los próximos shows de Manu Horazzi. ¡No te quedes afuera!',
        'meta_keywords' => 'Manu Horazzi, Stand Up, Comedy, Argentina, Shows, Entradas',
        'meta_image' => $imageUrl
    ]);
}
}
