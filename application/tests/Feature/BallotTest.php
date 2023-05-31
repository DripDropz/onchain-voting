<?php

use App\DataTransferObjects\BallotData;
use App\Http\Controllers\Admin\BallotController;
use App\Models\Ballot;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertTrue;


test('test super-admin can create ballot', function () {
    $superAdmin = asSuperAdmin();

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
    $admin = asAdmin();

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
    $user = asUser();

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
    $superAdmin = asSuperAdmin('superAdminTestDelete', 'superAdminTestDelete@gmail.com');

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
    $admin = asAdmin('adminTestDelete', 'adminTestDelete@gmail.com');

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
    $user = asUser('userTestDelete', 'userTestDelete@gmail.com');

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
    asSuperAdmin('startedAtFuture', 'super-admin-startedAtFuture@gmail.com');

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
    asSuperAdmin('startedAtPast', 'super-admin-startedAtPast@gmail.com');

    $existingBallot = asExistingBallot(Carbon::now($tz = 'UTC')->subMonth(2));
    $payload = array_merge($existingBallot->toArray(), ['title' => "updated ballot title", 'description' => 'updated ballot description', 'version' =>  (string) random_int(1, 199)]);
    $mockRequest = Request::create('ballots/' . $existingBallot->hash . '/update', 'PATCH', $payload);
    $response = (new BallotController)->update(BallotData::from($mockRequest), $existingBallot);
    $updatedBallot = Ballot::byHash($existingBallot->hash);

    // assert ballot update never occurred
    assertEquals(302, $response->status());
    assertEquals($existingBallot->title, $updatedBallot->title);


});


