<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quotation Generator - CloseMateAI</title>
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

        /* Actions Bar */
        .actions-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; gap: 16px; }
        .tabs { display: flex; gap: 8px; background: white; padding: 6px; border-radius: 30px; border: 1px solid var(--border-color); box-shadow: 0 2px 10px rgba(0,0,0,0.02); }
        .tab-item { padding: 8px 16px; font-size: 13px; font-weight: 500; color: var(--text-muted); cursor: pointer; border-radius: 20px; transition: 0.2s; }
        .tab-item:hover { color: var(--text-dark); }
        .tab-item.active { background: var(--bg-light); color: var(--text-dark); border: 1px solid #e4e6ef; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
        
        .search-add { display: flex; gap: 12px; align-items: center; }
        .search-box { position: relative; }
        .search-box input { padding: 10px 16px 10px 16px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px; width: 260px; outline: none; transition: border-color 0.2s; }
        .search-box input:focus { border-color: var(--primary); }
        
        .btn { padding: 10px 20px; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; transition: 0.2s; border: none; display: flex; align-items: center; gap: 8px; }
        .btn-primary { background-color: var(--primary); color: white; }
        .btn-primary:hover { background-color: var(--primary-hover); }
        .btn-outline { background-color: transparent; border: 1px solid var(--border-color); color: var(--text-dark); }
        .btn-outline:hover { background-color: var(--bg-light); }

        /* Empty State */
        .empty-state { background: white; border-radius: 12px; border: 1px solid var(--border-color); padding: 80px 20px; text-align: center; color: var(--text-muted); box-shadow: 0 2px 10px rgba(0,0,0,0.02); }
        .empty-icon { font-size: 32px; color: #b5b5c3; margin-bottom: 16px; }
        .empty-state p { font-size: 14px; }

        /* Modal Settings */
        .modal-overlay { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 1000; display: flex; justify-content: center; align-items: center; opacity: 0; visibility: hidden; transition: 0.3s ease; padding: 40px; }
        .modal-overlay.show { opacity: 1; visibility: visible; }
        .modal-content { background: white; border-radius: 12px; width: 100%; max-width: 800px; max-height: calc(100vh - 80px); display: flex; flex-direction: column; position: relative; transform: translateY(-20px); transition: 0.3s ease; box-shadow: 0 10px 40px rgba(0,0,0,0.2); }
        .modal-overlay.show .modal-content { transform: translateY(0); }
        
        .modal-header { padding: 24px; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: flex-start; }
        .modal-title h3 { font-size: 18px; font-weight: 600; margin-bottom: 4px; color: var(--text-dark); }
        .modal-title p { font-size: 13px; color: var(--text-muted); }
        .close-btn { background: none; border: none; font-size: 16px; color: var(--text-muted); cursor: pointer; transition: color 0.2s; padding: 4px; }
        .close-btn:hover { color: var(--danger); }
        
        .modal-body { padding: 24px; overflow-y: auto; flex: 1; display: flex; flex-direction: column; gap: 24px; }
        .modal-footer { padding: 20px 24px; border-top: 1px solid var(--border-color); display: flex; justify-content: flex-end; gap: 12px; background: white; border-radius: 0 0 12px 12px; }
        
        /* Forms in Modal */
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .form-group { display: flex; flex-direction: column; gap: 6px; }
        .form-label { font-size: 13px; font-weight: 500; color: var(--text-dark); }
        .form-control { padding: 10px 14px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px; color: var(--text-dark); width: 100%; outline: none; background: white; }
        .form-control:focus { border-color: var(--primary); }
        
        .section-header { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-color); padding-bottom: 12px; margin-bottom: 16px; }
        .section-title { font-size: 14px; font-weight: 600; color: var(--text-dark); }
        .btn-small { padding: 6px 12px; font-size: 12px; }
        
        .item-card { border: 1px solid var(--border-color); border-radius: 8px; padding: 16px; background: #fafafa; display: flex; flex-direction: column; gap: 12px; }
        .item-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .item-calc-row { display: grid; grid-template-columns: 1fr 1.5fr 2fr 2fr auto; gap: 12px; align-items: center; }
        .icon-btn { background: none; border: none; color: var(--danger); cursor: pointer; font-size: 14px; padding: 8px; opacity: 0.7; transition: 0.2s; }
        .icon-btn:hover { opacity: 1; }
        
        .summary-box { display: flex; justify-content: flex-end; }
        .summary-grid { width: 300px; display: grid; grid-template-columns: 1fr auto; gap: 8px; font-size: 13px; }
        .summary-grid .bold { font-weight: 600; color: var(--text-dark); }
        .summary-grid .total-row { padding-top: 8px; border-top: 1px dashed var(--border-color); font-size: 15px; }

        .termin-row { display: grid; grid-template-columns: 2fr 1fr 1fr auto; gap: 12px; align-items: center; margin-bottom: 12px; }
        .termin-value { font-size: 12px; color: var(--text-muted); margin-top: -6px; margin-bottom: 16px; }
        
        textarea.form-control { resize: vertical; min-height: 80px; font-family: 'Inter', sans-serif; }

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
                <h1>Quotation Generator</h1>
                <p>Buat penawaran harga digital, kirim link ke klien, dan convert jadi booking</p>
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

        <div class="actions-bar">
            <div class="tabs">
                <div class="tab-item active">Semua</div>
                <div class="tab-item">Draft</div>
                <div class="tab-item">Terkirim</div>
                <div class="tab-item">Disetujui</div>
                <div class="tab-item">Ditolak</div>
            </div>
            
            <div class="search-add">
                <div class="search-box">
                    <input type="text" placeholder="Cari klien / no. penawaran">
                </div>
                <button class="btn btn-primary" id="btn-create-quotation">
                    <i class="fa-solid fa-plus"></i> Buat Penawaran
                </button>
            </div>
        </div>
        
        <div class="empty-state">
            <div class="empty-icon">
                <i class="fa-solid fa-file-signature"></i>
            </div>
            <p>Belum ada penawaran. Buat penawaran pertama Anda sekarang.</p>
        </div>

    </div>

    <!-- Create Quotation Modal -->
    <div class="modal-overlay" id="quotation-modal">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title">
                    <h3>Buat Penawaran Baru</h3>
                    <p>Susun rincian layanan, termin pembayaran, dan syarat & ketentuan penawaran Anda.</p>
                </div>
                <button class="close-btn" id="close-modal"><i class="fa-solid fa-xmark"></i></button>
            </div>
            
            <div class="modal-body">
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">No. Penawaran</label>
                        <input type="text" class="form-control" value="QT-20261003-870">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Status</label>
                        <select class="form-control">
                            <option>Draft</option>
                            <option>Terkirim</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nama Klien</label>
                        <input type="text" class="form-control" placeholder="Nama calon klien">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nomor WhatsApp</label>
                        <input type="text" class="form-control" placeholder="08xxxxxxxxx">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tanggal Acara</label>
                        <input type="date" class="form-control" placeholder="mm/dd/yyyy">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Lokasi Venue / Kota</label>
                        <input type="text" class="form-control" placeholder="Contoh: Hotel Mulia, Jakarta">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Berlaku Sampai</label>
                        <input type="date" class="form-control" placeholder="mm/dd/yyyy">
                    </div>
                </div>
                
                <div>
                    <div class="section-header">
                        <div class="section-title">Item Penawaran</div>
                        <button class="btn btn-outline btn-small"><i class="fa-solid fa-plus"></i> Tambah Item</button>
                    </div>
                    
                    <div class="item-card">
                        <div class="item-row">
                            <div class="form-group">
                                <label class="form-label">Section (opsional)</label>
                                <input type="text" class="form-control" value="Paket Utama">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Nama Layanan / Produk</label>
                                <input type="text" class="form-control" placeholder="Contoh: Makeup Akad + Resepsi">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Deskripsi Detail</label>
                            <textarea class="form-control" placeholder="Rincian layanan yang didapatkan klien"></textarea>
                        </div>
                        <div class="item-calc-row" style="margin-top: 8px;">
                            <div class="form-group">
                                <label class="form-label">Qty</label>
                                <input type="number" class="form-control" value="1">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Satuan</label>
                                <select class="form-control">
                                    <option>Paket</option>
                                    <option>Pcs</option>
                                    <option>Sesi</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Harga Satuan</label>
                                <input type="text" class="form-control" value="0">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Subtotal</label>
                                <input type="text" class="form-control" value="Rp 0" readonly style="background: #f1f1f4; border: none;">
                            </div>
                            <button class="icon-btn" style="margin-top: 24px;"><i class="fa-solid fa-trash-can"></i></button>
                        </div>
                    </div>
                </div>
                
                <div class="form-grid" style="align-items: flex-start;">
                    <div class="form-group">
                        <label class="form-label">Diskon (Rp)</label>
                        <input type="text" class="form-control" value="0">
                    </div>
                    <div class="summary-box">
                        <div class="summary-grid">
                            <div style="color: var(--text-muted);">Subtotal</div>
                            <div class="bold">Rp 0</div>
                            <div style="color: var(--text-muted);">Diskon</div>
                            <div class="bold">- Rp 0</div>
                            <div class="bold total-row">Grand Total</div>
                            <div class="bold total-row">Rp 0</div>
                        </div>
                    </div>
                </div>
                
                <div style="margin-top: 10px;">
                    <div class="section-header">
                        <div class="section-title">Skema Termin Pembayaran</div>
                        <button class="btn btn-outline btn-small"><i class="fa-solid fa-plus"></i> Tambah Termin</button>
                    </div>
                    
                    <div class="termin-row">
                        <input type="text" class="form-control" value="DP 1 (Booking Fee)">
                        <select class="form-control">
                            <option>Persentase (%)</option>
                            <option>Nominal (Rp)</option>
                        </select>
                        <input type="number" class="form-control" value="30">
                        <button class="icon-btn"><i class="fa-solid fa-trash-can"></i></button>
                    </div>
                    <div class="termin-value">Nilai termin: Rp 0</div>
                    
                    <div class="termin-row">
                        <input type="text" class="form-control" value="DP 2">
                        <select class="form-control">
                            <option>Persentase (%)</option>
                            <option>Nominal (Rp)</option>
                        </select>
                        <input type="number" class="form-control" value="40">
                        <button class="icon-btn"><i class="fa-solid fa-trash-can"></i></button>
                    </div>
                    <div class="termin-value">Nilai termin: Rp 0</div>
                    
                    <div class="termin-row">
                        <input type="text" class="form-control" value="Pelunasan">
                        <select class="form-control">
                            <option>Persentase (%)</option>
                            <option>Nominal (Rp)</option>
                        </select>
                        <input type="number" class="form-control" value="30">
                        <button class="icon-btn"><i class="fa-solid fa-trash-can"></i></button>
                    </div>
                    <div class="termin-value">Nilai termin: Rp 0</div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Syarat & Ketentuan (T&C / SOP Vendor)</label>
                    <textarea class="form-control" style="min-height: 140px;">1. Penawaran ini berlaku sesuai tanggal masa berlaku di atas.
2. Tanggal acara dianggap ter-booking setelah DP 1 diterima.
3. DP yang sudah dibayarkan tidak dapat dikembalikan (non-refundable).
4. Pelunasan dilakukan paling lambat H-7 sebelum hari acara.
5. Perubahan susunan item/paket wajib dikonfirmasi maksimal H-14 sebelum acara.</textarea>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Catatan Internal (tidak tampil ke klien)</label>
                    <textarea class="form-control" style="min-height: 80px;"></textarea>
                </div>
                
            </div>
            
            <div class="modal-footer">
                <button class="btn btn-outline" id="btn-cancel-modal">Batal</button>
                <button class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Simpan Penawaran</button>
            </div>
        </div>
    </div>

    <script>
        // Sidebar Collapse
        document.getElementById('btn-collapse').addEventListener('click', function() {
            document.body.classList.toggle('sidebar-collapsed');
        });

        // Modal Logic
        const modal = document.getElementById('quotation-modal');
        const btnCreate = document.getElementById('btn-create-quotation');
        const btnClose = document.getElementById('close-modal');
        const btnCancel = document.getElementById('btn-cancel-modal');

        btnCreate.addEventListener('click', () => {
            modal.classList.add('show');
            document.body.style.overflow = 'hidden';
        });

        const closeModal = () => {
            modal.classList.remove('show');
            document.body.style.overflow = '';
        };

        btnClose.addEventListener('click', closeModal);
        btnCancel.addEventListener('click', closeModal);

        // Close on overlay click
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                closeModal();
            }
        });
    </script>
</body>
</html>
