<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SocialNetwork;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

class SocialNetworkController extends Controller
{
    public function index()
    {
        // 1. Obtenemos todas las redes
        $networks = SocialNetwork::all();

        // 2. Armamos el "Pool" leyendo directamente los archivos físicos del disco.
        // Storage::disk('public')->files('social_icons') devuelve un array con las rutas de los archivos
        // en esa carpeta, por ejemplo: ['social_icons/icono1.png', 'social_icons/icono2.svg']
        
        $availableIcons = [];
        
        // Verificamos si la carpeta existe para evitar errores
        if (Storage::disk('public')->exists('social_icons')) {
            $availableIcons = Storage::disk('public')->files('social_icons');
        }

        return Inertia::render('Admin/Redes', [
            'networks' => $networks,
            'availableIcons' => $availableIcons
        ]);
    }

    public function store(Request $request)
    {
        // ... (El código de store queda exactamente igual que en la versión anterior) ...
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'url'  => 'required|url|max:255',
            'icon_file' => 'nullable|image|mimes:jpeg,png,jpg,svg,webp|max:2048',
            'selected_icon_path' => 'nullable|string',
        ]);

        $iconPath = null;

        if ($request->hasFile('icon_file')) {
            $iconPath = $request->file('icon_file')->store('social_icons', 'public');
        } elseif ($request->filled('selected_icon_path')) {
            $iconPath = $request->selected_icon_path;
        }

        SocialNetwork::create([
            'name' => $validated['name'],
            'url' => $validated['url'],
            'icon' => $iconPath,
        ]);

        return redirect()->back()->with('message', 'Red social agregada.');
    }

    public function destroy(SocialNetwork $socialNetwork)
    {
        // ... (El código de destroy queda exactamente igual) ...
        $socialNetwork->delete();
        return redirect()->back()->with('message', 'Red eliminada.');
    }
}