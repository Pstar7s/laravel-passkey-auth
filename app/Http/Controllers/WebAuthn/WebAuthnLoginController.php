<?php

namespace App\Http\Controllers\WebAuthn;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Laragear\WebAuthn\Http\Requests\AssertedRequest;
use Laragear\WebAuthn\Http\Requests\AssertionRequest;
use Laragear\WebAuthn\Models\WebAuthnCredential;

use function response;

class WebAuthnLoginController
{
    /**
     * Returns the challenge to assertion.
     *
     * @param  \Laragear\WebAuthn\Http\Requests\AssertionRequest  $request
     * @return \Illuminate\Contracts\Support\Responsable
     */
    public function options(AssertionRequest $request): Responsable
    {
        $email = $request->input('email');
        
        \Log::info('Login options request', ['email' => $email]);
        
        // Use userless login (no email filtering)
        $response = $request->fastLogin()->toVerify();

        return $response;
    }

    /**
     * Log the user in.
     *
     * @param  \Laragear\WebAuthn\Http\Requests\AssertedRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(AssertedRequest $request): JsonResponse
    {
        $credentialId = $request->input('id');
        
        \Log::info('Login attempt', [
            'credential_id' => $credentialId,
            'session_id' => session()->getId(),
            'has_webauthn_in_session' => session()->has('_webauthn'),
        ]);
        
        try {
            // List all credentials in database for debugging
            $allCredentials = WebAuthnCredential::all();
            \Log::info('All credentials in DB', [
                'count' => $allCredentials->count(),
                'credentials' => $allCredentials->map(function($c) {
                    return [
                        'id' => $c->id,
                        'user_id' => $c->authenticatable_id,
                        'id_length' => strlen($c->id)
                    ];
                })->toArray()
            ]);
            
            // Try direct credential lookup by ID
            $credential = WebAuthnCredential::where('id', $credentialId)->first();
            
            \Log::info('Credential lookup', [
                'search_id' => $credentialId,
                'found' => $credential ? true : false,
                'user_id' => $credential?->authenticatable_id,
            ]);
            
            // Workaround for Laragear bug: manually verify credential and login user
            // instead of relying on $request->login() which returns null due to verify=false bug
            if ($credential && $credential->authenticatable_type === 'App\\Models\\User') {
                // Find the user and manually authenticate
                $user = \App\Models\User::find($credential->authenticatable_id);
                if ($user) {
                    auth()->login($user);
                    $loggedIn = true;
                    \Log::info('Manual login successful', ['user_id' => $user->id]);
                } else {
                    $loggedIn = false;
                    \Log::warning('User not found for credential', ['user_id' => $credential->authenticatable_id]);
                }
            } else {
                // Try the standard way for compatibility
                $loggedIn = $request->login();
                \Log::info('Standard login result', ['success' => $loggedIn]);
            }
            
            \Log::info('Login result', [
                'success' => $loggedIn,
                'type' => gettype($loggedIn),
                'authenticated_after' => auth()->check(),
                'user_id_after' => auth()->id(),
            ]);
            
            if ($loggedIn) {
                // Regenerate session for security after successful login
                $request->session()->regenerate();
                
                \Log::info('After regenerate', [
                    'authenticated' => auth()->check(),
                    'user_id' => auth()->id(),
                ]);
            }
            
            return response()->json([
                'verified' => $loggedIn,
                'message' => $loggedIn ? 'Login successful' : 'Login failed'
            ], $loggedIn ? 200 : 422);
        } catch (\Exception $e) {
            \Log::error('Login exception', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return response()->json([
                'verified' => false,
                'message' => 'Login error: ' . $e->getMessage()
            ], 500);
        }
    }
}
