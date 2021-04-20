<?php

namespace App\Policies;

use App\Models\TaskComment;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TaskCommentPolicy
{
    use HandlesAuthorization;

    public function before(User $user, $ability) {
        if($user->role == "admin" AND $ability != 'update') return true;
            return null;
    }

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $this->allow();
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\TaskComment  $taskComment
     * @return mixed
     */
    public function view(User $user, TaskComment $taskComment)
    {
        return $this->allow();
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $this->allow();
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\TaskComment  $taskComment
     * @return mixed
     */
    public function update(User $user, TaskComment $taskComment)
    {
        if($user->id == $taskComment->created_by)
            return $this->allow();
        return $this->deny(__('response.not_authorized'));
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\TaskComment  $taskComment
     * @return mixed
     */
    public function delete(User $user, TaskComment $taskComment)
    {
        if($user->id == $taskComment->created_by)
            return $this->allow();
        return $this->deny(__('response.not_authorized'));
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\TaskComment  $taskComment
     * @return mixed
     */
    public function restore(User $user, TaskComment $taskComment)
    {
        return $this->deny(__('response.not_authorized'));
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\TaskComment  $taskComment
     * @return mixed
     */
    public function forceDelete(User $user, TaskComment $taskComment)
    {
        return $this->deny(__('response.not_authorized'));
    }
}
