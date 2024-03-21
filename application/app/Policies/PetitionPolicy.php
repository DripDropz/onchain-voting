<?php

namespace App\Policies;

use App\Enums\PermissionEnum;
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
        return !!$user;
    }

    /**
     * Determine whether the user can create models.
     *
     *
     * @throws \Exception
     */
    public function create(User $user)
    {
        // return $this->canCreateAny($user)
        //     ? Response::allow()
        //     : Response::deny('You are not authorized to create a Petition.');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Petition $petition)
    {
        // return $this->canUpdate($user, $petition)
        //     ? Response::allow()
        //     : Response::deny('You are not authorized to update this Petition.');
    }

    /**
     * Determine whether the user can publish the model.
     */
    public function publish(User $user, Petition $petition)
    {
        // $authorized = $petition->publishable && $this->canUpdate($user, $petition);

        // return $authorized
        //     ? Response::allow()
        //     : Response::deny('You are not authorized to publish this Petition.');
    }

    /**
     * Determine whether the user can delete the post.
     */
    public function delete(User $user, Petition $petition)
    {
        // return $user->hasAnyPermission([PermissionEnum::DELETE_BALLOT]) || $this->canDelete($user, $petition);
    }
}
