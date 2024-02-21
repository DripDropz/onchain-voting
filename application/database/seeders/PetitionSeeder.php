<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Petition;
use App\Models\Signature;
use App\Models\User;
use Illuminate\Database\Seeder;
use Random\RandomException;

class PetitionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * @throws RandomException
     */
    public function run(): void
    {
        Petition::factory(40)
            ->recycle(User::factory()->count(1), 'user')
            ->hasAttached(
                Signature::factory()->count(random_int(3, 33)),
                [
                    'model_type' => Petition::class
                ]
            )
            ->hasAttached(
                Category::factory()->count(3),
                [
                    'model_type' => Petition::class
                ]
            )
            ->create();
    }
}
