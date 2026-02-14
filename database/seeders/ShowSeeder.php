<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\City;
use App\Models\Show;
use Faker\Factory as Faker;

class ShowSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('es_AR'); // Usamos Faker en español de Argentina
        $cities = City::all();

        // Si no hay ciudades, no podemos crear shows
        if ($cities->isEmpty()) {
            $this->command->warn("No hay ciudades en la DB. Corré primero el GeoSeeder.");
            return;
        }

        for ($i = 0; $i < 25; $i++) {
            Show::create([
                'titulo'         => $faker->randomElement(['Especial de Verano', 'Noche de Stand Up', 'Manu Horazzi Live', 'Humor y Birra', null]),
                'lugar'          => $faker->randomElement(['Teatro ', 'Centro Cultural ', 'Bar ', 'Club ']) . $faker->lastName,
                'direccion'      => $faker->streetAddress,
                'city_id'        => $cities->random()->id, // Elige una ciudad al azar de las que ya existen
                'fecha_hora'     => $faker->dateTimeBetween('now', '+6 months'),
                'ticket_url'     => 'https://plateavia.com.ar/' . $faker->slug,
                'esta_publicado' => true,
                'sold_out'       => $faker->boolean(20), // 20% de probabilidad de que esté agotado
            ]);
        }

        $this->command->info("¡Se crearon 25 shows de prueba con éxito!");
    }


}
