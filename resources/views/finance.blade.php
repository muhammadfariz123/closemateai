<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Keuangan - CloseMateAI</title>
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
                            <h4>Penapict <i class="fa-solid fa-chevron-down" style="font-size: 10px; color: #a1a5b7; margin-left: 4px;"></i></h4>
                            <p>Starter</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="filter-row">
            <div class="filter-tabs">
                <div class="filter-tab active">Bulan Ini</div>
                <div class="filter-tab">Bulan Lalu</div>
                <div class="filter-tab">30 Hari Terakhir</div>
                <div class="filter-tab">Tahun Ini</div>
            </div>
            <button class="btn btn-date"><i class="fa-regular fa-calendar"></i> 27 September 2026 - 28 September 2026</button>
        </div>
        
        <div class="filter-row" style="margin-bottom: 24px;">
            <button class="btn btn-outline"><i class="fa-solid fa-download"></i> Export Laporan (CSV)</button>
            <button class="btn btn-primary"><i class="fa-solid fa-plus"></i> Catat Pengeluaran Baru</button>
        </div>
        
        <div class="date-info">
            Menampilkan data: <strong>27 September 2026 - 28 September 2026</strong>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-title">TOTAL OMSET</div>
                <div class="stat-value">Rp 0</div>
                <div class="stat-icon"><i class="fa-solid fa-wallet"></i></div>
            </div>
            <div class="stat-card green">
                <div class="stat-title">UANG MASUK</div>
                <div class="stat-value">Rp 0</div>
                <div class="stat-icon"><i class="fa-solid fa-arrow-trend-up"></i></div>
            </div>
            <div class="stat-card">
                <div class="stat-title">SISA PIUTANG KLIEN</div>
                <div class="stat-value">Rp 0</div>
                <div class="stat-icon"><i class="fa-solid fa-hand-holding-dollar"></i></div>
            </div>
            <div class="stat-card red">
                <div class="stat-title">TOTAL PENGELUARAN</div>
                <div class="stat-value">Rp 0</div>
                <div class="stat-desc">Pengeluaran manual + biaya operasional + vendor (HPP)</div>
                <div class="stat-icon"><i class="fa-solid fa-arrow-trend-down"></i></div>
            </div>
            <div class="stat-card green">
                <div class="stat-title">PROFIT BERSIH</div>
                <div class="stat-value">Rp 0</div>
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
                
                <div style="position: absolute; left: 50%; bottom: 20px; display: flex; flex-direction: column; align-items: center; transform: translateX(-50%);">
                    <div style="width: 8px; height: 8px; border-radius: 50%; border: 2px solid #3b82f6; background: white; z-index: 2; margin-bottom: -4px;"></div>
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
                <select class="select-filter">
                    <option>Semua Kategori</option>
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
                <tbody>
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
                <tbody>
                    <tr>
                        <td colspan="6" class="empty-row">Belum ada booking untuk dihitung.</td>
                    </tr>
                </tbody>
            </table>
        </div>

    </div>

    <script>
        // Sidebar Collapse
        document.getElementById('btn-collapse').addEventListener('click', function() {
            document.body.classList.toggle('sidebar-collapsed');
        });
    </script>
</body>
</html>
