<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class NewPasswordController extends Controller
{
    /**
     * Display the password reset view.
     */
    public function create(Request $request): Response|RedirectResponse
    {
        // Check if user has valid session from OTP verification
        $isVerified = $request->session()->get('password_reset_verified');
        $verifiedTime = $request->session()->get('password_reset_verified_time');
        $verifiedEmail = $request->session()->get('password_reset_email_verified');
        
        if (!$isVerified || !$verifiedTime || !$verifiedEmail) {
            return redirect()->route('password.request')->withErrors([
                'email' => 'Sesi tidak valid. Silakan verifikasi OTP terlebih dahulu.'
            ]);
        }

        // Check if session is expired (5 minutes for password reset)
        if (now()->diffInMinutes($verifiedTime) > 5) {
            $request->session()->forget(['password_reset_verified', 'password_reset_verified_time', 'password_reset_email_verified']);
            return redirect()->route('password.request')->withErrors([
                'email' => 'Sesi telah berakhir. Silakan verifikasi OTP ulang.'
            ]);
        }

        // Verify email parameter matches session
        if ($request->email !== $verifiedEmail) {
            return redirect()->route('password.request')->withErrors([
                'email' => 'Email tidak sesuai dengan sesi verifikasi.'
            ]);
        }

        return Inertia::render('auth/ResetPassword', [
            'email' => $request->email,
            'token' => 'otp-verified', // dummy token for compatibility
        ]);
    }

    /**
     * Handle an incoming new password request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Check session validity
        $isVerified = $request->session()->get('password_reset_verified');
        $verifiedEmail = $request->session()->get('password_reset_email_verified');
        
        if (!$isVerified || !$verifiedEmail || $verifiedEmail !== $request->email) {
            return redirect()->route('password.request')->withErrors([
                'email' => 'Sesi tidak valid. Silakan verifikasi OTP terlebih dahulu.'
            ]);
        }

        // Find the user and update password
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'User tidak ditemukan.']);
        }

        // Update the user's password
        $user->forceFill([
            'password' => Hash::make($request->password),
            'remember_token' => Str::random(60),
        ])->save();

        // Clear all password reset sessions
        $request->session()->forget([
            'password_reset_verified', 
            'password_reset_verified_time', 
            'password_reset_email_verified'
        ]);

        event(new PasswordReset($user));

        return redirect()->route('login')->with('status', 'Password berhasil direset. Silakan login dengan password baru.');
    }
}
