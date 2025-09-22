<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    protected $fillable = [
        'name',
        'cnpj',
        'email',
        'phone',
        'address',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function offices(): HasMany
    {
        return $this->hasMany(Office::class);
    }

    public function processes(): HasMany
    {
        return $this->hasMany(Process::class);
    }
}
