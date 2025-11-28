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
            // Check if user is authenticated
            $user = Auth::user();
            
            if (!$user) {
                return redirect()->route('filament.admin.auth.login')
                    ->with('error', 'Você precisa estar logado para conectar sua conta Microsoft.');
            }

            // Get Microsoft user data
            $microsoftUser = \Laravel\Socialite\Facades\Socialite::driver('microsoft')->user();
            
            // Calculate token expiration from Microsoft response
            $expiresIn = $microsoftUser->expiresIn ?? 3600; // Default 1 hour if not provided
            
            // Update user with Microsoft tokens
            $user->update([
                'microsoft_id' => $microsoftUser->id,
                'microsoft_token' => $microsoftUser->token,
                'microsoft_refresh_token' => $microsoftUser->refreshToken,
                'microsoft_token_expires_at' => now()->addSeconds($expiresIn),
            ]);

            return redirect()->route('filament.admin.pages.inbox')
                ->with('success', 'Conta Microsoft conectada com sucesso!');
        } catch (\Laravel\Socialite\Two\InvalidStateException $e) {
            // Handle state mismatch (usually means session expired or CSRF issue)
            return redirect()->route('filament.admin.pages.inbox')
                ->with('error', 'Sessão expirada. Por favor, tente conectar novamente.');
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Microsoft OAuth Error: ' . $e->getMessage(), [
                'exception' => $e,
                'user_id' => Auth::id(),
            ]);
            
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

