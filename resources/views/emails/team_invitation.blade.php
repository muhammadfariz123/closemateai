<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { background-color: #f8f9fa; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; margin: 0; padding: 40px 16px; }
        .card { max-width: 540px; margin: 0 auto; background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.06); border: 1px solid #e2e8f0; }
        .header { background: linear-gradient(135deg, #7c3aed 0%, #6366f1 100%); padding: 36px 32px; color: white; }
        .header h3 { font-size: 13px; margin: 0 0 14px 0; text-transform: uppercase; letter-spacing: 1.5px; font-weight: 600; opacity: 0.9; }
        .header h1 { font-size: 26px; margin: 0; font-weight: 700; }
        .content { padding: 36px 32px; color: #1e293b; font-size: 15px; line-height: 1.6; }
        .btn { display: inline-block; padding: 13px 28px; background-color: #5542f6; color: white !important; text-decoration: none; border-radius: 10px; font-weight: 600; margin: 24px 0 32px 0; font-size: 14px; }
        .footer { font-size: 12px; color: #94a3b8; border-top: 1px solid #f1f5f9; padding-top: 20px; margin-top: 10px; }
        a { color: #6366f1; text-decoration: none; font-weight: 500; }
    </style>
</head>
<body>
    <div class="card">
        <div class="header">
            <h3>CLOSEMATEAI</h3>
            <h1>Anda diundang</h1>
        </div>
        <div class="content">
            <p>Anda diundang untuk bergabung ke workspace CloseMateAI (<a href="{{ url('/') }}" target="_blank">{{ url('/') }}</a>). Terima undangan dengan menekan tombol di bawah ini.</p>
            <a href="{{ $acceptUrl }}" class="btn">Terima Undangan</a>
            <p class="footer">Jika Anda merasa tidak mengenal undangan ini, abaikan saja email ini.</p>
        </div>
    </div>
</body>
</html>
