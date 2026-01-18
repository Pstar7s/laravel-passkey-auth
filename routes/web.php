<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PasskeyAuthController;
use App\Http\Controllers\WebAuthn\WebAuthnRegisterController;
use App\Http\Controllers\WebAuthn\WebAuthnLoginController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// Authentication Routes
Route::get('/register', [PasskeyAuthController::class, 'showRegister'])->name('register');
Route::post('/register', [PasskeyAuthController::class, 'register']);
Route::get('/login', [PasskeyAuthController::class, 'showLogin'])->name('login');
Route::post('/logout', [PasskeyAuthController::class, 'logout'])->name('logout');

// WebAuthn Routes (Laragear)
Route::post('/webauthn/register/options', [WebAuthnRegisterController::class, 'options'])->name('webauthn.register.options');
Route::post('/webauthn/register', [WebAuthnRegisterController::class, 'register'])->name('webauthn.register');
Route::post('/webauthn/login/options', [WebAuthnLoginController::class, 'options'])->name('webauthn.login.options');
Route::post('/webauthn/login', [WebAuthnLoginController::class, 'login'])->name('webauthn.login');

// Protected Routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [PasskeyAuthController::class, 'dashboard'])->name('dashboard');
});
