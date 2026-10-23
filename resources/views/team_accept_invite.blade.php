<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Aktivasi Akun Admin CS - CloseMateAI</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }
        body {
            background-color: #fbfbfe;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 24px 16px;
            color: #181c32;
        }
        .brand-header {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
            margin-bottom: 24px;
        }
        .brand-icon {
            width: 48px;
            height: 48px;
            background-color: #8c7ae6;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 20px;
            box-shadow: 0 4px 14px rgba(140, 122, 230, 0.35);
        }
        .brand-title {
            font-size: 20px;
            font-weight: 700;
            color: #181c32;
            letter-spacing: -0.3px;
        }
        .card-container {
            background: white;
            width: 100%;
            max-width: 460px;
            border: 1px solid #e2e8f0;
            border-radius: 20px;
            padding: 36px 32px;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.04);
        }
        .card-title {
            font-size: 19px;
            font-weight: 700;
            color: #181c32;
            text-align: center;
            margin-bottom: 6px;
        }
        .card-subtitle {
            font-size: 13px;
            color: #64748b;
            text-align: center;
            line-height: 1.5;
            margin-bottom: 4px;
        }
        .card-email {
            font-size: 13px;
            color: #64748b;
            text-align: center;
            margin-bottom: 24px;
        }
        .form-group {
            margin-bottom: 16px;
        }
        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 8px;
        }
        .form-control {
            width: 100%;
            height: 44px;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 0 14px;
            font-size: 14px;
            color: #181c32;
            outline: none;
            transition: all 0.15s;
            background: #fff;
        }
        .form-control:focus {
            border-color: #6c5ce7;
            box-shadow: 0 0 0 3px rgba(108, 92, 231, 0.1);
        }
        .btn-submit {
            width: 100%;
            height: 46px;
            background: #5b4bc4;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            margin-top: 10px;
            transition: background 0.15s;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .btn-submit:hover {
            background: #4d3db2;
        }
        .alert-error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #b91c1c;
            padding: 10px 14px;
            border-radius: 8px;
            font-size: 13px;
            margin-bottom: 16px;
        }
    </style>
</head>
<body>

    <div class="brand-header">
        <div class="brand-icon">
            <i class="fa-regular fa-heart"></i>
        </div>
        <div class="brand-title">CloseMateAI</div>
    </div>

    <div class="card-container">
        @if(isset($error) && $error)
            <div style="text-align: center; padding: 20px 0;">
                <div style="width: 54px; height: 54px; background: #fee2e2; color: #ef4444; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px auto; font-size: 22px;">
                    <i class="fa-solid fa-circle-exclamation"></i>
                </div>
                <h3 style="font-size: 18px; font-weight: 700; color: #181c32; margin-bottom: 8px;">Undangan Tidak Valid</h3>
                <p style="font-size: 13px; color: #64748b; line-height: 1.6; margin-bottom: 24px;">
                    {{ $error }}
                </p>
                <a href="/login" class="btn-submit" style="text-decoration: none;">
                    Ke Halaman Login
                </a>
            </div>
        @else
            <h2 class="card-title">Selamat Datang di CloseMateAI!</h2>
            <p class="card-subtitle">Silakan buat password baru untuk mengaktifkan akun Admin CS Anda.</p>
            <p class="card-email">{{ $member->email }}</p>

            @if(session('error'))
                <div class="alert-error">{{ session('error') }}</div>
            @endif

            <form method="POST" action="/invite/accept/{{ $token }}">
                @csrf
                <div class="form-group">
                    <label class="form-label">Nama Admin</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $member->name) }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Password Baru</label>
                    <input type="password" name="password" class="form-control" placeholder="Minimal 8 karakter" required minlength="8">
                </div>
                <div class="form-group">
                    <label class="form-label">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" class="form-control" required minlength="8">
                </div>
                <button type="submit" class="btn-submit">
                    Aktifkan Akun & Masuk
                </button>
            </form>
        @endif
    </div>

    @include('components.whatsapp-widget')

</body>
</html>
