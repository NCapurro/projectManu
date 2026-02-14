<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Show;
use App\Models\Province;
use App\Models\City;

use Illuminate\Support\Facades\Storage;


use Inertia\Inertia;

class ShowController extends Controller
{
    public function index()
{
    // El with() es clave para que city y province no lleguen null
    $shows = \App\Models\Show::with('city.province')
                ->where('esta_publicado', true)
                ->get();

    return \Inertia\Inertia::render('Welcome', [
        'shows' => $shows,
        'canLogin' => \Illuminate\Support\Facades\Route::has('login'),
    ]);
}

    public function adminIndex()
{
    // En el admin queremos ver TODOS, incluso los no publicados
    $shows = Show::with('city.province')->orderBy('fecha_hora', 'desc')->get();
    
    return Inertia::render('Dashboard', [
        'shows' => $shows
    ]);
}

 public function create()
{
    // Necesitamos las ciudades y provincias para los selects del formulario
    return Inertia::render('Shows/Create', [
        'provinces' => Province::with('cities')->get()
    ]);
}



public function store(Request $request)
{
    $validated = $request->validate([
        'titulo'         => 'nullable|string|max:100',
        'lugar'          => 'required|string|max:255',
        'fecha_hora'     => 'required|date',
        'city_id'        => 'required|exists:cities,id',
        'ticket_url'     => 'required|url',
        'esta_publicado' => 'required|boolean',
        'flyer'          => 'nullable|image|mimes:jpg,jpeg,png|max:8192', // Max 2MB
    ]);

    // Lógica para guardar la imagen
    if ($request->hasFile('flyer')) {
        $path = $request->file('flyer')->store('flyers', 'public');
        $validated['flyer_path'] = $path;
    }

    Show::create($validated);

    return redirect()->route('dashboard')->with('message', '¡Show creado con imagen!');
}

public function destroy(Show $show)
{
    // Borramos el show de la base de datos
    $show->delete();

    // Redireccionamos atrás (al Dashboard) con el mensaje flash
    return redirect()->back()->with('message', 'El show fue eliminado correctamente.');
}

public function edit(Show $show)
{
    // Cargamos el show con su ciudad y provincia para que el frontend sepa dónde está parado
    $show->load('city.province');

    return Inertia::render('Shows/Edit', [
        'show' => $show,
        'provinces' => \App\Models\Province::with('cities')->get()
    ]);
}

public function update(Request $request, Show $show)
    {
        $validated = $request->validate([
            'titulo'         => 'nullable|string|max:100',
            'lugar'          => 'required|string|max:255',
            'fecha_hora'     => 'required|date',
            'city_id'        => 'required|exists:cities,id',
            'ticket_url'     => 'required|url',
            'esta_publicado' => 'required|boolean',
            'sold_out'       => 'required|boolean',
            'flyer'          => 'nullable|image|mimes:jpg,jpeg,png|max:8192', // Max 8MB
        ]);

        // GESTIÓN DEL FLYER
        if ($request->hasFile('flyer')) {
            // 1. Borramos el flyer anterior si existe en el disco 'public'
            if ($show->flyer_path) {
                Storage::disk('public')->delete($show->flyer_path);
            }

            // 2. Guardamos el nuevo flyer
            $path = $request->file('flyer')->store('flyers', 'public');
            $validated['flyer_path'] = $path;
        }

        // Actualizamos los datos del show
        $show->update($validated);

        return redirect()->route('dashboard')->with('message', '¡Show actualizado con éxito!');
    }

public function toggleSoldOut(Show $show)
{
    $show->update([
        'sold_out' => !$show->sold_out
    ]);

    return redirect()->back()->with('message', 'Estado de entradas actualizado.');
}
}
