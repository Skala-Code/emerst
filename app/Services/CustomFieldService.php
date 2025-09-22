<?php

namespace App\Services;

use App\Models\CustomField;
use App\Models\CustomTab;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;

class CustomFieldService
{
    public static function getCustomFieldsForModel(string $modelType): array
    {
        $user = auth()->user();
        $userRoles = $user ? $user->getRoleNames()->toArray() : [];

        // Super-admin can see all tabs
        $isSuperAdmin = in_array('super-admin', $userRoles);

        $tabs = CustomTab::where('model_type', $modelType)
            ->where('active', true)
            ->where(function ($query) use ($userRoles, $isSuperAdmin) {
                if ($isSuperAdmin) {
                    // Super-admin can see all tabs
                    return;
                }

                // Show tabs with no permissions (public) or tabs where user has permission
                $query->whereNull('permissions')
                    ->orWhere('permissions', '[]')
                    ->orWhere(function ($q) use ($userRoles) {
                        foreach ($userRoles as $role) {
                            $q->orWhereJsonContains('permissions', $role);
                        }
                    });
            })
            ->orderBy('sort_order')
            ->with(['customFields' => function ($query) {
                $query->where('active', true)->orderBy('sort_order');
            }])
            ->get();

        if ($tabs->isEmpty()) {
            return [];
        }

        $tabsComponents = [];

        foreach ($tabs as $tab) {
            $fields = [];

            foreach ($tab->customFields as $field) {
                $component = self::createFieldComponent($field);
                if ($component) {
                    $fields[] = $component;
                }
            }

            if (! empty($fields)) {
                $tabsComponents[] = Tab::make($tab->name)
                    ->label($tab->label)
                    ->schema($fields);
            }
        }

        // Fields without tabs
        $fieldsWithoutTab = CustomField::where('model_type', $modelType)
            ->where('active', true)
            ->whereNull('custom_tab_id')
            ->orderBy('sort_order')
            ->get();

        $fieldsWithoutTabComponents = [];
        foreach ($fieldsWithoutTab as $field) {
            $component = self::createFieldComponent($field);
            if ($component) {
                $fieldsWithoutTabComponents[] = $component;
            }
        }

        $result = [];

        if (! empty($fieldsWithoutTabComponents)) {
            $result = array_merge($result, $fieldsWithoutTabComponents);
        }

        if (! empty($tabsComponents)) {
            $result[] = Tabs::make('custom_tabs')
                ->tabs($tabsComponents);
        }

        return $result;
    }

    private static function createFieldComponent(CustomField $field): ?Component
    {
        $component = match ($field->type) {
            'text' => TextInput::make("custom_data.{$field->name}")
                ->label($field->label)
                ->maxLength(255),
            'textarea' => Textarea::make("custom_data.{$field->name}")
                ->label($field->label)
                ->rows(4),
            'number' => TextInput::make("custom_data.{$field->name}")
                ->label($field->label)
                ->numeric(),
            'email' => TextInput::make("custom_data.{$field->name}")
                ->label($field->label)
                ->email(),
            'date' => DatePicker::make("custom_data.{$field->name}")
                ->label($field->label),
            'datetime' => DateTimePicker::make("custom_data.{$field->name}")
                ->label($field->label),
            'select' => Select::make("custom_data.{$field->name}")
                ->label($field->label)
                ->options($field->options ?? []),
            'radio' => Radio::make("custom_data.{$field->name}")
                ->label($field->label)
                ->options($field->options ?? []),
            'checkbox' => Toggle::make("custom_data.{$field->name}")
                ->label($field->label),
            'file' => FileUpload::make("custom_data.{$field->name}")
                ->label($field->label),
            default => null,
        };

        if ($component && $field->required) {
            $component->required();
        }

        return $component;
    }
}
