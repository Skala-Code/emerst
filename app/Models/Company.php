<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Company extends Model
{
    protected $fillable = [
        'name',
        'custom_name',
        'economic_group',
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
        'responsible_cpf_cnpj',
        'responsible_phone',
        'responsible_email',
        'responsible_position',
        'contract_type',
        'interested_party',
        'departments',
        'sync_internal_system',
        'contract_start_date',
        'contract_end_date',
        'readjustment_month',
        'readjustment_index',
        'cutoff_day',
        'payment_modality',
        'calculation_types',
        'company_rate',
        'sat_rate',
        'third_party_rate',
        'taxation_data',
    ];

    protected $casts = [
        'active' => 'boolean',
        'departments' => 'array',
        'calculation_types' => 'array',
        'taxation_data' => 'array',
        'contract_start_date' => 'date',
        'contract_end_date' => 'date',
        'company_rate' => 'decimal:2',
        'sat_rate' => 'decimal:2',
        'third_party_rate' => 'decimal:2',
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
