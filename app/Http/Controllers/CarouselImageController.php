<?php

namespace App\Http\Controllers;

use App\Models\CarouselImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class CarouselImageController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Carousel/Index', [
            'images' => CarouselImage::latest()->get()
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
}