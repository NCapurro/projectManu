<?php

namespace App\Http\Controllers;

use App\Models\CarouselImage;
use App\Models\Setting;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class CarouselImageController extends Controller
{
    public function index()
    {
        $title = Setting::where('key', 'hero_title')->value('value') ?? '';
    $subtitle = Setting::where('key', 'hero_subtitle')->value('value') ?? '';
    
        return Inertia::render('Admin/Carousel/Index', [
            'images' => CarouselImage::latest()->get(),
            'title' => $title,
            'subtitle' => $subtitle
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpg,jpeg,png|max:4096', // Máx 4MB
        ]);

        $path = $request->file('image')->store('carousel', 'public');

        CarouselImage::create([
            'image_path' => $path,
            'is_active' => true,
        ]);

        return back()->with('message', 'Imagen subida correctamente.');
    }

    public function toggle(CarouselImage $image)
    {
        $image->update(['is_active' => !$image->is_active]);
        return back();
    }

    public function destroy(CarouselImage $image)
    {
        Storage::disk('public')->delete($image->image_path);
        $image->delete();
        return back()->with('message', 'Imagen eliminada.');
    }

    public function updateTexts(Request $request)
{
    $request->validate([
        'hero_title' => 'nullable|string|max:255',
        'hero_subtitle' => 'nullable|string|max:255',
    ]);

    // Usamos updateOrCreate: Si existe la clave, la actualiza. Si no, la crea.
    Setting::updateOrCreate(
        ['key' => 'hero_title'],
        ['value' => $request->hero_title]
    );

    Setting::updateOrCreate(
        ['key' => 'hero_subtitle'],
        ['value' => $request->hero_subtitle]
    );

    return redirect()->back()->with('message', 'Textos actualizados.');
}

}