<?php

namespace App\Policies;

use App\Models\Process;
use App\Models\User;

class ProcessPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view_processes') || $user->hasPermissionTo('view_own_processes');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Process $process): bool
    {
        if ($user->hasPermissionTo('view_processes')) {
            return true;
        }

        if ($user->hasPermissionTo('view_own_processes')) {
            return $process->lawyer_id === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create_processes');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Process $process): bool
    {
        if ($user->hasPermissionTo('edit_processes')) {
            return true;
        }

        if ($user->hasPermissionTo('edit_own_processes')) {
            return $process->lawyer_id === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Process $process): bool
    {
        return $user->hasPermissionTo('delete_processes');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Process $process): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Process $process): bool
    {
        return false;
    }
}
