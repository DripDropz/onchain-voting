<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class RequiredObjectsSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            PermissionSeeder::class,
            AdminUserSeeder::class
        ]);
    }
}
