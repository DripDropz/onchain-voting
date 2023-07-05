<?php

namespace App\Policies;

use App\Enums\RoleEnum;
use App\Models\Interfaces\HasUser;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AppPolicy
{
    use HandlesAuthorization;

    /**
     * Perform pre-authorization checks.
     *
     * @param  string  $ability
     * @return void|bool
     */
    public function before(User $user, $ability)
    {
        if ($user->hasAnyRole([RoleEnum::SUPER_ADMIN->value])) {
            return true;
        }
    }

    /**
     * Determine whether the user can view the model.
     *
     *
     * @throws \Exception
     */
    public function canView(User $user, HasUser $model): bool
    {
        return $this->canViewAny($user) || $user->id === $model->user_identifier;
    }

    /**
     * Determine whether the user can view any models.
     */
    public function canViewAny(User $user): mixed
    {
        return $user->hasAnyRole([RoleEnum::SUPER_ADMIN->value]);
    }

    /**
     * Determine whether the user can create models.
     */
    public function canCreateAny(User $user): bool
    {
        return $user->hasAnyRole([RoleEnum::SUPER_ADMIN->value]);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function canUpdate(User $user, HasUser $model): mixed
    {
        return $this->canUpdateAny($user) || $user->id === $model->user_identifier;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function canUpdateAny(User $user): bool
    {
        return $user->hasAnyRole([RoleEnum::SUPER_ADMIN->value]);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function canDelete(User $user, HasUser $model): bool
    {
        return $this->canDeleteAny($user) || $user->id === $model->user_identifier;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function canDeleteAny(User $user): bool
    {
        return $user->hasAnyRole([RoleEnum::SUPER_ADMIN->value]);
    }
}
