<?php

namespace Database\Seeders;

use App\Models\Snapshot;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SnapshotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Snapshot::factory()->count(5)->create();
    }
}
