<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Keuangan - CloseMateAI</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
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

        /* Filter Row */
        .filter-row { display: flex; gap: 12px; margin-bottom: 16px; flex-wrap: wrap; align-items: center; }
        .filter-tabs { display: flex; gap: 8px; background: white; padding: 6px; border-radius: 30px; border: 1px solid var(--border-color); }
        .filter-tab { padding: 6px 16px; font-size: 13px; font-weight: 500; color: var(--text-muted); cursor: pointer; border-radius: 20px; transition: 0.2s; }
        .filter-tab:hover { color: var(--text-dark); }
        .filter-tab.active { background: var(--bg-light); color: var(--text-dark); border: 1px solid #e4e6ef; }
        
        .btn { padding: 10px 20px; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; transition: 0.2s; border: none; display: inline-flex; align-items: center; gap: 8px; }
        .btn-primary { background-color: var(--primary); color: white; }
        .btn-primary:hover { background-color: var(--primary-hover); }
        .btn-outline { background-color: white; border: 1px solid var(--border-color); color: var(--text-dark); }
        .btn-outline:hover { background-color: var(--bg-light); }
        .btn-date { background-color: var(--primary); color: white; border-radius: 30px; padding: 8px 20px; font-size: 13px; font-weight: 500; }

        .date-info { font-size: 13px; color: var(--text-muted); margin-bottom: 24px; font-weight: 500; }
        .date-info strong { color: var(--text-dark); }

        /* Stat Cards */
        .stats-grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: 16px; margin-bottom: 30px; }
        .stat-card { background: white; border: 1px solid var(--border-color); border-radius: 12px; padding: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.02); display: flex; flex-direction: column; gap: 12px; position: relative; }
        .stat-title { font-size: 11px; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; }
        .stat-value { font-size: 24px; font-weight: 700; color: var(--text-dark); }
        .stat-desc { font-size: 11px; color: var(--text-muted); line-height: 1.4; margin-top: auto; }
        
        .stat-icon { position: absolute; top: 16px; right: 16px; width: 32px; height: 32px; border-radius: 8px; display: flex; justify-content: center; align-items: center; font-size: 14px; background: var(--bg-light); color: var(--primary); }
        .stat-card.green .stat-value { color: var(--success); }
        .stat-card.red .stat-value { color: var(--danger); }
        .stat-card.green .stat-icon { background: rgba(80, 205, 137, 0.1); color: var(--success); }
        .stat-card.red .stat-icon { background: rgba(241, 65, 108, 0.1); color: var(--danger); }

        /* Content Sections */
        .content-section { background: white; border: 1px solid var(--border-color); border-radius: 12px; padding: 24px; box-shadow: 0 2px 10px rgba(0,0,0,0.02); margin-bottom: 24px; }
        .section-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; }
        .section-title h3 { font-size: 16px; font-weight: 600; color: var(--text-dark); margin-bottom: 4px; }
        .section-title p { font-size: 12px; color: var(--text-muted); }
        .section-meta { font-size: 12px; color: var(--text-muted); }

        /* Chart Placeholder */
        .chart-placeholder { height: 300px; display: flex; flex-direction: column; justify-content: space-between; position: relative; padding: 20px 0 20px 40px; margin-bottom: 20px; }
        .y-axis { position: absolute; left: 0; top: 20px; bottom: 20px; display: flex; flex-direction: column; justify-content: space-between; font-size: 11px; color: var(--text-muted); }
        .grid-line { width: 100%; border-top: 1px dashed var(--border-color); height: 0; }
        .grid-line.solid { border-top: 1px solid #ddd; }
        .x-axis { text-align: center; font-size: 11px; color: var(--text-muted); margin-top: 8px; }
        
        .chart-legend { display: flex; justify-content: center; gap: 24px; font-size: 12px; font-weight: 500; }
        .legend-item { display: flex; align-items: center; gap: 6px; }
        .legend-color { width: 12px; height: 12px; border-radius: 2px; }
        
        /* Table Styles */
        .data-table { width: 100%; border-collapse: collapse; }
        .data-table th { text-align: left; padding: 12px 16px; border-bottom: 1px solid var(--border-color); font-size: 12px; font-weight: 600; color: var(--text-muted); }
        .data-table td { padding: 16px; border-bottom: 1px solid var(--border-color); font-size: 13px; color: var(--text-dark); }
        
        .empty-row { text-align: center; padding: 40px !important; color: var(--text-muted); font-size: 13px; border-bottom: none !important; }
        
        .select-filter { padding: 8px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px; color: var(--text-dark); outline: none; background: white; cursor: pointer; }

        /* Modal Styles */
        .modal-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: none;
            justify-content: center; align-items: center;
            z-index: 1000;
        }
        .modal-content {
            background: white; border-radius: 12px; width: 500px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.15);
            display: flex; flex-direction: column; overflow: hidden;
        }
        .modal-header {
            padding: 20px 24px; border-bottom: 1px solid var(--border-color);
            display: flex; justify-content: space-between; align-items: center;
        }
        .modal-header h2 { font-size: 16px; font-weight: 600; margin-bottom: 4px; }
        .modal-header p { font-size: 12px; color: var(--text-muted); }
        .close-btn { font-size: 20px; color: var(--text-muted); cursor: pointer; }
        .modal-body { padding: 24px; }
        .form-group { margin-bottom: 16px; }
        .form-label { display: block; font-size: 13px; font-weight: 600; margin-bottom: 8px; }
        .form-control { width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px; outline: none; }
        .form-control:focus { border-color: var(--primary); }
        
        .action-btn { background: none; border: none; cursor: pointer; color: var(--text-muted); transition: 0.2s; font-size: 14px; display: inline-flex; justify-content: center; align-items: center; width: 28px; height: 28px; border-radius: 4px; }
        .action-btn:hover { background: var(--bg-light); color: var(--text-dark); }
        .action-btn.delete:hover { background: rgba(241, 65, 108, 0.1); color: var(--danger); }
        
        .chart-tooltip { position: absolute; left: 48%; top: 40%; background: white; border: 1px solid var(--border-color); border-radius: 12px; padding: 16px; box-shadow: 0 5px 20px rgba(0,0,0,0.08); z-index: 10; display: none; flex-direction: column; gap: 10px; font-size: 12px; pointer-events: none; }
        .chart-tooltip-title { font-weight: 600; color: var(--text-dark); margin-bottom: 2px; }
        .chart-interactive-area { position: absolute; top: 0; left: 0; right: 0; bottom: 0; z-index: 5; cursor: pointer; }
        .chart-interactive-area:hover ~ .chart-tooltip { display: flex; }
        
        /* Toasts */
        .toast-container { position: fixed; top: 20px; right: 20px; z-index: 9999; display: flex; flex-direction: column; gap: 10px; }
        .toast { background: white; border-left: 4px solid var(--success); padding: 12px 20px; border-radius: 4px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); font-size: 13px; font-weight: 500; display: flex; align-items: center; gap: 12px; animation: slideIn 0.3s ease; }
        @keyframes slideIn { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }

        /* Flatpickr Custom Theme */
        .flatpickr-calendar { box-shadow: 0 10px 40px rgba(0,0,0,0.1) !important; border: 1px solid var(--border-color) !important; border-radius: 12px !important; padding: 10px !important; }
        .flatpickr-day.selected, .flatpickr-day.startRange, .flatpickr-day.endRange, .flatpickr-day.selected.inRange, .flatpickr-day.startRange.inRange, .flatpickr-day.endRange.inRange, .flatpickr-day.selected:focus, .flatpickr-day.startRange:focus, .flatpickr-day.endRange:focus, .flatpickr-day.selected:hover, .flatpickr-day.startRange:hover, .flatpickr-day.endRange:hover, .flatpickr-day.selected.prevMonthDay, .flatpickr-day.startRange.prevMonthDay, .flatpickr-day.endRange.prevMonthDay, .flatpickr-day.selected.nextMonthDay, .flatpickr-day.startRange.nextMonthDay, .flatpickr-day.endRange.nextMonthDay { background: var(--primary) !important; border-color: var(--primary) !important; }
        .flatpickr-day.inRange, .flatpickr-day.prevMonthDay.inRange, .flatpickr-day.nextMonthDay.inRange, .flatpickr-day.today.inRange, .flatpickr-day.prevMonthDay.today.inRange, .flatpickr-day.nextMonthDay.today.inRange, .flatpickr-day:hover, .flatpickr-day.prevMonthDay:hover, .flatpickr-day.nextMonthDay:hover, .flatpickr-day:focus, .flatpickr-day.prevMonthDay:focus, .flatpickr-day.nextMonthDay:focus { background: rgba(107,92,216,0.1) !important; border-color: transparent !important; }
        
        .flatpickr-footer { display: flex; justify-content: space-between; align-items: center; padding: 12px 10px 4px; border-top: 1px solid var(--border-color); margin-top: 8px; }
        .flatpickr-footer-text { font-size: 12px; color: var(--text-muted); }
        .flatpickr-footer-btns { display: flex; gap: 8px; align-items: center; }
        .flatpickr-btn-reset { background: none; border: none; color: var(--text-dark); font-size: 13px; font-weight: 500; cursor: pointer; padding: 4px 8px; }
        .flatpickr-btn-apply { background: var(--primary); color: white; border: none; border-radius: 20px; font-size: 13px; font-weight: 500; padding: 6px 16px; cursor: pointer; }


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
                <h1>Laporan Keuangan & Cash Flow</h1>
                <p>Pantau omset, pengeluaran, dan profit bisnis kamu</p>
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

        <div class="filter-row">
            <div class="filter-tabs">
                <div class="filter-tab active" onclick="setFilter('Bulan Ini', this)">Bulan Ini</div>
                <div class="filter-tab" onclick="setFilter('Bulan Lalu', this)">Bulan Lalu</div>
                <div class="filter-tab" onclick="setFilter('30 Hari Terakhir', this)">30 Hari Terakhir</div>
                <div class="filter-tab" onclick="setFilter('Tahun Ini', this)">Tahun Ini</div>
            </div>
            <button class="btn btn-date" id="btn_custom_range_wrapper"><i class="fa-regular fa-calendar"></i> <span id="btn_custom_range">Custom Range</span></button>
        </div>
        
        <div class="filter-row" style="margin-bottom: 24px;">
            <button class="btn btn-outline" onclick="exportLaporanCSV()"><i class="fa-solid fa-download"></i> Export Laporan (CSV)</button>
            <button class="btn btn-primary" onclick="openExpenseModal()"><i class="fa-solid fa-plus"></i> Catat Pengeluaran Baru</button>
        </div>
        
        <div class="date-info">
            Menampilkan data: <strong id="report_period">27 September 2026 - 28 September 2026</strong>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-title">TOTAL OMSET</div>
                <div class="stat-value" id="val_omset">Rp 0</div>
                <div class="stat-icon"><i class="fa-solid fa-wallet"></i></div>
            </div>
            <div class="stat-card green">
                <div class="stat-title">UANG MASUK</div>
                <div class="stat-value" id="val_masuk">Rp 0</div>
                <div class="stat-icon"><i class="fa-solid fa-arrow-trend-up"></i></div>
            </div>
            <div class="stat-card">
                <div class="stat-title">SISA PIUTANG KLIEN</div>
                <div class="stat-value" id="val_piutang">Rp 0</div>
                <div class="stat-icon"><i class="fa-solid fa-hand-holding-dollar"></i></div>
            </div>
            <div class="stat-card red">
                <div class="stat-title">TOTAL PENGELUARAN</div>
                <div class="stat-value" id="val_pengeluaran">Rp 0</div>
                <div class="stat-desc">Pengeluaran manual + biaya operasional + vendor (HPP)</div>
                <div class="stat-icon"><i class="fa-solid fa-arrow-trend-down"></i></div>
            </div>
            <div class="stat-card green">
                <div class="stat-title">PROFIT BERSIH</div>
                <div class="stat-value" id="val_profit">Rp 0</div>
                <div class="stat-desc">Nilai paket dikurangi seluruh biaya acara</div>
                <div class="stat-icon"><i class="fa-solid fa-piggy-bank"></i></div>
            </div>
        </div>

        <div class="content-section">
            <div class="section-header">
                <div class="section-title">
                    <h3>Grafik Keuangan Bulanan</h3>
                    <p>Uang masuk vs pengeluaran vs net profit</p>
                </div>
                <div class="section-meta">Periode: 27 September 2026 - 28 September 2026</div>
            </div>
            
            <div class="chart-placeholder">
                <div class="y-axis">
                    <div>0jt -</div>
                    <div>0jt -</div>
                    <div>0jt -</div>
                    <div>0jt -</div>
                    <div>0jt -</div>
                </div>
                <div class="grid-line"></div>
                <div class="grid-line"></div>
                <div class="grid-line"></div>
                <div class="grid-line"></div>
                <div class="grid-line solid"></div>
                
                <div style="position: absolute; left: 40px; right: 0; bottom: 20px; top: 20px; display: flex; flex-direction: column; justify-content: flex-end; align-items: center;">
                    <div style="position: relative; width: 100%; height: 100%;">
                        <!-- Uang Masuk Line -->
                        <div id="chart_line_masuk" style="position: absolute; bottom: 0%; left: 10%; width: 40%; height: 2px; background: var(--success);"></div>
                        
                        <!-- Pengeluaran Bar -->
                        <div id="chart_bar_keluar" style="position: absolute; bottom: 0; left: 50%; width: 40%; height: 0%; background: #dc2626; border-radius: 4px 4px 0 0;"></div>
                        
                        <!-- Net Profit Line -->
                        <div id="chart_line_profit" style="position: absolute; bottom: 0%; left: 50%; width: 40%; height: 0px; border-top: 1px dashed #3b82f6;"></div>
                        <div id="chart_dot_profit" style="position: absolute; bottom: 0%; left: 50%; width: 8px; height: 8px; border-radius: 50%; border: 2px solid #3b82f6; background: white; transform: translate(-50%, 4px);"></div>
                    </div>
                    
                    <div class="chart-interactive-area"></div>
                    <div class="chart-tooltip" id="chart_tooltip">
                        <div class="chart-tooltip-title" id="tooltip_title">Okt 26</div>
                        <div style="color: var(--success);">Uang Masuk: <span id="tooltip_masuk">Rp 0</span></div>
                        <div style="color: #dc2626;">Pengeluaran: <span id="tooltip_keluar">Rp 0</span></div>
                        <div style="color: #3b82f6;">Net Profit: <span id="tooltip_profit">Rp 0</span></div>
                    </div>
                </div>
                
            </div>
            <div class="x-axis">Sep 26</div>
            
            <div class="chart-legend">
                <div class="legend-item">
                    <div class="legend-color" style="background: var(--success);"></div>
                    <span style="color: var(--success);">Uang Masuk</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color" style="background: var(--danger);"></div>
                    <span style="color: var(--danger);">Pengeluaran</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color" style="border: 2px solid #3b82f6; width: 14px; height: 4px; border-radius: 4px; background: white; position: relative;">
                        <div style="position: absolute; width: 4px; height: 4px; background: #3b82f6; border-radius: 50%; top: -2px; left: 3px;"></div>
                    </div>
                    <span style="color: #3b82f6;">Net Profit</span>
                </div>
            </div>
        </div>

        <div class="content-section">
            <div class="section-header">
                <div class="section-title">
                    <h3>Riwayat Pengeluaran</h3>
                </div>
                <select class="select-filter" id="filter_kategori" onchange="calculateFinance()">
                    <option value="Semua Kategori">Semua Kategori</option>
                    <option value="Fee Tim">Fee Tim</option>
                    <option value="Sewa Alat">Sewa Alat</option>
                    <option value="Transport">Transport</option>
                    <option value="Akomodasi">Akomodasi</option>
                    <option value="Cetak & Album">Cetak & Album</option>
                    <option value="Marketing">Marketing</option>
                    <option value="Operasional">Operasional</option>
                    <option value="Lainnya">Lainnya</option>
                </select>
            </div>
            
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Judul</th>
                        <th>Kategori</th>
                        <th>Acara</th>
                        <th>Nominal</th>
                    </tr>
                </thead>
                <tbody id="expense_table_body">
                    <tr>
                        <td colspan="5" class="empty-row">Belum ada pengeluaran pada periode ini.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="content-section">
            <div class="section-header">
                <div class="section-title">
                    <h3>Profitabilitas per Acara</h3>
                    <p>Performa keuangan tiap proyek booking</p>
                </div>
            </div>
            
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Klien</th>
                        <th>Tanggal Acara</th>
                        <th>Paket</th>
                        <th>Harga Paket</th>
                        <th>Total Biaya</th>
                        <th>Profit Margin</th>
                    </tr>
                </thead>
                <tbody id="profit_table_body">
                    <tr>
                        <td colspan="6" class="empty-row">Belum ada booking untuk dihitung.</td>
                    </tr>
                </tbody>
            </table>
        </div>

    </div>

    <!-- Catat Pengeluaran Baru Modal -->
    <div class="modal-overlay" id="expenseModal">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h2 id="modal_title" style="font-size: 18px; font-weight: 600; margin-bottom: 4px;">Catat Pengeluaran Baru</h2>
                    <p style="font-size: 13px; color: var(--text-muted); margin: 0;">Catat biaya operasional agar profit per acara terhitung otomatis.</p>
                </div>
                <button onclick="closeExpenseModal()" style="background: none; border: none; font-size: 20px; color: var(--text-muted); cursor: pointer;"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="exp_id" value="">
                <div class="form-group">
                    <label class="form-label">Acara (opsional)</label>
                    <select id="exp_acara" class="form-control">
                        <option value="">— Pengeluaran umum —</option>
                    </select>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div class="form-group">
                        <label class="form-label">Kategori</label>
                        <select id="exp_kategori" class="form-control">
                            <option>Fee Tim</option>
                            <option>Sewa Alat</option>
                            <option>Transport</option>
                            <option>Akomodasi</option>
                            <option>Cetak & Album</option>
                            <option>Marketing</option>
                            <option>Operasional</option>
                            <option>Lainnya</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tanggal</label>
                        <input type="date" id="exp_tanggal" class="form-control">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Judul Pengeluaran</label>
                    <input type="text" id="exp_judul" class="form-control" placeholder="Fee fotografer, sewa lensa, transport...">
                </div>

                <div class="form-group">
                    <label class="form-label">Nominal (Rp)</label>
                    <input type="text" id="exp_nominal" class="form-control" placeholder="0" oninput="formatRupiahInput(this)">
                </div>

                <div class="form-group" style="margin-bottom: 24px;">
                    <label class="form-label">Catatan</label>
                    <textarea id="exp_catatan" class="form-control" rows="3" placeholder=""></textarea>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 12px;">
                    <button class="btn btn-outline" onclick="closeExpenseModal()">Batal</button>
                    <button class="btn btn-primary" onclick="saveExpense()">Simpan</button>
                </div>
            </div>
        </div>
    </div>

    <div class="toast-container" id="toast-container"></div>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    
    <script>
        let currentFilterStart = null;
        let currentFilterEnd = null;
        let activeFilterType = 'Bulan Ini';
        let globalBookingsFinance = [];

        async function fetchBookingsFinance() {
            try {
                const res = await fetch('/api/bookings');
                if (res.ok) {
                    globalBookingsFinance = await res.json();
                    calculateFinance();
                }
            } catch(e) {
                console.error("Error fetching bookings for finance:", e);
            }
        }

        // Sidebar Collapse
        document.getElementById('btn-collapse').addEventListener('click', function() {
            document.body.classList.toggle('sidebar-collapsed');
        });

        function showToast(message, type = 'success') {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            toast.className = 'toast';
            toast.innerHTML = `<i class="fa-solid fa-circle-check"></i> ${message}`;
            container.appendChild(toast);
            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateX(100%)';
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }

        function formatRupiah(number) {
            return new Intl.NumberFormat('id-ID').format(number);
        }

        function parseRupiah(str) {
            if (!str) return 0;
            return parseInt(str.replace(/[^0-9]/g, ''), 10) || 0;
        }

        function formatRupiahInput(input) {
            let val = parseRupiah(input.value);
            if (val === 0) {
                input.value = '';
            } else {
                input.value = formatRupiah(val);
            }
        }

        function openExpenseModal() {
            const bookings = globalBookingsFinance;
            const select = document.getElementById('exp_acara');
            let html = `<option value="">— Pengeluaran umum —</option>`;
            bookings.forEach(b => {
                html += `<option value="${b.id}">${b.client_name} · ${b.event_date || '-'}</option>`;
            });
            select.innerHTML = html;
            
            document.getElementById('modal_title').innerText = "Catat Pengeluaran Baru";
            document.getElementById('exp_id').value = '';
            document.getElementById('exp_acara').value = '';
            document.getElementById('exp_tanggal').valueAsDate = new Date();
            document.getElementById('exp_kategori').value = 'Fee Tim';
            document.getElementById('exp_judul').value = '';
            document.getElementById('exp_nominal').value = '';
            document.getElementById('exp_catatan').value = '';
            
            document.getElementById('expenseModal').style.display = 'flex';
        }

        function closeExpenseModal() {
            document.getElementById('expenseModal').style.display = 'none';
        }

        function saveExpense() {
            const id = document.getElementById('exp_id').value;
            const acaraId = document.getElementById('exp_acara').value;
            const kategori = document.getElementById('exp_kategori').value;
            const tanggal = document.getElementById('exp_tanggal').value;
            const judul = document.getElementById('exp_judul').value;
            const nominal = parseRupiah(document.getElementById('exp_nominal').value);
            const catatan = document.getElementById('exp_catatan').value;
            
            if (!judul || !nominal) {
                alert("Judul dan nominal wajib diisi!");
                return;
            }
            
            let expenses = JSON.parse(localStorage.getItem('f_expenses')) || [];
            
            if (id) {
                const index = expenses.findIndex(e => e.id === id);
                if (index > -1) {
                    expenses[index] = { ...expenses[index], acaraId, kategori, tanggal, judul, nominal, catatan };
                    showToast('Pengeluaran diperbarui');
                }
            } else {
                const exp = {
                    id: 'exp_' + Date.now(),
                    acaraId, kategori, tanggal, judul, nominal, catatan
                };
                expenses.push(exp);
                showToast('Pengeluaran dicatat');
            }
            
            localStorage.setItem('f_expenses', JSON.stringify(expenses));
            
            if(window.logSysActivity && !id) {
                window.logSysActivity('Handler', 'Penapict', 'Mencatat pengeluaran', judul + ' (Rp ' + nominal.toLocaleString('id-ID') + ')', 'fa-money-bill-wave', 'grey');
            }
            
            closeExpenseModal();
            calculateFinance();
        }

        function calculateFinance() {
            let allBookings = globalBookingsFinance;
            let allExpenses = JSON.parse(localStorage.getItem('f_expenses')) || [];
            
            // Filter by date
            const filterStart = currentFilterStart ? new Date(currentFilterStart) : null;
            const filterEnd = currentFilterEnd ? new Date(currentFilterEnd) : null;
            
            const bookings = allBookings.filter(b => {
                if(!b.event_date) return true;
                const d = new Date(b.event_date);
                if(filterStart && d < filterStart) return false;
                if(filterEnd && d > filterEnd) return false;
                return true;
            });
            
            const expenses = allExpenses.filter(e => {
                if(!e.tanggal) return true;
                const d = new Date(e.tanggal);
                if(filterStart && d < filterStart) return false;
                if(filterEnd && d > filterEnd) return false;
                return true;
            });
            
            let totalOmset = 0;
            let uangMasuk = 0;
            let pengeluaranManual = 0;
            let pengeluaranOperasional = 0;
            
            bookings.forEach(b => {
                totalOmset += (b.total_income || 0);
                uangMasuk += (b.paid_amount || 0);
                pengeluaranOperasional += (b.total_operational_cost || 0);
            });
            
            expenses.forEach(e => {
                pengeluaranManual += (e.nominal || 0);
            });
            
            const sisaPiutang = totalOmset - uangMasuk;
            const totalPengeluaran = pengeluaranOperasional + pengeluaranManual;
            const profitBersih = totalOmset - totalPengeluaran;
            
            document.getElementById('val_omset').innerText = 'Rp ' + formatRupiah(totalOmset);
            document.getElementById('val_masuk').innerText = 'Rp ' + formatRupiah(uangMasuk);
            document.getElementById('val_piutang').innerText = 'Rp ' + formatRupiah(sisaPiutang > 0 ? sisaPiutang : 0);
            document.getElementById('val_pengeluaran').innerText = 'Rp ' + formatRupiah(totalPengeluaran);
            
            const profitEl = document.getElementById('val_profit');
            profitEl.innerText = 'Rp ' + formatRupiah(profitBersih);
            if (profitBersih < 0) {
                profitEl.style.color = 'var(--danger)';
                profitEl.parentElement.classList.remove('green');
                profitEl.parentElement.classList.add('red');
            } else {
                profitEl.style.color = 'var(--success)';
                profitEl.parentElement.classList.remove('red');
                profitEl.parentElement.classList.add('green');
            }
            
            const expTbody = document.getElementById('expense_table_body');
            const filterKategori = document.getElementById('filter_kategori').value;
            let filteredExpenses = expenses;
            if (filterKategori !== 'Semua Kategori') {
                filteredExpenses = expenses.filter(e => e.kategori === filterKategori);
            }
            
            if (filteredExpenses.length > 0) {
                let expHtml = '';
                [...filteredExpenses].reverse().forEach(e => {
                    let acaraName = '-';
                    if (e.acaraId) {
                        const b = allBookings.find(x => x.id === e.acaraId);
                        if (b) acaraName = `<span style="background: rgba(107,92,216,0.1); color: var(--primary); padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 500;">${b.client_name}</span>`;
                    }
                    expHtml += `
                    <tr>
                        <td>${e.tanggal || '-'}</td>
                        <td style="font-weight: 500;">${e.judul}</td>
                        <td><span style="color: var(--text-muted); font-size: 12px;">${e.kategori}</span></td>
                        <td>${acaraName}</td>
                        <td style="color: var(--danger); font-weight: 600; display: flex; align-items: center; justify-content: space-between;">
                            - Rp ${formatRupiah(e.nominal)}
                            <div>
                                <button class="action-btn" onclick="editExpense('${e.id}')"><i class="fa-solid fa-pen"></i></button>
                                <button class="action-btn delete" onclick="deleteExpense('${e.id}')"><i class="fa-regular fa-trash-can"></i></button>
                            </div>
                        </td>
                    </tr>
                    `;
                });
                expTbody.innerHTML = expHtml;
            } else {
                expTbody.innerHTML = `<tr><td colspan="5" class="empty-row">Belum ada pengeluaran pada periode ini.</td></tr>`;
            }
            
            const profTbody = document.getElementById('profit_table_body');
            if (bookings.length > 0) {
                let profHtml = '';
                bookings.forEach(b => {
                    const linkedExpenses = expenses.filter(e => e.acaraId === b.id).reduce((sum, e) => sum + (e.nominal || 0), 0);
                    const bookingCost = (b.total_operational_cost || 0) + linkedExpenses;
                    const bookingIncome = b.total_income || 0;
                    const bookingProfit = bookingIncome - bookingCost;
                    
                    let profitColor = 'var(--success)';
                    if (bookingProfit < 0) profitColor = 'var(--danger)';
                    
                    profHtml += `
                    <tr>
                        <td style="font-weight: 600;">${b.client_name || '-'}</td>
                        <td style="color: var(--text-muted);">${b.event_date || '-'}</td>
                        <td>${b.package_name || '-'}</td>
                        <td>Rp ${formatRupiah(bookingIncome)}</td>
                        <td style="color: var(--danger);">Rp ${formatRupiah(bookingCost)}</td>
                        <td style="color: ${profitColor}; font-weight: 600;">Rp ${formatRupiah(bookingProfit)}</td>
                    </tr>
                    `;
                });
                profTbody.innerHTML = profHtml;
            } else {
                profTbody.innerHTML = `<tr><td colspan="6" class="empty-row">Belum ada booking untuk dihitung.</td></tr>`;
            }
            
            // Update Tooltip
            document.getElementById('tooltip_masuk').innerText = 'Rp ' + formatRupiah(uangMasuk);
            document.getElementById('tooltip_keluar').innerText = 'Rp ' + formatRupiah(totalPengeluaran);
            document.getElementById('tooltip_profit').innerText = (profitBersih < 0 ? '-' : '') + 'Rp ' + formatRupiah(Math.abs(profitBersih));
            
            const today = new Date();
            const formatter = new Intl.DateTimeFormat('id-ID', { month: 'short', year: '2-digit' });
            const shortPeriod = formatter.format(today);
            document.getElementById('tooltip_title').innerText = shortPeriod;
            document.querySelector('.x-axis').innerText = shortPeriod;
            
            // Basic chart update mockup
            const chartProfit = uangMasuk - totalPengeluaran; // Cash flow profit
            let max = Math.max(uangMasuk, totalPengeluaran, Math.abs(chartProfit));
            if (max === 0) max = 1;
            max = max * 1.2; // Add 20% padding at top so bars aren't touching the very edge
            
            let masukPct = Math.min((uangMasuk / max) * 100, 100);
            let keluarPct = Math.min((totalPengeluaran / max) * 100, 100);
            let profitPct = Math.min((Math.abs(chartProfit) / max) * 100, 100);
            
            document.getElementById('chart_line_masuk').style.bottom = `${masukPct}%`;
            document.getElementById('chart_bar_keluar').style.height = `${keluarPct}%`;
            
            const dotProfit = document.getElementById('chart_dot_profit');
            const lineProfit = document.getElementById('chart_line_profit');
            
            if (chartProfit < 0) {
                dotProfit.style.bottom = `0%`;
                lineProfit.style.bottom = `0%`;
                dotProfit.style.borderColor = `var(--danger)`;
                lineProfit.style.borderTopColor = `var(--danger)`;
            } else {
                dotProfit.style.bottom = `${profitPct}%`;
                lineProfit.style.bottom = `${profitPct}%`;
                dotProfit.style.borderColor = `#3b82f6`;
                lineProfit.style.borderTopColor = `#3b82f6`;
            }
        }

        function editExpense(id) {
            const expenses = JSON.parse(localStorage.getItem('f_expenses')) || [];
            const e = expenses.find(x => x.id === id);
            if (!e) return;
            
            const bookings = JSON.parse(localStorage.getItem('b_events')) || [];
            const select = document.getElementById('exp_acara');
            let html = `<option value="">— Pengeluaran umum —</option>`;
            bookings.forEach(b => {
                html += `<option value="${b.id}">${b.client_name} · ${b.event_date || '-'}</option>`;
            });
            select.innerHTML = html;
            
            document.getElementById('modal_title').innerText = "Edit Pengeluaran";
            document.getElementById('exp_id').value = e.id;
            document.getElementById('exp_acara').value = e.acaraId || '';
            document.getElementById('exp_tanggal').value = e.tanggal || '';
            document.getElementById('exp_kategori').value = e.kategori || 'Fee Tim';
            document.getElementById('exp_judul').value = e.judul || '';
            document.getElementById('exp_nominal').value = formatRupiah(e.nominal || 0);
            document.getElementById('exp_catatan').value = e.catatan || '';
            
            document.getElementById('expenseModal').style.display = 'flex';
        }
        
        function deleteExpense(id) {
            if(confirm("Apakah Anda yakin ingin menghapus pengeluaran ini?")) {
                let expenses = JSON.parse(localStorage.getItem('f_expenses')) || [];
                expenses = expenses.filter(e => e.id !== id);
                localStorage.setItem('f_expenses', JSON.stringify(expenses));
                showToast("Pengeluaran dihapus");
                calculateFinance();
            }
        }

        function setFilter(type, element) {
            document.querySelectorAll('.filter-tab').forEach(el => el.classList.remove('active'));
            if(element) element.classList.add('active');
            activeFilterType = type;
            
            // clear flatpickr if we are using tabs
            if (fpInstance && type !== 'Custom') {
                fpInstance.clear();
            }
            
            const today = new Date();
            let start, end;
            
            if (type === 'Bulan Ini') {
                start = new Date(today.getFullYear(), today.getMonth(), 1);
                end = new Date(today.getFullYear(), today.getMonth() + 1, 0);
            } else if (type === 'Bulan Lalu') {
                start = new Date(today.getFullYear(), today.getMonth() - 1, 1);
                end = new Date(today.getFullYear(), today.getMonth(), 0);
            } else if (type === '30 Hari Terakhir') {
                start = new Date(today.getTime() - (30 * 24 * 60 * 60 * 1000));
                end = today;
            } else if (type === 'Tahun Ini') {
                start = new Date(today.getFullYear(), 0, 1);
                end = new Date(today.getFullYear(), 11, 31);
            }
            
            currentFilterStart = start;
            currentFilterEnd = end;
            updateDateText();
            calculateFinance();
        }

        function updateDateText() {
            const formatter = new Intl.DateTimeFormat('id-ID', { day: '2-digit', month: 'long', year: 'numeric' });
            if (currentFilterStart && currentFilterEnd) {
                const s = formatter.format(currentFilterStart);
                const e = formatter.format(currentFilterEnd);
                const text = `${s} - ${e}`;
                document.getElementById('report_period').innerText = text;
                const chartPeriodTitle = document.querySelector('.section-meta');
                if (chartPeriodTitle) chartPeriodTitle.innerText = `Periode: ${text}`;
                
                if (activeFilterType === 'Custom') {
                    document.getElementById('btn_custom_range').innerText = text;
                } else {
                    document.getElementById('btn_custom_range').innerText = "Custom Range";
                }
            }
        }

        function exportLaporanCSV() {
            // Get all current variables from calculateFinance logic
            let allBookings = JSON.parse(localStorage.getItem('b_events')) || [];
            let allExpenses = JSON.parse(localStorage.getItem('f_expenses')) || [];
            
            const filterStart = currentFilterStart ? new Date(currentFilterStart) : null;
            const filterEnd = currentFilterEnd ? new Date(currentFilterEnd) : null;
            
            const bookings = allBookings.filter(b => {
                if(!b.event_date) return true;
                const d = new Date(b.event_date);
                if(filterStart && d < filterStart) return false;
                if(filterEnd && d > filterEnd) return false;
                return true;
            });
            
            const expenses = allExpenses.filter(e => {
                if(!e.tanggal) return true;
                const d = new Date(e.tanggal);
                if(filterStart && d < filterStart) return false;
                if(filterEnd && d > filterEnd) return false;
                return true;
            });
            
            let totalOmset = 0;
            let uangMasuk = 0;
            let pengeluaranOperasional = 0;
            let pengeluaranManual = 0;
            
            bookings.forEach(b => {
                totalOmset += (b.total_income || 0);
                uangMasuk += (b.paid_amount || 0);
                pengeluaranOperasional += (b.total_operational_cost || 0);
            });
            expenses.forEach(e => pengeluaranManual += (e.nominal || 0));
            
            const sisaPiutang = totalOmset - uangMasuk;
            const totalPengeluaran = pengeluaranOperasional + pengeluaranManual;
            const profitBersih = totalOmset - totalPengeluaran;
            const chartProfit = uangMasuk - totalPengeluaran;
            
            const formatter = new Intl.DateTimeFormat('id-ID', { month: 'short', year: '2-digit' });
            const monthStr = formatter.format(currentFilterStart || new Date());
            
            let csv = "RINGKASAN KEUANGAN\n";
            csv += `Total Omset,${totalOmset}\n`;
            csv += `Uang Masuk,${uangMasuk}\n`;
            csv += `Sisa Piutang,${sisaPiutang > 0 ? sisaPiutang : 0}\n`;
            csv += `Total Pengeluaran,${totalPengeluaran}\n`;
            csv += `Profit Bersih,${profitBersih}\n\n`;
            
            csv += "CASH FLOW BULANAN\n";
            csv += "Bulan,Uang Masuk,Pengeluaran,Net Profit\n";
            csv += `${monthStr},${uangMasuk},${totalPengeluaran},${chartProfit}\n\n`;
            
            csv += "RIWAYAT PENGELUARAN\n";
            csv += "Tanggal,Kategori,Judul,Acara,Nominal,Catatan\n";
            expenses.forEach(e => {
                let acaraName = '';
                if(e.acaraId) {
                    const b = allBookings.find(x => x.id === e.acaraId);
                    if(b) acaraName = b.client_name;
                }
                const cat = e.catatan ? e.catatan.replace(/,/g, ' ') : '';
                csv += `${e.tanggal || ''},${e.kategori},${e.judul},${acaraName},${e.nominal},${cat}\n`;
            });
            csv += "\n";
            
            csv += "PROFITABILITAS PER ACARA\n";
            csv += "Klien,Tanggal Acara,Paket,Harga Paket,Total Biaya,Profit Margin\n";
            bookings.forEach(b => {
                const linkedExpenses = expenses.filter(e => e.acaraId === b.id).reduce((sum, e) => sum + (e.nominal || 0), 0);
                const bookingCost = (b.total_operational_cost || 0) + linkedExpenses;
                const bookingIncome = b.total_income || 0;
                const bookingProfit = bookingIncome - bookingCost;
                csv += `${b.client_name || ''},${b.event_date || ''},${b.package_name || ''},${bookingIncome},${bookingCost},${bookingProfit}\n`;
            });
            
            const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement("a");
            const url = URL.createObjectURL(blob);
            link.setAttribute("href", url);
            const yyyymmdd = new Date().toISOString().split('T')[0];
            link.setAttribute("download", `laporan-keuangan-${yyyymmdd}.csv`);
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            
            showToast("Laporan diunduh");
        }

        let fpInstance = null;

        document.addEventListener('DOMContentLoaded', () => {
            fetchBookingsFinance();
            setFilter('Bulan Ini', document.querySelector('.filter-tab.active'));
            
            fpInstance = flatpickr("#btn_custom_range_wrapper", {
                mode: "range",
                showMonths: 2,
                dateFormat: "Y-m-d",
                closeOnSelect: false,
                onReady: function(selectedDates, dateStr, instance) {
                    const footer = document.createElement("div");
                    footer.className = "flatpickr-footer";
                    footer.innerHTML = `
                        <div class="flatpickr-footer-text" id="fp_range_text">Pilih rentang tanggal</div>
                        <div class="flatpickr-footer-btns">
                            <button class="flatpickr-btn-reset" type="button">Reset</button>
                            <button class="flatpickr-btn-apply" type="button">Terapkan</button>
                        </div>
                    `;
                    instance.calendarContainer.appendChild(footer);
                    
                    footer.querySelector('.flatpickr-btn-reset').addEventListener('click', () => {
                        instance.clear();
                        document.getElementById('fp_range_text').innerText = 'Pilih rentang tanggal';
                    });
                    
                    footer.querySelector('.flatpickr-btn-apply').addEventListener('click', () => {
                        if (instance.selectedDates.length === 2) {
                            currentFilterStart = instance.selectedDates[0];
                            currentFilterEnd = instance.selectedDates[1];
                            document.querySelectorAll('.filter-tab').forEach(el => el.classList.remove('active'));
                            activeFilterType = 'Custom';
                            updateDateText();
                            calculateFinance();
                            instance.close();
                        } else {
                            alert("Harap pilih tanggal mulai dan selesai.");
                        }
                    });
                },
                onChange: function(selectedDates, dateStr, instance) {
                    if (selectedDates.length === 2) {
                        const formatter = new Intl.DateTimeFormat('id-ID', { day: '2-digit', month: 'long', year: 'numeric' });
                        const s = formatter.format(selectedDates[0]);
                        const e = formatter.format(selectedDates[1]);
                        document.getElementById('fp_range_text').innerText = `${s} - ${e}`;
                    } else if (selectedDates.length === 1) {
                        const formatter = new Intl.DateTimeFormat('id-ID', { day: '2-digit', month: 'long', year: 'numeric' });
                        document.getElementById('fp_range_text').innerText = `${formatter.format(selectedDates[0])} - ...`;
                    }
                }
            });
        });
    </script>
    <script src="/js/activity-logger.js"></script>
</body>
</html>
