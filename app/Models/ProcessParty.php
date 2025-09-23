<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProcessParty extends Model
{
    protected $fillable = [
        'process_id',
        'party_type',
        'person_type',
        'name',
        'document',
        'registration_number',
        'email',
        'phone',
        'address',
        'lawyer_name',
        'lawyer_oab',
        'role',
    ];

    protected $casts = [
        'party_type' => 'string',
        'person_type' => 'string',
    ];

    public function process(): BelongsTo
    {
        return $this->belongsTo(Process::class);
    }

    /**
     * Get the party type label
     */
    public function getPartyTypeLabel(): string
    {
        return match ($this->party_type) {
            'active' => 'Polo Ativo',
            'passive' => 'Polo Passivo',
            'interested' => 'Outros Interessados',
            default => $this->party_type,
        };
    }

    /**
     * Get the person type label
     */
    public function getPersonTypeLabel(): string
    {
        return match ($this->person_type) {
            'individual' => 'Pessoa Física',
            'legal' => 'Pessoa Jurídica',
            default => $this->person_type,
        };
    }
}