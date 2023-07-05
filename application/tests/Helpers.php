<?php

use App\Models\Ballot;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

function asExistingBallot(Carbon $startDateTime)
{
    return Ballot::factory()->create([
        'description' => 'createdAt description sentence',
        'version' => '1',
        'status' => 'published',
        'type' => 'snapshot',
        'started_at' => $startDateTime,
    ]);
}

function asSuperAdmin(string $name = 'super-adminName', string $email = 'super-admin@gmail.com')
{
    Auth::logout();
    $user = User::where('id', asSuperAdminId($name, $email))->first();
    Auth::login($user);

    return $user;
}

function asAdmin(string $name = 'adminName', string $email = 'admin@gmail.com')
{
    Auth::logout();
    $user = User::where('id', asAdminId($name, $email))->first();
    Auth::login($user);

    return $user;
}

function asUser(string $name = 'userName', string $email = 'user@gmail.com')
{
    Auth::logout();
    $user = User::where('id', asUserId($name, $email))->first();
    Auth::login($user);

    return $user;
}

function asSuperAdminId(string $name, string $email): int
{
    // create user and role
    User::factory()->create(['name' => $name, 'email' => $email]);
    if (DB::table('roles')->where('name', 'super-admin')->count() == 0) {
        DB::table('roles')->insert(['name' => 'super-admin', 'guard_name' => 'web']);
    }

    $superAdmin = DB::table('users')->where('email', $email)->first();
    $saRole = Role::where('name', 'super-admin')->first();
    DB::table('model_has_roles')
        ->insert([
            'role_id' => $saRole->id,
            'model_type' => 'App\Models\User',
            'model_id' => $superAdmin->id,
        ]);

    return $superAdmin->id;
};

function asAdminId(string $name, string $email): int
{
    // create user and role
    User::factory()->create(['name' => $name, 'email' => $email]);
    if (DB::table('roles')->where('name', 'admin')->count() == 0) {
        DB::table('roles')->insert(['name' => 'admin', 'guard_name' => 'web']);
    }

    // assign user 'admin role'
    $admin = DB::table('users')->where('email', $email)->first();
    $aRole = Role::where('name', 'admin')->first();
    DB::table('model_has_roles')
        ->insert([
            'role_id' => $aRole->id,
            'model_type' => 'App\Models\User',
            'model_id' => $admin->id,
        ]);

    return $admin->id;
};

function asUserId(string $name, string $email): int
{
    // create user and role
    User::factory()->create(['name' => $name, 'email' => $email]);
    if (DB::table('roles')->where('name', 'user')->count() == 0) {
        DB::table('roles')->insert(['name' => 'user', 'guard_name' => 'web']);
    }

    // assign user 'user role'
    $user = DB::table('users')->where('email', $email)->first();
    $uRole = Role::where('name', 'user')->first();
    DB::table('model_has_roles')
        ->insert([
            'role_id' => $uRole->id,
            'model_type' => 'App\Models\User',
            'model_id' => $user->id,
        ]);

    return $user->id;
}
