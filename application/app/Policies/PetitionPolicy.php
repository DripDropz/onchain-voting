<?php

namespace App\Policies;

use App\Models\Petition;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PetitionPolicy extends AppPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): mixed
    {
        return $this->canViewAny($user);
    }

    /**
     * Determine whether the user can view the model.
     *
     * @throws \Exception
     */
    public function view(User $user, Petition $petition)
    {
        if ($user->id === $petition->user->id) {
            return Response::allow();
        }

        if ($petition->status === 'published') {
            return Response::allow();
        }

        return Response::deny('You are not authorized to view this Petition.');
    }

    public function sign(User $user): mixed
    {
        return (bool) $user;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): Response
    {
        return $user
            ? Response::allow()
            : Response::deny('You must be logged in to create a petition.');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Petition $petition): Response
    {
        return $user->id === $petition->user_id
            ? Response::allow()
            : Response::deny('You are not authorized to update this petition.');
    }

    /**
     * Determine whether the user can publish the model.
     */
    public function publish(User $user, Petition $petition): Response
    {
        return $user->id === $petition->user_id
            ? Response::allow()
            : Response::deny('You are not authorized to publish this petition.');
    }

    /**
     * Determine whether the user can delete the post.
     */
    public function delete(User $user, Petition $petition): Response
    {
        return $user->id === $petition->user_id
            ? Response::allow()
            : Response::deny('You are not authorized to delete this petition.');
    }
}
