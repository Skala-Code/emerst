<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomField extends Model
{
    protected $fillable = [
        'model_type',
        'name',
        'label',
        'type',
        'options',
        'sort_order',
        'required',
        'active',
        'custom_tab_id',
    ];

    protected $casts = [
        'options' => 'array',
        'required' => 'boolean',
        'active' => 'boolean',
    ];

    public function customTab(): BelongsTo
    {
        return $this->belongsTo(CustomTab::class);
    }
}
