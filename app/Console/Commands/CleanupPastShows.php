<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Show;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class CleanupPastShows extends Command
{
    // El nombre del comando que usaremos en la terminal
    protected $signature = 'shows:cleanup';

    // La descripción que aparecerá en la consola
    protected $description = 'Elimina los shows que ya pasaron y borra sus flyers del disco para liberar espacio.';

    public function handle()
    {
        // 1. Buscamos todos los shows cuya fecha ya pasó (es menor a hoy/ahora)
        $pastShows = Show::where('fecha_hora', '<', Carbon::now())->get();

        $count = 0;

        foreach ($pastShows as $show) {
            // 2. Si el show tiene un flyer, lo borramos físicamente del disco
            if ($show->flyer_path) {
                Storage::disk('public')->delete($show->flyer_path);
            }

            // 3. Borramos el registro de la base de datos
            $show->delete();
            $count++;
        }

        // Mensaje de éxito en la consola
        $this->info("Limpieza completada: {$count} shows antiguos eliminados con sus flyers.");
    }
}