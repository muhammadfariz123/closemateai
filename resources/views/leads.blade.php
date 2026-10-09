<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leads CRM - CloseMateAI</title>
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
                <h1>Leads CRM</h1>
                <p>Semua prospek dari WhatsApp dalam satu tabel</p>
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
                    <input type="text" id="searchInput" class="form-control" placeholder="Cari nama, nomor, atau venue...">
                </div>
                
                <div class="date-picker">
                    <i class="fa-regular fa-calendar"></i>
                    <input type="text" id="dateFilter" class="form-control" placeholder="Pilih Rentang Tanggal..." readonly style="width: 200px;">
                </div>
                
                <select id="statusSelect" class="form-control status-select">
                    <option value="All Status">All Status</option>
                    <option value="New Inquiry">New Inquiry</option>
                    <option value="Hot Lead">Hot Lead</option>
                    <option value="Warm Lead">Warm Lead</option>
                    <option value="Proposal Sent">Proposal Sent</option>
                    <option value="Follow Up">Follow Up</option>
                    <option value="Done Follow-up 1">Done Follow-up 1</option>
                    <option value="Done Follow-up 2">Done Follow-up 2</option>
                    <option value="Booked">Booked</option>
                    <option value="Lost">Lost</option>
                </select>
                
                <button id="btnDeleteSelected" onclick="confirmDeleteSelected()" class="btn" style="background: var(--danger); color: white; border-radius: 8px; display: none;"><i class="fa-solid fa-trash-can"></i> Hapus</button>
                <button onclick="openAddModal()" class="btn btn-secondary" style="border-radius: 20px; padding-left: 16px; padding-right: 16px;"><i class="fa-solid fa-plus"></i> Tambah Lead</button>
                <button onclick="exportCSV()" class="btn btn-primary" style="border-radius: 8px;"><i class="fa-solid fa-download"></i> Export to CSV</button>
            </div>
            
            <div style="overflow-x: auto;">
                <table class="table">
                    <thead>
                        <tr>
                            <th style="width: 40px;"><input type="checkbox" id="selectAllLeads" onchange="toggleAllLeads(this)"></th>
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

    <!-- Edit/Add Lead Modal -->
    <div id="editLeadModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
        <div style="background: white; width: 500px; border-radius: 12px; padding: 24px; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
            <div style="display: flex; justify-content: space-between; margin-bottom: 4px; align-items: flex-start;">
                <div>
                    <h3 id="modalLeadTitle" style="font-size: 18px; font-weight: 600;">Edit Lead</h3>
                    <p style="font-size: 12px; color: var(--text-muted); margin-top: 4px;">Data lead ini juga dipakai AI untuk konteks follow-up klien.</p>
                </div>
                <i class="fa-solid fa-xmark" style="cursor: pointer; color: var(--text-muted);" onclick="closeEditModal()"></i>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-top: 24px;">
                <div class="form-group">
                    <label style="font-size: 12px; font-weight: 600; margin-bottom: 6px; display: block;">Nama Klien</label>
                    <input type="text" id="editLeadName" class="form-control" style="width: 100%;" placeholder="Contoh: Nisa & Rian">
                </div>
                <div class="form-group">
                    <label style="font-size: 12px; font-weight: 600; margin-bottom: 6px; display: block;">Nomor WhatsApp</label>
                    <input type="text" id="editLeadWa" class="form-control" style="width: 100%;" placeholder="Contoh: +62 812-0000-0000">
                </div>
                
                <div class="form-group">
                    <label style="font-size: 12px; font-weight: 600; margin-bottom: 6px; display: block;">Tanggal Acara</label>
                    <input type="text" id="editLeadEventDate" class="form-control" style="width: 100%;" placeholder="Contoh: 12 Dec 2026">
                </div>
                <div class="form-group">
                    <label style="font-size: 12px; font-weight: 600; margin-bottom: 6px; display: block;">Venue</label>
                    <input type="text" id="editLeadVenue" class="form-control" style="width: 100%;" placeholder="Contoh: Hotel Padma, Bandung">
                </div>
                
                <div class="form-group">
                    <label style="font-size: 12px; font-weight: 600; margin-bottom: 6px; display: block;">Paket</label>
                    <input type="text" id="editLeadPackage" class="form-control" style="width: 100%;" placeholder="Contoh: Gold Package">
                </div>
                <div class="form-group">
                    <label style="font-size: 12px; font-weight: 600; margin-bottom: 6px; display: block;">Lead Score</label>
                    <select id="editLeadScore" class="form-control" style="width: 100%;">
                        <option value="Hot">Hot</option>
                        <option value="Warm">Warm</option>
                        <option value="Cold">Cold</option>
                    </select>
                </div>
                
                <div class="form-group" style="grid-column: 1 / -1;">
                    <label style="font-size: 12px; font-weight: 600; margin-bottom: 6px; display: block;">Status</label>
                    <select id="editLeadStatus" class="form-control" style="width: 100%;">
                        <option value="New Inquiry">New Inquiry</option>
                        <option value="Hot Lead">Hot Lead</option>
                        <option value="Warm Lead">Warm Lead</option>
                        <option value="Proposal Sent">Proposal Sent</option>
                        <option value="Follow Up">Follow Up</option>
                        <option value="Done Follow-up 1">Done Follow-up 1</option>
                        <option value="Done Follow-up 2">Done Follow-up 2</option>
                        <option value="Booked">Booked</option>
                        <option value="Lost">Lost</option>
                    </select>
                </div>
            </div>
            
            <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px;">
                <button onclick="closeEditModal()" class="btn btn-secondary">Batal</button>
                <button onclick="saveEditLead()" class="btn btn-primary">Simpan</button>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteConfirmModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
        <div style="background: white; width: 400px; border-radius: 12px; padding: 24px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); text-align: center;">
            <i class="fa-solid fa-triangle-exclamation" style="font-size: 40px; color: var(--danger); margin-bottom: 16px;"></i>
            <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 8px;">Hapus Lead</h3>
            <p style="font-size: 14px; color: var(--text-muted); margin-bottom: 24px;">Apakah Anda yakin ingin menghapus lead ini? Data percakapan juga akan ikut terhapus.</p>
            <div style="display: flex; justify-content: center; gap: 12px;">
                <button onclick="closeDeleteModal()" class="btn btn-secondary">Batal</button>
                <button onclick="confirmDeleteLead()" class="btn" style="background: var(--danger); color: white;">Ya, Hapus</button>
            </div>
        </div>
    </div>

    <!-- Delete Multiple Confirmation Modal -->
    <div id="deleteMultipleConfirmModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
        <div style="background: white; width: 400px; border-radius: 12px; padding: 24px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); text-align: center;">
            <i class="fa-solid fa-triangle-exclamation" style="font-size: 40px; color: var(--danger); margin-bottom: 16px;"></i>
            <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 8px;">Hapus Lead Terpilih</h3>
            <p style="font-size: 14px; color: var(--text-muted); margin-bottom: 24px;">Apakah Anda yakin ingin menghapus <span id="deleteMultipleCount"></span> lead ini? Data percakapan juga akan ikut terhapus.</p>
            <div style="display: flex; justify-content: center; gap: 12px;">
                <button onclick="closeDeleteMultipleModal()" class="btn btn-secondary">Batal</button>
                <button onclick="executeDeleteMultiple()" class="btn" style="background: var(--danger); color: white;">Ya, Hapus</button>
            </div>
        </div>
    </div>

    <div class="toast-container" id="toast-container" style="position: fixed; bottom: 20px; right: 20px; z-index: 9999;"></div>

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        let allLeads = [];
        let currentFilteredLeads = [];
        let editingLeadId = null;
        let deletingLeadId = null;

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

        function showToast(message, iconClass = 'fa-check-circle') {
            const container = document.getElementById('toast-container');
            if(!container) return;
            const toast = document.createElement('div');
            toast.style.background = '#333';
            toast.style.color = 'white';
            toast.style.padding = '12px 20px';
            toast.style.borderRadius = '8px';
            toast.style.marginBottom = '10px';
            toast.style.fontSize = '14px';
            toast.style.display = 'flex';
            toast.style.alignItems = 'center';
            toast.style.gap = '8px';
            toast.style.boxShadow = '0 4px 12px rgba(0,0,0,0.15)';
            toast.innerHTML = `<i class="fa-solid ${iconClass}"></i> ${message}`;
            container.appendChild(toast);
            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transition = 'opacity 0.3s ease';
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }

        async function fetchLeads() {
            try {
                let res = await fetch('/api/chats');
                let data = await res.json();
                allLeads = Array.isArray(data) ? data : [];
                renderLeads();
            } catch(e) {
                console.error('Error fetching leads:', e);
            }
        }

        function getTemperatureBadge(score) {
            if(score === 'Hot') return `<span style="background: rgba(241, 65, 108, 0.1); color: var(--danger); padding: 4px 10px; border-radius: 12px; font-weight: 600; font-size: 12px; display: inline-flex; align-items: center; gap: 4px;"><i class="fa-solid fa-fire" style="font-size:10px;"></i> Hot</span>`;
            if(score === 'Cold') return `<span style="background: rgba(161, 165, 183, 0.1); color: var(--text-muted); padding: 4px 10px; border-radius: 12px; font-weight: 600; font-size: 12px; display: inline-flex; align-items: center; gap: 4px;"><i class="fa-solid fa-snowflake" style="font-size:10px;"></i> Cold</span>`;
            // Default warm
            return `<span style="background: rgba(255, 199, 0, 0.1); color: var(--warning); padding: 4px 10px; border-radius: 12px; font-weight: 600; font-size: 12px; display: inline-flex; align-items: center; gap: 4px;"><i class="fa-solid fa-circle" style="font-size:8px;"></i> Warm</span>`;
        }

        function getTimeAgo(dateString) {
            if(!dateString) return '-';
            const date = new Date(dateString);
            const now = new Date();
            const diffMs = now - date;
            const diffMins = Math.round(diffMs / 60000);
            if (diffMins < 60) return diffMins <= 1 ? 'baru saja' : `${diffMins} mnt lalu`;
            const diffHours = Math.round(diffMins / 60);
            if (diffHours < 24) return `${diffHours} jam lalu`;
            const diffDays = Math.round(diffHours / 24);
            return `${diffDays} hari lalu`;
        }

        function renderLeads() {
            const tbody = document.querySelector('.table tbody');
            const emptyState = document.querySelector('.empty-state');
            
            currentFilteredLeads = allLeads;
            
            // Search filter
            const sq = document.getElementById('searchInput') ? document.getElementById('searchInput').value.toLowerCase() : '';
            if(sq) {
                currentFilteredLeads = currentFilteredLeads.filter(l => 
                    (l.client_name && l.client_name.toLowerCase().includes(sq)) || 
                    (l.client_wa_number && l.client_wa_number.toLowerCase().includes(sq)) ||
                    (l.location && l.location.toLowerCase().includes(sq))
                );
            }
            
            // Status filter
            const st = document.getElementById('statusSelect') ? document.getElementById('statusSelect').value : 'All Status';
            if(st !== 'All Status') {
                currentFilteredLeads = currentFilteredLeads.filter(l => l.status === st);
            }
            
            // Date filter
            const dateRange = document.getElementById('dateFilter') ? document.getElementById('dateFilter').value : '';
            if(dateRange && dateRange.includes(' to ')) {
                const [startStr, endStr] = dateRange.split(' to ');
                const startDate = new Date(startStr);
                startDate.setHours(0,0,0,0);
                const endDate = new Date(endStr);
                endDate.setHours(23,59,59,999);
                
                currentFilteredLeads = currentFilteredLeads.filter(l => {
                    if(!l.updated_at) return false;
                    const d = new Date(l.updated_at);
                    return d >= startDate && d <= endDate;
                });
            }
            
            if(currentFilteredLeads.length === 0) {
                tbody.innerHTML = '';
                emptyState.style.display = 'block';
                document.querySelector('.panel-footer div:first-child').innerText = `Menampilkan 0 dari 0 lead`;
                document.getElementById('selectAllLeads').checked = false;
                toggleDeleteSelectedButton();
                return;
            }
            
            emptyState.style.display = 'none';
            let html = '';
            
            currentFilteredLeads.forEach(lead => {
                let lastContacted = getTimeAgo(lead.updated_at);
                let scoreBadge = getTemperatureBadge(lead.lead_score);
                let name = lead.client_name || '-';
                let venue = lead.location ? lead.location : '-';
                let event_date = lead.event_date ? lead.event_date : '-';
                let package_name = lead.package || '-';
                let status = lead.status || 'New Inquiry';
                
                html += `
                    <tr>
                        <td><input type="checkbox" class="lead-checkbox" value="${lead.id}" onchange="toggleDeleteSelectedButton()"></td>
                        <td>
                            <div style="font-weight: 600; color: var(--text-dark);">${name}</div>
                            <div style="font-size: 12px; color: var(--text-muted); margin-top: 2px;">${lead.client_wa_number}</div>
                        </td>
                        <td>
                            <div style="color: var(--text-dark);">${event_date}</div>
                            <div style="font-size: 12px; color: var(--text-muted); margin-top: 2px;">${venue}</div>
                        </td>
                        <td>${package_name}</td>
                        <td>${scoreBadge}</td>
                        <td>
                            <div style="display: inline-flex; align-items: center; padding: 6px 12px; background: rgba(107, 92, 216, 0.1); color: var(--primary); border-radius: 20px; font-size: 12px; font-weight: 600;">
                                ${status}
                            </div>
                        </td>
                        <td>${lastContacted}</td>
                        <td>
                            <div style="display: flex; gap: 12px; align-items: center;">
                                <a href="/chat?id=${lead.id}" style="color: var(--text-muted); cursor: pointer; transition: 0.2s;" onmouseover="this.style.color='var(--primary)'" onmouseout="this.style.color='var(--text-muted)'" title="Buka Chat">
                                    <i class="fa-regular fa-comment"></i>
                                </a>
                                <span style="color: var(--text-muted); cursor: pointer; transition: 0.2s;" onmouseover="this.style.color='var(--primary)'" onmouseout="this.style.color='var(--text-muted)'" onclick="openEditModal(${lead.id})" title="Edit Lead">
                                    <i class="fa-solid fa-pen"></i>
                                </span>
                                <span style="color: var(--danger); cursor: pointer; transition: 0.2s;" onmouseover="this.style.opacity='0.7'" onmouseout="this.style.opacity='1'" onclick="openDeleteModal(${lead.id})" title="Hapus Lead">
                                    <i class="fa-regular fa-trash-can"></i>
                                </span>
                            </div>
                        </td>
                    </tr>
                `;
            });
            
            tbody.innerHTML = html;
            
            // Update counter
            document.querySelector('.panel-footer div:first-child').innerText = `Menampilkan ${currentFilteredLeads.length} dari ${allLeads.length} lead`;
            
            document.getElementById('selectAllLeads').checked = false;
            toggleDeleteSelectedButton();
        }
        
        function toggleAllLeads(source) {
            const checkboxes = document.querySelectorAll('.lead-checkbox');
            checkboxes.forEach(cb => cb.checked = source.checked);
            toggleDeleteSelectedButton();
        }
        
        function toggleDeleteSelectedButton() {
            const checkboxes = document.querySelectorAll('.lead-checkbox:checked');
            const btn = document.getElementById('btnDeleteSelected');
            if(checkboxes.length > 0) {
                btn.style.display = 'inline-flex';
            } else {
                btn.style.display = 'none';
            }
        }
        
        function confirmDeleteSelected() {
            const checkboxes = document.querySelectorAll('.lead-checkbox:checked');
            if(checkboxes.length === 0) return;
            document.getElementById('deleteMultipleCount').innerText = checkboxes.length;
            document.getElementById('deleteMultipleConfirmModal').style.display = 'flex';
        }
        
        function closeDeleteMultipleModal() {
            document.getElementById('deleteMultipleConfirmModal').style.display = 'none';
        }
        
        async function executeDeleteMultiple() {
            const checkboxes = document.querySelectorAll('.lead-checkbox:checked');
            if(checkboxes.length === 0) return;
            
            showToast('Menghapus data...', 'fa-spinner fa-spin');
            
            for(let cb of checkboxes) {
                try {
                    await fetch(`/api/chats/${cb.value}`, {
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                    });
                } catch(e) {
                    console.error(e);
                }
            }
            
            closeDeleteMultipleModal();
            showToast(checkboxes.length + ' Lead berhasil dihapus', 'fa-check');
            fetchLeads();
        }
        
        function exportCSV() {
            let dataToExport = currentFilteredLeads;
            
            // Check if any specific rows are selected
            const selectedCheckboxes = document.querySelectorAll('.lead-checkbox:checked');
            if (selectedCheckboxes.length > 0) {
                const selectedIds = Array.from(selectedCheckboxes).map(cb => parseInt(cb.value));
                dataToExport = currentFilteredLeads.filter(l => selectedIds.includes(l.id));
            }
            
            if(dataToExport.length === 0) {
                showToast('Tidak ada lead untuk diexport', 'fa-triangle-exclamation');
                return;
            }
            
            let csvContent = "data:text/csv;charset=utf-8,";
            csvContent += "Client Name,WhatsApp Number,Event Date,Location,Package,Lead Score,Status,Last Contacted\n";
            
            dataToExport.forEach(function(rowArray) {
                let row = [
                    `"${rowArray.client_name || ''}"`,
                    `"'${rowArray.client_wa_number || ''}"`,
                    `"${rowArray.event_date || ''}"`,
                    `"${rowArray.location || ''}"`,
                    `"${rowArray.package || ''}"`,
                    `"${rowArray.lead_score || ''}"`,
                    `"${rowArray.status || ''}"`,
                    `"${rowArray.updated_at ? new Date(rowArray.updated_at).toLocaleString() : ''}"`
                ];
                csvContent += row.join(",") + "\n";
            });
            
            const encodedUri = encodeURI(csvContent);
            const link = document.createElement("a");
            link.setAttribute("href", encodedUri);
            link.setAttribute("download", "leads_export.csv");
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            
            showToast('Mengekspor data ke CSV...', 'fa-download');
        }
        
        function openAddModal() {
            editingLeadId = null;
            document.getElementById('modalLeadTitle').innerText = 'Tambah Lead';
            
            document.getElementById('editLeadName').value = '';
            document.getElementById('editLeadWa').value = '';
            document.getElementById('editLeadWa').readOnly = false;
            document.getElementById('editLeadWa').style.background = 'white';
            
            document.getElementById('editLeadEventDate').value = '';
            document.getElementById('editLeadVenue').value = '';
            document.getElementById('editLeadPackage').value = '';
            document.getElementById('editLeadScore').value = 'Warm';
            document.getElementById('editLeadStatus').value = 'New Inquiry';
            
            document.getElementById('editLeadModal').style.display = 'flex';
        }
        
        function openEditModal(id) {
            editingLeadId = id;
            document.getElementById('modalLeadTitle').innerText = 'Edit Lead';
            
            let lead = allLeads.find(l => l.id === id);
            if(!lead) return;
            
            document.getElementById('editLeadName').value = lead.client_name || '';
            document.getElementById('editLeadWa').value = lead.client_wa_number || '';
            document.getElementById('editLeadWa').readOnly = true;
            document.getElementById('editLeadWa').style.background = '#f8f9fa';
            
            document.getElementById('editLeadEventDate').value = lead.event_date || '';
            document.getElementById('editLeadVenue').value = lead.location || '';
            document.getElementById('editLeadPackage').value = lead.package || '';
            document.getElementById('editLeadScore').value = lead.lead_score || 'Warm';
            document.getElementById('editLeadStatus').value = lead.status || 'New Inquiry';
            
            document.getElementById('editLeadModal').style.display = 'flex';
        }
        
        function closeEditModal() {
            document.getElementById('editLeadModal').style.display = 'none';
            editingLeadId = null;
        }
        
        async function saveEditLead() {
            try {
                const payload = {
                    client_name: document.getElementById('editLeadName').value,
                    client_wa_number: document.getElementById('editLeadWa').value,
                    event_date: document.getElementById('editLeadEventDate').value,
                    location: document.getElementById('editLeadVenue').value,
                    package: document.getElementById('editLeadPackage').value,
                    lead_score: document.getElementById('editLeadScore').value,
                    status: document.getElementById('editLeadStatus').value
                };
                
                let url = '/api/chats';
                if (editingLeadId) {
                    url = `/api/chats/${editingLeadId}/edit`;
                }
                
                let res = await fetch(url, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify(payload)
                });
                
                if(res.ok) {
                    showToast(editingLeadId ? 'Data lead disimpan' : 'Lead berhasil ditambahkan');
                    closeEditModal();
                    fetchLeads();
                } else {
                    let errText = await res.text();
                    console.error("Server error:", res.status, errText);
                    showToast('Gagal menyimpan: Error ' + res.status, 'fa-xmark');
                }
            } catch(e) {
                console.error("Network or JS error:", e);
                showToast('Terjadi kesalahan pada sistem', 'fa-xmark');
            }
        }
        
        function openDeleteModal(id) {
            deletingLeadId = id;
            document.getElementById('deleteConfirmModal').style.display = 'flex';
        }
        
        function closeDeleteModal() {
            document.getElementById('deleteConfirmModal').style.display = 'none';
            deletingLeadId = null;
        }
        
        async function confirmDeleteLead() {
            if(!deletingLeadId) return;
            try {
                let res = await fetch(`/api/chats/${deletingLeadId}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                });
                if(res.ok) {
                    showToast('Lead dihapus', 'fa-trash-can');
                    closeDeleteModal();
                    fetchLeads();
                }
            } catch(e) {
                console.error(e);
            }
        }

        // INIT
        document.addEventListener('DOMContentLoaded', () => {
            fetchLeads();
            
            flatpickr("#dateFilter", {
                mode: "range",
                dateFormat: "Y-m-d",
                onChange: function(selectedDates, dateStr, instance) {
                    renderLeads();
                }
            });
            
            // Bind search and filter events
            const searchInput = document.getElementById('searchInput');
            if(searchInput) {
                searchInput.addEventListener('input', renderLeads);
            }
            const statusSelect = document.getElementById('statusSelect');
            if(statusSelect) {
                statusSelect.addEventListener('change', renderLeads);
            }
        });
    </script>
</body>
</html>
