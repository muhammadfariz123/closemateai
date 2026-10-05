<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Laravel\Socialite\Facades\Socialite;
use App\Mail\VerifyVendorEmail;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (auth()->attempt($credentials)) {
            $request->session()->regenerate();
            return redirect('/dashboard');
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->withInput($request->only('email'));
    }

    public function register(Request $request)
    {
        $request->validate([
            'bisnis' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'kategori' => 'required|string',
        ]);

        $user = User::create([
            'name' => $request->bisnis,
            'business_name' => $request->bisnis,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'category' => $request->kategori,
        ]);

        // Send email
        Mail::to($user->email)->send(new VerifyVendorEmail($user));

        return response()->json(['success' => true, 'message' => 'Registration successful']);
    }

    public function verifyEmail($id, $hash)
    {
        $user = User::findOrFail($id);
        
        if (sha1($user->email) !== $hash) {
            abort(403);
        }

        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }
        
        auth()->login($user);
        return redirect('/dashboard');
    }

    public function logout(Request $request)
    {
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            $user = User::where('email', $googleUser->getEmail())->first();
            
            if (!$user) {
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'business_name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'password' => null,
                    'email_verified_at' => now(), // Google emails are pre-verified
                ]);
            } else {
                $user->update(['google_id' => $googleUser->getId()]);
            }
            
            auth()->login($user);
            return redirect('/dashboard');
            
        } catch (\Exception $e) {
            return redirect('/')->withErrors(['google' => 'Gagal login menggunakan Google.']);
        }
    }
}
