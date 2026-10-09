<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking & Operasional - CloseMateAI</title>
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
            --warning: #ffc700;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }
        body { background-color: var(--bg-light); display: flex; min-height: 100vh; color: var(--text-dark); }

        /* Sidebar */
        .sidebar { width: 260px; background-color: var(--sidebar-bg); display: flex; flex-direction: column; height: 100vh; position: fixed; left: 0; top: 0; transition: width 0.3s ease; overflow-x: hidden; z-index: 200; }
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

        /* Utilities */
        .form-control { padding: 10px 14px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 14px; outline: none; transition: 0.2s; }
        .form-control:focus { border-color: var(--primary); }
        
        .btn { padding: 10px 20px; border-radius: 20px; font-size: 14px; font-weight: 500; cursor: pointer; border: 1px solid transparent; display: inline-flex; align-items: center; gap: 8px; transition: 0.2s; }
        .btn-primary { background: var(--primary); color: white; }
        .btn-secondary { background: white; border-color: var(--border-color); color: var(--text-dark); }
        .btn-danger { background: var(--danger); color: white; }
        
        /* Layout */
        .controls-row { display: flex; justify-content: space-between; margin-bottom: 24px; flex-wrap: wrap; gap: 12px; align-items: center; }
        .left-controls { display: flex; gap: 12px; flex: 1; min-width: 200px; }
        .right-controls { display: flex; gap: 12px; align-items: center; }
        
        .search-box { position: relative; flex: 1; max-width: 300px; }
        .search-box i { position: absolute; left: 14px; top: 12px; color: var(--text-muted); }
        .search-box input { width: 100%; padding-left: 36px; border-radius: 20px; }
        
        .btn-group { display: flex; border-radius: 20px; background: white; border: 1px solid var(--border-color); overflow: hidden; }
        .btn-group .btn { border: none; border-radius: 0; padding: 10px 16px; background: transparent; }
        .btn-group .btn.active { background: var(--primary); color: white; }
        
        /* Calendar */
        .panel { background: white; border-radius: 16px; padding: 24px; border: 1px solid var(--border-color); box-shadow: 0 2px 10px rgba(0,0,0,0.02); }
        .calendar-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
        .calendar-title { font-size: 18px; font-weight: 600; text-align: center; flex: 1; }
        .btn-nav { width: 36px; height: 36px; border-radius: 8px; border: 1px solid var(--border-color); display: flex; align-items: center; justify-content: center; background: white; cursor: pointer; }
        
        .calendar-grid { display: grid; grid-template-columns: repeat(7, 1fr); gap: 12px; }
        .calendar-day-header { text-align: center; font-size: 13px; font-weight: 500; color: var(--text-muted); padding-bottom: 12px; }
        .calendar-day { border: 1px solid var(--border-color); border-radius: 12px; min-height: 100px; padding: 12px; display: flex; flex-direction: column; transition: 0.2s; background: white; }
        .calendar-day.today { border: 2px solid var(--primary); }
        .calendar-day-num { font-size: 14px; font-weight: 500; color: var(--text-dark); margin-bottom: 8px; }
        
        .event-pill { font-size: 11px; padding: 4px 8px; border-radius: 4px; background: var(--warning-bg); color: #b58d00; border: 1px solid rgba(255,199,0,0.2); font-weight: 500; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; cursor: pointer; }
        
        .calendar-legend { display: flex; gap: 16px; margin-top: 24px; font-size: 12px; color: var(--text-muted); }
        .legend-item { display: flex; align-items: center; gap: 6px; }
        .legend-dot { width: 10px; height: 10px; border-radius: 50%; }

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
            
            .toast-container { position: fixed; bottom: 24px; right: 24px; z-index: 9999; display: flex; flex-direction: column; gap: 10px; }
            .toast { background: white; color: var(--text-dark); padding: 16px 24px; border-radius: 12px; font-size: 14px; font-weight: 500; display: flex; align-items: center; gap: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); border-left: 4px solid var(--success); animation: slideIn 0.3s forwards; }
            @keyframes slideIn { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
            @keyframes fadeOut { from { transform: translateX(0); opacity: 1; } to { transform: translateX(100%); opacity: 0; } }
            
            .package-dropdown-item:hover { background: rgba(255,255,255,0.1); }
        }
        .table-view {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
            color: var(--text-color);
        }
        .table-view th {
            text-align: left;
            padding: 12px 16px;
            color: var(--text-muted);
            font-weight: 500;
            border-bottom: 1px solid var(--border-color);
        }
        .table-view td {
            padding: 16px;
            border-bottom: 1px solid var(--border-color);
            vertical-align: middle;
        }
        .badge-status {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
        }
        .badge-dp1 { background: rgba(255, 199, 0, 0.1); color: #b38b00; border: 1px solid #ffc700; }
        .badge-dp2 { background: rgba(80, 205, 137, 0.1); color: #2d8a57; border: 1px solid #50cd89; }
        .badge-lunas { background: rgba(107, 92, 216, 0.1); color: #493bb3; border: 1px solid #6b5cd8; }
        .badge-production { background: rgba(243, 244, 246, 1); color: #4b5563; border: 1px solid #d1d5db; }
        .handler-badge {
            background: rgba(107, 92, 216, 0.1);
            color: #6b5cd8;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 11px;
        }
        .action-icons {
            display: flex;
            gap: 12px;
            color: var(--text-muted);
        }
        .action-icons i { cursor: pointer; }
        .action-icons i:hover { color: var(--primary-color); }
        .action-icons i.delete:hover { color: var(--danger); }
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
                <h1 id="main_title_h1">Booking & Operasional Management</h1>
                <p id="main_title_p">Pantau jadwal acara, pembayaran, biaya operasional, dan estimasi profit setiap klien.</p>
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
                        <div class="avatar">{{ auth()->check() ? auth()->user()->name[0] ?? 'P' : 'P' }}</div>
                        <div class="user-info">
                            <h4>{{ auth()->check() ? auth()->user()->name : 'Penapict' }} <i class="fa-solid fa-chevron-down"></i></h4>
                            <p>Starter</p>
                        </div>
                    </div>

                    <div class="profile-dropdown" id="profile-dropdown">
                        <div class="dropdown-header">{{ auth()->check() ? auth()->user()->name : 'Penapict' }}</div>
                        <a href="#" class="dropdown-item"><i class="fa-regular fa-user"></i> Profile</a>
                        <a href="#" class="dropdown-item"><i class="fa-regular fa-credit-card"></i> Billing</a>
                        <a href="/logout" class="dropdown-item text-danger"><i class="fa-solid fa-arrow-right-from-bracket"></i> Logout</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="controls-row">
            <div class="left-controls">
                <div class="search-box">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" class="form-control" placeholder="Cari nama klien, nomor, atau paket...">
                </div>
                <button class="btn btn-danger"><i class="fa-brands fa-youtube"></i> Tutorial</button>
                <button class="btn btn-secondary" onclick="openGoogleCalendarSync()"><i class="fa-regular fa-calendar"></i> Google Calendar</button>
            </div>
            
            <div class="right-controls">
                <div class="btn-group">
                    <button class="btn active" id="btn_calendar_view" onclick="switchView('calendar')"><i class="fa-regular fa-calendar-days"></i> Calendar View</button>
                    <button class="btn" id="btn_table_view" onclick="switchView('table')"><i class="fa-solid fa-table-list"></i> Table View</button>
                </div>
                <select class="form-control" style="border-radius: 20px; width: 140px;">
                    <option>Semua Bulan</option>
                </select>
                <button onclick="openBookingModal()" class="btn btn-primary" style="border-radius: 8px;"><i class="fa-solid fa-plus"></i> Tambah Booking</button>
            </div>
        </div>
        
        <div class="panel" id="calendar_panel">
            <div class="calendar-header">
                <button class="btn-nav"><i class="fa-solid fa-chevron-left"></i></button>
                <div class="calendar-title">Oktober 2026</div>
                <button class="btn-nav"><i class="fa-solid fa-chevron-right"></i></button>
            </div>
            
            <div class="calendar-grid" id="b_calendar_grid">
                <!-- Headers -->
                <div class="calendar-day-header">Sen</div>
                <div class="calendar-day-header">Sel</div>
                <div class="calendar-day-header">Rab</div>
                <div class="calendar-day-header">Kam</div>
                <div class="calendar-day-header">Jum</div>
                <div class="calendar-day-header">Sab</div>
                <div class="calendar-day-header">Min</div>
                
                <!-- Week 1 (Offset 3 days) -->
                <!-- Content will be rendered by renderCalendar() -->
            </div>
            
            <div class="calendar-legend">
                <div class="legend-item"><div class="legend-dot" style="background: #ffc700; border: 1px solid #e0b000;"></div> DP 1</div>
                <div class="legend-item"><div class="legend-dot" style="background: #50cd89; border: 1px solid #47b679;"></div> DP 2</div>
                <div class="legend-item"><div class="legend-dot" style="background: #6b5cd8; border: 1px solid #5a4db8;"></div> LUNAS</div>
            </div>
        </div>

        <div class="panel" id="table_panel" style="display: none; padding: 0;">
            <table class="table-view">
                <thead>
                    <tr>
                        <th>Klien</th>
                        <th>Handler</th>
                        <th>Tanggal Acara</th>
                        <th>Paket & Harga</th>
                        <th>Pembayaran</th>
                        <th>Produksi</th>
                        <th>Link Hasil</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="b_table_body">
                    <!-- Table content will be rendered by renderTable() -->
                </tbody>
            </table>
        </div>
        
        <div class="panel" id="workspace_panel" style="display: none; padding: 0; background: transparent; border: none; box-shadow: none;">
            <div style="margin-bottom: 24px;">
                <button onclick="closeWorkspace()" style="background: none; border: none; font-size: 14px; font-weight: 600; color: var(--text-color); cursor: pointer; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-arrow-left"></i> Kembali ke daftar booking
                </button>
            </div>

            <div style="display: flex; gap: 12px; margin-bottom: 24px;">
                <button class="btn" style="border-radius: 20px; font-weight: 600; padding: 8px 16px; background: white; border: 1px solid var(--border-color); color: var(--text-dark);"><i class="fa-regular fa-calendar-check"></i> Ringkasan & Tagihan</button>
                <button class="btn btn-secondary" style="border-radius: 20px; font-weight: 600; padding: 8px 16px; background: transparent;"><i class="fa-solid fa-file-lines"></i> Event Workspace</button>
            </div>
            
            <div style="display: grid; grid-template-columns: repeat(5, 1fr); gap: 16px; margin-bottom: 24px;">
                <div style="background: white; border-radius: 12px; padding: 16px; border: 1px solid var(--border-color); box-shadow: 0 4px 10px rgba(0,0,0,0.02);">
                    <div style="font-size: 12px; color: var(--text-muted); margin-bottom: 4px;">Nilai Paket</div>
                    <div style="font-size: 18px; font-weight: 700;" id="ws_nilai_paket">Rp 0</div>
                </div>
                <div style="background: white; border-radius: 12px; padding: 16px; border: 1px solid var(--border-color); box-shadow: 0 4px 10px rgba(0,0,0,0.02);">
                    <div style="font-size: 12px; color: var(--text-muted); margin-bottom: 4px;">Sudah Dibayar</div>
                    <div style="font-size: 18px; font-weight: 700; color: var(--success);" id="ws_sudah_dibayar">Rp 0</div>
                </div>
                <div style="background: white; border-radius: 12px; padding: 16px; border: 1px solid var(--border-color); box-shadow: 0 4px 10px rgba(0,0,0,0.02);">
                    <div style="font-size: 12px; color: var(--text-muted); margin-bottom: 4px;">Sisa Tagihan Klien</div>
                    <div style="font-size: 18px; font-weight: 700; color: var(--danger);" id="ws_sisa_tagihan">Rp 0</div>
                </div>
                <div style="background: white; border-radius: 12px; padding: 16px; border: 1px solid var(--border-color); box-shadow: 0 4px 10px rgba(0,0,0,0.02);">
                    <div style="font-size: 12px; color: var(--text-muted); margin-bottom: 4px;">Total Biaya Vendor (HPP)</div>
                    <div style="font-size: 18px; font-weight: 700; color: var(--danger);" id="ws_total_hpp">Rp 0</div>
                </div>
                <div style="background: white; border-radius: 12px; padding: 16px; border: 1px solid var(--border-color); box-shadow: 0 4px 10px rgba(0,0,0,0.02);">
                    <div style="font-size: 12px; color: var(--text-muted); margin-bottom: 4px;">Total Biaya Acara</div>
                    <div style="font-size: 18px; font-weight: 700; color: var(--text-dark);" id="ws_total_biaya">Rp 0</div>
                </div>
            </div>
            
            <div style="background: white; border-radius: 12px; border: 1px solid var(--border-color); box-shadow: 0 4px 10px rgba(0,0,0,0.02); margin-bottom: 24px; padding: 24px;">
                <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 24px;">
                    <div>
                        <div style="font-size: 12px; color: var(--text-muted); margin-bottom: 4px;">Estimasi Profit Acara</div>
                        <div style="font-size: 24px; font-weight: 700; color: var(--success);" id="ws_estimasi_profit">Rp 0</div>
                    </div>
                    <div style="text-align: right;">
                        <div style="font-size: 12px; color: var(--text-muted); margin-bottom: 4px;">Margin</div>
                        <div style="font-size: 24px; font-weight: 700; color: var(--success);" id="ws_margin">0%</div>
                    </div>
                </div>
                
                <div style="display: flex; flex-direction: column; gap: 12px; border-top: 1px solid var(--border-color); padding-top: 16px;">
                    <div style="display: flex; justify-content: space-between; font-size: 13px;">
                        <span style="color: var(--text-color);">Nilai Paket</span>
                        <span style="font-weight: 600;" id="ws_breakdown_paket">Rp 0</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; font-size: 13px;">
                        <span style="color: var(--text-color);">Biaya Operasional</span>
                        <span style="font-weight: 600; color: var(--danger);" id="ws_breakdown_operasional">- Rp 0</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; font-size: 13px;">
                        <span style="color: var(--text-color);">Biaya Vendor Rekanan (HPP)</span>
                        <span style="font-weight: 600; color: var(--danger);" id="ws_breakdown_hpp">- Rp 0</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; font-size: 13px;">
                        <span style="color: var(--text-color);">Pengeluaran Tercatat</span>
                        <span style="font-weight: 600; color: var(--danger);" id="ws_breakdown_pengeluaran">- Rp 0</span>
                    </div>
                </div>
                <div style="font-size: 11px; color: var(--text-muted); margin-top: 16px;">
                    Angka ini otomatis ikut berubah saat kamu menambah vendor rekanan atau mencatat biaya baru untuk acara ini.
                </div>
            </div>
            
            <div style="background: white; border-radius: 12px; border: 1px solid var(--border-color); box-shadow: 0 4px 10px rgba(0,0,0,0.02); padding: 24px;">
                <div style="display: flex; gap: 8px; margin-bottom: 24px;" id="ws_badges">
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 24px;">
                    <div>
                        <div style="font-size: 12px; color: var(--text-muted); margin-bottom: 4px;">Klien</div>
                        <div style="font-size: 14px; font-weight: 500;" id="ws_klien">-</div>
                    </div>
                    <div>
                        <div style="font-size: 12px; color: var(--text-muted); margin-bottom: 4px;">Alamat Klien</div>
                        <div style="font-size: 14px; font-weight: 500;" id="ws_alamat">-</div>
                    </div>
                    <div>
                        <div style="font-size: 12px; color: var(--text-muted); margin-bottom: 4px;">Tanggal Acara</div>
                        <div style="font-size: 14px; font-weight: 500;" id="ws_tanggal">-</div>
                    </div>
                    <div>
                        <div style="font-size: 12px; color: var(--text-muted); margin-bottom: 4px;">Paket</div>
                        <div style="font-size: 14px; font-weight: 500;" id="ws_paket">-</div>
                    </div>
                    <div>
                        <div style="font-size: 12px; color: var(--text-muted); margin-bottom: 4px;">Handler</div>
                        <div style="font-size: 14px; font-weight: 500;" id="ws_handler">-</div>
                    </div>
                </div>
                
                <div style="border-top: 1px solid var(--border-color); padding-top: 16px; margin-bottom: 16px;">
                    <div style="font-size: 12px; color: var(--text-muted); margin-bottom: 8px;">Add-on</div>
                    <div id="ws_addons_list">
                    </div>
                </div>
                
                <div style="border-top: 1px solid var(--border-color); padding-top: 16px; margin-bottom: 16px;">
                    <div style="font-size: 12px; color: var(--text-muted); margin-bottom: 8px;"><i class="fa-solid fa-user-group"></i> Tim Bertugas</div>
                    <div id="ws_team_list" style="font-size: 13px;">
                    </div>
                </div>
                
                <div style="border-top: 1px solid var(--border-color); padding-top: 16px;">
                    <div style="font-size: 12px; color: var(--text-muted); margin-bottom: 8px;">Catatan</div>
                    <div id="ws_catatan" style="font-size: 13px;">
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('components.booking-modal')
    
    <!-- Google Calendar Sync Modal -->
    <div class="modal-overlay" id="googleCalendarSyncModal" style="display: none;">
        <div class="modal-content" style="width: 500px; padding: 24px; border-radius: 12px; background: white;">
            <div class="modal-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                <h2 style="font-size: 16px; font-weight: 600; margin: 0; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-regular fa-calendar-check" style="color: var(--primary-color);"></i> Hubungkan Jadwal Booking ke Google Calendar
                </h2>
                <i class="fa-solid fa-times close-btn" onclick="closeGoogleCalendarSync()" style="cursor: pointer; color: var(--text-muted);"></i>
            </div>
            <div class="modal-body" style="font-size: 13px; color: var(--text-color);">
                <p style="margin-bottom: 16px; line-height: 1.5;">Salin link langganan di bawah ini, lalu tambahkan di Google Calendar melalui menu <strong>Other calendars &rarr; From URL</strong>. Semua booking akan muncul otomatis dan ikut ter-update saat kamu mengubah data.</p>
                
                <div style="display: flex; gap: 8px; margin-bottom: 16px;">
                    <input type="text" class="form-control" id="gcal_sync_link" readonly value="{{ url('/api/public/calendar/47f4e8be-387f-4497-b2bc-57d81fe5f331.ics') }}" style="flex: 1; background: #f9f9f9; padding: 8px 12px; border: 1px solid var(--border-color); border-radius: 6px;">
                    <button class="btn btn-secondary" onclick="copyGcalSyncLink()" style="padding: 8px 16px; border-radius: 8px; background: white; border: 1px solid var(--border-color); cursor: pointer;"><i class="fa-regular fa-copy"></i></button>
                </div>
                
                <ol style="margin-bottom: 16px; padding-left: 20px; line-height: 1.6;">
                    <li>Buka calendar.google.com di browser desktop.</li>
                    <li>Klik tanda + di samping "Other calendars" &rarr; pilih "From URL".</li>
                    <li>Tempel link di atas lalu klik "Add calendar".</li>
                </ol>
                
                <p style="font-size: 12px; color: var(--text-muted); margin-bottom: 16px; line-height: 1.5;">Catatan: Google Calendar menarik ulang link langganan secara berkala (biasanya beberapa jam, bisa sampai 24 jam). Jadi booking baru tidak muncul seketika. Kalau ingin langsung tampil, pakai ikon kalender di Table View untuk menambahkan booking itu secara manual.</p>
                
                <p style="font-size: 12px; color: var(--text-muted); line-height: 1.5;">Link bersifat rahasia. Jangan dibagikan ke pihak lain karena berisi jadwal klien. Untuk satu booking saja, gunakan ikon kalender di Table View.</p>
            </div>
        </div>
    </div>
    <div class="toast-container" id="toast-container"></div>

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
        
        // Toast Function
        function showToast(message, type = 'success') {
            const container = document.getElementById('toast-container');
            if(!container) return;
            
            const toast = document.createElement('div');
            toast.className = 'toast';
            const iconClass = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
            const iconColor = type === 'success' ? 'var(--success)' : 'var(--danger)';
            
            toast.innerHTML = `<i class="fa-solid ${iconClass}" style="color: ${iconColor}; font-size: 16px;"></i> ${message}`;
            container.appendChild(toast);
            
            setTimeout(() => {
                toast.style.animation = 'fadeOut 0.3s forwards';
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }

        // Booking Modal Logic
        const bookingModal = document.getElementById('bookingModal');
        let addonsData = [];
        let costsData = [];
        let teamData = [];
        let editingBookingId = null;

        function openBookingModal(id = null) {
            if (typeof id !== 'string') id = null;
            editingBookingId = id;
            if (id) {
                document.getElementById('booking_modal_title').innerText = 'Edit Booking & Biaya Operasional';
                
                const bookings = JSON.parse(localStorage.getItem('b_events')) || [];
                const b = bookings.find(x => x.id === id);
                if (b) {
                    document.getElementById('b_client_name').value = b.client_name || '';
                    document.getElementById('b_client_wa').value = b.client_wa_number || '';
                    document.getElementById('b_client_address').value = b.client_address || '';
                    document.getElementById('b_event_date').value = b.event_date || '';
                    document.getElementById('b_start_time').value = b.start_time || '';
                    document.getElementById('b_end_time').value = b.end_time || '';
                    document.getElementById('b_package_name').value = b.package_name || '';
                    document.getElementById('b_package_price').value = b.package_price ? new Intl.NumberFormat('id-ID').format(b.package_price) : '';
                    document.getElementById('b_package_qty').value = b.package_qty || '1';
                    document.getElementById('b_paid_amount').value = b.paid_amount ? new Intl.NumberFormat('id-ID').format(b.paid_amount) : '0';
                    document.getElementById('b_discount').value = b.discount ? new Intl.NumberFormat('id-ID').format(b.discount) : '0';
                    document.getElementById('b_payment_date').value = b.payment_date || '';
                    document.getElementById('b_payment_status').value = b.payment_status || 'DP 1';
                    
                    // Production Status Custom Logic
                    const stdOptions = ['Pre-Event', 'Hari H', 'Proses Edit', 'Revisi', 'Selesai & Terkirim'];
                    if (b.production_status && !stdOptions.includes(b.production_status)) {
                        document.getElementById('b_production_status').value = 'custom';
                        document.getElementById('b_custom_status_container').style.display = 'flex';
                        document.getElementById('b_production_status').style.display = 'none';
                        document.getElementById('b_custom_status_input').value = b.production_status;
                    } else {
                        document.getElementById('b_production_status').value = b.production_status || 'Pre-Event';
                        document.getElementById('b_custom_status_container').style.display = 'none';
                        document.getElementById('b_production_status').style.display = 'block';
                        document.getElementById('b_custom_status_input').value = '';
                    }
                    
                    document.getElementById('b_result_link').value = b.result_link || '';
                    document.getElementById('b_notes').value = b.notes || '';
                    
                    addonsData = b.addons || [];
                    costsData = b.operational_costs || [];
                    teamData = b.team_members || [];
                    
                    toggleHandlerBadge();
                }
            } else {
                document.getElementById('booking_modal_title').innerText = 'Tambah Booking';
                // Reset form
                document.getElementById('b_client_name').value = '';
                document.getElementById('b_client_wa').value = '';
                document.getElementById('b_client_address').value = '';
                document.getElementById('b_event_date').value = '';
                document.getElementById('b_start_time').value = '';
                document.getElementById('b_end_time').value = '';
                document.getElementById('b_package_name').value = '';
                document.getElementById('b_package_price').value = '';
                document.getElementById('b_package_qty').value = '1';
                document.getElementById('b_paid_amount').value = '0';
                document.getElementById('b_discount').value = '0';
                document.getElementById('b_payment_date').value = '';
                document.getElementById('b_payment_status').value = 'DP 1';
                document.getElementById('b_production_status').value = 'Pre-Event';
                document.getElementById('b_custom_status_container').style.display = 'none';
                document.getElementById('b_production_status').style.display = 'block';
                document.getElementById('b_custom_status_input').value = '';
                document.getElementById('b_result_link').value = '';
                document.getElementById('b_notes').value = '';
                
                addonsData = [];
                costsData = [];
                teamData = [];
                
                toggleHandlerBadge();
            }
            
            renderAddons();
            renderCosts();
            renderTeam();
            calculateBooking();
            
            bookingModal.style.display = 'flex';
        }

        function closeBookingModal() {
            bookingModal.style.display = 'none';
        }
        
        function toggleCustomStatus() {
            const select = document.getElementById('b_production_status');
            const container = document.getElementById('b_custom_status_container');
            const input = document.getElementById('b_custom_status_input');
            
            if (select.value === 'custom') {
                select.style.display = 'none';
                container.style.display = 'flex';
                input.focus();
            } else {
                select.style.display = 'block';
                container.style.display = 'none';
            }
        }
        
        function cancelCustomStatus() {
            const select = document.getElementById('b_production_status');
            const container = document.getElementById('b_custom_status_container');
            const input = document.getElementById('b_custom_status_input');
            
            select.value = 'Pre-Event';
            select.style.display = 'block';
            container.style.display = 'none';
            input.value = '';
        }
        
        function toggleHandlerBadge() {
            const clientName = document.getElementById('b_client_name').value;
            const badge = document.getElementById('b_handler_badge');
            if (clientName.trim() !== '') {
                badge.style.display = 'inline-block';
            } else {
                badge.style.display = 'none';
            }
        }

        // Helper functions for Rupiah input formatting
        function formatRupiahInput(input) {
            let value = input.value.replace(/[^,\d]/g, '').toString();
            if (value) {
                input.value = new Intl.NumberFormat('id-ID').format(value);
            } else {
                input.value = '';
            }
        }
        
        function parseRupiah(str) {
            if (!str) return 0;
            return parseInt(str.toString().replace(/[^0-9]/g, '')) || 0;
        }

        function addBookingAddon() {
            addonsData.push({ name: '', qty: 1, price: 0 });
            renderAddons();
            calculateBooking();
        }

        function removeBookingAddon(index) {
            addonsData.splice(index, 1);
            renderAddons();
            calculateBooking();
        }

        function renderAddons() {
            const container = document.getElementById('b_addons_container');
            if (addonsData.length === 0) {
                container.innerHTML = '<p style="font-size: 13px; color: var(--text-muted); margin: 0;">Belum ada add-on.</p>';
                return;
            }
            let html = '';
            addonsData.forEach((item, index) => {
                html += `
                <div style="display: flex; gap: 12px; margin-bottom: 12px; align-items: center;">
                    <input type="text" class="form-control" placeholder="Nama Add-on" value="${item.name}" onchange="addonsData[${index}].name = this.value">
                    <input type="number" class="form-control" placeholder="Qty" value="${item.qty}" min="1" oninput="addonsData[${index}].qty = parseFloat(this.value) || 1; calculateBooking();" style="width: 80px;">
                    <input type="text" class="form-control" placeholder="Harga" value="${item.price ? new Intl.NumberFormat('id-ID').format(item.price) : ''}" oninput="formatRupiahInput(this); addonsData[${index}].price = parseRupiah(this.value); calculateBooking();" style="width: 150px;">
                    <button onclick="removeBookingAddon(${index})" style="background: none; border: none; color: var(--danger); cursor: pointer;"><i class="fa-solid fa-trash"></i></button>
                </div>
                `;
            });
            container.innerHTML = html;
        }

        function addBookingCost() {
            costsData.push({ name: '', qty: 1, price: 0 });
            renderCosts();
            calculateBooking();
        }

        function removeBookingCost(index) {
            costsData.splice(index, 1);
            renderCosts();
            calculateBooking();
        }

        function renderCosts() {
            const container = document.getElementById('b_costs_container');
            if (costsData.length === 0) {
                container.innerHTML = '<p style="font-size: 13px; color: var(--text-muted); margin: 0;">Belum ada biaya operasional.</p>';
                return;
            }
            let html = '';
            costsData.forEach((item, index) => {
                html += `
                <div style="display: flex; gap: 12px; margin-bottom: 12px; align-items: center;">
                    <input type="text" class="form-control" placeholder="Nama Biaya (contoh: transport)" value="${item.name}" onchange="costsData[${index}].name = this.value">
                    <input type="number" class="form-control" placeholder="Qty" value="${item.qty || 1}" min="1" oninput="costsData[${index}].qty = parseFloat(this.value) || 1; calculateBooking();" style="width: 80px;">
                    <input type="text" class="form-control" placeholder="Harga" value="${item.price ? new Intl.NumberFormat('id-ID').format(item.price) : ''}" oninput="formatRupiahInput(this); costsData[${index}].price = parseRupiah(this.value); calculateBooking();" style="width: 150px;">
                    <button onclick="removeBookingCost(${index})" style="background: none; border: none; color: var(--danger); cursor: pointer;"><i class="fa-solid fa-trash"></i></button>
                </div>
                `;
            });
            container.innerHTML = html;
        }

        function addBookingTeam() {
            teamData.push({ role: '', name: '' });
            renderTeam();
        }

        function removeBookingTeam(index) {
            teamData.splice(index, 1);
            renderTeam();
        }

        function renderTeam() {
            const container = document.getElementById('b_team_container');
            if (teamData.length === 0) {
                container.innerHTML = '<p style="font-size: 13px; color: var(--text-muted); margin: 0;">Belum ada tim.</p>';
                return;
            }
            let html = '';
            teamData.forEach((item, index) => {
                html += `
                <div style="display: flex; gap: 12px; margin-bottom: 12px; align-items: center;">
                    <input type="text" class="form-control" placeholder="Nama anggota tim" value="${item.name}" onchange="teamData[${index}].name = this.value" style="flex: 2;">
                    <input type="text" class="form-control" placeholder="Posisi / job desk" value="${item.role}" onchange="teamData[${index}].role = this.value" style="flex: 1;">
                    <button onclick="removeBookingTeam(${index})" style="background: none; border: none; color: var(--danger); cursor: pointer;"><i class="fa-solid fa-trash"></i></button>
                </div>
                `;
            });
            container.innerHTML = html;
        }

        function formatRupiah(num) {
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(num);
        }

        function calculateBooking() {
            const packagePrice = parseRupiah(document.getElementById('b_package_price').value);
            const packageQty = parseFloat(document.getElementById('b_package_qty').value) || 1;
            const discount = parseRupiah(document.getElementById('b_discount').value);
            
            const totalPackage = packagePrice * packageQty;
            const totalAddon = addonsData.reduce((sum, item) => sum + ((parseFloat(item.price) || 0) * (parseFloat(item.qty) || 1)), 0);
            const totalIncome = totalPackage + totalAddon - discount;
            
            const totalCost = costsData.reduce((sum, item) => sum + ((parseFloat(item.price) || 0) * (parseFloat(item.qty) || 1)), 0);
            const profit = totalIncome - totalCost;
            
            document.getElementById('b_label_total_income').innerText = formatRupiah(totalIncome);
            document.getElementById('b_label_total_cost').innerText = formatRupiah(totalCost);
            
            // Dynamic label for income
            if (discount > 0 || totalAddon > 0) {
                let addonStr = totalAddon > 0 ? ` + add-on Rp ${new Intl.NumberFormat('id-ID').format(totalAddon)}` : '';
                let discStr = discount > 0 ? ` - diskon Rp ${new Intl.NumberFormat('id-ID').format(discount)}` : '';
                document.getElementById('b_label_total_harga_addon').innerText = `Total Harga (paket${addonStr}${discStr})`;
                document.getElementById('b_label_summary_income').innerText = `Total Pendapatan (paket${addonStr}${discStr})`;
            } else {
                document.getElementById('b_label_summary_income').innerText = `Total Pendapatan (Paket + Add-On)`;
                document.getElementById('b_label_total_harga_addon').innerText = `Total Harga (paket + add-on)`;
            }
            
            document.getElementById('b_summary_income').innerText = formatRupiah(totalIncome);
            document.getElementById('b_summary_cost').innerText = formatRupiah(totalCost);
            document.getElementById('b_summary_profit').innerText = formatRupiah(profit);
        }

        async function saveBooking() {
            const btn = document.getElementById('btnSaveBooking');
            btn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> Menyimpan...';
            btn.disabled = true;
            
            // Re-calculate to ensure variables are fresh
            const packagePrice = parseRupiah(document.getElementById('b_package_price').value);
            const packageQty = parseFloat(document.getElementById('b_package_qty').value) || 1;
            const discount = parseRupiah(document.getElementById('b_discount').value);
            const totalPackage = packagePrice * packageQty;
            const totalAddon = addonsData.reduce((sum, item) => sum + ((parseFloat(item.price) || 0) * (parseFloat(item.qty) || 1)), 0);
            const totalIncome = totalPackage + totalAddon - discount;
            const totalCost = costsData.reduce((sum, item) => sum + ((parseFloat(item.price) || 0) * (parseFloat(item.qty) || 1)), 0);
            const profit = totalIncome - totalCost;
            
            let prodStatus = document.getElementById('b_production_status').value;
            if (prodStatus === 'custom') {
                prodStatus = document.getElementById('b_custom_status_input').value || 'Status Custom';
            }

            const payload = {
                id: editingBookingId || Date.now().toString(),
                client_name: document.getElementById('b_client_name').value,
                client_wa_number: document.getElementById('b_client_wa').value,
                client_address: document.getElementById('b_client_address').value,
                event_date: document.getElementById('b_event_date').value,
                start_time: document.getElementById('b_start_time').value,
                end_time: document.getElementById('b_end_time').value,
                package_name: document.getElementById('b_package_name').value,
                package_price: packagePrice,
                package_qty: packageQty,
                paid_amount: parseRupiah(document.getElementById('b_paid_amount').value),
                discount: discount,
                addons: addonsData,
                operational_costs: costsData,
                total_income: totalIncome,
                total_operational_cost: totalCost,
                net_profit: profit,
                payment_date: document.getElementById('b_payment_date').value,
                payment_status: document.getElementById('b_payment_status').value,
                production_status: prodStatus,
                result_link: document.getElementById('b_result_link').value,
                team_members: teamData,
                notes: document.getElementById('b_notes').value,
            };
            
            let bookings = JSON.parse(localStorage.getItem('b_events')) || [];
            if (editingBookingId) {
                const index = bookings.findIndex(x => x.id === editingBookingId);
                if (index > -1) {
                    bookings[index] = payload;
                } else {
                    bookings.push(payload);
                }
            } else {
                bookings.push(payload);
            }
            
            localStorage.setItem('b_events', JSON.stringify(bookings));
            showToast(editingBookingId ? 'Booking diperbarui!' : 'Booking ditambahkan!');
            closeBookingModal();
            renderCalendar();
            renderTable();
            
            btn.innerHTML = 'Simpan Booking';
            btn.disabled = false;
        }

        // --- PACKAGE AUTOCOMPLETE & LOCAL STORAGE LOGIC ---
        const packageInput = document.getElementById('b_package_name');
        const packageDropdown = document.getElementById('package_dropdown');
        const packagePriceInput = document.getElementById('b_package_price');
        
        function getSavedPackages() {
            try {
                return JSON.parse(localStorage.getItem('savedPackages')) || [];
            } catch (e) {
                return [];
            }
        }
        
        function savePackageToLocal() {
            const name = packageInput.value.trim();
            const price = parseFloat(packagePriceInput.value) || 0;
            if (!name) return;
            
            let packages = getSavedPackages();
            const existingIndex = packages.findIndex(p => p.name.toLowerCase() === name.toLowerCase());
            
            if (existingIndex >= 0) {
                packages[existingIndex].price = price; // Update price
            } else {
                packages.push({ name, price });
            }
            
            localStorage.setItem('savedPackages', JSON.stringify(packages));
            showToast('Paket & harga disimpan');
            packageDropdown.style.display = 'none'; // Hide dropdown
        }
        
        function checkPackagePrice() {
            const name = packageInput.value.trim().toLowerCase();
            const packages = getSavedPackages();
            
            // Auto fill exact match
            const exactMatch = packages.find(p => p.name.toLowerCase() === name);
            if (exactMatch) {
                packagePriceInput.value = exactMatch.price;
                calculateBooking();
            }
            
            // Show suggestions
            if (packages.length > 0) {
                renderPackageDropdown(packages.filter(p => p.name.toLowerCase().includes(name)));
            } else {
                packageDropdown.style.display = 'none';
            }
        }
        
        function renderPackageDropdown(filteredPackages) {
            if (filteredPackages.length === 0) {
                packageDropdown.style.display = 'none';
                return;
            }
            
            let html = '';
            filteredPackages.forEach(pkg => {
                html += `
                <div class="package-dropdown-item" style="display: flex; justify-content: space-between; align-items: center; padding: 10px 16px; border-bottom: 1px solid rgba(255,255,255,0.1);">
                    <div onclick="selectPackage('${pkg.name}', ${pkg.price})" style="cursor: pointer; flex: 1;">
                        <div style="font-size: 13px; font-weight: 600;">${pkg.name}</div>
                        <div style="font-size: 12px; color: rgba(255,255,255,0.7);">${formatRupiah(pkg.price)}</div>
                    </div>
                    <button onclick="deleteSavedPackage(event, '${pkg.name}')" style="background: none; border: none; color: var(--danger); cursor: pointer; font-size: 14px; padding: 4px;"><i class="fa-regular fa-trash-can"></i></button>
                </div>`;
            });
            
            packageDropdown.innerHTML = html;
            packageDropdown.style.display = 'block';
        }
        
        function selectPackage(name, price) {
            packageInput.value = name;
            packagePriceInput.value = price ? new Intl.NumberFormat('id-ID').format(price) : '';
            packageDropdown.style.display = 'none';
            calculateBooking();
        }
        
        function deleteSavedPackage(event, name) {
            event.stopPropagation();
            let packages = getSavedPackages();
            packages = packages.filter(p => p.name.toLowerCase() !== name.toLowerCase());
            localStorage.setItem('savedPackages', JSON.stringify(packages));
            renderPackageDropdown(packages);
            if(packages.length === 0) {
                packageDropdown.style.display = 'none';
            }
        }
        
        function togglePackageDropdown(e) {
            if (e) {
                e.stopPropagation();
                e.preventDefault();
            }
            if (packageDropdown.style.display === 'block') {
                packageDropdown.style.display = 'none';
            } else {
                const packages = getSavedPackages();
                if (packages.length > 0) {
                    renderPackageDropdown(packages);
                } else {
                    packageDropdown.innerHTML = '<div style="padding: 10px 16px; font-size: 12px; color: rgba(255,255,255,0.5);">Belum ada paket tersimpan</div>';
                    packageDropdown.style.display = 'block';
                }
            }
        }
        
        // Show all on focus if empty
        packageInput.addEventListener('click', (e) => {
            if (!packageInput.value.trim() || packageDropdown.style.display !== 'block') {
                const packages = getSavedPackages();
                if (packages.length > 0) renderPackageDropdown(packages);
            }
        });
        
        packageInput.addEventListener('focus', () => {
            if (!packageInput.value.trim() && packageDropdown.style.display !== 'block') {
                const packages = getSavedPackages();
                if (packages.length > 0) renderPackageDropdown(packages);
            }
        });
        
        // Hide dropdown on click outside
        document.addEventListener('click', (e) => {
            if (e.target !== packageInput && e.target !== packageDropdown && !packageDropdown.contains(e.target)) {
                packageDropdown.style.display = 'none';
            }
        });
        
        // --- HPP TEMPLATE LOGIC ---
        function getHppTemplates() {
            try {
                return JSON.parse(localStorage.getItem('hppTemplates')) || [];
            } catch (e) {
                return [];
            }
        }
        
        function refreshHppTemplateDropdown() {
            const select = document.getElementById('b_hpp_template');
            const templates = getHppTemplates();
            if (templates.length === 0) {
                select.innerHTML = '<option value="">Belum ada template HPP</option>';
            } else {
                let html = '<option value="">Pilih Template HPP...</option>';
                templates.forEach(t => {
                    const itemCount = t.items ? t.items.length : 0;
                    html += `<option value="${t.name}">${t.name} (${itemCount} item)</option>`;
                });
                select.innerHTML = html;
            }
            onHppTemplateChange(); // Reset buttons
        }

        function onHppTemplateChange() {
            const select = document.getElementById('b_hpp_template');
            const applyBtn = document.getElementById('btn_apply_hpp_template');
            const deleteBtn = document.getElementById('btn_delete_hpp_template');
            
            if (select.value) {
                applyBtn.style.display = 'block';
                deleteBtn.style.display = 'block';
            } else {
                applyBtn.style.display = 'none';
                deleteBtn.style.display = 'none';
            }
        }
        
        function toggleSaveHppInput() {
            const btn = document.getElementById('btn_show_save_hpp');
            const group = document.getElementById('b_hpp_save_input_group');
            
            if (group.style.display === 'none' || !group.style.display) {
                group.style.display = 'flex';
                btn.style.display = 'none';
                document.getElementById('b_hpp_template_name').focus();
            } else {
                group.style.display = 'none';
                btn.style.display = 'block';
            }
        }
        
        function saveHppTemplate() {
            const name = document.getElementById('b_hpp_template_name').value.trim();
            if (!name) {
                showToast('Masukkan nama template', 'error');
                return;
            }
            if (costsData.length === 0) {
                showToast('Tambahkan minimal satu biaya operasional', 'error');
                return;
            }
            
            let templates = getHppTemplates();
            const existingIndex = templates.findIndex(t => t.name.toLowerCase() === name.toLowerCase());
            
            if (existingIndex >= 0) {
                templates[existingIndex].items = [...costsData];
            } else {
                templates.push({ name, items: [...costsData] });
            }
            
            localStorage.setItem('hppTemplates', JSON.stringify(templates));
            showToast('Template HPP disimpan');
            document.getElementById('b_hpp_template_name').value = '';
            toggleSaveHppInput(); // Hide input, show btn again
            refreshHppTemplateDropdown();
        }
        
        function applyHppTemplate() {
            const select = document.getElementById('b_hpp_template');
            const name = select.value;
            if (!name) return;
            
            const templates = getHppTemplates();
            const template = templates.find(t => t.name === name);
            if (template) {
                // Deep copy
                costsData = JSON.parse(JSON.stringify(template.items));
                renderCosts();
                calculateBooking();
                const itemCount = template.items ? template.items.length : 0;
                showToast(`Template "${template.name}" diterapkan (${itemCount} item)`);
            }
            // Reset select
            select.value = '';
            onHppTemplateChange();
        }
        
        function deleteHppTemplate() {
            const select = document.getElementById('b_hpp_template');
            const name = select.value;
            if (!name) return;
            
            if (confirm(`Hapus template HPP "${name}"?`)) {
                let templates = getHppTemplates();
                templates = templates.filter(t => t.name !== name);
                localStorage.setItem('hppTemplates', JSON.stringify(templates));
                showToast(`Template "${name}" dihapus`);
                refreshHppTemplateDropdown();
            }
        }
        
        // Initialize dropdowns on load
        document.addEventListener('DOMContentLoaded', () => {
            refreshHppTemplateDropdown();
            renderCalendar();
            renderTable();
        });
        
        // --- VIEW TOGGLE LOGIC ---
        function switchView(view) {
            const btnCalendar = document.getElementById('btn_calendar_view');
            const btnTable = document.getElementById('btn_table_view');
            const pnlCalendar = document.getElementById('calendar_panel');
            const pnlTable = document.getElementById('table_panel');
            
            if (view === 'table') {
                btnCalendar.classList.remove('active');
                btnTable.classList.add('active');
                pnlCalendar.style.display = 'none';
                pnlTable.style.display = 'block';
                renderTable();
            } else {
                btnTable.classList.remove('active');
                btnCalendar.classList.add('active');
                pnlTable.style.display = 'none';
                pnlCalendar.style.display = 'block';
                renderCalendar();
            }
        }
        
        // --- CALENDAR LOGIC ---
        function renderCalendar() {
            const grid = document.getElementById('b_calendar_grid');
            if (!grid) return;
            
            const daysInMonth = 31; // October 2026
            const firstDayOffset = 3; // October 1st 2026 is Thursday (0=Senin, 1=Selasa, 2=Rabu, 3=Kamis)
            
            let html = `
                <div class="calendar-day-header">Sen</div>
                <div class="calendar-day-header">Sel</div>
                <div class="calendar-day-header">Rab</div>
                <div class="calendar-day-header">Kam</div>
                <div class="calendar-day-header">Jum</div>
                <div class="calendar-day-header">Sab</div>
                <div class="calendar-day-header">Min</div>
            `;
            
            // Empty offset days
            for (let i = 0; i < firstDayOffset; i++) {
                html += `<div class="calendar-day" style="opacity: 0; pointer-events: none;"></div>`;
            }
            
            const bookings = JSON.parse(localStorage.getItem('b_events')) || [];
            
            for (let i = 1; i <= daysInMonth; i++) {
                const isToday = i === 3; // Example today
                const dateStr = `2026-10-${i.toString().padStart(2, '0')}`;
                
                // Find events for this date
                const dayEvents = bookings.filter(b => b.event_date === dateStr);
                
                let eventsHtml = '';
                dayEvents.forEach(evt => {
                    let bgCol = '#ffc700';
                    let textCol = '#fff';
                    let borderCol = '#e0b000';
                    
                    if (evt.payment_status === 'DP 2') {
                        bgCol = '#50cd89';
                        textCol = '#fff';
                        borderCol = '#47b679';
                    } else if (evt.payment_status === 'Lunas') {
                        bgCol = '#6b5cd8';
                        textCol = '#fff';
                        borderCol = '#5a4db8';
                    }
                    
                    eventsHtml += `<div class="event-pill" onclick="openBookingModal('${evt.id}')" style="cursor: pointer; margin-top: 4px; padding: 4px 8px; border-radius: 4px; background: ${bgCol}; color: ${textCol}; border: 1px solid ${borderCol}; font-size: 11px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">${evt.client_name || 'No Name'}</div>`;
                });
                
                html += `
                <div class="calendar-day ${isToday ? 'today' : ''}">
                    <div class="calendar-day-num">${i}</div>
                    ${eventsHtml}
                </div>
                `;
            }
            
            // Pad end of grid
            const totalCells = firstDayOffset + daysInMonth;
            const remainingCells = 35 - totalCells; // 5 weeks * 7 days = 35
            for (let i = 0; i < (remainingCells > 0 ? remainingCells : 42 - totalCells); i++) {
                html += `<div class="calendar-day" style="opacity: 0; pointer-events: none;"></div>`;
            }
            
            grid.innerHTML = html;
        }

        // --- TABLE LOGIC ---
        function renderTable() {
            const tbody = document.getElementById('b_table_body');
            if (!tbody) return;
            
            const bookings = JSON.parse(localStorage.getItem('b_events')) || [];
            if (bookings.length === 0) {
                tbody.innerHTML = `<tr><td colspan="8" style="text-align: center; color: var(--text-muted); padding: 32px;">Belum ada data booking.</td></tr>`;
                return;
            }
            
            let html = '';
            bookings.forEach(b => {
                let badgeClass = 'badge-dp1';
                if (b.payment_status === 'DP 2') badgeClass = 'badge-dp2';
                if (b.payment_status === 'Lunas') badgeClass = 'badge-lunas';
                
                const ownerName = '{{ auth()->check() ? auth()->user()->name : 'Penapict' }}';
                let handlersHtml = `<span class="handler-badge">${ownerName}</span>`;
                
                let resultLinkHtml = '-';
                if (b.result_link) {
                    resultLinkHtml = `<div style="display: flex; align-items: center; gap: 8px;"><i class="fa-solid fa-link" style="color: var(--text-muted);"></i><a href="${b.result_link}" target="_blank" style="color: var(--text-color); text-decoration: none; font-size: 12px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 150px;">${b.result_link}</a><i class="fa-solid fa-arrow-up-right-from-square" style="font-size: 10px; color: var(--text-muted);"></i></div>`;
                }

                html += `
                <tr>
                    <td>
                        <div style="font-weight: 600;">${b.client_name || '-'}</div>
                        <div style="font-size: 11px; color: var(--text-muted); margin-top: 4px;">${b.client_wa_number || ''}</div>
                        ${b.client_address ? `<div style="font-size: 11px; color: var(--text-muted);">${b.client_address}</div>` : ''}
                    </td>
                    <td>${handlersHtml || '-'}</td>
                    <td>${b.event_date || '-'}</td>
                    <td>
                        <div style="font-weight: 500;">${b.package_name || '-'}</div>
                        <div style="font-size: 11px; color: var(--text-muted); margin-top: 4px;">Rp ${new Intl.NumberFormat('id-ID').format(b.package_price || 0)} &middot; dibayar</div>
                        <div style="font-size: 11px; color: var(--text-muted);">Rp ${new Intl.NumberFormat('id-ID').format(b.paid_amount || 0)}</div>
                    </td>
                    <td><span class="badge-status ${badgeClass}">${b.payment_status || 'DP 1'}</span></td>
                    <td><span class="badge-status badge-production">${b.production_status || 'Pre-Event'}</span></td>
                    <td>${resultLinkHtml}</td>
                    <td>
                        <div class="action-icons">
                            <i class="fa-regular fa-folder" title="Buka event workspace" onclick="openWorkspace('${b.id}')"></i>
                            <i class="fa-regular fa-calendar-check" title="Tambah ke google calender" onclick="addToGoogleCalendar('${b.id}')"></i>
                            <i class="fa-solid fa-pen" onclick="openBookingModal('${b.id}')"></i>
                            <i class="fa-regular fa-trash-can delete" onclick="deleteBooking('${b.id}')"></i>
                        </div>
                    </td>
                </tr>
                `;
            });
            tbody.innerHTML = html;
        }

        function deleteBooking(id) {
            if(confirm('Yakin ingin menghapus booking ini?')) {
                let bookings = JSON.parse(localStorage.getItem('b_events')) || [];
                bookings = bookings.filter(x => x.id !== id);
                localStorage.setItem('b_events', JSON.stringify(bookings));
                renderTable();
                renderCalendar();
                showToast('Booking dihapus', 'success');
            }
        }

        const defaultTitleH1 = "Booking & Operasional Management";
        const defaultTitleP = "Pantau jadwal acara, pembayaran, biaya operasional, dan estimasi profit setiap klien.";

        function openWorkspace(id) {
            const bookings = JSON.parse(localStorage.getItem('b_events')) || [];
            const b = bookings.find(x => x.id === id);
            if (!b) return;

            document.querySelector('.controls-row').style.display = 'none';
            document.getElementById('calendar_panel').style.display = 'none';
            document.getElementById('table_panel').style.display = 'none';
            
            document.getElementById('main_title_h1').innerText = b.client_name || 'Tanpa Nama';
            const dateStr = b.event_date ? new Date(b.event_date).toLocaleDateString('id-ID', {day: '2-digit', month: 'short', year: 'numeric'}) : '-';
            document.getElementById('main_title_p').innerText = `${b.package_name || '-'} - ${dateStr}`;
            
            const pkgPrice = parseFloat(b.package_price) || 0;
            const pkgQty = parseFloat(b.package_qty) || 1;
            const paidAmount = parseFloat(b.paid_amount) || 0;
            const discount = parseFloat(b.discount) || 0;
            
            let addonsTotal = 0;
            let addonsHtml = '';
            if (b.addons && b.addons.length > 0) {
                b.addons.forEach(a => {
                    const aPrice = parseFloat(a.price) || 0;
                    addonsTotal += aPrice;
                    addonsHtml += `<div style="display: flex; justify-content: space-between; font-size: 13px; margin-bottom: 4px;">
                        <span>${a.name}</span>
                        <span>Rp ${new Intl.NumberFormat('id-ID').format(aPrice)}</span>
                    </div>`;
                });
            } else {
                addonsHtml = '<div style="font-size: 13px; color: var(--text-muted);">-</div>';
            }
            
            const nilaiPaket = (pkgPrice * pkgQty) + addonsTotal - discount;
            const sisaTagihan = nilaiPaket - paidAmount;
            
            let totalHpp = 0;
            let totalBiayaAcara = 0;
            if (b.operational_costs && b.operational_costs.length > 0) {
                b.operational_costs.forEach(c => {
                    const costAmt = (parseFloat(c.price) || 0) * (parseFloat(c.qty) || 1);
                    if (c.type === 'HPP') totalHpp += costAmt;
                    totalBiayaAcara += costAmt;
                });
            }
            
            const estimasiProfit = nilaiPaket - totalBiayaAcara;
            const margin = nilaiPaket > 0 ? ((estimasiProfit / nilaiPaket) * 100).toFixed(1) : 0;
            
            document.getElementById('ws_nilai_paket').innerText = `Rp ${new Intl.NumberFormat('id-ID').format(nilaiPaket)}`;
            document.getElementById('ws_sudah_dibayar').innerText = `Rp ${new Intl.NumberFormat('id-ID').format(paidAmount)}`;
            document.getElementById('ws_sisa_tagihan').innerText = `Rp ${new Intl.NumberFormat('id-ID').format(sisaTagihan)}`;
            document.getElementById('ws_total_hpp').innerText = `Rp ${new Intl.NumberFormat('id-ID').format(totalHpp)}`;
            document.getElementById('ws_total_biaya').innerText = `Rp ${new Intl.NumberFormat('id-ID').format(totalBiayaAcara)}`;
            
            document.getElementById('ws_estimasi_profit').innerText = `Rp ${new Intl.NumberFormat('id-ID').format(estimasiProfit)}`;
            document.getElementById('ws_margin').innerText = `${margin}%`;
            document.getElementById('ws_breakdown_paket').innerText = `Rp ${new Intl.NumberFormat('id-ID').format(nilaiPaket)}`;
            document.getElementById('ws_breakdown_operasional').innerText = `- Rp ${new Intl.NumberFormat('id-ID').format(totalBiayaAcara - totalHpp)}`;
            document.getElementById('ws_breakdown_hpp').innerText = `- Rp ${new Intl.NumberFormat('id-ID').format(totalHpp)}`;
            document.getElementById('ws_breakdown_pengeluaran').innerText = `- Rp 0`;
            
            const badgeColorMap = {
                'DP 1': 'rgba(255, 199, 0, 0.1); color: #b38b00; border: 1px solid #ffc700;',
                'DP 2': 'rgba(80, 205, 137, 0.1); color: #2d8a57; border: 1px solid #50cd89;',
                'Lunas': 'rgba(107, 92, 216, 0.1); color: #493bb3; border: 1px solid #6b5cd8;'
            };
            const paymentStatusBadge = `<span class="handler-badge" style="${badgeColorMap[b.payment_status || 'DP 1']}">${b.payment_status || 'DP 1'}</span>`;
            const productionStatusBadge = `<span class="handler-badge" style="background: rgba(243, 244, 246, 1); color: #4b5563; border: 1px solid #d1d5db;">${b.production_status || 'Pre-Event'}</span>`;
            
            document.getElementById('ws_badges').innerHTML = paymentStatusBadge + productionStatusBadge;
            document.getElementById('ws_klien').innerText = `${b.client_name || '-'} · ${b.client_wa_number || '-'}`;
            document.getElementById('ws_alamat').innerText = b.client_address || '-';
            
            const timeStr = (b.start_time || b.end_time) ? `${b.start_time || '00:00'}-${b.end_time || '23:59'}` : '';
            document.getElementById('ws_tanggal').innerText = `${dateStr} ${timeStr ? '· '+timeStr : ''}`;
            document.getElementById('ws_paket').innerText = b.package_name || '-';
            
            const ownerName = '{{ auth()->check() ? auth()->user()->name : 'Penapict' }}';
            document.getElementById('ws_handler').innerText = ownerName;
            
            document.getElementById('ws_addons_list').innerHTML = addonsHtml;
            
            let teamHtml = '';
            if (b.team_members && b.team_members.length > 0) {
                teamHtml = b.team_members.map(t => `${t.name} (${t.role})`).join('<br>');
            } else {
                teamHtml = '-';
            }
            document.getElementById('ws_team_list').innerHTML = teamHtml;
            document.getElementById('ws_catatan').innerText = b.notes || '-';
            
            document.getElementById('workspace_panel').style.display = 'block';
        }

        function closeWorkspace() {
            document.getElementById('workspace_panel').style.display = 'none';
            
            document.getElementById('main_title_h1').innerText = defaultTitleH1;
            document.getElementById('main_title_p').innerText = defaultTitleP;
            
            document.querySelector('.controls-row').style.display = 'flex';
            const isTable = document.getElementById('btn_table_view').classList.contains('active');
            switchView(isTable ? 'table' : 'calendar');
        }

        function addToGoogleCalendar(id) {
            const bookings = JSON.parse(localStorage.getItem('b_events')) || [];
            const b = bookings.find(x => x.id === id);
            if (!b) return;
            
            const title = encodeURIComponent(`${b.client_name || 'Klien'} - ${b.package_name || 'Event'}`);
            const details = encodeURIComponent(b.notes || '');
            const location = encodeURIComponent(b.client_address || '');
            
            let dates = '';
            if (b.event_date) {
                const d = new Date(b.event_date);
                const yyyy = d.getFullYear();
                const mm = String(d.getMonth() + 1).padStart(2, '0');
                const dd = String(d.getDate()).padStart(2, '0');
                const startDate = `${yyyy}${mm}${dd}`;
                
                const nextDay = new Date(d);
                nextDay.setDate(nextDay.getDate() + 1);
                const endYyyy = nextDay.getFullYear();
                const endMm = String(nextDay.getMonth() + 1).padStart(2, '0');
                const endDd = String(nextDay.getDate()).padStart(2, '0');
                const endDate = `${endYyyy}${endMm}${endDd}`;
                
                dates = `&dates=${startDate}/${endDate}`;
            }
            
            const url = `https://calendar.google.com/calendar/render?action=TEMPLATE&text=${title}${dates}&details=${details}&location=${location}`;
            window.open(url, '_blank');
        }

        function openGoogleCalendarSync() {
            document.getElementById('googleCalendarSyncModal').style.display = 'flex';
        }

        function closeGoogleCalendarSync() {
            document.getElementById('googleCalendarSyncModal').style.display = 'none';
        }

        function copyGcalSyncLink() {
            const copyText = document.getElementById("gcal_sync_link");
            copyText.select();
            copyText.setSelectionRange(0, 99999); 
            navigator.clipboard.writeText(copyText.value);
            showToast('Link kalender disalin', 'success');
        }
    </script>
</body>
</html>
