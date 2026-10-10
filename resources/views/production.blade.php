<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Progres Produksi - CloseMateAI</title>
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
            --info: #3b82f6;
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

        /* Stats Grid */
        .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 30px; }
        .stat-card { background: white; border: 1px solid var(--border-color); border-radius: 12px; padding: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.02); display: flex; flex-direction: column; gap: 12px; position: relative; }
        
        .stat-header { display: flex; align-items: center; gap: 10px; }
        .stat-icon { width: 32px; height: 32px; border-radius: 8px; display: flex; justify-content: center; align-items: center; font-size: 14px; }
        .stat-title { font-size: 13px; font-weight: 500; color: var(--text-muted); }
        
        .stat-value { font-size: 28px; font-weight: 700; color: var(--text-dark); margin-top: 8px; }
        .stat-desc { font-size: 13px; color: var(--text-muted); margin-top: auto; }

        /* Card Themes */
        .stat-card.blue .stat-icon { background: rgba(59, 130, 246, 0.1); color: var(--info); }
        .stat-card.orange .stat-icon { background: rgba(245, 158, 11, 0.1); color: #f59e0b; }
        .stat-card.red .stat-icon { background: rgba(241, 65, 108, 0.1); color: var(--danger); }
        .stat-card.cyan .stat-icon { background: rgba(6, 182, 212, 0.1); color: #06b6d4; }

        /* Project Card */
        .project-card { background: white; border: 1px solid var(--border-color); border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.02); display: flex; flex-direction: column; width: 100%; max-width: 600px; }
        .project-header { padding: 20px 24px; border-bottom: 1px solid var(--border-color); }
        .project-top { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px; }
        .project-title { font-size: 18px; font-weight: 600; color: var(--text-dark); }
        
        .btn-outline { background-color: white; border: 1px solid var(--border-color); color: var(--text-dark); padding: 8px 16px; border-radius: 20px; font-size: 13px; font-weight: 500; cursor: pointer; transition: 0.2s; display: inline-flex; align-items: center; gap: 8px; }
        .btn-outline:hover { background-color: var(--bg-light); border-color: #e4e6ef; }
        
        .project-meta { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; margin-bottom: 12px; }
        .meta-item { display: flex; align-items: center; gap: 6px; font-size: 13px; color: var(--text-muted); }
        
        .badge { padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 500; display: inline-flex; align-items: center; }
        .badge-danger { background-color: var(--danger-bg); color: var(--danger); }
        .badge-danger-soft { background-color: rgba(241, 65, 108, 0.1); color: var(--danger); border: 1px solid rgba(241, 65, 108, 0.2); }
        .badge-info-soft { background-color: rgba(59, 130, 246, 0.1); color: var(--info); border: 1px solid rgba(59, 130, 246, 0.2); }
        
        .icon-btn-small { color: var(--text-muted); cursor: pointer; transition: 0.2s; }
        .icon-btn-small:hover { color: var(--text-dark); }

        .project-body { padding: 24px; border-bottom: 1px solid var(--border-color); color: var(--text-muted); font-size: 14px; }
        
        .project-footer { padding: 16px 24px; display: flex; justify-content: space-between; align-items: center; background: #fafafa; border-radius: 0 0 12px 12px; }
        .badge-gray { background-color: #f1f1f4; color: var(--text-muted); padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 500; border: 1px solid #e4e6ef; }
        
        .btn-primary { background-color: var(--primary); color: white; padding: 10px 20px; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; transition: 0.2s; border: none; display: inline-flex; align-items: center; gap: 8px; }
        .btn-primary:hover { background-color: var(--primary-hover); }

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
                <h1>Progres Produksi</h1>
                <p>Tracker pasca-acara: editing, QC, hingga delivery ke klien</p>
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

        <div class="stats-grid">
            <div class="stat-card blue">
                <div class="stat-header">
                    <div class="stat-icon"><i class="fa-regular fa-paste"></i></div>
                    <div class="stat-title">Project In-Production</div>
                </div>
                <div class="stat-value" id="stat-in-production">1</div>
            </div>
            
            <div class="stat-card orange">
                <div class="stat-header">
                    <div class="stat-icon"><i class="fa-regular fa-clock"></i></div>
                    <div class="stat-title">Task Mendekati Deadline (H-3)</div>
                </div>
                <div class="stat-value" id="stat-h3">0</div>
            </div>
            
            <div class="stat-card red">
                <div class="stat-header">
                    <div class="stat-icon"><i class="fa-solid fa-triangle-exclamation"></i></div>
                    <div class="stat-title">Red Alert Overdue Task</div>
                </div>
                <div class="stat-value" id="stat-overdue">0</div>
            </div>
            
            <div class="stat-card cyan">
                <div class="stat-header">
                    <div class="stat-icon"><i class="fa-solid fa-users"></i></div>
                    <div class="stat-title">Workload PIC</div>
                </div>
                <div class="stat-desc" id="stat-workload" style="margin-top: 8px;">Belum ada task aktif.</div>
            </div>
        </div>

        <div id="projects_container" style="display: flex; flex-direction: column; gap: 20px;">
            <!-- Rendered by JS -->
        </div>

    </div>

    <div class="modal-overlay" id="trackModal">
        <div class="modal">
            <div class="modal-header">
                <div class="modal-title">
                    <h3>Tambah Track Produksi</h3>
                    <p id="modalClientName">Kelola progres pasca-acara untuk Test.</p>
                </div>
                <button class="modal-close" onclick="closeTrackModal()"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="trackBookingId">
                <input type="hidden" id="trackEditId">
                
                <div class="form-group">
                    <label>Kategori Task</label>
                    <select class="form-control" id="trackKategori">
                        <option>Photo Editing</option>
                        <option>Video Editing</option>
                        <option>Album Design</option>
                        <option>Cinematic Highlight</option>
                        <option>Same Day Edit</option>
                        <option>MUA Trial</option>
                        <option>Printing</option>
                        <option>Lainnya (ketik manual)</option>
                    </select>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Nama PIC</label>
                        <input type="text" class="form-control" id="trackPicName" placeholder="Contoh: bekti">
                    </div>
                    <div class="form-group">
                        <label>Nomor WA PIC</label>
                        <input type="text" class="form-control" id="trackPicWa" placeholder="0812xxxxxxx">
                    </div>
                </div>
                <div class="form-group">
                    <label>Stage Saat Ini</label>
                    <select class="form-control" id="trackStage">
                        <option>Sortir</option>
                        <option>Coloring</option>
                        <option>Retouch</option>
                        <option>QC</option>
                        <option>Delivery</option>
                    </select>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Tanggal Deadline Task</label>
                        <input type="date" class="form-control" id="trackDeadline">
                    </div>
                    <div class="form-group">
                        <label>Tanggal Reminder WA</label>
                        <input type="date" class="form-control" id="trackReminder">
                    </div>
                </div>
                <div class="form-group">
                    <label>Pesan Reminder ke WA PIC</label>
                    <textarea class="form-control" id="trackMessage" rows="3" placeholder="Halo {pic}, jangan lupa {kategori} untuk {klien}. Deadline {deadline} ya 🙏"></textarea>
                    <div style="font-size: 11px; color: var(--text-muted); margin-top: -4px;">Placeholder tersedia: {pic} {klien} {kategori} {stage} {deadline} {link}. Kosongkan untuk memakai pesan default.</div>
                </div>
                <div class="form-group">
                    <label>Link Project (Drive/Dropbox)</label>
                    <input type="url" class="form-control" id="trackLink" placeholder="https://drive.google.com/...">
                </div>
                <div class="form-group">
                    <label>Catatan Internal</label>
                    <textarea class="form-control" id="trackInternal" rows="2" placeholder="Catatan untuk tim produksi..."></textarea>
                </div>
                <div class="form-group">
                    <label>Catatan Revisi (Project)</label>
                    <textarea class="form-control" id="trackRevision" rows="2" placeholder="Permintaan revisi dari klien..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn-light" onclick="closeTrackModal()">Batal</button>
                <button class="btn-primary" onclick="saveTrack()">Simpan Task</button>
            </div>
        </div>
    </div>

    <div class="toast-container" id="toast-container"></div>

    <style>
        .modal-overlay { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); display: flex; justify-content: center; align-items: center; z-index: 1000; opacity: 0; pointer-events: none; transition: 0.2s; padding: 20px; }
        .modal-overlay.active { opacity: 1; pointer-events: all; }
        .modal { background: white; border-radius: 12px; width: 100%; max-width: 500px; max-height: 90vh; overflow-y: auto; display: flex; flex-direction: column; transform: translateY(20px); transition: 0.3s; }
        .modal-overlay.active .modal { transform: translateY(0); }
        .modal-header { padding: 20px 24px; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: flex-start; position: sticky; top: 0; background: white; z-index: 2; }
        .modal-title h3 { font-size: 18px; font-weight: 600; }
        .modal-title p { font-size: 13px; color: var(--text-muted); margin-top: 4px; }
        .modal-close { background: transparent; border: none; font-size: 16px; color: var(--text-muted); cursor: pointer; padding: 4px; }
        .modal-body { padding: 24px; display: flex; flex-direction: column; gap: 16px; }
        .form-group { display: flex; flex-direction: column; gap: 8px; }
        .form-group label { font-size: 13px; font-weight: 500; color: var(--text-dark); }
        .form-control { padding: 10px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 14px; outline: none; transition: 0.2s; }
        .form-control:focus { border-color: var(--primary); }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .modal-footer { padding: 16px 24px; border-top: 1px solid var(--border-color); display: flex; justify-content: flex-end; gap: 12px; position: sticky; bottom: 0; background: white; z-index: 2; }
        .btn-light { background: white; border: 1px solid var(--border-color); padding: 10px 20px; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; color: var(--text-dark); }
        .btn-light:hover { background: var(--bg-light); }
        
        .toast-container { position: fixed; bottom: 24px; right: 24px; z-index: 9999; display: flex; flex-direction: column; gap: 12px; }
        .toast { background: white; border-radius: 8px; padding: 16px 20px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); border-left: 4px solid var(--success); display: flex; align-items: center; gap: 12px; font-size: 14px; font-weight: 500; color: var(--text-dark); animation: slideInRight 0.3s cubic-bezier(0.4, 0, 0.2, 1); transition: opacity 0.3s, transform 0.3s; }
        .toast i { color: var(--success); font-size: 18px; }
        @keyframes slideInRight { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
    </style>

    <script>
        let globalBookings = [];
        const csrfToken = '{{ csrf_token() }}';

        // Sidebar Collapse
        document.getElementById('btn-collapse').addEventListener('click', function() {
            document.body.classList.toggle('sidebar-collapsed');
        });

        function showToast(message) {
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

        async function loadProjects() {
            try {
                let res = await fetch('/api/bookings');
                globalBookings = await res.json();
                renderProjects();
            } catch(e) {
                console.error(e);
            }
        }

        function formatDateDisplay(dateStr) {
            if (!dateStr) return '-';
            const d = new Date(dateStr);
            const formatter = new Intl.DateTimeFormat('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
            return formatter.format(d);
        }

        function renderProjects() {
            const container = document.getElementById('projects_container');
            if (globalBookings.length === 0) {
                container.innerHTML = '<div style="color: var(--text-muted);">Belum ada project booking.</div>';
                return;
            }

            let statInProduction = 0;
            let statH3 = 0;
            let statOverdue = 0;
            let picWorkload = {};

            let html = '';
            globalBookings.forEach(b => {
                if(b.production_status !== 'Selesai') statInProduction++;
                
                let deadlineHtml = '';
                
                if (b._isEditingDeadline) {
                    deadlineHtml = `
                    <div style="display: flex; gap: 8px; align-items: center;">
                        <input type="date" id="deadline_input_${b.id}" value="${b.production_deadline || ''}" style="padding: 4px 8px; border: 1px solid var(--border-color); border-radius: 4px; font-size: 13px; outline: none;">
                        <button onclick="saveDeadline(${b.id})" style="background: var(--success); color: white; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer; display: flex; align-items: center; justify-content: center;"><i class="fa-solid fa-check"></i></button>
                        <button onclick="cancelDeadlineEdit(${b.id})" style="background: white; border: 1px solid var(--border-color); color: var(--text-muted); padding: 5px 10px; border-radius: 4px; cursor: pointer; display: flex; align-items: center; justify-content: center;"><i class="fa-solid fa-xmark"></i></button>
                    </div>
                    `;
                } else {
                    if (b.production_deadline) {
                        const d = new Date(b.production_deadline);
                        d.setHours(0,0,0,0);
                        const today = new Date();
                        today.setHours(0,0,0,0);
                        const diffTime = d - today;
                        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)); 
                        
                        if (diffDays < 0) {
                            deadlineHtml = `<div class="badge badge-danger-soft">Terlambat ${Math.abs(diffDays)} hari</div>`;
                        } else if (diffDays === 0) {
                            deadlineHtml = `<div class="badge badge-danger-soft" style="color: #f59e0b; background: rgba(245, 158, 11, 0.1); border-color: rgba(245, 158, 11, 0.2);">Hari ini</div>`;
                        } else {
                            deadlineHtml = `<div class="badge badge-gray">H-${diffDays}</div>`;
                        }
                    } else {
                        deadlineHtml = `<div class="badge badge-gray" style="font-size: 11px;">Belum set deadline</div>`;
                    }
                    
                    deadlineHtml += `<i class="fa-solid fa-pencil icon-btn-small" style="margin-left: 4px;" onclick="editDeadline(${b.id})"></i>`;
                }

                let revisionCount = 0;
                let bodyHtml = '';
                if(b.production_tracks && b.production_tracks.length > 0) {
                    let tracksHtml = '';
                    b.production_tracks.forEach(t => {
                        if(t.revision_notes && t.revision_notes.trim() !== '') {
                            revisionCount++;
                        }
                        
                        // stats logic
                        if (t.deadline_task) {
                            const d = new Date(t.deadline_task);
                            d.setHours(0,0,0,0);
                            const today = new Date();
                            today.setHours(0,0,0,0);
                            const diffDays = Math.ceil((d - today) / (1000 * 60 * 60 * 24));
                            
                            if (diffDays < 0 && t.stage !== 'Delivery') {
                                statOverdue++;
                            } else if (diffDays >= 0 && diffDays <= 3 && t.stage !== 'Delivery') {
                                statH3++;
                            }
                        }
                        
                        if (t.pic_name && t.stage !== 'Delivery') {
                            const pName = t.pic_name.toLowerCase().trim();
                            if(!picWorkload[pName]) picWorkload[pName] = 0;
                            picWorkload[pName]++;
                        }
                        
                        let deadlineText = t.deadline_task ? formatDateDisplay(t.deadline_task) : 'Belum diatur';
                        
                        let deadlineWarning = '';
                        if (t.deadline_task) {
                            const d = new Date(t.deadline_task);
                            d.setHours(0,0,0,0);
                            const today = new Date();
                            today.setHours(0,0,0,0);
                            const diffDays = Math.ceil((d - today) / (1000 * 60 * 60 * 24));
                            if (diffDays === 0) {
                                deadlineWarning = ' <span style="color: #f59e0b; font-weight: 500;">(Deadline hari ini)</span>';
                            } else if (diffDays < 0) {
                                deadlineWarning = ` <span style="color: var(--danger); font-weight: 500;">(Terlambat ${Math.abs(diffDays)} hari)</span>`;
                            }
                        }
                        
                        let reminderText = t.reminder_date ? formatDateDisplay(t.reminder_date) : 'Belum diatur';
                        
                        tracksHtml += `
                        <div style="background-color: #fafafa; border-radius: 8px; margin-bottom: 12px; padding: 16px; border: 1px solid var(--border-color);">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px;">
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <div style="font-weight: 600; font-size: 14px; color: var(--text-dark);">${t.kategori || ''}</div>
                                    <div class="badge badge-gray" style="font-size: 11px;">${t.stage || ''}</div>
                                </div>
                                <div style="display: flex; gap: 8px;">
                                    <i class="fa-solid fa-pencil icon-btn-small" onclick="editTrack(${b.id}, '${t.id}')"></i>
                                    <i class="fa-solid fa-trash icon-btn-small" style="color: var(--danger);" onclick="deleteTrack(${b.id}, '${t.id}')"></i>
                                </div>
                            </div>
                            <div style="font-size: 13px; color: var(--text-muted); display: flex; flex-direction: column; gap: 4px;">
                                <div>PIC: ${t.pic_name || '-'} &middot; ${t.pic_wa || '-'}</div>
                                <div>Deadline: ${deadlineText}${deadlineWarning}</div>
                                <div>Reminder: ${reminderText}</div>
                            </div>
                        </div>
                        `;
                    });
                    bodyHtml = `<div class="project-body" style="padding: 24px; border-bottom: 1px solid var(--border-color);">${tracksHtml}</div>`;
                } else {
                    bodyHtml = `<div class="project-body" style="padding: 24px; border-bottom: 1px solid var(--border-color); color: var(--text-muted); font-size: 14px;">Belum ada track produksi. Tambahkan track pertama.</div>`;
                }

                html += `
                <div class="project-card">
                    <div class="project-header">
                        <div class="project-top">
                            <div class="project-title">${b.client_name || 'Tanpa Nama'}</div>
                            <button class="btn-outline" onclick="copyClientLink('${b.uuid}')"><i class="fa-solid fa-link"></i> Copy Client Progress Link</button>
                        </div>
                        <div class="project-meta">
                            <div class="meta-item"><i class="fa-regular fa-calendar"></i> ${formatDateDisplay(b.event_date)}</div>
                            <div style="display: flex; gap: 8px; align-items: center; min-height: 28px;">
                                ${deadlineHtml}
                            </div>
                        </div>
                        <div class="badge badge-info-soft">${b.production_status || 'In Production'}</div>
                    </div>
                    ${bodyHtml}
                    <div class="project-footer">
                        <div class="badge-gray">Revisi: ${revisionCount}x</div>
                        <button class="btn-primary" onclick="openTrackModal(${b.id}, '${b.client_name ? b.client_name.replace(/'/g, "\\'") : ''}')"><i class="fa-solid fa-plus"></i> Tambah Track Baru</button>
                    </div>
                </div>
                `;
            });

            container.innerHTML = html;
            
            // update stats UI
            document.getElementById('stat-in-production').innerText = statInProduction;
            document.getElementById('stat-h3').innerText = statH3;
            document.getElementById('stat-overdue').innerText = statOverdue;
            
            let workloadText = [];
            for (const [pic, count] of Object.entries(picWorkload)) {
                workloadText.push(`${pic}: ${count} Task`);
            }
            document.getElementById('stat-workload').innerText = workloadText.length > 0 ? workloadText.join(' | ') : 'Belum ada task aktif.';
        }

        function copyClientLink(uuid) {
            const url = window.location.origin + '/progress/' + uuid;
            navigator.clipboard.writeText(url).then(() => {
                showToast("Link progres klien disalin.");
            });
        }

        function editDeadline(id) {
            const b = globalBookings.find(x => x.id === id);
            if(b) {
                b._isEditingDeadline = true;
                renderProjects();
            }
        }

        function cancelDeadlineEdit(id) {
            const b = globalBookings.find(x => x.id === id);
            if(b) {
                b._isEditingDeadline = false;
                renderProjects();
            }
        }

        async function saveDeadline(id) {
            const b = globalBookings.find(x => x.id === id);
            if(!b) return;
            
            const inputVal = document.getElementById('deadline_input_' + id).value;
            b.production_deadline = inputVal;
            b._isEditingDeadline = false;
            renderProjects();
            
            try {
                await fetch('/api/bookings', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        id: b.id,
                        production_deadline: inputVal
                    })
                });
                
                showToast("Deadline project diperbarui.");
                if(window.logSysActivity) {
                    window.logSysActivity('Handler', '{{ auth()->check() ? auth()->user()->business_name : "Sistem" }}', 'Memperbarui deadline project', 'Klien: ' + b.client_name, 'fa-clock', 'orange');
                }
            } catch(e) {
                console.error(e);
                alert("Gagal menyimpan deadline");
            }
        }

        function openTrackModal(bookingId, clientName) {
            document.getElementById('trackBookingId').value = bookingId;
            document.getElementById('trackEditId').value = '';
            document.getElementById('modalClientName').innerText = `Kelola progres pasca-acara untuk ${clientName || 'Tanpa Nama'}.`;
            
            // Clear inputs
            document.getElementById('trackKategori').selectedIndex = 0;
            document.getElementById('trackPicName').value = '';
            document.getElementById('trackPicWa').value = '';
            document.getElementById('trackStage').selectedIndex = 0;
            document.getElementById('trackDeadline').value = '';
            document.getElementById('trackReminder').value = '';
            document.getElementById('trackMessage').value = '';
            document.getElementById('trackLink').value = '';
            document.getElementById('trackInternal').value = '';
            document.getElementById('trackRevision').value = '';
            
            document.getElementById('trackModal').classList.add('active');
        }

        function closeTrackModal() {
            document.getElementById('trackModal').classList.remove('active');
        }

        function editTrack(bookingId, trackId) {
            const b = globalBookings.find(x => x.id == bookingId);
            if(!b || !b.production_tracks) return;
            const t = b.production_tracks.find(x => x.id == trackId);
            if(!t) return;
            
            document.getElementById('trackBookingId').value = bookingId;
            document.getElementById('trackEditId').value = trackId;
            document.getElementById('modalClientName').innerText = `Edit progres pasca-acara untuk ${b.client_name || 'Tanpa Nama'}.`;
            
            document.getElementById('trackKategori').value = t.kategori || '';
            document.getElementById('trackPicName').value = t.pic_name || '';
            document.getElementById('trackPicWa').value = t.pic_wa || '';
            document.getElementById('trackStage').value = t.stage || '';
            document.getElementById('trackDeadline').value = t.deadline_task || '';
            document.getElementById('trackReminder').value = t.reminder_date || '';
            document.getElementById('trackMessage').value = t.reminder_message || '';
            document.getElementById('trackLink').value = t.link || '';
            document.getElementById('trackInternal').value = t.internal_notes || '';
            document.getElementById('trackRevision').value = t.revision_notes || '';
            
            document.getElementById('trackModal').classList.add('active');
        }

        async function saveTrack() {
            const bookingId = document.getElementById('trackBookingId').value;
            const editId = document.getElementById('trackEditId').value;
            const b = globalBookings.find(x => x.id == bookingId);
            if(!b) return;

            const track = {
                id: editId ? editId : Date.now().toString(),
                kategori: document.getElementById('trackKategori').value,
                pic_name: document.getElementById('trackPicName').value,
                pic_wa: document.getElementById('trackPicWa').value,
                stage: document.getElementById('trackStage').value,
                deadline_task: document.getElementById('trackDeadline').value,
                reminder_date: document.getElementById('trackReminder').value,
                reminder_message: document.getElementById('trackMessage').value,
                link: document.getElementById('trackLink').value,
                internal_notes: document.getElementById('trackInternal').value,
                revision_notes: document.getElementById('trackRevision').value,
            };

            if(!b.production_tracks) b.production_tracks = [];
            
            if(editId) {
                const idx = b.production_tracks.findIndex(x => x.id == editId);
                if(idx !== -1) {
                    const oldTrack = b.production_tracks[idx];
                    if (oldTrack.reminder_date === track.reminder_date && oldTrack.reminder_message === track.reminder_message) {
                        track.reminder_sent = oldTrack.reminder_sent;
                    }
                    b.production_tracks[idx] = track;
                }
            } else {
                b.production_tracks.push(track);
            }
            
            closeTrackModal();
            renderProjects();
            
            try {
                await fetch('/api/bookings', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        id: b.id,
                        production_tracks: b.production_tracks
                    })
                });
                showToast("Task produksi tersimpan.");
                if(window.logSysActivity) {
                    window.logSysActivity('Handler', '{{ auth()->check() ? auth()->user()->business_name : "Sistem" }}', 'Menambah track produksi', 'Kategori: ' + track.kategori, 'fa-list-check', 'info');
                }
            } catch(e) {
                console.error(e);
                alert("Gagal menyimpan task");
            }
        }

        async function deleteTrack(bookingId, trackId) {
            if(!confirm('Hapus task ini?')) return;
            const b = globalBookings.find(x => x.id == bookingId);
            if(!b) return;
            
            b.production_tracks = (b.production_tracks || []).filter(t => t.id !== trackId);
            renderProjects();
            
            try {
                await fetch('/api/bookings', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        id: b.id,
                        production_tracks: b.production_tracks
                    })
                });
                showToast("Task produksi dihapus.");
            } catch(e) {
                console.error(e);
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            loadProjects();
        });
    </script>
    <script src="/js/activity-logger.js"></script>
</body>
</html>
