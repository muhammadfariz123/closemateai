<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { background-color: #f8f9fa; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; margin: 0; padding: 40px 16px; }
        .card { max-width: 580px; margin: 0 auto; background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.06); border: 1px solid #e2e8f0; }
        .header { background: #6c5ce7; padding: 32px; color: white; }
        .header h3 { font-size: 13px; margin: 0 0 10px 0; text-transform: uppercase; letter-spacing: 1.5px; font-weight: 600; opacity: 0.9; }
        .header h1 { font-size: 24px; margin: 0; font-weight: 700; }
        .content { padding: 36px 32px; color: #1e293b; font-size: 15px; line-height: 1.6; }
        .btn { display: inline-block; padding: 13px 28px; background-color: #6c5ce7; color: white !important; text-decoration: none; border-radius: 10px; font-weight: 600; margin: 20px 0 28px 0; font-size: 14px; }
        .steps { background: #f8fafc; border-radius: 10px; padding: 18px 22px; margin-bottom: 24px; font-size: 14px; }
        .steps ol { margin: 8px 0 0 18px; padding: 0; }
        .steps li { margin-bottom: 6px; }
        .footer { font-size: 12px; color: #94a3b8; border-top: 1px solid #f1f5f9; padding-top: 20px; margin-top: 10px; }
    </style>
</head>
<body>
    <div class="card">
        <div class="header">
            <h3>CloseMate AI</h3>
            <h1>Selamat datang, {{ $member->name }}!</h1>
        </div>
        <div class="content">
            <p>Terima kasih telah bergabung dengan CloseMate AI. Workspace studio Anda sudah aktif dan siap digunakan untuk merapikan alur kerja bisnis wedding/event Anda.</p>
            <a href="{{ $dashboardUrl }}" class="btn">Buka Dashboard Utama</a>
            
            <div class="steps">
                <strong>Langkah cepat untuk mulai:</strong>
                <ol>
                    <li>Masuk ke Dashboard menggunakan email yang terdaftar.</li>
                    <li>Lengkapi profil studio & informasi bisnis Anda di menu Pengaturan.</li>
                    <li>Mulai kelola booking, keuangan, atau fitur yang tersedia sesuai paket langganan Anda.</li>
                </ol>
            </div>
            
            <p style="font-size: 13px; color: #64748b;">
                Jika tombol tidak bisa diklik, buka halaman login berikut: <a href="{{ $loginUrl }}" target="_blank">{{ $loginUrl }}</a>
            </p>
            
            <p class="footer">Jika butuh bantuan dalam menggunakan sistem, balas langsung email ini atau hubungi tim support kami.</p>
        </div>
    </div>
</body>
</html>
