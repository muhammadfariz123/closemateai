<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - CloseMateAI</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --sidebar-bg: #1e1e2d;
            --sidebar-text: #a1a5b7;
            --sidebar-active-bg: rgba(107, 92, 216, 0.2);
            --sidebar-active-text: #6b5cd8;
            --primary: #6b5cd8;
            --bg-light: #f8f9fa;
            --card-bg: #ffffff;
            --border-color: #f1f1f4;
            --text-dark: #181c32;
            --text-muted: #a1a5b7;
            --success: #50cd89;
            --danger: #f1416c;
            --danger-bg: #fff5f8;
            --warning: #ffc700;
            --warning-bg: #fff8dd;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }
        body { background-color: var(--bg-light); display: flex; min-height: 100vh; color: var(--text-dark); }

        /* Sidebar */
        .sidebar { width: 260px; background-color: var(--sidebar-bg); display: flex; flex-direction: column; height: 100vh; position: fixed; left: 0; top: 0; transition: width 0.3s ease; overflow-x: hidden; }
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
        .main-content { margin-left: 260px; flex: 1; padding: 30px; display: flex; flex-direction: column; transition: margin-left 0.3s ease; }
        .topbar { display: flex; justify-content: space-between; align-items: center; margin: -30px -30px 30px -30px; padding: 16px 30px; background-color: white; border-bottom: 1px solid var(--border-color); }
        .page-title h1 { font-size: 20px; font-weight: 600; margin-bottom: 6px; }
        .page-title p { font-size: 14px; color: var(--text-muted); }
        
        .top-actions { display: flex; align-items: center; gap: 20px; }
        .status-pill { display: flex; align-items: center; gap: 8px; background-color: rgba(80, 205, 137, 0.1); color: var(--success); padding: 8px 16px; border-radius: 20px; font-size: 13px; font-weight: 600; border: 1px solid rgba(80, 205, 137, 0.2); }
        .status-dot { width: 8px; height: 8px; background-color: var(--success); border-radius: 50%; }
        
        .profile-wrapper { position: relative; cursor: pointer; }
        .user-profile { display: flex; align-items: center; gap: 12px; padding: 6px 16px 6px 6px; border-radius: 30px; transition: background-color 0.2s; }
        .profile-wrapper:hover .user-profile { background-color: rgba(107, 92, 216, 0.08); }
        .user-profile i.fa-chevron-down { font-size: 10px; color: var(--text-muted); margin-left: 4px; }
        .avatar { width: 36px; height: 36px; background-color: var(--primary); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 14px; }
        .user-info h4 { font-size: 14px; font-weight: 600; cursor: pointer; }
        .user-info p { font-size: 12px; color: var(--text-muted); }

        /* Profile Dropdown */
        .profile-dropdown { position: absolute; top: calc(100% + 10px); right: 0; background: white; border: 1px solid var(--border-color); border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); width: 220px; opacity: 0; visibility: hidden; transform: translateY(-10px); transition: all 0.2s ease; z-index: 100; padding: 8px 0; }
        .profile-dropdown.show { opacity: 1; visibility: visible; transform: translateY(0); }
        .dropdown-header { padding: 12px 20px; font-weight: 600; font-size: 14px; color: var(--text-dark); border-bottom: 1px solid var(--border-color); margin-bottom: 8px; }
        .dropdown-item { display: flex; align-items: center; gap: 12px; padding: 10px 20px; color: var(--text-dark); text-decoration: none; font-size: 14px; font-weight: 500; transition: 0.2s; }
        .dropdown-item:hover { background-color: var(--bg-light); }
        .dropdown-item i { width: 16px; text-align: center; color: var(--text-muted); font-size: 14px; }
        .dropdown-item.text-danger { color: var(--danger); }
        .dropdown-item.text-danger i { color: var(--danger); }

        /* Dashboard Grid */
        .grid-stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 20px; }
        .stat-card { background: var(--card-bg); padding: 24px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.02); display: flex; flex-direction: column; gap: 16px; border: 1px solid var(--border-color); }
        .stat-card.warning-card { background-color: var(--warning-bg); border-color: rgba(255, 199, 0, 0.2); }
        .stat-header { display: flex; justify-content: space-between; text-transform: uppercase; font-size: 11px; font-weight: 600; color: var(--text-muted); letter-spacing: 0.5px; }
        .stat-icon { color: var(--primary); font-size: 16px; }
        .stat-value { font-size: 32px; font-weight: 700; color: var(--text-dark); margin-top: auto; }
        .stat-desc { font-size: 12px; color: var(--text-muted); }

        .grid-main { display: grid; grid-template-columns: 2fr 1fr; gap: 20px; }
        .panel { background: var(--card-bg); border-radius: 12px; padding: 24px; box-shadow: 0 2px 10px rgba(0,0,0,0.02); border: 1px solid var(--border-color); }
        .panel-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .panel-title h3 { font-size: 16px; font-weight: 600; margin-bottom: 4px; }
        .panel-title p { font-size: 12px; color: var(--text-muted); }

        /* Alert Box */
        .alert-box { background-color: var(--danger-bg); border: 1px solid rgba(241, 65, 108, 0.2); padding: 20px; border-radius: 8px; display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .alert-content { display: flex; gap: 16px; align-items: center; }
        .alert-icon { font-size: 24px; color: var(--danger); }
        .alert-text h4 { font-size: 15px; color: var(--text-dark); margin-bottom: 4px; }
        .alert-text p { font-size: 13px; color: var(--text-muted); }
        .alert-badge { background-color: transparent; border: 1px solid var(--danger); color: var(--danger); padding: 4px 12px; border-radius: 4px; font-size: 12px; font-weight: 500; }

        .btn-scan { background-color: var(--primary); color: white; padding: 10px 20px; border-radius: 8px; border: none; font-size: 14px; font-weight: 500; display: flex; align-items: center; gap: 8px; cursor: pointer; display: inline-flex; }

        /* Mini Stats */
        .mini-stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-top: 30px; }
        .mini-stat { border: 1px solid var(--border-color); padding: 16px; border-radius: 8px; }
        .mini-stat p { font-size: 12px; color: var(--text-muted); margin-bottom: 8px; }
        .mini-stat h4 { font-size: 20px; font-weight: 600; }

        /* Empty State */
        .empty-state { text-align: center; color: var(--text-muted); font-size: 13px; padding: 40px 0; }
        


        /* Responsive Mobile Layout */
        @media (max-width: 768px) {
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
                <h1>Dashboard & WA Status</h1>
                <p>Ringkasan performa AI assistant kamu hari ini</p>
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
                <div class="profile-wrapper" id="profile-btn">
                    <div class="user-profile">
                        <div class="avatar">{{ auth()->check() ? substr(auth()->user()->name, 0, 1) : 'C' }}</div>
                        <div class="user-info">
                            <h4>{{ auth()->check() ? auth()->user()->name : 'Vendor' }} <i class="fa-solid fa-chevron-down"></i></h4>
                            <p>Starter</p>
                        </div>
                    </div>

                    <div class="profile-dropdown" id="profile-dropdown">
                        <div class="dropdown-header">{{ auth()->check() ? auth()->user()->name : 'Vendor' }}</div>
                        <a href="#" class="dropdown-item"><i class="fa-regular fa-user"></i> Profile</a>
                        <a href="#" class="dropdown-item"><i class="fa-regular fa-credit-card"></i> Billing</a>
                        <a href="/logout" class="dropdown-item text-danger"><i class="fa-solid fa-arrow-right-from-bracket"></i> Logout</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid-stats">
            <div class="stat-card">
                <div class="stat-header">
                    TOTAL CONVERSATIONS TODAY
                    <i class="fa-regular fa-comment stat-icon"></i>
                </div>
                <div class="stat-value">0</div>
                <div class="stat-desc">Belum ada data kemarin</div>
            </div>
            <div class="stat-card">
                <div class="stat-header">
                    ACTIVE HOT LEADS
                    <i class="fa-solid fa-fire stat-icon" style="color: #a1a5b7;"></i>
                </div>
                <div class="stat-value">0</div>
                <div class="stat-desc">0 baru minggu ini</div>
            </div>
            <div class="stat-card">
                <div class="stat-header">
                    AI RESOLUTION RATE
                    <i class="fa-solid fa-robot stat-icon"></i>
                </div>
                <div class="stat-value">0%</div>
                <div class="stat-desc">AI menangani 0 dari 0 chat</div>
            </div>
            <div class="stat-card warning-card">
                <div class="stat-header">
                    PENDING HUMAN TAKEOVERS
                    <i class="fa-solid fa-hand-holding-hand stat-icon" style="color: #64553b;"></i>
                </div>
                <div class="stat-value">0</div>
                <div class="stat-desc" style="color: #8f8574;">Menunggu balasan admin</div>
            </div>
        </div>

        <div class="grid-main">
            <div class="panel">
                <div class="panel-header">
                    <div class="panel-title">
                        <h3>WhatsApp Gateway Status</h3>
                        <p>Nomor bisnis yang terhubung ke AI assistant</p>
                    </div>
                    <i class="fa-solid fa-mobile-screen" style="color: var(--text-muted);"></i>
                </div>

                @if($isWaConnected)
                <div class="alert-box" style="background-color: rgba(80, 205, 137, 0.1); border-color: var(--success);">
                    <div class="alert-content">
                        <i class="fa-regular fa-circle-check alert-icon" style="color: var(--success); font-size: 24px; margin-right: 16px;"></i>
                        <div class="alert-text">
                            <h4 style="color: var(--text-dark); margin-bottom: 2px;">Connected • {{ $currentUser->wa_number ?? $currentUser->business_wa_number ?? 'WhatsApp' }}</h4>
                            <p style="color: var(--text-muted); font-size: 13px;">Session aktif • terakhir sinkron {{ $currentUser->updated_at ? $currentUser->updated_at->diffForHumans() : 'baru saja' }}</p>
                        </div>
                    </div>
                    <div class="alert-badge" style="background-color: var(--success); color: white; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 600;">Connected</div>
                </div>
                <a href="/settings" class="btn-scan" style="text-decoration: none;"><i class="fa-solid fa-gears"></i> Kelola Koneksi</a>
                @else
                <div class="alert-box">
                    <div class="alert-content">
                        <i class="fa-solid fa-power-off alert-icon"></i>
                        <div class="alert-text">
                            <h4>Belum terhubung</h4>
                            <p>Hubungkan WhatsApp lewat scan QR di halaman Settings</p>
                        </div>
                    </div>
                    <div class="alert-badge">Offline</div>
                </div>
                <a href="/settings" class="btn-scan" style="text-decoration: none;"><i class="fa-solid fa-qrcode"></i> Scan QR Code</a>
                @endif

                <div class="mini-stats-grid">
                    <div class="mini-stat">
                        <p>Balasan terkirim hari ini</p>
                        <h4>0</h4>
                    </div>
                    <div class="mini-stat">
                        <p>Percakapan hari ini</p>
                        <h4>0</h4>
                    </div>
                    <div class="mini-stat">
                        <p>Perlu admin</p>
                        <h4>0</h4>
                    </div>
                </div>
            </div>

            <div class="panel">
                <div class="panel-header">
                    <div class="panel-title">
                        <h3>Quick Activity Feed</h3>
                    </div>
                    <i class="fa-solid fa-arrow-up-right-from-square" style="color: var(--text-muted); font-size: 14px;"></i>
                </div>
                
                <div class="empty-state">
                    Belum ada aktivitas. Aktivitas muncul otomatis saat ada chat masuk.
                </div>
            </div>
        </div>
    </div>

    @include('components.whatsapp-widget')
    
    <script>
        // Fitur Sidebar Collapse
        const btnCollapse = document.getElementById('btn-collapse');
        btnCollapse.addEventListener('click', () => {
            document.body.classList.toggle('sidebar-collapsed');
        });

        // Profile Dropdown Toggle
        const profileBtn = document.getElementById('profile-btn');
        const profileDropdown = document.getElementById('profile-dropdown');
        
        profileBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            profileDropdown.classList.toggle('show');
        });
        
        document.addEventListener('click', () => {
            if (profileDropdown.classList.contains('show')) {
                profileDropdown.classList.remove('show');
            }
        });
    </script>
</body>
</html>
