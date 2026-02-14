<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('shows', function (Blueprint $table) {
            $table->id();
            $table->string('titulo')->nullable();     // Por si el show tiene nombre (ej: "Sin Filtro")
            $table->string('lugar');                  // Nombre del teatro/bar
            $table->string('direccion')->nullable();  // Calle y número
            $table->foreignId('city_id')->constrained();                 // Paraná, Santa Fe, etc.
            $table->dateTime('fecha_hora');           // Importantísimo para ordenar el calendario
            $table->string('flyer_path')->nullable(); // Ruta del archivo de imagen
            $table->text('ticket_url');               // Link a la ticketera externa
            $table->boolean('esta_publicado')->default(true); // Para cargar borradores
            $table->boolean('sold_out')->default(false);      // El famoso "Entradas Agotadas"
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shows');
    }
};
