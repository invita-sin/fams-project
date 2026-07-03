<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(RoleSeeder::class);

        if (!User::where('email', 'admin@fams.test')->exists()) {
            $superAdmin = User::factory()->create([
                'name' => 'Admin Utama',
                'email' => 'admin@fams.test',
                'password' => bcrypt('password'),
            ]);

            $superAdmin->assignRole('super_admin');
        }

        $this->call(DummyDataSeeder::class);
    }
}
