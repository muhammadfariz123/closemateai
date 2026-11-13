<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Studio HRIS">
    <meta name="application-name" content="Studio HRIS">
    <meta name="theme-color" content="#059669">
    <title>HRIS Studio Absensi</title>

    <!-- PWA Manifest & Icons -->
    <link rel="manifest" href="/manifest.json">
    <link rel="icon" type="image/png" sizes="192x192" href="/hris-icon-192.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/hris-icon-192.png">

    <!-- Fonts & Icons (Ionicons + Plus Jakarta Sans) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

@php
    $appTheme = $settings->theme_color ?? (auth()->check() ? auth()->user()->getThemeColor() : '#4472c4');
@endphp
    <style>
        :root {
            --primary: {{ $appTheme }};
            --primary-dark: #1e3a8a;
            --primary-light: #eff6ff;
            --primary-border: #bfdbfe;
            --emerald-500: {{ $appTheme }};
            --emerald-600: {{ $appTheme }};
            --emerald-700: {{ $appTheme }};
            --emerald-900: #1e3a8a;
            --bg-page: #F3F4F6;
            --surface: #FFFFFF;
            --text-main: #111827;
            --text-muted: #6B7280;
            --border: #E5E7EB;
            --safe-bottom: env(safe-area-inset-bottom, 16px);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            -webkit-tap-highlight-color: transparent;
        }

        html, body {
            background: #0B131A;
            color: var(--text-main);
            min-height: 100vh;
            width: 100%;
            height: 100%;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Pure Mobile App Container */
        .app-root {
            width: 100%;
            max-width: 460px;
            height: 100%;
            max-height: 940px;
            background: #F9FAFB;
            display: flex;
            flex-direction: column;
            position: relative;
            overflow: hidden;
            box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.5);
            border-radius: 36px;
            border: 8px solid #1F2937;
        }

        @media (max-width: 600px) {
            body { background: #F9FAFB; }
            .app-root {
                max-width: 100%;
                height: 100vh;
                max-height: 100vh;
                border-radius: 0;
                border: none;
            }
        }

        /* Top Mobile App Header */
        .app-header {
            background: linear-gradient(135deg, #064E3B 0%, #047857 50%, #059669 100%);
            color: #FFFFFF;
            padding: 20px 18px 16px;
            flex-shrink: 0;
            position: relative;
            border-bottom-left-radius: 24px;
            border-bottom-right-radius: 24px;
            box-shadow: 0 10px 25px -5px rgba(5, 150, 105, 0.3);
            z-index: 20;
        }

        .header-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .user-badge-container {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .user-avatar {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            border: 2px solid rgba(255, 255, 255, 0.85);
            object-fit: cover;
            background: #10B981;
        }

        .header-title-box h3 {
            font-size: 15px;
            font-weight: 800;
            line-height: 1.2;
            color: #FFFFFF;
        }

        .header-division-tag {
            display: inline-block;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(4px);
            font-size: 11px;
            font-weight: 600;
            padding: 2px 8px;
            border-radius: 12px;
            margin-top: 3px;
            color: #E6FFFA;
        }

        .role-switch-pill {
            background: rgba(255, 255, 255, 0.18);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: #FFFFFF;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 6px;
            cursor: pointer;
            transition: all 0.2s;
        }
        .role-switch-pill:active { transform: scale(0.96); background: rgba(255,255,255,0.28); }

        /* Main Scrollable View Area */
        .app-body {
            flex: 1;
            overflow-y: auto;
            padding: 16px 16px 85px;
            position: relative;
            -webkit-overflow-scrolling: touch;
        }

        /* Bottom Tab Navigation Bar */
        .bottom-nav {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 68px;
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(14px);
            border-top: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-around;
            padding-bottom: calc(var(--safe-bottom) - 8px);
            z-index: 50;
        }

        .nav-tab-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: var(--text-muted);
            font-size: 10px;
            font-weight: 600;
            text-decoration: none;
            background: none;
            border: none;
            cursor: pointer;
            padding: 4px 10px;
            border-radius: 12px;
            transition: all 0.2s;
        }

        .nav-tab-item ion-icon {
            font-size: 22px;
            margin-bottom: 2px;
            transition: transform 0.2s;
        }

        .nav-tab-item.active {
            color: var(--primary);
            font-weight: 800;
        }

        .nav-tab-item.active ion-icon {
            transform: translateY(-2px);
        }

        /* Cards & Components */
        .card {
            background: var(--surface);
            border-radius: 20px;
            padding: 16px;
            margin-bottom: 14px;
            border: 1px solid var(--border);
            box-shadow: 0 4px 16px -3px rgba(0, 0, 0, 0.04);
        }

        .card-header-flex {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
        }

        .card-title {
            font-size: 14px;
            font-weight: 800;
            color: var(--text-main);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        /* Badges */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
        }
        .badge-success { background: #D1FAE5; color: #047857; }
        .badge-warning { background: #FEF3C7; color: #B45309; }
        .badge-danger { background: #FEE2E2; color: #B91C1C; }
        .badge-info { background: #E0F2FE; color: #0369A1; }
        .badge-purple { background: #F3E8FF; color: #7E22CE; }

        /* Time & Date Display */
        .clock-hero-box {
            background: #FFFFFF;
            border-radius: 20px;
            padding: 18px 16px;
            margin-bottom: 14px;
            border: 1px solid var(--border);
            text-align: center;
            box-shadow: 0 6px 20px -4px rgba(0,0,0,0.05);
        }

        .clock-time {
            font-size: 34px;
            font-weight: 800;
            color: #064E3B;
            letter-spacing: -1px;
        }

        .clock-date {
            font-size: 12px;
            color: var(--text-muted);
            font-weight: 600;
            margin-top: 2px;
        }

        /* 2 Side-by-Side Metric Cards */
        .metrics-2grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-bottom: 14px;
        }

        .metric-mini-card {
            background: #FFFFFF;
            border-radius: 16px;
            padding: 14px;
            border: 1px solid var(--border);
            box-shadow: 0 4px 12px -2px rgba(0,0,0,0.03);
        }

        .metric-mini-label {
            font-size: 11px;
            font-weight: 700;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            gap: 5px;
            text-transform: uppercase;
        }

        .metric-mini-val {
            font-size: 18px;
            font-weight: 800;
            color: #111827;
            margin-top: 6px;
        }

        .metric-mini-sub {
            font-size: 10px;
            color: #9CA3AF;
            margin-top: 2px;
        }

        /* Geofence Status Pill Banner */
        .geofence-banner {
            background: #ECFDF5;
            border: 1px solid #A7F3D0;
            border-radius: 16px;
            padding: 12px 14px;
            margin-bottom: 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .geofence-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .geofence-icon {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: #D1FAE5;
            color: #059669;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }

        /* Primary Action Buttons */
        .btn-clock-in {
            width: 100%;
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            color: #FFFFFF;
            border: none;
            padding: 15px;
            border-radius: 18px;
            font-size: 15px;
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            cursor: pointer;
            box-shadow: 0 8px 25px rgba(5, 150, 105, 0.4);
            margin-bottom: 10px;
            transition: all 0.2s;
        }
        .btn-clock-in:active { transform: scale(0.98); }

        .btn-clock-out {
            width: 100%;
            background: linear-gradient(135deg, #DC2626 0%, #B91C1C 100%);
            color: #FFFFFF;
            border: none;
            padding: 15px;
            border-radius: 18px;
            font-size: 15px;
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            cursor: pointer;
            box-shadow: 0 8px 25px rgba(220, 38, 38, 0.35);
            transition: all 0.2s;
        }
        .btn-clock-out:active { transform: scale(0.98); }

        /* PWA Install Promo Box */
        .pwa-promo-box {
            background: linear-gradient(135deg, #064E3B 0%, #047857 100%);
            color: #FFFFFF;
            border-radius: 18px;
            padding: 16px;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            cursor: pointer;
            box-shadow: 0 8px 20px rgba(6, 78, 59, 0.3);
        }

        .pwa-promo-title {
            font-size: 13px;
            font-weight: 800;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .pwa-promo-sub {
            font-size: 11px;
            opacity: 0.85;
            margin-top: 3px;
            line-height: 1.3;
        }

        /* Camera Modal Overlay */
        .modal-overlay {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(11, 19, 26, 0.8);
            backdrop-filter: blur(6px);
            display: none;
            align-items: flex-end;
            justify-content: center;
            z-index: 1000;
        }
        .modal-overlay.active { display: flex; }

        .modal-sheet {
            width: 100%;
            max-width: 460px;
            background: #FFFFFF;
            border-top-left-radius: 28px;
            border-top-right-radius: 28px;
            padding: 22px 20px 30px;
            max-height: 88vh;
            overflow-y: auto;
            animation: sheetUp 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }
        @keyframes sheetUp {
            from { transform: translateY(100%); }
            to { transform: translateY(0); }
        }

        .modal-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
            padding-bottom: 12px;
            border-bottom: 1px solid var(--border);
        }

        .modal-title { font-size: 16px; font-weight: 800; color: var(--text-main); }

        .modal-close-btn {
            background: #F3F4F6;
            border: none;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            color: var(--text-muted);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Camera Viewfinder */
        .camera-box {
            width: 100%;
            height: 310px;
            background: #000000;
            border-radius: 24px;
            overflow: hidden;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 16px;
        }

        #camera-feed {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transform: scaleX(-1);
        }

        .face-oval-guide {
            position: absolute;
            width: 190px;
            height: 250px;
            border: 2px dashed rgba(52, 211, 153, 0.9);
            border-radius: 50% / 60%;
            box-shadow: 0 0 0 9999px rgba(0, 0, 0, 0.45);
            pointer-events: none;
        }

        .scan-beam {
            position: absolute;
            width: 100%;
            height: 2px;
            background: linear-gradient(90deg, transparent, #34D399, #FFFFFF, #34D399, transparent);
            box-shadow: 0 0 14px #34D399;
            animation: radarBeam 2s infinite alternate ease-in-out;
        }
        @keyframes radarBeam {
            0% { top: 12%; opacity: 0.5; }
            100% { top: 88%; opacity: 1; }
        }

        /* Form elements */
        .form-group { margin-bottom: 14px; }
        .form-label { display: block; font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 6px; }
        .form-control {
            width: 100%;
            padding: 12px 14px;
            border-radius: 14px;
            border: 1px solid var(--border);
            font-size: 14px;
            outline: none;
            background: #F9FAFB;
            color: var(--text-main);
            transition: border 0.2s;
        }
        .form-control:focus { border-color: var(--primary); background: #FFFFFF; }

        /* Segmented Buttons */
        .segmented-row {
            display: flex;
            background: #E5E7EB;
            padding: 4px;
            border-radius: 14px;
            margin-bottom: 14px;
        }

        .segment-tab-btn {
            flex: 1;
            padding: 9px 12px;
            border: none;
            background: none;
            font-size: 12px;
            font-weight: 800;
            color: var(--text-muted);
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.2s;
        }
        .segment-tab-btn.active {
            background: #FFFFFF;
            color: var(--emerald-700);
            box-shadow: 0 2px 6px rgba(0,0,0,0.06);
        }

        .quick-presets-row {
            display: flex;
            gap: 6px;
            overflow-x: auto;
            margin-bottom: 12px;
            padding-bottom: 4px;
        }

        .preset-chip {
            background: #ECFDF5;
            border: 1px solid #A7F3D0;
            color: #065F46;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            white-space: nowrap;
            cursor: pointer;
        }

        /* History items */
        .list-item-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #F3F4F6;
        }
        .list-item-row:last-child { border-bottom: none; }
    </style>
</head>
<body>

<div class="app-root">

    <!-- ============================================== -->
    <!-- 1. LOGIN SCREEN (If Not Logged In)             -->
    <!-- ============================================== -->
    <div id="screen-login" style="display: none; height: 100%; overflow-y: auto; padding: 30px 20px 40px;">
        <div style="text-align: center; margin-bottom: 24px;">
            <img src="/hris-icon-192.png" alt="Logo" style="width: 72px; height: 72px; border-radius: 18px; box-shadow: 0 8px 25px rgba(5,150,105,0.3); margin-bottom: 14px;">
            <h2 style="font-size: 22px; font-weight: 800; color: #064E3B;">Studio Foto HRIS</h2>
            <p style="font-size: 12px; color: var(--text-muted); margin-top: 4px; line-height: 1.4;">
                Sistem Absensi Selfie + GPS, Cuti, Lembur & Slip Gaji Digital
            </p>
        </div>

        <!-- Tab Masuk / Daftar -->
        <div class="segmented-row">
            <button class="segment-tab-btn active" id="tabAuthLogin" onclick="switchAuthTab('login')">Masuk Akun</button>
            <button class="segment-tab-btn" id="tabAuthRegister" onclick="switchAuthTab('register')">Buat Akun Baru</button>
        </div>

        <!-- Form Login -->
        <form id="formLogin" onsubmit="handleAuthLogin(event)" style="margin-top: 10px;">
            <div id="authRegisterNameGroup" class="form-group" style="display: none;">
                <label class="form-label">Nama Lengkap</label>
                <div style="position: relative;">
                    <input type="text" class="form-control" id="loginName" placeholder="Contoh: Budi Owner Studio">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Email Karyawan / Admin</label>
                <input type="email" class="form-control" id="loginEmail" placeholder="nama@studiofoto.com" value="warungkombas@gmail.com" required>
            </div>

            <div class="form-group">
                <label class="form-label">Password</label>
                <div style="position: relative;">
                    <input type="password" class="form-control" id="loginPassword" placeholder="Minimal 6 karakter" value="Fariz123@" required>
                    <span onclick="togglePasswordVisibility('loginPassword')" style="position: absolute; right: 14px; top: 12px; cursor: pointer; color: #9CA3AF; font-size: 18px;">
                        <ion-icon name="eye-outline"></ion-icon>
                    </span>
                </div>
            </div>

            <button type="submit" class="btn-clock-in" id="btnLoginSubmit" style="margin-top: 14px;">
                <ion-icon name="log-in-outline" style="font-size: 20px;"></ion-icon>
                <span id="btnLoginText">Masuk Aplikasi</span>
            </button>
        </form>

        <!-- Quick 1-Click Demo Login Pill -->
        <div onclick="fillDemoCredentials()" style="margin-top: 14px; text-align: center; background: #ECFDF5; border: 1px dashed #10B981; padding: 10px; border-radius: 12px; cursor: pointer;">
            <div style="font-size: 11px; font-weight: 700; color: #047857;">👉 Ketuk di sini untuk Akun Demo Owner / Admin:</div>
            <div style="font-size: 11px; color: #064E3B; margin-top: 2px;">Email: <b>warungkombas@gmail.com</b> | Pass: <b>Fariz123@</b></div>
        </div>

        <!-- PWA Install Banner -->
        <div class="pwa-promo-box" onclick="promptPwaInstall()" style="margin-top: 24px;">
            <div>
                <div class="pwa-promo-title">
                    <ion-icon name="phone-portrait-outline"></ion-icon>
                    Instal Aplikasi Mobile (PWA)
                </div>
                <div class="pwa-promo-sub">
                    Ketuk di sini untuk menginstal aplikasi fullscreen tanpa address bar dengan ikon HD di layar utama HP Anda.
                </div>
            </div>
            <ion-icon name="download-outline" style="font-size: 24px;"></ion-icon>
        </div>

        <p style="font-size: 11px; color: #9CA3AF; text-align: center; margin-top: 16px; line-height: 1.5;">
            Satu aplikasi untuk Karyawan & Admin. Tampilan menu otomatis menyesuaikan hak akses akun Anda di database.
        </p>
    </div>

    <!-- ============================================== -->
    <!-- 2. MAIN APPLICATION (When Logged In)          -->
    <!-- ============================================== -->
    <div id="screen-main" style="display: flex; flex-direction: column; height: 100%;">
        
        <!-- App Top Header -->
        <header class="app-header">
            <div class="header-top">
                <div class="user-badge-container">
                    <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=150" alt="Avatar" class="user-avatar" id="appHeaderAvatar">
                    <div class="header-title-box">
                        <h3 id="appHeaderName">Memuat...</h3>
                        <span class="header-division-tag" id="appHeaderDivisionTag">Fotografer</span>
                    </div>
                </div>

                <div id="appRoleSwitchContainer">
                    <button class="role-switch-pill" onclick="toggleAppRoleView()">
                        <ion-icon name="repeat-outline"></ion-icon>
                        <span id="roleViewLabel">Mode Admin</span>
                    </button>
                </div>
            </div>
        </header>

        <!-- App Body (Tabs Container) -->
        <div class="app-body">

            <!-- PWA Install Notice (Floating) -->
            <div id="floatingInstallBanner" class="pwa-promo-box" onclick="promptPwaInstall()" style="display: none; padding: 12px 14px; margin-bottom: 12px;">
                <div>
                    <div class="pwa-promo-title" style="font-size: 12px;">
                        <ion-icon name="cloud-download-outline"></ion-icon>
                        Pasang Aplikasi di Layar Utama HP
                    </div>
                    <div class="pwa-promo-sub" style="font-size: 10px;">
                        Buka aplikasi layaknya native app tanpa browser bar.
                    </div>
                </div>
                <ion-icon name="chevron-forward" style="font-size: 18px;"></ion-icon>
            </div>

            <!-- ============================================== -->
            <!-- EMPLOYEE TAB 1: ABSENSI                        -->
            <!-- ============================================== -->
            <div id="tab-emp-absen" class="tab-pane">
                <!-- Clock Hero -->
                <div class="clock-hero-box">
                    <div class="clock-time" id="empLiveClock">08:00:00</div>
                    <div class="clock-date" id="empLiveDate">Memuat tanggal...</div>
                </div>

                <!-- 2 Metrics: Sisa Cuti & Shift Hari Ini -->
                <div class="metrics-2grid">
                    <div class="metric-mini-card">
                        <div class="metric-mini-label"><ion-icon name="calendar-outline"></ion-icon> Sisa Cuti</div>
                        <div class="metric-mini-val" id="empLeaveBalanceVal">12 Hari</div>
                        <div class="metric-mini-sub">Kuota 12 hari/thn</div>
                    </div>
                    <div class="metric-mini-card">
                        <div class="metric-mini-label"><ion-icon name="time-outline"></ion-icon> Jadwal Shift</div>
                        <div class="metric-mini-val" style="font-size: 14px; margin-top: 8px;" id="empShiftVal">Shift Pagi</div>
                        <div class="metric-mini-sub">08:00 - 17:00 WIB</div>
                    </div>
                </div>

                <!-- Studio Geofence Bar -->
                <div class="geofence-banner">
                    <div class="geofence-info">
                        <div class="geofence-icon" id="geofenceIconBox"><ion-icon name="location-outline"></ion-icon></div>
                        <div>
                            <div style="font-size: 11px; font-weight: 700; color: #064E3B;" id="geofenceStudioTitle">Studio Foto Utama</div>
                            <div style="font-size: 13px; font-weight: 800; color: #047857;" id="geofenceDistanceText">Mendeteksi GPS...</div>
                        </div>
                    </div>
                    <span id="marketingBypassBadge" class="badge badge-purple" style="display: none;">
                        Marketing · Bebas Jarak
                    </span>
                </div>

                <!-- Clock In & Clock Out Action Buttons -->
                <button class="btn-clock-in" onclick="openCameraModal('in')">
                    <ion-icon name="camera-outline" style="font-size: 22px;"></ion-icon>
                    <span>Clock In — Selfie & GPS</span>
                </button>

                <button class="btn-clock-out" onclick="handleClockOutClick()">
                    <ion-icon name="log-out-outline" style="font-size: 22px;"></ion-icon>
                    <span>Clock Out — Selesai Kerja</span>
                </button>
                <div style="text-align: center; font-size: 11px; color: #9CA3AF; margin-top: 6px; margin-bottom: 14px;">
                    *Sistem mewajibkan foto kamera langsung dan deteksi GPS aktif.
                </div>

                <!-- Status Kehadiran Hari Ini Card -->
                <div class="card">
                    <div class="card-header-flex">
                        <div class="card-title"><ion-icon name="checkmark-done-circle-outline" style="color: #10B981; font-size: 18px;"></ion-icon> Status Kehadiran Hari Ini</div>
                        <span id="empTodayBadge" class="badge badge-warning">Belum Clock In</span>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; background: #F9FAFB; padding: 12px; border-radius: 14px;">
                        <div>
                            <div style="font-size: 11px; color: var(--text-muted); font-weight: 600;">Jam Masuk:</div>
                            <div style="font-size: 15px; font-weight: 800; color: #111827; margin-top: 2px;" id="empTodayInTime">--:--</div>
                            <div style="font-size: 10px; color: #6B7280;" id="empTodayInDist">-- m dari Studio</div>
                        </div>
                        <div>
                            <div style="font-size: 11px; color: var(--text-muted); font-weight: 600;">Jam Pulang:</div>
                            <div style="font-size: 15px; font-weight: 800; color: #111827; margin-top: 2px;" id="empTodayOutTime">Belum Pulang</div>
                            <div style="font-size: 10px; color: #6B7280;" id="empTodayWorkDuration">-</div>
                        </div>
                    </div>
                </div>

                <!-- Riwayat Absensi Terbaru -->
                <div class="card">
                    <div class="card-header-flex">
                        <div class="card-title"><ion-icon name="time-outline" style="color: #059669; font-size: 18px;"></ion-icon> Riwayat Absensi Terbaru</div>
                    </div>
                    <div id="empAttendanceHistoryList">
                        <div style="font-size: 12px; color: var(--text-muted); text-align: center; padding: 15px;">Memuat data absensi...</div>
                    </div>
                </div>
            </div>

            <!-- ============================================== -->
            <!-- EMPLOYEE TAB 2: CUTI                           -->
            <!-- ============================================== -->
            <div id="tab-emp-cuti" class="tab-pane" style="display: none;">
                <div class="card">
                    <div class="card-title" style="margin-bottom: 12px;"><ion-icon name="calendar-outline" style="color: #059669; font-size: 18px;"></ion-icon> Form Pengajuan Cuti</div>
                    <form onsubmit="submitLeave(event)">
                        <div class="form-group">
                            <label class="form-label">Jenis Pengajuan</label>
                            <select class="form-control" id="formLeaveType">
                                <option value="Cuti Tahunan">Cuti Tahunan</option>
                                <option value="Sakit">Sakit (Surat Dokter)</option>
                                <option value="Izin">Izin / Keperluan Pribadi</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Pilih Cepat Durasi</label>
                            <div class="quick-presets-row">
                                <span class="preset-chip" onclick="setLeavePreset(0, 0)">Hari Ini (1 hr)</span>
                                <span class="preset-chip" onclick="setLeavePreset(1, 1)">Besok (1 hr)</span>
                                <span class="preset-chip" onclick="setLeavePreset(1, 2)">Besok (2 hr)</span>
                                <span class="preset-chip" onclick="setLeavePreset(1, 3)">3 Hari</span>
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                            <div class="form-group">
                                <label class="form-label">Tanggal Mulai</label>
                                <input type="date" class="form-control" id="formLeaveStart" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Tanggal Selesai</label>
                                <input type="date" class="form-control" id="formLeaveEnd" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Alasan Pengajuan</label>
                            <textarea class="form-control" id="formLeaveReason" rows="3" placeholder="Sakit, acara keluarga, dll..." required></textarea>
                        </div>

                        <button type="submit" class="btn-clock-in">
                            <ion-icon name="paper-plane-outline"></ion-icon> Kirim Pengajuan Cuti
                        </button>
                    </form>
                </div>

                <div class="card">
                    <div class="card-title" style="margin-bottom: 12px;"><ion-icon name="list-outline" style="color: #059669; font-size: 18px;"></ion-icon> Status Pengajuan Cuti</div>
                    <div id="empLeavesList">
                        <div style="font-size: 12px; color: var(--text-muted); text-align: center; padding: 15px;">Memuat data cuti...</div>
                    </div>
                </div>
            </div>

            <!-- ============================================== -->
            <!-- EMPLOYEE TAB 3: LEMBUR                         -->
            <!-- ============================================== -->
            <div id="tab-emp-lembur" class="tab-pane" style="display: none;">
                <div class="card">
                    <div class="card-title" style="margin-bottom: 12px;"><ion-icon name="time-outline" style="color: #059669; font-size: 18px;"></ion-icon> Form Pengajuan Lembur</div>
                    <form onsubmit="submitOvertime(event)">
                        <div class="form-group">
                            <label class="form-label">Tanggal Lembur</label>
                            <input type="date" class="form-control" id="formOtDate" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Pilih Cepat Jam Lembur</label>
                            <div class="quick-presets-row">
                                <span class="preset-chip" onclick="setOtPreset('17:00', '20:00')">17:00 - 20:00 (3 Jam)</span>
                                <span class="preset-chip" onclick="setOtPreset('18:00', '21:00')">18:00 - 21:00 (3 Jam)</span>
                                <span class="preset-chip" onclick="setOtPreset('19:00', '22:00')">19:00 - 22:00 (3 Jam)</span>
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                            <div class="form-group">
                                <label class="form-label">Jam Mulai</label>
                                <input type="time" class="form-control" id="formOtStart" value="17:00" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Jam Selesai</label>
                                <input type="time" class="form-control" id="formOtEnd" value="20:00" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Keterangan Pekerjaan Lembur</label>
                            <textarea class="form-control" id="formOtDesc" rows="3" placeholder="Contoh: Editing batch wedding event, shooting studio malam..." required></textarea>
                        </div>

                        <button type="submit" class="btn-clock-in">
                            <ion-icon name="send-outline"></ion-icon> Kirim Pengajuan Lembur
                        </button>
                    </form>
                </div>

                <div class="card">
                    <div class="card-title" style="margin-bottom: 12px;"><ion-icon name="time-outline" style="color: #059669; font-size: 18px;"></ion-icon> Riwayat Lembur</div>
                    <div id="empOvertimesList">
                        <div style="font-size: 12px; color: var(--text-muted); text-align: center; padding: 15px;">Memuat data lembur...</div>
                    </div>
                </div>
            </div>

            <!-- ============================================== -->
            <!-- EMPLOYEE TAB 4: SLIP GAJI                      -->
            <!-- ============================================== -->
            <div id="tab-emp-slip" class="tab-pane" style="display: none;">
                <div class="card" style="background: linear-gradient(135deg, #064E3B 0%, #047857 100%); color: #FFFFFF;">
                    <div style="font-size: 11px; text-transform: uppercase; opacity: 0.9;">Total Gaji Bersih (Take Home Pay)</div>
                    <div style="font-size: 28px; font-weight: 800; margin-top: 4px;" id="slipNetSalaryText">Rp 0</div>
                    <div style="font-size: 11px; opacity: 0.85; margin-top: 4px;" id="slipPeriodLabel">Periode: -</div>
                </div>

                <div class="card">
                    <div class="card-header-flex">
                        <div class="card-title"><ion-icon name="document-text-outline" style="color: #059669; font-size: 18px;"></ion-icon> Unduh Dokumen Slip Gaji</div>
                    </div>
                    <p style="font-size: 12px; color: var(--text-muted); margin-bottom: 14px;">
                        Pilih periode bulan untuk mengunduh atau mencetak file dokumen PDF keterangan slip gaji resmi Anda.
                    </p>

                    <div class="form-group">
                        <label class="form-label">Pilih Bulan & Tahun</label>
                        <input type="month" class="form-control" id="slipMonthPicker" onchange="loadEmployeeSlipDetails()">
                    </div>

                    <div id="slipComponentsBox" style="margin-top: 14px; background: #F9FAFB; padding: 14px; border-radius: 14px; border: 1px solid var(--border);">
                        <!-- Rendered by JS -->
                    </div>

                    <div style="display: flex; gap: 10px; margin-top: 16px;">
                        <button class="btn-clock-in" onclick="printActivePayslip()" style="flex: 1; padding: 12px;">
                            <ion-icon name="print-outline"></ion-icon> Cetak Dokumen
                        </button>
                    </div>
                </div>
            </div>

            <!-- ============================================== -->
            <!-- EMPLOYEE TAB 5: PROFIL                         -->
            <!-- ============================================== -->
            <div id="tab-emp-profil" class="tab-pane" style="display: none;">
                <div class="card" style="text-align: center; padding: 22px 16px;">
                    <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=150" alt="Avatar" id="profileAvatarImg" style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover; border: 3px solid #10B981; margin: 0 auto 12px;">
                    <h3 style="font-size: 17px; font-weight: 800;" id="profileNameText">Nama Karyawan</h3>
                    <div style="font-size: 12px; color: var(--text-muted); margin-top: 2px;" id="profileCodeDivText">KRY-001 • Fotografer</div>
                    <div style="margin-top: 8px;"><span class="badge badge-success" id="profileRoleBadge">KARYAWAN</span></div>
                </div>

                <div class="card">
                    <div class="card-title" style="margin-bottom: 12px;"><ion-icon name="person-outline" style="color: #059669; font-size: 18px;"></ion-icon> Biodata & Kontak</div>
                    <form onsubmit="saveProfileBiodata(event)">
                        <div class="form-group">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control" id="profNameInput" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Nomor WhatsApp / HP Aktif</label>
                            <input type="tel" class="form-control" id="profPhoneInput" placeholder="0812xxxxxxxx">
                        </div>
                        <button type="submit" class="btn-clock-in" style="padding: 12px;">
                            <ion-icon name="save-outline"></ion-icon> Simpan Biodata
                        </button>
                    </form>
                </div>

                <button onclick="handleLogout()" style="width: 100%; background: #FEE2E2; color: #DC2626; border: 1px solid #FECACA; padding: 14px; border-radius: 16px; font-weight: 800; font-size: 14px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px;">
                    <ion-icon name="log-out-outline" style="font-size: 20px;"></ion-icon> Keluar dari Aplikasi
                </button>
            </div>

            <!-- ============================================== -->
            <!-- ADMIN TAB 1: DASHBOARD                         -->
            <!-- ============================================== -->
            <div id="tab-adm-dash" class="tab-pane" style="display: none;">
                <div class="card-header-flex" style="margin-bottom: 10px;">
                    <h3 style="font-size: 16px; font-weight: 800; color: #064E3B;">Panel Admin & HR Studio</h3>
                    <span class="badge badge-info" id="adminDateHeader">Hari Ini</span>
                </div>

                <!-- 4 Metrics -->
                <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 8px; margin-bottom: 14px;">
                    <div style="background: #F0FDF4; border: 1px solid #BBF7D0; border-radius: 14px; padding: 10px 4px; text-align: center;">
                        <div style="font-size: 18px; font-weight: 800; color: #16A34A;" id="adStatPresent">0</div>
                        <div style="font-size: 10px; font-weight: 700; color: #15803D; margin-top: 2px;">Sudah Hadir</div>
                    </div>
                    <div style="background: #FFFBEB; border: 1px solid #FEF3C7; border-radius: 14px; padding: 10px 4px; text-align: center;">
                        <div style="font-size: 18px; font-weight: 800; color: #D97706;" id="adStatLate">0</div>
                        <div style="font-size: 10px; font-weight: 700; color: #B45309; margin-top: 2px;">Terlambat</div>
                    </div>
                    <div style="background: #EFF6FF; border: 1px solid #DBEAFE; border-radius: 14px; padding: 10px 4px; text-align: center;">
                        <div style="font-size: 18px; font-weight: 800; color: #2563EB;" id="adStatLeave">0</div>
                        <div style="font-size: 10px; font-weight: 700; color: #1D4ED8; margin-top: 2px;">Izin/Cuti</div>
                    </div>
                    <div style="background: #FEF2F2; border: 1px solid #FEE2E2; border-radius: 14px; padding: 10px 4px; text-align: center;">
                        <div style="font-size: 18px; font-weight: 800; color: #DC2626;" id="adStatAbsent">0</div>
                        <div style="font-size: 10px; font-weight: 700; color: #B91C1C; margin-top: 2px;">Belum Absen</div>
                    </div>
                </div>

                <!-- 2 Pending Cards (Clickable) -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 14px;">
                    <div onclick="switchAdminTab('approval'); setApprovalSub('leave');" style="background: #FEF3C7; border: 1px solid #FDE68A; border-radius: 14px; padding: 12px; cursor: pointer;">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="font-size: 11px; font-weight: 700; color: #92400E;">Pending Cuti</span>
                            <ion-icon name="chevron-forward" style="color: #92400E;"></ion-icon>
                        </div>
                        <div style="font-size: 20px; font-weight: 800; color: #B45309; margin-top: 4px;" id="adPendingLeavesCount">0</div>
                        <div style="font-size: 10px; color: #B45309;">Ketuk untuk setujui</div>
                    </div>

                    <div onclick="switchAdminTab('approval'); setApprovalSub('overtime');" style="background: #E0F2FE; border: 1px solid #BAE6FD; border-radius: 14px; padding: 12px; cursor: pointer;">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="font-size: 11px; font-weight: 700; color: #075985;">Pending Lembur</span>
                            <ion-icon name="chevron-forward" style="color: #075985;"></ion-icon>
                        </div>
                        <div style="font-size: 20px; font-weight: 800; color: #0284C7; margin-top: 4px;" id="adPendingOvertimesCount">0</div>
                        <div style="font-size: 10px; color: #0284C7;">Ketuk untuk setujui</div>
                    </div>
                </div>

                <!-- Aktivitas Kehadiran Hari Ini -->
                <div class="card">
                    <div class="card-header-flex">
                        <div class="card-title"><ion-icon name="camera-outline" style="color: #059669; font-size: 18px;"></ion-icon> Aktivitas Kehadiran Hari Ini</div>
                    </div>
                    <div id="adLiveAttendancesList">
                        <div style="font-size: 12px; color: var(--text-muted); text-align: center; padding: 15px;">Memuat presensi tim...</div>
                    </div>
                </div>
            </div>

            <!-- ============================================== -->
            <!-- ADMIN TAB 2: APPROVAL                          -->
            <!-- ============================================== -->
            <div id="tab-adm-approval" class="tab-pane" style="display: none;">
                <div class="card-title" style="margin-bottom: 12px;"><ion-icon name="checkmark-done-circle-outline" style="color: #059669; font-size: 20px;"></ion-icon> Persetujuan Cuti & Lembur</div>
                
                <div class="segmented-row">
                    <button class="segment-tab-btn active" id="btnAppSubLeave" onclick="setApprovalSub('leave')">Pengajuan Cuti</button>
                    <button class="segment-tab-btn" id="btnAppSubOt" onclick="setApprovalSub('overtime')">Pengajuan Lembur</button>
                </div>

                <div id="adminApprovalItemsList">
                    <!-- Rendered by JS -->
                </div>
            </div>

            <!-- ============================================== -->
            <!-- ADMIN TAB 3: KARYAWAN & LOKASI STUDIO          -->
            <!-- ============================================== -->
            <div id="tab-adm-karyawan" class="tab-pane" style="display: none;">
                <div class="segmented-row">
                    <button class="segment-tab-btn active" id="btnEmpSubList" onclick="setEmployeeSub('list')">Data Karyawan</button>
                    <button class="segment-tab-btn" id="btnEmpSubStudio" onclick="setEmployeeSub('studio')">Lokasi Studio</button>
                </div>

                <!-- Subtab 1: Data Karyawan -->
                <div id="adminSubEmployeeList">
                    <div class="card-header-flex" style="margin-bottom: 10px;">
                        <span style="font-size: 13px; font-weight: 700; color: var(--text-muted);" id="adminEmpCountLabel">Karyawan</span>
                        <button onclick="openEmployeeModal()" class="badge badge-success" style="cursor: pointer; padding: 6px 12px; border: none; font-size: 11px;">
                            + Tambah Karyawan
                        </button>
                    </div>
                    <div id="adminEmployeesContainer">
                        <!-- Rendered by JS -->
                    </div>
                </div>

                <!-- Subtab 2: Lokasi Studio & Geofence -->
                <div id="adminSubStudioSettings" style="display: none;">
                    <div class="card">
                        <div class="card-title" style="margin-bottom: 12px;"><ion-icon name="business-outline" style="color: #059669; font-size: 18px;"></ion-icon> Pengaturan Lokasi Studio & Shift</div>
                        <form onsubmit="saveStudioSettings(event)">
                            <div class="form-group">
                                <label class="form-label">Nama Studio</label>
                                <input type="text" class="form-control" id="studioNameInput" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Alamat Studio</label>
                                <input type="text" class="form-control" id="studioAddressInput">
                            </div>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                                <div class="form-group">
                                    <label class="form-label">Latitude Studio</label>
                                    <input type="number" step="any" class="form-control" id="studioLatInput" required>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Longitude Studio</label>
                                    <input type="number" step="any" class="form-control" id="studioLngInput" required>
                                </div>
                            </div>
                            <button type="button" onclick="setStudioCoordsCurrentGps()" style="width: 100%; margin-bottom: 12px; background: #ECFDF5; border: 1px dashed #10B981; color: #047857; padding: 8px; border-radius: 12px; font-size: 11px; font-weight: 700; cursor: pointer;">
                                <ion-icon name="locate-outline"></ion-icon> Gunakan Koordinat GPS Saya Saat Ini
                            </button>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                                <div class="form-group">
                                    <label class="form-label">Radius Geofence (Meter)</label>
                                    <input type="number" class="form-control" id="studioRadiusInput" value="100" required>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Toleransi Keterlambatan (Menit)</label>
                                    <input type="number" class="form-control" id="studioLateTolInput" value="15" required>
                                </div>
                            </div>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                                <div class="form-group">
                                    <label class="form-label">Jam Masuk</label>
                                    <input type="time" class="form-control" id="studioStartTimeInput" value="08:00" required>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Jam Pulang</label>
                                    <input type="time" class="form-control" id="studioEndTimeInput" value="17:00" required>
                                </div>
                            </div>
                            <button type="submit" class="btn-clock-in" style="padding: 12px;">
                                <ion-icon name="save-outline"></ion-icon> Simpan Lokasi Studio
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- ============================================== -->
            <!-- ADMIN TAB 4: PAYROLL                           -->
            <!-- ============================================== -->
            <div id="tab-adm-payroll" class="tab-pane" style="display: none;">
                <div class="card-title" style="margin-bottom: 12px;"><ion-icon name="cash-outline" style="color: #059669; font-size: 20px;"></ion-icon> Manajemen Gaji & Penerbitan Slip PDF</div>
                
                <div class="card">
                    <div style="font-size: 12px; font-weight: 800; color: #064E3B; margin-bottom: 10px;">1. Pilih Periode & Karyawan</div>
                    <div class="form-group">
                        <label class="form-label">Periode Bulan</label>
                        <input type="month" class="form-control" id="admPayrollMonth" onchange="loadAdminPayrollForEmployee()">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Pilih Karyawan</label>
                        <select class="form-control" id="admPayrollEmpSelect" onchange="loadAdminPayrollForEmployee()">
                            <!-- Rendered by JS -->
                        </select>
                    </div>
                    <div id="admPayrollMetricsBadge" style="display: flex; gap: 8px; font-size: 11px; color: var(--text-muted); background: #F9FAFB; padding: 10px; border-radius: 12px;">
                        <span>Hadir: <b id="payMetPresent">0</b></span> |
                        <span>Terlambat: <b id="payMetLate">0</b></span> |
                        <span>Lembur: <b id="payMetOt">0 Jam</b></span>
                    </div>
                </div>

                <div class="card">
                    <div style="font-size: 12px; font-weight: 800; color: #064E3B; margin-bottom: 10px;">2. Komponen Slip Gaji (Rp)</div>
                    <form onsubmit="saveAdminPayroll(event)">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                            <div class="form-group">
                                <label class="form-label">Gaji Pokok</label>
                                <input type="number" class="form-control" id="payBaseInput" value="4500000" oninput="calculatePayrollTotal()">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Tunjangan</label>
                                <input type="number" class="form-control" id="payAllowanceInput" value="500000" oninput="calculatePayrollTotal()">
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                            <div class="form-group">
                                <label class="form-label">Uang Lembur</label>
                                <input type="number" class="form-control" id="payOtInput" value="0" oninput="calculatePayrollTotal()">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Bonus / Insentif</label>
                                <input type="number" class="form-control" id="payBonusInput" value="0" oninput="calculatePayrollTotal()">
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                            <div class="form-group">
                                <label class="form-label">Potongan Keterlambatan</label>
                                <input type="number" class="form-control" id="payLateDeductInput" value="0" oninput="calculatePayrollTotal()">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Potongan Lainnya</label>
                                <input type="number" class="form-control" id="payOtherDeductInput" value="0" oninput="calculatePayrollTotal()">
                            </div>
                        </div>

                        <div style="background: #ECFDF5; border: 1px solid #A7F3D0; border-radius: 14px; padding: 14px; margin-bottom: 14px; display: flex; justify-content: space-between; align-items: center;">
                            <span style="font-size: 12px; font-weight: 800; color: #064E3B;">Total Gaji Bersih:</span>
                            <span style="font-size: 20px; font-weight: 800; color: #047857;" id="payCalculatedNetText">Rp 5.000.000</span>
                        </div>

                        <div style="display: flex; gap: 8px;">
                            <button type="submit" class="btn-clock-in" style="flex: 1; padding: 12px;">
                                <ion-icon name="checkmark-done-circle-outline"></ion-icon> Terbitkan Slip Gaji
                            </button>
                        </div>
                    </form>

                    <button onclick="generateAllPayrollsMonthly()" style="width: 100%; margin-top: 10px; background: #FFFFFF; border: 1px solid #10B981; color: #059669; padding: 10px; border-radius: 14px; font-size: 12px; font-weight: 800; cursor: pointer;">
                        ⚡ Hitung Otomatis Seluruh Karyawan
                    </button>
                </div>
            </div>

            <!-- ============================================== -->
            <!-- ADMIN TAB 5: REKAP PDF                         -->
            <!-- ============================================== -->
            <div id="tab-adm-rekap" class="tab-pane" style="display: none;">
                <div class="card">
                    <div class="card-title" style="margin-bottom: 12px;"><ion-icon name="print-outline" style="color: #059669; font-size: 20px;"></ion-icon> Rekapitulasi Kehadiran & Cetak PDF</div>
                    
                    <div class="form-group">
                        <label class="form-label">Pilih Bulan Periode</label>
                        <input type="month" class="form-control" id="rekapMonthInput" value="">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Filter Divisi</label>
                        <select class="form-control" id="rekapDivSelect">
                            <option value="all">Semua Divisi</option>
                            <option value="Fotografer">Fotografer (PHOTO)</option>
                            <option value="Videografer">Videografer (VIDEO)</option>
                            <option value="Editor">Editor (EDIT)</option>
                            <option value="Produksi">Produksi (PROD)</option>
                            <option value="Marketing">Marketing (Bebas Radius)</option>
                            <option value="HR">HR & Admin</option>
                        </select>
                    </div>

                    <button onclick="openPrintRecap()" class="btn-clock-in" style="padding: 14px; margin-top: 10px;">
                        <ion-icon name="print-outline"></ion-icon> Cetak Lembar Dokumen Rekap PDF
                    </button>
                </div>
            </div>
        </div>

        <!-- ============================================== -->
        <!-- BOTTOM TAB NAVIGATION (Dynamic Role-Based)     -->
        <!-- ============================================== -->
        <nav class="bottom-nav" id="bottomNavContainer">
            <!-- Rendered by JS based on active role -->
        </nav>
    </div>

</div>

<!-- ============================================== -->
<!-- CAMERA SELFIE MODAL                            -->
<!-- ============================================== -->
<div class="modal-overlay" id="modalCamera">
    <div class="modal-sheet">
        <div class="modal-header">
            <div class="modal-title" id="camModalTitle">Clock In — Selfie & GPS</div>
            <button class="modal-close-btn" onclick="closeCameraModal()"><ion-icon name="close"></ion-icon></button>
        </div>

        <div class="camera-box">
            <video id="camera-feed" autoplay playsinline></video>
            <canvas id="camera-canvas" style="display: none;"></canvas>
            <div class="face-oval-guide">
                <div class="scan-beam"></div>
            </div>
        </div>

        <div style="text-align: center; font-size: 11px; color: var(--text-muted); margin-bottom: 12px;">
            Posisikan wajah Anda di tengah bingkai oval kamera
        </div>

        <button class="btn-clock-in" id="btnSnapPhoto" onclick="captureAndSubmitAttendance()">
            <ion-icon name="camera" style="font-size: 20px;"></ion-icon> Ambil Foto & Kirim Presensi
        </button>
    </div>
</div>

<!-- ============================================== -->
<!-- MODAL TAMBAH/EDIT KARYAWAN                     -->
<!-- ============================================== -->
<div class="modal-overlay" id="modalAdminEmployee">
    <div class="modal-sheet">
        <div class="modal-header">
            <div class="modal-title" id="modalEmpFormTitle">Tambah Karyawan Baru</div>
            <button class="modal-close-btn" onclick="closeModal('modalAdminEmployee')"><ion-icon name="close"></ion-icon></button>
        </div>
        <form onsubmit="saveAdminEmployee(event)">
            <input type="hidden" id="adminEmpId">
            <div class="form-group">
                <label class="form-label">Nama Lengkap</label>
                <input type="text" class="form-control" id="admEmpName" required>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                <div class="form-group">
                    <label class="form-label">Divisi</label>
                    <select class="form-control" id="admEmpDivision" required>
                        <option value="Fotografer">Fotografer (PHOTO)</option>
                        <option value="Videografer">Videografer (VIDEO)</option>
                        <option value="Editor">Editor (EDIT)</option>
                        <option value="Produksi">Produksi (PROD)</option>
                        <option value="Marketing">Marketing (Bebas Radius)</option>
                        <option value="HR">HR & Admin</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Jabatan / Posisi</label>
                    <input type="text" class="form-control" id="admEmpPosition" placeholder="Contoh: Senior Photographer" required>
                </div>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                <div class="form-group">
                    <label class="form-label">Role Akses</label>
                    <select class="form-control" id="admEmpRole" required>
                        <option value="karyawan">Karyawan</option>
                        <option value="supervisor">Supervisor</option>
                        <option value="admin">Admin</option>
                        <option value="owner">Owner</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Gaji Pokok (Rp)</label>
                    <input type="number" class="form-control" id="admEmpBaseSalary" value="4500000" required>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Nomor WhatsApp / HP</label>
                <input type="tel" class="form-control" id="admEmpPhone" placeholder="08xxxxxxxxxx">
            </div>
            <button type="submit" class="btn-clock-in" style="padding: 12px;">Simpan Karyawan</button>
        </form>
    </div>
</div>

<script>
    // State Store
    const state = {
        isLoggedIn: false,
        user: null,
        employee: null,
        employees: [],
        settings: {},
        todayAttendance: null,
        activeRoleView: 'employee', // 'employee' or 'admin'
        activeTab: 'absen',
        currentLat: null,
        currentLng: null,
        currentDistance: null,
        cameraStream: null,
        cameraClockType: 'in', // 'in' or 'out'
        deferredPrompt: null
    };

    // Service Worker for PWA
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('/sw.js').catch(err => console.log('SW register fail:', err));
        });
    }

    // PWA Install prompt listener
    window.addEventListener('beforeinstallprompt', (e) => {
        e.preventDefault();
        state.deferredPrompt = e;
        const banner = document.getElementById('floatingInstallBanner');
        if (banner) banner.style.display = 'flex';
    });

    function promptPwaInstall() {
        if (state.deferredPrompt) {
            state.deferredPrompt.prompt();
            state.deferredPrompt.userChoice.then((choice) => {
                if (choice.outcome === 'accepted') {
                    const banner = document.getElementById('floatingInstallBanner');
                    if (banner) banner.style.display = 'none';
                }
                state.deferredPrompt = null;
            });
        } else {
            alert('Panduan Instalasi PWA:\n\n1. Di Google Chrome / Edge: Klik ikon titik tiga di kanan atas > pilih "Instal Aplikasi" / "Tambahkan ke Layar Utama".\n2. Di iPhone (Safari): Ketuk tombol Share di bawah > pilih "Add to Home Screen".');
        }
    }

    // On Load
    document.addEventListener('DOMContentLoaded', () => {
        startLiveClock();
        loadInitialData();
        detectGpsLocation();

        const currentMonth = new Date().toISOString().slice(0, 7);
        if (document.getElementById('slipMonthPicker')) document.getElementById('slipMonthPicker').value = currentMonth;
        if (document.getElementById('admPayrollMonth')) document.getElementById('admPayrollMonth').value = currentMonth;
        if (document.getElementById('rekapMonthInput')) document.getElementById('rekapMonthInput').value = currentMonth;
        if (document.getElementById('formOtDate')) document.getElementById('formOtDate').value = new Date().toISOString().split('T')[0];
    });

    // 1. Live Digital Clock
    function startLiveClock() {
        const update = () => {
            const now = new Date();
            const timeStr = now.toLocaleTimeString('id-ID', { hour12: false });
            const dateStr = now.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
            if (document.getElementById('empLiveClock')) document.getElementById('empLiveClock').innerText = timeStr;
            if (document.getElementById('empLiveDate')) document.getElementById('empLiveDate').innerText = `${dateStr} · WIB`;
        };
        update();
        setInterval(update, 1000);
    }

    // 2. Fetch Initial API Data
    async function loadInitialData() {
        try {
            const res = await fetch('/api/hris/initial-data');
            const data = await res.json();
            if (data.success) {
                state.settings = data.settings || {};
                state.employee = data.employee;
                state.employees = data.employees || [];
                state.todayAttendance = data.today_attendance;

                // Check auth status
                if (data.is_logged_in && state.employee) {
                    state.isLoggedIn = true;
                    showScreen('main');
                    renderAppHeader();
                    renderEmployeeAttendanceScreen();
                    renderBottomNav();
                    loadHistory();
                    loadLeaves();
                    loadOvertimes();
                    loadEmployeeSlipDetails();

                    const isManager = ['owner', 'admin', 'hr', 'supervisor'].includes((state.employee.role || '').toLowerCase());
                    if (isManager) {
                        loadAdminStats();
                        loadAdminEmployeesList();
                    }
                } else {
                    state.isLoggedIn = false;
                    showScreen('login');
                }
            }
        } catch (e) {
            console.error('Failed to load initial HRIS data:', e);
        }
    }

    function showScreen(screenId) {
        document.getElementById('screen-login').style.display = screenId === 'login' ? 'block' : 'none';
        document.getElementById('screen-main').style.display = screenId === 'main' ? 'flex' : 'none';
    }

    // 3. Auth Actions
    function switchAuthTab(type) {
        const tabLogin = document.getElementById('tabAuthLogin');
        const tabReg = document.getElementById('tabAuthRegister');
        const nameGroup = document.getElementById('authRegisterNameGroup');
        const btnText = document.getElementById('btnLoginText');

        if (type === 'login') {
            tabLogin.classList.add('active');
            tabReg.classList.remove('active');
            nameGroup.style.display = 'none';
            btnText.innerText = 'Masuk Aplikasi';
        } else {
            tabReg.classList.add('active');
            tabLogin.classList.remove('active');
            nameGroup.style.display = 'block';
            btnText.innerText = 'Daftarkan & Masuk';
        }
    }

    function togglePasswordVisibility(inputId) {
        const el = document.getElementById(inputId);
        el.type = el.type === 'password' ? 'text' : 'password';
    }

    function fillDemoCredentials() {
        document.getElementById('loginEmail').value = 'warungkombas@gmail.com';
        document.getElementById('loginPassword').value = 'Fariz123@';
        switchAuthTab('login');
    }

    async function handleAuthLogin(e) {
        e.preventDefault();
        const isRegister = document.getElementById('tabAuthRegister').classList.contains('active');
        const endpoint = isRegister ? '/api/hris/auth/register' : '/api/hris/auth/login';

        const payload = {
            email: document.getElementById('loginEmail').value,
            password: document.getElementById('loginPassword').value,
            name: document.getElementById('loginName').value || 'User Studio'
        };

        const btn = document.getElementById('btnLoginSubmit');
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<ion-icon name="sync-outline" class="spin"></ion-icon> Memproses...';

        try {
            const res = await fetch(endpoint, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify(payload)
            });
            const json = await res.json();
            if (json.success) {
                state.isLoggedIn = true;
                state.employee = json.employee;
                loadInitialData();
            } else {
                alert(`❌ ${json.message}`);
            }
        } catch (err) {
            console.error(err);
            alert('Terjadi kesalahan jaringan.');
        } finally {
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    }

    async function handleLogout() {
        if (!confirm('Yakin ingin keluar dari aplikasi?')) return;
        await fetch('/api/hris/auth/logout', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } });
        state.isLoggedIn = false;
        showScreen('login');
    }

    // 4. Header & Navigation Rendering
    function renderAppHeader() {
        const emp = state.employee;
        if (!emp) return;

        document.getElementById('appHeaderName').innerText = emp.name;
        document.getElementById('appHeaderDivisionTag').innerText = `${emp.division} • ${emp.position}`;
        if (emp.avatar_url) document.getElementById('appHeaderAvatar').src = emp.avatar_url;

        // Profile tab fields
        if (document.getElementById('profileNameText')) document.getElementById('profileNameText').innerText = emp.name;
        if (document.getElementById('profileCodeDivText')) document.getElementById('profileCodeDivText').innerText = `${emp.employee_code} • ${emp.division}`;
        if (document.getElementById('profileRoleBadge')) document.getElementById('profileRoleBadge').innerText = (emp.role || 'karyawan').toUpperCase();
        if (document.getElementById('profNameInput')) document.getElementById('profNameInput').value = emp.name;
        if (document.getElementById('profPhoneInput')) document.getElementById('profPhoneInput').value = emp.phone || '';

        // Role switcher button
        const canManage = ['owner', 'admin', 'hr', 'supervisor'].includes((emp.role || '').toLowerCase());
        const switchBtn = document.getElementById('appRoleSwitchContainer');
        if (switchBtn) {
            switchBtn.style.display = canManage ? 'block' : 'none';
            document.getElementById('roleViewLabel').innerText = state.activeRoleView === 'admin' ? 'Mode Karyawan' : 'Mode Admin';
        }
    }

    function toggleAppRoleView() {
        state.activeRoleView = state.activeRoleView === 'admin' ? 'employee' : 'admin';
        document.getElementById('roleViewLabel').innerText = state.activeRoleView === 'admin' ? 'Mode Karyawan' : 'Mode Admin';
        renderBottomNav();

        if (state.activeRoleView === 'admin') {
            switchAdminTab('dash');
            loadAdminStats();
        } else {
            switchEmployeeTab('absen');
        }
    }

    function renderBottomNav() {
        const container = document.getElementById('bottomNavContainer');
        if (!container) return;

        if (state.activeRoleView === 'employee') {
            container.innerHTML = `
                <button class="nav-tab-item active" id="nav-btn-absen" onclick="switchEmployeeTab('absen')">
                    <ion-icon name="finger-print-outline"></ion-icon>
                    <span>Absensi</span>
                </button>
                <button class="nav-tab-item" id="nav-btn-cuti" onclick="switchEmployeeTab('cuti')">
                    <ion-icon name="calendar-outline"></ion-icon>
                    <span>Cuti</span>
                </button>
                <button class="nav-tab-item" id="nav-btn-lembur" onclick="switchEmployeeTab('lembur')">
                    <ion-icon name="time-outline"></ion-icon>
                    <span>Lembur</span>
                </button>
                <button class="nav-tab-item" id="nav-btn-slip" onclick="switchEmployeeTab('slip')">
                    <ion-icon name="document-text-outline"></ion-icon>
                    <span>Slip Gaji</span>
                </button>
                <button class="nav-tab-item" id="nav-btn-profil" onclick="switchEmployeeTab('profil')">
                    <ion-icon name="person-circle-outline"></ion-icon>
                    <span>Profil</span>
                </button>
            `;
        } else {
            container.innerHTML = `
                <button class="nav-tab-item active" id="nav-btn-ad-dash" onclick="switchAdminTab('dash')">
                    <ion-icon name="grid-outline"></ion-icon>
                    <span>Dashboard</span>
                </button>
                <button class="nav-tab-item" id="nav-btn-ad-approval" onclick="switchAdminTab('approval')">
                    <ion-icon name="checkmark-done-circle-outline"></ion-icon>
                    <span>Approval</span>
                </button>
                <button class="nav-tab-item" id="nav-btn-ad-karyawan" onclick="switchAdminTab('karyawan')">
                    <ion-icon name="people-outline"></ion-icon>
                    <span>Karyawan</span>
                </button>
                <button class="nav-tab-item" id="nav-btn-ad-payroll" onclick="switchAdminTab('payroll')">
                    <ion-icon name="cash-outline"></ion-icon>
                    <span>Payroll</span>
                </button>
                <button class="nav-tab-item" id="nav-btn-ad-rekap" onclick="switchAdminTab('rekap')">
                    <ion-icon name="print-outline"></ion-icon>
                    <span>Rekap PDF</span>
                </button>
            `;
        }
    }

    function switchEmployeeTab(tab) {
        state.activeTab = tab;
        document.querySelectorAll('.tab-pane').forEach(el => el.style.display = 'none');
        document.querySelectorAll('.nav-tab-item').forEach(el => el.classList.remove('active'));

        const target = document.getElementById(`tab-emp-${tab}`);
        const nav = document.getElementById(`nav-btn-${tab}`);
        if (target) target.style.display = 'block';
        if (nav) nav.classList.add('active');
    }

    function switchAdminTab(tab) {
        state.activeTab = tab;
        document.querySelectorAll('.tab-pane').forEach(el => el.style.display = 'none');
        document.querySelectorAll('.nav-tab-item').forEach(el => el.classList.remove('active'));

        const target = document.getElementById(`tab-adm-${tab}`);
        const nav = document.getElementById(`nav-btn-ad-${tab}`);
        if (target) target.style.display = 'block';
        if (nav) nav.classList.add('active');
    }

    // 5. GPS & Geofence
    function calculateDistance(lat1, lon1, lat2, lon2) {
        const R = 6371000;
        const dLat = (lat2 - lat1) * Math.PI / 180;
        const dLon = (lon2 - lon1) * Math.PI / 180;
        const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                  Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                  Math.sin(dLon/2) * Math.sin(dLon/2);
        return Math.round(R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a)) * 10) / 10;
    }

    function detectGpsLocation() {
        if (!navigator.geolocation) return;
        navigator.geolocation.getCurrentPosition(
            (pos) => {
                state.currentLat = pos.coords.latitude;
                state.currentLng = pos.coords.longitude;

                const studioLat = parseFloat(state.settings.studio_lat || -6.175392);
                const studioLng = parseFloat(state.settings.studio_lng || 106.827153);
                const maxRadius = parseInt(state.settings.radius_meters || 100);

                const dist = calculateDistance(state.currentLat, state.currentLng, studioLat, studioLng);
                state.currentDistance = dist;

                const isMarketing = (state.employee && state.employee.division || '').toLowerCase() === 'marketing';
                const distEl = document.getElementById('geofenceDistanceText');
                if (distEl) distEl.innerText = `${dist} m dari Studio`;

                const mktBadge = document.getElementById('marketingBypassBadge');
                if (mktBadge) mktBadge.style.display = isMarketing ? 'inline-flex' : 'none';
            },
            (err) => console.warn('GPS error:', err),
            { enableHighAccuracy: true, timeout: 8000 }
        );
    }

    // 6. Camera & Attendance Flow
    async function openCameraModal(type) {
        state.cameraClockType = type;
        document.getElementById('camModalTitle').innerText = type === 'in' ? 'Clock In — Selfie & GPS' : 'Clock Out — Selfie & GPS';
        document.getElementById('modalCamera').classList.add('active');

        try {
            const constraints = { video: { facingMode: 'user', width: { ideal: 640 }, height: { ideal: 480 } }, audio: false };
            state.cameraStream = await navigator.mediaDevices.getUserMedia(constraints);
            const feed = document.getElementById('camera-feed');
            if (feed) feed.srcObject = state.cameraStream;
        } catch (e) {
            alert('Akses kamera depan wajib diizinkan untuk mengambil foto selfie presensi.');
            closeCameraModal();
        }
    }

    function closeCameraModal() {
        if (state.cameraStream) {
            state.cameraStream.getTracks().forEach(t => t.stop());
            state.cameraStream = null;
        }
        document.getElementById('modalCamera').classList.remove('active');
    }

    function handleClockOutClick() {
        // Validation matching expo app: Clock in first!
        if (!state.todayAttendance || !state.todayAttendance.clock_in) {
            alert('WAJIB CLOCK IN DULU:\n\nAnda belum melakukan Clock In hari ini. Silakan lakukan Clock In terlebih dahulu sebelum Clock Out.');
            return;
        }

        // Check if before 17:00
        const now = new Date();
        const hour = now.getHours();
        if (hour < 17) {
            if (confirm('Belum Waktunya Pulang!\n\nShift Kerja Studio berakhir pada pk 17:00 WIB.\nApakah Anda yakin ingin Izin Pulang Cepat / Simulasi Clock Out?')) {
                openCameraModal('out');
            }
        } else {
            openCameraModal('out');
        }
    }

    async function captureAndSubmitAttendance() {
        const video = document.getElementById('camera-feed');
        const canvas = document.getElementById('camera-canvas');
        if (!video || !canvas) return;

        canvas.width = video.videoWidth || 640;
        canvas.height = video.videoHeight || 480;
        const ctx = canvas.getContext('2d');
        ctx.translate(canvas.width, 0);
        ctx.scale(-1, 1);
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
        const photoBase64 = canvas.toDataURL('image/jpeg', 0.85);

        const type = state.cameraClockType;
        const endpoint = type === 'in' ? '/api/hris/attendance/clock-in' : '/api/hris/attendance/clock-out';
        const btn = document.getElementById('btnSnapPhoto');
        btn.disabled = true;
        btn.innerHTML = '<ion-icon name="sync-outline" class="spin"></ion-icon> Mengirim ke Database...';

        try {
            const res = await fetch(endpoint, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({
                    employee_id: state.employee.id,
                    photo: photoBase64,
                    lat: state.currentLat || -6.175392,
                    lng: state.currentLng || 106.827153
                })
            });
            const json = await res.json();
            if (json.success) {
                alert(`✅ ${json.message}`);
                closeCameraModal();
                loadInitialData();
            } else {
                alert(`❌ ${json.message}`);
            }
        } catch (e) {
            console.error(e);
            alert('Gagal mengirim data presensi.');
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<ion-icon name="camera"></ion-icon> Ambil Foto & Kirim Presensi';
        }
    }

    function renderEmployeeAttendanceScreen() {
        const att = state.todayAttendance;
        const badge = document.getElementById('empTodayBadge');
        const inTime = document.getElementById('empTodayInTime');
        const inDist = document.getElementById('empTodayInDist');
        const outTime = document.getElementById('empTodayOutTime');
        const outDur = document.getElementById('empTodayWorkDuration');

        if (!att || !att.clock_in) {
            badge.className = 'badge badge-warning';
            badge.innerText = 'Belum Clock In';
            inTime.innerText = '--:--';
            inDist.innerText = '-- m dari Studio';
            outTime.innerText = 'Belum Pulang';
            outDur.innerText = '-';
            return;
        }

        const inDate = new Date(att.clock_in);
        inTime.innerText = inDate.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) + ' WIB';
        inDist.innerText = `${att.distance_meters || 0} m dari Studio`;

        if (att.status === 'terlambat') {
            badge.className = 'badge badge-danger';
            badge.innerText = `Terlambat (${att.late_minutes}m)`;
        } else {
            badge.className = 'badge badge-success';
            badge.innerText = 'Hadir Tepat Waktu';
        }

        if (att.clock_out) {
            const outDate = new Date(att.clock_out);
            outTime.innerText = outDate.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) + ' WIB';
            const diffHours = ((outDate - inDate) / (1000 * 60 * 60)).toFixed(1);
            outDur.innerText = `Durasi: ${diffHours} Jam`;
        } else {
            outTime.innerText = 'Aktif Bekerja';
            outDur.innerText = 'Sedang Berlangsung';
        }
    }

    // 7. Leaves (Cuti)
    function setLeavePreset(startDays, endDays) {
        const today = new Date();
        const start = new Date(today);
        start.setDate(today.getDate() + startDays);
        const end = new Date(today);
        end.setDate(today.getDate() + endDays);

        document.getElementById('formLeaveStart').value = start.toISOString().split('T')[0];
        document.getElementById('formLeaveEnd').value = end.toISOString().split('T')[0];
    }

    async function submitLeave(e) {
        e.preventDefault();
        const payload = {
            employee_id: state.employee.id,
            type: document.getElementById('formLeaveType').value,
            start_date: document.getElementById('formLeaveStart').value,
            end_date: document.getElementById('formLeaveEnd').value,
            reason: document.getElementById('formLeaveReason').value
        };

        const res = await fetch('/api/hris/leaves', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify(payload)
        });
        const json = await res.json();
        if (json.success) {
            alert(`✅ ${json.message}`);
            document.getElementById('formLeaveReason').value = '';
            loadLeaves();
        } else {
            alert(`❌ ${json.message}`);
        }
    }

    async function loadLeaves() {
        const list = document.getElementById('empLeavesList');
        if (!list) return;
        const res = await fetch('/api/hris/leaves');
        const json = await res.json();
        if (json.success && json.leaves.length > 0) {
            list.innerHTML = json.leaves.map(l => `
                <div class="list-item-row">
                    <div>
                        <div style="font-size: 13px; font-weight: 800; color: #111827;">${l.type} (${l.total_days} hari)</div>
                        <div style="font-size: 11px; color: var(--text-muted);">${l.start_date} s/d ${l.end_date}</div>
                        <div style="font-size: 11px; color: #4B5563; margin-top: 2px;"><i>"${l.reason}"</i></div>
                    </div>
                    <span class="badge ${l.status === 'approved' ? 'badge-success' : (l.status === 'rejected' ? 'badge-danger' : 'badge-warning')}">
                        ${l.status.toUpperCase()}
                    </span>
                </div>
            `).join('');
        } else {
            list.innerHTML = '<div style="font-size: 12px; color: var(--text-muted); text-align: center; padding: 15px;">Belum ada riwayat pengajuan cuti di database.</div>';
        }
    }

    // 8. Overtime (Lembur)
    function setOtPreset(start, end) {
        document.getElementById('formOtStart').value = start;
        document.getElementById('formOtEnd').value = end;
    }

    async function submitOvertime(e) {
        e.preventDefault();
        const payload = {
            employee_id: state.employee.id,
            date: document.getElementById('formOtDate').value,
            start_time: document.getElementById('formOtStart').value,
            end_time: document.getElementById('formOtEnd').value,
            description: document.getElementById('formOtDesc').value
        };

        const res = await fetch('/api/hris/overtimes', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify(payload)
        });
        const json = await res.json();
        if (json.success) {
            alert(`✅ ${json.message}`);
            document.getElementById('formOtDesc').value = '';
            loadOvertimes();
        } else {
            alert(`❌ ${json.message}`);
        }
    }

    async function loadOvertimes() {
        const list = document.getElementById('empOvertimesList');
        if (!list) return;
        const res = await fetch('/api/hris/overtimes');
        const json = await res.json();
        if (json.success && json.overtimes.length > 0) {
            list.innerHTML = json.overtimes.map(ot => `
                <div class="list-item-row">
                    <div>
                        <div style="font-size: 13px; font-weight: 800; color: #111827;">${ot.project_name || 'Lembur'}</div>
                        <div style="font-size: 11px; color: var(--text-muted);">${ot.date} • ${ot.start_time} - ${ot.end_time} (${ot.duration_hours} Jam)</div>
                        <div style="font-size: 11px; color: #059669; font-weight: 700; margin-top: 2px;">Est: Rp ${(ot.estimated_pay||0).toLocaleString('id-ID')}</div>
                    </div>
                    <span class="badge ${ot.status === 'approved' ? 'badge-success' : (ot.status === 'rejected' ? 'badge-danger' : 'badge-warning')}">
                        ${ot.status.toUpperCase()}
                    </span>
                </div>
            `).join('');
        } else {
            list.innerHTML = '<div style="font-size: 12px; color: var(--text-muted); text-align: center; padding: 15px;">Belum ada pengajuan lembur di database.</div>';
        }
    }

    // 9. Employee Payslip
    async function loadEmployeeSlipDetails() {
        const month = document.getElementById('slipMonthPicker').value;
        const box = document.getElementById('slipComponentsBox');
        if (!box) return;

        const res = await fetch('/api/hris/payrolls');
        const json = await res.json();
        const payrolls = json.payrolls || [];
        const active = payrolls.find(p => p.period_month === month) || payrolls[0];

        if (active) {
            document.getElementById('slipNetSalaryText').innerText = 'Rp ' + (active.net_salary || 0).toLocaleString('id-ID');
            document.getElementById('slipPeriodLabel').innerText = `Periode: ${active.period_month}`;
            state.activePayrollId = active.id;

            box.innerHTML = `
                <div style="display: flex; justify-content: space-between; font-size: 13px; margin-bottom: 6px;">
                    <span style="color: var(--text-muted);">Gaji Pokok:</span>
                    <b style="color: #111827;">Rp ${(active.base_salary || 0).toLocaleString('id-ID')}</b>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 13px; margin-bottom: 6px;">
                    <span style="color: var(--text-muted);">Tunjangan:</span>
                    <b style="color: #111827;">Rp ${(active.allowance || 0).toLocaleString('id-ID')}</b>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 13px; margin-bottom: 6px;">
                    <span style="color: var(--text-muted);">Uang Lembur (${active.total_overtime_hours || 0} Jam):</span>
                    <b style="color: #059669;">+ Rp ${(active.overtime_pay || 0).toLocaleString('id-ID')}</b>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 13px; margin-bottom: 6px;">
                    <span style="color: var(--text-muted);">Potongan Terlambat:</span>
                    <b style="color: #DC2626;">- Rp ${(active.late_deductions || 0).toLocaleString('id-ID')}</b>
                </div>
                <div style="border-top: 1px dashed var(--border); margin: 8px 0;"></div>
                <div style="display: flex; justify-content: space-between; font-size: 14px; font-weight: 800;">
                    <span style="color: #064E3B;">Total Gaji Bersih:</span>
                    <span style="color: #059669;">Rp ${(active.net_salary || 0).toLocaleString('id-ID')}</span>
                </div>
            `;
        } else {
            const emp = state.employee || {};
            const est = (emp.base_salary || 4500000) + (emp.allowance || 500000);
            document.getElementById('slipNetSalaryText').innerText = 'Rp ' + est.toLocaleString('id-ID');
            document.getElementById('slipPeriodLabel').innerText = `Periode: ${month} (Estimasi Master)`;
            box.innerHTML = `
                <div style="display: flex; justify-content: space-between; font-size: 13px; margin-bottom: 6px;">
                    <span style="color: var(--text-muted);">Gaji Pokok:</span>
                    <b>Rp ${(emp.base_salary || 4500000).toLocaleString('id-ID')}</b>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 13px; margin-bottom: 6px;">
                    <span style="color: var(--text-muted);">Tunjangan:</span>
                    <b>Rp ${(emp.allowance || 500000).toLocaleString('id-ID')}</b>
                </div>
                <div style="font-size: 11px; color: var(--text-muted); margin-top: 10px; font-style: italic;">
                    *Slip resmi bulan ini belum diterbitkan oleh Admin. Hubungi HR untuk penerbitan slip gaji.
                </div>
            `;
        }
    }

    function printActivePayslip() {
        if (state.activePayrollId) {
            window.open(`/hris/payslip/${state.activePayrollId}/print`, '_blank');
        } else {
            alert('Slip gaji resmi bulan ini belum diterbitkan Admin. Silakan pilih bulan yang sudah terbit atau hubungi Admin Studio.');
        }
    }

    // 10. Attendance History
    async function loadHistory() {
        const list = document.getElementById('empAttendanceHistoryList');
        if (!list) return;
        const res = await fetch('/api/hris/attendance/history');
        const json = await res.json();
        if (json.success && json.attendances.length > 0) {
            list.innerHTML = json.attendances.map(a => `
                <div class="list-item-row">
                    <div>
                        <div style="font-size: 13px; font-weight: 800; color: #111827;">${a.date}</div>
                        <div style="font-size: 11px; color: var(--text-muted);">
                            Masuk: <b>${a.clock_in ? new Date(a.clock_in).toLocaleTimeString('id-ID', {hour:'2-digit', minute:'2-digit'}) : '-'}</b>
                            · Pulang: <b>${a.clock_out ? new Date(a.clock_out).toLocaleTimeString('id-ID', {hour:'2-digit', minute:'2-digit'}) : '-'}</b>
                        </div>
                        <div style="font-size: 10px; color: #6B7280; margin-top: 2px;">Jarak: ${a.distance_meters || 0} m</div>
                    </div>
                    <span class="badge ${a.status === 'hadir' ? 'badge-success' : 'badge-danger'}">
                        ${a.status.toUpperCase()}
                    </span>
                </div>
            `).join('');
        } else {
            list.innerHTML = '<div style="font-size: 12px; color: var(--text-muted); text-align: center; padding: 15px;">Belum ada riwayat absensi tercatat di database.</div>';
        }
    }

    // 11. Profile Update
    async function saveProfileBiodata(e) {
        e.preventDefault();
        const payload = {
            name: document.getElementById('profNameInput').value,
            phone: document.getElementById('profPhoneInput').value
        };

        const res = await fetch('/api/hris/profile/update', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify(payload)
        });
        const json = await res.json();
        if (json.success) {
            alert('✅ Biodata kontak berhasil disimpan ke database!');
            loadInitialData();
        }
    }

    // 12. ADMIN FEATURES
    async function loadAdminStats() {
        try {
            const res = await fetch('/api/hris/admin/stats');
            const json = await res.json();
            if (json.success) {
                const s = json.stats;
                document.getElementById('adStatPresent').innerText = s.present_today || 0;
                document.getElementById('adStatLate').innerText = s.late_today || 0;
                document.getElementById('adStatLeave').innerText = s.leave_today || 0;
                document.getElementById('adStatAbsent').innerText = s.absent_today || 0;
                document.getElementById('adPendingLeavesCount').innerText = s.pending_leaves || 0;
                document.getElementById('adPendingOvertimesCount').innerText = s.pending_overtimes || 0;

                renderAdminLiveAttendances(json.today_attendances || []);
                loadAdminApprovalList('leave');
            }
        } catch (e) {
            console.error(e);
        }
    }

    function renderAdminLiveAttendances(items) {
        const list = document.getElementById('adLiveAttendancesList');
        if (!list) return;

        if (items.length === 0) {
            list.innerHTML = '<div style="font-size: 12px; color: var(--text-muted); text-align: center; padding: 15px;">Belum ada karyawan yang melakukan Clock In hari ini.</div>';
            return;
        }

        list.innerHTML = items.map(a => `
            <div class="list-item-row">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <img src="${a.clock_in_photo || (a.employee && a.employee.avatar_url) || 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=150'}" style="width: 38px; height: 38px; border-radius: 10px; object-fit: cover;">
                    <div>
                        <div style="font-size: 13px; font-weight: 800; color: #111827;">${a.employee ? a.employee.name : 'Karyawan'}</div>
                        <div style="font-size: 11px; color: var(--text-muted);">
                            ${a.employee ? a.employee.division : 'Studio'} • Masuk: <b>${a.clock_in ? new Date(a.clock_in).toLocaleTimeString('id-ID', {hour:'2-digit', minute:'2-digit'}) : '-'}</b>
                        </div>
                    </div>
                </div>
                <span class="badge ${a.status === 'hadir' ? 'badge-success' : 'badge-danger'}">${a.status.toUpperCase()}</span>
            </div>
        `).join('');
    }

    function setApprovalSub(type) {
        document.getElementById('btnAppSubLeave').classList.toggle('active', type === 'leave');
        document.getElementById('btnAppSubOt').classList.toggle('active', type === 'overtime');
        loadAdminApprovalList(type);
    }

    async function loadAdminApprovalList(type) {
        const list = document.getElementById('adminApprovalItemsList');
        if (!list) return;

        const endpoint = type === 'leave' ? '/api/hris/leaves?all=1' : '/api/hris/overtimes?all=1';
        const res = await fetch(endpoint);
        const json = await res.json();
        const items = type === 'leave' ? (json.leaves || []) : (json.overtimes || []);
        const pending = items.filter(i => i.status === 'pending');

        if (pending.length === 0) {
            list.innerHTML = `<div style="font-size: 12px; color: var(--text-muted); text-align: center; padding: 20px;">Belum ada pengajuan ${type === 'leave' ? 'cuti' : 'lembur'} pending dari karyawan.</div>`;
            return;
        }

        list.innerHTML = pending.map(item => `
            <div class="card" style="margin-bottom: 10px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                    <b style="font-size: 13px; color: #064E3B;">${item.employee ? item.employee.name : 'Karyawan'}</b>
                    <span class="badge badge-warning">PENDING</span>
                </div>
                <div style="font-size: 12px; color: #374151;">
                    ${type === 'leave' ? `<b>${item.type}</b> (${item.total_days} hari) · ${item.start_date}` : `<b>${item.project_name || 'Lembur'}</b> (${item.duration_hours} Jam) · ${item.date}`}
                </div>
                <div style="font-size: 11px; color: #6B7280; margin-top: 4px;">Alasan: <i>"${item.reason || item.description || '-'}"</i></div>
                <div style="display: flex; gap: 8px; margin-top: 10px;">
                    <button onclick="processApproval('${type}', ${item.id}, 'approved')" style="flex: 1; background: #059669; color: #FFFFFF; border: none; padding: 8px; border-radius: 10px; font-weight: 700; font-size: 12px; cursor: pointer;">
                        ✓ Setujui
                    </button>
                    <button onclick="processApproval('${type}', ${item.id}, 'rejected')" style="flex: 1; background: #DC2626; color: #FFFFFF; border: none; padding: 8px; border-radius: 10px; font-weight: 700; font-size: 12px; cursor: pointer;">
                        ✕ Tolak
                    </button>
                </div>
            </div>
        `).join('');
    }

    async function processApproval(type, id, action) {
        const endpoint = type === 'leave' ? `/api/hris/admin/leaves/${id}/action` : `/api/hris/admin/overtimes/${id}/action`;
        const res = await fetch(endpoint, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ action: action })
        });
        const json = await res.json();
        if (json.success) {
            alert(`✅ ${json.message}`);
            loadAdminStats();
        }
    }

    // 13. Karyawan & Lokasi Studio Subtab
    function setEmployeeSub(type) {
        document.getElementById('btnEmpSubList').classList.toggle('active', type === 'list');
        document.getElementById('btnEmpSubStudio').classList.toggle('active', type === 'studio');
        document.getElementById('adminSubEmployeeList').style.display = type === 'list' ? 'block' : 'none';
        document.getElementById('adminSubStudioSettings').style.display = type === 'studio' ? 'block' : 'none';
    }

    function loadAdminEmployeesList() {
        const container = document.getElementById('adminEmployeesContainer');
        const select = document.getElementById('admPayrollEmpSelect');
        if (!container) return;

        const employees = state.employees || [];
        document.getElementById('adminEmpCountLabel').innerText = `Data Karyawan (${employees.length} Orang)`;

        if (select) {
            select.innerHTML = employees.map(e => `<option value="${e.id}">${e.name} (${e.division} • ${e.employee_code})</option>`).join('');
        }

        container.innerHTML = employees.map(emp => `
            <div class="card" style="margin-bottom: 8px; padding: 12px;">
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <img src="${emp.avatar_url || 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=150'}" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                        <div>
                            <div style="font-size: 13px; font-weight: 800; color: #111827;">${emp.name}</div>
                            <div style="font-size: 11px; color: var(--text-muted);">${emp.employee_code} · ${emp.division} (${emp.position})</div>
                            <div style="font-size: 11px; color: #059669; font-weight: 700; margin-top: 2px;">Gaji: Rp ${(emp.base_salary||0).toLocaleString('id-ID')}</div>
                        </div>
                    </div>
                    <div style="display: flex; gap: 4px;">
                        <button onclick='editAdminEmployee(${JSON.stringify(emp)})' style="background: #ECFDF5; color: #047857; border: none; width: 30px; height: 30px; border-radius: 8px; cursor: pointer;">
                            <ion-icon name="create-outline"></ion-icon>
                        </button>
                        <button onclick="deleteAdminEmployee(${emp.id})" style="background: #FEE2E2; color: #DC2626; border: none; width: 30px; height: 30px; border-radius: 8px; cursor: pointer;">
                            <ion-icon name="trash-outline"></ion-icon>
                        </button>
                    </div>
                </div>
            </div>
        `).join('');

        // Also populate studio settings form
        const s = state.settings || {};
        if (document.getElementById('studioNameInput')) document.getElementById('studioNameInput').value = s.studio_name || 'Studio Foto Utama';
        if (document.getElementById('studioAddressInput')) document.getElementById('studioAddressInput').value = s.studio_address || 'Jakarta Pusat';
        if (document.getElementById('studioLatInput')) document.getElementById('studioLatInput').value = s.studio_lat || -6.175392;
        if (document.getElementById('studioLngInput')) document.getElementById('studioLngInput').value = s.studio_lng || 106.827153;
        if (document.getElementById('studioRadiusInput')) document.getElementById('studioRadiusInput').value = s.radius_meters || 100;
        if (document.getElementById('studioLateTolInput')) document.getElementById('studioLateTolInput').value = s.late_tolerance_minutes || 15;
    }

    function openEmployeeModal() {
        document.getElementById('modalEmpFormTitle').innerText = 'Tambah Karyawan Baru';
        document.getElementById('adminEmpId').value = '';
        document.getElementById('admEmpName').value = '';
        document.getElementById('admEmpPosition').value = '';
        document.getElementById('admEmpBaseSalary').value = 4500000;
        document.getElementById('admEmpPhone').value = '';
        document.getElementById('modalAdminEmployee').classList.add('active');
    }

    function editAdminEmployee(emp) {
        document.getElementById('modalEmpFormTitle').innerText = 'Edit Data Karyawan';
        document.getElementById('adminEmpId').value = emp.id;
        document.getElementById('admEmpName').value = emp.name;
        document.getElementById('admEmpDivision').value = emp.division;
        document.getElementById('admEmpPosition').value = emp.position;
        document.getElementById('admEmpRole').value = emp.role;
        document.getElementById('admEmpBaseSalary').value = emp.base_salary;
        document.getElementById('admEmpPhone').value = emp.phone || '';
        document.getElementById('modalAdminEmployee').classList.add('active');
    }

    async function saveAdminEmployee(e) {
        e.preventDefault();
        const payload = {
            id: document.getElementById('adminEmpId').value || null,
            name: document.getElementById('admEmpName').value,
            division: document.getElementById('admEmpDivision').value,
            position: document.getElementById('admEmpPosition').value,
            role: document.getElementById('admEmpRole').value,
            base_salary: document.getElementById('admEmpBaseSalary').value,
            phone: document.getElementById('admEmpPhone').value
        };

        const res = await fetch('/api/hris/admin/employees', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify(payload)
        });
        const json = await res.json();
        if (json.success) {
            alert('✅ Data karyawan berhasil disimpan ke database!');
            closeModal('modalAdminEmployee');
            loadInitialData();
        }
    }

    async function deleteAdminEmployee(id) {
        if (!confirm('Hapus karyawan ini dari database?')) return;
        const res = await fetch(`/api/hris/admin/employees/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } });
        const json = await res.json();
        if (json.success) {
            loadInitialData();
        }
    }

    function setStudioCoordsCurrentGps() {
        if (state.currentLat && state.currentLng) {
            document.getElementById('studioLatInput').value = state.currentLat;
            document.getElementById('studioLngInput').value = state.currentLng;
            alert(`Koordinat GPS terisi: Lat: ${state.currentLat}, Lng: ${state.currentLng}`);
        } else {
            alert('Mendeteksi GPS...');
            detectGpsLocation();
        }
    }

    async function saveStudioSettings(e) {
        e.preventDefault();
        const payload = {
            studio_name: document.getElementById('studioNameInput').value,
            studio_address: document.getElementById('studioAddressInput').value,
            studio_lat: document.getElementById('studioLatInput').value,
            studio_lng: document.getElementById('studioLngInput').value,
            radius_meters: document.getElementById('studioRadiusInput').value,
            late_tolerance_minutes: document.getElementById('studioLateTolInput').value,
            work_start_time: document.getElementById('studioStartTimeInput').value,
            work_end_time: document.getElementById('studioEndTimeInput').value
        };

        const res = await fetch('/api/hris/admin/settings', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify(payload)
        });
        const json = await res.json();
        if (json.success) {
            alert('✅ Pengaturan studio berhasil disimpan ke database!');
            loadInitialData();
        }
    }

    // 14. Admin Payroll
    function calculatePayrollTotal() {
        const base = parseFloat(document.getElementById('payBaseInput').value || 0);
        const allowance = parseFloat(document.getElementById('payAllowanceInput').value || 0);
        const ot = parseFloat(document.getElementById('payOtInput').value || 0);
        const bonus = parseFloat(document.getElementById('payBonusInput').value || 0);
        const late = parseFloat(document.getElementById('payLateDeductInput').value || 0);
        const other = parseFloat(document.getElementById('payOtherDeductInput').value || 0);

        const total = Math.max(0, base + allowance + ot + bonus - late - other);
        document.getElementById('payCalculatedNetText').innerText = 'Rp ' + total.toLocaleString('id-ID');
    }

    function loadAdminPayrollForEmployee() {
        const empId = document.getElementById('admPayrollEmpSelect').value;
        const emp = state.employees.find(e => e.id == empId);
        if (emp) {
            document.getElementById('payBaseInput').value = emp.base_salary || 4500000;
            document.getElementById('payAllowanceInput').value = emp.allowance || 500000;
            calculatePayrollTotal();
        }
    }

    async function saveAdminPayroll(e) {
        e.preventDefault();
        const payload = {
            employee_id: document.getElementById('admPayrollEmpSelect').value,
            period_month: document.getElementById('admPayrollMonth').value,
            base_salary: document.getElementById('payBaseInput').value,
            allowance: document.getElementById('payAllowanceInput').value,
            overtime_pay: document.getElementById('payOtInput').value,
            bonus: document.getElementById('payBonusInput').value,
            late_deductions: document.getElementById('payLateDeductInput').value,
            deductions: document.getElementById('payOtherDeductInput').value
        };

        const res = await fetch('/api/hris/admin/payroll/save', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify(payload)
        });
        const json = await res.json();
        if (json.success) {
            alert(`✅ ${json.message}`);
        }
    }

    async function generateAllPayrollsMonthly() {
        const month = document.getElementById('admPayrollMonth').value;
        if (!confirm(`Hitung dan terbitkan slip gaji otomatis untuk seluruh karyawan periode ${month}?`)) return;

        const res = await fetch('/api/hris/admin/payrolls/generate', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ month: month })
        });
        const json = await res.json();
        if (json.success) {
            alert(`✅ ${json.message}`);
        }
    }

    // 15. Admin Rekap PDF
    function openPrintRecap() {
        const month = document.getElementById('rekapMonthInput').value;
        const div = document.getElementById('rekapDivSelect').value;
        window.open(`/hris/recap/print?month=${month}&division=${div}`, '_blank');
    }

    function closeModal(id) {
        document.getElementById(id).classList.remove('active');
    }
</script>

</body>
</html>
