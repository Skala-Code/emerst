<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Lawyer extends Model
{
    protected $fillable = [
        'user_id',
        'office_id',
        'name',
        'cpf',
        'oab',
        'email',
        'phone',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    // Manter relacionamentos diretos para processos e ordens de serviço
    public function processes(): HasMany
    {
        return $this->hasMany(Process::class);
    }

    public function serviceOrders(): HasMany
    {
        return $this->hasMany(ServiceOrder::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Relacionamento many-to-many com empresas
    public function companies(): BelongsToMany
    {
        return $this->belongsToMany(Company::class, 'company_lawyer')
                    ->withTimestamps();
    }

    // Relacionamento many-to-many com escritórios
    public function offices(): BelongsToMany
    {
        return $this->belongsToMany(Office::class, 'lawyer_office')
                    ->withTimestamps();
    }
}
