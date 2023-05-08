<?php

namespace App\Policies;

use App\Enums\PermissionEnum;
use App\Models\Ballot;
use App\Models\User;

class BallotPolicy extends AppPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): mixed
    {
        return $user->hasAnyPermission([PermissionEnum::read_ballot()->value]) || $this->canViewAny($user);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Ballot $ballot): bool
    {
        return $user->hasAnyPermission([PermissionEnum::read_ballot()->value]) || $this->canView($user, $post);
    }

    /**
     * Determine whether the user can create models.
     *
     *
     * @throws \Exception
     */
    public function create(User $user, Ballot $ballot): bool
    {
        return $user->hasAnyPermission([PermissionEnum::create_ballot()->value]) || $this->canCreate($user);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @return mixed
     */
    public function update(User $user, Ballot $ballot)
    {
        return $user->hasAnyPermission([PermissionEnum::update_ballot()->value]) || $this->canUpdateAny($user);
    }

    /**
     * Determine whether the user can delete the post.
     *
     * @param  \App\User  $user
     * @param  \App\Post  $post
     * @return mixed
     */
    public function delete(User $user, Ballot $ballot)
    {
        return $user->hasAnyPermission([PermissionEnum::delete_ballot()->value]) || $this->canDeleteAny($user);
       
    }
}
