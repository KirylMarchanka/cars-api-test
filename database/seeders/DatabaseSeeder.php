<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        User::query()->create([
            'name' => 'Test Account',
            'email' => 'test.acc@example.net',
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
        ]);
         \App\Models\User::factory(10)->create();
        \App\Models\CarBrand::factory(10)->create();
        \App\Models\CarModel::factory(10)->create();
    }
}
