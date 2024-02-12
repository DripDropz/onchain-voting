<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminUser = User::where('email', env('APP_ADMIN_EMAIL', 'chainvote@dripdropz.io'))->first();

        if (!$adminUser instanceof User) {
            User::factory([
                'name' => env('APP_ADMIN_USERNAME', 'chainvote'),
                'email' => env('APP_ADMIN_EMAIL', 'chainvote@dripdropz.io'),
                'password' => Hash::make(env('APP_ADMIN_PASSWORD', 'ouroboros')),
            ])->hasAttached(Role::where('name', RoleEnum::SUPER_ADMIN)->first())
                ->create();
        }
    }
}
