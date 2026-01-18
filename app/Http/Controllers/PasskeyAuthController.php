<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PasskeyAuthController extends Controller
{
    /**
     * Show the registration form
     */
    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * Show the login form
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Register a new user
     */
    public function register(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255',
            ]);

            // Check if user already exists
            $user = User::where('email', $validated['email'])->first();
            
            if ($user) {
                // User exists, just login
                Auth::login($user);
                // Don't regenerate session during AJAX flow - will cause CSRF mismatch
                // $request->session()->regenerate();
                $request->session()->save(); // Explicitly save session
                
                return response()->json([
                    'success' => true,
                    'message' => 'User already exists. Logged in successfully.',
                    'existing_user' => true,
                    'csrf_token' => csrf_token(),
                    'user_id' => $user->id,
                    'authenticated' => Auth::check()
                ]);
            }

            // Create new user
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => bcrypt('temporary_password'), // Password tidak digunakan untuk passkey
            ]);

            Auth::login($user);
            
            // Don't regenerate session during AJAX flow - will cause CSRF mismatch
            // Session will be regenerated on successful passkey registration
            // $request->session()->regenerate();
            $request->session()->save(); // Explicitly save session

            return response()->json([
                'success' => true,
                'message' => 'User created successfully',
                'existing_user' => false,
                'csrf_token' => csrf_token(),
                'user_id' => $user->id,
                'authenticated' => Auth::check()
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    /**
     * Show dashboard
     */
    public function dashboard()
    {
        return view('dashboard');
    }
}
