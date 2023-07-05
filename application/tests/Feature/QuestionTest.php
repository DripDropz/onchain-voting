<?php

use App\DataTransferObjects\QuestionData;
use App\Enums\ModelStatusEnum;
use App\Enums\QuestionTypeEnum;
use App\Http\Controllers\Admin\BallotController;
use App\Models\Question;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertTrue;

test('test super-admin can create ballot question', function () {
    $superAdmin = asSuperAdmin('superAdminTestQuestionCreate1', 'superAdminTestQuestionCreate@gmail.com');
    $existingBallot = asExistingBallot(Carbon::now($tz = 'UTC'));

    // dd($existingBallot->hash);
    $payload = ['ballot' => $existingBallot, 'ballot_id' => $existingBallot->id, 'title' => 'superAdminTestQuestionCreateTitle', 'status' => ModelStatusEnum::PUBLISHED->value, 'type' => QuestionTypeEnum::SINGLE->value];
    $mockRequest = Request::create('admin/ballots/'.$existingBallot->hash.'/questions/create', 'POST', $payload);
    $response = (new BallotController)->storeQuestion($mockRequest, QuestionData::from($payload));

    // test if user is as expected
    assertTrue($superAdmin->hasRole('super-admin'));
    assertEquals($superAdmin->name, 'superAdminTestQuestionCreate1');
    expect($superAdmin)->toBeInstanceOf(User::class);

    // assert question creation was succesful
    assertEquals(302, $response->status());
    expect(Question::where('title', $payload['title'])->first())->toBeTruthy();
});

test('test admin can create ballot question', function () {
    $admin = asAdmin('adminTestQuestionCreate1', 'adminTestQuestionCreate1@gmail.com');
    $existingBallot = asExistingBallot(Carbon::now($tz = 'UTC'));

    $payload = ['ballot' => $existingBallot, 'title' => 'adminTestQuestionCreateTitle', 'status' => ModelStatusEnum::PUBLISHED->value, 'type' => QuestionTypeEnum::SINGLE->value];
    $mockRequest = Request::create('admin/ballots/'.$existingBallot->hash.'/questions/create', 'POST', $payload);
    $response = (new BallotController)->storeQuestion($mockRequest, QuestionData::from($payload));

    // test if user is as expected
    assertTrue($admin->hasRole('admin'));
    assertEquals($admin->name, 'adminTestQuestionCreate1');
    expect($admin)->toBeInstanceOf(User::class);

    // assert question creation was succesful
    assertEquals(302, $response->status());
    expect(Question::where('title', $payload['title'])->first())->toBeTruthy();
});

test('test user cannot create ballot question', function () {
    $user = asUser('userTestQuestionCreate1', 'userTestQuestionCreate1@gmail.com');
    $existingBallot = asExistingBallot(Carbon::now($tz = 'UTC'));

    $payload = ['ballot' => $existingBallot, 'title' => 'userTestQuestionCreateTitle', 'status' => ModelStatusEnum::PUBLISHED->value, 'type' => QuestionTypeEnum::SINGLE->value];
    $mockRequest = Request::create('admin/ballots/'.$existingBallot->hash.'/questions/create', 'POST', $payload);
    $response = (new BallotController)->storeQuestion($mockRequest, QuestionData::from($payload));

    // test if user is as expected
    assertTrue($user->hasRole('user'));
    assertEquals($user->name, 'userTestQuestionCreate1');
    expect($user)->toBeInstanceOf(User::class);

    // assert question creation was allowed.
    assertEquals(302, $response->status());
    expect(Question::where('title', $payload['title'])->first())->toBeFalsy();
});

test('test super-admin can delete ballot question', function () {
    $superAdmin = asSuperAdmin('superAdminTestQuestionDelete', 'superAdminTestQuestionDelete@gmail.com');
    $existingBallot = asExistingBallot(Carbon::now($tz = 'UTC'));
    $existingQuestion = Question::where('ballot_id', $existingBallot->id)->first();

    $mockRequest = Request::create('admin/ballots/'.$existingBallot->hash.'/questions/delete'.$existingQuestion->id, 'DELETE');
    $response = (new BallotController)->destroyQuestion($mockRequest, $existingBallot, $existingQuestion);

    // test if user is as expected
    assertTrue($superAdmin->hasRole('super-admin'));
    assertEquals($superAdmin->name, 'superAdminTestQuestionDelete');
    expect($superAdmin)->toBeInstanceOf(User::class);

    // assert ballot deletion was succesful
    expect(Question::byHash($existingQuestion->hash))->toBeFalsy();
});

test('test admin can delete ballot question', function () {
    $admin = asAdmin('adminTestQuestionDelete', 'adminTestQuestionDelete@gmail.com');
    $existingBallot = asExistingBallot(Carbon::now($tz = 'UTC'));
    $existingQuestion = Question::where('ballot_id', $existingBallot->id)->first();

    $mockRequest = Request::create('admin/ballots/'.$existingBallot->hash.'/questions/delete'.$existingQuestion->id, 'DELETE');
    $response = (new BallotController)->destroyQuestion($mockRequest, $existingBallot, $existingQuestion);

    // test if user is as expected
    assertTrue($admin->hasRole('admin'));
    assertEquals($admin->name, 'adminTestQuestionDelete');
    expect($admin)->toBeInstanceOf(User::class);

    // assert ballot deletion was succesful
    assertEquals(302, $response->status());
    expect(Question::byHash($existingQuestion->hash))->toBeTruthy();
});

test('test user can delete ballot question', function () {
    $user = asUser('userTestQuestionDelete', 'userTestQuestionDelete@gmail.com');
    $existingBallot = asExistingBallot(Carbon::now($tz = 'UTC'));
    $existingQuestion = Question::where('ballot_id', $existingBallot->id)->first();

    $mockRequest = Request::create('admin/ballots/'.$existingBallot->hash.'/questions/delete'.$existingQuestion->id, 'DELETE');
    $response = (new BallotController)->destroyQuestion($mockRequest, $existingBallot, $existingQuestion);

    // test if user is as expected
    assertTrue($user->hasRole('user'));
    assertEquals($user->name, 'userTestQuestionDelete');
    expect($user)->toBeInstanceOf(User::class);

    // assert ballot deletion was succesful
    assertEquals(302, $response->status());
    expect(Question::byHash($existingQuestion->hash))->toBeTruthy();
});

test('test ballot question can be edited before started_at date', function () {
    $user = $superAdmin = asSuperAdmin('startedAtFutureQuestion', 'startedAtFutureQuestion@gmail.com');

    $existingBallot = asExistingBallot(Carbon::now('UTC')->addMonths(2));
    $existingQuestion = Question::where('ballot_id', $existingBallot->id)->first();

    $payload = array_merge($existingQuestion->toArray(), ['title' => 'updated Question title', 'description' => 'updated ballot description', 'version' => (string) random_int(1, 199)]);
    $mockRequest = Request::create('admin/ballots/'.$existingBallot->hash.'/questions/create', 'PATCH', $payload);
    $response = (new BallotController)->updateQuestion($mockRequest, $existingBallot, $existingQuestion);
    $updatedBallot = Question::byHash($existingQuestion->hash);

    // assert ballot update occurred
    assertEquals(302, $response->status());
    assertEquals($updatedBallot->title, $payload['title']);

});

test('test ballot question cannot be edited after started_at date', function () {
    $user = $superAdmin = asSuperAdmin('startedAtPastQuestion', 'startedAtPastQuestion@gmail.com');

    $existingBallot = asExistingBallot(Carbon::now('UTC')->subMonths(2));
    $existingQuestion = Question::where('ballot_id', $existingBallot->id)->first();

    $payload = array_merge($existingQuestion->toArray(), ['title' => 'updated Question title past', 'description' => 'updated ballot description', 'version' => (string) random_int(1, 199)]);
    $mockRequest = Request::create('admin/ballots/'.$existingBallot->hash.'/questions/create', 'PATCH', $payload);
    $response = (new BallotController)->updateQuestion($mockRequest, $existingBallot, $existingQuestion);
    $updatedBallot = Question::byHash($existingQuestion->hash);

    // assert ballot update occurred
    assertEquals(302, $response->status());
    assertEquals($updatedBallot->title, $existingQuestion->title);

});
