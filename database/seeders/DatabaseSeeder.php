<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Nicolas Admin',
            'email' => 'nicolascapurro23@gmail.com',
            'password' => bcrypt('Reiayanami_2398')
        ]);
        
        User::factory()->create([
            'name' => 'Manu Admin',
            'email' => 'valkirian@gmail.com',
            'password' => bcrypt('manuhorazzi')
        ]);


        $this->call(GeoSeeder::class);
        
    }
}
