@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow-md p-8">
        <h2 class="text-3xl font-bold mb-6">Dashboard</h2>
        
        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded">
            <p class="text-green-800">
                ✅ <strong>Selamat datang, {{ auth()->user()->name }}!</strong> 
                Anda berhasil login menggunakan Passkey Authentication.
            </p>
        </div>

        <div class="grid md:grid-cols-2 gap-6">
            <div class="border rounded-lg p-6">
                <h3 class="text-xl font-semibold mb-3">👤 Informasi User</h3>
                <div class="space-y-2">
                    <p><strong>Nama:</strong> {{ auth()->user()->name }}</p>
                    <p><strong>Email:</strong> {{ auth()->user()->email }}</p>
                    <p><strong>ID:</strong> {{ auth()->user()->id }}</p>
                </div>
            </div>

            <div class="border rounded-lg p-6">
                <h3 class="text-xl font-semibold mb-3">🔐 Passkey Info</h3>
                <div class="space-y-2">
                    <p class="text-sm text-gray-600">
                        Anda menggunakan WebAuthn (Passkey) untuk authentication, 
                        yang merupakan standar keamanan modern yang lebih aman dari password tradisional.
                    </p>
                    <div class="mt-4">
                        <p class="text-sm"><strong>Keuntungan Passkey:</strong></p>
                        <ul class="list-disc list-inside text-sm text-gray-600 mt-2">
                            <li>Tidak bisa di-phishing</li>
                            <li>Tidak perlu mengingat password</li>
                            <li>Lebih cepat untuk login</li>
                            <li>Menggunakan biometric device</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-8 border-t pt-6">
            <h3 class="text-xl font-semibold mb-4">📝 Tentang Implementasi</h3>
            <div class="bg-gray-50 p-4 rounded">
                <p class="text-sm text-gray-700 mb-2">
                    <strong>Stack yang digunakan:</strong>
                </p>
                <ul class="list-disc list-inside text-sm text-gray-600 space-y-1">
                    <li>Laravel 10</li>
                    <li>Laragear/WebAuthn Package</li>
                    <li>SimpleWebAuthn Browser Library</li>
                    <li>Tailwind CSS</li>
                    <li>SQLite Database</li>
                </ul>
            </div>
        </div>

        <div class="mt-6 flex justify-end">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        onclick="event.preventDefault(); this.closest('form').submit();"
                        class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-6 rounded">
                    Logout
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
