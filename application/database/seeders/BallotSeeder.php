<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use App\Models\Ballot;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class BallotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Ballot::factory()->create();
    }
}
