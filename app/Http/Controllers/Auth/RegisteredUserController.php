<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Mail\OtpMail;
use App\Mail\SendRegisterOTP;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Show the registration page.
     */
    public function create(): Response
    {
        return Inertia::render('auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Generate OTP (4 digits)
        $otpCode = str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'otp_register' => $otpCode,
        ]);

        $user->assignRole('user');

        // Store email in session to verify OTP access
        $request->session()->put('otp_register_email', $user->email);
        $request->session()->put('otp_register_time', now());

        // Send OTP via email - pass the User object
        Mail::to($user->email)->send(new SendRegisterOTP($user));

        return to_route('otp.verify', ['email' => $user->email])
            ->with('message', 'Kode OTP telah dikirim ke email Anda.');
    }

    public function resendOtp(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        // Check session validity
        $sessionEmail = $request->session()->get('otp_register_email');
        if (!$sessionEmail || $sessionEmail !== $request->email) {
            return redirect()->route('register')->withErrors([
                'email' => 'Sesi tidak valid. Silakan daftar terlebih dahulu.'
            ]);
        }

        $user = User::where('email', $request->email)->firstOrFail();

        // Generate new OTP (4 digits)
        $otpCode = str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);
        $user->update(['otp_register' => $otpCode]);

        // Update session time
        $request->session()->put('otp_register_time', now());

        // Send new OTP via email
        Mail::to($user->email)->send(new SendRegisterOTP($user));

        return back()->with([
            'message' => 'Kode OTP baru telah dikirim ke email Anda.',
            'type' => 'success'
        ]);
    }

    public function verifyOtp(Request $request): Response|RedirectResponse
    {
        // Check if user has valid session from registration attempt
        $sessionEmail = $request->session()->get('otp_register_email');
        $sessionTime = $request->session()->get('otp_register_time');

        if (!$sessionEmail || !$sessionTime) {
            return redirect()->route('register')->withErrors([
                'email' => 'Sesi tidak valid. Silakan daftar terlebih dahulu.'
            ]);
        }

        // Check if session is expired (10 minutes)
        if (now()->diffInMinutes($sessionTime) > 10) {
            $request->session()->forget(['otp_register_email', 'otp_register_time']);
            return redirect()->route('register')->withErrors([
                'email' => 'Sesi telah berakhir. Silakan daftar ulang.'
            ]);
        }

        // Verify email parameter matches session
        if ($request->email !== $sessionEmail) {
            return redirect()->route('register')->withErrors([
                'email' => 'Email tidak sesuai dengan sesi registrasi.'
            ]);
        }

        return Inertia::render('auth/VerifyOtp', [
            'email' => $request->email,
        ]);
    }

    public function verifyOtpPost(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => 'required|email',
            'otp_code' => 'required|digits:4',
        ]);

        // Check session validity
        $sessionEmail = $request->session()->get('otp_register_email');
        if (!$sessionEmail || $sessionEmail !== $request->email) {
            return redirect()->route('register')->withErrors([
                'email' => 'Sesi tidak valid. Silakan daftar terlebih dahulu.'
            ]);
        }

        $user = User::where('email', $request->email)->firstOrFail();

        if ($user->otp_register !== $request->otp_code) {
            return back()->withErrors(['otp_code' => 'Kode OTP tidak valid.']);
        }

        // Mark user as verified with explicit timestamp in method
        $user->email_verified_at = now();
        $user->otp_register = null;
        $user->save();
        // Clear session data
        $request->session()->forget(['otp_register_email', 'otp_register_time']);

        // Fire registered event after verification
        event(new Registered($user));

        // Login user
        Auth::login($user);

        return to_route('user.dashboard')->with('message', 'Registrasi berhasil, selamat datang!');
    }
}
