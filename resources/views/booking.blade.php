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
                <h1>Booking & Operasional Management</h1>
                <p>Pantau jadwal acara, pembayaran, biaya operasional, dan estimasi profit setiap klien.</p>
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
                <button class="btn btn-secondary"><i class="fa-regular fa-calendar"></i> Google Calendar</button>
            </div>
            
            <div class="right-controls">
                <div class="btn-group">
                    <button class="btn active"><i class="fa-regular fa-calendar-days"></i> Calendar View</button>
                    <button class="btn"><i class="fa-solid fa-table-list"></i> Table View</button>
                </div>
                <select class="form-control" style="border-radius: 20px; width: 140px;">
                    <option>Semua Bulan</option>
                </select>
                <button onclick="openBookingModal()" class="btn btn-primary" style="border-radius: 8px;"><i class="fa-solid fa-plus"></i> Tambah Booking</button>
            </div>
        </div>
        
        <div class="panel">
            <div class="calendar-header">
                <button class="btn-nav"><i class="fa-solid fa-chevron-left"></i></button>
                <div class="calendar-title">Oktober 2026</div>
                <button class="btn-nav"><i class="fa-solid fa-chevron-right"></i></button>
            </div>
            
            <div class="calendar-grid">
                <!-- Headers -->
                <div class="calendar-day-header">Sen</div>
                <div class="calendar-day-header">Sel</div>
                <div class="calendar-day-header">Rab</div>
                <div class="calendar-day-header">Kam</div>
                <div class="calendar-day-header">Jum</div>
                <div class="calendar-day-header">Sab</div>
                <div class="calendar-day-header">Min</div>
                
                <!-- Week 1 (Offset 3 days) -->
                <div class="calendar-day" style="opacity: 0; pointer-events: none;"></div>
                <div class="calendar-day" style="opacity: 0; pointer-events: none;"></div>
                <div class="calendar-day" style="opacity: 0; pointer-events: none;"></div>
                <div class="calendar-day"><div class="calendar-day-num">1</div></div>
                <div class="calendar-day"><div class="calendar-day-num">2</div></div>
                <div class="calendar-day today"><div class="calendar-day-num">3</div></div>
                <div class="calendar-day"><div class="calendar-day-num">4</div></div>
                
                <!-- Week 2 -->
                <div class="calendar-day"><div class="calendar-day-num">5</div></div>
                <div class="calendar-day"><div class="calendar-day-num">6</div></div>
                <div class="calendar-day"><div class="calendar-day-num">7</div></div>
                <div class="calendar-day"><div class="calendar-day-num">8</div></div>
                <div class="calendar-day"><div class="calendar-day-num">9</div></div>
                <div class="calendar-day"><div class="calendar-day-num">10</div></div>
                <div class="calendar-day"><div class="calendar-day-num">11</div></div>
                
                <!-- Week 3 -->
                <div class="calendar-day">
                    <div class="calendar-day-num">12</div>
                    <div class="event-pill">Test</div>
                </div>
                <div class="calendar-day"><div class="calendar-day-num">13</div></div>
                <div class="calendar-day"><div class="calendar-day-num">14</div></div>
                <div class="calendar-day"><div class="calendar-day-num">15</div></div>
                <div class="calendar-day"><div class="calendar-day-num">16</div></div>
                <div class="calendar-day"><div class="calendar-day-num">17</div></div>
                <div class="calendar-day"><div class="calendar-day-num">18</div></div>
                
                <!-- Week 4 -->
                <div class="calendar-day"><div class="calendar-day-num">19</div></div>
                <div class="calendar-day"><div class="calendar-day-num">20</div></div>
                <div class="calendar-day"><div class="calendar-day-num">21</div></div>
                <div class="calendar-day"><div class="calendar-day-num">22</div></div>
                <div class="calendar-day"><div class="calendar-day-num">23</div></div>
                <div class="calendar-day"><div class="calendar-day-num">24</div></div>
                <div class="calendar-day"><div class="calendar-day-num">25</div></div>
                
                <!-- Week 5 -->
                <div class="calendar-day"><div class="calendar-day-num">26</div></div>
                <div class="calendar-day"><div class="calendar-day-num">27</div></div>
                <div class="calendar-day"><div class="calendar-day-num">28</div></div>
                <div class="calendar-day"><div class="calendar-day-num">29</div></div>
                <div class="calendar-day"><div class="calendar-day-num">30</div></div>
                <div class="calendar-day"><div class="calendar-day-num">31</div></div>
                <div class="calendar-day" style="opacity: 0; pointer-events: none;"></div>
            </div>
            
            <div class="calendar-legend">
                <div class="legend-item"><div class="legend-dot" style="background: #ffc700; border: 1px solid #e0b000;"></div> DP 1</div>
                <div class="legend-item"><div class="legend-dot" style="background: transparent; border: 2px solid #50cd89;"></div> DP 2</div>
                <div class="legend-item"><div class="legend-dot" style="background: transparent; border: 2px solid #6b5cd8;"></div> LUNAS</div>
            </div>
        </div>
    </div>

    @include('components.booking-modal')
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

        function openBookingModal() {
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
            document.getElementById('b_result_link').value = '';
            document.getElementById('b_notes').value = '';
            
            addonsData = [];
            costsData = [];
            teamData = [];
            renderAddons();
            renderCosts();
            renderTeam();
            calculateBooking();
            
            bookingModal.style.display = 'flex';
        }

        function closeBookingModal() {
            bookingModal.style.display = 'none';
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
                    <input type="text" class="form-control" placeholder="Role (e.g. Fotografer)" value="${item.role}" onchange="teamData[${index}].role = this.value">
                    <input type="text" class="form-control" placeholder="Nama" value="${item.name}" onchange="teamData[${index}].name = this.value">
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

            const payload = {
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
                production_status: document.getElementById('b_production_status').value,
                result_link: document.getElementById('b_result_link').value,
                team_members: teamData,
                notes: document.getElementById('b_notes').value,
            };
            
            try {
                const res = await fetch('/api/bookings', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify(payload)
                });
                
                if (res.ok) {
                    showToast('Booking berhasil disimpan!');
                    closeBookingModal();
                    // Here you would typically refresh the table or calendar
                } else {
                    showToast('Gagal menyimpan booking.', 'error');
                }
            } catch (err) {
                showToast('Terjadi kesalahan jaringan.', 'error');
            }
            
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
        });

    </script>
</body>
</html>
