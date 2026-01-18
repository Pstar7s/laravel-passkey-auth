@extends('layouts.app')

@section('title', 'Laravel Passkey Authentication')

@section('content')
<div class="max-w-4xl mx-auto text-center">
    <div class="bg-white rounded-lg shadow-md p-12">
        <h1 class="text-5xl font-bold mb-6 text-gray-800">🔐 Laravel Passkey Auth</h1>
        
        <p class="text-xl text-gray-600 mb-8">
            Implementasi Modern WebAuthn (Passkey) Authentication dengan Laravel 10
        </p>

        <div class="flex justify-center gap-4 mb-12">
            <a href="{{ route('register') }}" 
               class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-lg text-lg transition">
                🚀 Mulai Register
            </a>
            <a href="{{ route('login') }}" 
               class="bg-green-500 hover:bg-green-700 text-white font-bold py-3 px-8 rounded-lg text-lg transition">
                🔑 Login
            </a>
        </div>

        <div class="grid md:grid-cols-3 gap-6 text-left">
            <div class="border rounded-lg p-6 hover:shadow-lg transition">
                <div class="text-3xl mb-3">🛡️</div>
                <h3 class="font-bold text-lg mb-2">Lebih Aman</h3>
                <p class="text-sm text-gray-600">
                    Tidak bisa di-phishing dan tidak ada password yang bisa dicuri
                </p>
            </div>

            <div class="border rounded-lg p-6 hover:shadow-lg transition">
                <div class="text-3xl mb-3">⚡</div>
                <h3 class="font-bold text-lg mb-2">Lebih Cepat</h3>
                <p class="text-sm text-gray-600">
                    Login hanya dengan satu sentuhan atau scan biometric
                </p>
            </div>

            <div class="border rounded-lg p-6 hover:shadow-lg transition">
                <div class="text-3xl mb-3">🧠</div>
                <h3 class="font-bold text-lg mb-2">Lebih Mudah</h3>
                <p class="text-sm text-gray-600">
                    Tidak perlu mengingat password yang rumit
                </p>
            </div>
        </div>

        <div class="mt-12 p-6 bg-blue-50 border border-blue-200 rounded-lg">
            <h3 class="font-bold text-lg mb-3">💡 Apa itu Passkey?</h3>
            <p class="text-sm text-gray-700 leading-relaxed">
                Passkey adalah metode authentication modern yang menggunakan teknologi WebAuthn. 
                Anda bisa login menggunakan Face ID, Touch ID, Windows Hello, atau authenticator lainnya 
                tanpa perlu mengingat password. Passkey lebih aman karena credential tersimpan 
                di device Anda dan tidak pernah dikirim ke server.
            </p>
        </div>

        <div class="mt-8 text-sm text-gray-500">
            <p>Built with ❤️ using Laravel 10 + Laragear/WebAuthn</p>
        </div>
    </div>
</div>
@endsection
