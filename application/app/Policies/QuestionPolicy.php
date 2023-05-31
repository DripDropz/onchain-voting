<?php

namespace App\Policies;


use App\Enums\RoleEnum;
use App\Models\Question;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Auth\Access\Response;

class QuestionPolicy extends AppPolicy
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
     * @throws \Exception
     */
    public function view(User $user, Question $question): bool
    {
        return $this->canView($user, $question);
    }

    /**
     * Determine whether the user can create models.
     *
     *
     * @throws \Exception
     */
    public function create(User $user): Response
    {
        $authorized = $this->canCreateAny($user) || $user->hasAnyRole([RoleEnum::SUPER_ADMIN->value, RoleEnum::ADMIN->value]);

        return $authorized
                ? Response::allow()
                : Response::deny('You are not authorized to create a question.');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param Question $question
     * @return mixed
     */
    public function update(User $user, Question $question): mixed
    {
        $authorized = $this->update($user, $question);

        // cannot be edited after started_at ballot
        $beforeStartedAt = $question->ballot->started_at == null || $question->ballot->started_at > Carbon::now('UTC');

        return $authorized && $beforeStartedAt
            ? Response::allow()
            : Response::deny('Cannot update question after ballot has started.');
    }

    /**
     * Determine whether the user can delete the post.
     *
     * @param User $user
     * @param Question $question
     * @return mixed
     */
    public function delete(User $user, Question $question): mixed
    {
        return $this->canDelete($user, $question);

    }
}
