<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Laporan Keuangan - CloseMateAI</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    @include('components.theme-styles')
    <style>
        :root {
            --sidebar-bg: #1e1e2d;
            --sidebar-text: #cbd5e1;
            --sidebar-active-bg: {{ auth()->check() ? auth()->user()->getThemeColor() : '#4472c4' }}26;
            --sidebar-active-text: {{ auth()->check() ? auth()->user()->getThemeColor() : '#4472c4' }};
            --primary: {{ auth()->check() ? auth()->user()->getThemeColor() : '#4472c4' }};
            --primary-hover: {{ auth()->check() ? auth()->user()->getThemeColor() : '#4472c4' }};
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
    <link rel="stylesheet" href="/css/responsive.css">
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-logo-icon">
                <i class="fa-solid fa-heart"></i>
            </div>
            <div class="sidebar-title">
                <h2>{{ auth()->check() ? (auth()->user()->getEffectiveOwner()->business_name ?: auth()->user()->getEffectiveOwner()->name) : 'CloseMateAI' }}</h2>
                <p>AI Bot Console</p>
            </div>
        </div>
        
        @include('components.sidebar-nav')
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="topbar">
            <button class="mobile-menu-btn" style="display:none; background:none; border:none; font-size:20px; cursor:pointer; margin-right:16px; color:var(--text-dark);"><i class="fa-solid fa-bars"></i></button>
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
        
        <div class="filter-row" style="margin-bottom: 24px; display: flex; flex-wrap: wrap; gap: 10px;">
            <button class="btn btn-outline" onclick="exportLaporanCSV()" style="font-weight: 600; display: inline-flex; align-items: center; gap: 8px;"><i class="fa-solid fa-file-excel" style="color: #10b981; font-size: 1.1rem;"></i> Export Laporan CSV</button>
            <button class="btn btn-outline" onclick="openMasterModal()" style="font-weight: 600; display: inline-flex; align-items: center; gap: 8px;"><i class="fa-solid fa-sliders" style="color: #6366f1;"></i> Master Biaya &amp; Gaji</button>
            <button class="btn btn-primary" onclick="openExpenseModal()"><i class="fa-solid fa-plus"></i> Catat Pengeluaran Baru</button>
        </div>
        
        <div class="date-info">
            Menampilkan data: <strong id="report_period">27 September 2026 - 28 September 2026</strong>
        </div>

        <!-- Analytical Report Modules Navigation -->
        <div style="display: flex; gap: 10px; margin-bottom: 24px; overflow-x: auto; padding-bottom: 4px;">
            <button class="report-view-tab" id="tab_btn_overview" onclick="switchReportTab('overview')" style="padding: 10px 18px; border-radius: 10px; border: none; font-size: 13px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 8px; background: var(--primary); color: white; white-space: nowrap;">
                <i class="fa-solid fa-chart-pie"></i> Arus Kas &amp; Finansial
            </button>
            <button class="report-view-tab" id="tab_btn_team" onclick="switchReportTab('team')" style="padding: 10px 18px; border-radius: 10px; border: 1px solid #e2e8f0; font-size: 13px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 8px; background: white; color: var(--text-dark); white-space: nowrap;">
                <i class="fa-solid fa-users-gear"></i> 1. Tim &amp; Gaji (Kinerja Karyawan)
            </button>
            <button class="report-view-tab" id="tab_btn_packages" onclick="switchReportTab('packages')" style="padding: 10px 18px; border-radius: 10px; border: 1px solid #e2e8f0; font-size: 13px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 8px; background: white; color: var(--text-dark); white-space: nowrap;">
                <i class="fa-solid fa-camera"></i> 2. Paket &amp; Penjualan (Performa Produk)
            </button>
            <button class="report-view-tab" id="tab_btn_operations" onclick="switchReportTab('operations')" style="padding: 10px 18px; border-radius: 10px; border: 1px solid #e2e8f0; font-size: 13px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 8px; background: white; color: var(--text-dark); white-space: nowrap;">
                <i class="fa-solid fa-store"></i> 3. Keuangan &amp; Operasional Studio
            </button>
        </div>

        <div id="panel_report_overview">
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
                <div class="y-axis" id="chart_y_axis">
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

        </div> <!-- End panel_report_overview -->

        <!-- PANEL 1: LAPORAN TIM & GAJI (KINERJA KARYAWAN) -->
        <div id="panel_report_team" style="display: none;">
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-title">TOTAL CREW &amp; TIM</div>
                    <div class="stat-value" id="stat_team_crew">9 Orang</div>
                    <div class="stat-desc">FG, VG, Editor, Cetak, Admin &amp; Marketing</div>
                    <div class="stat-icon"><i class="fa-solid fa-users"></i></div>
                </div>
                <div class="stat-card green">
                    <div class="stat-title">TOTAL GAJI &amp; KOMISI</div>
                    <div class="stat-value" id="stat_team_salary">Rp 42.100.000</div>
                    <div class="stat-desc">Gaji Pokok + Uang Jalan + Lembur + Bonus</div>
                    <div class="stat-icon"><i class="fa-solid fa-money-check-dollar"></i></div>
                </div>
                <div class="stat-card">
                    <div class="stat-title">RATA-RATA RATING KRU</div>
                    <div class="stat-value" id="stat_team_rating" style="color: #f59e0b;"><i class="fa-solid fa-star"></i> 4.9 <span style="font-size: 14px; color: var(--text-muted); font-weight: 500;">/ 5.0</span></div>
                    <div class="stat-desc">Dari ulasan kepuasan klien lapangan</div>
                    <div class="stat-icon"><i class="fa-regular fa-star"></i></div>
                </div>
                <div class="stat-card green">
                    <div class="stat-title">CLOSING MARKETING</div>
                    <div class="stat-value" id="stat_team_closing">48 Leads</div>
                    <div class="stat-desc">Closing dari leads WhatsApp &amp; Sales</div>
                    <div class="stat-icon"><i class="fa-solid fa-bullseye"></i></div>
                </div>
            </div>

            <!-- Rincian Pendapatan & Beban Kerja Kru -->
            <div class="content-section">
                <div class="section-header">
                    <div class="section-title">
                        <h3>1. Rincian Pendapatan &amp; Beban Kerja Kru</h3>
                        <p>Pemisahan gaji pokok, operasional jalan, lembur, bonus proyek, dan total sesi foto sebulan</p>
                    </div>
                    <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                        <button class="btn btn-outline" onclick="exportTeamLaporanCsv()" style="font-size: 12px; display: inline-flex; align-items: center; gap: 6px;">
                            <i class="fa-solid fa-file-excel" style="color: #10b981;"></i> Ekspor Payroll CSV
                        </button>
                        <button class="btn btn-outline" onclick="printAllCrewRecap()" style="font-size: 12px; display: inline-flex; align-items: center; gap: 6px;">
                            <i class="fa-solid fa-print"></i> Cetak Rekap Gaji Bulanan
                        </button>
                    </div>
                </div>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Nama Kru</th>
                            <th>Divisi</th>
                            <th style="text-align: center;">Beban Sesi (Event)</th>
                            <th style="text-align: right;">Gaji Pokok</th>
                            <th style="text-align: right;">Uang Jalan / Makan</th>
                            <th style="text-align: right;">Uang Lembur</th>
                            <th style="text-align: right;">Bonus / Fee Proyek</th>
                            <th style="text-align: right;">Total Gaji Bersih</th>
                            <th style="text-align: center; width: 120px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tbody_team_crews">
                    </tbody>
                </table>
            </div>

            <!-- Kecepatan & Ketepatan Kerja Tim Editor & Cetak -->
            <div class="content-section">
                <div class="section-header">
                    <div class="section-title">
                        <h3>2. Kecepatan &amp; Kualitas Kerja Tim Editor &amp; Cetak</h3>
                        <p>Pemantauan Same Day Edit, Fast Edit, Deadline Cetak, dan Waktu Siap Diambil Klien</p>
                    </div>
                    <div style="display: flex; gap: 8px;">
                        <button class="btn btn-primary" onclick="openOfficeEvalModal()" style="font-size: 12px;"><i class="fa-solid fa-plus"></i> Catat Evaluasi Kantor</button>
                        <a href="/api/office-evaluations/export/csv" class="btn btn-outline" style="font-size: 12px;"><i class="fa-solid fa-download"></i> Unduh CSV Evaluasi</a>
                    </div>
                </div>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Editor</th>
                            <th>Staff Cetak</th>
                            <th>Layanan</th>
                            <th>Durasi Selesai</th>
                            <th>Ketepatan Waktu</th>
                            <th>Status Cetak &amp; Ambil</th>
                            <th style="text-align: right;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tbody_office_evals">
                    </tbody>
                </table>
            </div>

            <!-- Kepuasan Klien Lapangan & Feedback Form -->
            <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 20px; margin-bottom: 24px;">
                <div class="content-section" style="margin-bottom: 0;">
                    <div class="section-title" style="margin-bottom: 16px;">
                        <h3>Penilaian Kepuasan Klien Lapangan</h3>
                        <p>Feedback langsung lewat Form Review 5-Bintang</p>
                    </div>
                    <div style="text-align: center; padding: 20px 0; border-bottom: 1px solid var(--border-color);">
                        <div style="font-size: 48px; font-weight: 800; color: #181c32;" id="score_client_reviews">4.9</div>
                        <div style="color: #f59e0b; font-size: 20px; margin: 4px 0;">
                            <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                        </div>
                        <div style="font-size: 13px; color: var(--text-muted);">98% Klien Puas dengan Pelayanan</div>
                    </div>
                    <div style="padding-top: 16px;">
                        <div style="font-size: 12px; font-weight: 600; color: var(--text-muted); margin-bottom: 8px;">TAG PUJIAN POPULER:</div>
                        <div style="display: flex; flex-wrap: wrap; gap: 6px;">
                            <span class="badge" style="background:#f0fdf4; color:#16a34a; border:1px solid #bbf7d0; padding:4px 8px; font-size:11px;">Ramah &amp; Sabar</span>
                            <span class="badge" style="background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe; padding:4px 8px; font-size:11px;">Hasil Foto Keren</span>
                            <span class="badge" style="background:#fef3c7; color:#b45309; border:1px solid #fde68a; padding:4px 8px; font-size:11px;">Arahan Gaya Bagus</span>
                            <span class="badge" style="background:#f3e8ff; color:#7e22ce; border:1px solid #e9d5ff; padding:4px 8px; font-size:11px;">Tepat Waktu</span>
                        </div>
                    </div>
                </div>

                <div class="content-section" style="margin-bottom: 0;">
                    <div class="section-header">
                        <div class="section-title">
                            <h3>Ulasan Klien Terbaru untuk Kru</h3>
                            <p>Ulasan masuk dari form review publik</p>
                        </div>
                        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                            <button class="btn btn-primary" onclick="openClientReviewModal()" style="font-size: 12px; display: inline-flex; align-items: center; gap: 6px;"><i class="fa-solid fa-plus"></i> Catat Ulasan Klien</button>
                            <button class="btn btn-outline" onclick="copyClientReviewLink()" style="font-size: 12px; display: inline-flex; align-items: center; gap: 6px;"><i class="fa-solid fa-link"></i> Link Form Review Publik</button>
                            <a href="/api/reviews/export/csv" class="btn btn-outline" style="font-size: 12px; display: inline-flex; align-items: center; gap: 6px;"><i class="fa-solid fa-download"></i> Unduh CSV Review</a>
                        </div>
                    </div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Klien</th>
                                <th>Kru Bertugas</th>
                                <th>Rating</th>
                                <th>Komentar Klien</th>
                                <th style="text-align: right;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="tbody_client_reviews">
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Hasil Kerja Marketing -->
            <div class="content-section">
                <div class="section-header">
                    <div class="section-title">
                        <h3>3. Hasil Kerja Tim Marketing</h3>
                        <p>Pencapaian konversi leads prospek menjadi booking aktif</p>
                    </div>
                </div>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Marketer / Channel</th>
                            <th>Total Leads Masuk</th>
                            <th>Booking Closing</th>
                            <th>Conversion Rate</th>
                            <th>Total Omset Dihasilkan</th>
                            <th>Fee / Komisi</th>
                        </tr>
                    </thead>
                    <tbody id="tbody_team_marketing">
                    </tbody>
                </table>
            </div>
        </div>

        <!-- PANEL 2: LAPORAN PAKET FOTO & PENJUALAN (PERFORMA PRODUK) -->
        <div id="panel_report_packages" style="display: none;">
            <div class="stats-grid">
                <div class="stat-card green">
                    <div class="stat-title">PAKET PALING LAKU</div>
                    <div class="stat-value" id="stat_pkg_most_popular" style="font-size: 18px;">-</div>
                    <div class="stat-desc">Volume booking tertinggi dalam sistem</div>
                    <div class="stat-icon"><i class="fa-solid fa-trophy"></i></div>
                </div>
                <div class="stat-card green">
                    <div class="stat-title">PAKET PALING UNTUNG</div>
                    <div class="stat-value" id="stat_pkg_most_profitable" style="font-size: 18px;">-</div>
                    <div class="stat-desc">Margin keuntungan &amp; laba bersih tertinggi</div>
                    <div class="stat-icon"><i class="fa-solid fa-chart-line"></i></div>
                </div>
                <div class="stat-card red">
                    <div class="stat-title">PAKET KURANG LAKU</div>
                    <div class="stat-value" id="stat_pkg_least_popular" style="font-size: 18px;">-</div>
                    <div class="stat-desc">Peminat rendah (Evaluasi harga / jadikan bonus)</div>
                    <div class="stat-icon"><i class="fa-solid fa-triangle-exclamation"></i></div>
                </div>
                <div class="stat-card">
                    <div class="stat-title">ADD-ON TERLARIS</div>
                    <div class="stat-value" id="stat_pkg_top_addon" style="font-size: 18px;">Drone Aerial 4K</div>
                    <div class="stat-desc">24 Klien memilih add-on ini (+Rp 28.8jt)</div>
                    <div class="stat-icon"><i class="fa-solid fa-helicopter"></i></div>
                </div>
            </div>

            <!-- Tabel Analisis Performa Paket -->
            <div class="content-section">
                <div class="section-header">
                    <div class="section-title">
                        <h3>Analisis Penjualan &amp; Margin Keuntungan Tiap Paket</h3>
                        <p>Evaluasi paket terlaris, paling menguntungkan, dan paket yang butuh perbaikan harga</p>
                    </div>
                </div>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Nama Paket Foto</th>
                            <th>Kategori</th>
                            <th>Harga Jual</th>
                            <th style="text-align: center;">Jumlah Terjual</th>
                            <th>Total Omset</th>
                            <th>Rata-rata HPP</th>
                            <th>Profit Bersih</th>
                            <th>Margin %</th>
                            <th>Evaluasi &amp; Rekomendasi</th>
                        </tr>
                    </thead>
                    <tbody id="tbody_packages_list">
                    </tbody>
                </table>
            </div>

            <!-- Produk Tambahan & Tren Musiman -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="content-section" style="margin-bottom: 0;">
                    <div class="section-title" style="margin-bottom: 16px;">
                        <h3>Produk Tambahan (Add-On) Terlaris</h3>
                        <p>Penghasil keuntungan tambahan di luar paket dasar</p>
                    </div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Item Add-On</th>
                                <th>Harga Satuan</th>
                                <th style="text-align: center;">Terjual</th>
                                <th>Total Revenue</th>
                            </tr>
                        </thead>
                        <tbody id="tbody_addons_list">
                            <tr>
                                <td><strong>Drone Aerial 4K</strong></td>
                                <td>Rp 1.200.000</td>
                                <td style="text-align: center;"><strong>24x</strong></td>
                                <td style="color:#10b981; font-weight:700;">Rp 28.800.000</td>
                            </tr>
                            <tr>
                                <td><strong>Cetak Canvas Jumbo 60x90</strong></td>
                                <td>Rp 750.000</td>
                                <td style="text-align: center;"><strong>19x</strong></td>
                                <td style="color:#10b981; font-weight:700;">Rp 14.250.000</td>
                            </tr>
                            <tr>
                                <td><strong>Extra Kru Fotografer</strong></td>
                                <td>Rp 800.000</td>
                                <td style="text-align: center;"><strong>14x</strong></td>
                                <td style="color:#10b981; font-weight:700;">Rp 11.200.000</td>
                            </tr>
                            <tr>
                                <td><strong>Photobooth Unlimited 3 Jam</strong></td>
                                <td>Rp 2.500.000</td>
                                <td style="text-align: center;"><strong>8x</strong></td>
                                <td style="color:#10b981; font-weight:700;">Rp 20.000.000</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="content-section" style="margin-bottom: 0;">
                    <div class="section-title" style="margin-bottom: 16px;">
                        <h3>Tren Penjualan &amp; Pola Musiman</h3>
                        <p>Antisipasi periode ramai (peak) vs periode sepi (low)</p>
                    </div>
                    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px; margin-bottom: 12px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                            <strong style="color: #15803d;"><i class="fa-solid fa-fire"></i> PEAK SEASON (Juli, September, Desember)</strong>
                            <span class="badge" style="background:#dcfce7; color:#15803d; font-weight:700;">Penuh 95%</span>
                        </div>
                        <p style="font-size: 12px; color: #475569; margin: 0;">Musim pernikahan &amp; liburan akhir tahun. Saran: Tambah kru freelance cadangan dan naikkan tarif paket weekend.</p>
                    </div>
                    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                            <strong style="color: #b45309;"><i class="fa-solid fa-cloud-sun"></i> LOW SEASON (Februari, Maret / Bulan Puasa)</strong>
                            <span class="badge" style="background:#fef3c7; color:#b45309; font-weight:700;">Pemanfaatan 40%</span>
                        </div>
                        <p style="font-size: 12px; color: #475569; margin: 0;">Pernikahan berkurang. Saran: Luncurkan paket Maternity, Newborn, Family Ramadan, dan promo booking lebih awal (Early Bird).</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- PANEL 3: LAPORAN KEUANGAN & OPERASIONAL STUDIO -->
        <div id="panel_report_operations" style="display: none;">
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-title">UTILISASI JADWAL STUDIO</div>
                    <div class="stat-value" id="stat_ops_utilization">78.5%</div>
                    <div class="stat-desc">Rata-rata 220 jam terisi dari 280 jam aktif</div>
                    <div class="stat-icon"><i class="fa-solid fa-clock"></i></div>
                </div>
                <div class="stat-card green">
                    <div class="stat-title">HARI TERAMAI</div>
                    <div class="stat-value" style="font-size: 20px;">Sabtu &amp; Minggu</div>
                    <div class="stat-desc">Okupansi 96% jadwal studio &amp; kru lapangan</div>
                    <div class="stat-icon"><i class="fa-solid fa-calendar-week"></i></div>
                </div>
                <div class="stat-card red">
                    <div class="stat-title">SISA PIUTANG TERTUNDA</div>
                    <div class="stat-value" id="stat_unpaid_receivables">Rp 18.450.000</div>
                    <div class="stat-desc">Klien yang belum melunasi sisa tagihan</div>
                    <div class="stat-icon"><i class="fa-solid fa-hand-holding-dollar"></i></div>
                </div>
                <div class="stat-card">
                    <div class="stat-title">LEAD SOURCE TERBESAR</div>
                    <div class="stat-value" style="font-size: 20px;">Instagram Ads</div>
                    <div class="stat-desc">Menyumbang 48% dari seluruh booking masuk</div>
                    <div class="stat-icon"><i class="fa-brands fa-instagram"></i></div>
                </div>
            </div>

            <!-- Jam Operasional & Utilisasi Studio -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 24px;">
                <div class="content-section" style="margin-bottom: 0;">
                    <div class="section-title" style="margin-bottom: 16px;">
                        <h3>Jam &amp; Hari Teramai Studio</h3>
                        <p>Optimalkan penempatan staf dan promo di jam sepi</p>
                    </div>
                    <div style="margin-bottom: 12px;">
                        <div style="display: flex; justify-content: space-between; font-size: 13px; margin-bottom: 4px;">
                            <span>Sabtu &amp; Minggu (Weekend)</span>
                            <strong>96% (Penuh)</strong>
                        </div>
                        <div style="height: 10px; background: #e2e8f0; border-radius: 5px; overflow: hidden;">
                            <div style="width: 96%; height: 100%; background: #10b981;"></div>
                        </div>
                    </div>
                    <div style="margin-bottom: 12px;">
                        <div style="display: flex; justify-content: space-between; font-size: 13px; margin-bottom: 4px;">
                            <span>Jumat Sore &amp; Malam</span>
                            <strong>82% (Ramai)</strong>
                        </div>
                        <div style="height: 10px; background: #e2e8f0; border-radius: 5px; overflow: hidden;">
                            <div style="width: 82%; height: 100%; background: #3b82f6;"></div>
                        </div>
                    </div>
                    <div style="margin-bottom: 12px;">
                        <div style="display: flex; justify-content: space-between; font-size: 13px; margin-bottom: 4px;">
                            <span>Senin - Kamis (Siang - Sore)</span>
                            <strong>54% (Normal)</strong>
                        </div>
                        <div style="height: 10px; background: #e2e8f0; border-radius: 5px; overflow: hidden;">
                            <div style="width: 54%; height: 100%; background: #f59e0b;"></div>
                        </div>
                    </div>
                    <div>
                        <div style="display: flex; justify-content: space-between; font-size: 13px; margin-bottom: 4px;">
                            <span>Selasa - Rabu Pagi (08:00 - 11:00)</span>
                            <strong>28% (Sepi)</strong>
                        </div>
                        <div style="height: 10px; background: #e2e8f0; border-radius: 5px; overflow: hidden;">
                            <div style="width: 28%; height: 100%; background: #ef4444;"></div>
                        </div>
                        <div style="font-size: 11px; color: var(--text-muted); margin-top: 6px;">
                            <i class="fa-solid fa-lightbulb" style="color: #f59e0b;"></i> Tips: Berikan diskon khusus 20% untuk sesi foto studio di hari Selasa &amp; Rabu pagi.
                        </div>
                    </div>
                </div>

                <!-- Asal Kedatangan Klien (Lead Sources) -->
                <div class="content-section" style="margin-bottom: 0;">
                    <div class="section-title" style="margin-bottom: 16px;">
                        <h3>Asal Kedatangan Klien (Lead Sources)</h3>
                        <p>Dari mana calon pengantin &amp; klien mengetahui studio Anda</p>
                    </div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Sumber Channel</th>
                                <th style="text-align: center;">Jumlah Leads</th>
                                <th style="text-align: center;">Closing %</th>
                                <th>Kontribusi Omset</th>
                            </tr>
                        </thead>
                        <tbody id="tbody_lead_sources">
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Arus Kas & Sisa Tagihan Piutang -->
            <div class="content-section">
                <div class="section-header">
                    <div class="section-title">
                        <h3>Kesehatan Arus Kas &amp; Piutang Klien</h3>
                        <p>Monitoring tagihan aktif dan sisa pelunasan sebelum acara berlangsung</p>
                    </div>
                </div>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Klien</th>
                            <th>Tanggal Acara</th>
                            <th>Paket Booking</th>
                            <th>Total Nilai Paket</th>
                            <th>Sudah Dibayar</th>
                            <th>Sisa Tagihan (Piutang)</th>
                            <th>Jatuh Tempo</th>
                            <th>Tindakan Reminder</th>
                        </tr>
                    </thead>
                    <tbody id="tbody_unpaid_clients">
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
                    <input type="text" id="exp_nominal" class="form-control" placeholder="0" oninput="formatRupiahInput(this)" onblur="if(!this.value.trim()) this.value = '0';" onfocus="if(this.value === '0') this.select();">
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

    <!-- Modal Evaluasi Kerja Kantor -->
    <div class="modal-overlay" id="officeEvalModal" style="display: none; align-items: center; justify-content: center; z-index: 1060;">
        <div class="modal-content" style="max-width: 600px; width: 95%; max-height: 90vh; overflow-y: auto; background: white; border-radius: 12px; padding: 24px;">
            <div class="modal-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; border-bottom: 1px solid var(--border-color); padding-bottom: 12px;">
                <div>
                    <h2 style="font-size: 18px; font-weight: 600; margin-bottom: 4px;">Catat Penilaian Kerja Kantor</h2>
                    <p style="font-size: 13px; color: var(--text-muted); margin: 0;">Monitoring Same Day Edit, Fast Edit, Deadline Cetak &amp; Ready to Pick Up</p>
                </div>
                <button onclick="closeOfficeEvalModal()" style="background: none; border: none; font-size: 20px; color: var(--text-muted); cursor: pointer;"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="oe_id" value="">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 14px;">
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">Nama Editor</label>
                        <input type="text" id="oe_editor_name" class="form-control" placeholder="Faris, Bagus, Fauzan...">
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">Staff Produksi / Cetak</label>
                        <input type="text" id="oe_prod_staff" class="form-control" placeholder="Angga...">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 14px;">
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">Tipe Layanan Edit</label>
                        <select id="oe_service_type" class="form-control">
                            <option>Same Day Edit</option>
                            <option>Fast Edit 24 Jam</option>
                            <option>Fast Edit 3 Hari</option>
                            <option>Normal</option>
                            <option>Rush</option>
                        </select>
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">Status Ketepatan Waktu</label>
                        <select id="oe_edit_status" class="form-control">
                            <option>On Time</option>
                            <option>Ahead of Schedule</option>
                            <option>Delayed</option>
                        </select>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 14px;">
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">Durasi Jam Selesai</label>
                        <input type="number" step="0.5" id="oe_completion_hours" class="form-control" placeholder="Contoh: 3.5">
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">Skor Kualitas (1 - 5)</label>
                        <select id="oe_quality_score" class="form-control">
                            <option value="5">5 Bintang (Sempurna)</option>
                            <option value="4">4 Bintang (Bagus)</option>
                            <option value="3">3 Bintang (Cukup)</option>
                            <option value="2">2 Bintang (Kurang)</option>
                            <option value="1">1 Bintang (Revisi Banyak)</option>
                        </select>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 14px;">
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">Deadline Cetak Album/Box</label>
                        <input type="date" id="oe_print_deadline" class="form-control">
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">Tanggal Siap Diambil (Ready)</label>
                        <input type="date" id="oe_ready_pickup" class="form-control">
                    </div>
                </div>

                <div class="form-group" style="margin-bottom: 14px;">
                    <label class="form-label">Status Cetak &amp; Pengambilan</label>
                    <select id="oe_print_status" class="form-control">
                        <option>Ready to Pick Up</option>
                        <option>On Schedule</option>
                        <option>In Printing Process</option>
                        <option>Picked Up by Client</option>
                        <option>Delayed</option>
                    </select>
                </div>

                <div class="form-group" style="margin-bottom: 20px;">
                    <label class="form-label">Catatan / Evaluasi</label>
                    <textarea id="oe_notes" class="form-control" rows="2" placeholder="Catatan kualitas render, revisi klien, kelengkapan cetak..."></textarea>
                </div>

                <div style="display: flex; gap: 10px; justify-content: flex-end; border-top: 1px solid #e2e8f0; padding-top: 14px;">
                    <button type="button" class="btn btn-outline" onclick="closeOfficeEvalModal()">Batal</button>
                    <button type="button" class="btn btn-primary" onclick="saveOfficeEvaluation()"><i class="fa-regular fa-floppy-disk"></i> Simpan Penilaian</button>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL SLIP GAJI GENERATOR -->
    <div class="modal-overlay" id="crewSlipModal" style="display: none; align-items: center; justify-content: center; z-index: 1070;">
        <div class="modal-content" style="max-width: 580px; width: 95%; max-height: 90vh; overflow-y: auto; background: white; border-radius: 16px; padding: 24px; box-shadow: 0 20px 40px rgba(0,0,0,0.2);">
            <div class="modal-header" style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px; border-bottom: 2px solid #f1f5f9; padding-bottom: 14px;">
                <div>
                    <span style="font-size: 10px; font-weight: 800; background: var(--sidebar-active-bg); color: var(--primary); padding: 3px 8px; border-radius: 6px; letter-spacing: 1px;">SLIP GAJI GENERATOR</span>
                    <h2 style="font-size: 20px; font-weight: 700; margin: 6px 0 2px; color: #0f172a;" id="modal_slip_crew_name">Nama Karyawan</h2>
                    <p style="font-size: 12px; color: var(--text-muted); margin: 0;"><span id="modal_slip_crew_pos">Divisi</span> &bull; Periode: <span id="modal_slip_period">Bulan Berjalan</span></p>
                </div>
                <button onclick="closeCrewSlipModal()" style="background: none; border: none; font-size: 20px; color: var(--text-muted); cursor: pointer;"><i class="fa-solid fa-xmark"></i></button>
            </div>
            
            <div id="printableSlipArea" style="background: #ffffff; padding: 4px 0;">
                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 14px 18px; margin-bottom: 16px; display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <div style="font-size: 11px; color: #64748b; font-weight: 600; text-transform: uppercase;">Beban Kerja Bulan Ini</div>
                        <div style="font-size: 18px; font-weight: 800; color: #0f172a;" id="modal_slip_sessions">0 Sesi Foto / Proyek</div>
                    </div>
                    <div style="text-align: right;">
                        <span class="badge" style="background: #dcfce7; color: #15803d; font-size: 12px; font-weight: 700; padding: 5px 12px; border-radius: 20px;">
                            <i class="fa-solid fa-circle-check"></i> Siap Dibayarkan
                        </span>
                    </div>
                </div>

                <div style="border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; margin-bottom: 16px;">
                    <div style="background: #f1f5f9; padding: 10px 16px; font-size: 12px; font-weight: 700; color: #475569; letter-spacing: 0.5px;">
                        RINCIAN KOMPONEN PENDAPATAN
                    </div>
                    <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                        <tbody>
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td style="padding: 12px 16px; color: #334155;">Gaji Pokok / Standar</td>
                                <td style="padding: 12px 16px; text-align: right; font-weight: 600;" id="modal_slip_base">Rp 0</td>
                            </tr>
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td style="padding: 12px 16px; color: #334155;">Uang Makan &amp; Operasional Jalan</td>
                                <td style="padding: 12px 16px; text-align: right; font-weight: 600;" id="modal_slip_meal">Rp 0</td>
                            </tr>
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td style="padding: 12px 16px; color: #334155;">Uang Lembur (Overtime)</td>
                                <td style="padding: 12px 16px; text-align: right; font-weight: 600;" id="modal_slip_overtime">Rp 0</td>
                            </tr>
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td style="padding: 12px 16px; color: #10b981; font-weight: 600;">Bonus / Fee Per Proyek</td>
                                <td style="padding: 12px 16px; text-align: right; font-weight: 600; color: #10b981;" id="modal_slip_bonus">Rp 0</td>
                            </tr>
                            <tr style="background: var(--sidebar-active-bg);">
                                <td style="padding: 14px 16px; font-size: 14px; font-weight: 700; color: #0f172a;">TOTAL GAJI BERSIH (TAKE HOME PAY)</td>
                                <td style="padding: 14px 16px; text-align: right; font-size: 18px; font-weight: 800; color: var(--primary);" id="modal_slip_total">Rp 0</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div style="font-size: 11px; color: #94a3b8; text-align: center; margin-bottom: 8px;">
                    Dokumen ini digenerate secara otomatis oleh sistem CloseMateAI Studio Management.
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 18px; border-top: 1px solid #e2e8f0; padding-top: 16px;">
                <button type="button" class="btn btn-secondary" onclick="closeCrewSlipModal()" style="font-size: 13px; padding: 8px 18px;">Tutup</button>
                <button type="button" class="btn btn-primary" onclick="printCrewSlip()" style="font-size: 13px; padding: 8px 20px; display: inline-flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-print"></i> Cetak PDF / Print
                </button>
            </div>
        </div>
    </div>

    <!-- MODAL CATAT ULASAN KEPUASAN KLIEN -->
    <div class="modal-overlay" id="clientReviewModal" style="display: none; align-items: center; justify-content: center; z-index: 1070;">
        <div class="modal-content" style="max-width: 540px; width: 95%; max-height: 90vh; overflow-y: auto; background: white; border-radius: 16px; padding: 24px; box-shadow: 0 20px 40px rgba(0,0,0,0.2);">
            <div class="modal-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; border-bottom: 1px solid var(--border-color); padding-bottom: 12px;">
                <div>
                    <h2 style="font-size: 18px; font-weight: 700; margin-bottom: 4px; color: #0f172a;">Catat Ulasan &amp; Penilaian Klien</h2>
                    <p style="font-size: 12px; color: var(--text-muted); margin: 0;">Rating kepuasan pelayanan kru lapangan (skor bintang 1 - 5 ala Grab)</p>
                </div>
                <button onclick="closeClientReviewModal()" style="background: none; border: none; font-size: 20px; color: var(--text-muted); cursor: pointer;"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <div class="modal-body">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 14px;">
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">Nama Klien</label>
                        <input type="text" id="cr_client_name" class="form-control" placeholder="Contoh: Sarah &amp; Dimas">
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">No WhatsApp Klien</label>
                        <input type="text" id="cr_client_phone" class="form-control" placeholder="08xxxxxxxx">
                    </div>
                </div>

                <div class="form-group" style="margin-bottom: 14px;">
                    <label class="form-label">Kru Lapangan yang Bertugas</label>
                    <input type="text" id="cr_field_crew_names" class="form-control" placeholder="Contoh: Dika (FG) &amp; Arya (VG)">
                </div>

                <div class="form-group" style="margin-bottom: 14px;">
                    <label class="form-label">Rating Kepuasan Klien</label>
                    <select id="cr_rating_overall" class="form-control" style="font-weight: 600;">
                        <option value="5">⭐⭐⭐⭐⭐ 5.0 - Sangat Puas / Istimewa</option>
                        <option value="4">⭐⭐⭐⭐ 4.0 - Puas / Bagus</option>
                        <option value="3">⭐⭐⭐ 3.0 - Cukup Baik</option>
                        <option value="2">⭐⭐ 2.0 - Kurang Memuaskan</option>
                        <option value="1">⭐ 1.0 - Kecewa</option>
                    </select>
                </div>

                <div class="form-group" style="margin-bottom: 14px;">
                    <label class="form-label">Tag Pujian (Pilih beberapa)</label>
                    <div style="display: flex; flex-wrap: wrap; gap: 8px; margin-top: 6px;">
                        <label style="font-size: 12px; display: inline-flex; align-items: center; gap: 4px; background: #f1f5f9; padding: 4px 10px; border-radius: 20px; cursor: pointer;">
                            <input type="checkbox" name="cr_tags" value="Ramah &amp; Sabar" checked> Ramah &amp; Sabar
                        </label>
                        <label style="font-size: 12px; display: inline-flex; align-items: center; gap: 4px; background: #f1f5f9; padding: 4px 10px; border-radius: 20px; cursor: pointer;">
                            <input type="checkbox" name="cr_tags" value="Hasil Foto Keren" checked> Hasil Foto Keren
                        </label>
                        <label style="font-size: 12px; display: inline-flex; align-items: center; gap: 4px; background: #f1f5f9; padding: 4px 10px; border-radius: 20px; cursor: pointer;">
                            <input type="checkbox" name="cr_tags" value="Arahan Gaya Bagus"> Arahan Gaya Bagus
                        </label>
                        <label style="font-size: 12px; display: inline-flex; align-items: center; gap: 4px; background: #f1f5f9; padding: 4px 10px; border-radius: 20px; cursor: pointer;">
                            <input type="checkbox" name="cr_tags" value="Tepat Waktu"> Tepat Waktu
                        </label>
                    </div>
                </div>

                <div class="form-group" style="margin-bottom: 20px;">
                    <label class="form-label">Komentar / Ulasan Klien</label>
                    <textarea id="cr_review_comment" class="form-control" rows="3" placeholder="Contoh: Pelayanan fotografer sangat ramah, sabar mengarahkan gaya, dan hasil fotonya sangat memuaskan!"></textarea>
                </div>

                <div style="display: flex; gap: 10px; justify-content: flex-end; border-top: 1px solid #e2e8f0; padding-top: 14px;">
                    <button type="button" class="btn btn-outline" onclick="closeClientReviewModal()">Batal</button>
                    <button type="button" class="btn btn-primary" onclick="saveClientReview()"><i class="fa-regular fa-floppy-disk"></i> Simpan Ulasan</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Master Biaya & Gaji Modal -->
    <div class="modal-overlay" id="masterModal" style="display: none; align-items: center; justify-content: center; z-index: 1050;">
        <div class="modal-content" style="max-width: 800px; width: 95%; max-height: 90vh; overflow-y: auto; background: white; border-radius: 12px; padding: 24px;">
            <div class="modal-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; border-bottom: 1px solid var(--border-color); padding-bottom: 12px;">
                <div>
                    <h2 style="font-size: 18px; font-weight: 600; margin-bottom: 4px; color: #1e293b;">
                        <i class="fa-solid fa-sliders" style="color: #6366f1; margin-right: 6px;"></i> Kelola Master Biaya &amp; Gaji
                    </h2>
                    <p style="font-size: 13px; color: var(--text-muted); margin: 0;">Pengaturan nilai gaji divisi dan biaya operasional bulanan yang masuk ke tab Excel.</p>
                </div>
                <button onclick="closeMasterModal()" style="background: none; border: none; font-size: 20px; color: var(--text-muted); cursor: pointer;"><i class="fa-solid fa-xmark"></i></button>
            </div>

            <!-- Tabs -->
            <div style="display: flex; gap: 8px; border-bottom: 2px solid #e2e8f0; margin-bottom: 16px;">
                <button type="button" class="master-tab-btn active" id="mtab_btn_studio" onclick="switchMasterTab('studio')" style="padding: 8px 16px; font-size: 13px; font-weight: 600; border: none; background: none; border-bottom: 2px solid #2563eb; color: #2563eb; cursor: pointer;">Gaji Studio</button>
                <button type="button" class="master-tab-btn" id="mtab_btn_sosmed" onclick="switchMasterTab('sosmed')" style="padding: 8px 16px; font-size: 13px; font-weight: 600; border: none; background: none; border-bottom: 2px solid transparent; color: #64748b; cursor: pointer;">Gaji Sosmed</button>
                <button type="button" class="master-tab-btn" id="mtab_btn_ops" onclick="switchMasterTab('ops')" style="padding: 8px 16px; font-size: 13px; font-weight: 600; border: none; background: none; border-bottom: 2px solid transparent; color: #64748b; cursor: pointer;">Operasional Bulanan</button>
                <button type="button" class="master-tab-btn" id="mtab_btn_album" onclick="switchMasterTab('album')" style="padding: 8px 16px; font-size: 13px; font-weight: 600; border: none; background: none; border-bottom: 2px solid transparent; color: #64748b; cursor: pointer;">Info Album</button>
            </div>

            <div id="master_tab_content">
                <!-- Studio Tab -->
                <div id="master_content_studio">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                        <p style="font-size: 12px; color: #64748b; margin: 0;">Daftar staf tetap studio Penapict (tab <strong>BEBAN GAJI DIVISI STUDIO</strong> di Excel):</p>
                        <button type="button" class="btn btn-outline" onclick="addMasterStudioRow()" style="font-size: 12px; padding: 6px 14px; font-weight: 600; border-color: #2563eb; color: #2563eb; display: inline-flex; align-items: center; gap: 6px;">
                            <i class="fa-solid fa-plus"></i> Tambah Staf Baru
                        </button>
                    </div>
                    <div id="master_studio_table_container" style="overflow-x: auto; margin-bottom: 16px;">
                        <!-- Table injected via JS -->
                    </div>
                </div>

                <!-- Sosmed Tab -->
                <div id="master_content_sosmed" style="display: none;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                        <p style="font-size: 12px; color: #64748b; margin: 0;">Daftar channel sosmed (tab <strong>BEBAN GAJI DIVISI SOSMED</strong> di Excel):</p>
                        <button type="button" class="btn btn-outline" onclick="addMasterSosmedRow()" style="font-size: 12px; padding: 6px 14px; font-weight: 600; border-color: #2563eb; color: #2563eb; display: inline-flex; align-items: center; gap: 6px;">
                            <i class="fa-solid fa-plus"></i> Tambah Channel / Pos
                        </button>
                    </div>
                    <div id="master_sosmed_table_container" style="overflow-x: auto; margin-bottom: 16px;">
                        <!-- Table injected via JS -->
                    </div>
                </div>

                <!-- Ops Tab -->
                <div id="master_content_ops" style="display: none;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                        <p style="font-size: 12px; color: #64748b; margin: 0;">Biaya operasional rutin tetap (tab <strong>OPERASIONAL BULANAN</strong> di Excel):</p>
                        <button type="button" class="btn btn-outline" onclick="addMasterOpsRow()" style="font-size: 12px; padding: 6px 14px; font-weight: 600; border-color: #2563eb; color: #2563eb; display: inline-flex; align-items: center; gap: 6px;">
                            <i class="fa-solid fa-plus"></i> Tambah Pengeluaran Rutin
                        </button>
                    </div>
                    <div id="master_ops_table_container" style="overflow-x: auto; margin-bottom: 16px;">
                        <!-- Table injected via JS -->
                    </div>
                </div>

                <!-- Album Tab -->
                <div id="master_content_album" style="display: none;">
                    <div style="padding: 16px; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; color: #166534; font-size: 13px;">
                        <h4 style="font-weight: 700; margin-bottom: 6px;"><i class="fa-solid fa-boxes-stacked"></i> Tab ALBUM Terhubung ke Stock Opname</h4>
                        <p style="margin-bottom: 8px;">Semua data pada tab <strong>ALBUM</strong> di file Excel diambil langsung secara otomatis dari menu <strong>Stock Opname &amp; Inventaris</strong> (kategori Album).</p>
                        <p style="margin: 0;">Untuk menambah, mengubah stok fisik, atau harga persediaan album, Anda dapat langsung mengaturnya di menu <strong>Stock Opname</strong>.</p>
                        <div style="margin-top: 12px;">
                            <a href="/stock" class="btn btn-primary" style="font-size: 12px; padding: 6px 14px; text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
                                <i class="fa-solid fa-arrow-up-right-from-square"></i> Buka Menu Stock Opname
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 20px; border-top: 1px solid #e2e8f0; padding-top: 16px;">
                <button class="btn btn-outline" onclick="closeMasterModal()">Tutup</button>
                <button class="btn btn-primary" id="btnSaveMaster" onclick="saveMasterData()">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan
                </button>
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

        async function fetchExpensesFinance() {
            try {
                const res = await fetch('/api/finance/expenses');
                if (res.ok) {
                    const data = await res.json();
                    if (data && data.success && Array.isArray(data.data) && data.data.length > 0) {
                        const mapped = data.data.map(e => ({
                            id: e.id,
                            acaraId: e.booking_id || '',
                            kategori: e.category || 'Operasional',
                            tanggal: e.date ? e.date.substring(0, 10) : '',
                            judul: e.name || '',
                            nominal: Number(e.amount) || 0,
                            catatan: e.description || ''
                        }));
                        localStorage.setItem('f_expenses', JSON.stringify(mapped));
                        calculateFinance();
                    }
                }
            } catch(e) {
                console.warn("Could not sync expenses from server:", e);
            }
        }

        let masterSalariesData = [];
        let masterOpsData = [];
        let deletedSalariesIds = [];
        let deletedOpsIds = [];
        let currentMasterTab = 'studio';

        async function openMasterModal() {
            document.getElementById('masterModal').style.display = 'flex';
            deletedSalariesIds = [];
            deletedOpsIds = [];
            await loadMasterData();
        }

        function closeMasterModal() {
            document.getElementById('masterModal').style.display = 'none';
        }

        function switchMasterTab(tab) {
            syncInputsToState();
            currentMasterTab = tab;
            ['studio', 'sosmed', 'ops', 'album'].forEach(t => {
                const btn = document.getElementById('mtab_btn_' + t);
                const content = document.getElementById('master_content_' + t);
                if (btn) {
                    if (t === tab) {
                        btn.style.color = '#2563eb';
                        btn.style.borderBottomColor = '#2563eb';
                    } else {
                        btn.style.color = '#64748b';
                        btn.style.borderBottomColor = 'transparent';
                    }
                }
                if (content) {
                    content.style.display = (t === tab) ? 'block' : 'none';
                }
            });
        }

        function syncInputsToState() {
            masterSalariesData.forEach(s => {
                const nameInp = document.getElementById('ms_name_' + s.id);
                const posInp = document.getElementById('ms_pos_' + s.id);
                const qtyInp = document.getElementById('ms_qty_' + s.id);
                const priceInp = document.getElementById('ms_price_' + s.id);
                if (nameInp) s.name = nameInp.value;
                if (posInp) s.position = posInp.value;
                if (qtyInp) {
                    if (!qtyInp.value || qtyInp.value.trim() === '') qtyInp.value = '1';
                    s.qty = parseInt(qtyInp.value) || 1;
                }
                if (priceInp) {
                    if (!priceInp.value || priceInp.value.trim() === '') priceInp.value = '0';
                    s.price = parseRupiah(priceInp.value) || 0;
                    s.total = s.qty * s.price;
                }
            });

            masterOpsData.forEach(op => {
                const nameInp = document.getElementById('mop_name_' + op.id);
                const qtyInp = document.getElementById('mop_qty_' + op.id);
                const priceInp = document.getElementById('mop_price_' + op.id);
                if (nameInp) op.name = nameInp.value;
                if (qtyInp) {
                    if (!qtyInp.value || qtyInp.value.trim() === '') qtyInp.value = '1';
                    op.qty = parseInt(qtyInp.value) || 1;
                }
                if (priceInp) {
                    if (!priceInp.value || priceInp.value.trim() === '') priceInp.value = '0';
                    op.price = parseRupiah(priceInp.value) || 0;
                    op.total = op.qty * op.price;
                }
            });
        }

        async function loadMasterData() {
            try {
                const resSal = await fetch('/api/finance/master-salaries');
                const dataSal = await resSal.json();
                if (dataSal.success) {
                    masterSalariesData = dataSal.data || [];
                    renderMasterStudioTable();
                    renderMasterSosmedTable();
                }

                const resOps = await fetch('/api/finance/monthly-operations');
                const dataOps = await resOps.json();
                if (dataOps.success) {
                    masterOpsData = dataOps.data || [];
                    renderMasterOpsTable();
                }
            } catch(e) {
                console.error("Error loading master data:", e);
            }
        }

        // Live Calculation functions
        function updateStudioTotal() {
            let sum = 0;
            const studioItems = masterSalariesData.filter(s => s.division === 'studio');
            studioItems.forEach(s => {
                const qtyEl = document.getElementById('ms_qty_' + s.id);
                const priceEl = document.getElementById('ms_price_' + s.id);
                const qty = qtyEl ? (parseInt(qtyEl.value) || 0) : (s.qty || 0);
                const price = priceEl ? (parseRupiah(priceEl.value) || 0) : (s.price || 0);
                sum += (qty * price);
            });
            const disp = document.getElementById('total_studio_display');
            if (disp) disp.innerText = 'Rp ' + formatRupiah(sum);
        }

        function updateSosmedTotal() {
            let sum = 0;
            const sosmedItems = masterSalariesData.filter(s => s.division === 'sosmed');
            sosmedItems.forEach(s => {
                const qtyEl = document.getElementById('ms_qty_' + s.id);
                const priceEl = document.getElementById('ms_price_' + s.id);
                const qty = qtyEl ? (parseInt(qtyEl.value) || 0) : (s.qty || 0);
                const price = priceEl ? (parseRupiah(priceEl.value) || 0) : (s.price || 0);
                sum += (qty * price);
            });
            const disp = document.getElementById('total_sosmed_display');
            if (disp) disp.innerText = 'Rp ' + formatRupiah(sum);
        }

        function updateOpsTotal() {
            let sum = 0;
            masterOpsData.forEach(op => {
                const qtyEl = document.getElementById('mop_qty_' + op.id);
                const priceEl = document.getElementById('mop_price_' + op.id);
                const qty = qtyEl ? (parseInt(qtyEl.value) || 0) : (op.qty || 0);
                const price = priceEl ? (parseRupiah(priceEl.value) || 0) : (op.price || 0);
                sum += (qty * price);
            });
            const disp = document.getElementById('total_ops_display');
            if (disp) disp.innerText = 'Rp ' + formatRupiah(sum);
        }

        function handleMasterPriceBlur(input, type) {
            const val = input.value.trim();
            if (!val || val === '') {
                input.value = '0';
            } else {
                const num = parseRupiah(val);
                input.value = formatRupiah(num);
            }
            if (type === 'studio') updateStudioTotal();
            else if (type === 'sosmed') updateSosmedTotal();
            else if (type === 'ops') updateOpsTotal();
        }

        function handleMasterQtyBlur(input, type) {
            const val = input.value.trim();
            if (!val || parseInt(val) < 0 || isNaN(parseInt(val))) {
                input.value = '1';
            }
            if (type === 'studio') updateStudioTotal();
            else if (type === 'sosmed') updateSosmedTotal();
            else if (type === 'ops') updateOpsTotal();
        }

        function handleMasterPriceFocus(input) {
            if (input.value === '0') {
                input.select();
            }
        }

        // Add new row functions (automatic insert at the bottom)
        function addMasterStudioRow() {
            syncInputsToState();
            const newId = 'new_studio_' + Date.now();
            masterSalariesData.push({
                id: newId,
                division: 'studio',
                name: '',
                position: '',
                qty: 1,
                price: 0,
                total: 0
            });
            renderMasterStudioTable();
            setTimeout(() => {
                const el = document.getElementById('ms_name_' + newId);
                if (el) el.focus();
            }, 50);
        }

        function addMasterSosmedRow() {
            syncInputsToState();
            const newId = 'new_sosmed_' + Date.now();
            masterSalariesData.push({
                id: newId,
                division: 'sosmed',
                name: '',
                position: '',
                qty: 1,
                price: 0,
                total: 0
            });
            renderMasterSosmedTable();
            setTimeout(() => {
                const el = document.getElementById('ms_name_' + newId);
                if (el) el.focus();
            }, 50);
        }

        function addMasterOpsRow() {
            syncInputsToState();
            const newId = 'new_ops_' + Date.now();
            masterOpsData.push({
                id: newId,
                name: '',
                qty: 1,
                price: 0,
                total: 0
            });
            renderMasterOpsTable();
            setTimeout(() => {
                const el = document.getElementById('mop_name_' + newId);
                if (el) el.focus();
            }, 50);
        }

        // Delete row functions
        function deleteMasterSalaryRow(id, division) {
            syncInputsToState();
            if (typeof id === 'number' || (!String(id).startsWith('new_') && !isNaN(id))) {
                deletedSalariesIds.push(Number(id));
            }
            masterSalariesData = masterSalariesData.filter(s => String(s.id) !== String(id));
            if (division === 'sosmed') {
                renderMasterSosmedTable();
            } else {
                renderMasterStudioTable();
            }
        }

        function deleteMasterOpsRow(id) {
            syncInputsToState();
            if (typeof id === 'number' || (!String(id).startsWith('new_') && !isNaN(id))) {
                deletedOpsIds.push(Number(id));
            }
            masterOpsData = masterOpsData.filter(op => String(op.id) !== String(id));
            renderMasterOpsTable();
        }

        function renderMasterStudioTable() {
            const container = document.getElementById('master_studio_table_container');
            if (!container) return;
            const studioItems = masterSalariesData.filter(s => s.division === 'studio');
            let html = `
                <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                    <thead>
                        <tr style="background: #f1f5f9; text-align: left; color: #334155;">
                            <th style="padding: 8px 10px; border: 1px solid #cbd5e1;">Nama</th>
                            <th style="padding: 8px 10px; border: 1px solid #cbd5e1;">Jabatan</th>
                            <th style="padding: 8px 10px; border: 1px solid #cbd5e1; width: 60px;">QTY</th>
                            <th style="padding: 8px 10px; border: 1px solid #cbd5e1; width: 140px;">Gaji / Tarif (Rp)</th>
                            <th style="padding: 8px 10px; border: 1px solid #cbd5e1; width: 45px; text-align: center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
            `;
            studioItems.forEach((item) => {
                const priceVal = (item.price !== undefined && item.price !== null && item.price !== '') ? formatRupiah(item.price) : '0';
                const qtyVal = (item.qty !== undefined && item.qty !== null && item.qty !== '') ? item.qty : 1;
                html += `
                    <tr>
                        <td style="padding: 6px 10px; border: 1px solid #cbd5e1;">
                            <input type="text" class="form-control" style="font-size: 12px; padding: 4px 8px;" id="ms_name_${item.id}" value="${item.name || ''}" placeholder="Nama staf">
                        </td>
                        <td style="padding: 6px 10px; border: 1px solid #cbd5e1;">
                            <input type="text" class="form-control" style="font-size: 12px; padding: 4px 8px;" id="ms_pos_${item.id}" value="${item.position || ''}" placeholder="Jabatan">
                        </td>
                        <td style="padding: 6px 10px; border: 1px solid #cbd5e1;">
                            <input type="number" min="0" class="form-control" style="font-size: 12px; padding: 4px 8px;" id="ms_qty_${item.id}" value="${qtyVal}" oninput="updateStudioTotal()" onblur="handleMasterQtyBlur(this, 'studio')" onfocus="if(this.value === '1' || this.value === '0') this.select();">
                        </td>
                        <td style="padding: 6px 10px; border: 1px solid #cbd5e1;">
                            <input type="text" class="form-control" style="font-size: 12px; padding: 4px 8px;" id="ms_price_${item.id}" value="${priceVal}" oninput="formatRupiahInput(this); updateStudioTotal()" onblur="handleMasterPriceBlur(this, 'studio')" onfocus="handleMasterPriceFocus(this)">
                        </td>
                        <td style="padding: 6px 10px; border: 1px solid #cbd5e1; text-align: center;">
                            <button type="button" onclick="deleteMasterSalaryRow('${item.id}', 'studio')" style="background: none; border: none; color: #ef4444; cursor: pointer; padding: 4px; font-size: 14px;" title="Hapus baris">
                                <i class="fa-solid fa-trash-can"></i>
                            </button>
                        </td>
                    </tr>
                `;
            });
            html += `
                    </tbody>
                    <tfoot>
                        <tr style="background: #f8fafc; font-weight: 700; color: #1e293b;">
                            <td colspan="3" style="padding: 8px 10px; border: 1px solid #cbd5e1; text-align: right;">TOTAL:</td>
                            <td id="total_studio_display" style="padding: 8px 10px; border: 1px solid #cbd5e1; text-align: right; color: #2563eb;">Rp 0</td>
                            <td style="border: 1px solid #cbd5e1;"></td>
                        </tr>
                    </tfoot>
                </table>
            `;
            container.innerHTML = html;
            updateStudioTotal();
        }

        function renderMasterSosmedTable() {
            const container = document.getElementById('master_sosmed_table_container');
            if (!container) return;
            const sosmedItems = masterSalariesData.filter(s => s.division === 'sosmed');
            let html = `
                <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                    <thead>
                        <tr style="background: #f1f5f9; text-align: left; color: #334155;">
                            <th style="padding: 8px 10px; border: 1px solid #cbd5e1;">Nama Channel Sosmed</th>
                            <th style="padding: 8px 10px; border: 1px solid #cbd5e1; width: 60px;">QTY</th>
                            <th style="padding: 8px 10px; border: 1px solid #cbd5e1; width: 140px;">Biaya / Gaji (Rp)</th>
                            <th style="padding: 8px 10px; border: 1px solid #cbd5e1; width: 45px; text-align: center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
            `;
            sosmedItems.forEach((item) => {
                const priceVal = (item.price !== undefined && item.price !== null && item.price !== '') ? formatRupiah(item.price) : '0';
                const qtyVal = (item.qty !== undefined && item.qty !== null && item.qty !== '') ? item.qty : 1;
                html += `
                    <tr>
                        <td style="padding: 6px 10px; border: 1px solid #cbd5e1;">
                            <input type="text" class="form-control" style="font-size: 12px; padding: 4px 8px;" id="ms_name_${item.id}" value="${item.name || ''}" placeholder="Nama channel / pos">
                        </td>
                        <td style="padding: 6px 10px; border: 1px solid #cbd5e1;">
                            <input type="number" min="0" class="form-control" style="font-size: 12px; padding: 4px 8px;" id="ms_qty_${item.id}" value="${qtyVal}" oninput="updateSosmedTotal()" onblur="handleMasterQtyBlur(this, 'sosmed')" onfocus="if(this.value === '1' || this.value === '0') this.select();">
                        </td>
                        <td style="padding: 6px 10px; border: 1px solid #cbd5e1;">
                            <input type="text" class="form-control" style="font-size: 12px; padding: 4px 8px;" id="ms_price_${item.id}" value="${priceVal}" oninput="formatRupiahInput(this); updateSosmedTotal()" onblur="handleMasterPriceBlur(this, 'sosmed')" onfocus="handleMasterPriceFocus(this)">
                        </td>
                        <td style="padding: 6px 10px; border: 1px solid #cbd5e1; text-align: center;">
                            <button type="button" onclick="deleteMasterSalaryRow('${item.id}', 'sosmed')" style="background: none; border: none; color: #ef4444; cursor: pointer; padding: 4px; font-size: 14px;" title="Hapus baris">
                                <i class="fa-solid fa-trash-can"></i>
                            </button>
                        </td>
                    </tr>
                `;
            });
            html += `
                    </tbody>
                    <tfoot>
                        <tr style="background: #f8fafc; font-weight: 700; color: #1e293b;">
                            <td colspan="2" style="padding: 8px 10px; border: 1px solid #cbd5e1; text-align: right;">TOTAL:</td>
                            <td id="total_sosmed_display" style="padding: 8px 10px; border: 1px solid #cbd5e1; text-align: right; color: #2563eb;">Rp 0</td>
                            <td style="border: 1px solid #cbd5e1;"></td>
                        </tr>
                    </tfoot>
                </table>
            `;
            container.innerHTML = html;
            updateSosmedTotal();
        }

        function renderMasterOpsTable() {
            const container = document.getElementById('master_ops_table_container');
            if (!container) return;
            let html = `
                <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                    <thead>
                        <tr style="background: #f1f5f9; text-align: left; color: #334155;">
                            <th style="padding: 8px 10px; border: 1px solid #cbd5e1;">Nama Pengeluaran Bulanan</th>
                            <th style="padding: 8px 10px; border: 1px solid #cbd5e1; width: 60px;">QTY</th>
                            <th style="padding: 8px 10px; border: 1px solid #cbd5e1; width: 140px;">Biaya Rutin (Rp)</th>
                            <th style="padding: 8px 10px; border: 1px solid #cbd5e1; width: 45px; text-align: center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
            `;
            masterOpsData.forEach((item) => {
                const priceVal = (item.price !== undefined && item.price !== null && item.price !== '') ? formatRupiah(item.price) : '0';
                const qtyVal = (item.qty !== undefined && item.qty !== null && item.qty !== '') ? item.qty : 1;
                html += `
                    <tr>
                        <td style="padding: 6px 10px; border: 1px solid #cbd5e1;">
                            <input type="text" class="form-control" style="font-size: 12px; padding: 4px 8px;" id="mop_name_${item.id}" value="${item.name || ''}" placeholder="Nama pengeluaran">
                        </td>
                        <td style="padding: 6px 10px; border: 1px solid #cbd5e1;">
                            <input type="number" min="0" class="form-control" style="font-size: 12px; padding: 4px 8px;" id="mop_qty_${item.id}" value="${qtyVal}" oninput="updateOpsTotal()" onblur="handleMasterQtyBlur(this, 'ops')" onfocus="if(this.value === '1' || this.value === '0') this.select();">
                        </td>
                        <td style="padding: 6px 10px; border: 1px solid #cbd5e1;">
                            <input type="text" class="form-control" style="font-size: 12px; padding: 4px 8px;" id="mop_price_${item.id}" value="${priceVal}" oninput="formatRupiahInput(this); updateOpsTotal()" onblur="handleMasterPriceBlur(this, 'ops')" onfocus="handleMasterPriceFocus(this)">
                        </td>
                        <td style="padding: 6px 10px; border: 1px solid #cbd5e1; text-align: center;">
                            <button type="button" onclick="deleteMasterOpsRow('${item.id}')" style="background: none; border: none; color: #ef4444; cursor: pointer; padding: 4px; font-size: 14px;" title="Hapus baris">
                                <i class="fa-solid fa-trash-can"></i>
                            </button>
                        </td>
                    </tr>
                `;
            });
            html += `
                    </tbody>
                    <tfoot>
                        <tr style="background: #f8fafc; font-weight: 700; color: #1e293b;">
                            <td colspan="2" style="padding: 8px 10px; border: 1px solid #cbd5e1; text-align: right;">TOTAL:</td>
                            <td id="total_ops_display" style="padding: 8px 10px; border: 1px solid #cbd5e1; text-align: right; color: #2563eb;">Rp 0</td>
                            <td style="border: 1px solid #cbd5e1;"></td>
                        </tr>
                    </tfoot>
                </table>
            `;
            container.innerHTML = html;
            updateOpsTotal();
        }

        async function saveMasterData() {
            const btn = document.getElementById('btnSaveMaster');
            btn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> Menyimpan...';
            btn.disabled = true;

            syncInputsToState();

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            const updatedSalaries = masterSalariesData.map(s => ({
                id: (typeof s.id === 'string' && s.id.startsWith('new_')) ? null : s.id,
                division: s.division,
                name: s.name,
                position: s.position,
                qty: s.qty,
                price: s.price
            }));

            const updatedOps = masterOpsData.map(op => ({
                id: (typeof op.id === 'string' && op.id.startsWith('new_')) ? null : op.id,
                name: op.name,
                qty: op.qty,
                price: op.price
            }));

            try {
                await fetch('/api/finance/master-salaries', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken || '' },
                    body: JSON.stringify({ items: updatedSalaries, deleted_ids: deletedSalariesIds })
                });

                await fetch('/api/finance/monthly-operations', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken || '' },
                    body: JSON.stringify({ items: updatedOps, deleted_ids: deletedOpsIds })
                });

                showToast("Master biaya & gaji berhasil diperbarui!");
                closeMasterModal();
                await loadMasterData();
            } catch(e) {
                console.error("Save master error:", e);
                alert("Gagal menyimpan data master");
            } finally {
                btn.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan';
                btn.disabled = false;
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

        function cleanNumber(val) {
            if (val === null || val === undefined || val === '') return 0;
            if (typeof val === 'number') {
                return isNaN(val) ? 0 : val;
            }
            let s = String(val).trim();
            if (s === '' || s.toLowerCase() === 'nan' || s.toLowerCase() === 'null' || s.toLowerCase() === 'undefined') return 0;
            
            // Bersihkan prefix Rp dan spasi
            s = s.replace(/^[Rr][Pp]\.?\s*/, '').trim();

            // Jika ada koma desimal Indonesia, misal "1.500.000,00" atau "1000,50"
            if (s.includes(',')) {
                s = s.split(',')[0];
                s = s.replace(/\./g, '');
                const num = parseInt(s, 10);
                return isNaN(num) ? 0 : num;
            }

            // Jika memiliki lebih dari 1 titik, misal "1.500.000" (pasti pemisah ribuan)
            if ((s.match(/\./g) || []).length > 1) {
                s = s.replace(/\./g, '');
                const num = parseInt(s, 10);
                return isNaN(num) ? 0 : num;
            }

            // Jika memiliki tepat 1 titik dan diikuti 3 digit (misal "1.000", "10.000", "500.000")
            // Ini adalah pemisah ribuan Indonesia!
            if (/\.\d{3}$/.test(s)) {
                s = s.replace(/\./g, '');
                const num = parseInt(s, 10);
                return isNaN(num) ? 0 : num;
            }

            // Standard float/int string dari database, e.g. "1500000" atau "1500000.00" atau "250.50"
            if (/^-?\d+(\.\d+)?$/.test(s)) {
                const num = parseFloat(s);
                return isNaN(num) ? 0 : Math.round(num);
            }

            // Fallback: hapus semua non-digit kecuali minus
            s = s.replace(/[^0-9-]/g, '');
            const parsed = parseInt(s, 10);
            return isNaN(parsed) ? 0 : parsed;
        }

        function formatRupiah(number) {
            const num = cleanNumber(number);
            return new Intl.NumberFormat('id-ID').format(num);
        }

        function parseRupiah(str) {
            if (str === null || str === undefined || str === '') return 0;
            if (typeof str === 'number') return isNaN(str) ? 0 : str;
            // Ambil hanya digit angka
            const cleanDigits = String(str).replace(/[^0-9]/g, '');
            return parseInt(cleanDigits, 10) || 0;
        }

        function formatRupiahInput(input) {
            const raw = input.value.replace(/[^0-9]/g, '');
            if (!raw) {
                input.value = '';
                return;
            }
            const num = parseInt(raw, 10);
            if (isNaN(num)) {
                input.value = '0';
            } else {
                input.value = new Intl.NumberFormat('id-ID').format(num);
            }
        }

        function openExpenseModal() {
            const bookings = (Array.isArray(globalBookingsFinance) && globalBookingsFinance.length > 0)
                ? globalBookingsFinance
                : (JSON.parse(localStorage.getItem('b_events')) || []);
            const select = document.getElementById('exp_acara');
            let html = `<option value="">— Pengeluaran umum —</option>`;
            bookings.forEach(b => {
                html += `<option value="${b.id}">${b.client_name || 'Klien'} · ${b.event_date || '-'}</option>`;
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
            
            if (!judul || !nominal || isNaN(nominal)) {
                alert("Judul dan nominal wajib diisi!");
                return;
            }
            
            let expenses = [];
            try {
                expenses = JSON.parse(localStorage.getItem('f_expenses')) || [];
                if (!Array.isArray(expenses)) expenses = [];
            } catch(e) {
                expenses = [];
            }
            
            if (id) {
                const index = expenses.findIndex(e => e.id === id);
                if (index > -1) {
                    expenses[index] = { ...expenses[index], acaraId, kategori, tanggal, judul, nominal: cleanNumber(nominal), catatan };
                    showToast('Pengeluaran diperbarui');
                }
            } else {
                const exp = {
                    id: 'exp_' + Date.now(),
                    acaraId, kategori, tanggal, judul, nominal: cleanNumber(nominal), catatan
                };
                expenses.push(exp);
                showToast('Pengeluaran dicatat');
            }
            
            localStorage.setItem('f_expenses', JSON.stringify(expenses));
            
            // Sync to backend API
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            fetch('/api/finance/expenses', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken || ''
                },
                body: JSON.stringify({
                    id: (id && !String(id).startsWith('exp_')) ? id : null,
                    name: judul,
                    amount: cleanNumber(nominal),
                    date: tanggal || new Date().toISOString().split('T')[0],
                    category: kategori,
                    description: catatan,
                    booking_id: acaraId ? parseInt(acaraId) : null
                })
            }).catch(e => console.warn('Could not sync expense to server:', e));

            if(window.logSysActivity && !id) {
                window.logSysActivity('Handler', 'Penapict', 'Mencatat pengeluaran', judul + ' (Rp ' + formatRupiah(nominal) + ')', 'fa-money-bill-wave', 'grey');
            }
            
            closeExpenseModal();
            calculateFinance();
        }

        function switchReportTab(tab) {
            const tabs = ['overview', 'team', 'packages', 'operations'];
            tabs.forEach(t => {
                const btn = document.getElementById('tab_btn_' + t);
                const panel = document.getElementById('panel_report_' + t);
                if (t === tab) {
                    if (btn) {
                        btn.style.background = 'var(--primary)';
                        btn.style.color = '#ffffff';
                        btn.style.border = 'none';
                    }
                    if (panel) panel.style.display = 'block';
                } else {
                    if (btn) {
                        btn.style.background = '#ffffff';
                        btn.style.color = 'var(--text-dark)';
                        btn.style.border = '1px solid #e2e8f0';
                    }
                    if (panel) panel.style.display = 'none';
                }
            });
            loadAnalyticsData();
        }

        function exportTeamLaporanCsv() {
            window.location.href = '/api/finance/export/excel';
        }

        let currentSelectedCrew = null;

        function openCrewSlipModal(crewEncoded) {
            let crew = crewEncoded;
            if (typeof crewEncoded === 'string') {
                try {
                    crew = JSON.parse(decodeURIComponent(crewEncoded));
                } catch(e) {
                    try { crew = JSON.parse(crewEncoded); } catch(err){}
                }
            }
            currentSelectedCrew = crew;
            document.getElementById('modal_slip_crew_name').innerText = crew.name || 'Karyawan';
            document.getElementById('modal_slip_crew_pos').innerText = crew.position || crew.division || '-';
            document.getElementById('modal_slip_period').innerText = document.getElementById('report_period')?.innerText || 'Bulan Berjalan';
            document.getElementById('modal_slip_sessions').innerText = (crew.sessions || 0) + ' Sesi Foto / Proyek';
            document.getElementById('modal_slip_base').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(crew.base_salary || 0);
            document.getElementById('modal_slip_meal').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(crew.meal_transport || 0);
            document.getElementById('modal_slip_overtime').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(crew.overtime || 0);
            document.getElementById('modal_slip_bonus').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(crew.bonus || 0);
            document.getElementById('modal_slip_total').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(crew.net_salary || 0);

            const m = document.getElementById('crewSlipModal');
            if (m) m.style.display = 'flex';
        }

        function closeCrewSlipModal() {
            const m = document.getElementById('crewSlipModal');
            if (m) m.style.display = 'none';
        }

        function printCrewSlip() {
            if (!currentSelectedCrew) return;
            const printContent = document.getElementById('printableSlipArea').innerHTML;
            const printWindow = window.open('', '_blank', 'width=750,height=650');
            printWindow.document.write(`
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Slip Gaji - ${currentSelectedCrew.name}</title>
                    <style>
                        body { font-family: 'Inter', -apple-system, sans-serif; padding: 30px; color: #181c32; }
                        .badge { padding: 4px 8px; border-radius: 6px; font-size: 11px; }
                        table { width: 100%; border-collapse: collapse; }
                        @media print {
                            body { padding: 10px; }
                        }
                    </style>
                </head>
                <body>
                    <div style="text-align: center; margin-bottom: 24px; border-bottom: 2px solid #334155; padding-bottom: 14px;">
                        <h2 style="margin:0; font-size: 20px;">SLIP PENGGAJIAN KARYAWAN</h2>
                        <div style="font-size: 13px; color: #64748b; margin-top: 4px;">CloseMateAI Studio &bull; Periode: ${document.getElementById('report_period')?.innerText || 'Bulan Berjalan'}</div>
                    </div>
                    <div style="margin-bottom: 16px;">
                        <strong>Nama:</strong> ${currentSelectedCrew.name} <br>
                        <strong>Jabatan / Divisi:</strong> ${currentSelectedCrew.position || currentSelectedCrew.division}
                    </div>
                    ${printContent}
                    <div style="display: flex; justify-content: space-between; margin-top: 50px;">
                        <div style="text-align: center;">
                            <p style="margin-bottom: 60px;">Penerima,</p>
                            <p><strong>(${currentSelectedCrew.name})</strong></p>
                        </div>
                        <div style="text-align: center;">
                            <p style="margin-bottom: 60px;">Finance / Pimpinan Studio,</p>
                            <p><strong>(.......................................)</strong></p>
                        </div>
                    </div>
                </body>
                </html>
            `);
            printWindow.document.close();
            printWindow.focus();
            setTimeout(() => {
                printWindow.print();
            }, 400);
        }

        function printAllCrewRecap() {
            window.open('/hris/recap/print', '_blank');
        }

        async function loadAnalyticsData() {
            try {
                let url = '/api/finance/analytics';
                if (currentFilterStart && currentFilterEnd) {
                    url += `?start=${currentFilterStart}&end=${currentFilterEnd}`;
                }
                const res = await fetch(url);
                const json = await res.json();
                if (!json.success || !json.data) return;

                const d = json.data;

                // 1. Team Tab
                if (d.team) {
                    if (document.getElementById('stat_team_crew')) document.getElementById('stat_team_crew').innerText = d.team.total_crew + ' Orang';
                    if (document.getElementById('stat_team_salary')) document.getElementById('stat_team_salary').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(d.team.total_salary);
                    if (document.getElementById('stat_team_rating')) document.getElementById('stat_team_rating').innerHTML = '<i class="fa-solid fa-star"></i> ' + d.team.avg_rating + ' <span style="font-size: 14px; color: var(--text-muted); font-weight: 500;">/ 5.0</span>';
                    if (document.getElementById('score_client_reviews')) document.getElementById('score_client_reviews').innerText = d.team.avg_rating;
                    
                    // Render Crew Table
                    const tbodyCrews = document.getElementById('tbody_team_crews');
                    if (tbodyCrews && Array.isArray(d.team.crew_list)) {
                        tbodyCrews.innerHTML = d.team.crew_list.map(c => `
                            <tr>
                                <td><strong>${c.name}</strong></td>
                                <td><span class="badge" style="background:#e0f2fe; color:#0369a1; padding:4px 8px; border-radius:6px; font-size:11px; font-weight:600;">${c.position || c.division}</span></td>
                                <td style="text-align: center;"><strong>${c.sessions} Sesi</strong></td>
                                <td style="text-align: right;">Rp ${new Intl.NumberFormat('id-ID').format(c.base_salary)}</td>
                                <td style="text-align: right;">Rp ${new Intl.NumberFormat('id-ID').format(c.meal_transport)}</td>
                                <td style="text-align: right;">Rp ${new Intl.NumberFormat('id-ID').format(c.overtime)}</td>
                                <td style="text-align: right; color:#10b981; font-weight:600;">Rp ${new Intl.NumberFormat('id-ID').format(c.bonus)}</td>
                                <td style="text-align: right; font-weight:700;">Rp ${new Intl.NumberFormat('id-ID').format(c.net_salary)}</td>
                                <td style="text-align: center;">
                                    <button class="btn btn-sm btn-primary" onclick="openCrewSlipModal('${encodeURIComponent(JSON.stringify(c))}')" style="font-size: 11px; padding: 4px 10px; border-radius: 6px; cursor: pointer; display: inline-flex; align-items: center; gap: 4px; white-space: nowrap;">
                                        <i class="fa-solid fa-file-invoice-dollar"></i> Slip Gaji
                                    </button>
                                </td>
                            </tr>
                        `).join('');
                    }

                    // Render Office Evaluations Table
                    const tbodyOffice = document.getElementById('tbody_office_evals');
                    if (tbodyOffice && Array.isArray(d.team.office_evaluations)) {
                        if (d.team.office_evaluations.length === 0) {
                            tbodyOffice.innerHTML = `<tr><td colspan="7" class="empty-row" style="text-align: center; color: var(--text-muted); padding: 16px;">Belum ada data evaluasi kantor. Klik "+ Catat Evaluasi Kantor" untuk menambah.</td></tr>`;
                        } else {
                            tbodyOffice.innerHTML = d.team.office_evaluations.map(oe => `
                                <tr>
                                    <td><strong>${oe.editor_name || '-'}</strong></td>
                                    <td>${oe.production_staff_name || '-'}</td>
                                    <td><span class="badge" style="background:#fee2e2; color:#b91c1c; font-weight:700; padding:3px 8px; border-radius:6px; font-size:11px;">${oe.fast_edit_type}</span></td>
                                    <td>${oe.edit_completion_hours ? oe.edit_completion_hours + ' Jam' : '-'}</td>
                                    <td><span class="badge" style="background:#dcfce7; color:#15803d; font-weight:600; padding:3px 8px; border-radius:6px; font-size:11px;"><i class="fa-solid fa-check"></i> ${oe.edit_status}</span></td>
                                    <td><span class="badge" style="background:#e0f2fe; color:#0369a1; padding:3px 8px; border-radius:6px; font-size:11px;">${oe.print_status}</span></td>
                                    <td style="text-align: right;">
                                        <button class="btn btn-outline" style="padding: 3px 8px; font-size: 11px;" onclick="deleteOfficeEval(${oe.id})"><i class="fa-solid fa-trash" style="color: #ef4444;"></i></button>
                                    </td>
                                </tr>
                            `).join('');
                        }
                    }

                    // Render Reviews Table
                    const tbodyRev = document.getElementById('tbody_client_reviews');
                    if (tbodyRev && Array.isArray(d.team.reviews)) {
                        if (d.team.reviews.length === 0) {
                            tbodyRev.innerHTML = `<tr><td colspan="5" class="empty-row" style="text-align: center; color: var(--text-muted); padding: 16px;">Belum ada review klien masuk.</td></tr>`;
                        } else {
                            tbodyRev.innerHTML = d.team.reviews.map(r => `
                                <tr>
                                    <td><strong>${r.client_name}</strong></td>
                                    <td>${r.field_crew_names || '-'}</td>
                                    <td><span style="color:#f59e0b; font-weight:700;"><i class="fa-solid fa-star"></i> ${r.rating_overall}.0</span></td>
                                    <td style="font-size: 12px; color: #475569;">"${r.review_comment || '-'}"</td>
                                    <td style="text-align: right;">
                                        <button class="btn btn-outline" style="padding: 3px 8px; font-size: 11px;" onclick="deleteClientReview(${r.id})"><i class="fa-solid fa-trash" style="color: #ef4444;"></i></button>
                                    </td>
                                </tr>
                            `).join('');
                        }
                    }

                    // Render Marketing Table
                    const tbodyMkt = document.getElementById('tbody_team_marketing');
                    if (tbodyMkt && Array.isArray(d.team.marketing)) {
                        tbodyMkt.innerHTML = d.team.marketing.map(m => `
                            <tr>
                                <td><strong>${m.name}</strong></td>
                                <td>${m.leads} Leads</td>
                                <td><strong>${m.closing} Booking</strong></td>
                                <td><span class="badge" style="background:#dcfce7; color:#15803d; font-weight:700; padding:4px 8px; border-radius:6px;">${m.rate}</span></td>
                                <td>Rp ${new Intl.NumberFormat('id-ID').format(m.omset)}</td>
                                <td style="color:#10b981; font-weight:700;">Rp ${new Intl.NumberFormat('id-ID').format(m.commission)}</td>
                            </tr>
                        `).join('');
                    }
                }

                // 2. Packages Tab
                if (d.packages) {
                    if (d.packages.most_popular && document.getElementById('stat_pkg_most_popular')) {
                        document.getElementById('stat_pkg_most_popular').innerText = d.packages.most_popular.name;
                    }
                    if (d.packages.most_profitable && document.getElementById('stat_pkg_most_profitable')) {
                        document.getElementById('stat_pkg_most_profitable').innerText = d.packages.most_profitable.name;
                    }
                    if (d.packages.least_popular && document.getElementById('stat_pkg_least_popular')) {
                        document.getElementById('stat_pkg_least_popular').innerText = d.packages.least_popular.name;
                    }

                    // Render Packages List
                    const tbodyPkgs = document.getElementById('tbody_packages_list');
                    if (tbodyPkgs && Array.isArray(d.packages.list)) {
                        tbodyPkgs.innerHTML = d.packages.list.map(p => `
                            <tr>
                                <td><strong>${p.name}</strong></td>
                                <td>${p.category}</td>
                                <td>Rp ${new Intl.NumberFormat('id-ID').format(p.price)}</td>
                                <td style="text-align: center;"><strong>${p.sold}</strong></td>
                                <td>Rp ${new Intl.NumberFormat('id-ID').format(p.omset)}</td>
                                <td>Rp ${new Intl.NumberFormat('id-ID').format(p.hpp)}</td>
                                <td style="color:#10b981; font-weight:700;">Rp ${new Intl.NumberFormat('id-ID').format(p.profit)}</td>
                                <td><span class="badge" style="background:#dcfce7; color:#15803d; font-weight:700;">${p.margin}%</span></td>
                                <td><span style="font-size: 11px; color:#15803d;"><i class="fa-solid fa-circle-check"></i> ${p.margin > 60 ? 'Margin Sehat' : 'Evaluasi Biaya'}</span></td>
                            </tr>
                        `).join('');
                    }
                }

                // 3. Operations Tab
                if (d.operations) {
                    if (document.getElementById('stat_unpaid_receivables')) {
                        document.getElementById('stat_unpaid_receivables').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(d.operations.total_receivables);
                    }

                    // Render Unpaid Clients
                    const tbodyUnpaid = document.getElementById('tbody_unpaid_clients');
                    if (tbodyUnpaid && Array.isArray(d.operations.unpaid_clients)) {
                        tbodyUnpaid.innerHTML = d.operations.unpaid_clients.map(u => `
                            <tr>
                                <td><strong>${u.client_name}</strong></td>
                                <td>${u.event_date}</td>
                                <td>${u.package_name}</td>
                                <td>Rp ${new Intl.NumberFormat('id-ID').format(u.package_price)}</td>
                                <td>Rp ${new Intl.NumberFormat('id-ID').format(u.paid_amount)}</td>
                                <td style="color: #ef4444; font-weight: 700;">Rp ${new Intl.NumberFormat('id-ID').format(u.remaining)}</td>
                                <td>H-1 Acara</td>
                                <td><button class="btn btn-outline" style="padding: 4px 10px; font-size: 11px;" onclick="window.open('https://wa.me/${u.client_phone ? u.client_phone.replace(/[^0-9]/g, '') : ''}?text=Halo%20${encodeURIComponent(u.client_name)}%2C%20ini%20reminder%20pelunasan%20sisa%20tagihan%20booking%20sebesar%20Rp%20${new Intl.NumberFormat('id-ID').format(u.remaining)}%20ya.')"><i class="fa-brands fa-whatsapp" style="color: #10b981;"></i> Kirim Tagihan WA</button></td>
                            </tr>
                        `).join('');
                    }

                    // Render Lead Sources
                    const tbodyLeads = document.getElementById('tbody_lead_sources');
                    if (tbodyLeads && Array.isArray(d.operations.lead_sources)) {
                        tbodyLeads.innerHTML = d.operations.lead_sources.map(ls => `
                            <tr>
                                <td><strong>${ls.channel}</strong></td>
                                <td style="text-align: center;">${ls.leads}</td>
                                <td style="text-align: center;"><span class="badge" style="background:#dcfce7; color:#15803d; font-weight:700;">${ls.closing}</span></td>
                                <td style="color:#10b981; font-weight:700;">Rp ${new Intl.NumberFormat('id-ID').format(ls.revenue)}</td>
                            </tr>
                        `).join('');
                    }
                }

            } catch (e) {
                console.error("Gagal load analytics data:", e);
            }
        }

        function openOfficeEvalModal() {
            document.getElementById('oe_id').value = '';
            document.getElementById('oe_editor_name').value = '';
            document.getElementById('oe_prod_staff').value = '';
            document.getElementById('oe_completion_hours').value = '';
            document.getElementById('oe_print_deadline').value = '';
            document.getElementById('oe_ready_pickup').value = '';
            document.getElementById('oe_notes').value = '';
            document.getElementById('officeEvalModal').style.display = 'flex';
        }

        function closeOfficeEvalModal() {
            document.getElementById('officeEvalModal').style.display = 'none';
        }

        async function saveOfficeEvaluation() {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const data = {
                id: document.getElementById('oe_id').value || null,
                editor_name: document.getElementById('oe_editor_name').value,
                production_staff_name: document.getElementById('oe_prod_staff').value,
                fast_edit_type: document.getElementById('oe_service_type').value,
                edit_status: document.getElementById('oe_edit_status').value,
                edit_completion_hours: document.getElementById('oe_completion_hours').value,
                quality_score: document.getElementById('oe_quality_score').value,
                print_deadline_date: document.getElementById('oe_print_deadline').value,
                ready_to_pickup_date: document.getElementById('oe_ready_pickup').value,
                print_status: document.getElementById('oe_print_status').value,
                office_notes: document.getElementById('oe_notes').value,
            };

            const res = await fetch('/api/office-evaluations', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify(data)
            });

            const json = await res.json();
            if (json.success) {
                showToast('Penilaian kinerja office berhasil disimpan!', 'success');
                closeOfficeEvalModal();
                loadAnalyticsData();
            } else {
                alert('Gagal menyimpan penilaian');
            }
        }

        async function deleteOfficeEval(id) {
            if (!confirm('Yakin ingin menghapus data evaluasi ini?')) return;
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const res = await fetch('/api/office-evaluations/' + id, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': csrfToken }
            });
            const json = await res.json();
            if (json.success) {
                showToast('Data evaluasi berhasil dihapus', 'success');
                loadAnalyticsData();
            }
        }

        function openClientReviewModal() {
            document.getElementById('cr_client_name').value = '';
            document.getElementById('cr_client_phone').value = '';
            document.getElementById('cr_field_crew_names').value = '';
            document.getElementById('cr_review_comment').value = '';
            document.getElementById('clientReviewModal').style.display = 'flex';
        }

        function closeClientReviewModal() {
            document.getElementById('clientReviewModal').style.display = 'none';
        }

        async function saveClientReview() {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const tags = Array.from(document.querySelectorAll('input[name="cr_tags"]:checked')).map(el => el.value);
            const data = {
                client_name: document.getElementById('cr_client_name').value || 'Klien Studio',
                client_phone: document.getElementById('cr_client_phone').value || '-',
                field_crew_names: document.getElementById('cr_field_crew_names').value || 'Tim Lapangan',
                rating_overall: document.getElementById('cr_rating_overall').value,
                rating_field_crew: document.getElementById('cr_rating_overall').value,
                rating_punctuality: document.getElementById('cr_rating_overall').value,
                rating_hospitality: document.getElementById('cr_rating_overall').value,
                positive_tags: tags,
                review_comment: document.getElementById('cr_review_comment').value || 'Pelayanan memuaskan'
            };

            const res = await fetch('/api/reviews', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify(data)
            });

            const json = await res.json();
            if (json.success) {
                showToast('Ulasan klien berhasil disimpan!', 'success');
                closeClientReviewModal();
                loadAnalyticsData();
            } else {
                alert('Gagal menyimpan ulasan klien');
            }
        }

        function copyClientReviewLink() {
            const url = window.location.origin + '/review/penapict';
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(url).then(() => {
                    showToast('Link Form Review Klien (Bintang 5) berhasil disalin!', 'success');
                }).catch(() => {
                    prompt('Salin link form review klien:', url);
                });
            } else {
                prompt('Salin link form review klien:', url);
            }
        }

        async function deleteClientReview(id) {
            if (!confirm('Yakin ingin menghapus ulasan klien ini?')) return;
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const res = await fetch('/api/reviews/' + id, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': csrfToken }
            });
            const json = await res.json();
            if (json.success) {
                showToast('Ulasan klien berhasil dihapus', 'success');
                loadAnalyticsData();
            }
        }

        function calculateFinance() {
            let allBookings = Array.isArray(globalBookingsFinance) ? globalBookingsFinance : [];
            let allExpenses = [];
            try {
                allExpenses = JSON.parse(localStorage.getItem('f_expenses')) || [];
                if (!Array.isArray(allExpenses)) allExpenses = [];
            } catch(e) {
                allExpenses = [];
            }
            
            // Filter by date
            const filterStart = currentFilterStart ? new Date(currentFilterStart) : null;
            if (filterStart) filterStart.setHours(0, 0, 0, 0);
            const filterEnd = currentFilterEnd ? new Date(currentFilterEnd) : null;
            if (filterEnd) filterEnd.setHours(23, 59, 59, 999);
            
            const bookings = allBookings.filter(b => {
                if(!b.event_date) return true;
                const d = new Date(b.event_date.substring(0, 10) + 'T00:00:00');
                if(isNaN(d.getTime())) return true;
                if(filterStart && d < filterStart) return false;
                if(filterEnd && d > filterEnd) return false;
                return true;
            });
            
            const expenses = allExpenses.filter(e => {
                if(!e.tanggal) return true;
                const d = new Date(e.tanggal.substring(0, 10) + 'T00:00:00');
                if(isNaN(d.getTime())) return true;
                if(filterStart && d < filterStart) return false;
                if(filterEnd && d > filterEnd) return false;
                return true;
            });
            
            let totalOmset = 0;
            let uangMasuk = 0;
            let pengeluaranManual = 0;
            let pengeluaranOperasional = 0;
            
            bookings.forEach(b => {
                const bIncome = cleanNumber(b.total_income) || (cleanNumber(b.package_price) * (cleanNumber(b.package_qty) || 1));
                const bPaid = cleanNumber(b.paid_amount);
                let bOpCost = cleanNumber(b.total_operational_cost);
                if (bOpCost === 0 && Array.isArray(b.operational_costs) && b.operational_costs.length > 0) {
                    bOpCost = b.operational_costs.reduce((sum, item) => sum + (cleanNumber(item.price) * (cleanNumber(item.qty) || 1)), 0);
                }
                
                totalOmset += bIncome;
                uangMasuk += bPaid;
                pengeluaranOperasional += bOpCost;
            });
            
            expenses.forEach(e => {
                pengeluaranManual += cleanNumber(e.nominal);
            });
            
            const sisaPiutang = totalOmset - uangMasuk;
            const totalPengeluaran = pengeluaranOperasional + pengeluaranManual;
            const profitBersih = totalOmset - totalPengeluaran;
            
            document.getElementById('val_omset').innerText = 'Rp ' + formatRupiah(totalOmset);
            document.getElementById('val_masuk').innerText = 'Rp ' + formatRupiah(uangMasuk);
            document.getElementById('val_piutang').innerText = 'Rp ' + formatRupiah(sisaPiutang > 0 ? sisaPiutang : 0);
            document.getElementById('val_pengeluaran').innerText = 'Rp ' + formatRupiah(totalPengeluaran);
            
            const profitEl = document.getElementById('val_profit');
            profitEl.innerText = (profitBersih < 0 ? '- Rp ' : 'Rp ') + formatRupiah(Math.abs(profitBersih));
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
                        const b = allBookings.find(x => x.id == e.acaraId);
                        if (b) acaraName = `<span style="background: rgba(107,92,216,0.1); color: var(--primary); padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 500;">${b.client_name || 'Klien'}</span>`;
                    }
                    expHtml += `
                    <tr>
                        <td>${e.tanggal || '-'}</td>
                        <td style="font-weight: 500;">${e.judul || '-'}</td>
                        <td><span style="color: var(--text-muted); font-size: 12px;">${e.kategori || '-'}</span></td>
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
                    const linkedExpenses = expenses.filter(e => e.acaraId == b.id).reduce((sum, e) => sum + cleanNumber(e.nominal), 0);
                    let bOpCost = cleanNumber(b.total_operational_cost);
                    if (bOpCost === 0 && Array.isArray(b.operational_costs) && b.operational_costs.length > 0) {
                        bOpCost = b.operational_costs.reduce((sum, item) => sum + (cleanNumber(item.price) * (cleanNumber(item.qty) || 1)), 0);
                    }
                    const bookingCost = bOpCost + linkedExpenses;
                    const bookingIncome = cleanNumber(b.total_income) || (cleanNumber(b.package_price) * (cleanNumber(b.package_qty) || 1));
                    const bookingProfit = bookingIncome - bookingCost;
                    
                    let profitColor = 'var(--success)';
                    let displayProfit = 'Rp ' + formatRupiah(bookingProfit);
                    if (bookingProfit < 0) {
                        profitColor = 'var(--danger)';
                        displayProfit = '- Rp ' + formatRupiah(Math.abs(bookingProfit));
                    }
                    
                    profHtml += `
                    <tr>
                        <td style="font-weight: 600;">${b.client_name || '-'}</td>
                        <td style="color: var(--text-muted);">${b.event_date || '-'}</td>
                        <td>${b.package_name || '-'}</td>
                        <td>Rp ${formatRupiah(bookingIncome)}</td>
                        <td style="color: var(--danger);">Rp ${formatRupiah(bookingCost)}</td>
                        <td style="color: ${profitColor}; font-weight: 600;">${displayProfit}</td>
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
            document.getElementById('tooltip_profit').innerText = (profitBersih < 0 ? '- Rp ' : 'Rp ') + formatRupiah(Math.abs(profitBersih));
            
            const periodDate = currentFilterStart || new Date();
            const formatter = new Intl.DateTimeFormat('id-ID', { month: 'short', year: '2-digit' });
            const shortPeriod = formatter.format(periodDate);
            const tooltipTitleEl = document.getElementById('tooltip_title');
            if (tooltipTitleEl) tooltipTitleEl.innerText = shortPeriod;
            const xAxisEl = document.querySelector('.x-axis');
            if (xAxisEl) xAxisEl.innerText = shortPeriod;
            
            // Basic chart update mockup
            const chartProfit = uangMasuk - totalPengeluaran; // Cash flow profit
            let max = Math.max(uangMasuk, totalPengeluaran, Math.abs(chartProfit));
            if (max <= 0 || isNaN(max)) max = 1000000;
            max = max * 1.2; // Add 20% padding at top so bars aren't touching the very edge
            
            let masukPct = Math.max(0, Math.min((uangMasuk / max) * 100, 100));
            let keluarPct = Math.max(0, Math.min((totalPengeluaran / max) * 100, 100));
            let profitPct = Math.max(0, Math.min((Math.abs(chartProfit) / max) * 100, 100));
            
            const lineMasuk = document.getElementById('chart_line_masuk');
            if (lineMasuk) lineMasuk.style.bottom = `${masukPct}%`;
            
            const barKeluar = document.getElementById('chart_bar_keluar');
            if (barKeluar) barKeluar.style.height = `${keluarPct}%`;
            
            const dotProfit = document.getElementById('chart_dot_profit');
            const lineProfit = document.getElementById('chart_line_profit');
            
            if (dotProfit && lineProfit) {
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

            // Update Y-Axis labels dynamically
            const yAxisEl = document.getElementById('chart_y_axis');
            if (yAxisEl) {
                function formatAxisLabel(val) {
                    if (val >= 1000000) {
                        return (val / 1000000).toFixed(val % 1000000 === 0 ? 0 : 1) + 'jt -';
                    } else if (val >= 1000) {
                        return Math.round(val / 1000) + 'rb -';
                    } else {
                        return Math.round(val) + ' -';
                    }
                }
                yAxisEl.innerHTML = `
                    <div>${formatAxisLabel(max)}</div>
                    <div>${formatAxisLabel(max * 0.75)}</div>
                    <div>${formatAxisLabel(max * 0.5)}</div>
                    <div>${formatAxisLabel(max * 0.25)}</div>
                    <div>0 -</div>
                `;
            }
        }

        function editExpense(id) {
            let expenses = [];
            try {
                expenses = JSON.parse(localStorage.getItem('f_expenses')) || [];
                if (!Array.isArray(expenses)) expenses = [];
            } catch(e) { expenses = []; }
            const e = expenses.find(x => x.id === id);
            if (!e) return;
            
            const bookings = (Array.isArray(globalBookingsFinance) && globalBookingsFinance.length > 0)
                ? globalBookingsFinance 
                : (JSON.parse(localStorage.getItem('b_events')) || []);
            const select = document.getElementById('exp_acara');
            let html = `<option value="">— Pengeluaran umum —</option>`;
            bookings.forEach(b => {
                html += `<option value="${b.id}">${b.client_name || 'Klien'} · ${b.event_date || '-'}</option>`;
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
                let expenses = [];
                try {
                    expenses = JSON.parse(localStorage.getItem('f_expenses')) || [];
                    if (!Array.isArray(expenses)) expenses = [];
                } catch(e) { expenses = []; }
                expenses = expenses.filter(e => e.id !== id);
                localStorage.setItem('f_expenses', JSON.stringify(expenses));
                
                // Also delete from server if numeric ID
                if (id && !String(id).startsWith('exp_')) {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                    fetch('/api/finance/expenses/' + id, {
                        method: 'DELETE',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken || ''
                        }
                    }).catch(e => console.warn('Could not delete from server:', e));
                }

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
            let url = '/api/finance/export/excel';
            const params = [];
            if (currentFilterStart && currentFilterEnd) {
                const s = (currentFilterStart instanceof Date ? currentFilterStart : new Date(currentFilterStart)).toISOString().split('T')[0];
                const e = (currentFilterEnd instanceof Date ? currentFilterEnd : new Date(currentFilterEnd)).toISOString().split('T')[0];
                params.push(`start=${s}`);
                params.push(`end=${e}`);
            }
            if (params.length > 0) {
                url += '?' + params.join('&');
            }
            showToast('Mengunduh Laporan Keuangan Multi-Tab (WPS / Excel)...');
            window.location.href = url;
        }

        function exportLaporanKeuangan(format = 'excel') {
            let allBookings = (Array.isArray(globalBookingsFinance) && globalBookingsFinance.length > 0)
                ? globalBookingsFinance
                : (JSON.parse(localStorage.getItem('b_events')) || []);
            let allExpenses = [];
            try {
                allExpenses = JSON.parse(localStorage.getItem('f_expenses')) || [];
                if (!Array.isArray(allExpenses)) allExpenses = [];
            } catch(e) {
                allExpenses = [];
            }
            
            const filterStart = currentFilterStart ? new Date(currentFilterStart) : null;
            if (filterStart) filterStart.setHours(0, 0, 0, 0);
            const filterEnd = currentFilterEnd ? new Date(currentFilterEnd) : null;
            if (filterEnd) filterEnd.setHours(23, 59, 59, 999);
            
            const bookings = allBookings.filter(b => {
                if(!b.event_date) return true;
                const d = new Date(b.event_date.substring(0, 10) + 'T00:00:00');
                if(isNaN(d.getTime())) return true;
                if(filterStart && d < filterStart) return false;
                if(filterEnd && d > filterEnd) return false;
                return true;
            });
            
            const expenses = allExpenses.filter(e => {
                if(!e.tanggal) return true;
                const d = new Date(e.tanggal.substring(0, 10) + 'T00:00:00');
                if(isNaN(d.getTime())) return true;
                if(filterStart && d < filterStart) return false;
                if(filterEnd && d > filterEnd) return false;
                return true;
            });
            
            let totalOmset = 0;
            let uangMasuk = 0;
            let pengeluaranOperasional = 0;
            let pengeluaranManual = 0;
            
            bookings.forEach(b => {
                const bIncome = cleanNumber(b.total_income) || (cleanNumber(b.package_price) * (cleanNumber(b.package_qty) || 1));
                const bPaid = cleanNumber(b.paid_amount);
                let bOpCost = cleanNumber(b.total_operational_cost);
                if (bOpCost === 0 && Array.isArray(b.operational_costs) && b.operational_costs.length > 0) {
                    bOpCost = b.operational_costs.reduce((sum, item) => sum + (cleanNumber(item.price) * (cleanNumber(item.qty) || 1)), 0);
                }
                
                totalOmset += bIncome;
                uangMasuk += bPaid;
                pengeluaranOperasional += bOpCost;
            });
            expenses.forEach(e => pengeluaranManual += cleanNumber(e.nominal));
            
            const sisaPiutang = Math.max(0, totalOmset - uangMasuk);
            const totalPengeluaran = pengeluaranOperasional + pengeluaranManual;
            const profitBersih = totalOmset - totalPengeluaran;
            const cashFlowProfit = uangMasuk - totalPengeluaran;

            const fmtRp = (num) => {
                const rounded = Math.round(cleanNumber(num));
                return `Rp ${formatRupiah(rounded)}`;
            };

            const dFormatter = new Intl.DateTimeFormat('id-ID', { day: '2-digit', month: 'long', year: 'numeric' });
            let periodeStr = 'Semua Periode';
            if (currentFilterStart && currentFilterEnd) {
                periodeStr = `${dFormatter.format(currentFilterStart)} s/d ${dFormatter.format(currentFilterEnd)}`;
            } else if (activeFilterType) {
                periodeStr = activeFilterType;
            }
            const nowStr = new Intl.DateTimeFormat('id-ID', { dateStyle: 'medium', timeStyle: 'short' }).format(new Date());
            const monthFormatter = new Intl.DateTimeFormat('id-ID', { month: 'long', year: 'numeric' });
            const monthStr = monthFormatter.format(currentFilterStart || new Date());
            const yyyymmdd = new Date().toISOString().split('T')[0];

            if (format === 'csv') {
                // Raw CSV export
                const escapeCsv = (val) => {
                    if (val === null || val === undefined) return '""';
                    const str = String(val).replace(/"/g, '""');
                    return `"${str}"`;
                };

                const lines = [];
                lines.push([escapeCsv("LAPORAN KEUANGAN & ARUS KAS")]);
                lines.push([escapeCsv(`Periode: ${periodeStr}`)]);
                lines.push([escapeCsv(`Waktu Unduh: ${nowStr}`)]);
                lines.push([]);

                lines.push([escapeCsv("1. RINGKASAN EKSEKUTIF KEUANGAN")]);
                lines.push([escapeCsv("Indikator"), escapeCsv("Nominal"), escapeCsv("Keterangan")]);
                lines.push([escapeCsv("Total Omset"), escapeCsv(fmtRp(totalOmset)), escapeCsv("Total nilai seluruh kontrak/paket acara")]);
                lines.push([escapeCsv("Total Uang Masuk (Kas Riil)"), escapeCsv(fmtRp(uangMasuk)), escapeCsv("Total DP dan pelunasan yang telah diterima")]);
                lines.push([escapeCsv("Sisa Piutang"), escapeCsv(fmtRp(sisaPiutang)), escapeCsv("Tagihan yang belum dilunasi klien")]);
                lines.push([escapeCsv("Biaya Operasional Acara"), escapeCsv(fmtRp(pengeluaranOperasional)), escapeCsv("Total biaya operasional tim/alat pada paket")]);
                lines.push([escapeCsv("Biaya Pengeluaran Lainnya"), escapeCsv(fmtRp(pengeluaranManual)), escapeCsv("Biaya operasional kantor, transport, konsumsi, kas keluar")]);
                lines.push([escapeCsv("Total Seluruh Pengeluaran"), escapeCsv(fmtRp(totalPengeluaran)), escapeCsv("Akumulasi biaya operasional + pengeluaran lain")]);
                lines.push([escapeCsv("Net Cash Flow (Kas Riil - Pengeluaran)"), escapeCsv(fmtRp(cashFlowProfit)), escapeCsv(cashFlowProfit >= 0 ? "Surplus kas (Positif)" : "Defisit kas")]);
                lines.push([escapeCsv("Proyeksi Laba Bersih (Omset - Pengeluaran)"), escapeCsv(fmtRp(profitBersih)), escapeCsv("Estimasi laba bersih setelah seluruh tagihan lunas")]);
                lines.push([]);

                lines.push([escapeCsv("2. ARUS KAS BULANAN (CASH FLOW)")]);
                lines.push([escapeCsv("Periode"), escapeCsv("Uang Masuk"), escapeCsv("Pengeluaran"), escapeCsv("Net Cash Flow"), escapeCsv("Status Arus Kas")]);
                lines.push([
                    escapeCsv(monthStr),
                    escapeCsv(fmtRp(uangMasuk)),
                    escapeCsv(fmtRp(totalPengeluaran)),
                    escapeCsv(fmtRp(cashFlowProfit)),
                    escapeCsv(cashFlowProfit >= 0 ? "Surplus (Sehat)" : "Defisit")
                ]);
                lines.push([]);

                lines.push([escapeCsv("3. PROFITABILITAS PER EVENT / ACARA")]);
                lines.push([escapeCsv("No"), escapeCsv("Nama Klien"), escapeCsv("Tanggal Acara"), escapeCsv("Paket Layanan"), escapeCsv("Harga Paket"), escapeCsv("Biaya Operasional"), escapeCsv("Biaya Tambahan"), escapeCsv("Total Biaya Acara"), escapeCsv("Profit Bersih Acara"), escapeCsv("Margin (%)")]);
                
                let totInc = 0, totOp = 0, totExt = 0, totCost = 0, totProf = 0;
                if (bookings.length === 0) {
                    lines.push([escapeCsv("-"), escapeCsv("Tidak ada data booking pada periode ini"), escapeCsv(""), escapeCsv(""), escapeCsv(""), escapeCsv(""), escapeCsv(""), escapeCsv(""), escapeCsv(""), escapeCsv("")]);
                } else {
                    bookings.forEach((b, idx) => {
                        const linked = expenses.filter(e => e.acaraId == b.id).reduce((sum, e) => sum + cleanNumber(e.nominal), 0);
                        let bOp = cleanNumber(b.total_operational_cost);
                        if (bOp === 0 && Array.isArray(b.operational_costs) && b.operational_costs.length > 0) {
                            bOp = b.operational_costs.reduce((sum, item) => sum + (cleanNumber(item.price) * (cleanNumber(item.qty) || 1)), 0);
                        }
                        const bC = bOp + linked;
                        const bI = cleanNumber(b.total_income) || (cleanNumber(b.package_price) * (cleanNumber(b.package_qty) || 1));
                        const bP = bI - bC;
                        const m = bI > 0 ? ((bP / bI) * 100).toFixed(1) + '%' : '0%';
                        totInc += bI; totOp += bOp; totExt += linked; totCost += bC; totProf += bP;
                        lines.push([escapeCsv(idx + 1), escapeCsv(b.client_name || '-'), escapeCsv(b.event_date || '-'), escapeCsv(b.package_name || '-'), escapeCsv(fmtRp(bI)), escapeCsv(fmtRp(bOp)), escapeCsv(fmtRp(linked)), escapeCsv(fmtRp(bC)), escapeCsv(fmtRp(bP)), escapeCsv(m)]);
                    });
                    const totM = totInc > 0 ? ((totProf / totInc) * 100).toFixed(1) + '%' : '0%';
                    lines.push([escapeCsv("TOTAL"), escapeCsv(`${bookings.length} Acara`), escapeCsv(""), escapeCsv(""), escapeCsv(fmtRp(totInc)), escapeCsv(fmtRp(totOp)), escapeCsv(fmtRp(totExt)), escapeCsv(fmtRp(totCost)), escapeCsv(fmtRp(totProf)), escapeCsv(totM)]);
                }
                lines.push([]);

                lines.push([escapeCsv("4. RINCIAN PENGELUARAN")]);
                lines.push([escapeCsv("No"), escapeCsv("Tanggal"), escapeCsv("Kategori"), escapeCsv("Judul / Keterangan"), escapeCsv("Terkait Acara"), escapeCsv("Nominal"), escapeCsv("Catatan")]);
                let totExp = 0;
                if (expenses.length === 0) {
                    lines.push([escapeCsv("-"), escapeCsv("Tidak ada data pengeluaran pada periode ini"), escapeCsv(""), escapeCsv(""), escapeCsv(""), escapeCsv(""), escapeCsv("")]);
                } else {
                    expenses.forEach((e, idx) => {
                        let acara = '-';
                        if (e.acaraId) {
                            const b = allBookings.find(x => x.id == e.acaraId);
                            if (b) acara = b.client_name || '-';
                        }
                        const nom = cleanNumber(e.nominal);
                        totExp += nom;
                        lines.push([escapeCsv(idx + 1), escapeCsv(e.tanggal || '-'), escapeCsv(e.kategori || '-'), escapeCsv(e.judul || '-'), escapeCsv(acara), escapeCsv(fmtRp(nom)), escapeCsv(e.catatan || '')]);
                    });
                    lines.push([escapeCsv("TOTAL"), escapeCsv(`${expenses.length} Transaksi`), escapeCsv(""), escapeCsv(""), escapeCsv(""), escapeCsv(fmtRp(totExp)), escapeCsv("")]);
                }

                const csvContent = "\uFEFF" + lines.map(row => row.join(",")).join("\r\n");
                const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
                const link = document.createElement("a");
                link.href = URL.createObjectURL(blob);
                link.download = `laporan-keuangan-${yyyymmdd}.csv`;
                link.style.visibility = 'hidden';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                showToast("Laporan CSV berhasil diunduh");
                return;
            }

            // EXCEL RICH STYLED SPREADSHEET (.xls HTML)
            let rowsHtml1 = `
                <tr style="background:#ffffff;">
                    <td style="font-weight:600; padding:8px 12px; border:1px solid #cbd5e1;">Total Omset</td>
                    <td style="font-weight:700; text-align:right; color:#0f172a; padding:8px 12px; border:1px solid #cbd5e1;">${fmtRp(totalOmset)}</td>
                    <td style="color:#475569; padding:8px 12px; border:1px solid #cbd5e1;">Total nilai seluruh kontrak / paket acara</td>
                </tr>
                <tr style="background:#f8fafc;">
                    <td style="font-weight:600; padding:8px 12px; border:1px solid #cbd5e1;">Total Uang Masuk (Kas Riil)</td>
                    <td style="font-weight:700; text-align:right; color:#16a34a; padding:8px 12px; border:1px solid #cbd5e1;">${fmtRp(uangMasuk)}</td>
                    <td style="color:#475569; padding:8px 12px; border:1px solid #cbd5e1;">Total DP dan pelunasan yang telah diterima</td>
                </tr>
                <tr style="background:#ffffff;">
                    <td style="font-weight:600; padding:8px 12px; border:1px solid #cbd5e1;">Sisa Piutang</td>
                    <td style="font-weight:700; text-align:right; color:#d97706; padding:8px 12px; border:1px solid #cbd5e1;">${fmtRp(sisaPiutang)}</td>
                    <td style="color:#475569; padding:8px 12px; border:1px solid #cbd5e1;">Tagihan yang belum dilunasi klien</td>
                </tr>
                <tr style="background:#f8fafc;">
                    <td style="font-weight:600; padding:8px 12px; border:1px solid #cbd5e1;">Biaya Operasional Acara</td>
                    <td style="font-weight:700; text-align:right; color:#dc2626; padding:8px 12px; border:1px solid #cbd5e1;">${fmtRp(pengeluaranOperasional)}</td>
                    <td style="color:#475569; padding:8px 12px; border:1px solid #cbd5e1;">Total biaya operasional tim/alat pada paket</td>
                </tr>
                <tr style="background:#ffffff;">
                    <td style="font-weight:600; padding:8px 12px; border:1px solid #cbd5e1;">Biaya Pengeluaran Lainnya</td>
                    <td style="font-weight:700; text-align:right; color:#dc2626; padding:8px 12px; border:1px solid #cbd5e1;">${fmtRp(pengeluaranManual)}</td>
                    <td style="color:#475569; padding:8px 12px; border:1px solid #cbd5e1;">Biaya operasional kantor, transport, konsumsi, kas keluar</td>
                </tr>
                <tr style="background:#fef2f2;">
                    <td style="font-weight:700; padding:9px 12px; border:1px solid #cbd5e1; color:#991b1b;">Total Seluruh Pengeluaran</td>
                    <td style="font-weight:700; text-align:right; color:#dc2626; padding:9px 12px; border:1px solid #cbd5e1;">${fmtRp(totalPengeluaran)}</td>
                    <td style="color:#7f1d1d; padding:9px 12px; border:1px solid #cbd5e1;">Akumulasi biaya operasional + pengeluaran lain</td>
                </tr>
                <tr style="background:${cashFlowProfit >= 0 ? '#ecfdf5' : '#fef2f2'};">
                    <td style="font-weight:700; padding:9px 12px; border:1px solid #cbd5e1; color:${cashFlowProfit >= 0 ? '#065f46' : '#991b1b'};">Net Cash Flow (Kas Riil - Pengeluaran)</td>
                    <td style="font-weight:800; text-align:right; color:${cashFlowProfit >= 0 ? '#047857' : '#b91c1c'}; padding:9px 12px; border:1px solid #cbd5e1;">${fmtRp(cashFlowProfit)}</td>
                    <td style="font-weight:600; color:${cashFlowProfit >= 0 ? '#047857' : '#b91c1c'}; padding:9px 12px; border:1px solid #cbd5e1;">${cashFlowProfit >= 0 ? 'Surplus Kas (Positif / Sehat)' : 'Defisit Kas'}</td>
                </tr>
                <tr style="background:#f0fdf4;">
                    <td style="font-weight:700; padding:9px 12px; border:1px solid #cbd5e1; color:#14532d;">Proyeksi Laba Bersih (Omset - Pengeluaran)</td>
                    <td style="font-weight:800; text-align:right; color:#15803d; padding:9px 12px; border:1px solid #cbd5e1;">${fmtRp(profitBersih)}</td>
                    <td style="color:#14532d; padding:9px 12px; border:1px solid #cbd5e1;">Estimasi laba bersih setelah seluruh tagihan lunas</td>
                </tr>
            `;

            let rowsAcara = '';
            let totInc = 0, totOp = 0, totExt = 0, totCost = 0, totProf = 0;
            if (bookings.length === 0) {
                rowsAcara = `<tr><td colspan="10" style="text-align:center; padding:16px; color:#94a3b8; border:1px solid #cbd5e1;">Tidak ada data booking pada periode ini</td></tr>`;
            } else {
                bookings.forEach((b, idx) => {
                    const linked = expenses.filter(e => e.acaraId == b.id).reduce((sum, e) => sum + cleanNumber(e.nominal), 0);
                    let bOp = cleanNumber(b.total_operational_cost);
                    if (bOp === 0 && Array.isArray(b.operational_costs) && b.operational_costs.length > 0) {
                        bOp = b.operational_costs.reduce((sum, item) => sum + (cleanNumber(item.price) * (cleanNumber(item.qty) || 1)), 0);
                    }
                    const bC = bOp + linked;
                    const bI = cleanNumber(b.total_income) || (cleanNumber(b.package_price) * (cleanNumber(b.package_qty) || 1));
                    const bP = bI - bC;
                    const m = bI > 0 ? ((bP / bI) * 100).toFixed(1) + '%' : '0%';
                    totInc += bI; totOp += bOp; totExt += linked; totCost += bC; totProf += bP;
                    const bg = idx % 2 === 0 ? '#ffffff' : '#f8fafc';
                    rowsAcara += `
                        <tr style="background:${bg};">
                            <td style="text-align:center; padding:7px 10px; border:1px solid #cbd5e1;">${idx + 1}</td>
                            <td style="font-weight:600; padding:7px 10px; border:1px solid #cbd5e1;">${b.client_name || '-'}</td>
                            <td style="text-align:center; padding:7px 10px; border:1px solid #cbd5e1; color:#475569;">${b.event_date || '-'}</td>
                            <td style="padding:7px 10px; border:1px solid #cbd5e1;">${b.package_name || '-'}</td>
                            <td style="text-align:right; font-weight:600; padding:7px 10px; border:1px solid #cbd5e1;">${fmtRp(bI)}</td>
                            <td style="text-align:right; color:#dc2626; padding:7px 10px; border:1px solid #cbd5e1;">${fmtRp(bOp)}</td>
                            <td style="text-align:right; color:#dc2626; padding:7px 10px; border:1px solid #cbd5e1;">${fmtRp(linked)}</td>
                            <td style="text-align:right; font-weight:600; color:#b91c1c; padding:7px 10px; border:1px solid #cbd5e1;">${fmtRp(bC)}</td>
                            <td style="text-align:right; font-weight:700; color:${bP >= 0 ? '#16a34a' : '#dc2626'}; padding:7px 10px; border:1px solid #cbd5e1;">${fmtRp(bP)}</td>
                            <td style="text-align:center; font-weight:600; color:${bP >= 0 ? '#15803d' : '#b91c1c'}; padding:7px 10px; border:1px solid #cbd5e1;">${m}</td>
                        </tr>
                    `;
                });
                const totM = totInc > 0 ? ((totProf / totInc) * 100).toFixed(1) + '%' : '0%';
                rowsAcara += `
                    <tr style="background:#e0f2fe; font-weight:bold; color:#0369a1; border-top:2px solid #0284c7; border-bottom:2px solid #0284c7;">
                        <td colspan="4" style="text-align:center; padding:9px 10px; border:1px solid #94a3b8; font-weight:700;">TOTAL (${bookings.length} ACARA)</td>
                        <td style="text-align:right; padding:9px 10px; border:1px solid #94a3b8; font-weight:700;">${fmtRp(totInc)}</td>
                        <td style="text-align:right; padding:9px 10px; border:1px solid #94a3b8; font-weight:700; color:#dc2626;">${fmtRp(totOp)}</td>
                        <td style="text-align:right; padding:9px 10px; border:1px solid #94a3b8; font-weight:700; color:#dc2626;">${fmtRp(totExt)}</td>
                        <td style="text-align:right; padding:9px 10px; border:1px solid #94a3b8; font-weight:700; color:#b91c1c;">${fmtRp(totCost)}</td>
                        <td style="text-align:right; padding:9px 10px; border:1px solid #94a3b8; font-weight:800; color:${totProf >= 0 ? '#15803d' : '#b91c1c'};">${fmtRp(totProf)}</td>
                        <td style="text-align:center; padding:9px 10px; border:1px solid #94a3b8; font-weight:700;">${totM}</td>
                    </tr>
                `;
            }

            let rowsExp = '';
            let totExp = 0;
            if (expenses.length === 0) {
                rowsExp = `<tr><td colspan="7" style="text-align:center; padding:16px; color:#94a3b8; border:1px solid #cbd5e1;">Tidak ada data pengeluaran pada periode ini</td></tr>`;
            } else {
                expenses.forEach((e, idx) => {
                    let acara = '-';
                    if (e.acaraId) {
                        const b = allBookings.find(x => x.id == e.acaraId);
                        if (b) acara = b.client_name || '-';
                    }
                    const nom = cleanNumber(e.nominal);
                    totExp += nom;
                    const bg = idx % 2 === 0 ? '#ffffff' : '#f8fafc';
                    rowsExp += `
                        <tr style="background:${bg};">
                            <td style="text-align:center; padding:7px 10px; border:1px solid #cbd5e1;">${idx + 1}</td>
                            <td style="text-align:center; padding:7px 10px; border:1px solid #cbd5e1; color:#475569;">${e.tanggal || '-'}</td>
                            <td style="text-align:center; padding:7px 10px; border:1px solid #cbd5e1;"><span style="background:#f1f5f9; color:#334155; font-size:9pt; padding:3px 8px; border-radius:4px; font-weight:600;">${e.kategori || '-'}</span></td>
                            <td style="font-weight:600; padding:7px 10px; border:1px solid #cbd5e1;">${e.judul || '-'}</td>
                            <td style="padding:7px 10px; border:1px solid #cbd5e1; color:#475569;">${acara}</td>
                            <td style="text-align:right; font-weight:700; color:#dc2626; padding:7px 10px; border:1px solid #cbd5e1;">${fmtRp(nom)}</td>
                            <td style="color:#64748b; font-size:9pt; padding:7px 10px; border:1px solid #cbd5e1;">${e.catatan || ''}</td>
                        </tr>
                    `;
                });
                rowsExp += `
                    <tr style="background:#fef2f2; font-weight:bold; color:#991b1b; border-top:2px solid #ef4444; border-bottom:2px solid #ef4444;">
                        <td colspan="5" style="text-align:center; padding:9px 10px; border:1px solid #94a3b8; font-weight:700;">TOTAL PENGELUARAN (${expenses.length} TRANSAKSI)</td>
                        <td style="text-align:right; padding:9px 10px; border:1px solid #94a3b8; font-weight:800; color:#dc2626;">${fmtRp(totExp)}</td>
                        <td style="border:1px solid #94a3b8;"></td>
                    </tr>
                `;
            }

            const excelHtml = `
<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<style>
  body { font-family: 'Calibri', 'Segoe UI', Arial, sans-serif; font-size: 11pt; color: #1e293b; }
  table { border-collapse: collapse; margin-bottom: 24px; font-family: 'Calibri', 'Segoe UI', Arial, sans-serif; }
  th, td { border: 1px solid #cbd5e1; padding: 7px 10px; font-size: 10pt; vertical-align: middle; }
</style>
</head>
<body>
    <!-- Title Banner -->
    <table style="width:100%; border:none; margin-bottom:16px;">
        <tr>
            <td colspan="10" style="background:#0f172a; color:#ffffff; font-size:16pt; font-weight:bold; padding:14px 16px; border:none;">
                LAPORAN KEUANGAN &amp; ARUS KAS - CLOSEMATE AI / PENAPICT
            </td>
        </tr>
        <tr>
            <td colspan="10" style="background:#f1f5f9; color:#475569; font-size:10pt; padding:6px 16px; border:none;">
                <strong>Periode:</strong> ${periodeStr} &nbsp;|&nbsp; <strong>Waktu Cetak:</strong> ${nowStr}
            </td>
        </tr>
    </table>

    <!-- Section 1: Ringkasan Eksekutif -->
    <table style="width:100%; border-collapse:collapse; margin-bottom:24px;">
        <colgroup>
            <col style="width:300px;">
            <col style="width:200px;">
            <col style="width:400px;">
        </colgroup>
        <thead>
            <tr>
                <th colspan="3" style="background:#1e40af; color:#ffffff; font-size:11pt; font-weight:bold; text-align:left; padding:10px 14px; border:1px solid #1e40af;">
                    1. RINGKASAN EKSEKUTIF KEUANGAN
                </th>
            </tr>
            <tr style="background:#e2e8f0; color:#0f172a; font-weight:bold;">
                <th style="text-align:left; padding:8px 12px; border:1px solid #94a3b8;">Indikator</th>
                <th style="text-align:right; padding:8px 12px; border:1px solid #94a3b8;">Nominal (Rp)</th>
                <th style="text-align:left; padding:8px 12px; border:1px solid #94a3b8;">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            ${rowsHtml1}
        </tbody>
    </table>

    <!-- Section 2: Cash Flow Bulanan -->
    <table style="width:100%; border-collapse:collapse; margin-bottom:24px;">
        <colgroup>
            <col style="width:200px;">
            <col style="width:180px;">
            <col style="width:180px;">
            <col style="width:180px;">
            <col style="width:160px;">
        </colgroup>
        <thead>
            <tr>
                <th colspan="5" style="background:#0d9488; color:#ffffff; font-size:11pt; font-weight:bold; text-align:left; padding:10px 14px; border:1px solid #0d9488;">
                    2. ARUS KAS BULANAN (CASH FLOW)
                </th>
            </tr>
            <tr style="background:#e2e8f0; color:#0f172a; font-weight:bold;">
                <th style="text-align:left; padding:8px 12px; border:1px solid #94a3b8;">Periode / Bulan</th>
                <th style="text-align:right; padding:8px 12px; border:1px solid #94a3b8;">Uang Masuk (Rp)</th>
                <th style="text-align:right; padding:8px 12px; border:1px solid #94a3b8;">Pengeluaran (Rp)</th>
                <th style="text-align:right; padding:8px 12px; border:1px solid #94a3b8;">Net Cash Flow (Rp)</th>
                <th style="text-align:center; padding:8px 12px; border:1px solid #94a3b8;">Status Arus Kas</th>
            </tr>
        </thead>
        <tbody>
            <tr style="background:#ffffff;">
                <td style="font-weight:600; padding:9px 12px; border:1px solid #cbd5e1;">${monthStr}</td>
                <td style="text-align:right; font-weight:700; color:#16a34a; padding:9px 12px; border:1px solid #cbd5e1;">${fmtRp(uangMasuk)}</td>
                <td style="text-align:right; font-weight:700; color:#dc2626; padding:9px 12px; border:1px solid #cbd5e1;">${fmtRp(totalPengeluaran)}</td>
                <td style="text-align:right; font-weight:800; color:${cashFlowProfit >= 0 ? '#15803d' : '#b91c1c'}; padding:9px 12px; border:1px solid #cbd5e1;">${fmtRp(cashFlowProfit)}</td>
                <td style="text-align:center; font-weight:700; background:${cashFlowProfit >= 0 ? '#dcfce7' : '#fee2e2'}; color:${cashFlowProfit >= 0 ? '#15803d' : '#b91c1c'}; padding:9px 12px; border:1px solid #cbd5e1;">
                    ${cashFlowProfit >= 0 ? 'Surplus (Sehat)' : 'Defisit'}
                </td>
            </tr>
        </tbody>
    </table>

    <!-- Section 3: Profitabilitas Per Acara -->
    <table style="width:100%; border-collapse:collapse; margin-bottom:24px;">
        <colgroup>
            <col style="width:45px;">
            <col style="width:170px;">
            <col style="width:120px;">
            <col style="width:160px;">
            <col style="width:140px;">
            <col style="width:140px;">
            <col style="width:140px;">
            <col style="width:140px;">
            <col style="width:140px;">
            <col style="width:90px;">
        </colgroup>
        <thead>
            <tr>
                <th colspan="10" style="background:#4338ca; color:#ffffff; font-size:11pt; font-weight:bold; text-align:left; padding:10px 14px; border:1px solid #4338ca;">
                    3. PROFITABILITAS PER EVENT / ACARA
                </th>
            </tr>
            <tr style="background:#e2e8f0; color:#0f172a; font-weight:bold;">
                <th style="text-align:center; padding:8px 8px; border:1px solid #94a3b8;">No</th>
                <th style="text-align:left; padding:8px 10px; border:1px solid #94a3b8;">Nama Klien</th>
                <th style="text-align:center; padding:8px 10px; border:1px solid #94a3b8;">Tanggal Acara</th>
                <th style="text-align:left; padding:8px 10px; border:1px solid #94a3b8;">Paket Layanan</th>
                <th style="text-align:right; padding:8px 10px; border:1px solid #94a3b8;">Harga Paket</th>
                <th style="text-align:right; padding:8px 10px; border:1px solid #94a3b8;">Biaya Operasional</th>
                <th style="text-align:right; padding:8px 10px; border:1px solid #94a3b8;">Biaya Tambahan</th>
                <th style="text-align:right; padding:8px 10px; border:1px solid #94a3b8;">Total Biaya</th>
                <th style="text-align:right; padding:8px 10px; border:1px solid #94a3b8;">Profit Bersih</th>
                <th style="text-align:center; padding:8px 8px; border:1px solid #94a3b8;">Margin</th>
            </tr>
        </thead>
        <tbody>
            ${rowsAcara}
        </tbody>
    </table>

    <!-- Section 4: Rincian Pengeluaran -->
    <table style="width:100%; border-collapse:collapse; margin-bottom:24px;">
        <colgroup>
            <col style="width:45px;">
            <col style="width:120px;">
            <col style="width:140px;">
            <col style="width:220px;">
            <col style="width:160px;">
            <col style="width:150px;">
            <col style="width:250px;">
        </colgroup>
        <thead>
            <tr>
                <th colspan="7" style="background:#c2410c; color:#ffffff; font-size:11pt; font-weight:bold; text-align:left; padding:10px 14px; border:1px solid #c2410c;">
                    4. RINCIAN PENGELUARAN
                </th>
            </tr>
            <tr style="background:#e2e8f0; color:#0f172a; font-weight:bold;">
                <th style="text-align:center; padding:8px 8px; border:1px solid #94a3b8;">No</th>
                <th style="text-align:center; padding:8px 10px; border:1px solid #94a3b8;">Tanggal</th>
                <th style="text-align:center; padding:8px 10px; border:1px solid #94a3b8;">Kategori</th>
                <th style="text-align:left; padding:8px 10px; border:1px solid #94a3b8;">Judul / Keterangan</th>
                <th style="text-align:left; padding:8px 10px; border:1px solid #94a3b8;">Terkait Acara</th>
                <th style="text-align:right; padding:8px 10px; border:1px solid #94a3b8;">Nominal (Rp)</th>
                <th style="text-align:left; padding:8px 10px; border:1px solid #94a3b8;">Catatan</th>
            </tr>
        </thead>
        <tbody>
            ${rowsExp}
        </tbody>
    </table>
</body>
</html>
            `;

            const blob = new Blob([excelHtml], { type: 'application/vnd.ms-excel;charset=utf-8;' });
            const link = document.createElement("a");
            link.href = URL.createObjectURL(blob);
            link.download = `laporan-keuangan-${yyyymmdd}.xls`;
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            showToast("Laporan keuangan berhasil diunduh");
        }

        let fpInstance = null;

        document.addEventListener('DOMContentLoaded', () => {
            fetchBookingsFinance();
            fetchExpensesFinance();
            loadAnalyticsData();
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
    <script src="/js/responsive.js"></script>
</body>
</html>
