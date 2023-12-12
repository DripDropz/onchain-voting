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
            // RoleSeeder::class,
            PermissionSeeder::class,
            UserSeeder::class,
            AdminUserSeeder::class,
            BallotSeeder::class,
            // AdminUserSeeder::class,
            QuestionSeeder::class,
            VotingPowerSeeder::class,
            PolicySeeder::class,
            BallotSeeder::class
        ]);
    }
}
