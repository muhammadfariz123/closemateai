<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Generator - CloseMateAI</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --sidebar-bg: #1e1e2d;
            --sidebar-text: #cbd5e1;
            --sidebar-active-bg: rgba(107, 92, 216, 0.2);
            --sidebar-active-text: #6b5cd8;
            --primary: #6b5cd8;
            --bg-light: #f8f9fa;
            --card-bg: #ffffff;
            --border-color: #e1e1e4;
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

        /* Main Content */
        .main-content { margin-left: 260px; flex: 1; padding: 30px; display: flex; flex-direction: column; }
        
        .topbar { display: flex; justify-content: space-between; align-items: center; margin: -30px -30px 30px -30px; padding: 16px 30px; background-color: white; border-bottom: 1px solid var(--border-color); }
        .page-title h1 { font-size: 20px; font-weight: 600; margin-bottom: 6px; }
        .page-title p { font-size: 14px; color: var(--text-muted); }
        
        .top-actions { display: flex; align-items: center; gap: 20px; }
        .status-pill { display: flex; align-items: center; gap: 8px; background-color: rgba(80, 205, 137, 0.1); color: var(--success); padding: 8px 16px; border-radius: 20px; font-size: 13px; font-weight: 600; border: 1px solid rgba(80, 205, 137, 0.2); }
        .status-dot { width: 8px; height: 8px; background-color: var(--success); border-radius: 50%; }
        
        .profile-wrapper { display: flex; align-items: center; gap: 12px; padding: 6px 16px 6px 6px; border-radius: 30px; cursor: pointer; }
        .avatar { width: 36px; height: 36px; background-color: var(--primary); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 14px; }
        .user-info h4 { font-size: 14px; font-weight: 600; }
        .user-info p { font-size: 12px; color: var(--text-muted); }

        /* Actions Row */
        .actions-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
        .btn { padding: 10px 20px; border-radius: 8px; font-size: 13px; font-weight: 500; cursor: pointer; border: 1px solid transparent; display: inline-flex; align-items: center; justify-content: center; gap: 8px; transition: 0.2s; }
        .btn-primary { background: var(--primary); color: white; }
        .btn-secondary { background: white; border-color: var(--border-color); color: var(--text-dark); }
        .btn-outline-primary { border: 1px dashed var(--primary); color: var(--primary); background: transparent; width: 100%; padding: 12px; }
        .btn-group { display: flex; background: white; border: 1px solid var(--border-color); border-radius: 20px; padding: 2px; }
        .btn-group .btn { padding: 6px 16px; border-radius: 18px; border: none; font-size: 12px; font-weight: 600; color: var(--text-muted); }
        .btn-group .btn.active { background: var(--primary); color: white; }

        /* Invoice Grid */
        .invoice-grid { display: grid; grid-template-columns: 1.1fr 1fr; gap: 24px; }
        
        /* Accordion Panel */
        .acc-panel { background: white; border-radius: 12px; border: 1px solid var(--border-color); margin-bottom: 16px; overflow: hidden; }
        .acc-header { padding: 16px 20px; display: flex; justify-content: space-between; align-items: center; cursor: pointer; font-weight: 600; font-size: 14px; color: var(--text-dark); }
        .acc-header i { color: var(--text-muted); transition: 0.3s; font-size: 12px; }
        .acc-body { padding: 0 20px 20px 20px; }
        
        .form-group { margin-bottom: 16px; }
        .form-label { display: block; font-size: 12px; color: var(--text-muted); margin-bottom: 6px; }
        .form-control { width: 100%; padding: 10px 14px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px; outline: none; font-family: inherit; }
        .form-control:focus { border-color: var(--primary); }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        
        .upload-box { border: 1px dashed var(--border-color); border-radius: 8px; padding: 12px; text-align: center; color: var(--text-dark); font-size: 13px; font-weight: 500; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; }
        .info-text { font-size: 11px; color: var(--text-muted); margin-bottom: 8px; }
        .info-text.bg { background: var(--bg-light); padding: 8px 12px; border-radius: 6px; border: 1px solid var(--border-color); margin-top: 12px; }
        
        .add-on-box { border: 1px solid var(--border-color); border-radius: 8px; padding: 16px; margin-bottom: 16px; position: relative; }
        .btn-trash { position: absolute; right: 16px; top: 16px; color: var(--text-muted); cursor: pointer; border: none; background: transparent; }
        .subtotal-text { text-align: right; font-size: 12px; color: var(--text-muted); margin-top: 8px; }
        .subtotal-text strong { color: var(--text-dark); }

        /* Draft WhatsApp Area */
        .draft-area { background: white; border-radius: 12px; border: 1px solid var(--border-color); padding: 20px; margin-top: 24px; }
        .draft-title { font-weight: 600; font-size: 14px; margin-bottom: 16px; display: flex; align-items: center; gap: 8px; }
        .tone-badges { display: flex; gap: 8px; margin-bottom: 12px; }
        .tone-badge { padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 500; cursor: pointer; border: 1px solid var(--border-color); color: var(--text-muted); }
        .tone-badge.active { background: var(--primary); color: white; border-color: var(--primary); }
        .draft-textarea { width: 100%; min-height: 200px; background: #f8f9fa; border: 1px solid var(--border-color); border-radius: 8px; padding: 16px; font-size: 13px; color: var(--text-dark); resize: vertical; outline: none; margin-bottom: 12px; }
        
        /* Right Panel */
        .section-title { font-size: 13px; font-weight: 600; color: var(--text-muted); margin-bottom: 12px; display: flex; align-items: center; gap: 8px; text-transform: uppercase; letter-spacing: 0.5px; }
        
        .design-options { display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 24px; }
        .design-card { background: white; border: 1px solid var(--border-color); border-radius: 8px; padding: 12px 16px; font-size: 12px; font-weight: 600; color: var(--text-dark); cursor: pointer; flex: 1; text-align: center; min-width: 140px; }
        .design-card.active { border: 2px solid var(--primary); }
        
        /* Invoice Paper */
        .invoice-paper { background: white; border-radius: 8px; padding: 40px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); margin-bottom: 24px; font-family: 'Inter', sans-serif; color: #333; }
        .inv-header { display: flex; justify-content: space-between; border-bottom: 2px solid #eee; padding-bottom: 20px; margin-bottom: 30px; }
        .inv-logo-text { font-size: 20px; font-weight: 700; letter-spacing: 1px; }
        .inv-meta { text-align: right; }
        .inv-meta h2 { font-size: 24px; font-weight: 300; letter-spacing: 2px; color: #666; margin-bottom: 10px; }
        .inv-meta p { font-size: 11px; color: #888; margin-bottom: 4px; }
        
        .inv-bill-to { margin-bottom: 30px; }
        .inv-bill-to p { font-size: 10px; color: #888; text-transform: uppercase; margin-bottom: 4px; }
        .inv-bill-to h4 { font-size: 14px; font-weight: 600; }
        
        .inv-desc-row { display: flex; justify-content: space-between; font-size: 12px; margin-bottom: 16px; }
        
        .inv-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .inv-table th { text-align: left; padding: 10px 0; border-bottom: 1px solid #ddd; font-size: 10px; color: #888; text-transform: uppercase; }
        .inv-table td { padding: 12px 0; border-bottom: 1px solid #eee; font-size: 12px; }
        .inv-table td.text-right, .inv-table th.text-right { text-align: right; }
        
        .inv-totals { display: flex; justify-content: flex-end; margin-bottom: 30px; }
        .inv-totals-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 8px 30px; font-size: 12px; text-align: right; }
        .inv-totals-grid .bold { font-weight: 700; font-size: 13px; }
        
        .inv-payment { background: #f9f9f9; padding: 16px; border-radius: 6px; font-size: 11px; margin-bottom: 30px; }
        .inv-payment p { color: #888; margin-bottom: 4px; }
        .inv-payment h5 { font-size: 13px; font-weight: 600; margin-bottom: 4px; }
        
        .inv-footer { display: flex; justify-content: space-between; font-size: 9px; color: #888; }
        .inv-signature { text-align: center; width: 120px; }
        .inv-signature p { margin-top: 40px; border-top: 1px solid #ccc; padding-top: 8px; }
        
        .checkbox-row { display: flex; gap: 16px; font-size: 12px; color: var(--text-muted); margin-top: 12px; }
        .checkbox-row label { display: flex; align-items: center; gap: 6px; cursor: pointer; }

        /* Invoice Themes */
        .invoice-paper.theme-minimalist { background: white; color: #333; }
        .invoice-paper.theme-minimalist .inv-header { border-bottom: none; }
        .invoice-paper.theme-minimalist .inv-table th { border-top: 1px solid #eee; border-bottom: 1px solid #eee; padding: 12px 0; }
        .invoice-paper.theme-minimalist .inv-table td { padding: 12px 0; border-bottom: 1px dashed #eee; }
        .invoice-paper.theme-minimalist .highlight-bg { background: #fafafa; padding: 8px 12px; margin: -8px -12px; border-radius: 4px; display: inline-block; width: calc(100% + 24px); box-sizing: border-box; }
        .invoice-paper.theme-minimalist .inv-payment { background: transparent; padding: 0; }
        
        /* Creative & Aesthetic */
        .invoice-paper.theme-creative { background: #fff5f7; color: #5a3c4a; }
        .invoice-paper.theme-creative .inv-header { flex-direction: column; align-items: center; text-align: center; border-bottom: none; }
        .invoice-paper.theme-creative .inv-logo-text { color: #d67a9a; font-size: 24px; margin-bottom: 16px; }
        .invoice-paper.theme-creative .inv-meta { text-align: center; }
        .invoice-paper.theme-creative .inv-meta h2 { color: #d67a9a; font-weight: 300; letter-spacing: 4px; font-size: 28px; }
        .invoice-paper.theme-creative .inv-meta p { color: #c47692; }
        .invoice-paper.theme-creative .inv-table th { color: #d67a9a; border-bottom: none; background: #fce8ee; padding: 12px 16px; }
        .invoice-paper.theme-creative .inv-table td { padding: 12px 16px; border-bottom: 1px solid #f9e1e8; }
        .invoice-paper.theme-creative .highlight-bg { background: #fce8ee; padding: 8px 12px; margin: -8px -12px; border-radius: 4px; display: inline-block; width: calc(100% + 24px); box-sizing: border-box; color: #d67a9a; }
        .invoice-paper.theme-creative .inv-payment { background: transparent; padding: 0; }
        .invoice-paper.theme-creative .bold { color: #a34c6e; }
        
        /* Corporate & Professional */
        .invoice-paper.theme-corporate { background: white; color: #333; padding: 0; overflow: hidden; }
        .invoice-paper.theme-corporate .inv-header { background: #284168; color: white; padding: 40px; border-bottom: none; margin-bottom: 0; }
        .invoice-paper.theme-corporate .inv-header .inv-logo-text { color: white; }
        .invoice-paper.theme-corporate .inv-meta h2 { color: white; font-weight: 600; letter-spacing: 1px; }
        .invoice-paper.theme-corporate .inv-meta p { color: rgba(255,255,255,0.7); }
        .invoice-paper.theme-corporate .inv-desc-row { padding: 30px 40px 10px; }
        .invoice-paper.theme-corporate .inv-bill-to p { color: #888; }
        .invoice-paper.theme-corporate .inv-table { margin: 0 40px 30px; width: calc(100% - 80px); }
        .invoice-paper.theme-corporate .inv-table th { color: #284168; border-top: 2px solid #284168; border-bottom: 2px solid #284168; background: transparent; padding: 12px 0; }
        .invoice-paper.theme-corporate .inv-table td { padding: 12px 0; border-bottom: 1px solid #eee; }
        .invoice-paper.theme-corporate .inv-totals { padding: 0 40px 30px; }
        .invoice-paper.theme-corporate .highlight-bg { background: #ebf1f9; padding: 8px 12px; margin: -8px -12px; border-radius: 4px; display: inline-block; width: calc(100% + 24px); box-sizing: border-box; }
        .invoice-paper.theme-corporate .highlight-bg.left { color: #284168; }
        .invoice-paper.theme-corporate .highlight-bg.right { color: #284168; }
        .invoice-paper.theme-corporate .inv-payment { margin: 0 40px 30px; background: transparent; padding: 0; }
        .invoice-paper.theme-corporate .inv-footer { padding: 0 40px 40px; }

        /* Modern & Bold */
        .invoice-paper.theme-modern { background: white; color: #111; font-family: 'Inter', sans-serif; }
        .invoice-paper.theme-modern .inv-header { border-bottom: 8px solid #111; padding-bottom: 30px; }
        .invoice-paper.theme-modern .inv-meta h2 { color: #111; font-weight: 800; font-size: 32px; letter-spacing: 0; }
        .invoice-paper.theme-modern .inv-table th { color: white; background: #111; border-bottom: none; padding: 12px 16px; }
        .invoice-paper.theme-modern .inv-table td { padding: 12px 16px; border-bottom: 1px solid #eee; }
        .invoice-paper.theme-modern .highlight-bg { background: #f5f5f5; padding: 8px 12px; margin: -8px -12px; border-radius: 4px; display: inline-block; width: calc(100% + 24px); box-sizing: border-box; }
        .invoice-paper.theme-modern .inv-payment { background: transparent; padding: 0; }
        
        /* Classic / Traditional */
        .invoice-paper.theme-classic { background: #fdfbf6; color: #4a3c31; font-family: 'Times New Roman', serif; }
        .invoice-paper.theme-classic .inv-header { flex-direction: column; align-items: center; text-align: center; border-bottom: none; }
        .invoice-paper.theme-classic .inv-logo-text { color: #876839; font-size: 24px; margin-bottom: 16px; }
        .invoice-paper.theme-classic .inv-meta { text-align: center; }
        .invoice-paper.theme-classic .inv-meta h2 { color: #876839; font-weight: 400; letter-spacing: 4px; font-size: 28px; }
        .invoice-paper.theme-classic .inv-table th { color: #876839; border-bottom: none; background: #f0e3cc; padding: 12px 16px; }
        .invoice-paper.theme-classic .inv-table td { padding: 12px 16px; border-bottom: 1px solid #f0e3cc; }
        .invoice-paper.theme-classic .highlight-bg { background: #f0e3cc; padding: 8px 12px; margin: -8px -12px; border-radius: 4px; display: inline-block; width: calc(100% + 24px); box-sizing: border-box; color: #876839; }
        .invoice-paper.theme-classic .inv-payment { background: transparent; padding: 0; }

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
            <button class="mobile-menu-btn" style="display:none; background:none; border:none; font-size:20px; cursor:pointer; margin-right:16px; color:var(--text-dark);"><i class="fa-solid fa-bars"></i></button>
            <div class="page-title">
                <h1>Invoice Generator</h1>
                <p>Buat invoice profesional, simpan riwayatnya, dan kirim ke klien</p>
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
                    <div class="avatar">{{ auth()->check() ? auth()->user()->name[0] ?? 'P' : 'P' }}</div>
                    <div class="user-info">
                        <h4>{{ auth()->check() ? auth()->user()->name : 'Penapict' }} <i class="fa-solid fa-chevron-down"></i></h4>
                        <p>Starter</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="actions-row">
            <div style="display: flex; gap: 12px;">
                <button class="btn btn-primary"><i class="fa-solid fa-plus"></i> Invoice Baru</button>
                <button class="btn btn-secondary"><i class="fa-regular fa-file-lines"></i> Invoice Tersimpan (3)</button>
            </div>
            
            <div class="btn-group">
                <button class="btn active">ID</button>
                <button class="btn">EN</button>
            </div>
        </div>
        
        <div class="invoice-grid">
            
            <!-- LEFT COLUMN: Forms -->
            <div>
                <!-- Panel 1 -->
                <div class="acc-panel">
                    <div class="acc-header">
                        Informasi Bisnis
                        <i class="fa-solid fa-chevron-up"></i>
                    </div>
                    <div class="acc-body">
                        <label class="form-label">Logo Vendor (JPG/PNG, maks 5MB)</label>
                        <div id="logoUploadContainer" style="display: flex; gap: 12px; align-items: center; margin-bottom: 16px;">
                            <div id="logoPreviewThumbnail" style="display: none; width: 48px; height: 48px; border: 1px solid var(--border-color); border-radius: 4px; overflow: hidden; justify-content: center; align-items: center; background: #fafafa;">
                                <img id="logoImgThumbnail" src="" style="max-width: 100%; max-height: 100%;">
                            </div>
                            <div class="upload-box form-group" id="btnUploadBox" style="margin-bottom: 0; flex: 1; display: flex; align-items: center; justify-content: center; cursor: pointer; padding: 10px; border: 1px dashed var(--border-color); border-radius: 8px; color: #555; font-size: 14px;" onclick="document.getElementById('logoInput').click()">
                                <i class="fa-solid fa-arrow-up-from-bracket" style="margin-right: 8px;"></i> Unggah Logo
                            </div>
                            <span id="btnHapusLogo" style="display: none; color: var(--text-muted); cursor: pointer; font-size: 13px;" onclick="hapusLogo()">Hapus</span>
                            <input type="file" id="logoInput" style="display: none;" accept="image/png, image/jpeg" onchange="previewLogoFile(this)">
                        </div>
                        
                        <div class="form-row form-group">
                            <div>
                                <label class="form-label">Nama Vendor</label>
                                <input type="text" class="form-control" value="PENAPICT" id="inputNamaVendor" oninput="syncPreview('inputNamaVendor', 'previewNamaVendor')">
                            </div>
                            <div>
                                <label class="form-label">Alamat Perusahaan</label>
                                <input type="text" class="form-control" value="Rumah sakit no 13 panjer" id="inputAlamatVendor" oninput="syncPreview('inputAlamatVendor', 'previewAlamatVendor')">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Nomor Invoice (Kustom)</label>
                            <input type="text" class="form-control" value="INV-202610-001" id="inputNoInvoice" oninput="syncPreview('inputNoInvoice', 'previewNoInvoice')">
                        </div>
                        
                        <div class="info-text">Kosongkan untuk memakai nomor otomatis berurutan: <strong id="previewAutoInvoice">INV-202610-001</strong></div>
                        
                        <div class="form-row form-group" style="align-items: flex-end;">
                            <div style="grid-column: 1 / 2; margin-top: 12px;">
                                <select class="form-control" id="inputFormatInvoice">
                                    <option value="INV-{YYYY}{MM}-{SEQ3}">INV-{YYYY}{MM}-{SEQ3}</option>
                                </select>
                            </div>
                            <div style="grid-column: 2 / 3; margin-top: 12px;">
                                <button class="btn btn-secondary" style="width: 100%; border: 1px solid var(--primary); color: var(--primary);" id="btnSimpanFormat" onclick="simpanFormat()"><i class="fa-solid fa-check" id="iconFormat" style="display:none; margin-right:4px;"></i> <span id="textFormat">Simpan Format</span></button>
                            </div>
                        </div>
                        <div class="info-text" style="font-size: 10px; color: #ccc;">YY/YYYY, MM, DD, SEQ3/SEQ4/SEQ5</div>
                        
                        <button class="btn btn-secondary" style="width: 100%; margin-top: 16px; border: 1px solid var(--border-color); font-weight: 500;" id="btnSimpanBisnis" onclick="simpanBisnis()"><i class="fa-regular fa-floppy-disk" id="iconBisnis"></i> <span id="textBisnis">Simpan Informasi Bisnis</span></button>
                    </div>
                </div>

                <!-- Panel 2 -->
                <div class="acc-panel">
                    <div class="acc-header">
                        Informasi Klien
                        <i class="fa-solid fa-chevron-up"></i>
                    </div>
                    <div class="acc-body">
                        <div id="bookingConnectedBox" style="display: none; padding: 12px; background: rgba(80, 205, 137, 0.05); border: 1px solid rgba(80, 205, 137, 0.2); border-radius: 8px; margin-bottom: 16px;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                                <span style="font-weight: 600; font-size: 13px; color: var(--text-dark);" id="bookingConnectedText">Terhubung ke Booking: -</span>
                                <span style="color: #f1416c; font-size: 12px; cursor: pointer;" onclick="putuskanBooking()">Putuskan Hubungan</span>
                            </div>
                            <div style="font-size: 11px; color: var(--text-muted);">Pembayaran yang dicatat di invoice ini otomatis memperbarui status pembayaran booking.</div>
                        </div>
                        
                        <button class="btn btn-primary form-group" style="border-radius: 8px; width: 100%;" id="btnAmbilBooking" onclick="openBookingModal()"><i class="fa-solid fa-cloud-arrow-down"></i> Ambil dari Booking</button>
                        <div class="info-text form-group" id="infoAmbilBooking">Isi otomatis nama, WhatsApp, alamat, tanggal acara, paket, diskon, dan add-on dari data Booking & Operasional.</div>
                        
                        <div class="form-row form-group">
                            <div>
                                <label class="form-label">Nama Klien (Pengantin)</label>
                                <input type="text" class="form-control" value="Rian & Amal" id="inputNamaKlien" oninput="syncPreview('inputNamaKlien', 'previewNamaKlien')">
                            </div>
                            <div>
                                <label class="form-label">No. WhatsApp Klien</label>
                                <input type="text" class="form-control" value="081234567890" id="inputNoWaKlien">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Alamat Klien (opsional)</label>
                            <input type="text" class="form-control" placeholder="Alamat lengkap klien" id="inputAlamatKlien" oninput="syncPreview('inputAlamatKlien', 'previewAlamatKlien')">
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Tanggal Acara</label>
                            <div style="position: relative;">
                                <input type="text" class="form-control" placeholder="mm/dd/yyyy" id="inputTglAcara" oninput="syncPreview('inputTglAcara', 'previewTglAcara')">
                                <i class="fa-regular fa-calendar" style="position: absolute; right: 14px; top: 12px; color: var(--text-muted);"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Panel 3 -->
                <div class="acc-panel">
                    <div class="acc-header">
                        Rincian Paket & Biaya
                        <i class="fa-solid fa-chevron-up"></i>
                    </div>
                    <div class="acc-body">
                        <div class="form-row form-group">
                            <div>
                                <label class="form-label">Nama Paket Utama</label>
                                <input type="text" class="form-control" value="Paket Utama" id="inputPaketUtama" oninput="calculateTotals()">
                            </div>
                            <div>
                                <label class="form-label">Harga Paket Utama</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" class="form-control" value="10000000" id="inputHargaUtama" oninput="calculateTotals()">
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group" style="width: 50%;">
                            <label class="form-label">Diskon Spesial</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" class="form-control" value="500000" id="inputDiskon" oninput="calculateTotals()">
                            </div>
                        </div>

                        <div id="addonContainer">
                            <!-- Addon rows go here -->
                        </div>
                        
                        <button class="btn btn-outline-primary form-group" onclick="addAddon()"><i class="fa-solid fa-plus"></i> Tambah Paket</button>
                        
                        <div class="info-text bg form-group" style="display: flex; justify-content: space-between; font-weight: 500; align-items: center; padding: 12px; border-radius: 8px;">
                            <div style="display: flex; flex-direction: column; gap: 4px;">
                                <span style="font-size: 11px; color: var(--text-muted);">Subtotal Paket</span>
                                <span id="calcSubtotal">Rp 10.000.000</span>
                            </div>
                            <div style="display: flex; flex-direction: column; gap: 4px;">
                                <span style="font-size: 11px; color: var(--text-muted);">Total Add-on</span>
                                <span id="calcAddon">Rp 0</span>
                            </div>
                            <div style="display: flex; flex-direction: column; gap: 4px; text-align: right;">
                                <span style="font-size: 11px; color: var(--text-muted);">GRAND TOTAL</span>
                                <span id="calcGrandTotal" style="color: var(--primary); font-size: 16px; font-weight: 600;">Rp 9.500.000</span>
                            </div>
                        </div>
                        
                        <button class="btn btn-secondary" style="width: 100%; border: 1px solid var(--border-color); font-weight: 500;" id="btnSimpanBiaya" onclick="simpanBiaya()"><i class="fa-regular fa-floppy-disk" id="iconBiaya"></i> <span id="textBiaya">Simpan Paket & Harga</span></button>
                    </div>
                </div>
                
                <!-- Panel 5 -->
                <div class="acc-panel">
                    <div class="acc-header">
                        Termin & Pembayaran
                        <i class="fa-solid fa-chevron-up"></i>
                    </div>
                    <div class="acc-body">
                        <div class="form-row form-group">
                            <div>
                                <label class="form-label">Termin Saat Ini</label>
                                <select class="form-control">
                                    <option>Uang Muka / DP 1</option>
                                </select>
                            </div>
                            <div>
                                <label class="form-label">Nominal DP 1</label>
                                <input type="text" class="form-control" value="Rp 3.000.000">
                            </div>
                        </div>
                        
                        <div class="form-group" style="width: 50%;">
                            <label class="form-label">Nominal DP 2</label>
                            <input type="text" class="form-control" value="Rp 0">
                        </div>
                        
                        <div class="info-text bg form-group" style="display: flex; justify-content: space-between; font-weight: 500;">
                            <span>Total DP 1: <strong style="color: var(--text-dark);">Rp 3.000.000</strong></span>
                            <span>Sisa Tagihan: <strong style="color: var(--text-dark);">Rp 8.000.000</strong></span>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Tanggal Jatuh Tempo Pelunasan</label>
                            <div style="position: relative;">
                                <input type="text" class="form-control" placeholder="mm/dd/yyyy">
                                <i class="fa-regular fa-calendar" style="position: absolute; right: 14px; top: 12px; color: var(--text-muted);"></i>
                            </div>
                        </div>
                        
                        <div class="form-row form-group">
                            <div>
                                <label class="form-label">Nama Bank</label>
                                <input type="text" class="form-control" value="BNI">
                            </div>
                            <div>
                                <label class="form-label">No Rekening</label>
                                <input type="text" class="form-control" value="0432063867">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Atas Nama</label>
                            <input type="text" class="form-control" value="Umi Salamah">
                        </div>
                        
                        <button class="btn btn-secondary" style="width: 100%;"><i class="fa-regular fa-floppy-disk"></i> Simpan Data Bank</button>
                    </div>
                </div>

                <!-- Panel 6 -->
                <div class="acc-panel">
                    <div class="acc-header">
                        Syarat & Ketentuan (Kustom)
                        <i class="fa-solid fa-chevron-up"></i>
                    </div>
                    <div class="acc-body">
                        <div class="info-text form-group">Atur poin-poin S&K yang muncul di invoice. Perubahan tersimpan otomatis di perangkat ini.</div>
                        <div class="form-row">
                            <button class="btn btn-outline-primary"><i class="fa-solid fa-plus"></i> Tambah Poin</button>
                            <button class="btn btn-secondary" style="border: 1px dashed var(--border-color);">Reset ke Default</button>
                        </div>
                    </div>
                </div>
                
                <!-- Draf Pesan WA -->
                <div class="draft-area">
                    <div class="draft-title">
                        <i class="fa-regular fa-message"></i> Draf Pesan WhatsApp
                    </div>
                    
                    <div class="tone-badges">
                        <div class="tone-badge active" data-tone="ramah">Ramah</div>
                        <div class="tone-badge" data-tone="formal">Formal</div>
                        <div class="tone-badge" data-tone="ringkas">Ringkas</div>
                        <div class="tone-badge" data-tone="reminder">Reminder</div>
                    </div>
                    
                    <textarea class="draft-textarea" id="wa-draft-textarea">Halo Kak , semoga persiapannya lancar ya. ✨ 
Kami dari *PENAPICT * ingin mengirimkan invoice untuk *Uang Muka / DP 1*. 

Total tagihan untuk termin ini sebesar *Rp 3.000.000* 

Dapat ditransfer ke:
 *BNI* 0431063867 a.n Umi Salamah. 

Detail invoice (termasuk rincian paket, diskon, add-on, serta Syarat & Ketentuan) terlampir ya Kak. 
Terima kasih banyak! 🤍</textarea>
                    
                    <div class="info-text form-group">Pesan bisa diedit langsung sebelum dikirim ke klien.</div>
                    
                    <button class="btn btn-primary" style="width: 100%; padding: 14px; font-weight: 600;"><i class="fa-regular fa-paper-plane"></i> Kirim ke WhatsApp Klien</button>
                </div>
                
            </div>
            
            <!-- RIGHT COLUMN: Preview -->
            <div>
                <div class="section-title"><i class="fa-solid fa-palette"></i> Gaya Desain Invoice</div>
                <div class="design-options">
                    <div class="design-card active" data-theme="theme-minimalist">
                        Minimalis & Clean
                        <div style="height: 4px; background: #666; border-radius: 2px; margin-top: 12px; width: 100%;"></div>
                    </div>
                    <div class="design-card" data-theme="theme-creative">
                        Creative & Aesthetic
                        <div style="height: 4px; background: #d67a9a; border-radius: 2px; margin-top: 12px; width: 100%;"></div>
                    </div>
                    <div class="design-card" data-theme="theme-corporate">
                        Corporate & Professional
                        <div style="height: 4px; background: #1b365d; border-radius: 2px; margin-top: 12px; width: 100%;"></div>
                    </div>
                    <div class="design-card" data-theme="theme-modern">
                        Modern & Bold
                        <div style="height: 4px; background: #111; border-radius: 2px; margin-top: 12px; width: 100%;"></div>
                    </div>
                    <div class="design-card" data-theme="theme-classic">
                        Classic / Traditional
                        <div style="height: 4px; background: #876839; border-radius: 2px; margin-top: 12px; width: 100%;"></div>
                    </div>
                </div>
                
                <div class="section-title"><i class="fa-regular fa-eye"></i> LIVE PREVIEW INVOICE</div>
                
                <!-- Paper Invoice -->
                <div class="invoice-paper theme-minimalist" id="invoice-preview" style="background: white; padding: 40px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); border-radius: 12px; margin-top: 20px;">
                    <!-- HEADER -->
                    <div class="inv-header" style="border-bottom: 2px solid #eee; padding-bottom: 20px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: flex-start;">
                        <div style="display: flex; align-items: center; gap: 16px;">
                            <div id="previewLogoContainer" style="width: 56px; height: 56px; border-radius: 50%; background: #f1f1f4; display: flex; align-items: center; justify-content: center; overflow: hidden; flex-shrink: 0; border: 1px solid #e4e6ef;">
                                <img id="previewLogoImg" src="" style="max-height: 100%; max-width: 100%; display: none;">
                                <i class="fa-regular fa-image" id="previewLogoPlaceholder" style="color: #a1a5b7; font-size: 20px;"></i>
                            </div>
                            <div>
                                <div class="inv-logo-text" id="previewNamaVendor" style="margin-bottom: 4px; font-size: 20px; font-weight: 600; color: #222;">PENAPICT</div>
                                <div style="font-size: 10px; color: #888;" id="previewAlamatVendor">Rumah sakit no 13 panjer</div>
                            </div>
                        </div>
                        <div class="inv-meta" style="text-align: right;">
                            <h2 style="font-size: 24px; font-weight: 300; letter-spacing: 2px; color: #555; margin-bottom: 8px;">INVOICE</h2>
                            <p style="font-size: 10px; color: #888; margin-bottom: 2px;">No. <span id="previewNoInvoice">INV-202610-001</span></p>
                            <p style="font-size: 10px; color: #888;">10 Oktober 2026</p>
                        </div>
                    </div>
                    
                    <!-- KEPADA / TANGGAL ACARA -->
                    <div class="inv-desc-row" style="margin-bottom: 24px; display: flex; justify-content: space-between;">
                        <div class="inv-bill-to">
                            <p style="font-size: 9px; font-weight: 600; color: #999; margin-bottom: 8px; text-transform: uppercase;">KEPADA</p>
                            <h4 id="previewNamaKlien" style="font-size: 15px; font-weight: 500; color: #333; margin-bottom: 4px;">fariz</h4>
                            <p style="font-size: 11px; color: #888;" id="previewAlamatKlien">+6285878067644</p>
                        </div>
                        <div class="inv-bill-to" style="text-align: right;">
                            <p style="font-size: 9px; font-weight: 600; color: #999; margin-bottom: 8px; text-transform: uppercase;">TANGGAL ACARA</p>
                            <h4 style="font-size: 15px; font-weight: 500; color: #333; margin-bottom: 4px;" id="previewTglAcara">Sabtu, 10 Oktober 2026</h4>
                            <p style="font-size: 11px; color: #888;">Uang Muka / DP 1</p>
                        </div>
                    </div>
                    
                    <!-- TABEL -->
                    <table class="inv-table" style="margin-bottom: 16px; width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: #f8f9fa;">
                                <th style="text-align: left; padding: 12px 16px; font-size: 10px; font-weight: 600; color: #666; text-transform: uppercase;">DESKRIPSI</th>
                                <th style="text-align: right; padding: 12px 16px; font-size: 10px; font-weight: 600; color: #666; text-transform: uppercase;">HARGA</th>
                            </tr>
                        </thead>
                        <tbody id="previewTableBody">
                            <tr>
                                <td style="padding: 16px; border-bottom: 1px solid #f1f1f4;">
                                    <div style="font-size: 13px; font-weight: 500; color: #333; margin-bottom: 4px;">Penawaran QT-20261010-236</div>
                                    <div style="font-size: 10px; color: #888;">Paket Utama</div>
                                </td>
                                <td style="text-align: right; padding: 16px; border-bottom: 1px solid #f1f1f4; font-size: 13px; color: #333;">Rp 9.000</td>
                            </tr>
                        </tbody>
                    </table>
                    
                    <!-- TOTALS -->
                    <div class="inv-totals" style="display: flex; justify-content: flex-end; margin-bottom: 24px; padding-right: 16px;">
                        <div style="width: 280px;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 11px;">
                                <div style="color: #666;">Subtotal Paket</div>
                                <div id="previewSubtotal" style="color: #333;">Rp 9.000</div>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 12px; font-size: 11px;">
                                <div style="color: #666;">Total Add-on</div>
                                <div id="previewAddon" style="color: #333;">Rp 0</div>
                            </div>
                            <!-- Grand Total -->
                            <div style="display: flex; justify-content: space-between; padding-top: 12px; border-top: 1px solid #eee; font-size: 12px; font-weight: 700;">
                                <div style="color: #333;">GRAND TOTAL</div>
                                <div id="previewGrandTotal" style="color: #333;">Rp 9.000</div>
                            </div>
                            
                            <!-- DP Info Box -->
                            <div style="background: #f8f9fa; border-radius: 8px; padding: 12px; margin-top: 16px;">
                                <div style="display: flex; justify-content: space-between; margin-bottom: 6px; font-size: 11px;">
                                    <div style="color: #666;">Uang Muka / DP 1</div>
                                    <div id="previewDP1" style="color: #333;">Rp 3.000.000</div>
                                </div>
                                <div style="display: flex; justify-content: space-between; font-size: 11px; font-weight: 600;">
                                    <div style="color: #666;">Sisa Tagihan</div>
                                    <div id="previewSisaTagihan" style="color: #333; font-size: 13px;">Rp 0</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- METODE PEMBAYARAN -->
                    <div class="inv-payment" style="border: 1px solid #f1f1f4; border-radius: 8px; padding: 16px; margin-bottom: 32px;">
                        <p style="text-transform: uppercase; font-size: 9px; font-weight: 600; color: #999; margin-bottom: 8px;">METODE PEMBAYARAN</p>
                        <h5 style="font-size: 12px; font-weight: 400; color: #555; margin: 0;"><strong style="color: #333;">BNI</strong> - 0431063867 - a.n Umi Salamah</h5>
                    </div>
                    
                    <!-- SYARAT KETENTUAN -->
                    <div class="inv-footer" style="display: block;">
                        <div style="margin-bottom: 24px;">
                            <p style="font-weight: 700; font-size: 10px; margin-bottom: 12px; color: #555;">SYARAT & KETENTUAN</p>
                            <!-- Line divider and signature -->
                            <div style="border-top: 1px solid #eee; padding-top: 24px; display: flex; justify-content: flex-end;">
                                <div class="inv-signature" style="text-align: center; width: 150px;">
                                    <div style="margin-bottom: 40px; font-weight: 400; font-style: italic; font-size: 14px; color: #333;" id="previewTtdNama">Fariz hidayat</div>
                                    <div style="border-top: 1px solid #ccc; padding-top: 8px;">
                                        <p style="font-size: 9px; color: #888; margin: 0;">Tanda Tangan</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="section-title"><i class="fa-solid fa-pen-nib"></i> Tanda Tangan (PNG/JPG, maks 2MB)</div>
                <div class="upload-box form-group">
                    <i class="fa-solid fa-signature" style="color: var(--text-muted);"></i> Unggah TTD
                </div>
                <div class="checkbox-row" style="margin-bottom: 24px;">
                    <label><input type="checkbox"> Belum ada logo</label>
                    <label><input type="checkbox"> Belum ada tanda tangan</label>
                </div>
                
                <div style="display: flex; gap: 12px; margin-bottom: 40px;">
                    <button class="btn btn-secondary"><i class="fa-regular fa-image"></i> Unduh PNG</button>
                    <button class="btn btn-secondary" style="background: var(--text-dark); color: white; border-color: var(--text-dark);"><i class="fa-regular fa-file-pdf"></i> Unduh PDF</button>
                    <button class="btn btn-primary" style="flex: 1;"><i class="fa-regular fa-folder-open"></i> Simpan ke Folder</button>
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

        // Accordion Toggle
        document.querySelectorAll('.acc-header').forEach(header => {
            header.addEventListener('click', () => {
                const body = header.nextElementSibling;
                const icon = header.querySelector('i');
                if (body.style.display === 'none') {
                    body.style.display = 'block';
                    icon.style.transform = 'rotate(0deg)';
                } else {
                    body.style.display = 'none';
                    icon.style.transform = 'rotate(180deg)';
                }
            });
        });

        // Invoice Theme Switcher
        const designCards = document.querySelectorAll('.design-card');
        const invoicePaper = document.getElementById('invoice-preview');
        
        designCards.forEach(card => {
            card.addEventListener('click', () => {
                designCards.forEach(c => c.classList.remove('active'));
                card.classList.add('active');
                
                // Remove all theme classes
                invoicePaper.className = 'invoice-paper';
                
                // Add selected theme
                const theme = card.getAttribute('data-theme');
                if (theme) {
                    invoicePaper.classList.add(theme);
                }
            });
        });

        // WhatsApp Draft Templates
        const waTemplates = {
            ramah: `Halo Kak , semoga persiapannya lancar ya. ✨ 
Kami dari *PENAPICT * ingin mengirimkan invoice untuk *Uang Muka / DP 1*. 

Total tagihan untuk termin ini sebesar *Rp 3.000.000* 

Dapat ditransfer ke:
 *BNI* 0431063867 a.n Umi Salamah. 

Detail invoice (termasuk rincian paket, diskon, add-on, serta Syarat & Ketentuan) terlampir ya Kak. 
Terima kasih banyak! 🤍`,
            formal: `Kepada Yth. Bapak/Ibu ,

Berikut kami sampaikan tagihan resmi dari *PENAPICT * untuk *Uang Muka / DP 1*.

• No. Invoice: INV-202610-006
• Jumlah Tagihan: *Rp 3.000.000*
• Rekening Pembayaran: *BNI* 0431063867 a.n Umi Salamah

Dokumen invoice beserta rincian paket dan Syarat & Ketentuan terlampir.
Atas perhatian dan kerja samanya, kami ucapkan terima kasih.`,
            ringkas: `*INVOICE — PENAPICT *
Klien: 
Tagihan: Uang Muka / DP 1 — *Rp 3.000.000*
Transfer ke: *BNI* 0431063867 a.n Umi Salamah

Invoice digital terlampir. Mohon konfirmasi bukti transfer jika sudah dibayar ya.`,
            reminder: `Halo Kak , salam dari *PENAPICT *. 🙏

Sekadar mengingatkan untuk invoice *Uang Muka / DP 1* sebesar *Rp 3.000.000*.

Pembayaran dapat ditransfer ke:
*BNI* 0431063867 a.n Umi Salamah

Jika pembayaran sudah dilakukan, mohon abaikan pesan ini atau kirimkan buktinya ya Kak. Terima kasih banyak! 🤍`
        };

        const toneBadges = document.querySelectorAll('.tone-badge');
        const waTextarea = document.getElementById('wa-draft-textarea');
        
        toneBadges.forEach(badge => {
            badge.addEventListener('click', () => {
                toneBadges.forEach(b => b.classList.remove('active'));
                badge.classList.add('active');
                
                const tone = badge.getAttribute('data-tone');
                if (tone && waTemplates[tone]) {
                    waTextarea.value = waTemplates[tone];
                }
            });
        });

        // Live Preview functions
        function syncPreview(inputId, previewId) {
            const inputVal = document.getElementById(inputId).value;
            const previewEl = document.getElementById(previewId);
            
            if (previewEl) {
                previewEl.innerText = inputVal;
            }
            
            // Khusus untuk input Nomor Invoice Kustom
            if(inputId === 'inputNoInvoice') {
                const autoInvoiceEl = document.getElementById('previewAutoInvoice');
                if (autoInvoiceEl) {
                    autoInvoiceEl.innerText = inputVal || "INV-202610-001";
                }
                if (!inputVal && previewEl) {
                    previewEl.innerText = "INV-202610-001"; // fallback ke otomatis jika kosong
                }
            }
        }

        function previewLogoFile(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('logoPreviewThumbnail').style.display = 'flex';
                    document.getElementById('logoImgThumbnail').src = e.target.result;
                    document.getElementById('btnUploadBox').style.display = 'none';
                    document.getElementById('btnHapusLogo').style.display = 'inline';
                    
                    document.getElementById('previewLogoPlaceholder').style.display = 'none';
                    document.getElementById('previewLogoImg').style.display = 'block';
                    document.getElementById('previewLogoImg').src = e.target.result;
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        function hapusLogo() {
            document.getElementById('logoInput').value = '';
            document.getElementById('logoPreviewThumbnail').style.display = 'none';
            document.getElementById('logoImgThumbnail').src = '';
            document.getElementById('btnUploadBox').style.display = 'flex';
            document.getElementById('btnHapusLogo').style.display = 'none';
            
            document.getElementById('previewLogoPlaceholder').style.display = 'block';
            document.getElementById('previewLogoImg').style.display = 'none';
            document.getElementById('previewLogoImg').src = '';
        }

        function simpanFormat() {
            const btn = document.getElementById('btnSimpanFormat');
            const icon = document.getElementById('iconFormat');
            const text = document.getElementById('textFormat');
            
            btn.style.background = 'rgba(80, 205, 137, 0.1)';
            btn.style.borderColor = 'var(--success)';
            btn.style.color = 'var(--success)';
            icon.style.display = 'inline-block';
            text.innerText = 'Tersimpan';
            setTimeout(() => {
                btn.style.background = 'transparent';
                btn.style.borderColor = 'var(--primary)';
                btn.style.color = 'var(--primary)';
                icon.style.display = 'none';
                text.innerText = 'Simpan Format';
            }, 2000);
        }

        function simpanBisnis() {
            const btn = document.getElementById('btnSimpanBisnis');
            const icon = document.getElementById('iconBisnis');
            const text = document.getElementById('textBisnis');
            
            btn.style.background = 'rgba(80, 205, 137, 0.1)';
            btn.style.borderColor = 'var(--success)';
            btn.style.color = 'var(--success)';
            icon.className = 'fa-solid fa-check';
            text.innerText = 'Tersimpan';
            setTimeout(() => {
                btn.style.background = 'transparent';
                btn.style.borderColor = 'var(--border-color)';
                btn.style.color = 'var(--text-dark)';
                icon.className = 'fa-regular fa-floppy-disk';
                text.innerText = 'Simpan Informasi Bisnis';
            }, 2000);
        }
        
        // --- Modal Booking ---
        function openBookingModal() {
            if(!document.getElementById('bookingModal')) {
                const modalHtml = `
                <div id="bookingModal" style="display: flex; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
                    <div style="background: white; width: 90%; max-width: 500px; border-radius: 12px; padding: 24px; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                            <h3 style="margin: 0; font-size: 18px; font-weight: 600;">Pilih Booking</h3>
                            <i class="fa-solid fa-xmark" style="cursor: pointer; color: var(--text-muted); font-size: 18px;" onclick="closeBookingModal()"></i>
                        </div>
                        <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 20px;">Data klien pada form invoice akan ditimpa oleh data booking yang dipilih.</p>
                        
                        <div style="margin-bottom: 16px; position: relative;">
                            <input type="text" class="form-control" placeholder="Cari nama klien atau paket..." style="border-radius: 20px; padding-left: 16px; border-color: var(--primary);">
                        </div>
                        
                        <div style="max-height: 250px; overflow-y: auto;">
                            <div style="padding: 12px 16px; border: 1px solid var(--border-color); border-radius: 8px; cursor: pointer; margin-bottom: 8px; transition: 0.2s;" onclick="pilihBooking('fariz', '085878067644', 'Alamat lengkap klien', '10/10/2026', 'Penawaran QT-20261010-236', 9000, 0, [])" onmouseover="this.style.borderColor='var(--primary)'" onmouseout="this.style.borderColor='var(--border-color)'">
                                <div style="font-weight: 600; font-size: 14px; margin-bottom: 4px;">fariz</div>
                                <div style="font-size: 12px; color: var(--text-muted);">2026-10-10 &bull; Penawaran QT-20261010-236 &bull; Rp 9.000</div>
                            </div>
                        </div>
                    </div>
                </div>`;
                document.body.insertAdjacentHTML('beforeend', modalHtml);
            } else {
                document.getElementById('bookingModal').style.display = 'flex';
            }
        }
        
        function closeBookingModal() {
            document.getElementById('bookingModal').style.display = 'none';
        }
        
        function pilihBooking(nama, wa, alamat, tgl, paketUtama, hargaUtama, diskonUtama, addons) {
            closeBookingModal();
            
            // Tampilkan state connected
            document.getElementById('btnAmbilBooking').style.display = 'none';
            document.getElementById('infoAmbilBooking').style.display = 'none';
            document.getElementById('bookingConnectedBox').style.display = 'block';
            document.getElementById('bookingConnectedText').innerText = `Terhubung ke Booking: ${nama} — ${tgl}`;
            
            // Set values di UI Klien
            document.getElementById('inputNamaKlien').value = nama;
            document.getElementById('inputNoWaKlien').value = wa;
            document.getElementById('inputAlamatKlien').value = alamat;
            document.getElementById('inputTglAcara').value = tgl;
            
            // Set values di UI Biaya
            document.getElementById('inputPaketUtama').value = paketUtama;
            document.getElementById('inputHargaUtama').value = hargaUtama;
            document.getElementById('inputDiskon').value = diskonUtama;
            
            // Render Live Preview Klien
            syncPreview('inputNamaKlien', 'previewNamaKlien');
            syncPreview('inputAlamatKlien', 'previewAlamatKlien');
            syncPreview('inputTglAcara', 'previewTglAcara');
            
            // Clear addons (mock aja)
            document.getElementById('addonContainer').innerHTML = '';
            
            // Render Biaya
            calculateTotals();
            
            // Toast / Notif (optional tapi diminta user)
            alert("Data klien diambil dari booking"); // Nanti bisa diganti dengan toast JS
        }
        
        function putuskanBooking() {
            document.getElementById('btnAmbilBooking').style.display = 'block';
            document.getElementById('infoAmbilBooking').style.display = 'block';
            document.getElementById('bookingConnectedBox').style.display = 'none';
            document.getElementById('inputNamaKlien').value = '';
            document.getElementById('inputNoWaKlien').value = '';
            document.getElementById('inputAlamatKlien').value = '';
            document.getElementById('inputTglAcara').value = '';
            syncPreview('inputNamaKlien', 'previewNamaKlien');
            syncPreview('inputAlamatKlien', 'previewAlamatKlien');
            syncPreview('inputTglAcara', 'previewTglAcara');
        }
        
        // --- Biaya Dinamis ---
        function formatRupiah(angka) {
            return 'Rp ' + parseInt(angka).toLocaleString('id-ID');
        }

        function addAddon() {
            const html = `
            <div class="addon-row form-group" style="border: 1px dashed var(--border-color); padding: 12px; border-radius: 8px; position: relative;">
                <i class="fa-solid fa-trash" style="color: #f1416c; position: absolute; right: 12px; top: 12px; cursor: pointer;" onclick="removeAddon(this)"></i>
                <div style="margin-bottom: 12px;">
                    <label class="form-label">Nama Paket Tambahan</label>
                    <input type="text" class="form-control addon-name" value="Live Streaming" oninput="calculateTotals()">
                </div>
                <div class="form-row">
                    <div>
                        <label class="form-label">Harga Paket</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" class="form-control addon-price" value="1500000" oninput="calculateTotals()">
                        </div>
                    </div>
                    <div>
                        <label class="form-label">Diskon</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" class="form-control addon-discount" value="0" oninput="calculateTotals()">
                        </div>
                    </div>
                </div>
            </div>`;
            document.getElementById('addonContainer').insertAdjacentHTML('beforeend', html);
            calculateTotals();
        }
        
        function removeAddon(btn) {
            btn.closest('.addon-row').remove();
            calculateTotals();
        }
        
        function calculateTotals() {
            // Get Utama
            const pktUtama = document.getElementById('inputPaketUtama').value || 'Paket Utama';
            const hrgaUtama = parseInt(document.getElementById('inputHargaUtama').value) || 0;
            const diskon = parseInt(document.getElementById('inputDiskon').value) || 0;
            
            let totalAddon = 0;
            let tbHtml = `
                <tr>
                    <td style="padding: 16px; border-bottom: 1px solid #f1f1f4;">
                        <div style="font-size: 13px; font-weight: 500; color: #333; margin-bottom: 4px;">${pktUtama}</div>
                        <div style="font-size: 10px; color: #888;">Paket Utama</div>
                    </td>
                    <td style="text-align: right; padding: 16px; border-bottom: 1px solid #f1f1f4; font-size: 13px; color: #333;">${formatRupiah(hrgaUtama)}</td>
                </tr>`;
                
            // Get Addons
            const addons = document.querySelectorAll('.addon-row');
            addons.forEach(row => {
                const name = row.querySelector('.addon-name').value || 'Add-on';
                const price = parseInt(row.querySelector('.addon-price').value) || 0;
                const disc = parseInt(row.querySelector('.addon-discount').value) || 0;
                const net = price - disc;
                totalAddon += net;
                
                tbHtml += `
                <tr>
                    <td style="padding: 16px; border-bottom: 1px solid #f1f1f4;">
                        <div style="font-size: 13px; font-weight: 500; color: #333; margin-bottom: 4px;">${name}</div>
                        <div style="font-size: 10px; color: #888;">Add-on${disc > 0 ? ' &bull; Diskon: ' + formatRupiah(disc) : ''}</div>
                    </td>
                    <td style="text-align: right; padding: 16px; border-bottom: 1px solid #f1f1f4; font-size: 13px; color: #333;">${formatRupiah(net)}</td>
                </tr>`;
            });
            
            const subtotal = hrgaUtama;
            const grandTotal = subtotal + totalAddon - diskon;
            
            // Set UI Left
            document.getElementById('calcSubtotal').innerText = formatRupiah(subtotal);
            document.getElementById('calcAddon').innerText = formatRupiah(totalAddon);
            document.getElementById('calcGrandTotal').innerText = formatRupiah(grandTotal);
            
            // Set UI Preview
            document.getElementById('previewTableBody').innerHTML = tbHtml;
            document.getElementById('previewSubtotal').innerText = formatRupiah(subtotal);
            document.getElementById('previewAddon').innerText = formatRupiah(totalAddon);
            
            // Diskon logic - if no discount, maybe hide it? In the image, diskon is subtracted from Grand Total but not explicitly shown as a separate row above subtotal. Oh wait, it's just GRAND TOTAL.
            // Let's just update grand total
            document.getElementById('previewGrandTotal').innerText = formatRupiah(grandTotal);
            
            // Mock DP calculation (just keeping the existing UI values but calculating Sisa)
            const dp1Str = document.getElementById('previewDP1').innerText.replace(/[^0-9]/g, '');
            const dp1 = parseInt(dp1Str) || 3000000;
            const sisa = grandTotal - dp1;
            document.getElementById('previewSisaTagihan').innerText = formatRupiah(sisa > 0 ? sisa : 0);
        }
        
        function simpanBiaya() {
            const btn = document.getElementById('btnSimpanBiaya');
            const icon = document.getElementById('iconBiaya');
            const text = document.getElementById('textBiaya');
            
            btn.style.background = 'rgba(80, 205, 137, 0.1)';
            btn.style.borderColor = 'var(--success)';
            btn.style.color = 'var(--success)';
            icon.className = 'fa-solid fa-check';
            text.innerText = 'Tersimpan';
            setTimeout(() => {
                btn.style.background = 'transparent';
                btn.style.borderColor = 'var(--border-color)';
                btn.style.color = 'var(--text-dark)';
                icon.className = 'fa-regular fa-floppy-disk';
                text.innerText = 'Simpan Paket & Harga';
            }, 2000);
            
            alert("Paket disimpan"); // Toast mock
        }
        
        // Initialize once on load
        document.addEventListener('DOMContentLoaded', () => {
            calculateTotals();
        });
    </script>
    <script src="/js/responsive.js"></script>
</body>
</html>
