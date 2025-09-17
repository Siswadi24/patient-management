<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Mail\SendLoginOTP;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;

class AuthenticatedSessionController extends Controller
{
    /**
     * Show the login page.
     */
    public function create(Request $request): Response
    {
        return Inertia::render('auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => $request->session()->get('status'),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ]);
        }

        // Generate OTP (4 digits)
        $otpCode = str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);

        // Store OTP in user table
        $user->update(['otp_login' => $otpCode]);

        // Store email in session to verify OTP access
        $request->session()->put('otp_login_email', $user->email);
        $request->session()->put('otp_login_time', now());

        // Send OTP via email
        Mail::to($user->email)->send(new SendLoginOTP($user));

        return to_route('login.otp.verify', ['email' => $user->email])
            ->with('message', 'Kode OTP telah dikirim ke email Anda.');
    }

    public function verifyOtp(Request $request): Response|RedirectResponse
    {
        // Check if user has valid session from login attempt
        $sessionEmail = $request->session()->get('otp_login_email');
        $sessionTime = $request->session()->get('otp_login_time');

        if (!$sessionEmail || !$sessionTime) {
            return redirect()->route('login')->withErrors([
                'email' => 'Sesi tidak valid. Silakan login terlebih dahulu.'
            ]);
        }

        // Check if session is expired (10 minutes)
        if (now()->diffInMinutes($sessionTime) > 10) {
            $request->session()->forget(['otp_login_email', 'otp_login_time']);
            return redirect()->route('login')->withErrors([
                'email' => 'Sesi telah berakhir. Silakan login ulang.'
            ]);
        }

        // Verify email parameter matches session
        if ($request->email !== $sessionEmail) {
            return redirect()->route('login')->withErrors([
                'email' => 'Email tidak sesuai dengan sesi login.'
            ]);
        }

        return Inertia::render('auth/VerifyLoginOtp', [
            'email' => $request->email,
        ]);
    }

    /**
     * Verify OTP for login
     */
    public function verifyOtpPost(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => 'required|email',
            'otp_code' => 'required|digits:4',
        ]);

        // Check session validity
        $sessionEmail = $request->session()->get('otp_login_email');
        if (!$sessionEmail || $sessionEmail !== $request->email) {
            return redirect()->route('login')->withErrors([
                'email' => 'Sesi tidak valid. Silakan login terlebih dahulu.'
            ]);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || $user->otp_login !== $request->otp_code) {
            return back()->withErrors(['otp_code' => 'Kode OTP tidak valid.']);
        }

        // Clear OTP and session data
        $user->update(['otp_login' => null]);
        $request->session()->forget(['otp_login_email', 'otp_login_time']);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->intended(route('user.dashboard', absolute: false));
    }

    /**
     * Resend OTP for login
     */
    public function resendLoginOtp(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        // Check session validity
        $sessionEmail = $request->session()->get('otp_login_email');
        if (!$sessionEmail || $sessionEmail !== $request->email) {
            return redirect()->route('login')->withErrors([
                'email' => 'Sesi tidak valid. Silakan login terlebih dahulu.'
            ]);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'User tidak ditemukan.']);
        }

        // Generate new OTP (4 digits)
        $otpCode = str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);
        $user->update(['otp_login' => $otpCode]);

        // Update session time
        $request->session()->put('otp_login_time', now());

        // Send new OTP via email
        Mail::to($user->email)->send(new SendLoginOTP($user));

        return back()->with([
            'message' => 'Kode OTP baru telah dikirim ke email Anda.',
            'type' => 'success'
        ]);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
