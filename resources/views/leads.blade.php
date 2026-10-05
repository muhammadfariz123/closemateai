<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leads CRM - CloseMateAI</title>
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
        .dropdown-item i { width: 16px; text-align: center; color: var(--text-muted); font-size: 14px; }
        .dropdown-item.text-danger { color: var(--danger); }
        .dropdown-item.text-danger i { color: var(--danger); }

        /* Form Controls & Buttons */
        .form-control { padding: 10px 14px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 14px; outline: none; transition: 0.2s; }
        .form-control:focus { border-color: var(--primary); }
        
        .btn { padding: 10px 20px; border-radius: 8px; font-size: 14px; font-weight: 500; cursor: pointer; border: 1px solid transparent; display: inline-flex; align-items: center; gap: 8px; transition: 0.2s; }
        .btn-primary { background: var(--primary); color: white; }
        .btn-primary:hover { opacity: 0.9; }
        .btn-secondary { background: white; border-color: var(--border-color); color: var(--text-dark); }
        .btn-secondary:hover { background: var(--bg-light); }
        
        /* Layout Utilities */
        .d-flex { display: flex; }
        .gap-2 { gap: 8px; }
        .gap-3 { gap: 12px; }
        .align-items-center { align-items: center; }
        .justify-content-between { justify-content: space-between; }
        .w-100 { width: 100%; }

        /* CRM Table Panel */
        .panel { background: white; border-radius: 12px; border: 1px solid var(--border-color); box-shadow: 0 2px 10px rgba(0,0,0,0.02); overflow: hidden; }
        
        .panel-header { padding: 20px 24px; display: flex; gap: 12px; align-items: center; border-bottom: 1px solid var(--border-color); flex-wrap: wrap; }
        .search-wrapper { position: relative; flex: 1; min-width: 200px; }
        .search-wrapper i { position: absolute; left: 14px; top: 12px; color: var(--text-muted); }
        .search-wrapper input { width: 100%; padding-left: 36px; border-radius: 20px; }
        
        .date-picker { position: relative; }
        .date-picker i { position: absolute; left: 14px; top: 12px; color: var(--text-muted); }
        .date-picker input { padding-left: 36px; border-radius: 20px; width: 220px; cursor: pointer; }
        
        .status-select { border-radius: 20px; padding-right: 32px; min-width: 140px; cursor: pointer; }
        
        .table { width: 100%; border-collapse: collapse; }
        .table th, .table td { padding: 16px 24px; text-align: left; border-bottom: 1px solid var(--border-color); font-size: 13px; }
        .table th { color: var(--text-muted); font-weight: 500; }
        .table td { color: var(--text-dark); }
        
        .empty-state { padding: 60px 24px; text-align: center; color: var(--text-muted); font-size: 13px; }
        
        .panel-footer { padding: 16px 24px; display: flex; justify-content: space-between; align-items: center; color: var(--text-muted); font-size: 13px; }
        
        .pagination { display: flex; gap: 6px; }
        .page-item { width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border-radius: 6px; border: 1px solid var(--border-color); cursor: pointer; transition: 0.2s; }
        .page-item:hover { background: var(--bg-light); }
        .page-item.active { background: var(--primary); color: white; border-color: var(--primary); }
    
        /* Responsive Mobile Layout */
        @media (max-width: 768px) {
            .sidebar {
                width: 100% !important;
                height: auto;
                position: fixed;
                top: 73px; /* Just below topbar */
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
                padding: 12px 16px;
                gap: 8px;
                -webkit-overflow-scrolling: touch;
            }
            .nav-menu::-webkit-scrollbar { display: none; }
            
            .nav-item {
                padding: 8px 16px;
                background: #f1f1f4;
                color: var(--text-dark);
                border-radius: 30px;
                margin: 0;
            }
            .nav-item.active {
                background: var(--sidebar-active-bg);
                color: var(--primary);
            }
            .nav-item:hover { color: var(--primary); }
            #btn-collapse, .nav-menu div[style*="margin-top: 30px"] { display: none !important; }
            
            .main-content {
                margin-left: 0 !important;
                padding: 16px;
                padding-top: 145px; /* Topbar (73px) + Nav (60px) + Gap */
            }
            
            .topbar {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                margin: 0;
                z-index: 100;
            }
            
            .settings-grid { grid-template-columns: 1fr; gap: 16px; }
            .form-grid { grid-template-columns: 1fr; gap: 16px; }
            .grid-stats { grid-template-columns: 1fr; gap: 16px; }
            .grid-main { grid-template-columns: 1fr; gap: 16px; }
            .mini-stats-grid { grid-template-columns: 1fr; gap: 16px; }
            
            .page-title h1 { font-size: 18px; }
            .page-title p { font-size: 12px; }
            .status-pill { padding: 6px 12px; font-size: 11px; }
            .user-info { display: none; } /* Hide user text, show only avatar */
            
            .card, .panel { padding: 16px; }
            .card-header, .panel-header { flex-direction: column; gap: 12px; align-items: flex-start; }
            .progress-steps { flex-direction: column; align-items: flex-start; gap: 16px; }
            .progress-line, .progress-line-active { display: none; } /* Hide line on mobile */
            .step { flex-direction: row; text-align: left; padding: 0; }
            .step-text { text-align: left; }
            .qr-placeholder { width: 100%; max-width: 240px; }
            
            /* Responsive adjust for top actions to fit */
            .top-actions { gap: 10px; }
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
                <h1>Leads CRM</h1>
                <p>Semua prospek dari WhatsApp dalam satu tabel</p>
            </div>
            <div class="top-actions">
                <div class="status-pill">
                    <div class="status-dot"></div>
                    WhatsApp Connected
                </div>
                <div class="profile-wrapper" id="profile-btn">
                    <div class="user-profile">
                        <div class="avatar">{{ auth()->check() ? substr(auth()->user()->name, 0, 1) : 'P' }}</div>
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

        <div class="panel">
            <div class="panel-header">
                <div class="search-wrapper">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" class="form-control" placeholder="Cari nama, nomor, atau venue...">
                </div>
                
                <div class="date-picker">
                    <i class="fa-regular fa-calendar"></i>
                    <input type="text" class="form-control" value="01 Aug - 31 Aug 2026" readonly>
                </div>
                
                <select class="form-control status-select">
                    <option>All Status</option>
                    <option>Hot Lead</option>
                    <option>Warm Lead</option>
                    <option>Cold Lead</option>
                    <option>Closing</option>
                </select>
                
                <button class="btn btn-secondary" style="border-radius: 20px; padding-left: 16px; padding-right: 16px;"><i class="fa-solid fa-plus"></i> Tambah Lead</button>
                <button class="btn btn-primary" style="border-radius: 8px;"><i class="fa-solid fa-download"></i> Export to CSV</button>
            </div>
            
            <div style="overflow-x: auto;">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Client</th>
                            <th>Event & Venue</th>
                            <th>Package</th>
                            <th>Temperature</th>
                            <th>Status</th>
                            <th>Last Contacted</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Empty State Row -->
                    </tbody>
                </table>
                <div class="empty-state">
                    Tidak ada lead yang cocok dengan filter.
                </div>
            </div>
            
            <div class="panel-footer">
                <div>Menampilkan 0 dari 0 lead</div>
                <div class="pagination">
                    <div class="page-item"><i class="fa-solid fa-chevron-left"></i></div>
                    <div class="page-item active">1</div>
                    <div class="page-item"><i class="fa-solid fa-chevron-right"></i></div>
                </div>
            </div>
        </div>
    </div>

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
