<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Country;
use App\Models\Province;
use App\Models\City;

class GeoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
{
    $country = Country::create(['name' => 'Argentina', 'code' => 'ARG']);
   
  
    $ba = Province::create(['name' => 'Buenos Aires', 'country_id' => $country->id]);
    $cat = Province::create(['name' => 'Catamarca', 'country_id' => $country->id]);
    $cha = Province::create(['name' => 'Chaco', 'country_id' => $country->id]);
    $chu = Province::create(['name' => 'Chubut', 'country_id' => $country->id]);
    $cba = Province::create(['name' => 'Córdoba', 'country_id' => $country->id]);
    $cor = Province::create(['name' => 'Corrientes', 'country_id' => $country->id]);
    $er = Province::create(['name' => 'Entre Ríos', 'country_id' => $country->id]);
    $fo = Province::create(['name' => 'Formosa', 'country_id' => $country->id]);
    $juj = Province::create(['name' => 'Jujuy', 'country_id' => $country->id]);
    $lp = Province::create(['name' => 'La Pampa', 'country_id' => $country->id]);
    $lr = Province::create(['name' => 'La Rioja', 'country_id' => $country->id]);
    $mza = Province::create(['name' => 'Mendoza', 'country_id' => $country->id]);
    $mis = Province::create(['name' => 'Misiones', 'country_id' => $country->id]);
    $neu = Province::create(['name' => 'Neuquén', 'country_id' => $country->id]);
    $rn = Province::create(['name' => 'Río Negro', 'country_id' => $country->id]);
    $sa = Province::create(['name' => 'Salta', 'country_id' => $country->id]);
    $sf = Province::create(['name' => 'Santa Fe', 'country_id' => $country->id]);
    $sgo = Province::create(['name' => 'Santiago del Estero', 'country_id' => $country->id]);
    $sl = Province::create(['name' => 'San Luis', 'country_id' => $country->id]);
    $sj = Province::create(['name' => 'San Juan', 'country_id' => $country->id]);
    $sc = Province::create(['name' => 'Santa Cruz', 'country_id' => $country->id]);
    $tuc = Province::create(['name' => 'Tucumán', 'country_id' => $country->id]);
    $ti = Province::create(['name' => 'Tierra del Fuego', 'country_id' => $country->id]);

    City::create(['name' => 'Paraná', 'province_id' => $er->id]);
    City::create(['name' => 'Concordia', 'province_id' => $er->id]);
    City::create(['name' => 'Santa Fe', 'province_id' => $sf->id]);
    City::create(['name' => 'Rosario', 'province_id' => $sf->id]);
}
}
