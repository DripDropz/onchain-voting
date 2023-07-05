<?php

namespace App\Policies;

use App\Enums\PermissionEnum;
use App\Models\Ballot;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class BallotPolicy extends AppPolicy
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
    public function view(User $user, Ballot $ballot): bool
    {
        return $this->canView($user, $ballot);
    }

    /**
     * Determine whether the user can create models.
     *
     *
     * @throws \Exception
     */
    public function create(User $user): Response
    {
        return $this->canCreateAny($user)
                ? Response::allow()
                : Response::deny('You are not authorized to create a ballot.');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Ballot $ballot): Response
    {
        return $this->canUpdate($user, $ballot)
                ? Response::allow()
                : Response::deny('You are not authorized to update this ballot.');
    }

    /**
     * Determine whether the user can publish the model.
     */
    public function publish(User $user, Ballot $ballot): Response
    {
        $authorized = $ballot->publishable && $this->canUpdate($user, $ballot);

        return $authorized
                ? Response::allow()
                : Response::deny('You are not authorized to publish this ballot.');
    }

    /**
     * Determine whether the user can delete the post.
     */
    public function delete(User $user, Ballot $ballot): bool
    {
        return $user->hasAnyPermission([PermissionEnum::delete_ballot()->value]) || $this->canDelete($user, $ballot);

    }
}
