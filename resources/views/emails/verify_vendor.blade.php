<!DOCTYPE html>
<html>
<head>
    <style>
        body { background-color: #f8f9fc; font-family: 'Helvetica Neue', Arial, sans-serif; padding: 40px; margin: 0; }
        .card { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; }
        .header { background-color: #8352ff; padding: 40px 30px; color: white; }
        .header h3 { font-size: 13px; margin: 0 0 10px 0; text-transform: uppercase; letter-spacing: 1px; font-weight: 500; }
        .header h1 { font-size: 26px; margin: 0; font-weight: 600; }
        .content { padding: 40px 30px; color: #1e293b; font-size: 15px; line-height: 1.6; }
        .btn { display: inline-block; padding: 12px 24px; background-color: #6b5cd8; color: white !important; text-decoration: none; border-radius: 8px; font-weight: 600; margin-top: 20px; margin-bottom: 30px; }
        .footer { font-size: 13px; color: #94a3b8; }
        a { color: #6b5cd8; font-weight: 500; }
    </style>
</head>
<body>
    <div class="card">
        <div class="header">
            <h3>CLOSEMATEAI</h3>
            <h1>Konfirmasi email Anda</h1>
        </div>
        <div class="content">
            <p>Terima kasih sudah mendaftar di <a href="#">CloseMateAI</a>.</p>
            <p>Silakan konfirmasi alamat email Anda (<a href="mailto:{{ $user->email }}">{{ $user->email }}</a>) dengan menekan tombol di bawah ini:</p>
            <a href="{{ $verificationUrl }}" class="btn">Verifikasi Email</a>
            <p class="footer">Jika Anda tidak membuat akun ini, abaikan saja email ini.</p>
        </div>
    </div>
</body>
</html>
