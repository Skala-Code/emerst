<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use SocialiteProviders\Microsoft\Provider;

class MicrosoftAuthController extends Controller
{
    /**
     * Redirect to Microsoft OAuth
     */
    public function redirect()
    {
        return \Laravel\Socialite\Facades\Socialite::driver('microsoft')
            ->scopes(['Mail.Read', 'Mail.ReadWrite', 'offline_access'])
            ->redirect();
    }

    /**
     * Handle Microsoft OAuth callback
     */
    public function callback()
    {
        try {
            $microsoftUser = \Laravel\Socialite\Facades\Socialite::driver('microsoft')->user();
            
            $user = Auth::user();
            
            if (!$user) {
                return redirect()->route('filament.admin.auth.login')
                    ->with('error', 'Você precisa estar logado para conectar sua conta Microsoft.');
            }

            // Calculate token expiration (usually 3600 seconds)
            $expiresIn = 3600; // Default 1 hour, adjust if provided by Microsoft
            
            $user->update([
                'microsoft_token' => $microsoftUser->token,
                'microsoft_refresh_token' => $microsoftUser->refreshToken,
                'microsoft_token_expires_at' => now()->addSeconds($expiresIn),
            ]);

            return redirect()->route('filament.admin.pages.inbox')
                ->with('success', 'Conta Microsoft conectada com sucesso!');
        } catch (\Exception $e) {
            return redirect()->route('filament.admin.pages.inbox')
                ->with('error', 'Erro ao conectar conta Microsoft: ' . $e->getMessage());
        }
    }

    /**
     * Disconnect Microsoft account
     */
    public function disconnect()
    {
        $user = Auth::user();
        
        $user->update([
            'microsoft_token' => null,
            'microsoft_refresh_token' => null,
            'microsoft_token_expires_at' => null,
        ]);

        return redirect()->back()->with('success', 'Conta Microsoft desconectada com sucesso!');
    }
}

