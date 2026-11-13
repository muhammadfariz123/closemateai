<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="theme-color" content="#4f46e5">
    <title>HRIS Penapict - Mobile Attendance & Employee System</title>

    <!-- Google Fonts & Font Awesome -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        :root {
            --primary: #4f46e5;
            --primary-dark: #4338ca;
            --primary-light: #eef2ff;
            --secondary: #0ea5e9;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --bg-body: #f8fafc;
            --surface: #ffffff;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --border: #e2e8f0;
            --safe-bottom: env(safe-area-inset-bottom, 16px);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            -webkit-tap-highlight-color: transparent;
        }

        body {
            background: #0f172a;
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow-x: hidden;
        }

        /* Desktop Container Simulator */
        .app-wrapper {
            width: 100%;
            max-width: 440px;
            height: 100vh;
            max-height: 920px;
            background: var(--bg-body);
            display: flex;
            flex-direction: column;
            position: relative;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.4);
            overflow: hidden;
            border-radius: 36px;
            border: 8px solid #1e293b;
        }

        @media (max-width: 600px) {
            body { background: var(--bg-body); }
            .app-wrapper {
                max-width: 100%;
                height: 100vh;
                max-height: 100vh;
                border-radius: 0;
                border: none;
            }
            .desktop-nav-header { display: none !important; }
        }

        /* Top Desktop Floating Action Bar */
        .desktop-nav-header {
            position: fixed;
            top: 15px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            align-items: center;
            gap: 12px;
            background: rgba(30, 41, 59, 0.85);
            backdrop-filter: blur(10px);
            padding: 8px 16px;
            border-radius: 30px;
            color: #fff;
            font-size: 13px;
            z-index: 999;
            box-shadow: 0 10px 25px rgba(0,0,0,0.3);
        }
        .desktop-nav-header a {
            color: #94a3b8;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: color 0.2s;
        }
        .desktop-nav-header a:hover { color: #fff; }

        /* Mobile Header */
        .app-header {
            background: linear-gradient(135deg, #312e81 0%, #4338ca 50%, #4f46e5 100%);
            color: #fff;
            padding: 24px 20px 18px;
            flex-shrink: 0;
            position: relative;
            border-bottom-left-radius: 24px;
            border-bottom-right-radius: 24px;
            box-shadow: 0 10px 25px -5px rgba(79, 70, 229, 0.3);
        }
        .header-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 14px;
        }
        .user-pill {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .user-avatar {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            border: 2px solid rgba(255, 255, 255, 0.8);
            object-fit: cover;
            background: #6366f1;
        }
        .user-info h4 {
            font-size: 15px;
            font-weight: 700;
            line-height: 1.2;
        }
        .user-badge {
            display: inline-block;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(5px);
            font-size: 11px;
            font-weight: 600;
            padding: 2px 8px;
            border-radius: 20px;
            margin-top: 3px;
        }
        .emp-switcher-btn {
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: #fff;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s;
        }
        .emp-switcher-btn:hover { background: rgba(255,255,255,0.25); }

        /* Main View Container */
        .app-body {
            flex: 1;
            overflow-y: auto;
            padding: 16px 16px 90px;
            position: relative;
        }

        /* Bottom Navigation Bar */
        .bottom-nav {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 68px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(12px);
            border-top: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-around;
            padding-bottom: calc(var(--safe-bottom) - 10px);
            z-index: 100;
        }
        .nav-tab {
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
            padding: 6px 10px;
            border-radius: 12px;
            transition: all 0.2s;
            position: relative;
        }
        .nav-tab i {
            font-size: 18px;
            margin-bottom: 4px;
            transition: transform 0.2s;
        }
        .nav-tab.active {
            color: var(--primary);
        }
        .nav-tab.active i {
            transform: translateY(-2px);
        }
        .nav-tab-center {
            position: relative;
            top: -14px;
            background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%);
            color: #fff !important;
            width: 52px;
            height: 52px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 20px rgba(79, 70, 229, 0.45);
            border: 3px solid #fff;
        }
        .nav-tab-center i {
            font-size: 20px;
            margin-bottom: 0;
        }

        /* Cards & Widgets */
        .card {
            background: var(--surface);
            border-radius: 18px;
            padding: 16px;
            margin-bottom: 14px;
            border: 1px solid var(--border);
            box-shadow: 0 4px 15px -3px rgba(0, 0, 0, 0.03);
        }
        .card-header-flex {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
        }
        .card-title {
            font-size: 14px;
            font-weight: 700;
            color: var(--text-main);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        /* Clock & Shift Card */
        .attendance-summary-card {
            background: #ffffff;
            border-radius: 20px;
            padding: 18px;
            margin-bottom: 16px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 8px 25px -5px rgba(0,0,0,0.05);
        }
        .clock-display {
            text-align: center;
            margin-bottom: 14px;
        }
        .clock-time {
            font-size: 32px;
            font-weight: 800;
            color: #1e1b4b;
            letter-spacing: -1px;
        }
        .clock-date {
            font-size: 12px;
            color: var(--text-muted);
            font-weight: 500;
            margin-top: 2px;
        }
        .times-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-top: 12px;
        }
        .time-box {
            background: #f8fafc;
            border-radius: 14px;
            padding: 12px;
            text-align: center;
            border: 1px solid #edf2f7;
        }
        .time-box-label {
            font-size: 11px;
            color: var(--text-muted);
            font-weight: 600;
            text-transform: uppercase;
        }
        .time-box-val {
            font-size: 16px;
            font-weight: 800;
            color: var(--text-main);
            margin-top: 4px;
        }

        /* Status Badges */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
        }
        .badge-success { background: #dcfce7; color: #15803d; }
        .badge-warning { background: #fef3c7; color: #b45309; }
        .badge-danger { background: #fee2e2; color: #b91c1c; }
        .badge-info { background: #e0f2fe; color: #0369a1; }
        .badge-purple { background: #f3e8ff; color: #7e22ce; }

        /* Quick Action Grid */
        .quick-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin-bottom: 16px;
        }
        .quick-btn {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 12px 6px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s;
        }
        .quick-btn:active { transform: scale(0.95); }
        .quick-icon {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            margin-bottom: 6px;
        }
        .quick-btn span {
            font-size: 11px;
            font-weight: 600;
            color: var(--text-main);
        }

        /* Camera Scanner Styles */
        .camera-container {
            width: 100%;
            height: 320px;
            border-radius: 24px;
            background: #000;
            overflow: hidden;
            position: relative;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }
        #webcam-video {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transform: scaleX(-1); /* Mirror selfie */
        }
        .face-oval-guide {
            position: absolute;
            width: 190px;
            height: 250px;
            border: 2px dashed rgba(255, 255, 255, 0.7);
            border-radius: 50% / 60%;
            box-shadow: 0 0 0 9999px rgba(0, 0, 0, 0.45);
            pointer-events: none;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .scan-line {
            position: absolute;
            width: 100%;
            height: 2px;
            background: linear-gradient(90deg, transparent, #38bdf8, #fff, #38bdf8, transparent);
            animation: scanAnim 2.2s infinite alternate ease-in-out;
            box-shadow: 0 0 12px #38bdf8;
        }
        @keyframes scanAnim {
            0% { top: 10%; opacity: 0.6; }
            100% { top: 90%; opacity: 1; }
        }
        .cam-controls {
            position: absolute;
            top: 12px;
            right: 12px;
            display: flex;
            gap: 8px;
            z-index: 10;
        }
        .btn-cam-circle {
            background: rgba(0,0,0,0.5);
            backdrop-filter: blur(5px);
            border: 1px solid rgba(255,255,255,0.3);
            color: #fff;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }

        /* GPS Distance Card */
        .gps-status-card {
            background: #ffffff;
            border-radius: 16px;
            padding: 14px 16px;
            margin-bottom: 16px;
            border: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .gps-info { display: flex; align-items: center; gap: 12px; }
        .gps-icon {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
        }

        /* Action Buttons */
        .btn-action-primary {
            width: 100%;
            background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%);
            color: #fff;
            border: none;
            padding: 14px;
            border-radius: 16px;
            font-size: 14px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            cursor: pointer;
            box-shadow: 0 6px 20px rgba(79, 70, 229, 0.35);
            transition: all 0.2s;
        }
        .btn-action-primary:active { transform: scale(0.98); }
        .btn-action-danger {
            width: 100%;
            background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%);
            color: #fff;
            border: none;
            padding: 14px;
            border-radius: 16px;
            font-size: 14px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            cursor: pointer;
            box-shadow: 0 6px 20px rgba(239, 68, 68, 0.3);
            transition: all 0.2s;
        }

        /* Modals */
        .modal-overlay {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(15, 23, 42, 0.7);
            backdrop-filter: blur(4px);
            display: none;
            align-items: flex-end;
            justify-content: center;
            z-index: 1000;
        }
        .modal-overlay.active { display: flex; }
        .modal-sheet {
            width: 100%;
            max-width: 440px;
            background: #fff;
            border-top-left-radius: 28px;
            border-top-right-radius: 28px;
            padding: 22px 20px 30px;
            max-height: 85vh;
            overflow-y: auto;
            animation: slideUp 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }
        @keyframes slideUp {
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
        .modal-title { font-size: 16px; font-weight: 700; color: var(--text-main); }
        .modal-close-btn {
            background: #f1f5f9;
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

        /* Form elements */
        .form-group { margin-bottom: 14px; }
        .form-label { display: block; font-size: 12px; font-weight: 600; color: var(--text-muted); margin-bottom: 6px; }
        .form-control {
            width: 100%;
            padding: 10px 14px;
            border-radius: 12px;
            border: 1px solid var(--border);
            font-size: 13px;
            outline: none;
            background: #f8fafc;
            color: var(--text-main);
        }
        .form-control:focus { border-color: var(--primary); background: #fff; }

        /* Segmented Control Tabs */
        .segmented-control {
            display: flex;
            background: #e2e8f0;
            padding: 4px;
            border-radius: 14px;
            margin-bottom: 14px;
        }
        .segment-btn {
            flex: 1;
            padding: 8px 12px;
            border: none;
            background: none;
            font-size: 12px;
            font-weight: 700;
            color: var(--text-muted);
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.2s;
        }
        .segment-btn.active {
            background: #fff;
            color: var(--text-main);
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        }

        /* Timeline Items */
        .history-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #f1f5f9;
        }
        .history-item:last-child { border-bottom: none; }
    </style>
</head>
<body>

<!-- Floating Desktop Bar -->
<div class="desktop-nav-header">
    <a href="/dashboard"><i class="fa-solid fa-arrow-left"></i> Kembali ke Web Utama</a>
    <span style="opacity: 0.4;">|</span>
    <span>📱 <b>HRIS Penapict Mobile App</b></span>
</div>

<div class="app-wrapper">
    <!-- Mobile App Header -->
    <header class="app-header">
        <div class="header-top">
            <div class="user-pill">
                <img src="{{ $activeEmployee->avatar_url ?? 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=150' }}" alt="Avatar" class="user-avatar" id="headerAvatar">
                <div class="user-info">
                    <h4 id="headerEmpName">{{ $activeEmployee->name ?? 'Dimas Wicaksono' }}</h4>
                    <span class="user-badge" id="headerDivisionBadge">{{ $activeEmployee->division ?? 'Fotografer' }}</span>
                </div>
            </div>
            <button class="emp-switcher-btn" onclick="openEmployeePicker()">
                <i class="fa-solid fa-users"></i>
                <span>Ganti</span>
            </button>
        </div>
        <div style="display: flex; align-items: center; justify-content: space-between; font-size: 11px; opacity: 0.9;">
            <div id="headerStudioName"><i class="fa-solid fa-camera-retro"></i> {{ $settings->studio_name ?? 'Penapict Studio' }}</div>
            <div id="modePill" style="cursor: pointer;" onclick="switchAppTab('admin')">
                <i class="fa-solid fa-shield-halved"></i> {{ in_array(strtolower($activeEmployee->role ?? ''), ['owner', 'admin', 'hr', 'supervisor']) ? 'Admin Portal' : 'Karyawan' }}
            </div>
        </div>
    </header>

    <!-- App Body (Tabs View) -->
    <div class="app-body">
        
        <!-- ============================================== -->
        <!-- TAB 1: BERANDA (HOME)                          -->
        <!-- ============================================== -->
        <div id="tab-home" class="tab-content">
            <!-- Digital Clock & Today's Attendance Status -->
            <div class="attendance-summary-card">
                <div class="clock-display">
                    <div class="clock-time" id="liveClock">08:00:00</div>
                    <div class="clock-date" id="liveDate">Memuat tanggal...</div>
                </div>

                <div style="display: flex; align-items: center; justify-content: space-between; padding: 10px 14px; background: #f1f5f9; border-radius: 12px;">
                    <div style="font-size: 12px; font-weight: 600; color: var(--text-muted);">Status Kehadiran Hari Ini:</div>
                    <span id="homeTodayStatusBadge" class="badge badge-warning">Memeriksa...</span>
                </div>

                <div class="times-grid">
                    <div class="time-box">
                        <div class="time-box-label"><i class="fa-regular fa-clock"></i> Jam Masuk</div>
                        <div class="time-box-val" id="homeClockInVal">--:--</div>
                    </div>
                    <div class="time-box">
                        <div class="time-box-label"><i class="fa-solid fa-arrow-right-from-bracket"></i> Jam Pulang</div>
                        <div class="time-box-val" id="homeClockOutVal">--:--</div>
                    </div>
                </div>
            </div>

            <!-- Quick Action 4-Grid -->
            <div class="quick-grid">
                <div class="quick-btn" onclick="switchAppTab('absen')">
                    <div class="quick-icon" style="background: #e0e7ff; color: #4338ca;"><i class="fa-solid fa-camera"></i></div>
                    <span>Absen</span>
                </div>
                <div class="quick-btn" onclick="switchAppTab('pengajuan'); switchSubTab('cuti');">
                    <div class="quick-icon" style="background: #fef3c7; color: #b45309;"><i class="fa-solid fa-calendar-plus"></i></div>
                    <span>Izin/Cuti</span>
                </div>
                <div class="quick-btn" onclick="switchAppTab('pengajuan'); switchSubTab('lembur');">
                    <div class="quick-icon" style="background: #e0f2fe; color: #0284c7;"><i class="fa-solid fa-stopwatch"></i></div>
                    <span>Lembur</span>
                </div>
                <div class="quick-btn" onclick="switchAppTab('slip')">
                    <div class="quick-icon" style="background: #dcfce7; color: #16a34a;"><i class="fa-solid fa-file-invoice-dollar"></i></div>
                    <span>Slip Gaji</span>
                </div>
            </div>

            <!-- Studio Geofence & Location Live Card -->
            <div class="card">
                <div class="card-header-flex">
                    <div class="card-title"><i class="fa-solid fa-location-dot" style="color: #ef4444;"></i> Radius Presensi Studio</div>
                    <span id="geofenceRuleBadge" class="badge badge-purple">Radius 100m</span>
                </div>
                <div style="font-size: 12px; color: var(--text-muted); line-height: 1.5; margin-bottom: 10px;">
                    <b id="geofenceStudioName">{{ $settings->studio_name ?? 'Penapict Studio' }}</b><br>
                    <span id="geofenceStudioAddress">{{ $settings->studio_address ?? 'Jakarta Selatan' }}</span>
                </div>
                <div class="gps-status-card" style="margin-bottom: 0; background: #f8fafc;">
                    <div class="gps-info">
                        <div class="gps-icon" id="homeGpsIcon" style="background: #e2e8f0; color: #64748b;">
                            <i class="fa-solid fa-satellite-dish"></i>
                        </div>
                        <div>
                            <div style="font-size: 11px; color: var(--text-muted); font-weight: 600;">Jarak Anda ke Studio:</div>
                            <div style="font-size: 15px; font-weight: 800; color: var(--text-main);" id="homeDistanceVal">Mendeteksi GPS...</div>
                        </div>
                    </div>
                    <button onclick="detectLocation(true)" style="background: #fff; border: 1px solid #cbd5e1; border-radius: 8px; padding: 6px 10px; font-size: 11px; font-weight: 600; cursor: pointer;">
                        <i class="fa-solid fa-rotate"></i> Refresh
                    </button>
                </div>
                <div id="marketingBypassNotice" style="display: none; margin-top: 10px; font-size: 11px; color: #4338ca; background: #e0e7ff; padding: 8px 12px; border-radius: 8px; font-weight: 600;">
                    🌟 <b>Divisi Marketing Bebas Radius:</b> Anda dapat melakukan presensi di luar studio saat meeting atau visit klien/event foto.
                </div>
            </div>

            <!-- Studio Announcements -->
            <div class="card">
                <div class="card-header-flex">
                    <div class="card-title"><i class="fa-solid fa-bullhorn" style="color: #4f46e5;"></i> Pengumuman Studio</div>
                    <span class="badge badge-info">Info Penting</span>
                </div>
                <div id="homeAnnouncementsList">
                    <div style="font-size: 12px; color: var(--text-muted); text-align: center; padding: 10px;">Memuat pengumuman...</div>
                </div>
            </div>

            <!-- Recent Attendance Mini History -->
            <div class="card">
                <div class="card-header-flex">
                    <div class="card-title"><i class="fa-solid fa-calendar-check" style="color: #10b981;"></i> Riwayat 7 Hari Terakhir</div>
                </div>
                <div id="homeRecentAttendanceList">
                    <div style="font-size: 12px; color: var(--text-muted); text-align: center; padding: 10px;">Memuat riwayat...</div>
                </div>
            </div>
        </div>

        <!-- ============================================== -->
        <!-- TAB 2: ABSEN (CAMERA SELFIE + GPS GEOFENCE)    -->
        <!-- ============================================== -->
        <div id="tab-absen" class="tab-content" style="display: none;">
            <div class="card" style="padding: 14px;">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
                    <div>
                        <h3 style="font-size: 15px; font-weight: 800;">Presensi Digital Selfie</h3>
                        <p style="font-size: 11px; color: var(--text-muted);">Posisikan wajah Anda dalam bingkai oval di bawah</p>
                    </div>
                    <span id="camGeofenceBadge" class="badge badge-warning"><i class="fa-solid fa-spinner fa-spin"></i> Cek GPS</span>
                </div>

                <!-- Camera Viewfinder with Face Guide Oval -->
                <div class="camera-container" id="cameraBox">
                    <video id="webcam-video" autoplay playsinline></video>
                    <canvas id="photo-canvas" style="display: none;"></canvas>
                    
                    <div class="face-oval-guide">
                        <div class="scan-line"></div>
                    </div>

                    <div class="cam-controls">
                        <button class="btn-cam-circle" onclick="toggleCameraFacing()" title="Putar Kamera">
                            <i class="fa-solid fa-camera-rotate"></i>
                        </button>
                    </div>
                </div>

                <!-- GPS Accuracy & Distance Indicator -->
                <div class="gps-status-card">
                    <div class="gps-info">
                        <div class="gps-icon" id="camGpsIcon" style="background: #f1f5f9; color: #475569;">
                            <i class="fa-solid fa-location-crosshairs"></i>
                        </div>
                        <div>
                            <div style="font-size: 11px; color: var(--text-muted); font-weight: 600;">Jarak ke Titik Studio:</div>
                            <div style="font-size: 14px; font-weight: 800;" id="camDistanceVal">Mendeteksi...</div>
                        </div>
                    </div>
                    <div style="text-align: right;">
                        <span id="camRadiusStatusPill" class="badge badge-warning">Cek Radius</span>
                    </div>
                </div>

                <!-- Action Clock In / Clock Out Buttons -->
                <div id="clockActionContainer" style="display: flex; flex-direction: column; gap: 10px;">
                    <button class="btn-action-primary" id="btnClockIn" onclick="takePhotoAndClock('in')">
                        <i class="fa-solid fa-camera"></i> Ambil Foto & Absen Masuk
                    </button>
                    <button class="btn-action-danger" id="btnClockOut" onclick="takePhotoAndClock('out')">
                        <i class="fa-solid fa-arrow-right-from-bracket"></i> Ambil Foto & Absen Pulang
                    </button>
                </div>
            </div>

            <!-- Today's Clock In Captured Preview -->
            <div class="card" id="todayProofCard" style="display: none;">
                <div class="card-title" style="margin-bottom: 10px;"><i class="fa-solid fa-circle-check" style="color: #10b981;"></i> Bukti Presensi Hari Ini</div>
                <div style="display: flex; gap: 12px; align-items: center;">
                    <img id="todayProofImg" src="" style="width: 75px; height: 95px; border-radius: 12px; object-fit: cover; border: 2px solid #4f46e5;">
                    <div style="font-size: 12px; line-height: 1.6;">
                        <div><b>Jam Masuk:</b> <span id="todayProofInTime">--</span></div>
                        <div><b>Jam Pulang:</b> <span id="todayProofOutTime">--</span></div>
                        <div><b>Status:</b> <span id="todayProofStatus" class="badge badge-success">Hadir</span></div>
                        <div><b>Jarak Absen:</b> <span id="todayProofDist">--</span></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ============================================== -->
        <!-- TAB 3: PENGAJUAN (IZIN/CUTI & LEMBUR)           -->
        <!-- ============================================== -->
        <div id="tab-pengajuan" class="tab-content" style="display: none;">
            <div class="segmented-control">
                <button class="segment-btn active" id="btnSubTabCuti" onclick="switchSubTab('cuti')">
                    <i class="fa-regular fa-calendar"></i> Izin & Cuti
                </button>
                <button class="segment-btn" id="btnSubTabLembur" onclick="switchSubTab('lembur')">
                    <i class="fa-solid fa-stopwatch"></i> Lembur (Overtime)
                </button>
            </div>

            <!-- SUB TAB: CUTI & IZIN -->
            <div id="subtab-cuti">
                <div class="card" style="background: linear-gradient(135deg, #4338ca 0%, #6366f1 100%); color: #fff;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <div style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; opacity: 0.9;">Sisa Kuota Cuti Tahunan</div>
                            <div style="font-size: 28px; font-weight: 800; margin-top: 4px;" id="cutiBalanceVal">12 Hari</div>
                            <div style="font-size: 11px; opacity: 0.8;">Dari total 12 hari kuota tahun ini</div>
                        </div>
                        <button onclick="openLeaveModal()" style="background: #fff; color: #4338ca; border: none; padding: 10px 16px; border-radius: 12px; font-weight: 700; font-size: 12px; cursor: pointer; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
                            + Ajukan Cuti
                        </button>
                    </div>
                </div>

                <div class="card">
                    <div class="card-title" style="margin-bottom: 12px;"><i class="fa-solid fa-list-check" style="color: #4f46e5;"></i> Riwayat Pengajuan Izin/Cuti</div>
                    <div id="leavesHistoryList">
                        <div style="font-size: 12px; color: var(--text-muted); text-align: center; padding: 15px;">Memuat riwayat pengajuan...</div>
                    </div>
                </div>
            </div>

            <!-- SUB TAB: LEMBUR -->
            <div id="subtab-lembur" style="display: none;">
                <div class="card" style="background: linear-gradient(135deg, #0284c7 0%, #38bdf8 100%); color: #fff;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <div style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; opacity: 0.9;">Honor Lembur Project</div>
                            <div style="font-size: 22px; font-weight: 800; margin-top: 4px;" id="overtimeRateVal">Rp 25.000 / Jam</div>
                            <div style="font-size: 11px; opacity: 0.8;">Dihitung setelah disetujui Supervisor</div>
                        </div>
                        <button onclick="openOvertimeModal()" style="background: #fff; color: #0284c7; border: none; padding: 10px 16px; border-radius: 12px; font-weight: 700; font-size: 12px; cursor: pointer; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
                            + Ajukan Lembur
                        </button>
                    </div>
                </div>

                <div class="card">
                    <div class="card-title" style="margin-bottom: 12px;"><i class="fa-solid fa-clock-rotate-left" style="color: #0284c7;"></i> Riwayat Pengajuan Lembur</div>
                    <div id="overtimesHistoryList">
                        <div style="font-size: 12px; color: var(--text-muted); text-align: center; padding: 15px;">Memuat data lembur...</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ============================================== -->
        <!-- TAB 4: SLIP GAJI                               -->
        <!-- ============================================== -->
        <div id="tab-slip" class="tab-content" style="display: none;">
            <div class="card" style="background: linear-gradient(135deg, #1e1b4b 0%, #312e81 100%); color: #fff;">
                <div style="font-size: 11px; text-transform: uppercase; opacity: 0.8; letter-spacing: 0.5px;">Estimasi Gaji Pokok & Tunjangan</div>
                <div style="font-size: 26px; font-weight: 800; margin-top: 4px;" id="slipEstimatedTakeHome">Rp 0</div>
                <div style="display: flex; gap: 8px; margin-top: 10px; font-size: 11px; opacity: 0.9;">
                    <span>Pokok: <b id="slipBaseVal">Rp 0</b></span> |
                    <span>Tunjangan: <b id="slipAllowanceVal">Rp 0</b></span>
                </div>
            </div>

            <div class="card">
                <div class="card-header-flex">
                    <div class="card-title"><i class="fa-solid fa-receipt" style="color: #10b981;"></i> Daftar Slip Gaji Resmi</div>
                </div>
                <div id="payrollsList">
                    <div style="font-size: 12px; color: var(--text-muted); text-align: center; padding: 15px;">Memuat data slip gaji...</div>
                </div>
            </div>
        </div>

        <!-- ============================================== -->
        <!-- TAB 5: ADMIN / MANAGEMENT PORTAL               -->
        <!-- ============================================== -->
        <div id="tab-admin" class="tab-content" style="display: none;">
            <!-- Admin Metrics Card -->
            <div class="card" style="padding: 14px;">
                <div class="card-header-flex" style="margin-bottom: 10px;">
                    <div class="card-title"><i class="fa-solid fa-chart-pie" style="color: #4f46e5;"></i> Rekap Presensi Tim Hari Ini</div>
                    <span class="badge badge-info" id="adminDateBadge">Hari Ini</span>
                </div>
                <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 8px; text-align: center;">
                    <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 12px; padding: 10px 4px;">
                        <div style="font-size: 18px; font-weight: 800; color: #16a34a;" id="statPresent">0</div>
                        <div style="font-size: 10px; font-weight: 600; color: #15803d; margin-top: 2px;">Hadir</div>
                    </div>
                    <div style="background: #fffbeb; border: 1px solid #fef3c7; border-radius: 12px; padding: 10px 4px;">
                        <div style="font-size: 18px; font-weight: 800; color: #d97706;" id="statLate">0</div>
                        <div style="font-size: 10px; font-weight: 600; color: #b45309; margin-top: 2px;">Terlambat</div>
                    </div>
                    <div style="background: #eff6ff; border: 1px solid #dbeafe; border-radius: 12px; padding: 10px 4px;">
                        <div style="font-size: 18px; font-weight: 800; color: #2563eb;" id="statLeave">0</div>
                        <div style="font-size: 10px; font-weight: 600; color: #1d4ed8; margin-top: 2px;">Izin/Cuti</div>
                    </div>
                    <div style="background: #fef2f2; border: 1px solid #fee2e2; border-radius: 12px; padding: 10px 4px;">
                        <div style="font-size: 18px; font-weight: 800; color: #dc2626;" id="statAbsent">0</div>
                        <div style="font-size: 10px; font-weight: 600; color: #b91c1c; margin-top: 2px;">Belum</div>
                    </div>
                </div>
            </div>

            <!-- Admin Action Buttons (Payroll & Recap Export) -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 14px;">
                <button onclick="triggerGeneratePayroll()" style="background: #4f46e5; color: #fff; border: none; padding: 12px; border-radius: 14px; font-size: 12px; font-weight: 700; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 6px;">
                    <i class="fa-solid fa-calculator"></i> Hitung Gaji Bulan Ini
                </button>
                <a href="/hris/recap/print" target="_blank" style="background: #0f172a; color: #fff; text-decoration: none; padding: 12px; border-radius: 14px; font-size: 12px; font-weight: 700; text-align: center; display: flex; align-items: center; justify-content: center; gap: 6px;">
                    <i class="fa-solid fa-print"></i> Cetak Rekap Bulanan
                </a>
            </div>

            <!-- Approval Center (Leaves & Overtime) -->
            <div class="card">
                <div class="card-header-flex">
                    <div class="card-title"><i class="fa-solid fa-signature" style="color: #f59e0b;"></i> Approval Cuti & Lembur</div>
                    <span class="badge badge-warning" id="adminPendingCount">0 Menunggu</span>
                </div>
                <div id="adminApprovalList">
                    <div style="font-size: 12px; color: var(--text-muted); text-align: center; padding: 10px;">Tidak ada pengajuan pending.</div>
                </div>
            </div>

            <!-- Live Presensi Tim Hari Ini -->
            <div class="card">
                <div class="card-header-flex">
                    <div class="card-title"><i class="fa-solid fa-camera-viewfinder" style="color: #10b981;"></i> Presensi Tim Hari Ini</div>
                </div>
                <div id="adminTodayAttendancesList">
                    <div style="font-size: 12px; color: var(--text-muted); text-align: center; padding: 10px;">Memuat presensi tim...</div>
                </div>
            </div>

            <!-- Employee Management -->
            <div class="card">
                <div class="card-header-flex">
                    <div class="card-title"><i class="fa-solid fa-users-gear" style="color: #4338ca;"></i> Kelola Karyawan Studio</div>
                    <button onclick="openEmployeeModal()" style="background: #4f46e5; color: #fff; border: none; padding: 6px 12px; border-radius: 8px; font-size: 11px; font-weight: 700; cursor: pointer;">
                        + Karyawan
                    </button>
                </div>
                <div id="adminEmployeesList">
                    <div style="font-size: 12px; color: var(--text-muted); text-align: center; padding: 10px;">Memuat daftar karyawan...</div>
                </div>
            </div>

            <!-- Studio Geofence & Settings Form -->
            <div class="card">
                <div class="card-title" style="margin-bottom: 12px;"><i class="fa-solid fa-sliders" style="color: #6366f1;"></i> Pengaturan Studio & Geofence</div>
                <form id="formHrisSettings" onsubmit="saveHrisSettings(event)">
                    <div class="form-group">
                        <label class="form-label">Nama Studio Fotografi</label>
                        <input type="text" class="form-control" id="setStudioName" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Alamat Studio</label>
                        <input type="text" class="form-control" id="setStudioAddress">
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                        <div class="form-group">
                            <label class="form-label">Latitude Studio</label>
                            <input type="number" step="any" class="form-control" id="setStudioLat" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Longitude Studio</label>
                            <input type="number" step="any" class="form-control" id="setStudioLng" required>
                        </div>
                    </div>
                    <button type="button" onclick="setStudioCoordsFromCurrentGps()" style="width: 100%; margin-bottom: 12px; background: #f1f5f9; border: 1px dashed #94a3b8; padding: 8px; border-radius: 10px; font-size: 11px; font-weight: 700; color: #334155; cursor: pointer;">
                        <i class="fa-solid fa-location-crosshairs"></i> Gunakan Koordinat GPS Saya Saat Ini
                    </button>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                        <div class="form-group">
                            <label class="form-label">Radius Geofence (Meter)</label>
                            <input type="number" class="form-control" id="setRadiusMeters" value="100" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Toleransi Keterlambatan (Menit)</label>
                            <input type="number" class="form-control" id="setLateTolerance" value="15" required>
                        </div>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                        <div class="form-group">
                            <label class="form-label">Jam Masuk</label>
                            <input type="time" class="form-control" id="setWorkStart" value="08:30" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Jam Pulang</label>
                            <input type="time" class="form-control" id="setWorkEnd" value="17:30" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Upah Lembur per Jam (Rp)</label>
                        <input type="number" class="form-control" id="setOvertimeRate" value="25000" required>
                    </div>
                    <button type="submit" class="btn-action-primary" style="padding: 10px;">
                        <i class="fa-solid fa-floppy-disk"></i> Simpan Pengaturan Studio
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Bottom Navigation Bar -->
    <nav class="bottom-nav">
        <button class="nav-tab active" id="nav-home" onclick="switchAppTab('home')">
            <i class="fa-solid fa-house"></i>
            <span>Beranda</span>
        </button>
        <button class="nav-tab" id="nav-pengajuan" onclick="switchAppTab('pengajuan')">
            <i class="fa-solid fa-file-signature"></i>
            <span>Pengajuan</span>
        </button>
        <button class="nav-tab nav-tab-center" id="nav-absen" onclick="switchAppTab('absen')">
            <i class="fa-solid fa-camera"></i>
        </button>
        <button class="nav-tab" id="nav-slip" onclick="switchAppTab('slip')">
            <i class="fa-solid fa-wallet"></i>
            <span>Slip Gaji</span>
        </button>
        <button class="nav-tab" id="nav-admin" onclick="switchAppTab('admin')">
            <i class="fa-solid fa-user-gear"></i>
            <span id="navAdminLabel">Admin</span>
        </button>
    </nav>
</div>

<!-- ============================================== -->
<!-- MODALS                                         -->
<!-- ============================================== -->

<!-- 1. Employee Persona Switcher Modal -->
<div class="modal-overlay" id="modalEmpPicker">
    <div class="modal-sheet">
        <div class="modal-header">
            <div class="modal-title">Pilih Profil Karyawan</div>
            <button class="modal-close-btn" onclick="closeModal('modalEmpPicker')"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <p style="font-size: 12px; color: var(--text-muted); margin-bottom: 12px;">Pilih karyawan untuk simulasi presensi, cuti, atau hak akses:</p>
        <div id="empPickerList" style="display: flex; flex-direction: column; gap: 8px;">
            <!-- Rendered by JS -->
        </div>
    </div>
</div>

<!-- 2. Pengajuan Cuti / Izin Modal -->
<div class="modal-overlay" id="modalLeave">
    <div class="modal-sheet">
        <div class="modal-header">
            <div class="modal-title">Formulir Pengajuan Cuti / Izin</div>
            <button class="modal-close-btn" onclick="closeModal('modalLeave')"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form onsubmit="submitLeaveForm(event)">
            <div class="form-group">
                <label class="form-label">Jenis Pengajuan</label>
                <select class="form-control" id="leaveType" required>
                    <option value="Cuti Tahunan">Cuti Tahunan</option>
                    <option value="Izin Sakit">Izin Sakit (Surat Dokter)</option>
                    <option value="Keperluan Pribadi">Keperluan Mendesak/Keluarga</option>
                    <option value="Melahirkan">Cuti Melahirkan</option>
                    <option value="Lainnya">Lainnya</option>
                </select>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                <div class="form-group">
                    <label class="form-label">Tanggal Mulai</label>
                    <input type="date" class="form-control" id="leaveStartDate" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Tanggal Selesai</label>
                    <input type="date" class="form-control" id="leaveEndDate" required>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Alasan Pengajuan</label>
                <textarea class="form-control" id="leaveReason" rows="3" placeholder="Jelaskan keperluan izin/cuti..." required></textarea>
            </div>
            <button type="submit" class="btn-action-primary">Kirim Pengajuan Cuti</button>
        </form>
    </div>
</div>

<!-- 3. Pengajuan Lembur Modal -->
<div class="modal-overlay" id="modalOvertime">
    <div class="modal-sheet">
        <div class="modal-header">
            <div class="modal-title">Pengajuan Lembur (Overtime)</div>
            <button class="modal-close-btn" onclick="closeModal('modalOvertime')"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form onsubmit="submitOvertimeForm(event)">
            <div class="form-group">
                <label class="form-label">Tanggal Lembur</label>
                <input type="date" class="form-control" id="otDate" required>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                <div class="form-group">
                    <label class="form-label">Jam Mulai</label>
                    <input type="time" class="form-control" id="otStartTime" value="18:00" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Jam Selesai</label>
                    <input type="time" class="form-control" id="otEndTime" value="21:00" required>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Nama Project / Klien Foto</label>
                <input type="text" class="form-control" id="otProjectName" placeholder="Contoh: Wedding Raffi, Editing Batch Album" required>
            </div>
            <div class="form-group">
                <label class="form-label">Deskripsi Pekerjaan Lembur</label>
                <textarea class="form-control" id="otDescription" rows="2" placeholder="Rincian pekerjaan yang diselesaikan..."></textarea>
            </div>
            <button type="submit" class="btn-action-primary">Kirim Pengajuan Lembur</button>
        </form>
    </div>
</div>

<!-- 4. Tambah / Edit Karyawan Modal -->
<div class="modal-overlay" id="modalEmployee">
    <div class="modal-sheet">
        <div class="modal-header">
            <div class="modal-title" id="modalEmployeeTitle">Tambah Karyawan Baru</div>
            <button class="modal-close-btn" onclick="closeModal('modalEmployee')"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form onsubmit="saveEmployeeForm(event)">
            <input type="hidden" id="empId">
            <div class="form-group">
                <label class="form-label">Nama Lengkap</label>
                <input type="text" class="form-control" id="empName" required>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                <div class="form-group">
                    <label class="form-label">NIK / Kode Karyawan</label>
                    <input type="text" class="form-control" id="empCode" placeholder="Otomatis jika kosong">
                </div>
                <div class="form-group">
                    <label class="form-label">Divisi</label>
                    <select class="form-control" id="empDivision" required>
                        <option value="Fotografer">Fotografer</option>
                        <option value="Videografer">Videografer</option>
                        <option value="Editor">Editor</option>
                        <option value="Produksi">Produksi</option>
                        <option value="Marketing">Marketing (Bebas Radius)</option>
                        <option value="Admin">Admin</option>
                        <option value="HR">HR</option>
                    </select>
                </div>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                <div class="form-group">
                    <label class="form-label">Jabatan / Posisi</label>
                    <input type="text" class="form-control" id="empPosition" placeholder="Contoh: Senior Photographer" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Role Akses</label>
                    <select class="form-control" id="empRole" required>
                        <option value="karyawan">Karyawan</option>
                        <option value="supervisor">Supervisor</option>
                        <option value="admin">Admin</option>
                        <option value="hr">HR</option>
                    </select>
                </div>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                <div class="form-group">
                    <label class="form-label">Gaji Pokok (Rp)</label>
                    <input type="number" class="form-control" id="empBaseSalary" value="3500000" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Tunjangan (Rp)</label>
                    <input type="number" class="form-control" id="empAllowance" value="500000" required>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">No. WhatsApp</label>
                <input type="text" class="form-control" id="empPhone" placeholder="08xxxxxxxxxx">
            </div>
            <button type="submit" class="btn-action-primary">Simpan Data Karyawan</button>
        </form>
    </div>
</div>

<script>
    // Global State
    let appData = {
        settings: {},
        employee: {},
        employees: [],
        today_attendance: null,
        announcements: [],
        currentLat: null,
        currentLng: null,
        currentDistance: null,
        cameraStream: null,
        useFrontCamera: true
    };

    // Initialize App on DOM Load
    document.addEventListener('DOMContentLoaded', () => {
        startLiveClock();
        loadInitialData();
        detectLocation();

        // Default date picker values
        const todayIso = new Date().toISOString().split('T')[0];
        if (document.getElementById('leaveStartDate')) document.getElementById('leaveStartDate').value = todayIso;
        if (document.getElementById('leaveEndDate')) document.getElementById('leaveEndDate').value = todayIso;
        if (document.getElementById('otDate')) document.getElementById('otDate').value = todayIso;
    });

    // 1. Digital Clock
    function startLiveClock() {
        const update = () => {
            const now = new Date();
            const timeStr = now.toLocaleTimeString('id-ID', { hour12: false });
            const dateStr = now.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
            
            const clockEl = document.getElementById('liveClock');
            const dateEl = document.getElementById('liveDate');
            if (clockEl) clockEl.innerText = timeStr;
            if (dateEl) dateEl.innerText = dateStr;
        };
        update();
        setInterval(update, 1000);
    }

    // 2. Fetch Initial API Data
    async function loadInitialData() {
        try {
            const res = await fetch('/api/hris/initial-data');
            const json = await res.json();
            if (json.success) {
                appData.settings = json.settings || {};
                appData.employee = json.employee || {};
                appData.employees = json.employees || [];
                appData.today_attendance = json.today_attendance;
                appData.announcements = json.announcements || [];

                renderProfile();
                renderTodayAttendance();
                renderAnnouncements();
                renderGeofenceInfo();
                loadRecentHistory();
                loadLeaves();
                loadOvertimes();
                loadPayrolls();

                if (appData.employee && ['owner', 'admin', 'hr', 'supervisor'].includes((appData.employee.role || '').toLowerCase())) {
                    loadAdminData();
                }
            }
        } catch (e) {
            console.error('Failed to load HRIS data:', e);
        }
    }

    // Render User Header & Details
    function renderProfile() {
        const emp = appData.employee;
        if (!emp || !emp.name) return;

        document.getElementById('headerEmpName').innerText = emp.name;
        document.getElementById('headerDivisionBadge').innerText = emp.division + ' • ' + emp.position;
        if (emp.avatar_url) document.getElementById('headerAvatar').src = emp.avatar_url;

        // Geofence notice for Marketing
        const mktNotice = document.getElementById('marketingBypassNotice');
        if (mktNotice) {
            mktNotice.style.display = (emp.division || '').toLowerCase() === 'marketing' ? 'block' : 'none';
        }

        // Leave & salary estimates
        if (document.getElementById('cutiBalanceVal')) {
            document.getElementById('cutiBalanceVal').innerText = (emp.leave_balance || 0) + ' Hari';
        }
        if (document.getElementById('slipEstimatedTakeHome')) {
            const total = (emp.base_salary || 0) + (emp.allowance || 0);
            document.getElementById('slipEstimatedTakeHome').innerText = 'Rp ' + total.toLocaleString('id-ID');
            document.getElementById('slipBaseVal').innerText = 'Rp ' + (emp.base_salary || 0).toLocaleString('id-ID');
            document.getElementById('slipAllowanceVal').innerText = 'Rp ' + (emp.allowance || 0).toLocaleString('id-ID');
        }
    }

    // Render Today's Attendance Box
    function renderTodayAttendance() {
        const att = appData.today_attendance;
        const badge = document.getElementById('homeTodayStatusBadge');
        const inVal = document.getElementById('homeClockInVal');
        const outVal = document.getElementById('homeClockOutVal');
        const proofCard = document.getElementById('todayProofCard');

        if (!att || !att.clock_in) {
            badge.className = 'badge badge-warning';
            badge.innerHTML = '<i class="fa-solid fa-circle-exclamation"></i> Belum Absen Masuk';
            inVal.innerText = '--:--';
            outVal.innerText = '--:--';
            if (proofCard) proofCard.style.display = 'none';
            return;
        }

        const inTime = new Date(att.clock_in).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
        inVal.innerText = inTime;

        if (att.status === 'terlambat') {
            badge.className = 'badge badge-danger';
            badge.innerHTML = `<i class="fa-solid fa-triangle-exclamation"></i> Terlambat (${att.late_minutes}m)`;
        } else {
            badge.className = 'badge badge-success';
            badge.innerHTML = '<i class="fa-solid fa-circle-check"></i> Hadir Tepat Waktu';
        }

        if (att.clock_out) {
            const outTime = new Date(att.clock_out).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
            outVal.innerText = outTime;
            badge.className = 'badge badge-info';
            badge.innerHTML = '<i class="fa-solid fa-flag-checkered"></i> Selesai Kerja';
        } else {
            outVal.innerText = 'Aktif Bekerja';
        }

        // Show Proof Card
        if (proofCard && att.clock_in_photo) {
            proofCard.style.display = 'block';
            document.getElementById('todayProofImg').src = att.clock_in_photo;
            document.getElementById('todayProofInTime').innerText = inTime;
            document.getElementById('todayProofOutTime').innerText = att.clock_out ? new Date(att.clock_out).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) : '-';
            document.getElementById('todayProofStatus').innerText = att.status.toUpperCase();
            document.getElementById('todayProofDist').innerText = (att.distance_meters || 0) + 'm dari Studio';
        }
    }

    // Render Studio Geofence Details
    function renderGeofenceInfo() {
        const s = appData.settings;
        if (!s) return;
        document.getElementById('geofenceStudioName').innerText = s.studio_name || 'Penapict Studio';
        document.getElementById('geofenceStudioAddress').innerText = s.studio_address || 'Jl. Kemang Raya No. 45';
        document.getElementById('geofenceRuleBadge').innerText = `Radius ${s.radius_meters || 100}m`;
        document.getElementById('headerStudioName').innerHTML = `<i class="fa-solid fa-camera-retro"></i> ${s.studio_name || 'Penapict Studio'}`;
        if (document.getElementById('overtimeRateVal')) {
            document.getElementById('overtimeRateVal').innerText = `Rp ${(s.overtime_rate_per_hour || 25000).toLocaleString('id-ID')} / Jam`;
        }
    }

    // Render Studio Announcements
    function renderAnnouncements() {
        const list = document.getElementById('homeAnnouncementsList');
        if (!list) return;
        if (!appData.announcements || appData.announcements.length === 0) {
            list.innerHTML = '<div style="font-size: 12px; color: var(--text-muted); text-align: center; padding: 10px;">Belum ada pengumuman baru.</div>';
            return;
        }

        list.innerHTML = appData.announcements.map(a => `
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 12px; margin-bottom: 8px;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 4px;">
                    <div style="font-size: 13px; font-weight: 700; color: #0f172a;">${a.title}</div>
                    ${a.is_pinned ? '<span class="badge badge-purple" style="font-size: 9px;"><i class="fa-solid fa-thumbtack"></i> Pinned</span>' : ''}
                </div>
                <div style="font-size: 12px; color: #475569; line-height: 1.4;">${a.content}</div>
                <div style="font-size: 10px; color: #94a3b8; margin-top: 6px;">Kategori: ${a.category || 'Umum'}</div>
            </div>
        `).join('');
    }

    // 3. Geolocation & Haversine Distance Calculation
    function calculateDistance(lat1, lon1, lat2, lon2) {
        const R = 6371000; // Radius bumi dalam meter
        const dLat = (lat2 - lat1) * Math.PI / 180;
        const dLon = (lon2 - lon1) * Math.PI / 180;
        const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                  Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                  Math.sin(dLon/2) * Math.sin(dLon/2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
        return Math.round(R * c * 10) / 10;
    }

    function detectLocation(notifyUser = false) {
        if (!navigator.geolocation) {
            alert('Browser Anda tidak mendukung deteksi lokasi GPS.');
            return;
        }

        navigator.geolocation.getCurrentPosition(
            (pos) => {
                appData.currentLat = pos.coords.latitude;
                appData.currentLng = pos.coords.longitude;

                const studioLat = (appData.settings && appData.settings.studio_lat) ? parseFloat(appData.settings.studio_lat) : -6.2607187;
                const studioLng = (appData.settings && appData.settings.studio_lng) ? parseFloat(appData.settings.studio_lng) : 106.8166667;
                const maxRadius = (appData.settings && appData.settings.radius_meters) ? parseInt(appData.settings.radius_meters) : 100;

                const dist = calculateDistance(appData.currentLat, appData.currentLng, studioLat, studioLng);
                appData.currentDistance = dist;

                const isMarketing = (appData.employee.division || '').toLowerCase() === 'marketing';
                const inRadius = dist <= maxRadius;

                // Update Home widget
                const distEl = document.getElementById('homeDistanceVal');
                const gpsIcon = document.getElementById('homeGpsIcon');
                if (distEl) distEl.innerText = `${dist} Meter`;
                if (gpsIcon) {
                    gpsIcon.style.background = (inRadius || isMarketing) ? '#dcfce7' : '#fee2e2';
                    gpsIcon.style.color = (inRadius || isMarketing) ? '#16a34a' : '#dc2626';
                }

                // Update Camera Tab indicators
                const camDistEl = document.getElementById('camDistanceVal');
                const camPill = document.getElementById('camRadiusStatusPill');
                const camBadge = document.getElementById('camGeofenceBadge');
                if (camDistEl) camDistEl.innerText = `${dist}m dari Studio`;

                if (isMarketing) {
                    if (camPill) { camPill.className = 'badge badge-purple'; camPill.innerHTML = '🌟 Bebas Radius'; }
                    if (camBadge) { camBadge.className = 'badge badge-purple'; camBadge.innerHTML = 'Marketing Mobile'; }
                } else if (inRadius) {
                    if (camPill) { camPill.className = 'badge badge-success'; camPill.innerHTML = `✅ Di Dalam Radius (< ${maxRadius}m)`; }
                    if (camBadge) { camBadge.className = 'badge badge-success'; camBadge.innerHTML = 'Lokasi Valid'; }
                } else {
                    if (camPill) { camPill.className = 'badge badge-danger'; camPill.innerHTML = `⚠️ Di Luar Radius (> ${maxRadius}m)`; }
                    if (camBadge) { camBadge.className = 'badge badge-danger'; camBadge.innerHTML = 'Lokasi Jauh'; }
                }

                if (notifyUser) {
                    alert(`GPS berhasil diperbarui! Jarak Anda ke Studio: ${dist} meter.`);
                }
            },
            (err) => {
                console.warn('Geolocation error:', err);
                const distEl = document.getElementById('homeDistanceVal');
                if (distEl) distEl.innerText = 'GPS Dinonaktifkan';
            },
            { enableHighAccuracy: true, timeout: 8000, maximumAge: 0 }
        );
    }

    // 4. Camera Stream & Selfie Scanner
    async function startCamera() {
        try {
            stopCamera();
            const constraints = {
                video: {
                    facingMode: appData.useFrontCamera ? 'user' : 'environment',
                    width: { ideal: 640 },
                    height: { ideal: 480 }
                },
                audio: false
            };
            appData.cameraStream = await navigator.mediaDevices.getUserMedia(constraints);
            const videoEl = document.getElementById('webcam-video');
            if (videoEl) {
                videoEl.srcObject = appData.cameraStream;
                videoEl.style.transform = appData.useFrontCamera ? 'scaleX(-1)' : 'none';
            }
        } catch (err) {
            console.error('Camera access denied:', err);
            alert('Akses kamera ditolak atau tidak tersedia. Harap izinkan kamera pada browser Anda.');
        }
    }

    function stopCamera() {
        if (appData.cameraStream) {
            appData.cameraStream.getTracks().forEach(track => track.stop());
            appData.cameraStream = null;
        }
    }

    function toggleCameraFacing() {
        appData.useFrontCamera = !appData.useFrontCamera;
        startCamera();
    }

    function captureSelfieData() {
        const video = document.getElementById('webcam-video');
        const canvas = document.getElementById('photo-canvas');
        if (!video || !canvas) return null;

        canvas.width = video.videoWidth || 640;
        canvas.height = video.videoHeight || 480;
        const ctx = canvas.getContext('2d');

        if (appData.useFrontCamera) {
            ctx.translate(canvas.width, 0);
            ctx.scale(-1, 1);
        }
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
        return canvas.toDataURL('image/jpeg', 0.85);
    }

    // 5. Clock In / Out Action
    async function takePhotoAndClock(type) {
        if (!appData.currentLat || !appData.currentLng) {
            alert('Koordinat GPS belum terdeteksi. Silakan klik tombol Refresh GPS atau izinkan lokasi browser.');
            detectLocation(true);
            return;
        }

        const isMarketing = (appData.employee.division || '').toLowerCase() === 'marketing';
        const maxRadius = (appData.settings && appData.settings.radius_meters) ? parseInt(appData.settings.radius_meters) : 100;
        
        if (!isMarketing && appData.currentDistance > maxRadius) {
            alert(`Presensi Ditolak: Lokasi Anda berjarak ${appData.currentDistance} meter dari Studio (Maksimal ${maxRadius} meter). Silakan masuk ke area studio.`);
            return;
        }

        const photoBase64 = captureSelfieData();
        if (!photoBase64) {
            alert('Gagal mengambil foto selfie. Pastikan kamera telah aktif.');
            return;
        }

        const endpoint = type === 'in' ? '/api/hris/attendance/clock-in' : '/api/hris/attendance/clock-out';
        const btn = type === 'in' ? document.getElementById('btnClockIn') : document.getElementById('btnClockOut');
        const oldHtml = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Memproses Presensi...';

        try {
            const res = await fetch(endpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    employee_id: appData.employee.id,
                    photo: photoBase64,
                    lat: appData.currentLat,
                    lng: appData.currentLng
                })
            });

            const json = await res.json();
            if (json.success) {
                alert(`✅ ${json.message}`);
                loadInitialData();
                switchAppTab('home');
            } else {
                alert(`❌ ${json.message || 'Gagal memproses presensi'}`);
            }
        } catch (e) {
            console.error(e);
            alert('Terjadi kesalahan jaringan saat menyimpan presensi.');
        } finally {
            btn.disabled = false;
            btn.innerHTML = oldHtml;
        }
    }

    // 6. Tab Switching Logic
    function switchAppTab(tabId) {
        document.querySelectorAll('.tab-content').forEach(el => el.style.display = 'none');
        document.querySelectorAll('.nav-tab').forEach(el => el.classList.remove('active'));

        const target = document.getElementById(`tab-${tabId}`);
        const nav = document.getElementById(`nav-${tabId}`);
        if (target) target.style.display = 'block';
        if (nav) nav.classList.add('active');

        // Manage camera stream when entering/leaving absen tab
        if (tabId === 'absen') {
            startCamera();
            detectLocation();
        } else {
            stopCamera();
        }
    }

    function switchSubTab(sub) {
        const cutiTab = document.getElementById('subtab-cuti');
        const lemburTab = document.getElementById('subtab-lembur');
        const btnCuti = document.getElementById('btnSubTabCuti');
        const btnLembur = document.getElementById('btnSubTabLembur');

        if (sub === 'cuti') {
            cutiTab.style.display = 'block';
            lemburTab.style.display = 'none';
            btnCuti.classList.add('active');
            btnLembur.classList.remove('active');
        } else {
            cutiTab.style.display = 'none';
            lemburTab.style.display = 'block';
            btnCuti.classList.remove('active');
            btnLembur.classList.add('active');
        }
    }

    // 7. Modals
    function openModal(id) {
        document.getElementById(id).classList.add('active');
    }
    function closeModal(id) {
        document.getElementById(id).classList.remove('active');
    }

    // 8. Employee Persona Switcher
    function openEmployeePicker() {
        const list = document.getElementById('empPickerList');
        if (!list) return;

        list.innerHTML = appData.employees.map(emp => `
            <div onclick="selectEmployee(${emp.id})" style="display: flex; align-items: center; justify-content: space-between; padding: 12px; border: 1px solid ${emp.id === appData.employee.id ? '#4f46e5' : '#e2e8f0'}; background: ${emp.id === appData.employee.id ? '#eef2ff' : '#f8fafc'}; border-radius: 14px; cursor: pointer;">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <img src="${emp.avatar_url || 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=150'}" style="width: 38px; height: 38px; border-radius: 50%; object-fit: cover;">
                    <div>
                        <div style="font-size: 13px; font-weight: 700; color: #0f172a;">${emp.name}</div>
                        <div style="font-size: 11px; color: #64748b;">${emp.division} • ${emp.position}</div>
                    </div>
                </div>
                <span class="badge ${emp.division === 'Marketing' ? 'badge-purple' : 'badge-info'}">${emp.role.toUpperCase()}</span>
            </div>
        `).join('');

        openModal('modalEmpPicker');
    }

    async function selectEmployee(empId) {
        try {
            const res = await fetch('/api/hris/switch-employee', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ employee_id: empId })
            });
            const json = await res.json();
            if (json.success) {
                closeModal('modalEmpPicker');
                loadInitialData();
            }
        } catch (e) {
            console.error(e);
        }
    }

    // 9. Leave Form Actions
    function openLeaveModal() {
        openModal('modalLeave');
    }

    async function submitLeaveForm(e) {
        e.preventDefault();
        const payload = {
            employee_id: appData.employee.id,
            type: document.getElementById('leaveType').value,
            start_date: document.getElementById('leaveStartDate').value,
            end_date: document.getElementById('leaveEndDate').value,
            reason: document.getElementById('leaveReason').value
        };

        try {
            const res = await fetch('/api/hris/leaves', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify(payload)
            });
            const json = await res.json();
            if (json.success) {
                alert('✅ Pengajuan cuti berhasil dikirim.');
                closeModal('modalLeave');
                loadLeaves();
                loadInitialData();
            } else {
                alert(`❌ ${json.message}`);
            }
        } catch (err) {
            console.error(err);
        }
    }

    async function loadLeaves() {
        const list = document.getElementById('leavesHistoryList');
        if (!list) return;

        try {
            const res = await fetch('/api/hris/leaves');
            const json = await res.json();
            if (json.success && json.leaves.length > 0) {
                list.innerHTML = json.leaves.map(l => `
                    <div class="history-item">
                        <div>
                            <div style="font-size: 13px; font-weight: 700; color: #0f172a;">${l.type} (${l.total_days} Hari)</div>
                            <div style="font-size: 11px; color: #64748b;">${l.start_date} s/d ${l.end_date}</div>
                            <div style="font-size: 11px; color: #475569; margin-top: 2px;"><i>"${l.reason}"</i></div>
                        </div>
                        <span class="badge ${l.status === 'approved' ? 'badge-success' : (l.status === 'rejected' ? 'badge-danger' : 'badge-warning')}">
                            ${l.status.toUpperCase()}
                        </span>
                    </div>
                `).join('');
            } else {
                list.innerHTML = '<div style="font-size: 12px; color: var(--text-muted); text-align: center; padding: 15px;">Belum ada pengajuan cuti.</div>';
            }
        } catch (e) {
            console.error(e);
        }
    }

    // 10. Overtime Form Actions
    function openOvertimeModal() {
        openModal('modalOvertime');
    }

    async function submitOvertimeForm(e) {
        e.preventDefault();
        const payload = {
            employee_id: appData.employee.id,
            date: document.getElementById('otDate').value,
            start_time: document.getElementById('otStartTime').value,
            end_time: document.getElementById('otEndTime').value,
            project_name: document.getElementById('otProjectName').value,
            description: document.getElementById('otDescription').value
        };

        try {
            const res = await fetch('/api/hris/overtimes', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify(payload)
            });
            const json = await res.json();
            if (json.success) {
                alert('✅ Pengajuan lembur berhasil dikirim.');
                closeModal('modalOvertime');
                loadOvertimes();
            } else {
                alert(`❌ ${json.message}`);
            }
        } catch (err) {
            console.error(err);
        }
    }

    async function loadOvertimes() {
        const list = document.getElementById('overtimesHistoryList');
        if (!list) return;

        try {
            const res = await fetch('/api/hris/overtimes');
            const json = await res.json();
            if (json.success && json.overtimes.length > 0) {
                list.innerHTML = json.overtimes.map(ot => `
                    <div class="history-item">
                        <div>
                            <div style="font-size: 13px; font-weight: 700; color: #0f172a;">${ot.project_name || 'Lembur Project'}</div>
                            <div style="font-size: 11px; color: #64748b;">${ot.date} • ${ot.start_time} - ${ot.end_time} (${ot.duration_hours} Jam)</div>
                            <div style="font-size: 11px; color: #16a34a; font-weight: 700; margin-top: 2px;">Est: Rp ${(ot.estimated_pay || 0).toLocaleString('id-ID')}</div>
                        </div>
                        <span class="badge ${ot.status === 'approved' ? 'badge-success' : (ot.status === 'rejected' ? 'badge-danger' : 'badge-warning')}">
                            ${ot.status.toUpperCase()}
                        </span>
                    </div>
                `).join('');
            } else {
                list.innerHTML = '<div style="font-size: 12px; color: var(--text-muted); text-align: center; padding: 15px;">Belum ada pengajuan lembur.</div>';
            }
        } catch (e) {
            console.error(e);
        }
    }

    // 11. Payrolls
    async function loadPayrolls() {
        const list = document.getElementById('payrollsList');
        if (!list) return;

        try {
            const res = await fetch('/api/hris/payrolls');
            const json = await res.json();
            if (json.success && json.payrolls.length > 0) {
                list.innerHTML = json.payrolls.map(p => `
                    <div class="history-item">
                        <div>
                            <div style="font-size: 13px; font-weight: 700; color: #0f172a;">Slip Gaji Periode: ${p.period_month}</div>
                            <div style="font-size: 12px; color: #16a34a; font-weight: 800; margin-top: 2px;">
                                Total: Rp ${(p.net_salary || 0).toLocaleString('id-ID')}
                            </div>
                            <div style="font-size: 11px; color: #64748b;">Masuk: ${p.total_present} Hari | Lembur: ${p.total_overtime_hours} Jam</div>
                        </div>
                        <a href="/hris/payslip/${p.id}/print" target="_blank" style="background: #eef2ff; color: #4338ca; text-decoration: none; padding: 8px 12px; border-radius: 10px; font-size: 11px; font-weight: 700; display: flex; align-items: center; gap: 4px;">
                            <i class="fa-solid fa-file-pdf"></i> Lihat PDF
                        </a>
                    </div>
                `).join('');
            } else {
                list.innerHTML = '<div style="font-size: 12px; color: var(--text-muted); text-align: center; padding: 15px;">Belum ada slip gaji bulan ini. Klik "Hitung Gaji" di menu Admin.</div>';
            }
        } catch (e) {
            console.error(e);
        }
    }

    // 12. Recent 7 Days History
    async function loadRecentHistory() {
        const list = document.getElementById('homeRecentAttendanceList');
        if (!list) return;

        try {
            const res = await fetch('/api/hris/attendance/history');
            const json = await res.json();
            if (json.success && json.attendances.length > 0) {
                list.innerHTML = json.attendances.slice(0, 5).map(a => `
                    <div class="history-item">
                        <div>
                            <div style="font-size: 12px; font-weight: 700; color: #0f172a;">${a.date}</div>
                            <div style="font-size: 11px; color: #64748b;">
                                Masuk: ${a.clock_in ? new Date(a.clock_in).toLocaleTimeString('id-ID', {hour:'2-digit', minute:'2-digit'}) : '-'} | 
                                Pulang: ${a.clock_out ? new Date(a.clock_out).toLocaleTimeString('id-ID', {hour:'2-digit', minute:'2-digit'}) : '-'}
                            </div>
                        </div>
                        <span class="badge ${a.status === 'hadir' ? 'badge-success' : (a.status === 'terlambat' ? 'badge-danger' : 'badge-warning')}">
                            ${a.status.toUpperCase()}
                        </span>
                    </div>
                `).join('');
            } else {
                list.innerHTML = '<div style="font-size: 12px; color: var(--text-muted); text-align: center; padding: 10px;">Belum ada riwayat absensi.</div>';
            }
        } catch (e) {
            console.error(e);
        }
    }

    // 13. Admin Management Portal Actions
    async function loadAdminData() {
        try {
            const res = await fetch('/api/hris/admin/stats');
            const json = await res.json();
            if (json.success) {
                const s = json.stats;
                document.getElementById('statPresent').innerText = s.present_today || 0;
                document.getElementById('statLate').innerText = s.late_today || 0;
                document.getElementById('statLeave').innerText = s.leave_today || 0;
                document.getElementById('statAbsent').innerText = s.absent_today || 0;
                document.getElementById('adminPendingCount').innerText = `${(s.pending_leaves + s.pending_overtimes)} Menunggu`;

                renderAdminTodayAttendances(json.today_attendances || []);
                loadAdminEmployees();
                loadAdminApprovals();
                populateSettingsForm();
            }
        } catch (e) {
            console.error(e);
        }
    }

    function renderAdminTodayAttendances(items) {
        const list = document.getElementById('adminTodayAttendancesList');
        if (!list) return;

        if (items.length === 0) {
            list.innerHTML = '<div style="font-size: 12px; color: var(--text-muted); text-align: center; padding: 10px;">Belum ada karyawan yang absen hari ini.</div>';
            return;
        }

        list.innerHTML = items.map(a => `
            <div class="history-item">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <img src="${a.clock_in_photo || (a.employee && a.employee.avatar_url) || 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=150'}" style="width: 36px; height: 36px; border-radius: 8px; object-fit: cover;">
                    <div>
                        <div style="font-size: 12px; font-weight: 700; color: #0f172a;">${a.employee ? a.employee.name : 'Karyawan'}</div>
                        <div style="font-size: 11px; color: #64748b;">
                            Masuk: <b>${a.clock_in ? new Date(a.clock_in).toLocaleTimeString('id-ID', {hour:'2-digit', minute:'2-digit'}) : '-'}</b>
                            • Jarak: ${a.distance_meters || 0}m
                        </div>
                    </div>
                </div>
                <span class="badge ${a.status === 'hadir' ? 'badge-success' : 'badge-danger'}">${a.status.toUpperCase()}</span>
            </div>
        `).join('');
    }

    async function loadAdminApprovals() {
        const list = document.getElementById('adminApprovalList');
        if (!list) return;

        try {
            const [resLeaves, resOvertimes] = await Promise.all([
                fetch('/api/hris/leaves?all=1'),
                fetch('/api/hris/overtimes?all=1')
            ]);
            const jsonLeaves = await resLeaves.json();
            const jsonOvertimes = await resOvertimes.json();

            const pendingLeaves = (jsonLeaves.leaves || []).filter(l => l.status === 'pending');
            const pendingOvertimes = (jsonOvertimes.overtimes || []).filter(ot => ot.status === 'pending');

            if (pendingLeaves.length === 0 && pendingOvertimes.length === 0) {
                list.innerHTML = '<div style="font-size: 12px; color: var(--text-muted); text-align: center; padding: 10px;">Semua pengajuan telah diproses.</div>';
                return;
            }

            let html = '';
            pendingLeaves.forEach(l => {
                html += `
                    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 10px; margin-bottom: 8px;">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <span class="badge badge-warning" style="font-size: 9px;">CUTI</span>
                                <b style="font-size: 12px; margin-left: 4px;">${l.employee ? l.employee.name : 'Karyawan'}</b>
                            </div>
                            <span style="font-size: 11px; color: #64748b;">${l.total_days} Hari (${l.start_date})</span>
                        </div>
                        <div style="font-size: 11px; color: #475569; margin: 4px 0;">Alasan: <i>"${l.reason}"</i></div>
                        <div style="display: flex; gap: 6px; margin-top: 8px;">
                            <button onclick="actionApproval('leave', ${l.id}, 'approved')" style="flex: 1; background: #16a34a; color: #fff; border: none; padding: 6px; border-radius: 8px; font-size: 11px; font-weight: 700; cursor: pointer;">
                                <i class="fa-solid fa-check"></i> Setujui
                            </button>
                            <button onclick="actionApproval('leave', ${l.id}, 'rejected')" style="flex: 1; background: #dc2626; color: #fff; border: none; padding: 6px; border-radius: 8px; font-size: 11px; font-weight: 700; cursor: pointer;">
                                <i class="fa-solid fa-xmark"></i> Tolak
                            </button>
                        </div>
                    </div>
                `;
            });

            pendingOvertimes.forEach(ot => {
                html += `
                    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 10px; margin-bottom: 8px;">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <span class="badge badge-info" style="font-size: 9px;">LEMBUR</span>
                                <b style="font-size: 12px; margin-left: 4px;">${ot.employee ? ot.employee.name : 'Karyawan'}</b>
                            </div>
                            <span style="font-size: 11px; color: #16a34a; font-weight: 700;">${ot.duration_hours} Jam (Rp ${(ot.estimated_pay||0).toLocaleString('id-ID')})</span>
                        </div>
                        <div style="font-size: 11px; color: #475569; margin: 4px 0;">Project: <b>${ot.project_name || '-'}</b> (${ot.date})</div>
                        <div style="display: flex; gap: 6px; margin-top: 8px;">
                            <button onclick="actionApproval('overtime', ${ot.id}, 'approved')" style="flex: 1; background: #16a34a; color: #fff; border: none; padding: 6px; border-radius: 8px; font-size: 11px; font-weight: 700; cursor: pointer;">
                                <i class="fa-solid fa-check"></i> Setujui
                            </button>
                            <button onclick="actionApproval('overtime', ${ot.id}, 'rejected')" style="flex: 1; background: #dc2626; color: #fff; border: none; padding: 6px; border-radius: 8px; font-size: 11px; font-weight: 700; cursor: pointer;">
                                <i class="fa-solid fa-xmark"></i> Tolak
                            </button>
                        </div>
                    </div>
                `;
            });

            list.innerHTML = html;
        } catch (e) {
            console.error(e);
        }
    }

    async function actionApproval(type, id, action) {
        const endpoint = type === 'leave' ? `/api/hris/admin/leaves/${id}/action` : `/api/hris/admin/overtimes/${id}/action`;
        try {
            const res = await fetch(endpoint, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ action: action })
            });
            const json = await res.json();
            if (json.success) {
                alert(`✅ Pengajuan berhasil ${action === 'approved' ? 'disetujui' : 'ditolak'}.`);
                loadAdminData();
            }
        } catch (e) {
            console.error(e);
        }
    }

    // 14. Admin Employees List & CRUD
    function loadAdminEmployees() {
        const list = document.getElementById('adminEmployeesList');
        if (!list) return;

        list.innerHTML = appData.employees.map(emp => `
            <div class="history-item">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <img src="${emp.avatar_url || 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=150'}" style="width: 38px; height: 38px; border-radius: 50%; object-fit: cover;">
                    <div>
                        <div style="font-size: 13px; font-weight: 700; color: #0f172a;">${emp.name}</div>
                        <div style="font-size: 11px; color: #64748b;">${emp.employee_code} • ${emp.division}</div>
                    </div>
                </div>
                <div style="display: flex; align-items: center; gap: 6px;">
                    <button onclick='editEmployee(${JSON.stringify(emp)})' style="background: #eef2ff; color: #4338ca; border: none; width: 28px; height: 28px; border-radius: 6px; cursor: pointer;">
                        <i class="fa-solid fa-pen"></i>
                    </button>
                    <button onclick="deleteEmployee(${emp.id})" style="background: #fee2e2; color: #dc2626; border: none; width: 28px; height: 28px; border-radius: 6px; cursor: pointer;">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </div>
            </div>
        `).join('');
    }

    function openEmployeeModal() {
        document.getElementById('modalEmployeeTitle').innerText = 'Tambah Karyawan Baru';
        document.getElementById('empId').value = '';
        document.getElementById('empName').value = '';
        document.getElementById('empCode').value = '';
        document.getElementById('empPosition').value = '';
        document.getElementById('empBaseSalary').value = 3500000;
        document.getElementById('empAllowance').value = 500000;
        document.getElementById('empPhone').value = '';
        openModal('modalEmployee');
    }

    function editEmployee(emp) {
        document.getElementById('modalEmployeeTitle').innerText = 'Edit Data Karyawan';
        document.getElementById('empId').value = emp.id;
        document.getElementById('empName').value = emp.name;
        document.getElementById('empCode').value = emp.employee_code;
        document.getElementById('empDivision').value = emp.division;
        document.getElementById('empPosition').value = emp.position;
        document.getElementById('empRole').value = emp.role;
        document.getElementById('empBaseSalary').value = emp.base_salary;
        document.getElementById('empAllowance').value = emp.allowance;
        document.getElementById('empPhone').value = emp.phone || '';
        openModal('modalEmployee');
    }

    async function saveEmployeeForm(e) {
        e.preventDefault();
        const payload = {
            id: document.getElementById('empId').value || null,
            name: document.getElementById('empName').value,
            employee_code: document.getElementById('empCode').value,
            division: document.getElementById('empDivision').value,
            position: document.getElementById('empPosition').value,
            role: document.getElementById('empRole').value,
            base_salary: document.getElementById('empBaseSalary').value,
            allowance: document.getElementById('empAllowance').value,
            phone: document.getElementById('empPhone').value
        };

        try {
            const res = await fetch('/api/hris/admin/employees', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify(payload)
            });
            const json = await res.json();
            if (json.success) {
                alert('✅ Data karyawan berhasil disimpan.');
                closeModal('modalEmployee');
                loadInitialData();
            }
        } catch (err) {
            console.error(err);
        }
    }

    async function deleteEmployee(id) {
        if (!confirm('Yakin ingin menghapus data karyawan ini?')) return;
        try {
            const res = await fetch(`/api/hris/admin/employees/${id}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            });
            const json = await res.json();
            if (json.success) {
                loadInitialData();
            }
        } catch (e) {
            console.error(e);
        }
    }

    // 15. Studio Settings Form & GPS Setter
    function populateSettingsForm() {
        const s = appData.settings;
        if (!s) return;
        document.getElementById('setStudioName').value = s.studio_name || '';
        document.getElementById('setStudioAddress').value = s.studio_address || '';
        document.getElementById('setStudioLat').value = s.studio_lat || -6.2607187;
        document.getElementById('setStudioLng').value = s.studio_lng || 106.8166667;
        document.getElementById('setRadiusMeters').value = s.radius_meters || 100;
        document.getElementById('setLateTolerance').value = s.late_tolerance_minutes || 15;
        document.getElementById('setWorkStart').value = s.work_start_time || '08:30';
        document.getElementById('setWorkEnd').value = s.work_end_time || '17:30';
        document.getElementById('setOvertimeRate').value = s.overtime_rate_per_hour || 25000;
    }

    function setStudioCoordsFromCurrentGps() {
        if (!appData.currentLat || !appData.currentLng) {
            alert('Mendeteksi GPS...');
            detectLocation(true);
            return;
        }
        document.getElementById('setStudioLat').value = appData.currentLat;
        document.getElementById('setStudioLng').value = appData.currentLng;
        alert(`Koordinat Studio berhasil diisi dengan lokasi GPS Anda saat ini:\nLat: ${appData.currentLat}, Lng: ${appData.currentLng}`);
    }

    async function saveHrisSettings(e) {
        e.preventDefault();
        const payload = {
            studio_name: document.getElementById('setStudioName').value,
            studio_address: document.getElementById('setStudioAddress').value,
            studio_lat: document.getElementById('setStudioLat').value,
            studio_lng: document.getElementById('setStudioLng').value,
            radius_meters: document.getElementById('setRadiusMeters').value,
            late_tolerance_minutes: document.getElementById('setLateTolerance').value,
            work_start_time: document.getElementById('setWorkStart').value,
            work_end_time: document.getElementById('setWorkEnd').value,
            overtime_rate_per_hour: document.getElementById('setOvertimeRate').value
        };

        try {
            const res = await fetch('/api/hris/admin/settings', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify(payload)
            });
            const json = await res.json();
            if (json.success) {
                alert('✅ Pengaturan studio & geofence berhasil disimpan!');
                loadInitialData();
            }
        } catch (err) {
            console.error(err);
        }
    }

    async function triggerGeneratePayroll() {
        const month = prompt('Masukkan periode bulan slip gaji (YYYY-MM):', new Date().toISOString().slice(0, 7));
        if (!month) return;

        try {
            const res = await fetch('/api/hris/admin/payrolls/generate', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ month: month })
            });
            const json = await res.json();
            if (json.success) {
                alert(`✅ ${json.message}`);
                loadPayrolls();
            }
        } catch (e) {
            console.error(e);
        }
    }
</script>

</body>
</html>
