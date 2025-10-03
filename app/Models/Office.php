<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Office extends Model
{
    protected $fillable = [
        'company_id',
        'name',
        'custom_name',
        'contract_status',
        'legal_name',
        'cnpj',
        'email',
        'phone',
        'address',
        'zip_code',
        'address_number',
        'complement',
        'state',
        'city',
        'active',
        'responsible_name',
        'responsible_cpf',
        'responsible_phone',
        'responsible_email',
        'responsible_position',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    // Manter relacionamento direto para processos
    public function processes(): HasMany
    {
        return $this->hasMany(Process::class);
    }

    // Relacionamento many-to-many com empresas
    public function companies(): BelongsToMany
    {
        return $this->belongsToMany(Company::class, 'company_office')
                    ->withTimestamps();
    }

    // Relacionamento many-to-many com advogados
    public function lawyers(): BelongsToMany
    {
        return $this->belongsToMany(Lawyer::class, 'lawyer_office')
                    ->withTimestamps();
    }
}
