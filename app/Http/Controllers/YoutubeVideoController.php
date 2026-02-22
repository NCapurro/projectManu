<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\YoutubeVideo;
use Inertia\Inertia;

class YoutubeVideoController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Videos', [
            // Traemos los videos ordenados por el último agregado
            'videos' => YoutubeVideo::latest()->get() 
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'url'   => 'required|url',
        ]);

        // Magia Analítica: Extraemos el ID del video de YouTube de cualquier formato de URL
        preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i', $request->url, $match);

        if (!isset($match[1])) {
            return redirect()->back()->withErrors(['url' => 'El enlace de YouTube no es válido.']);
        }

        YoutubeVideo::create([
            'title' => $request->title,
            'video_id' => $match[1], // Guardamos solo los 11 caracteres
        ]);

        return redirect()->back()->with('message', 'Video agregado correctamente.');
    }

    public function destroy(YoutubeVideo $video)
    {
        $video->delete();
        return redirect()->back()->with('message', 'Video eliminado.');
    }
}