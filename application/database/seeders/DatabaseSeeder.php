<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            CategorySeeder::class,
            PermissionSeeder::class,
            AdminUserSeeder::class,
            UserSeeder::class,
            PetitionSeeder::class,
            PolicySeeder::class,
            BallotSeeder::class,
            VotingPowerSeeder::class,
            PollSeeder::class,
        ]);
    }
}
