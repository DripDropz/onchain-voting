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
            UserSeeder::class,
            PetitionSeeder::class,
            AdminUserSeeder::class,
            QuestionSeeder::class,
            VotingPowerSeeder::class,
            PolicySeeder::class,
            BallotSeeder::class,
            PollSeeder::class,
        ]);
    }
}
