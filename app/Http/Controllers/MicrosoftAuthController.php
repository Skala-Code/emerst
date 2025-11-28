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
    public function callback(Request $request)
    {
        \Log::info('Microsoft OAuth Callback called', [
            'url' => $request->fullUrl(),
            'code' => $request->has('code'),
            'state' => $request->has('state'),
            'user_authenticated' => Auth::check(),
        ]);

        try {
            // Check if user is authenticated
            $user = Auth::user();
            
            if (!$user) {
                // Store the OAuth code in session and redirect to login
                // After login, user can complete the connection
                $request->session()->put('microsoft_oauth_code', $request->get('code'));
                $request->session()->put('microsoft_oauth_state', $request->get('state'));
                
                return redirect()->route('filament.admin.auth.login')
                    ->with('info', 'Por favor, faça login para completar a conexão com Microsoft.');
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

            \Log::info('Microsoft OAuth Success', ['user_id' => $user->id]);

            return redirect()->route('filament.admin.pages.inbox')
                ->with('success', 'Conta Microsoft conectada com sucesso!');
        } catch (\Laravel\Socialite\Two\InvalidStateException $e) {
            \Log::warning('Microsoft OAuth InvalidStateException', ['error' => $e->getMessage()]);
            // Handle state mismatch (usually means session expired or CSRF issue)
            return redirect()->route('filament.admin.auth.login')
                ->with('error', 'Sessão expirada. Por favor, faça login e tente conectar novamente.');
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Microsoft OAuth Error: ' . $e->getMessage(), [
                'exception' => $e,
                'user_id' => Auth::id(),
                'request' => $request->all(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            $redirectRoute = Auth::check() 
                ? route('filament.admin.pages.inbox')
                : route('filament.admin.auth.login');
            
            return redirect($redirectRoute)
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

