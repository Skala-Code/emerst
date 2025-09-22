<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CustomTab extends Model
{
    protected $fillable = [
        'model_type',
        'name',
        'label',
        'sort_order',
        'active',
        'permissions',
    ];

    protected $casts = [
        'active' => 'boolean',
        'permissions' => 'array',
    ];

    public function customFields(): HasMany
    {
        return $this->hasMany(CustomField::class);
    }
}
