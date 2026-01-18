@extends('layouts.app')

@section('title', 'Login - Passkey Auth')

@section('content')
<div class="max-w-md mx-auto bg-white rounded-lg shadow-md p-8">
    <h2 class="text-2xl font-bold mb-6 text-center">Login dengan Passkey</h2>
    
    <div id="error-message" class="hidden bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
    </div>

    <div id="success-message" class="hidden bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
    </div>

    <form id="login-form">
        @csrf
        <div class="mb-6">
            <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email:</label>
            <input type="email" id="email" name="email" required
                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
        </div>

        <button type="submit" id="submit-btn"
                class="w-full bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline disabled:opacity-50 disabled:cursor-not-allowed">
            <span id="btn-text">🔐 Login dengan Passkey</span>
            <span id="btn-loading" class="hidden">⏳ Processing...</span>
        </button>
    </form>

    <p class="mt-4 text-center text-sm text-gray-600">
        Belum punya akun? <a href="{{ route('register') }}" class="text-blue-500 hover:text-blue-700">Register di sini</a>
    </p>

    <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded">
        <p class="text-sm text-blue-800">
            <strong>ℹ️ Info:</strong> Gunakan biometric atau PIN device Anda untuk login. 
            Tidak perlu password!
        </p>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/@simplewebauthn/browser@10/dist/bundle/index.umd.min.js"></script>
<script>
const { startAuthentication } = SimpleWebAuthnBrowser;

document.getElementById('login-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const submitBtn = document.getElementById('submit-btn');
    const btnText = document.getElementById('btn-text');
    const btnLoading = document.getElementById('btn-loading');
    const errorDiv = document.getElementById('error-message');
    const successDiv = document.getElementById('success-message');
    
    // Prevent multiple submissions
    if (submitBtn.disabled) return;
    
    // Disable button and show loading
    submitBtn.disabled = true;
    btnText.classList.add('hidden');
    btnLoading.classList.remove('hidden');
    
    errorDiv.classList.add('hidden');
    successDiv.classList.add('hidden');

    const email = document.getElementById('email').value;
    
    // Validate CSRF token exists
    if (!csrfToken || csrfToken === '') {
        errorDiv.textContent = 'CSRF token tidak ditemukan. Refresh halaman dan coba lagi.';
        errorDiv.classList.remove('hidden');
        submitBtn.disabled = false;
        btnText.classList.remove('hidden');
        btnLoading.classList.add('hidden');
        return;
    }

    try {
        // Step 1: Get WebAuthn login options
        const optionsResponse = await fetch('/webauthn/login/options', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            credentials: 'same-origin',
            body: JSON.stringify({ email })
        });

        if (!optionsResponse.ok) {
            const errorText = await optionsResponse.text();
            console.error('Options response:', errorText);
            
            // Provide more helpful error message
            let errorMsg = 'User tidak ditemukan atau belum setup passkey. ';
            errorMsg += 'Silakan register terlebih dahulu atau gunakan email yang sudah punya passkey.';
            
            throw new Error(errorMsg);
        }

        const optionsData = await optionsResponse.json();
        console.log('Login options:', optionsData);

        // Step 2: Start WebAuthn authentication (use passkey)
        const assertionResponse = await startAuthentication(optionsData);

        // Step 3: Send assertion to server
        const verificationResponse = await fetch('/webauthn/login', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            credentials: 'same-origin',
            body: JSON.stringify(assertionResponse)
        });

        if (!verificationResponse.ok) {
            const errorText = await verificationResponse.text();
            console.error('Verification response:', errorText);
            throw new Error('Authentication failed');
        }

        const result = await verificationResponse.json();

        if (result.verified) {
            successDiv.textContent = 'Login berhasil! Redirect ke dashboard...';
            successDiv.classList.remove('hidden');
            
            setTimeout(() => {
                window.location.href = '/dashboard';
            }, 1500);
        } else {
            throw new Error(result.message || 'Authentication failed');
        }

    } catch (error) {
        console.error('Login error:', error);
        errorDiv.textContent = 'Error: ' + error.message;
        errorDiv.classList.remove('hidden');
        
        // Re-enable button
        submitBtn.disabled = false;
        btnText.classList.remove('hidden');
        btnLoading.classList.add('hidden');
    }
});
</script>
@endpush
