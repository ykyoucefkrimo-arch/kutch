<?php
namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@ebeniste.dz'],
            [
                'name'     => 'Admin',
                'password' => bcrypt('Admin@2026'),
                'is_admin' => true,
            ]
        );

        $this->command->info('Admin user seeded successfully.');
    }
}
