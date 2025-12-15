<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MicrosoftAccount extends Model
{
    protected $fillable = [
        'email',
        'microsoft_id',
        'token',
        'refresh_token',
        'token_expires_at',
        'connected_by_user_id',
    ];

    protected $casts = [
        'token_expires_at' => 'datetime',
    ];

    /**
     * User who connected this account
     */
    public function connectedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'connected_by_user_id');
    }

    /**
     * Emails associated with this Microsoft account
     */
    public function emails(): HasMany
    {
        return $this->hasMany(Email::class);
    }

    /**
     * Check if account is connected (has valid token)
     */
    public function isConnected(): bool
    {
        return !empty($this->token);
    }

    /**
     * Refresh access token if needed
     */
    public function refreshTokenIfNeeded(): bool
    {
        // Check if token is expired or will expire soon
        if (!$this->token_expires_at || 
            $this->token_expires_at->isPast() ||
            $this->token_expires_at->diffInMinutes(now()) < 5) {
            
            return $this->refreshAccessToken();
        }

        return true;
    }

    /**
     * Refresh access token using refresh token
     */
    private function refreshAccessToken(): bool
    {
        if (!$this->refresh_token) {
            return false;
        }

        try {
            $response = Http::asForm()->post('https://login.microsoftonline.com/common/oauth2/v2.0/token', [
                'client_id' => config('services.microsoft.client_id'),
                'client_secret' => config('services.microsoft.client_secret'),
                'grant_type' => 'refresh_token',
                'refresh_token' => $this->refresh_token,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                $this->update([
                    'token' => $data['access_token'],
                    'refresh_token' => $data['refresh_token'] ?? $this->refresh_token,
                    'token_expires_at' => now()->addSeconds($data['expires_in']),
                ]);

                return true;
            }
        } catch (\Exception $e) {
            Log::error('Failed to refresh Microsoft token', [
                'microsoft_account_id' => $this->id,
                'email' => $this->email,
                'error' => $e->getMessage(),
            ]);
        }

        return false;
    }

    /**
     * Get access token (refresh if needed)
     */
    public function getAccessToken(): ?string
    {
        if (!$this->refreshTokenIfNeeded()) {
            return null;
        }

        return $this->token;
    }
}

