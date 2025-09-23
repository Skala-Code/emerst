<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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

    // Manter relacionamento direto para processos
    public function processes(): HasMany
    {
        return $this->hasMany(Process::class);
    }

    // Relacionamento many-to-many com escritórios
    public function offices(): BelongsToMany
    {
        return $this->belongsToMany(Office::class, 'company_office')
                    ->withTimestamps();
    }

    // Relacionamento many-to-many com advogados
    public function lawyers(): BelongsToMany
    {
        return $this->belongsToMany(Lawyer::class, 'company_lawyer')
                    ->withTimestamps();
    }
}
