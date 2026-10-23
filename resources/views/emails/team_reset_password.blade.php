<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { background-color: #f8f9fa; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; margin: 0; padding: 40px 16px; }
        .card { max-width: 540px; margin: 0 auto; background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.06); border: 1px solid #e2e8f0; }
        .header { background: #6c5ce7; padding: 32px; color: white; }
        .header h3 { font-size: 13px; margin: 0 0 10px 0; text-transform: uppercase; letter-spacing: 1.5px; font-weight: 600; opacity: 0.9; }
        .header h1 { font-size: 24px; margin: 0; font-weight: 700; }
        .content { padding: 36px 32px; color: #1e293b; font-size: 15px; line-height: 1.6; }
        .btn { display: inline-block; padding: 13px 28px; background-color: #6c5ce7; color: white !important; text-decoration: none; border-radius: 10px; font-weight: 600; margin: 24px 0 32px 0; font-size: 14px; }
        .footer { font-size: 12px; color: #94a3b8; border-top: 1px solid #f1f5f9; padding-top: 20px; margin-top: 10px; }
    </style>
</head>
<body>
    <div class="card">
        <div class="header">
            <h3>CloseMateAI</h3>
            <h1>Atur ulang kata sandi</h1>
        </div>
        <div class="content">
            <p>Kami menerima permintaan untuk mengatur ulang kata sandi akun CloseMateAI Anda. Tekan tombol di bawah untuk membuat kata sandi baru.</p>
            <a href="{{ $resetUrl }}" class="btn">Atur Ulang Kata Sandi</a>
            <p class="footer">Jika Anda tidak meminta perubahan ini, abaikan email ini — kata sandi Anda tetap aman.</p>
        </div>
    </div>
</body>
</html>
