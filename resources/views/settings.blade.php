<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Settings & Profile - CloseMateAI</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --sidebar-bg: #1e1e2d;
            --sidebar-text: #a1a5b7;
            --sidebar-active-bg: rgba(107, 92, 216, 0.2);
            --sidebar-active-text: #6b5cd8;
            --primary: #6b5cd8;
            --primary-hover: #5a4bcf;
            --bg-light: #f8f9fa;
            --card-bg: #ffffff;
            --border-color: #f1f1f4;
            --text-dark: #181c32;
            --text-muted: #a1a5b7;
            --success: #50cd89;
            --danger: #f1416c;
            --warning: #ffc700;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }
        body { background-color: var(--bg-light); display: flex; min-height: 100vh; color: var(--text-dark); }

        /* Sidebar */
        .sidebar { width: 260px; background-color: var(--sidebar-bg); display: flex; flex-direction: column; height: 100vh; position: fixed; left: 0; top: 0; transition: width 0.3s ease; overflow-x: hidden; z-index: 10; }
        .sidebar-header { padding: 24px; display: flex; align-items: center; gap: 12px; transition: 0.3s ease; }
        .sidebar-logo-icon { min-width: 40px; width: 40px; height: 40px; background-color: rgba(255,255,255,0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 18px; }
        .sidebar-title { white-space: nowrap; }
        .sidebar-title h2 { color: white; font-size: 16px; font-weight: 600; margin-bottom: 4px; }
        .sidebar-title p { color: var(--sidebar-text); font-size: 12px; }
        
        .nav-menu { flex: 1; overflow-y: auto; padding: 12px; }
        .nav-item { display: flex; align-items: center; gap: 12px; padding: 12px 16px; color: var(--sidebar-text); text-decoration: none; border-radius: 8px; font-size: 14px; font-weight: 500; margin-bottom: 4px; transition: 0.2s; white-space: nowrap; cursor: pointer; }
        .nav-item:hover { color: white; background-color: rgba(255,255,255,0.05); }
        .nav-item.active { background-color: var(--sidebar-active-bg); color: var(--sidebar-active-text); }
        .nav-item i { font-size: 16px; width: 20px; text-align: center; }

        /* Sidebar Collapsed State */
        body.sidebar-collapsed .sidebar { width: 80px; }
        body.sidebar-collapsed .main-content { margin-left: 80px; }
        body.sidebar-collapsed .sidebar-title { display: none; }
        body.sidebar-collapsed .sidebar-header { justify-content: center; padding: 24px 0; }
        body.sidebar-collapsed .nav-item span { display: none; }
        body.sidebar-collapsed .nav-item { justify-content: center; padding-left: 0; padding-right: 0; }
        body.sidebar-collapsed #collapse-icon { transform: rotate(180deg); }

        /* Main Content */
        .main-content { margin-left: 260px; flex: 1; padding: 30px; display: flex; flex-direction: column; transition: margin-left 0.3s ease; position: relative; }
        .topbar { display: flex; justify-content: space-between; align-items: center; margin: -30px -30px 30px -30px; padding: 16px 30px; background-color: white; border-bottom: 1px solid var(--border-color); }
        .page-title h1 { font-size: 20px; font-weight: 600; margin-bottom: 6px; }
        .page-title p { font-size: 14px; color: var(--text-muted); }
        
        .top-actions { display: flex; align-items: center; gap: 20px; }
        .status-pill { display: flex; align-items: center; gap: 8px; background-color: rgba(80, 205, 137, 0.1); color: var(--success); padding: 8px 16px; border-radius: 20px; font-size: 13px; font-weight: 600; border: 1px solid rgba(80, 205, 137, 0.2); }
        .status-dot { width: 8px; height: 8px; background-color: var(--success); border-radius: 50%; }
        
        .profile-wrapper { position: relative; cursor: pointer; }
        .user-profile { display: flex; align-items: center; gap: 12px; padding: 6px 16px 6px 6px; border-radius: 30px; transition: background-color 0.2s; }
        .user-profile:hover { background-color: rgba(107, 92, 216, 0.08); }
        .avatar { width: 36px; height: 36px; background-color: var(--primary); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 14px; }
        .user-info h4 { font-size: 14px; font-weight: 600; }
        .user-info p { font-size: 12px; color: var(--text-muted); }

        /* Settings Grid Layout */
        .settings-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 24px; align-items: start; }

        /* Card Styles */
        .card { background: white; border: 1px solid var(--border-color); border-radius: 12px; padding: 24px; box-shadow: 0 2px 10px rgba(0,0,0,0.02); margin-bottom: 24px; }
        
        .card-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px; }
        .card-header-left { display: flex; gap: 16px; }
        .header-icon { width: 40px; height: 40px; border-radius: 10px; background: rgba(107, 92, 216, 0.1); color: var(--primary); display: flex; justify-content: center; align-items: center; font-size: 18px; }
        .header-title h3 { font-size: 16px; font-weight: 600; color: var(--text-dark); margin-bottom: 4px; }
        .header-title p { font-size: 13px; color: var(--text-muted); }

        /* Badges */
        .badge { padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 500; display: inline-flex; align-items: center; gap: 6px; }
        .badge-warning { background: #fff8dd; color: #d97706; border: 1px solid #fde68a; }
        
        /* Progress Steps */
        .progress-steps { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; position: relative; }
        .progress-line { position: absolute; top: 14px; left: 0; right: 0; height: 4px; background: #e4e6ef; border-radius: 2px; z-index: 1; }
        .progress-line-active { position: absolute; top: 14px; left: 0; width: 33%; height: 4px; background: var(--primary); border-radius: 2px; z-index: 2; transition: 0.3s; }
        
        .step { display: flex; flex-direction: column; align-items: center; gap: 8px; z-index: 3; position: relative; background: white; padding: 0 10px; }
        .step-icon { width: 32px; height: 32px; border-radius: 50%; display: flex; justify-content: center; align-items: center; font-size: 14px; border: 2px solid #e4e6ef; color: var(--text-muted); background: white; transition: 0.3s; }
        .step.active .step-icon { border-color: var(--primary); color: var(--primary); }
        .step.completed .step-icon { border-color: var(--success); color: var(--success); }
        .step-text { font-size: 12px; font-weight: 500; color: var(--text-muted); text-align: center; }
        .step.active .step-text { color: var(--text-dark); font-weight: 600; }

        /* Alerts */
        .alert-warning { background: #fff8dd; border: 1px solid #fde68a; color: #b45309; padding: 16px; border-radius: 8px; display: flex; align-items: center; gap: 12px; font-size: 14px; margin-bottom: 20px; transition: 0.3s; }
        
        /* Banner Box */
        .banner-box { background: #fafafa; border: 1px solid var(--border-color); border-radius: 8px; padding: 16px; display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
        .banner-text h4 { font-size: 14px; font-weight: 600; color: var(--text-dark); margin-bottom: 4px; }
        .banner-text p { font-size: 13px; color: var(--text-muted); }

        /* Forms */
        .form-group { margin-bottom: 16px; }
        .form-label { display: block; font-size: 13px; font-weight: 500; color: var(--text-dark); margin-bottom: 8px; }
        .form-control { width: 100%; padding: 10px 14px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px; color: var(--text-dark); outline: none; transition: 0.2s; background: white; }
        .form-control:focus { border-color: var(--primary); }
        .form-text { font-size: 12px; color: var(--text-muted); margin-top: 6px; }

        .input-group { display: flex; gap: 12px; }
        .input-group .form-control { flex: 1; }

        /* Buttons */
        .btn { padding: 10px 20px; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; transition: 0.2s; border: none; display: inline-flex; align-items: center; gap: 8px; justify-content: center; }
        .btn-primary { background-color: var(--primary); color: white; }
        .btn-primary:hover { background-color: var(--primary-hover); }
        .btn-outline { background-color: white; border: 1px solid var(--border-color); color: var(--text-dark); }
        .btn-outline:hover { background-color: var(--bg-light); }
        .btn-light { background-color: #f1f1f4; color: var(--text-muted); }
        .btn-light:hover { background-color: #e4e6ef; color: var(--text-dark); }

        /* QR Section */
        .qr-section { margin-top: 16px; display: flex; flex-direction: column; align-items: center; gap: 16px; }
        .qr-placeholder { width: 240px; height: 240px; background: #fafafa; border: 1px dashed #d1d5db; border-radius: 12px; display: flex; justify-content: center; align-items: center; flex-direction: column; gap: 12px; color: var(--text-muted); font-size: 13px; }
        .spinner { width: 24px; height: 24px; border: 3px solid #e4e6ef; border-top: 3px solid var(--primary); border-radius: 50%; animation: spin 1s linear infinite; }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }

        .instruction-list { font-size: 13px; color: var(--text-muted); line-height: 1.6; text-align: left; max-width: 400px; margin: 0 auto; }
        .loading-text { display: flex; align-items: center; gap: 8px; font-size: 12px; color: var(--text-muted); margin-top: 8px; }

        /* Accordion */
        .accordion { border: 1px solid var(--border-color); border-radius: 8px; overflow: hidden; margin-top: 24px; }
        .accordion-header { padding: 16px; background: #fafafa; display: flex; justify-content: space-between; align-items: center; font-size: 14px; font-weight: 600; cursor: pointer; color: var(--text-dark); }
        .accordion-item { border-top: 1px solid var(--border-color); }
        .accordion-item-header { padding: 16px; display: flex; justify-content: space-between; align-items: center; font-size: 13px; font-weight: 500; cursor: pointer; color: var(--text-dark); background: white; transition: 0.2s; }
        .accordion-item-header:hover { background: #fafafa; }
        .accordion-item-header i { color: var(--text-muted); transition: 0.3s; }

        /* Form Grid */
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px; }

        /* Toggle Switch */
        .toggle-row { display: flex; justify-content: space-between; align-items: center; padding: 16px 0; border-bottom: 1px solid var(--border-color); }
        .toggle-row:last-of-type { border-bottom: none; margin-bottom: 16px; }
        .toggle-info h4 { font-size: 14px; font-weight: 600; color: var(--text-dark); margin-bottom: 4px; }
        .toggle-info p { font-size: 12px; color: var(--text-muted); }
        
        .switch { position: relative; display: inline-block; width: 44px; height: 24px; }
        .switch input { opacity: 0; width: 0; height: 0; }
        .slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #ccc; transition: .4s; border-radius: 24px; }
        .slider:before { position: absolute; content: ""; height: 18px; width: 18px; left: 3px; bottom: 3px; background-color: white; transition: .4s; border-radius: 50%; box-shadow: 0 2px 4px rgba(0,0,0,0.2); }
        input:checked + .slider { background-color: var(--primary); }
        input:checked + .slider:before { transform: translateX(20px); }

        /* Toast Alert */
        .toast { position: fixed; top: 80px; right: 24px; background: white; border: 1px solid var(--border-color); border-radius: 8px; padding: 16px 20px; display: flex; align-items: center; gap: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); z-index: 1000; font-size: 14px; font-weight: 500; color: var(--text-dark); transition: 0.3s; }
        .toast i { font-size: 16px; }

        /* Responsive Mobile Layout */
        @media (max-width: 768px) {
            .toast { top: 20px; right: 16px; left: 16px; justify-content: center; }
            .sidebar {
                width: 100% !important;
                height: 64px !important;
                position: fixed;
                top: 72px; /* Height of topbar */
                left: 0;
                z-index: 90;
                background: white;
                border-bottom: 1px solid var(--border-color);
                box-shadow: 0 4px 12px rgba(0,0,0,0.03);
            }
            .sidebar-header { display: none; }
            
            .nav-menu {
                display: flex;
                flex-direction: row;
                overflow-x: auto;
                padding: 10px 16px;
                gap: 8px;
                -webkit-overflow-scrolling: touch;
                height: 64px;
                align-items: center;
            }
            .nav-menu::-webkit-scrollbar { 
                height: 4px; /* Scrollbar horizontal yang tipis */
                display: block;
            }
            .nav-menu::-webkit-scrollbar-track {
                background: transparent;
            }
            .nav-menu::-webkit-scrollbar-thumb {
                background: #d1d5db;
                border-radius: 4px;
            }
            .nav-menu::-webkit-scrollbar-thumb:hover {
                background: #9ca3af;
            }
            
            .nav-item {
                padding: 8px 16px;
                background: transparent;
                color: var(--text-muted);
                border-radius: 30px;
                margin: 0;
                font-size: 13px;
                font-weight: 600;
                white-space: nowrap;
            }
            .nav-item.active {
                background: var(--primary);
                color: white;
            }
            .nav-item:hover { color: var(--primary); background: transparent; }
            #btn-collapse, .nav-menu div[style*="margin-top: 30px"] { display: none !important; }
            
            .main-content {
                margin-left: 0 !important;
                padding: 160px 16px 24px 16px !important; /* Force padding */
            }
            
            .topbar {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                height: 72px;
                margin: 0 !important;
                padding: 0 16px !important;
                z-index: 100;
                display: flex;
                align-items: center;
                justify-content: space-between;
                background: white;
                border-bottom: 1px solid var(--border-color);
            }
            
            .page-title h1 { font-size: 16px; margin-bottom: 2px; }
            .page-title p { font-size: 11px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 150px; }
            .status-pill { padding: 4px 10px; font-size: 10px; }
            .user-info { display: none; } /* Hide user text, show only avatar */
            .avatar { width: 32px; height: 32px; font-size: 12px; }
            .top-actions { gap: 10px; }
            
            /* Grids */
            .settings-grid, .form-grid, .grid-stats, .grid-main, .mini-stats-grid { 
                grid-template-columns: 1fr !important; 
                gap: 16px; 
            }
            
            .card, .panel { padding: 16px; margin-bottom: 16px; }
            .card-header, .panel-header { flex-direction: column; gap: 12px; align-items: flex-start; margin-bottom: 16px; }
            .progress-steps { flex-direction: column; align-items: flex-start; gap: 16px; }
            .progress-line, .progress-line-active { display: none; }
            .step { flex-direction: row; text-align: left; padding: 0; }
            .step-text { text-align: left; }
            .qr-placeholder { width: 100%; max-width: 240px; }
            .alert-box { flex-direction: column; align-items: flex-start; gap: 12px; }
        }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-logo-icon">
                <i class="fa-solid fa-heart"></i>
            </div>
            <div class="sidebar-title">
                <h2>{{ auth()->check() ? auth()->user()->business_name : 'CloseMateAI' }}</h2>
                <p>AI Bot Console</p>
            </div>
        </div>
        
        <div class="nav-menu">
            <a class="nav-item"><i class="fa-solid fa-circle-play"></i> <span>Video Tutorial</span></a>
            <a href="/dashboard" class="nav-item {{ request()->is('dashboard') ? 'active' : '' }}"><i class="fa-solid fa-border-all"></i> <span>Dashboard & WA Status</span></a>
            <a href="/chat" class="nav-item {{ request()->is('chat') ? 'active' : '' }}"><i class="fa-solid fa-message"></i> <span>Live Chat Inbox</span></a>
            <a href="/knowledge" class="nav-item {{ request()->is('knowledge') ? 'active' : '' }}"><i class="fa-solid fa-book"></i> <span>Knowledge Base</span></a>
            <a href="/leads" class="nav-item {{ request()->is('leads') ? 'active' : '' }}"><i class="fa-solid fa-users"></i> <span>Leads CRM</span></a>
            <a href="/followup" class="nav-item {{ request()->is('followup') ? 'active' : '' }}"><i class="fa-solid fa-clock-rotate-left"></i> <span>Follow-Up Otomatis</span></a>
            <a href="/booking" class="nav-item {{ request()->is('booking') ? 'active' : '' }}"><i class="fa-regular fa-calendar-check"></i> <span>Booking & Operasional</span></a>
            <a href="/invoice" class="nav-item {{ request()->is('invoice') ? 'active' : '' }}"><i class="fa-solid fa-file-invoice"></i> <span>Invoice Generator</span></a>
            <a href="/quotations" class="nav-item {{ request()->is('quotations') ? 'active' : '' }}"><i class="fa-solid fa-file-contract"></i> <span>Quotation Generator</span></a>
            <a href="/finance" class="nav-item {{ request()->is('finance') ? 'active' : '' }}"><i class="fa-solid fa-chart-pie"></i> <span>Laporan Keuangan</span></a>
            <a href="/production" class="nav-item {{ request()->is('production') ? 'active' : '' }}"><i class="fa-solid fa-list-check"></i> <span>Progres Produksi</span></a>
            <a class="nav-item"><i class="fa-solid fa-user-group"></i> <span>Manajemen Tim</span></a>
            <a href="/activity" class="nav-item {{ request()->is('activity') ? 'active' : '' }}"><i class="fa-solid fa-clock-rotate-left"></i> <span>Riwayat Aktivitas</span></a>
            <a class="nav-item"><i class="fa-solid fa-credit-card"></i> <span>Langganan & Pembayaran</span></a>
            
            <div style="margin-top: 30px;"></div>
            
            <a href="/settings" class="nav-item {{ request()->is('settings') ? 'active' : '' }}"><i class="fa-solid fa-gear"></i> <span>Settings & Profile</span></a>
            <a class="nav-item" id="btn-collapse" style="margin-top: auto; opacity: 0.7;">
                <i class="fa-solid fa-angles-left" id="collapse-icon" style="transition: transform 0.3s;"></i> <span>Collapse</span>
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="topbar">
            <div class="page-title">
                <h1>Settings & Profile</h1>
                <p>Kelola akun dan preferensi vendor kamu</p>
            </div>
            
            <div class="top-actions">
                @php
                    $currentUser = auth()->user() ?? \App\Models\User::first();
                    $isWaConnected = $currentUser && $currentUser->wa_status == 'connected' ? true : false;
                @endphp
                @if($isWaConnected)
                <div class="status-pill">
                    <div class="status-dot"></div>
                    WhatsApp Connected
                </div>
                @else
                <div class="status-pill" style="background-color: #f1f1f4; color: var(--text-muted); border-color: #e4e6ef;">
                    <div class="status-dot" style="background-color: var(--text-muted);"></div>
                    WhatsApp Disconnected
                </div>
                @endif
                
                <div class="profile-wrapper">
                    <div class="user-profile">
                        <div class="avatar">
                            P
                        </div>
                        <div class="user-info">
                            <h4>Penapict <i class="fa-solid fa-chevron-down" style="font-size: 10px; color: #a1a5b7; margin-left: 4px;"></i></h4>
                            <p>Starter</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @php
            $hasToken = isset($user) && $user->fonnte_token ? true : false;
            $isConnected = isset($user) && $user->wa_status == 'connected' ? true : false;
        @endphp

        <div class="settings-grid">
            
            <!-- Left Column -->
            <div class="col-left">
                
                <!-- Onboarding WhatsApp Card -->
                <div class="card">
                    <div class="card-header">
                        <div class="card-header-left">
                            <div class="header-icon"><i class="fa-solid fa-qrcode"></i></div>
                            <div class="header-title">
                                <h3>Onboarding WhatsApp</h3>
                                <p>Tiga langkah singkat lewat Fonnte. Setelah terhubung, pesan masuk otomatis dibalas AI.</p>
                            </div>
                        </div>
                        @if(!$hasToken)
                            <div class="badge badge-warning"><i class="fa-solid fa-circle" style="font-size: 8px;"></i> Belum diatur</div>
                        @elseif(!$isConnected)
                            <div class="badge badge-warning" style="background:#fff4de; color:#d97706; border-color:#fde68a;"><i class="fa-solid fa-circle" style="font-size: 8px;"></i> Menunggu scan</div>
                        @else
                            <div class="badge" style="background:rgba(80, 205, 137, 0.1); color:var(--success); border:1px solid rgba(80, 205, 137, 0.2);"><i class="fa-solid fa-circle" style="font-size: 8px;"></i> Connected</div>
                        @endif
                    </div>

                    <div class="progress-steps">
                        <div class="progress-line"></div>
                        <div class="progress-line-active" style="width: {{ $isConnected ? '100%' : ($hasToken ? '50%' : '33%') }}; {{ $isConnected ? 'background-color: var(--success);' : '' }}"></div>
                        
                        <div class="step {{ $hasToken ? 'completed' : 'active' }}">
                            <div class="step-icon"><i class="fa-solid fa-key"></i></div>
                            <div class="step-text">Langkah 1<br><span style="font-weight: 400; color: var(--text-muted);">Isi token Fonnte</span></div>
                        </div>
                        <div class="step {{ $isConnected ? 'completed' : ($hasToken ? 'active' : '') }}">
                            <div class="step-icon"><i class="fa-solid fa-mobile-screen"></i></div>
                            <div class="step-text">Langkah 2<br><span style="font-weight: 400; color: var(--text-muted);">Scan QR dari HP</span></div>
                        </div>
                        <div class="step {{ $isConnected ? 'completed' : '' }}">
                            <div class="step-icon"><i class="fa-regular fa-circle-check"></i></div>
                            <div class="step-text">Langkah 3<br><span style="font-weight: 400; color: var(--text-muted);">Status Connected</span></div>
                        </div>
                    </div>

                    @if(!$isConnected)
                        <div class="alert-warning" style="background: {{ $hasToken ? '#f1f1f4' : '#fff8dd' }}; color: {{ $hasToken ? 'var(--text-dark)' : '#b45309' }}; border-color: {{ $hasToken ? '#e4e6ef' : '#fde68a' }};">
                            @if(!$hasToken)
                                <i class="fa-solid fa-triangle-exclamation" style="font-size: 18px;"></i>
                                Belum terhubung — masukkan token perangkat Fonnte untuk menyiapkan sesi.
                            @else
                                <i class="fa-solid fa-triangle-exclamation" style="font-size: 18px;"></i>
                                Perangkat siap — klik "Tampilkan QR" lalu scan dari WhatsApp di HP.
                            @endif
                        </div>

                        <div class="banner-box">
                            <div class="banner-text">
                                <h4>Belum punya akun Fonnte?</h4>
                                <p>Daftar dulu secara gratis, tambahkan perangkat, lalu salin token-nya ke sini.</p>
                            </div>
                            <a href="https://md.fonnte.com/new/register.php" target="_blank" class="btn btn-primary" style="white-space: nowrap; text-decoration: none;"><i class="fa-solid fa-arrow-up-right-from-square"></i> Daftar di Fonnte</a>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Langkah 1 - Token Perangkat Fonnte</label>
                            <div class="input-group">
                                <input type="text" id="fonnte_token_input" class="form-control" value="{{ $user ? $user->fonnte_token : '' }}" placeholder="Tempel token perangkat dari dashboard Fonnte">
                                <button id="btn-save-token" class="btn btn-light" style="white-space: nowrap; background: #dcd9f6; color: var(--primary);">{{ $hasToken ? 'Perbarui Token' : 'Hubungkan WhatsApp' }}</button>
                            </div>
                            <div class="form-text">Ambil token di dashboard Fonnte -> Device -> Token. Token disimpan aman di server.</div>
                        </div>

                        @if($hasToken)
                            <div class="form-group" style="margin-top: 24px;">
                                <label class="form-label">Langkah 2 - Scan QR</label>
                                <div style="display: flex; gap: 12px;">
                                    <button id="btn-show-qr" class="btn btn-outline"><i class="fa-solid fa-qrcode"></i> Ambil QR Baru</button>
                                    <button id="btn-check-status" class="btn btn-outline"><i class="fa-solid fa-rotate-right"></i> Cek Status</button>
                                </div>
                                
                                <div class="qr-section" id="qr-section" style="display: none;">
                                    <div class="qr-placeholder" id="qr-placeholder">
                                        <div class="spinner"></div>
                                        <span>Menyiapkan QR...</span>
                                    </div>
                                    
                                    <div class="instruction-list">
                                        1. Buka WhatsApp di HP &rarr; Setelan &rarr; Perangkat Tertaut.<br>
                                        2. Pilih "Tautkan Perangkat" lalu arahkan kamera ke QR di atas.<br>
                                        3. Status dicek otomatis tiap 3 detik.
                                    </div>
                                    
                                    <div class="loading-text">
                                        <i class="fa-solid fa-rotate" style="animation: spin 2s linear infinite;"></i> Memantau status koneksi...
                                    </div>
                                </div>
                            </div>
                        @endif

                    @else
                        <!-- Connected State -->
                        <div class="alert-warning" style="background: rgba(80, 205, 137, 0.1); color: var(--success); border-color: rgba(80, 205, 137, 0.2); margin-bottom: 24px;">
                            <i class="fa-regular fa-circle-check" style="font-size: 18px;"></i>
                            Terhubung ke {{ $user->wa_number ?? 'WhatsApp' }}
                        </div>

                        <div class="form-group">
                            <label class="form-label">Webhook URL</label>
                            <div class="input-group">
                                <input type="text" class="form-control" readonly value="{{ url('/api/public/wa/' . ($user->webhook_secret ?? '')) }}" style="background: #fafafa; color: var(--text-dark);">
                                <button class="btn btn-outline" onclick="navigator.clipboard.writeText('{{ url('/api/public/wa/' . ($user->webhook_secret ?? '')) }}'); alert('Disalin!');" style="padding: 10px 14px;"><i class="fa-regular fa-copy"></i></button>
                            </div>
                            <div class="form-text">Tempel URL ini di dashboard Fonnte -> Device -> Webhook.</div>
                        </div>

                    @endif

                    <div class="accordion">
                        <div class="accordion-header">
                            <div><i class="fa-regular fa-life-ring"></i> Panduan & Troubleshooting <span style="color: var(--danger);">?</span></div>
                        </div>
                        <div class="accordion-item">
                            <div class="accordion-item-header">QR tidak muncul atau gagal dimuat <i class="fa-solid fa-chevron-down"></i></div>
                        </div>
                        <div class="accordion-item">
                            <div class="accordion-item-header">Sudah scan tapi status masih "Belum terhubung" <i class="fa-solid fa-chevron-down"></i></div>
                        </div>
                        <div class="accordion-item">
                            <div class="accordion-item-header">Pesan masuk tidak dibalas AI <i class="fa-solid fa-chevron-down"></i></div>
                        </div>
                        <div class="accordion-item">
                            <div class="accordion-item-header">Koneksi tiba-tiba terputus <i class="fa-solid fa-chevron-down"></i></div>
                        </div>
                        <div class="accordion-item">
                            <div class="accordion-item-header">Ingin ganti nomor WhatsApp <i class="fa-solid fa-chevron-down"></i></div>
                        </div>
                    </div>

                    @if($isConnected)
                        <div style="margin-top:24px; text-align:right;">
                            <button id="btn-disconnect" class="btn btn-outline" style="color: var(--danger); border-color: rgba(241, 65, 108, 0.2); background: rgba(241, 65, 108, 0.05);"><i class="fa-solid fa-link-slash"></i> Putuskan WhatsApp</button>
                        </div>
                    @endif
                </div>

                <!-- Business Profile Card -->
                <div class="card">
                    <div class="card-header" style="margin-bottom: 24px;">
                        <div class="card-header-left">
                            <div class="header-icon" style="background: rgba(107, 92, 216, 0.1);"><i class="fa-solid fa-building"></i></div>
                            <div class="header-title">
                                <h3>Business Profile</h3>
                                <p>Tampil pada balasan AI dan proposal</p>
                            </div>
                        </div>
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">Business Name</label>
                            <input type="text" class="form-control" value="Penapict">
                        </div>
                        <div class="form-group">
                            <label class="form-label">WhatsApp Number</label>
                            <input type="text" class="form-control">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Owner Name</label>
                            <input type="text" class="form-control">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" value="hotautomag@gmail.com">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Kategori Bisnis</label>
                        <select class="form-control">
                            <option>Pilih kategori bisnis</option>
                            <option>Fotografi / Videografi</option>
                            <option>Makeup Artist</option>
                            <option>Dekorasi</option>
                        </select>
                        <div class="form-text">Pilih "Dekorasi" untuk mengaktifkan Katalog Prop & Gudang, penawaran berfoto, dan tab Operasional Dekor.</div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Business Description</label>
                        <textarea class="form-control" rows="4" placeholder="Ceritakan singkat tentang bisnis kamu..."></textarea>
                    </div>

                    <button class="btn btn-primary"><i class="fa-regular fa-floppy-disk"></i> Save Profile</button>
                </div>

            </div>
            
            <!-- Right Column -->
            <div class="col-right">
                
                <div class="toast" id="toast-box" style="display: {{ !$hasToken ? 'flex' : 'none' }};">
                    <span id="toast-message">
                        <i class="fa-solid fa-circle-exclamation"></i>
                        Token Fonnte belum diatur
                    </span>
                </div>

                <!-- Notifications Card -->
                <div class="card">
                    <div class="card-header" style="margin-bottom: 24px;">
                        <div class="card-header-left">
                            <div class="header-icon" style="background: rgba(107, 92, 216, 0.1);"><i class="fa-regular fa-bell"></i></div>
                            <div class="header-title">
                                <h3>Notifications</h3>
                                <p>Dikirim ke WhatsApp lewat gateway yang terhubung.</p>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Nomor tujuan notifikasi</label>
                        <input type="text" class="form-control" value="628123456789">
                        <div class="form-text">Kosongkan untuk memakai nomor WhatsApp bisnis di atas.</div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Nomor WhatsApp Owner</label>
                        <input type="text" class="form-control" value="628xxxxxxxxx">
                        <div class="form-text">Dipakai saat AI butuh bantuan owner (human takeover). Format 628xxx.</div>
                    </div>

                    <div class="toggle-row" style="margin-top: 24px;">
                        <div class="toggle-info">
                            <h4>Hot lead baru</h4>
                            <p>Notif saat ada lead panas masuk</p>
                        </div>
                        <label class="switch">
                            <input type="checkbox" checked>
                            <span class="slider"></span>
                        </label>
                    </div>
                    
                    <div class="toggle-row">
                        <div class="toggle-info">
                            <h4>Permintaan human takeover</h4>
                            <p>Notif saat AI butuh bantuan owner</p>
                        </div>
                        <label class="switch">
                            <input type="checkbox" checked>
                            <span class="slider"></span>
                        </label>
                    </div>

                    <button class="btn btn-primary" style="width: 100%;"><i class="fa-regular fa-floppy-disk"></i> Simpan Notifikasi</button>
                </div>

                <!-- Billing Card -->
                <div class="card">
                    <div class="card-header" style="margin-bottom: 24px;">
                        <div class="card-header-left">
                            <div class="header-icon" style="background: rgba(107, 92, 216, 0.1);"><i class="fa-solid fa-credit-card"></i></div>
                            <div class="header-title">
                                <h3>Billing</h3>
                            </div>
                        </div>
                    </div>

                    <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 4px; color: var(--text-dark);">starter</h3>
                    <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 20px;">Rp125.000 / bulan atau Rp1.250.000 / tahun</p>
                    
                    <button class="btn btn-outline" style="width: 100%; margin-bottom: 24px;">Manage Subscription</button>
                    
                    <div style="display: flex; align-items: center; gap: 8px; font-size: 12px; color: var(--text-muted); justify-content: center;">
                        <i class="fa-solid fa-shield-halved"></i> Data percakapan terenkripsi end-to-end
                    </div>
                </div>

            </div>

        </div>

    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Fonnte Logic
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            if (localStorage.getItem('show_disconnect_toast') === 'true') {
                localStorage.removeItem('show_disconnect_toast');
                $('#toast-message').html('<i class="fa-solid fa-check-circle" style="color:var(--text-dark);"></i> WhatsApp diputuskan');
                $('#toast-box').css('display', 'flex').hide().fadeIn().delay(3000).fadeOut();
            }

            $('#btn-save-token').click(function() {
                let token = $('#fonnte_token_input').val();
                if (!token) return alert('Silakan masukkan token!');
                
                $(this).text('Menyimpan...').prop('disabled', true);
                
                $.post('/settings/token', { fonnte_token: token }, function(res) {
                    if (res.success) {
                        $('#toast-message').html('<i class="fa-solid fa-check-circle" style="color:var(--success);"></i> Token tersimpan - silakan scan QR');
                        $('#toast-box').css('display', 'flex').hide().fadeIn();
                        setTimeout(() => location.reload(), 1500);
                    }
                }).fail(function(err) {
                    alert('Gagal menyimpan token: ' + err.statusText);
                    $('#btn-save-token').text('Hubungkan WhatsApp').prop('disabled', false);
                });
            });

            let statusInterval = null;

            $('#btn-show-qr').click(function() {
                loadQR();
            });

            $('#btn-check-status').click(function() {
                let btn = $(this);
                let originalText = btn.html();
                btn.html('<i class="fa-solid fa-spinner fa-spin"></i> Mengecek...');
                
                $.get('/settings/device', function(res) {
                    btn.html(originalText);
                    if (res.success && res.data && res.data.device_status === 'connect') {
                        clearInterval(statusInterval);
                        location.reload();
                    } else {
                        $('#toast-message').html('<i class="fa-solid fa-circle-info" style="color:var(--text-dark);"></i> Belum terhubung — pastikan QR sudah discan di HP.');
                        $('#toast-box').css('display', 'flex').hide().fadeIn().delay(3000).fadeOut();
                    }
                }).fail(function() {
                    btn.html(originalText);
                });
            });

            $('#btn-disconnect').click(function() {
                if(!confirm('Yakin ingin memutuskan koneksi WhatsApp?')) return;
                $(this).text('Memutuskan...').prop('disabled', true);
                
                $.post('/settings/disconnect', function(res) {
                    if(res.success) {
                        localStorage.setItem('show_disconnect_toast', 'true');
                        location.reload();
                    }
                });
            });

            function loadQR() {
                $('#qr-section').show();
                $('#qr-placeholder').html('<div class="spinner"></div><span>Menyiapkan QR...</span>');
                
                $.get('/settings/device?get_qr=1', function(res) {
                    if (res.success && res.data && res.data.device_status === 'disconnect' && res.data.url) {
                        $('#qr-placeholder').html('<img src="' + res.data.url + '" style="width:100%; height:100%; border-radius:12px;">');
                    } else if (res.success && res.data && res.data.device_status === 'disconnect' && res.data.qr) {
                        $('#qr-placeholder').html('<img src="data:image/png;base64,' + res.data.qr + '" style="width:100%; height:100%; border-radius:12px; object-fit:contain;">');
                    } else if (res.success && res.data && res.data.device_status === 'connect') {
                        location.reload();
                    } else {
                        let errMsg = res.message || 'Gagal memuat QR. Periksa token Anda.';
                        $('#qr-placeholder').html('<span style="color:var(--danger); text-align:center; padding:16px;">' + errMsg + '</span>');
                        if (errMsg.includes("Terlalu banyak")) {
                            clearInterval(statusInterval);
                            statusInterval = null;
                        }
                    }
                }).fail(function() {
                    $('#qr-placeholder').html('<span style="color:var(--danger); text-align:center; padding:16px;">Gagal terhubung ke server.</span>');
                    clearInterval(statusInterval);
                    statusInterval = null;
                });

                if(!statusInterval) {
                    statusInterval = setInterval(checkStatus, 10000); // Poll every 10 seconds to avoid rate limits
                }
            }

            function checkStatus() {
                $.get('/settings/device', function(res) {
                    if (res.success && res.data && res.data.device_status === 'connect') {
                        clearInterval(statusInterval);
                        location.reload();
                    } else if (res.success === false && res.message && res.message.includes("Terlalu banyak")) {
                        clearInterval(statusInterval);
                        statusInterval = null;
                        $('#qr-placeholder').html('<span style="color:var(--danger); text-align:center; padding:16px;">' + res.message + '</span>');
                    }
                });
            }
        });
    </script>
</body>
</html>