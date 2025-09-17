<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\SendResetPasswordOTP;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Inertia\Response;

class PasswordResetLinkController extends Controller
{
    /**
     * Show the password reset link request page.
     */
    public function create(Request $request): Response
    {
        return Inertia::render('auth/ForgotPassword', [
            'status' => $request->session()->get('status'),
        ]);
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->with('status', 'Reset password akan dikirim jika akun tersebut terdaftar.');
        }

        // Generate OTP (4 digits)
        $otpCode = str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);

        // Store OTP in user table (reuse otp_login field)
        $user->update(['otp_login' => $otpCode]);

        // Store email in session to verify OTP access
        $request->session()->put('password_reset_email', $user->email);
        $request->session()->put('password_reset_time', now());

        // Send OTP via email
        Mail::to($user->email)->send(new SendResetPasswordOTP($user));

        return to_route('password.reset.verify', ['email' => $user->email])
            ->with('message', 'Kode OTP untuk reset password telah dikirim ke email Anda.');
    }

    /**
     * Show OTP verification page for password reset
     */
    public function verifyOtp(Request $request): Response|RedirectResponse
    {
        // Check if user has valid session from password reset attempt
        $sessionEmail = $request->session()->get('password_reset_email');
        $sessionTime = $request->session()->get('password_reset_time');

        if (!$sessionEmail || !$sessionTime) {
            return redirect()->route('password.request')->withErrors([
                'email' => 'Sesi tidak valid. Silakan coba lagi.'
            ]);
        }

        // Check if session is expired (10 minutes)
        if (now()->diffInMinutes($sessionTime) > 10) {
            $request->session()->forget(['password_reset_email', 'password_reset_time']);
            return redirect()->route('password.request')->withErrors([
                'email' => 'Sesi telah berakhir. Silakan coba lagi.'
            ]);
        }

        // Verify email parameter matches session
        if ($request->email !== $sessionEmail) {
            return redirect()->route('password.request')->withErrors([
                'email' => 'Email tidak sesuai dengan sesi reset password.'
            ]);
        }

        return Inertia::render('auth/VerifyPasswordResetOtp', [
            'email' => $request->email,
        ]);
    }

    /**
     * Verify OTP for password reset
     */
    public function verifyOtpPost(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => 'required|email',
            'otp_code' => 'required|digits:4',
        ]);

        // Check session validity
        $sessionEmail = $request->session()->get('password_reset_email');
        if (!$sessionEmail || $sessionEmail !== $request->email) {
            return redirect()->route('password.request')->withErrors([
                'email' => 'Sesi tidak valid. Silakan coba lagi.'
            ]);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || $user->otp_login !== $request->otp_code) {
            return back()->withErrors(['otp_code' => 'Kode OTP tidak valid.']);
        }

        // Clear OTP and redirect to reset password form
        $user->update(['otp_login' => null]);

        // Store verification token in session for password reset form
        $request->session()->put('password_reset_verified', true);
        $request->session()->put('password_reset_verified_time', now());
        $request->session()->put('password_reset_email_verified', $user->email);

        return to_route('password.reset', ['email' => $user->email])
            ->with('message', 'OTP berhasil diverifikasi. Silakan buat password baru.');
    }

    /**
     * Resend OTP for password reset
     */
    public function resendOtp(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        // Check session validity
        $sessionEmail = $request->session()->get('password_reset_email');
        if (!$sessionEmail || $sessionEmail !== $request->email) {
            return redirect()->route('password.request')->withErrors([
                'email' => 'Sesi tidak valid. Silakan coba lagi.'
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
        $request->session()->put('password_reset_time', now());

        // Send new OTP via email
        Mail::to($user->email)->send(new SendResetPasswordOTP($user));

        return back()->with([
            'message' => 'Kode OTP baru telah dikirim ke email Anda.',
            'type' => 'success'
        ]);
    }
}
