<?php

namespace App\Http\Controllers\WebAuthn;

use App\Http\Requests\CustomAttestationRequest;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Laragear\WebAuthn\Http\Requests\AttestedRequest;

use function response;

class WebAuthnRegisterController
{
    /**
     * Returns a challenge to be verified by the user device.
     *
     * @param  \App\Http\Requests\CustomAttestationRequest  $request
     * @return \Illuminate\Contracts\Support\Responsable
     */
    public function options(CustomAttestationRequest $request): Responsable
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            abort(403, 'User must be logged in to register a passkey');
        }
        
        return $request
            ->fastRegistration()
            ->toCreate();
    }

    /**
     * Registers a device for further WebAuthn authentication.
     *
     * @param  \Laragear\WebAuthn\Http\Requests\AttestedRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(AttestedRequest $request): JsonResponse
    {
        \Log::info('Registration attempt', [
            'credential_id' => $request->input('id'),
            'user_id' => auth()->id(),
            'authenticated' => auth()->check(),
        ]);
        
        try {
            $request->save();
            
            \Log::info('Registration saved', [
                'user_id' => auth()->id(),
                'credentials_count' => auth()->user()->webAuthnCredentials->count(),
                'last_credential' => auth()->user()->webAuthnCredentials->last()?->id,
            ]);
            
            // Regenerate session for security after passkey is registered
            $request->session()->regenerate();

            return response()->json([
                'verified' => true,
                'message' => 'Passkey registered successfully'
            ]);
        } catch (\Exception $e) {
            \Log::error('Registration error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return response()->json([
                'verified' => false,
                'message' => 'Registration failed: ' . $e->getMessage()
            ], 422);
        }
    }
}
