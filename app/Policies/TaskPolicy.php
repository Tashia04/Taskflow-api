<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TaskPolicy
{
    /**
     * ADMIN → peut tout faire
     * MANAGER → peut tout faire sur les tâches de ses projets
     * DEVELOPER → peut seulement voir et modifier ses propres tâches
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Task $task): bool
    {
        //Admin voit tout
        if($user->isAdmin()) {
            return true;
        }
        //Manager voit les tâches de ses projets
        if($user->isManager()) {
            return $task->project->owner_id === $user->id;
        }

        //Developer voit seulement ses tâches
        return $task->assigned_id === $user->id;

    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, Project $project): bool
    {
        //Admin peut créer des tâches dans tous les projets
        if ($user->isAdmin()) {
            return true;
        }
        //Manager peut créer des tâches uniquement dans ses projets
        if ($user->isManager()) {
            return $project->owner_id === $user->id;
        }
        //Les développeurs ne peuvent pas créer de tâches
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Task $task): bool
    {
        if($user->isAdmin())
        {
            return true;
        }
        //Manager peut modifier les tâches de ses projets
        if($user->isManager())
        {
            return $task->project->owner_id === $user->id;
        }
        //Developer peut modifier ses propres tâches
        return $task->assigned_id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Task $task): bool
    {
        if ($user->isAdmin()) {
            return true;
        }
        // Manager peut supprimer les tâches de ses projets
        if ($user->isManager()) {
            return $task->project->owner_id === $user->id;
        }
        // Les développeurs ne peuvent pas supprimer de tâches
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Task $task): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Task $task): bool
    {
        return false;
    }
}
