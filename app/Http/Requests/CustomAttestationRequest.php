<?php

namespace App\Http\Requests;

use Laragear\WebAuthn\Contracts\WebAuthnAuthenticatable;
use Laragear\WebAuthn\Http\Requests\AttestationRequest;

class CustomAttestationRequest extends AttestationRequest
{
    /**
     * Override authorization to always return true
     * We handle auth check in the controller instead
     */
    public function authorize(?WebAuthnAuthenticatable $user): bool
    {
        return true;
    }
}
