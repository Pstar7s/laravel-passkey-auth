@extends('layouts.app')

@section('title', 'Register - Passkey Auth')

@section('content')
<div class="max-w-md mx-auto bg-white rounded-lg shadow-md p-8">
    <h2 class="text-2xl font-bold mb-6 text-center">Register dengan Passkey</h2>
    
    <div id="error-message" class="hidden bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
    </div>

    <div id="success-message" class="hidden bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
    </div>

    <form id="register-form">
        @csrf
        <div class="mb-4">
            <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Nama:</label>
            <input type="text" id="name" name="name" required
                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
        </div>

        <div class="mb-6">
            <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email:</label>
            <input type="email" id="email" name="email" required
                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
        </div>

        <button type="submit" id="submit-btn"
                class="w-full bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline disabled:opacity-50 disabled:cursor-not-allowed">
            <span id="btn-text">Register & Setup Passkey</span>
            <span id="btn-loading" class="hidden">⏳ Processing...</span>
        </button>
    </form>

    <p class="mt-4 text-center text-sm text-gray-600">
        Sudah punya akun? <a href="{{ route('login') }}" class="text-blue-500 hover:text-blue-700">Login di sini</a>
    </p>

    <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded">
        <p class="text-sm text-blue-800">
            <strong>ℹ️ Info:</strong> Passkey menggunakan biometric (Face ID, Touch ID, atau Windows Hello) 
            atau PIN device Anda untuk authentication yang lebih aman dan praktis.
        </p>
    </div>
    
    <div class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded">
        <p class="text-sm text-yellow-800">
            <strong>💡 Tips:</strong> Jika email sudah terdaftar tapi belum punya passkey, 
            Anda bisa setup passkey dengan register ulang menggunakan email yang sama.
        </p>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/@simplewebauthn/browser@10/dist/bundle/index.umd.min.js"></script>
<script>
const { startRegistration } = SimpleWebAuthnBrowser;

document.getElementById('register-form').addEventListener('submit', async (e) => {
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

    const name = document.getElementById('name').value;
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
        // Step 1: Register user
        const registerResponse = await fetch('/register', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            credentials: 'same-origin',
            body: JSON.stringify({ name, email })
        });

        if (!registerResponse.ok) {
            const contentType = registerResponse.headers.get('content-type');
            console.error('Register failed. Status:', registerResponse.status);
            console.error('Content-Type:', contentType);
            
            let errorMessage = 'Registration failed';
            
            if (contentType && contentType.includes('application/json')) {
                const error = await registerResponse.json();
                errorMessage = error.message || JSON.stringify(error);
                
                // Check for CSRF token mismatch
                if (error.message && error.message.includes('CSRF')) {
                    errorMessage = 'Session expired. Halaman akan di-refresh...';
                    errorDiv.textContent = errorMessage;
                    errorDiv.classList.remove('hidden');
                    setTimeout(() => window.location.reload(), 2000);
                    return;
                }
            } else {
                const errorText = await registerResponse.text();
                console.error('Error response (HTML):', errorText.substring(0, 500));
                errorMessage = `Server returned HTML error (Status ${registerResponse.status}). Check console for details.`;
            }
            
            throw new Error(errorMessage);
        }

        const registerData = await registerResponse.json();
        console.log('Registration successful:', registerData);
        console.log('User authenticated:', registerData.authenticated);
        console.log('User ID:', registerData.user_id);
        
        // Show info if user already exists
        if (registerData.existing_user) {
            console.log('User already exists, proceeding to setup passkey...');
        }
        
        // Wait a bit for session to fully propagate (500ms should be enough)
        await new Promise(resolve => setTimeout(resolve, 500));

        // Step 2: Get WebAuthn registration options
        const optionsResponse = await fetch('/webauthn/register/options', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            credentials: 'same-origin'
        });

        if (!optionsResponse.ok) {
            const errorText = await optionsResponse.text();
            console.error('Options response:', errorText);
            throw new Error('Failed to get registration options. Make sure you are logged in.');
        }

        const optionsData = await optionsResponse.json();
        console.log('Registration options:', optionsData);
        
        // Check WebAuthn support
        if (!window.PublicKeyCredential) {
            throw new Error('WebAuthn not supported in this browser. Please use Chrome, Edge, or Safari.');
        }
        
        console.log('Starting WebAuthn registration...');

        // Step 3: Start WebAuthn registration using SimpleWebAuthn
        const attestationResponse = await startRegistration(optionsData);
        console.log('Passkey created successfully:', attestationResponse);

        // Step 4: Send attestation to server
        const verificationResponse = await fetch('/webauthn/register', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            credentials: 'same-origin',
            body: JSON.stringify(attestationResponse)
        });

        if (!verificationResponse.ok) {
            const error = await verificationResponse.json();
            console.error('Verification response:', error);
            throw new Error(error.message || 'Failed to verify passkey');
        }
        
        const result = await verificationResponse.json();
        console.log('Verification successful:', result);
        
        successDiv.textContent = 'Passkey berhasil dibuat! Redirect ke dashboard...';
        successDiv.classList.remove('hidden');
        
        setTimeout(() => {
            window.location.href = '/dashboard';
        }, 1500);

    } catch (error) {
        console.error('Registration error:', error);
        console.error('Error stack:', error.stack);
        errorDiv.textContent = 'Error: ' + error.message;
        errorDiv.classList.remove('hidden');
        
        // Re-enable button
        submitBtn.disabled = false;
        btnText.classList.remove('hidden');
        btnLoading.classList.add('hidden');
        
        // Scroll to error message
        errorDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
});
</script>
@endpush
