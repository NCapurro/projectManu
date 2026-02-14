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
        Schema::create('social_networks', function (Blueprint $table) {
    $table->id();
    $table->string('name'); // Ej: Instagram, TikTok
    $table->string('url');  // Ej: https://instagram.com/manuh...
    $table->string('icon')->nullable(); // Para el nombre de la red o clase de icono
    $table->boolean('is_active')->default(true);
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('social_networks');
    }
};
