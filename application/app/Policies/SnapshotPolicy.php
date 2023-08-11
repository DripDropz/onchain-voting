<?php

namespace App\Policies;

use App\Enums\PermissionEnum;
use App\Models\Snapshot;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class SnapshotPolicy extends AppPolicy
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
    public function view(User $user, Snapshot $snapshot): bool
    {
        return $this->canView($user, $snapshot);
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
                : Response::deny('You are not authorized to create a snapshot.');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Snapshot $snapshot): Response
    {
        return $this->canUpdate($user, $snapshot)
                ? Response::allow()
                : Response::deny('You are not authorized to update this snapshot.');
    }

    /**
     * Determine whether the user can publish the model.
     */
    public function publish(User $user, Snapshot $snapshot): Response
    {
        $authorized = $snapshot->publishable && $this->canUpdate($user, $snapshot);

        return $authorized
                ? Response::allow()
                : Response::deny('You are not authorized to publish this snapshot.');
    }

    /**
     * Determine whether the user can delete the post.
     */
    public function delete(User $user, Snapshot $snapshot): bool
    {
        return $user->hasAnyPermission([PermissionEnum::delete_ballot()->value]) || $this->canDelete($user, $snapshot);

    }
}
