<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Kode Verifikasi Login</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h2>Halo {{ $user->name }}!</h2>

        <p>Seseorang mencoba masuk ke akun Anda. Untuk melanjutkan proses login, silakan masukkan kode verifikasi berikut:</p>

        <div style="background-color: #f8f9fa; border: 1px solid #dee2e6; border-radius: 6px; padding: 20px; text-align: center; margin: 20px 0;">
            <h1 style="color: #007bff; font-size: 36px; margin: 0; letter-spacing: 5px;">{{ $user->otp_login }}</h1>
        </div>

        <p>Kode ini hanya berlaku untuk satu kali penggunaan.</p>

        <p>Jika Anda tidak melakukan login, abaikan email ini dan segera ubah password Anda.</p>

        <hr style="border: none; border-top: 1px solid #eee; margin: 20px 0;">
        <p style="font-size: 12px; color: #666;">Email ini dikirim secara otomatis, mohon tidak membalas.</p>
    </div>
</body>
</html>
