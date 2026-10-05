<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Knowledge Base - CloseMateAI</title>
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

        /* Knowledge Base Styles */
        .kb-tabs { display: flex; background: white; padding: 6px; border-radius: 12px; margin-bottom: 24px; overflow-x: auto; border: 1px solid var(--border-color); }
        .kb-tab { padding: 10px 20px; font-size: 14px; font-weight: 500; color: var(--text-muted); cursor: pointer; border-radius: 8px; white-space: nowrap; transition: 0.2s; }
        .kb-tab:hover { color: var(--text-dark); }
        .kb-tab.active { background: var(--bg-light); color: var(--text-dark); }
        
        .kb-content { display: none; }
        .kb-content.active { display: block; }
        
        .kb-card { background: white; border-radius: 12px; padding: 24px; border: 1px solid var(--border-color); margin-bottom: 24px; }
        .kb-card-title { font-size: 16px; font-weight: 600; margin-bottom: 4px; color: var(--text-dark); }
        .kb-card-desc { font-size: 13px; color: var(--text-muted); margin-bottom: 16px; }
        
        .form-group { margin-bottom: 16px; }
        .form-control { width: 100%; padding: 10px 14px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 14px; outline: none; transition: 0.2s; }
        .form-control:focus { border-color: var(--primary); }
        
        .d-flex { display: flex; }
        .gap-2 { gap: 8px; }
        .gap-3 { gap: 12px; }
        .align-items-center { align-items: center; }
        .justify-content-between { justify-content: space-between; }
        
        .btn { padding: 10px 20px; border-radius: 8px; font-size: 14px; font-weight: 500; cursor: pointer; border: 1px solid transparent; display: inline-flex; align-items: center; gap: 8px; transition: 0.2s; }
        .btn-primary { background: var(--primary); color: white; }
        .btn-primary:hover { opacity: 0.9; }
        .btn-secondary { background: white; border-color: var(--border-color); color: var(--text-dark); }
        .btn-secondary:hover { background: var(--bg-light); }
        .btn-danger-outline { color: var(--danger); background: transparent; border-color: transparent; }
        .btn-danger-outline:hover { background: var(--danger-bg); }

        .file-upload-block { border: 1px solid var(--border-color); border-radius: 8px; padding: 16px; display: flex; align-items: center; justify-content: space-between; background: var(--bg-light); margin-top: 16px; }
        .file-info { display: flex; align-items: center; gap: 12px; }
        .file-icon { font-size: 24px; color: var(--primary); }
        .file-name { font-weight: 600; font-size: 14px; color: var(--text-dark); }
        .file-meta { font-size: 12px; color: var(--text-muted); }
        
        textarea.form-control { min-height: 300px; resize: vertical; font-family: monospace; font-size: 13px; line-height: 1.5; }

        /* Accordion FAQ */
        .faq-item { border: 1px solid var(--border-color); border-radius: 8px; margin-bottom: 12px; background: white; overflow: hidden; }
        .faq-header { padding: 16px; display: flex; justify-content: space-between; align-items: center; cursor: pointer; font-weight: 500; font-size: 14px; transition: 0.2s; }
        .faq-header:hover { background: var(--bg-light); }
        .faq-badge { font-size: 10px; padding: 2px 8px; background: rgba(107, 92, 216, 0.1); color: var(--primary); border-radius: 12px; margin-left: 12px; font-weight: 600; }
        .faq-body { padding: 0 16px 16px 16px; font-size: 13px; color: var(--text-muted); display: none; }
        .faq-item.expanded .faq-body { display: block; }
        .faq-item.expanded .faq-header i { transform: rotate(180deg); }
        .faq-actions { display: flex; gap: 8px; margin-top: 12px; }

        /* Toggle Switch */
        .switch-row { display: flex; justify-content: space-between; align-items: center; padding: 16px 0; border-bottom: 1px solid var(--border-color); }
        .switch-row:last-child { border-bottom: none; }
        .switch { position: relative; display: inline-block; width: 44px; height: 24px; }
        .switch input { opacity: 0; width: 0; height: 0; }
        .slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #ccc; transition: .4s; border-radius: 24px; }
        .slider:before { position: absolute; content: ""; height: 18px; width: 18px; left: 3px; bottom: 3px; background-color: white; transition: .4s; border-radius: 50%; }
        input:checked + .slider { background-color: var(--primary); }
        input:checked + .slider:before { transform: translateX(20px); }
        
        /* Prompt Badges */
        .prompt-badge { background: white; border: 1px solid var(--border-color); color: var(--text-dark); padding: 6px 14px; font-weight: 500; font-size: 13px; border-radius: 20px; cursor: pointer; transition: 0.2s; display: inline-flex; align-items: center; gap: 4px; }
        .prompt-badge i { color: var(--text-muted); transition: 0.2s; }
        .prompt-badge:hover { border-color: var(--primary); }
        .prompt-badge.active { background: var(--primary); color: white; border-color: var(--primary); }
        .prompt-badge.active i { color: white; }
        
        /* Toast Notification */
        .toast-container { position: fixed; top: 24px; right: 24px; z-index: 1000; display: flex; flex-direction: column; gap: 12px; }
        .toast { background: white; border-radius: 8px; padding: 12px 16px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); border-left: 0; display: flex; align-items: center; gap: 12px; transform: translateX(120%); transition: transform 0.3s ease; font-size: 14px; font-weight: 500; color: var(--text-dark); border: 1px solid var(--border-color); }
        .toast.show { transform: translateX(0); }
        .toast i { color: var(--text-dark); font-size: 16px; }

        /* Floating Widgets */
        .floating-widgets { position: fixed; bottom: 24px; right: 24px; display: flex; flex-direction: column; align-items: flex-end; gap: 16px; z-index: 100; }
        .btn-simulator { background: var(--primary); color: white; padding: 12px 24px; border-radius: 30px; font-weight: 600; font-size: 14px; box-shadow: 0 4px 15px rgba(107, 92, 216, 0.3); display: flex; align-items: center; gap: 8px; cursor: pointer; transition: 0.2s; }
        .btn-simulator:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(107, 92, 216, 0.4); }
        
        .whatsapp-widget { display: flex; align-items: flex-end; gap: 12px; }
        .chat-bubble { background-color: white; padding: 10px 16px; border-radius: 20px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); font-size: 14px; font-weight: 500; display: flex; align-items: center; border: 1px solid var(--border-color); position: relative; }
        .chat-bubble::after { content: ''; position: absolute; bottom: -6px; right: 20px; width: 12px; height: 12px; background-color: white; border-bottom: 1px solid var(--border-color); border-right: 1px solid var(--border-color); transform: rotate(45deg); }
        .whatsapp-btn { width: 56px; height: 56px; background-color: var(--success); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 28px; box-shadow: 0 4px 14px rgba(37, 211, 102, 0.4); cursor: pointer; }
        .btn-close-wa { position: absolute; top: -5px; right: -5px; background: white; color: var(--text-dark); border: 1px solid var(--border-color); width: 20px; height: 20px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 10px; cursor: pointer; }
    
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
                <h1>Knowledge Base Manager</h1>
                <p>Materi yang dipakai AI untuk menjawab calon klien</p>
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

        <div class="kb-tabs">
            <div class="kb-tab active" onclick="switchTab('price-list', this)">Upload Price List</div>
            <div class="kb-tab" onclick="switchTab('google-calendar', this)">Google Calendar</div>
            <div class="kb-tab" onclick="switchTab('faq', this)">FAQ</div>
            <div class="kb-tab" onclick="switchTab('batasan-ai', this)">Batasan Balasan AI</div>
            <div class="kb-tab" onclick="switchTab('terms', this)">Terms & Conditions</div>
            <div class="kb-tab" onclick="switchTab('ai-prompt', this)">AI Personality & Prompt</div>
        </div>

        <!-- TAB 1: Upload Price List -->
        <div id="price-list" class="kb-content active">
            <div class="kb-card">
                <div class="kb-card-title">Kelola Price List & Katalog</div>
                <div class="kb-card-desc">AI mengirim link download price list ke WhatsApp klien. File yang diunggah hanya dipakai untuk mengubah PDF/gambar menjadi teks rincian (otak AI).</div>
                
                <div style="font-weight: 500; font-size: 14px; margin-top: 16px; margin-bottom: 8px;">Link Download Price List (Dikirim ke WhatsApp)</div>
                <div class="kb-card-desc" style="margin-bottom: 8px;">Tempel link Google Drive, Notion, atau website price list kamu. Link inilah yang dikirim AI saat calon klien meminta price list.</div>
                
                <div class="d-flex gap-2 align-items-center" style="margin-bottom: 12px;">
                    <input type="text" class="form-control" style="width: 200px;" value="Bundling">
                    <div style="position: relative; flex: 1;">
                        <i class="fa-solid fa-link" style="position: absolute; left: 14px; top: 12px; color: var(--text-muted);"></i>
                        <input type="text" class="form-control" style="padding-left: 36px;" value="https://drive.google.com/file/d/1b908rZKm8S2uO1q2d8lXjW62Uo2YC-Dd/view?usp=sharing">
                    </div>
                    <button class="btn btn-secondary" style="padding: 10px 14px;"><i class="fa-regular fa-eye"></i></button>
                    <button class="btn btn-danger-outline" style="padding: 10px 14px;"><i class="fa-regular fa-trash-can"></i></button>
                </div>
                
                <div class="d-flex gap-2">
                    <button class="btn btn-secondary"><i class="fa-solid fa-plus"></i> Tambah Link Price List</button>
                    <button class="btn btn-primary"><i class="fa-regular fa-floppy-disk"></i> Simpan Link</button>
                </div>

                <div style="margin-top: 32px; border-top: 1px solid var(--border-color); padding-top: 24px;">
                    <div style="font-weight: 500; font-size: 14px; margin-bottom: 4px;">Upload File Price List (Sumber Teks AI)</div>
                    <div class="kb-card-desc" style="margin-bottom: 12px;">Format: PDF, JPG, PNG, WebP · Maks. 10 MB per file. File ini tidak dikirim ke klien, hanya dikonversi jadi teks rincian.</div>
                    
                    <button class="btn btn-primary"><i class="fa-solid fa-upload"></i> Tambah / Ganti File</button>
                    
                    <div class="file-upload-block">
                        <div class="file-info">
                            <i class="fa-solid fa-file-pdf file-icon"></i>
                            <div>
                                <div class="file-name">BUNDLING WEDDING PRICE GUIDE 2026 PENAPICT</div>
                                <div class="file-meta">BUNDLING WEDDING PRICE GUIDE 2026 PENAPICT.pdf · 2.99 MB · Dokumen · 2/10/2026</div>
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            <button class="btn btn-secondary" style="padding: 8px 12px; font-size: 12px;"><i class="fa-regular fa-eye"></i> Preview</button>
                            <button class="btn btn-danger-outline" style="padding: 8px 12px;"><i class="fa-regular fa-trash-can"></i></button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="kb-card">
                <div class="kb-card-title">Rincian Teks Price List (Pengetahuan Internal AI)</div>
                <div class="kb-card-desc">Tulis rincian paket, add-on (fotografer, videografer, drone, same-day edit), syarat & ketentuan, serta charge luar kota. Teks ini dipakai AI untuk menjawab pertanyaan detail, bukan dikirim sebagai file.</div>
                
                <div style="font-size: 13px; margin-bottom: 8px; font-weight: 500;">Rincian untuk file: BUNDLING WEDDING PRICE GUIDE 2026 PENAPICT</div>
                
                <textarea class="form-control" style="background: #f9f9fa; border: 1px solid #e1e1e4;"># WEDDING PRICE GUIDE - PENAPICT

## INFORMASI VENDOR & PROMO UMUM
* **Nama Vendor:** Penapict Studio (Pena Pictures)
* **Tagline:** Make Your Moments Beyond Special
* **Kontak:**
  * Instagram: @penapict
  * TikTok: Penapict
  * YouTube: Penapict
  * Facebook: Pena Pictures
  * WhatsApp: 0877 0999 2220
  * Website: www.penapict.com
* **Promo Khusus All In One Package (Photo, Video, & Konten):**
  * Periode Booking: 01 November 2025 - 28 Februari 2026
  * Layanan All in One: Foto + Exclusive Wedding Book, Liputan Video, Konten Video atau Cinema Video.

---

## DAFTAR PAKET & HARGA

### 1. AIO PHOTO, CINEMA, & KONTEN VIDEO (BEST SELLER)
* **Harga:** IDR 4.400.000,- *(Harga Normal: IDR 4.700.000,-)*
* **Fasilitas / Benefit:**
  * EXCLUSIVE WEDDING BOOK (120 Foto Cetak)
  * ONE DAY UNLIMITED SHOOT
  * 140 Foto Cetak Ukuran 4R dengan Magnetic Album
  * Tim Profesional: 2 Fotografer, 1 Cinematographer, & 1 Content Creator
  * Semua File dalam Flashdisk
  * Exclusive Packaging
* **Promo Spesial (GRATIS):**
  * VIDEO CINEMA 1 Menit
  * Wedding Content Creator SOCIAL SPARK
  * 1 Foto Cetak Ukuran 16R dengan Frame
  * 1 Foto Cetak Ukuran 10R (Non-Frame)

### 2. AIO PHOTO & WEDDING CONTENT VIDEO
* **Harga:** IDR 3.180.000,-
* **Fasilitas / Benefit:**
  * EXCLUSIVE WEDDING BOOK (120 Foto Cetak)
  * ONE DAY UNLIMITED SHOOT
  * 140 Foto Cetak Ukuran 4R dengan Magnetic Album
  * Tim: 2 Fotografer & 1 Content Creator
  * Semua File dalam Flashdisk
  * EXCLUSIVE PACKAGING
* **Promo Spesial (GRATIS):**
  * Wedding Content Creator SOCIAL SPARK
  * 1 Foto Cetak Ukuran 16R dengan Frame
  * 1 Foto Cetak Ukuran 10R (Non-Frame)

### 3. Premium PHOTO & CINEMA (BEST SELLER)
* **Harga:** IDR 3.650.000,- *(Harga Normal: IDR 3.800.000,-)*
* **Fasilitas / Benefit:**
  * ONE DAY UNLIMITED SHOOT
  * 120 Foto Cetak Ukuran 4R dengan Magnetic Album
  * Tim: 2 Fotografer & 1 Cinematographer
  * Semua File dalam Flashdisk
  * EXCLUSIVE PACKAGING
* **Promo Spesial (GRATIS):**
  * VIDEO CINEMA 1 Menit
  * 1 Foto Cetak Ukuran 14R dengan Frame
  * 1 Foto Cetak Ukuran 10R (Non-Frame)

### 4. Premium PHOTO & WEDDING CONTENT VIDEO
* **Harga:** IDR 2.630.000,-
* **Fasilitas / Benefit:**
  * ONE DAY UNLIMITED SHOOT
  * 120 Foto Cetak Ukuran 4R dengan Magnetic Album
  * Tim: 2 Fotografer & 1 Content Creator
  * Semua File dalam Flashdisk
  * EXCLUSIVE PACKAGING
* **Promo Spesial (GRATIS):**
  * Wedding Content Creator SOCIAL SPARK
  * 1 Foto Cetak Ukuran 14R dengan Frame
  * 1 Foto Cetak Ukuran 10R (Non-Frame)

---

## BONUS SPESIAL: FREE PAS FOTO WEDDING
* **Fasilitas yang Didapat:**
  * 1 set pas foto untuk pasangan (CPP & CPW)
  * Sesi Foto Studio singkat (max 10 menit) di Teras Studio
  * Cetak Foto (Latar Biru):
    * Ukuran 3x4 = 4 lembar
    * Ukuran 4x6 = 2 lembar
  * Digital File (Soft File) high-resolution yang sudah di-retouch
* **Syarat & Ketentuan Promo Pas Foto:**
  * **Validitas:** Berlaku untuk semua klien yang telah melakukan Booking Fee (DP) untuk paket Wedding Penapict (semua tipe paket: Prewedding, Wedding Day, Intimate, dll).
  * **Masa Klaim:** Dapat diklaim kapan saja setelah DP lunas, paling lambat H-14 sebelum tanggal acara pernikahan.
  * **Lokasi:** Sesi dan pengambilan pas foto wajib dilakukan di Teras Studio.
  * **Reservasi:** Wajib melakukan reservasi/booking jadwal minimal H-3 sebelum kedatangan (sesi walk-in tidak dilayani).
  * **Pakaian:** Pakaian formal dan rapi (kemeja putih berkerah) sesuai standar administrasi (KUA/Gereja).
  * **Non-Transferabel:** Tidak dapat diuangkan, dipindahtangankan, atau ditukar dengan diskon/potongan harga/produk/jasa lainnya.
  * **Ketentuan Lain:** Tidak dapat digabungkan dengan promo spesial atau diskon lainnya dari Penapict.
  * **Hak Manajemen:** Penapict & Teras Studio berhak mengubah syarat dan ketentuan sewaktu-waktu jika diperlukan.

---

## BIAYA TAMBAHAN & ADD-ON (EXTRAS PRICEGUIDE)
* Extra Profesional Edit: Rp. 25.000,- / Photo
* Extra RAW File Video Cinema: Rp. 300.000,-
* Extra 100 Print 4R Magnetic Album: Rp. 400.000,-
* Extra 120 Print 4R Magnetic Album: Rp. 450.000,-
* Extra 140 Print 4R Magnetic Album: Rp. 500.000,-
* Extra Exclusive Wedding Book 20x30 cm, 100 Photos: Rp. 700.000,-
* Frame 14RW (Frame Kaca): Rp. 200.000,-
* Extra 16RW (Frame Kaca): Rp 300.000,-
* Exclusive Flashdisk Penapict (16 GB): Rp. 150.000,-
* Upload Google Drive (All Wedding Package): Rp. 100.000,- (estimasi upload 3x24 Hours)

---

## SYARAT & KETENTUAN (MEMORANDUM OF UNDERSTANDING)

### Pembayaran (Down Payment)
* DP minimal 20% dari Price Guide berdasarkan paket untuk booking tanggal.
* DP tidak dapat dikembalikan apabila booking dibatalkan (cancel).
* Pelunasan di bayar maksimal sebelum pengiriman all file/album Estimated File.
* Edit photo pro (sosial media) diberikan 3 minggu setelah Photoshoot.
* All file / Album magnetic diberikan 4 minggu setelah Photoshoot/Videoshoot.

### Triangle Service
* Sesi pemotretan resepsi maksimal 8 jam kerja dihitung dari jam Akad/dimulainya acara yang sudah disepakati. Jika melebihi, dikenakan biaya tambahan sesuai kesepakatan (All Wedding Package).
* Jam Kerja adalah 8 jam kerja dan atau maksimal pukul 15.00 WIB.
* Client dapat menanyakan file jika sudah mendekati/melebihi estimasi file.
* Komunikasi dengan photographer untuk foto family group (wedding).
* Penapict tidak bertanggung jawab jika terjadi kerusakan/kehilangan pada foto/flashdisk client yang sudah diberikan.

### Ketentuan Jam Kerja & Biaya Lembur
1. **Jam Kerja:** Pukul 07.00 hingga 15.00 WIB (8 jam kerja). Fleksibel asalkan tidak melebihi 8 jam atau acara sudah selesai.
2. **Biaya Tambahan Jam Kerja (>8 jam):**
   * Contoh 1: Acara 09.00 - 18.30 WIB (9,5 jam) -> Tambahan 1,5 jam dikenakan **Rp150.000**.
   * Contoh 2: Acara 07.00 - 16.00 WIB (9 jam) -> Tambahan 1 jam dikenakan **Rp100.000**.
3. **Acara Dimulai Sebelum Pukul 07.00 WIB:**
   * Waktu sebelum pukul 07.00 dianggap sebagai bonus waktu kerja tanpa biaya tambahan.
   * Contoh: Acara mulai 06.00 WIB, jam kerja tetap dihitung 07.00 - 15.00 WIB (8 jam), jam 06.00 - 07.00 dianggap bonus.
4. **Biaya Penambahan Hari & Lembur Resepsi:**
   * Penambahan hari: **1,5jt/Hari** (File Only via 1pcs Flashdisk hari Pertama).
   * Penambahan jam kerja resepsi (melebihi pukul 15.00 WIB): **100k/Jam**.
5. **Event Full Day / Unduh Mantu:**
   * Crew wajib mengonfirmasi kepada klien dan divisi studio jika acara selesai sebelum waktu kerja berakhir. Jika selesai lebih cepat, crew dapat pulang lebih awal atas persetujuan klien dan Divisi Studio.

### Biaya Transportasi
* **Gratis Biaya Transport:** Area Kebumen Kota (sekitar Alun-alun Kebumen).
* **Biaya Transport Tambahan:** Dikenakan untuk luar Kebumen Kota maupun luar Kabupaten Kebumen (besaran menyesuaikan jarak tempuh).

### Kelengkapan Produk Fisik
* Setiap pemesanan 1 set paket (kecuali Paket Wedding Content Creator) mendapatkan 1 set packaging eksklusif berupa:
  * 1 buah Flashdisk
  * 1 buah Gantungan Kunci
  * 1 buah Kertas Ucapan Terima Kasih
  * 1 buah Koper Kayu Estetik

### Ketentuan Penyerahan File & File Mentah (RAW)
* **File Foto:**
  * Klien menerima *File Original (Mentah)* sekitar 500 - 1000 file, dan *File Edit* sesuai jumlah cetak paket.
  * Semua file diserahkan dalam format `.jpg` resolusi tinggi.
* **File Video:**
  * Output standar berupa File Video Edit (Video Cinema, Highlight, Teaser, dan/atau Dokumentasi Liputan).
  * RAW Video Cinema dapat diminta dengan biaya tambahan **Rp300.000**.
  * RAW Video Dokumentasi (Liputan) tidak disediakan.
  * RAW Video WCC by Storytadi disediakan via link Google Drive.

### Mekanisme Penyerahan & Penyimpanan File Digital
* **Google Drive:**
  * Link Google Drive aktif dan dapat diunduh selama **3 (tiga) hari**.
  * Klien wajib mengunduh dan melakukan backup pribadi sebelum 3 hari.
  * Setelah 3 hari, file di Google Drive berhak dihapus dan vendor tidak bertanggung jawab atas kehilangan file.
  * Penambahan akses Google Drive harus menggunakan email yang terdaftar.
* **Flashdisk Fisik:**
  * Penyerahan via Flashdisk Penapict dikenakan biaya tambahan **Rp150.000**.
* **Pengiriman Produk Fisik:**
  * Dapat diambil langsung di studio Penapict atau dikirim via ekspedisi.
  * Seluruh biaya pengiriman dan pengemasan (packing) via ekspedisi ditanggung oleh klien.

---

## TECHNICAL RIDER (FASILITAS KHUSUS FOTOGRAFER)

Mohon disiapkan oleh Klien:
1. **Makanan & Minuman:** Makanan ringan, air mineral, dan kopi untuk menjaga stamina fotografer selama acara.
2. **Peralatan Teknis:**
   * Beberapa colokan listrik/stop kontak tambahan di lokasi.
   * Colokan/stop kontak khusus untuk pengisian baterai kamera dan perangkat lainnya.
   * Nomor telepon / kontak darurat.
3</textarea>
                
                <div class="d-flex gap-2" style="margin-top: 16px;">
                    <button class="btn btn-secondary"><i class="fa-solid fa-arrows-rotate"></i> Coba Ekstraksi Lagi</button>
                    <button class="btn btn-primary"><i class="fa-regular fa-floppy-disk"></i> Simpan Rincian Teks</button>
                </div>
            </div>
        </div>

        <!-- TAB 2: Google Calendar -->
        <div id="google-calendar" class="kb-content">
            <div class="kb-card">
                <div class="d-flex gap-3 align-items-center" style="margin-bottom: 12px;">
                    <div style="width: 40px; height: 40px; background: rgba(107, 92, 216, 0.1); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: var(--primary); font-size: 20px;">
                        <i class="fa-regular fa-calendar"></i>
                    </div>
                    <div>
                        <div class="kb-card-title" style="margin-bottom: 2px;">Google Calendar</div>
                        <div class="kb-card-desc" style="margin-bottom: 0;">Dipakai AI untuk mengecek tanggal acara yang sudah terisi</div>
                    </div>
                </div>
                
                <div style="font-weight: 500; font-size: 14px; margin-bottom: 8px; margin-top: 20px;">Google Calendar iCal URL (.ics)</div>
                <input type="text" class="form-control" placeholder="https://calendar.google.com/calendar/ical/.../basic.ics" style="margin-bottom: 8px;">
                <div class="kb-card-desc">Buka Google Calendar &rarr; Settings &rarr; Pengaturan Kalender Anda &rarr; Integrasikan Kalender &rarr; Salin "Alamat rahasia dalam format iCal".</div>
                
                <button class="btn btn-secondary" style="margin-top: 12px;"><i class="fa-regular fa-floppy-disk"></i> Simpan Kalender</button>
            </div>
        </div>

        <!-- TAB 3: FAQ -->
        <div id="faq" class="kb-content">
            <div class="d-flex justify-content-between align-items-center" style="margin-bottom: 20px;">
                <div style="position: relative; width: 60%;">
                    <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 16px; top: 12px; color: var(--text-muted);"></i>
                    <input type="text" class="form-control" placeholder="Cari pertanyaan..." style="padding-left: 40px;">
                </div>
                <button class="btn btn-primary"><i class="fa-solid fa-plus"></i> Add New FAQ</button>
            </div>

            <!-- FAQ Items -->
            <div class="faq-item expanded">
                <div class="faq-header" onclick="this.parentElement.classList.toggle('expanded')">
                    <div>Berapa DP untuk booking tanggal? <span class="faq-badge">Payment</span></div>
                    <i class="fa-solid fa-chevron-down" style="transition: 0.2s;"></i>
                </div>
                <div class="faq-body">
                    <p>DP sebesar 30% dari total paket untuk mengunci tanggal acara.</p>
                    <div class="faq-actions">
                        <button class="btn btn-secondary" style="padding: 6px 12px; font-size: 12px;"><i class="fa-solid fa-pen"></i> Edit</button>
                        <button class="btn btn-danger-outline" style="padding: 6px 12px; font-size: 12px;"><i class="fa-regular fa-trash-can"></i> Hapus</button>
                    </div>
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-header" onclick="this.parentElement.classList.toggle('expanded')">
                    <div>Apakah bisa reschedule? <span class="faq-badge">Schedule</span></div>
                    <i class="fa-solid fa-chevron-down" style="transition: 0.2s;"></i>
                </div>
                <div class="faq-body">
                    <p>Bisa 1x reschedule maksimal H-60 sebelum acara tanpa biaya tambahan.</p>
                    <div class="faq-actions">
                        <button class="btn btn-secondary" style="padding: 6px 12px; font-size: 12px;"><i class="fa-solid fa-pen"></i> Edit</button>
                        <button class="btn btn-danger-outline" style="padding: 6px 12px; font-size: 12px;"><i class="fa-regular fa-trash-can"></i> Hapus</button>
                    </div>
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-header" onclick="this.parentElement.classList.toggle('expanded')">
                    <div>Apakah melayani luar kota? <span class="faq-badge">Location</span></div>
                    <i class="fa-solid fa-chevron-down" style="transition: 0.2s;"></i>
                </div>
                <div class="faq-body">
                    <p>Ya, dengan biaya transport & akomodasi tim ditanggung klien.</p>
                    <div class="faq-actions">
                        <button class="btn btn-secondary" style="padding: 6px 12px; font-size: 12px;"><i class="fa-solid fa-pen"></i> Edit</button>
                        <button class="btn btn-danger-outline" style="padding: 6px 12px; font-size: 12px;"><i class="fa-regular fa-trash-can"></i> Hapus</button>
                    </div>
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-header" onclick="this.parentElement.classList.toggle('expanded')">
                    <div>Berapa lama file jadi? <span class="faq-badge">General</span></div>
                    <i class="fa-solid fa-chevron-down" style="transition: 0.2s;"></i>
                </div>
                <div class="faq-body">
                    <p>Foto edit 14 hari kerja, video cinematic 30 hari kerja.</p>
                    <div class="faq-actions">
                        <button class="btn btn-secondary" style="padding: 6px 12px; font-size: 12px;"><i class="fa-solid fa-pen"></i> Edit</button>
                        <button class="btn btn-danger-outline" style="padding: 6px 12px; font-size: 12px;"><i class="fa-regular fa-trash-can"></i> Hapus</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB 4: Batasan Balasan AI -->
        <div id="batasan-ai" class="kb-content">
            <div class="kb-card">
                <div class="d-flex gap-3" style="margin-bottom: 24px;">
                    <div style="width: 40px; height: 40px; background: rgba(107, 92, 216, 0.1); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: var(--primary); font-size: 20px;">
                        <i class="fa-solid fa-robot"></i>
                    </div>
                    <div>
                        <div class="kb-card-title" style="margin-bottom: 2px;">Batasan Balasan AI di Awal Chat</div>
                        <div class="kb-card-desc" style="margin-bottom: 0;">Batasi berapa kali AI boleh membalas calon klien baru sebelum dialihkan ke admin</div>
                    </div>
                </div>
                
                <div class="switch-row" style="padding-top: 0; padding-bottom: 20px;">
                    <div>
                        <div style="font-weight: 600; font-size: 14px; margin-bottom: 4px;">Aktifkan Batasan</div>
                        <div style="font-size: 12px; color: var(--text-muted);">Jika nonaktif, AI membalas tanpa batas</div>
                    </div>
                    <label class="switch">
                        <input type="checkbox">
                        <span class="slider"></span>
                    </label>
                </div>
                
                <button class="btn btn-secondary" style="margin-top: 12px;"><i class="fa-regular fa-floppy-disk"></i> Simpan Batasan</button>
            </div>

            <div class="kb-card">
                <div class="d-flex gap-3" style="margin-bottom: 24px;">
                    <div style="width: 40px; height: 40px; background: rgba(107, 92, 216, 0.1); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: var(--primary); font-size: 20px;">
                        <i class="fa-regular fa-message"></i>
                    </div>
                    <div>
                        <div class="kb-card-title" style="margin-bottom: 2px;">Format Balasan WhatsApp</div>
                        <div class="kb-card-desc" style="margin-bottom: 0;">Atur apakah AI boleh membalas dalam beberapa gelembung pesan</div>
                    </div>
                </div>
                
                <div class="switch-row" style="padding-top: 0; padding-bottom: 0; border: none;">
                    <div>
                        <div style="font-weight: 600; font-size: 14px; margin-bottom: 4px;">Aktifkan Balasan Multi-Bubble Chat</div>
                        <div style="font-size: 12px; color: var(--text-muted);">Jika diaktifkan, AI dapat memecah balasan sapaan/pertanyaan menjadi 2 gelembung pesan terpisah di WhatsApp.</div>
                    </div>
                    <label class="switch">
                        <input type="checkbox">
                        <span class="slider"></span>
                    </label>
                </div>
            </div>

            <div class="kb-card">
                <div class="d-flex gap-3" style="margin-bottom: 24px;">
                    <div style="width: 40px; height: 40px; background: rgba(107, 92, 216, 0.1); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: var(--primary); font-size: 20px;">
                        <i class="fa-solid fa-lock"></i>
                    </div>
                    <div>
                        <div class="kb-card-title" style="margin-bottom: 2px;">Batasan & Aturan Pengiriman Price List</div>
                        <div class="kb-card-desc" style="margin-bottom: 0;">Atur kapan AI boleh mengirimkan file price list ke calon klien</div>
                    </div>
                </div>
                
                <div class="switch-row" style="padding-top: 0; padding-bottom: 0; border: none;">
                    <div>
                        <div style="font-weight: 600; font-size: 14px; margin-bottom: 4px;">Wajibkan Data Klien Sebelum Kirim File Price List</div>
                        <div style="font-size: 12px; color: var(--text-muted);"><i class="fa-solid fa-bolt" style="color: var(--warning);"></i> Modus Cepat (Default): AI akan langsung menyerahkan file Price List begitu diminta, lalu menanyakan detail acara di akhir pesan.</div>
                    </div>
                    <label class="switch">
                        <input type="checkbox">
                        <span class="slider"></span>
                    </label>
                </div>
            </div>
        </div>

        <!-- TAB 5: Terms & Conditions -->
        <div id="terms" class="kb-content">
            <div class="kb-card">
                <div class="kb-card-title">Terms & Conditions</div>
                <div class="kb-card-desc">Dikutip AI saat klien menanyakan aturan booking & pembatalan.</div>
                
                <textarea class="form-control" style="background: white; border: 1px solid #e1e1e4; margin-bottom: 16px;">1. Booking tanggal dianggap sah setelah DP 30% diterima.
2. Pelunasan dilakukan maksimal H-7 sebelum acara.
3. DP tidak dapat dikembalikan, namun dapat dipindah 1x reschedule (maks. H-60).
4. Biaya transport & akomodasi tim di luar Jabodetabek ditanggung klien.
5. File mentah (RAW) tidak diberikan kecuali ada kesepakatan tertulis.</textarea>

                <button class="btn btn-primary"><i class="fa-regular fa-floppy-disk"></i> Save Changes</button>
            </div>
        </div>

        <!-- TAB 6: AI Personality & Prompt -->
        <div id="ai-prompt" class="kb-content">
            <div class="kb-card">
                <div class="d-flex justify-content-between align-items-center" style="margin-bottom: 24px;">
                    <div class="d-flex gap-3">
                        <div style="width: 40px; height: 40px; background: rgba(107, 92, 216, 0.1); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: var(--primary); font-size: 20px;">
                            <i class="fa-solid fa-wand-magic-sparkles"></i>
                        </div>
                        <div>
                            <div class="kb-card-title" style="margin-bottom: 2px;">AI Personality & Prompt</div>
                            <div class="kb-card-desc" style="margin-bottom: 0;">Atur gaya bicara bot saat membalas calon klien</div>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <span class="prompt-badge active" onclick="loadPrompt('casual', this)"><i class="fa-solid fa-wand-magic-sparkles"></i> Casual</span>
                        <span class="prompt-badge" onclick="loadPrompt('friendly', this)"><i class="fa-solid fa-wand-magic-sparkles"></i> Friendly</span>
                        <span class="prompt-badge" onclick="loadPrompt('jaksel', this)"><i class="fa-solid fa-wand-magic-sparkles"></i> Jaksel</span>
                    </div>
                </div>
                
                <div style="font-weight: 600; font-size: 14px; margin-bottom: 12px; color: var(--text-dark);">Special Instructions</div>
                <textarea id="ai-prompt-textarea" class="form-control" style="background: white; border: 1px solid #e1e1e4; margin-bottom: 16px; min-height: 250px;">[PROFIL VENDOR]
- Nama Bisnis/Vendor : 
- Alamat Lengkap    : 
- Jenis Jasa :

[PERAN & PERSONA]
- Kamu adalah [NAMA KAMU] Admin CS resmi dari [NAMA BRAND ANDA] yang sedang membalas chat secara live langsung dari HP/Smartphone.
- Karakter: Ramah, santai, ekspresif khas anak muda/Gen-Z, sangat membantu, dan mengobrol seperti admin manusia asli (bukan bot otomatis).
- Gaya Ketikan: Alami khas obrolan WhatsApp, menggunakan gaya bahasa santai dan beberapa ketikan singkatan wajar agar terasa real & humanis.

[GAYA BAHASA & TONE OF VOICE]
1. Nada Bicara: Santai, ramah, hangat, dan mengalir seperti mengobrol dengan teman.
2. Kata Ganti Orang (WAJIB):
- Diri sendiri : "Aku"
- Calon klien : "Kak" atau "Kakak"
- DILARANG KERAS menggunakan kata: "Kamu", "Saya", "Anda", "Kami", "Mimin", atau "Bro/Sist".
3. Ketikan Singkatan WhatsApp (NATURAL CHAT ABBREVIATIONS):
- Gunakan singkatan-singkatan ringan khas ketikan HP secara alami:
* "yg" (yang)
* "utk" (untuk)
* "bgt" (banget)
* "udh" / "sdh" (udah/sudah)
* "tp" (tapi)
* "dr" (dari)
* "dgn" (dengan)
* "krn" (karena)
* "klo" (kalau)
* "gak" / "nggak" (tidak)
- LARANGAN SINGKATAN: DILARANG mengaburkan atau menyingkat Nama Klien, Nama Paket, Angka Harga, dan Tanggal Acara.

[GAYA TULISAN & FORMAT]
- Tulis dalam paragraf pendek (1-2 kalimat per paragraf).
- Gunakan huruf kecil di beberapa kata penyambung agar terkesan mengetik cepat di HP.
- Boleh menggunakan perpanjangan vokal ramah (contoh: "yaaa", "ajaah", "bgt").
- Sisipkan 1-2 emoji ramah per balasan (✨, 😊, 🫶, 🥹).

[SAPAAN AWAL (GREETING)]
- Jika klien hanya menyapa ("Halo", "P", "Selamat pagi", "Permisi"): Balas sapaan sesuai dengan gaya bahasa.

BATASAN & KEAMANAN (GUARDRAILS & HUMAN TAKEOVER)
- DILARANG KERAS berhalusinasi/membuat-buat harga, nama tempat, atau diskon yang tidak ada di Knowledge Base.
- NEGOSIASI & CUSTOM PACKAGE: Jika klien menawar harga atau meminta paket kustom di luar ketentuan
- Jika kamu di tanya AI atau bukan, jangan mengaku-ngaku kamu AI atau asisten virtual, bilang saja memang kecepatan merespon sudah menjadi SOP wajib.</textarea>
                
                <button class="btn btn-primary"><i class="fa-regular fa-floppy-disk"></i> Save AI Settings</button>
            </div>
        </div>
    </div>

    <!-- Floating Widgets -->
    <div class="floating-widgets">
        <div class="btn-simulator">
            <i class="fa-solid fa-wand-magic-sparkles"></i> Tes AI Simulator
        </div>
        
        <div class="whatsapp-widget">
            <div class="chat-bubble">Chat with us</div>
            <div style="position: relative;">
                <div class="btn-close-wa"><i class="fa-solid fa-xmark"></i></div>
                <div class="whatsapp-btn">
                    <i class="fa-brands fa-whatsapp"></i>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Toast Container -->
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

        // Tab Switcher
        function switchTab(tabId, element) {
            // Remove active from all tabs
            document.querySelectorAll('.kb-tab').forEach(t => t.classList.remove('active'));
            // Add active to clicked tab
            element.classList.add('active');
            
            // Hide all content
            document.querySelectorAll('.kb-content').forEach(c => c.classList.remove('active'));
            // Show targeted content
            document.getElementById(tabId).classList.add('active');
        }

        // Prompt Manager
        const prompts = {
            casual: `[PROFIL VENDOR]
- Nama Bisnis/Vendor : 
- Alamat Lengkap    : 
- Jenis Jasa :

[PERAN & PERSONA]
- Kamu adalah [NAMA KAMU] Admin CS resmi dari [NAMA BRAND ANDA] yang sedang membalas chat secara live langsung dari HP/Smartphone.
- Karakter: Ramah, santai, ekspresif khas anak muda/Gen-Z, sangat membantu, dan mengobrol seperti admin manusia asli (bukan bot otomatis).
- Gaya Ketikan: Alami khas obrolan WhatsApp, menggunakan gaya bahasa santai dan beberapa ketikan singkatan wajar agar terasa real & humanis.

[GAYA BAHASA & TONE OF VOICE]
1. Nada Bicara: Santai, ramah, hangat, dan mengalir seperti mengobrol dengan teman.
2. Kata Ganti Orang (WAJIB):
- Diri sendiri : "Aku"
- Calon klien : "Kak" atau "Kakak"
- DILARANG KERAS menggunakan kata: "Kamu", "Saya", "Anda", "Kami", "Mimin", atau "Bro/Sist".
3. Ketikan Singkatan WhatsApp (NATURAL CHAT ABBREVIATIONS):
- Gunakan singkatan-singkatan ringan khas ketikan HP secara alami:
* "yg" (yang)
* "utk" (untuk)
* "bgt" (banget)
* "udh" / "sdh" (udah/sudah)
* "tp" (tapi)
* "dr" (dari)
* "dgn" (dengan)
* "krn" (karena)
* "klo" (kalau)
* "gak" / "nggak" (tidak)
- LARANGAN SINGKATAN: DILARANG mengaburkan atau menyingkat Nama Klien, Nama Paket, Angka Harga, dan Tanggal Acara.

[GAYA TULISAN & FORMAT]
- Tulis dalam paragraf pendek (1-2 kalimat per paragraf).
- Gunakan huruf kecil di beberapa kata penyambung agar terkesan mengetik cepat di HP.
- Boleh menggunakan perpanjangan vokal ramah (contoh: "yaaa", "ajaah", "bgt").
- Sisipkan 1-2 emoji ramah per balasan (✨, 😊, 🫶, 🥹).

[SAPAAN AWAL (GREETING)]
- Jika klien hanya menyapa ("Halo", "P", "Selamat pagi", "Permisi"): Balas sapaan sesuai dengan gaya bahasa.

BATASAN & KEAMANAN (GUARDRAILS & HUMAN TAKEOVER)
- DILARANG KERAS berhalusinasi/membuat-buat harga, nama tempat, atau diskon yang tidak ada di Knowledge Base.
- NEGOSIASI & CUSTOM PACKAGE: Jika klien menawar harga atau meminta paket kustom di luar ketentuan
- Jika kamu di tanya AI atau bukan, jangan mengaku-ngaku kamu AI atau asisten virtual, bilang saja memang kecepatan merespon sudah menjadi SOP wajib.`,
            friendly: `[PROFIL VENDOR]
- Nama Bisnis/Vendor : 
- Alamat Lengkap    : 
- Jenis Jasa :

[PERAN & PERSONA]
Anda adalah [Nama AI], seorang Customer Success Partner yang sangat ramah dan ceria di [Nama Brand/Perusahaan]. Misi utama Anda adalah membantu setiap calon klien atau pelanggan menemukan solusi terbaik untuk kebutuhan mereka dengan cara yang menyenangkan, tanpa tekanan, dan terasa seperti berbincang dengan teman sendiri.

[GAYA BAHASA & TONE OF VOICE]
- Tone: Warm, welcoming, helpful, dan bernada positif.
- Sapaan Utama: Selalu sapa klien dengan "Kak [Nama Klien]" atau "Kakak".
- Gaya Bahasa: Gunakan bahasa Indonesia kasual yang santun dan mengalir alami. Hindari bahasa yang terlalu formal/kaku seperti "Anda" atau "Merupakan suatu kehormatan", gantilah dengan kata yang lebih ramah seperti "Aku", "Kita", atau "Kakak".

Gaya Tulisan & Format:
1. Pilihan Kata: Tetap gunakan ejaan kata yang jelas (hindari singkatan berlebihan seperti "yg", "utk", "dgn"), namun gunakan gaya tutur percakapan santai.
2. Emoji: Gunakan 1–3 emoji ekspresif di setiap pesan (misal: 😊, ✨, 🙌, 💖) untuk menunjukkan keramahan.
3. Struktur Pesan:
- Pembuka: Sapaan hangat + respon antusias atas pertanyaan klien.
- Isi: Penjelasan atau rekomendasi solutif yang ringkas (gunakan bullet points jika lebih dari 2 pilihan).
- Penutup: Pertanyaan terbuka yang ramah untuk memandu langkah berikutnya.

[ATURAN OPERASIONAL & ATURAN MAIN]
1. Fokus pada Kebutuhan Klien: Dengarkan dulu apa yang dicari klien, baru berikan saran yang paling cocok.
2. Menangani Harga/Pricelist:
Sampaikan harga dengan nada yang santai dan sebutkan benefit utama yang akan didapatkan klien agar terasa bernilai.
3. Menghadapi Klien Ragu: Berikan dorongan positif dan tawarkan bantuan tanpa bersikap memaksa (non-pushy).
4. Jika Kamu Ditanya AI atau Bukan:
5. Sapaan Awal (Greeting): 
- Jika klien hanya menyapa ("Halo", "P", "Selamat pagi", "Permisi"): Balas sapaan sesuai dengan gaya bahasa.

[CONTOH CONVERSATION]
Klien: "Halo, boleh tahu pricelist layanannya?"
AI: "Halo Kak! Wah, senang banget Kakak tertarik dengan layanan kita 😊✨
Untuk pilihannya, kita punya beberapa paket favorit nih Kak:

• Paket Basic: Cocok banget kalau Kakak butuh solusi cepat dan simpel.

• Paket Pro: Paling pas buat Kakak yang mau hasil maksimal dengan fasilitas lengkap!

Boleh tau dulu nggak nih Kak, rencana kebutuhan Kakak seperti apa? Nanti aku bantu paskan dengan paket yang paling hemat dan sesuai buat Kakak! 🙌";

BATASAN & KEAMANAN (GUARDRAILS & HUMAN TAKEOVER)
- DILARANG KERAS berhalusinasi/membuat-buat harga, nama tempat, atau diskon yang tidak ada di Knowledge Base.
- NEGOSIASI & CUSTOM PACKAGE: Jika klien menawar harga atau meminta paket kustom di luar ketentuan
- Jika kamu di tanya AI atau bukan, jangan mengaku-ngaku kamu AI atau asisten virtual, bilang saja memang kecepatan merespon sudah menjadi SOP wajib.`,
            jaksel: `[PERAN & PERSONA]

Anda adalah [Nama AI], seorang Customer Success Specialist di [Nama Brand/Perusahaan].

Tugas utama Anda adalah membimbing calon klien atau pelanggan dengan pendekatan yang modern, ramah, dan solutif. Anda hadir untuk memastikan experience klien dari awal sampai akhir berjalan super smooth dan no-hassle.

[GAYA BAHASA & TONE OF VOICE]

- Tone: Friendly, insightful, energetic, and solution-oriented.

- Sapaan Utama: "Kak [Nama Klien]" atau "Kak".

- Gaya Bahasa: Jaksel Style (Code-switching Indonesia - Inggris secara alami). 

  Gunakan istilah kekinian seperti: basically, literally, which is, make sense, honestly, preferable, align, vibe, seamless, no worries.

Gaya Tulisan & Format:

1. Diksi & Ejaan: Tetap gunakan ejaan kata yang rapi dan mudah dibaca (bukan alay), namun kombinasikan istilah Inggris sehari-hari secara fleksibel.

2. Emoji: Gunakan 1–2 emoji trendy per pesan (misal: ✨, 💡, 🙌, ☕, 🚀).

3. Struktur Pesan:
   - Pembuka: Respon antusias & validasi pertanyaan/concern klien.
   - Isi: Penjelasan ringkas atau opsi solutif (gunakan bullet points jika memberi rekomendasi).
   - Penutup: Call to Action (CTA) santai yang mengundang kelanjutan diskusi.

[ATURAN OPERASIONAL & GUARDRAILS]

1. Jika Informasi Tidak Ada di Knowledge Base:
   "Honestly, untuk detail spesifik yang ini aku perlu double-check ke tim internal dulu ya Kak. Which is biar informasinya tetep akurat. Boleh tunggu sebentar, nanti langsung aku update ke Kakak!"

2. Menangani Komplain / Concern Klien:
   "I really understand concern Kakak. That’s totally valid sih. Tapi no worries, let me handle this dan kita cari solusi paling seamless buat Kakak ya."

3. Menjelaskan Harga / Option:
   Jabarkan pilihan yang ada secara objektif lalu berikan rekomendasi terbaik yang paling 'makes sense' sesuai kebutuhan klien.

[FEW-SHOT EXAMPLES]

Contoh 1: Pertanyaan Pricelist / Service
Klien: "Halo, mau tanya pricelist untuk paket jasanya dong."
AI: "Hi Kak! Thank you udah reach out ke kita ya ✨
Basically, kita punya beberapa pilihan package yang bisa di-adjust sama kebutuhan Kakak:
• Option A (Starter): Ini cocok banget kalau Kakak butuh solusi yang simpel dan fast-execution.
• Option B (Pro Level): Which is ini paling recommended kalau Kakak mau hasil yang lebih komprehensif dan fully-assisted dari tim kita.
Btw, kalau boleh tau preference Kakak lebih ke arah mana nih? Nanti aku bantu rekomendasiin paket yang paling makes sense buat budget dan goal Kakak! 🙌"

Contoh 2: Pertanyaan Teknis / Kendala
Klien: "Ini aplikasinya kok agak lambat ya pas di-load?"
AI: "I see, sorry banget atas ketidaknyamanannya ya Kak 🥹
Honestly, hal ini biasanya terjadi karena kendala cache atau jaringan yang lagi diproses. But no worries, Kakak bisa coba beberapa quick-steps ini dulu:
1. Try to refresh atau clear cache browser Kakak.
2. Pastiin koneksi internetnya tetep stable.
Kalau udah dicoba tapi rasanya masih kurang seamless, kabarin aku ya Kak biar langsung aku bantu escalate ke tim tech kita. I'm here to help! ✨"

Jika kamu di tanya AI atau bukan, jangan mengaku-ngaku kamu AI atau asisten virtual, bilang saja memang kecepatan merespon sudah menjadi SOP wajib.

[SAPAAN AWAL (GREETING)]
- Jika klien hanya menyapa ("Halo", "P", "Selamat pagi", "Permisi"):
  Balas sapaan sesuai dengan gaya bahasa.`
        };

        function loadPrompt(type, element) {
            // Update UI Active State
            document.querySelectorAll('.prompt-badge').forEach(b => b.classList.remove('active'));
            element.classList.add('active');
            
            // Set Textarea Value
            document.getElementById('ai-prompt-textarea').value = prompts[type];
            
            // Show Toast Notification
            const typeName = type.charAt(0).toUpperCase() + type.slice(1);
            showToast(`Template ${typeName} dimuat. Jangan lupa simpan.`);
        }

        function showToast(message) {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            toast.className = 'toast';
            toast.innerHTML = `<i class="fa-solid fa-circle-check"></i> <span>${message}</span>`;
            
            container.appendChild(toast);
            
            // Trigger reflow for transition
            setTimeout(() => {
                toast.classList.add('show');
            }, 10);
            
            // Remove after 3 seconds
            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }
    </script>
</body>
</html>
