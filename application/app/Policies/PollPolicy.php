<?php

namespace App\Policies;

use App\Models\Poll;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PollPolicy extends AppPolicy
{
    public function viewAny(User $user): mixed
    {
        return $this->canViewAny($user);
    }

    public function view(User $user, Poll $poll): Response
    {
        if ($user->id === $poll->user?->id) {
            return Response::allow();
        }

        if ($poll->status->value === 'published') {
            return Response::allow();
        }

        return Response::deny('You are not authorized to view this poll.');
    }

    public function create(User $user): Response
    {
        return $user
            ? Response::allow()
            : Response::deny('You must be logged in to create a poll.');
    }

    public function update(User $user, Poll $poll): Response
    {
        return $user->id === $poll->user_id
            ? Response::allow()
            : Response::deny('You are not authorized to update this poll.');
    }

    public function publish(User $user, Poll $poll): Response
    {
        return $user->id === $poll->user_id
            ? Response::allow()
            : Response::deny('You are not authorized to publish this poll.');
    }

    public function vote(User $user, Poll $poll): Response
    {
        if ($poll->status->value !== 'published') {
            return Response::deny('Only published polls can receive votes.');
        }

        return $user
            ? Response::allow()
            : Response::deny('You must be logged in to vote on this poll.');
    }

    public function delete(User $user, Poll $poll): Response
    {
        return $user->id === $poll->user_id
            ? Response::allow()
            : Response::deny('You are not authorized to delete this poll.');
    }
}
