<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Show;
use App\Models\Province;
use App\Models\City;


use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;


use Inertia\Inertia;

class ShowController extends Controller
{
    public function index(Request $request)
{
    $query = Show::with(['city.province'])
        ->orderBy('fecha_hora', 'desc'); // Los más nuevos o próximos primero

    // Búsqueda en el servidor
    if ($request->filled('search')) {
        $term = '%' . $request->search . '%';
        $query->where(function($q) use ($term) {
            $q->where('lugar', 'like', $term)
              ->orWhere('titulo', 'like', $term)
              ->orWhereHas('city', function($c) use ($term) {
                  $c->where('name', 'like', $term);
              });
        });
    }

    return Inertia::render('Admin/Shows/Index', [
        'shows' => $query->paginate(10)->withQueryString(),
        'filters' => $request->only(['search']),
    ]);
}

    public function adminIndex(Request $request)
    {
        $query = Show::with(['city.province'])
            ->orderBy('fecha_hora', 'desc');

        // Búsqueda en el servidor para el Admin
        if ($request->filled('search')) {
            $term = '%' . $request->search . '%';
            $query->where(function($q) use ($term) {
                $q->where('lugar', 'like', $term)
                  ->orWhere('titulo', 'like', $term)
                  ->orWhereHas('city', function($c) use ($term) {
                      $c->where('name', 'like', $term);
                  });
            });
        }

        return Inertia::render('Dashboard', [
            // IMPORTANTE: Paginar para que coincida con la vista
            'shows' => $query->paginate(10)->withQueryString(),
            // IMPORTANTE: Enviar los filtros para que el input no rompa la página
            'filters' => $request->only(['search']),
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
    // 1. VALIDACIÓN ESTRICTA (La muralla de seguridad)
    $validated = $request->validate([
        'titulo' => 'nullable|string|max:255',
        'lugar' => 'required|string|max:255',
        'direccion' => 'required|string|max:255',
        'fecha_hora' => 'required|date',
        'ticket_url' => 'required|url|max:255',
        'esta_publicado' => 'boolean',
        'flyer' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096', // Máx 4MB, solo imágenes seguras
        
        // Lógica de validación cruzada para Provincias/Ciudades
        'province_id' => 'nullable|exists:provinces,id',
        'new_province_name' => 'required_without:province_id|nullable|string|max:255|unique:provinces,name',
        
        'city_id' => 'nullable|exists:cities,id',
        'new_city_name' => 'required_without:city_id|nullable|string|max:255',
    ]);

    // 2. Lógica para crear Provincia si es nueva
    $provinceId = $request->province_id;
    if (!$provinceId && $request->new_province_name) {
        $formattedProvinceName = Str::title(strtolower($request->new_province_name));
        $province = Province::create(['name' => $formattedProvinceName, 'country_id' => 1]); // Asumimos country_id=1 para simplificar
        $provinceId = $province->id;
    }

    // 3. Lógica para crear Ciudad si es nueva
    $cityId = $request->city_id;
    if (!$cityId && $request->new_city_name) {
        $formattedCityName = Str::title(strtolower($request->new_city_name));
        $city = City::create([
            'name' => $formattedCityName,
            'province_id' => $provinceId
        ]);
        $cityId = $city->id;
    }

    // 4. Procesar la imagen (si subió una)
    $flyerPath = null;
    if ($request->hasFile('flyer')) {
        $flyerPath = $request->file('flyer')->store('flyers', 'public');
    }

    // 5. Guardar el Show de forma segura
    Show::create([
        'titulo' => $validated['titulo'],
        'lugar' => $validated['lugar'],
        'direccion' => $validated['direccion'],
        'city_id' => $cityId,
        'fecha_hora' => $validated['fecha_hora'],
        'ticket_url' => $validated['ticket_url'],
        'esta_publicado' => $request->boolean('esta_publicado'),
        'flyer_path' => $flyerPath,
    ]);

    // Redireccionar con mensaje de éxito
    return redirect()->route('dashboard')->with('success', 'Show creado exitosamente.');
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
            'direccion'      => 'required|string|max:255',
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

public function toggleVisibility(Show $show)
{
    $show->update([
        'esta_publicado' => !$show->esta_publicado
    ]);

    return back()->with('message', $show->esta_publicado ? 'Show publicado en la web.' : 'Show movido a borradores.');
}

}
