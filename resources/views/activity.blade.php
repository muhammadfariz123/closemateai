<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Aktivitas - CloseMateAI</title>
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
        .main-content { margin-left: 260px; flex: 1; padding: 30px; display: flex; flex-direction: column; transition: margin-left 0.3s ease; }
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

        /* Filter Card */
        .filter-card { background: white; border: 1px solid var(--border-color); border-radius: 12px; padding: 24px; box-shadow: 0 2px 10px rgba(0,0,0,0.02); margin-bottom: 24px; display: flex; flex-direction: column; gap: 20px; }
        
        .filter-row { display: flex; gap: 12px; flex-wrap: wrap; align-items: center; }
        
        .badge-tab { padding: 8px 16px; border-radius: 20px; font-size: 13px; font-weight: 500; cursor: pointer; transition: 0.2s; border: 1px solid var(--border-color); color: var(--text-dark); background: white; }
        .badge-tab:hover { background: var(--bg-light); }
        .badge-tab.active-purple { background: var(--primary); color: white; border-color: var(--primary); }
        .badge-tab.active-gray { background: var(--bg-light); color: var(--text-dark); border-color: #e4e6ef; }
        
        .badge-tab-text { font-size: 14px; font-weight: 500; color: var(--text-muted); cursor: pointer; transition: 0.2s; margin-right: 16px; }
        .badge-tab-text:hover { color: var(--text-dark); }
        .badge-tab-text.active { color: var(--text-dark); }

        /* Content Card */
        .content-card { background: white; border: 1px solid var(--border-color); border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.02); min-height: 300px; display: flex; flex-direction: column; }
        .content-header { padding: 20px 24px; font-size: 16px; font-weight: 600; color: var(--text-dark); display: flex; align-items: center; gap: 8px; border-bottom: 1px solid var(--border-color); }
        
        .empty-state { flex: 1; display: flex; justify-content: center; align-items: center; color: var(--text-muted); font-size: 14px; padding: 40px; }

        /* Activity Items */
        .activity-list { display: flex; flex-direction: column; }
        .activity-item { display: flex; align-items: center; padding: 16px 24px; gap: 16px; border-bottom: 1px solid var(--border-color); transition: background-color 0.2s; }
        .activity-item:last-child { border-bottom: none; }
        .activity-item:hover { background-color: var(--bg-light); }
        
        .activity-icon { width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 16px; flex-shrink: 0; }
        .icon-purple { background-color: rgba(107, 92, 216, 0.1); color: var(--primary); }
        .icon-green { background-color: rgba(80, 205, 137, 0.1); color: var(--success); }
        .icon-blue { background-color: rgba(0, 158, 253, 0.1); color: #009efd; }
        .icon-orange { background-color: rgba(255, 199, 0, 0.1); color: var(--warning); }
        .icon-grey { background-color: rgba(161, 165, 183, 0.1); color: var(--text-muted); }
        
        .activity-details { flex: 1; min-width: 0; }
        .activity-details h4 { font-size: 14px; font-weight: 600; color: var(--text-dark); margin-bottom: 4px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .activity-details p { font-size: 13px; color: var(--text-muted); margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        
        .activity-meta { text-align: right; flex-shrink: 0; display: flex; flex-direction: column; gap: 4px; }
        .activity-meta span { font-size: 13px; font-weight: 600; color: var(--text-dark); }
        .activity-meta small { font-size: 11px; color: var(--text-muted); }

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
                <h1>Riwayat Aktivitas</h1>
                <p>Jejak aktivitas Owner dan Admin CS di seluruh percakapan</p>
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
                            <h4>{{ auth()->check() ? auth()->user()->name : 'Vendor' }} <i class="fa-solid fa-chevron-down" style="font-size: 10px; color: #a1a5b7; margin-left: 4px;"></i></h4>
                            <p>Starter</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="filter-card">
            <div class="filter-row" id="main_filters">
                <div class="badge-tab active-purple" onclick="setMainFilter('Semua')">Semua</div>
                <div class="badge-tab" onclick="setMainFilter('Balasan Admin')">Balasan Admin</div>
                <div class="badge-tab" onclick="setMainFilter('Balasan AI')">Balasan AI</div>
                <div class="badge-tab" onclick="setMainFilter('Chat Masuk')">Chat Masuk</div>
                <div class="badge-tab" onclick="setMainFilter('Handler')">Handler</div>
                <div class="badge-tab" onclick="setMainFilter('Takeover')">Takeover</div>
                <div class="badge-tab" onclick="setMainFilter('Lead')">Lead</div>
            </div>
            <div class="filter-row" style="border-top: 1px solid var(--border-color); padding-top: 20px;" id="sub_filters">
                <div class="badge-tab active-gray" style="border: none;" onclick="setSubFilter('Semua Anggota')">Semua Anggota</div>
                <div class="badge-tab-text" onclick="setSubFilter('Penapict (Owner)')">Penapict (Owner)</div>
                <div class="badge-tab-text" onclick="setSubFilter('Sistem / AI')">Sistem / AI</div>
            </div>
        </div>

        <div class="content-card">
            <div class="content-header">
                <i class="fa-solid fa-clock-rotate-left" style="color: var(--text-muted);"></i> <span id="activity_count">0 aktivitas</span>
            </div>
            <div class="activity-list" id="activity_container">
                <!-- Activities rendered here -->
            </div>
        </div>

    </div>

    <script>
        const mockActivities = [
            { type: 'Lead', actor: 'Sistem', title: 'Proses follow up label Follow Up', desc: '1 pesan terkirim. 0 dibatalkan | 1 barusan.', time: '08 Okt, 20.27', icon: 'fa-bolt', color: 'purple' },
            { type: 'Lead', actor: 'Sistem', title: 'Follow up terkirim ke e', desc: 'Label "Follow Up" -> "Pesan Follow up 1"', time: '08 Okt, 20.27', icon: 'fa-comment-dots', color: 'grey' },
            { type: 'Balasan Admin', actor: 'Penapict', title: 'Admin membalas e', desc: 'Halo Kak e, mau tanya apakah ada yang ingin didiskusikan lagi terkait paket yang kami tawarkan untuk tanggal acara Anda?', time: '08 Okt, 20.27', icon: 'fa-user-tie', color: 'green' },
            { type: 'Lead', actor: 'Sistem', title: 'Lead baru: e', desc: '+6281223334455', time: '08 Okt, 20.27', icon: 'fa-bolt', color: 'purple' },
            { type: 'Balasan AI', actor: 'Sistem', title: 'AI membalas e', desc: 'Halo Kak e, selamat datang di Penapict! Ada yang bisa kami bantu untuk rencana acara bahagianya?', time: '08 Okt, 19.57', icon: 'fa-robot', color: 'purple' },
            { type: 'Chat Masuk', actor: 'Sistem', title: 'Pesan baru dari e', desc: 'yis', time: '08 Okt, 19.57', icon: 'fa-comment-dots', color: 'blue' },
            { type: 'Takeover', actor: 'Penapict', title: 'Penapict mengaktifkan kembali AI', desc: 'Fariz', time: '08 Okt, 16.54', icon: 'fa-wrench', color: 'orange' },
            { type: 'Chat Masuk', actor: 'Sistem', title: 'Pesan baru dari Fariz', desc: 'permisi ka', time: '08 Okt, 19.44', icon: 'fa-comment-dots', color: 'blue' },
            { type: 'Handler', actor: 'Penapict', title: 'Penapict mengambil chat Fariz', desc: 'Handler: Penapict', time: '08 Okt, 16.26', icon: 'fa-user-plus', color: 'grey' },
            { type: 'Handler', actor: 'Penapict', title: 'Penapict melepas handler chat Fariz', desc: 'Chat kembali tanpa handler', time: '08 Okt, 16.16', icon: 'fa-user-minus', color: 'grey' },
            { type: 'Handler', actor: 'Penapict', title: 'Penapict mengambil chat Fariz', desc: 'Handler: Penapict', time: '08 Okt, 16.15', icon: 'fa-user-plus', color: 'grey' },
            { type: 'Handler', actor: 'Penapict', title: 'Penapict melepas handler chat Fariz', desc: 'Chat kembali tanpa handler', time: '08 Okt, 16.15', icon: 'fa-user-minus', color: 'grey' },
            { type: 'Takeover', actor: 'Penapict', title: 'Penapict mengaktifkan Human Takeover', desc: 'Fariz', time: '08 Okt, 12.59', icon: 'fa-wrench', color: 'orange' },
            { type: 'Takeover', actor: 'Sistem', title: 'Human takeover diminta', desc: 'Fariz menunggu balasan admin', time: '08 Okt, 12.58', icon: 'fa-wrench', color: 'orange' },
            { type: 'Balasan Admin', actor: 'Penapict', title: 'Penapict membalas Frz', desc: 'halo ka', time: '08 Okt, 12.49', icon: 'fa-user-tie', color: 'green' },
            { type: 'Balasan AI', actor: 'Sistem', title: 'AI membalas Frz', desc: 'Maaf kak, admin kami akan segera membalas pesan ini ya 🙏', time: '08 Okt, 12.34', icon: 'fa-robot', color: 'purple' },
            { type: 'Balasan AI', actor: 'Sistem', title: 'AI membalas Frz', desc: 'Ini link price list-nya ya kak 🙏 https://drive.google.com/file/...', time: '08 Okt, 12.32', icon: 'fa-robot', color: 'purple' },
            { type: 'Balasan AI', actor: 'Sistem', title: 'AI membalas Frz', desc: 'Boleh diinfokan juga untuk rencana tanggal dan lokasi acaranya agar bisa aku cek ketersediaan tim kami?', time: '08 Okt, 12.32', icon: 'fa-robot', color: 'purple' },
            { type: 'Balasan AI', actor: 'Sistem', title: 'AI membalas Frz', desc: 'Halo Kak Frz tentu boleh. Ini aku kirimkan katalog lengkap paket dokumentasi kami ya.', time: '08 Okt, 12.32', icon: 'fa-robot', color: 'purple' },
            { type: 'Chat Masuk', actor: 'Sistem', title: 'Pesan baru dari Frz', desc: 'Untuk tanggal 26 desember 2026 ka lokasinya di purwokerto', time: '08 Okt, 12.34', icon: 'fa-comment-dots', color: 'blue' },
            { type: 'Chat Masuk', actor: 'Sistem', title: 'Pesan baru dari Frz', desc: 'Boleh minta pricelist?', time: '08 Okt, 12.32', icon: 'fa-comment-dots', color: 'blue' },
            { type: 'Chat Masuk', actor: 'Sistem', title: 'Pesan baru dari Frz', desc: 'Halo ka', time: '08 Okt, 12.22', icon: 'fa-comment-dots', color: 'blue' },
            { type: 'Lead', actor: 'Sistem', title: 'Lead baru: Frz', desc: '+628987654321', time: '08 Okt, 12.22', icon: 'fa-bolt', color: 'purple' }
        ];

        let sysActivities = JSON.parse(localStorage.getItem('sys_activities'));
        if (!sysActivities || sysActivities.length === 0) {
            sysActivities = mockActivities;
            localStorage.setItem('sys_activities', JSON.stringify(sysActivities));
        }

        let currentMainFilter = 'Semua';
        let currentSubFilter = 'Semua Anggota';

        function renderActivities() {
            // refresh data from localStorage in case it changed in another tab
            sysActivities = JSON.parse(localStorage.getItem('sys_activities')) || [];
            
            const container = document.getElementById('activity_container');
            const countEl = document.getElementById('activity_count');
            
            let filtered = sysActivities.filter(a => {
                let matchMain = (currentMainFilter === 'Semua') || (a.type === currentMainFilter);
                
                let matchSub = true;
                if (currentSubFilter === 'Penapict (Owner)') {
                    matchSub = (a.actor === 'Penapict');
                } else if (currentSubFilter === 'Sistem / AI') {
                    matchSub = (a.actor === 'Sistem');
                }
                
                return matchMain && matchSub;
            });
            
            countEl.innerText = `${filtered.length} aktivitas`;
            
            if (filtered.length === 0) {
                container.innerHTML = `<div class="empty-state">Belum ada aktivitas untuk filter ini.</div>`;
                return;
            }
            
            let html = '';
            filtered.forEach(a => {
                html += `
                <div class="activity-item">
                    <div class="activity-icon icon-${a.color}">
                        <i class="fa-solid ${a.icon}"></i>
                    </div>
                    <div class="activity-details">
                        <h4>${a.title}</h4>
                        <p>${a.desc}</p>
                    </div>
                    <div class="activity-meta">
                        <span>${a.actor}</span>
                        <small>${a.time}</small>
                    </div>
                </div>
                `;
            });
            
            container.innerHTML = html;
        }

        function setMainFilter(type) {
            currentMainFilter = type;
            const parent = document.getElementById('main_filters');
            parent.querySelectorAll('.badge-tab').forEach(el => {
                if(el.innerText === type) {
                    el.classList.add('active-purple');
                } else {
                    el.classList.remove('active-purple');
                }
            });
            renderActivities();
        }

        function setSubFilter(type) {
            currentSubFilter = type;
            const parent = document.getElementById('sub_filters');
            parent.querySelectorAll('div').forEach(el => {
                if(el.innerText === type) {
                    el.className = 'badge-tab active-gray';
                    el.style.border = 'none';
                } else {
                    el.className = 'badge-tab-text';
                    el.style.border = '';
                }
            });
            renderActivities();
        }

        document.addEventListener('DOMContentLoaded', () => {
            renderActivities();
        });

        // Sidebar Collapse
        document.getElementById('btn-collapse').addEventListener('click', function() {
            document.body.classList.toggle('sidebar-collapsed');
        });
    </script>
</body>
</html>
