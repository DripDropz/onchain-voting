<?php

use App\DataTransferObjects\BallotData;
use App\Http\Controllers\BallotController;
use App\Models\Ballot;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertTrue;


test('test super-admin can create ballot', function () {
    Auth::logout();
    $superAdmin = User::where('id', asSuperAdminId())->first();
    Auth::login($superAdmin);

    $payload = ['title' => "super-admin ballot", 'version' => "1", 'status' => 'published', 'type' => 'snapshot'];
    $mockRequest = Request::create('ballots/create', 'POST', $payload);
    $response = (new BallotController)->store(BallotData::from($mockRequest));
    $createdBallot = Ballot::where('version', '1')->first();

    // test if user is as expected
    assertTrue($superAdmin->hasRole('super-admin'));
    assertEquals($superAdmin->name, 'super-adminName');
    expect($superAdmin)->toBeInstanceOf(User::class);

    // assert ballot creation was succesful
    assertEquals(302, $response->status());
    expect(Ballot::where('version', '1')->first())->toBeTruthy();
    assertEquals('super-admin ballot', $createdBallot->title);
});

test('test admin cannot create ballot', function () {
    Auth::logout();
    $admin = User::where('id', asAdminId())->first();
    Auth::login($admin);

    $payload = ['title' => "admin ballot", 'version' => "1", 'status' => 'published', 'type' => 'snapshot'];
    $mockRequest = Request::create('ballots/create', 'POST', $payload);
    $response = (new BallotController)->store(BallotData::from($mockRequest));


    // test if user is as expected
    assertTrue($admin->hasRole('admin'));
    assertEquals($admin->name, 'adminName');
    expect($admin)->toBeInstanceOf(User::class);

    // assert ballot creation was unsuccesful
    assertEquals(302, $response->status());
    expect(Ballot::where('version', 'admin version')->first())->toBeFalsy();
});

test('test user cannot create ballot', function () {
    Auth::logout();
    $user = User::where('id', asUserId())->first();
    Auth::login($user);

    $payload = ['title' => "user ballot", 'version' => '1', 'status' => 'published', 'type' => 'snapshot'];
    $mockRequest = Request::create('ballots/create', 'POST', $payload);
    $response = (new BallotController)->store(BallotData::from($mockRequest));


    // test if user is as expected
    assertTrue($user->hasRole('user'));
    assertEquals($user->name, 'userName');
    expect($user)->toBeInstanceOf(User::class);

    // assert ballot creation was unsuccesful
    assertEquals(302, $response->status());
    expect(Ballot::where('version', 'user version')->first())->toBeFalsy();
});

test('test super-admin can delete ballot', function () {
    Auth::logout();
    $superAdmin = User::where('id', asSuperAdminId('superAdminTestDelete', 'superAdminTestDelete@gmail.com'))->first();
    Auth::login($superAdmin);

    $existingBallot = asExistingBallot(Carbon::now($tz = 'UTC'));
    $mockRequest = Request::create('ballots/' . $existingBallot->hash . '/delete', 'DELETE');
    $response = (new BallotController)->destroy($mockRequest, $ballot = $existingBallot->hash);

    // test if user is as expected
    assertTrue($superAdmin->hasRole('super-admin'));
    assertEquals($superAdmin->name, 'superAdminTestDelete');
    expect($superAdmin)->toBeInstanceOf(User::class);

    // assert ballot deletion was succesful
    assertEquals(302, $response->status());
    expect(Ballot::byHash($existingBallot->hash))->toBeFalsy();
    expect(Ballot::byHash($existingBallot->hash))->toBeFalsy();
});

test('test admin cannot delete ballot', function () {
    Auth::logout();
    $admin = User::where('id', asAdminId('adminTestDelete', 'adminTestDelete@gmail.com'))->first();
    Auth::login($admin);

    $existingBallot = asExistingBallot(Carbon::now($tz = 'UTC'));
    $mockRequest = Request::create('ballots/' . $existingBallot->hash . '/delete', 'DELETE');
    $response = (new BallotController)->destroy($mockRequest, $ballot = $existingBallot->hash);

    // test if user is as expected
    assertTrue($admin->hasRole('admin'));
    assertEquals($admin->name, 'adminTestDelete');
    expect($admin)->toBeInstanceOf(User::class);

    // assert ballot deletion was unsucceful
    assertEquals(302, $response->status());
    expect(Ballot::byHash($existingBallot->hash))->toBeTruthy();
    expect(Ballot::byHash($existingBallot->hash))->toBeTruthy();
});

test('test user cannot delete ballot', function () {
    Auth::logout();
    $user = User::where('id', asUserId('userTestDelete', 'userTestDelete@gmail.com'))->first();
    Auth::login($user);

    $existingBallot = asExistingBallot(Carbon::now($tz = 'UTC'));
    $mockRequest = Request::create('ballots/' . $existingBallot->hash . '/delete', 'DELETE');
    $response = (new BallotController)->destroy($mockRequest, $ballot = $existingBallot->hash);

    // test if user is as expected
    assertTrue($user->hasRole('user'));
    assertEquals($user->name, 'userTestDelete');
    expect($user)->toBeInstanceOf(User::class);

    // assert ballot deletion was unsucceful
    assertEquals(302, $response->status());
    expect(Ballot::byHash($existingBallot->hash))->toBeTruthy();
    expect(Ballot::byHash($existingBallot->hash))->toBeTruthy();
});

test('test ballot can be edited before started_at date', function () {
    Auth::logout();
    $superAdmin = User::where('id', asSuperAdminId('startedAtFuture', 'super-admin-startedAtFuture@gmail.com'))->first();
    Auth::login($superAdmin);

    $existingBallot = asExistingBallot(Carbon::now('UTC'));
    $existingBallot->started_at = Carbon::createFromFormat('Y-m-d H:i:s', "2023-06-05 03:19:07");
    $existingBallot->update();

    $payload = array_merge($existingBallot->toArray(), ['title' => "updated ballot title", 'description' => 'updated ballot description', 'version' => (string) random_int(1, 199)]);
    $mockRequest = Request::create('ballots/' . $existingBallot->hash . '/update', 'PATCH', $payload);
    $response = (new BallotController)->update(BallotData::from($mockRequest), $existingBallot);
    $updatedBallot = Ballot::where('title', $payload['title'])->first();

    // assert ballot update occurred
    assertEquals(302, $response->status());
    assertEquals($payload['title'], $updatedBallot->title);

});

test('test ballot cannot be edited after started_at date', function () {
    Auth::logout();
    $superAdmin = User::where('id', asSuperAdminId('startedAtPast', 'super-admin-startedAtPast@gmail.com'))->first();
    Auth::login($superAdmin);

    $existingBallot = asExistingBallot(Carbon::now($tz = 'UTC')->subMonth(2));
    $payload = array_merge($existingBallot->toArray(), ['title' => "updated ballot title", 'description' => 'updated ballot description', 'version' =>  (string) random_int(1, 199)]);
    $mockRequest = Request::create('ballots/' . $existingBallot->hash . '/update', 'PATCH', $payload);
    $response = (new BallotController)->update(BallotData::from($mockRequest), $existingBallot);
    $updatedBallot = Ballot::byHash($existingBallot->hash);

    // assert ballot update never occurred
    assertEquals(302, $response->status());
    assertEquals($existingBallot->title, $updatedBallot->title);


});

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// helper functions
function asExistingBallot(Carbon $startDateTime)
{
    return Ballot::factory()->create([
        'description' => "createdAt description sentence",
        'version' => "1",
        'status' => 'published',
        'type' => 'snapshot',
        'started_at' => $startDateTime,
    ]);
}

function asSuperAdminId(string $name = 'super-adminName', string $email = "super-admin@gmail.com"): int
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
}

;

function asAdminId(string $name = 'adminName', string $email = "admin@gmail.com"): int
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
}

;

function asUserId(string $name = 'userName', string $email = "user@gmail.com"): int
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



