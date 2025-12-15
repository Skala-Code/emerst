<?php

namespace App\Policies;

use App\Models\ServiceOrder;
use App\Models\User;

class ServiceOrderPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view_service_orders') || 
               $user->hasPermissionTo('view_own_service_orders');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ServiceOrder $serviceOrder): bool
    {
        if ($user->hasPermissionTo('view_service_orders')) {
            return true;
        }

        if ($user->hasPermissionTo('view_own_service_orders')) {
            return $serviceOrder->current_responsible_id === $user->lawyer?->id;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create_service_orders');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ServiceOrder $serviceOrder): bool
    {
        if ($user->hasPermissionTo('edit_service_orders')) {
            return true;
        }

        if ($user->hasPermissionTo('edit_own_service_orders')) {
            return $serviceOrder->current_responsible_id === $user->lawyer?->id;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ServiceOrder $serviceOrder): bool
    {
        return $user->hasPermissionTo('delete_service_orders');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ServiceOrder $serviceOrder): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ServiceOrder $serviceOrder): bool
    {
        return false;
    }
}
