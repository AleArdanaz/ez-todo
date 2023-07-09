<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TaskPolicy
{
    /**
     * Determine whether the user can view any models.
     * @noinspection PhpInconsistentReturnPointsInspection
     */
    public function viewAny(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Task $task): bool
    {
        return $user->id === $task->list->user_id;
    }

    /**
     * Determine whether the user can create models.
     * @noinspection PhpInconsistentReturnPointsInspection
     */
    public function create(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Task $task): bool
    {
        return $user->id === $task->list->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Task $task): bool
    {
        return $user->id === $task->list->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     * @noinspection PhpInconsistentReturnPointsInspection
     */
    public function restore(User $user, Task $task): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     * @noinspection PhpInconsistentReturnPointsInspection
     */
    public function forceDelete(User $user, Task $task): bool
    {
        //
    }
}
