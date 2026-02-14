<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GeoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
{
    $country = \App\Models\Country::create(['name' => 'Argentina', 'code' => 'ARG']);

    $er = \App\Models\Province::create(['name' => 'Entre Ríos', 'country_id' => $country->id]);
    $sf = \App\Models\Province::create(['name' => 'Santa Fe', 'country_id' => $country->id]);

    \App\Models\City::create(['name' => 'Paraná', 'province_id' => $er->id]);
    \App\Models\City::create(['name' => 'Concordia', 'province_id' => $er->id]);
    \App\Models\City::create(['name' => 'Santa Fe', 'province_id' => $sf->id]);
    \App\Models\City::create(['name' => 'Rosario', 'province_id' => $sf->id]);
}
}
