<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

trait HasFilamentPermissions
{
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->when(
            auth()->user()?->hasRole(['colaborador', 'advogado']),
            function (Builder $query) {
                // For lawyers and collaborators, filter to show only their own records
                $modelClass = static::getModel();

                if (method_exists($modelClass, 'getTable')) {
                    $tableName = (new $modelClass)->getTable();

                    // Check if the model has a lawyer_id field
                    if (in_array('lawyer_id', (new $modelClass)->getFillable())) {
                        return $query->where('lawyer_id', auth()->id());
                    }
                }

                return $query;
            }
        );
    }

    public static function canViewAny(): bool
    {
        $permission = static::getViewAnyPermission();

        return auth()->user()?->hasPermissionTo($permission) ?? false;
    }

    public static function canCreate(): bool
    {
        $permission = static::getCreatePermission();

        return auth()->user()?->hasPermissionTo($permission) ?? false;
    }

    public static function canEdit(Model $record): bool
    {
        $permission = static::getEditPermission();

        return auth()->user()?->hasPermissionTo($permission) ?? false;
    }

    public static function canDelete(Model $record): bool
    {
        $permission = static::getDeletePermission();

        return auth()->user()?->hasPermissionTo($permission) ?? false;
    }

    protected static function getViewAnyPermission(): string
    {
        $modelName = strtolower(class_basename(static::getModel()));

        return "view_{$modelName}s";
    }

    protected static function getCreatePermission(): string
    {
        $modelName = strtolower(class_basename(static::getModel()));

        return "create_{$modelName}s";
    }

    protected static function getEditPermission(): string
    {
        $modelName = strtolower(class_basename(static::getModel()));

        return "edit_{$modelName}s";
    }

    protected static function getDeletePermission(): string
    {
        $modelName = strtolower(class_basename(static::getModel()));

        return "delete_{$modelName}s";
    }
}
