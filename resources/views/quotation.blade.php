<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quotation Generator - CloseMateAI</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Cormorant+Garamond:ital,wght@0,500;0,600;0,700;1,500;1,600;1,700&display=swap" rel="stylesheet">
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
        
        .table-responsive { width: 100%; overflow-x: auto; background: white; border-radius: 12px; border: 1px solid var(--border-color); }
        table { width: 100%; border-collapse: collapse; min-width: 800px; }
        th { padding: 16px; text-align: left; font-size: 13px; font-weight: 600; color: var(--text-muted); border-bottom: 1px dashed var(--border-color); white-space: nowrap; }
        td { padding: 16px; font-size: 14px; border-bottom: 1px dashed var(--border-color); vertical-align: middle; }
        tr:hover { background-color: #fafafa; }
        tr:last-child td { border-bottom: none; }
        
        .status-badge { padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; text-transform: uppercase; }
        .status-draft { background: #f1f1f4; color: var(--text-muted); }
        .status-terkirim { background: rgba(107, 92, 216, 0.1); color: var(--primary); }
        .status-disetujui { background: rgba(80, 205, 137, 0.1); color: var(--success); }
        .status-ditolak { background: rgba(241, 65, 108, 0.1); color: var(--danger); }
        
        .action-btns { display: flex; gap: 8px; }
        .btn-icon { width: 32px; height: 32px; border-radius: 8px; display: flex; justify-content: center; align-items: center; cursor: pointer; border: none; font-size: 14px; transition: 0.2s; }
        .btn-view { background: #f1f1f4; color: var(--text-dark); }
        .btn-view:hover { background: #e4e6ef; }
        .btn-send-wa { background: rgba(80, 205, 137, 0.1); color: var(--success); }
        .btn-send-wa:hover { background: rgba(80, 205, 137, 0.2); }
        .btn-del { background: rgba(241, 65, 108, 0.1); color: var(--danger); }
        .btn-del:hover { background: rgba(241, 65, 108, 0.2); }
        
        /* Toast Container */
        #toast-container { position: fixed; top: 20px; right: 20px; z-index: 9999; display: flex; flex-direction: column; gap: 10px; }
        .toast { background: white; color: var(--text-dark); padding: 16px 20px; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); display: flex; align-items: center; gap: 12px; font-size: 14px; font-weight: 500; border-left: 4px solid var(--success); animation: slideInRight 0.3s forwards; }
        @keyframes slideInRight { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
        @keyframes fadeOut { from { transform: translateX(0); opacity: 1; } to { transform: translateX(100%); opacity: 0; } }

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

        /* =====================================================
           PREVIEW MODAL
           ===================================================== */
        #preview-modal .modal-content { overflow: hidden; }

        .preview-controls { padding: 16px 24px; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; background: white; border-radius: 12px 12px 0 0; flex-shrink: 0; }
        .theme-selectors { display: flex; gap: 12px; flex-wrap: wrap; }
        .theme-btn { padding: 8px 16px; border-radius: 20px; border: 1px solid var(--border-color); background: white; cursor: pointer; display: flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 500; transition: 0.2s; }
        .theme-btn:hover { background: #f9f9f9; }
        .theme-btn.active { border-color: var(--primary); background: rgba(107, 92, 216, 0.05); color: var(--primary); }
        .theme-color-dot { width: 12px; height: 12px; border-radius: 50%; }
        
        /* Area scroll preview: flex agar tinggi mengikuti modal & tidak overflow */
        .preview-document-container { padding: 24px; background: #f1f1f4; overflow: auto; flex: 1 1 auto; min-height: 0; display: flex; justify-content: center; align-items: flex-start; border-radius: 0 0 12px 12px; }
        
        /* Halaman A4 (794 x 1123 px) */
        .document-page { 
            box-sizing: border-box; 
            width: 794px; 
            min-height: 1123px;
            background: #fff; 
            padding: 52px 48px 64px 48px; /* bawah lebih besar agar tidak menabrak garis footer */
            box-shadow: 0 4px 20px rgba(0,0,0,0.08); 
            position: relative; 
            font-family: 'Inter', sans-serif;
            border-radius: 8px;
            display: flex;
            flex-direction: column;
            margin: 0 auto;
            flex-shrink: 0;
            overflow: hidden;
            zoom: 0.85; /* hanya untuk tampilan preview; export memakai klon tanpa zoom */
        }
        
        /* Document Themes */
        .theme-wedding { --doc-primary: #b76e79; --doc-secondary: #f9eaec; --doc-border: #ecd3d7; --doc-accent-text: #7d3f48; --doc-text: #333; --doc-font: 'Cormorant Garamond', Georgia, serif; --doc-vendor-style: italic; --doc-title-weight: 600; }
        .theme-navy   { --doc-primary: #1b365d; --doc-secondary: #eef3f9; --doc-border: #d3deec; --doc-accent-text: #1b365d; --doc-text: #333; --doc-font: 'Inter', sans-serif; --doc-vendor-style: normal; --doc-title-weight: 700; }
        .theme-minimal{ --doc-primary: #222222; --doc-secondary: #f5f5f5; --doc-border: #dddddd; --doc-accent-text: #222222; --doc-text: #333; --doc-font: 'Inter', sans-serif; --doc-vendor-style: normal; --doc-title-weight: 700; }
        
        .doc-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; gap: 20px; }
        .doc-vendor h1 { font-family: var(--doc-font); color: var(--doc-accent-text); font-size: 30px; font-style: var(--doc-vendor-style); font-weight: 600; line-height: 1.1; margin-bottom: 10px; }
        .doc-status { background: var(--doc-secondary); color: var(--doc-primary); padding: 4px 12px; border-radius: 999px; font-size: 11px; font-weight: 600; display: inline-block; }
        
        .doc-meta { text-align: right; }
        .doc-meta h2 { font-family: var(--doc-font); font-weight: var(--doc-title-weight); letter-spacing: 3px; color: var(--doc-accent-text); font-size: 22px; line-height: 1.2; margin-bottom: 6px; text-transform: uppercase; white-space: nowrap; }
        .doc-meta p { font-size: 11px; color: #777; margin-bottom: 2px; line-height: 1.4; }
        .doc-meta p.doc-no-text { font-weight: 700; color: #222; font-size: 12px; margin-top: 8px; margin-bottom: 4px; }
        
        .doc-info-box { background: var(--doc-secondary); padding: 18px 24px; border-radius: 12px; margin-bottom: 24px; display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; }
        .doc-info-item { min-width: 0; }
        .doc-info-item h4 { font-size: 10px; color: #777; text-transform: uppercase; letter-spacing: 1.5px; margin-bottom: 4px; font-weight: 600; }
        .doc-info-item p { font-size: 13px; color: var(--doc-text); font-weight: 600; word-break: break-word; }
        .doc-info-item.full-width { grid-column: span 3; }
        
        .doc-table { width: 100%; border-collapse: collapse; margin-bottom: 16px; table-layout: fixed; min-width: 0; word-break: break-word; }
        .doc-table th, .doc-table td { box-sizing: border-box; }
        .doc-table th { background: var(--doc-primary); color: white; padding: 10px 12px; font-size: 10px; letter-spacing: 1px; text-transform: uppercase; font-weight: 700; white-space: normal; border: none; text-align: left; }
        .doc-table td { padding: 12px; border-bottom: 1px solid #eee; font-size: 13px; color: var(--doc-text); white-space: normal; vertical-align: top; }
        .doc-table tr:hover { background: transparent; }
        .doc-section-title { font-weight: 700; color: var(--doc-accent-text); font-size: 11px; letter-spacing: 2px; text-transform: uppercase; padding-top: 16px !important; padding-bottom: 8px !important; }
        
        .doc-totals { width: 100%; max-width: 340px; margin-left: auto; margin-bottom: 28px; }
        .doc-total-row { display: flex; justify-content: space-between; align-items: center; padding: 6px 12px; font-size: 13px; }
        .doc-grand-total { background: var(--doc-primary); color: white; font-weight: 700; font-size: 14px; border-radius: 10px; margin-top: 8px; padding: 14px 16px; }
        .doc-grand-total span:last-child { font-size: 16px; }
        
        .doc-termins-title { font-size: 11px; color: var(--doc-accent-text); font-weight: 700; letter-spacing: 2px; margin-bottom: 12px; text-transform: uppercase; }
        .doc-termins-grid { display: flex; flex-wrap: wrap; gap: 12px; margin-bottom: 24px; }
        .doc-termin-card { border: 1px solid var(--doc-border); border-top: 3px solid var(--doc-primary); padding: 12px 14px; border-radius: 10px; flex: 1 1 0; min-width: 120px; }
        .doc-termin-card h5 { font-size: 10px; color: var(--doc-accent-text); text-transform: uppercase; letter-spacing: 1.5px; margin-bottom: 4px; font-weight: 700; }
        .doc-termin-card p { font-size: 12px; font-weight: 600; color: var(--doc-text); margin-bottom: 4px; }
        .doc-termin-card .termin-val { font-size: 14px; color: #222; font-weight: 700; }
        
        .doc-tnc { font-size: 11px; color: #444; line-height: 1.7; margin-bottom: auto; }
        .doc-tnc h4 { font-size: 11px; color: var(--doc-accent-text); font-weight: 700; letter-spacing: 2px; margin-bottom: 8px; text-transform: uppercase; }
        
        .doc-signatures { display: flex; justify-content: space-between; gap: 24px; margin-top: 32px; padding-bottom: 8px; }
        .doc-sig-box { text-align: center; flex: 1 1 0; max-width: 345px; }
        .doc-sig-box p { font-size: 11px; color: #666; margin-bottom: 56px; }
        .doc-sig-box .sig-line { border-bottom: 1px solid #222; margin-bottom: 6px; }
        .doc-sig-box .sig-name { font-weight: 700; font-size: 13px; color: #222; }
        
        /* Skala preview di layar kecil (hanya tampilan, bukan export) */
        @media (max-width: 900px) {
            .document-page { zoom: 0.7; }
        }
        @media (max-width: 768px) {
            #preview-modal.modal-overlay { padding: 16px; }
            .document-page { zoom: 0.55; }
        }
        @media (max-width: 500px) {
            .document-page { zoom: 0.45; }
        }
        
        @media print {
            body * { visibility: hidden; }
            #preview-document, #preview-document * { visibility: visible; }
            #preview-document { position: absolute; left: 0; top: 0; margin: 0; padding: 52px 48px 64px 48px; box-shadow: none; zoom: 1 !important; }
        }
    </style>
</head>
<body>
    <div id="toast-container"></div>

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
                <div class="tab-item active" onclick="setFilter('Semua', this)">Semua</div>
                <div class="tab-item" onclick="setFilter('Draft', this)">Draft</div>
                <div class="tab-item" onclick="setFilter('Terkirim', this)">Terkirim</div>
                <div class="tab-item" onclick="setFilter('Disetujui', this)">Disetujui</div>
                <div class="tab-item" onclick="setFilter('Ditolak', this)">Ditolak</div>
            </div>
            
            <div class="search-add">
                <div class="search-box">
                    <input type="text" placeholder="Cari klien / no. penawaran">
                </div>
                <button class="btn btn-primary" onclick="openQuotationModal()">
                    <i class="fa-solid fa-plus"></i> Buat Penawaran
                </button>
            </div>
        </div>
        
        <div class="empty-state" id="empty-state">
            <div class="empty-icon">
                <i class="fa-solid fa-file-signature"></i>
            </div>
            <p>Belum ada penawaran. Buat penawaran pertama Anda sekarang.</p>
        </div>
        
        <div id="quotations-table-container" style="display: none;">
            <div id="quotations-table-body" style="display: flex; flex-direction: column; gap: 8px;">
                <!-- Dinamis render cards -->
            </div>
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
                    <input type="hidden" id="q_id">
                    <div class="form-group">
                        <label class="form-label">No. Penawaran</label>
                        <input type="text" class="form-control" id="q_no" readonly style="background: #f1f1f4;">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Status</label>
                        <select class="form-control" id="q_status">
                            <option value="Draft">Draft</option>
                            <option value="Terkirim">Terkirim</option>
                            <option value="Disetujui">Disetujui</option>
                            <option value="Ditolak">Ditolak</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nama Klien</label>
                        <input type="text" class="form-control" id="q_client" placeholder="Nama calon klien">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nomor WhatsApp</label>
                        <input type="text" class="form-control" id="q_phone" placeholder="08xxxxxxxxx">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tanggal Acara</label>
                        <input type="date" class="form-control" id="q_event_date" placeholder="mm/dd/yyyy">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Lokasi Venue / Kota</label>
                        <input type="text" class="form-control" id="q_venue" placeholder="Contoh: Hotel Mulia, Jakarta">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Berlaku Sampai</label>
                        <input type="date" class="form-control" id="q_valid_until" placeholder="mm/dd/yyyy">
                    </div>
                </div>
                
                <div>
                    <div class="section-header">
                        <div class="section-title">Item Penawaran</div>
                        <button class="btn btn-outline btn-small" onclick="addQuotationItem()"><i class="fa-solid fa-plus"></i> Tambah Item</button>
                    </div>
                    <div id="q_items_container" style="display:flex; flex-direction:column; gap:16px;">
                        <!-- Items rendered by JS -->
                    </div>
                </div>
                
                <div class="form-grid" style="align-items: flex-start;">
                    <div class="form-group">
                        <label class="form-label">Diskon (Rp)</label>
                        <input type="text" class="form-control" id="q_discount" value="" placeholder="0" onkeyup="formatRupiahInput(this); calculateQuotationTotals()">
                    </div>
                    <div class="summary-box">
                        <div class="summary-grid">
                            <div style="color: var(--text-muted);">Subtotal</div>
                            <div class="bold" id="q_sum_subtotal">Rp 0</div>
                            <div style="color: var(--text-muted);">Diskon</div>
                            <div class="bold" id="q_sum_discount">- Rp 0</div>
                            <div class="bold total-row">Grand Total</div>
                            <div class="bold total-row" id="q_sum_grandtotal">Rp 0</div>
                        </div>
                    </div>
                </div>
                
                <div style="margin-top: 10px;">
                    <div class="section-header">
                        <div class="section-title">Skema Termin Pembayaran</div>
                        <button class="btn btn-outline btn-small" onclick="addTerminItem()"><i class="fa-solid fa-plus"></i> Tambah Termin</button>
                    </div>
                    
                    <div id="q_termins_container">
                        <!-- Termin rendered by JS -->
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Syarat & Ketentuan (T&C / SOP Vendor)</label>
                    <textarea class="form-control" id="q_tnc" style="min-height: 140px;">1. Penawaran ini berlaku sesuai tanggal masa berlaku di atas.
2. Tanggal acara dianggap ter-booking setelah DP 1 diterima.
3. DP yang sudah dibayarkan tidak dapat dikembalikan (non-refundable).
4. Pelunasan dilakukan paling lambat H-7 sebelum hari acara.
5. Perubahan susunan item/paket wajib dikonfirmasi maksimal H-14 sebelum acara.</textarea>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Catatan Internal (tidak tampil ke klien)</label>
                    <textarea class="form-control" id="q_internal_notes" style="min-height: 80px;"></textarea>
                </div>
                
            </div>
            
            <div class="modal-footer">
                <button class="btn btn-outline" id="btn-cancel-modal">Batal</button>
                <button class="btn btn-primary" onclick="saveQuotation()"><i class="fa-solid fa-floppy-disk"></i> Simpan Penawaran</button>
            </div>
        </div>
    </div>

    <!-- Preview Modal -->
    <div class="modal-overlay" id="preview-modal">
        <div class="modal-content" style="max-width: 900px; padding: 0; background: white;">
            <div class="preview-controls">
                <div style="font-weight: 600; font-size: 16px;">Preview Penawaran</div>
                <div class="theme-selectors">
                    <button class="theme-btn active" onclick="setDocTheme('theme-wedding', this)"><div class="theme-color-dot" style="background: #b76e79;"></div> Wedding Elegance</button>
                    <button class="theme-btn" onclick="setDocTheme('theme-navy', this)"><div class="theme-color-dot" style="background: #1b365d;"></div> Royal Navy</button>
                    <button class="theme-btn" onclick="setDocTheme('theme-minimal', this)"><div class="theme-color-dot" style="background: #222222;"></div> Clean Minimalist</button>
                </div>
                <div style="display: flex; gap: 8px;">
                    <button class="btn btn-outline" onclick="downloadDocPNG()"><i class="fa-solid fa-image"></i> PNG</button>
                    <button class="btn btn-primary" onclick="downloadDocPDF()"><i class="fa-solid fa-file-pdf"></i> PDF</button>
                    <button class="btn-icon" style="background: transparent; color: var(--text-muted);" onclick="closePreviewModal()"><i class="fa-solid fa-xmark"></i></button>
                </div>
            </div>
            
            <div class="preview-document-container">
                <div class="document-page theme-wedding" id="preview-document">
                    <!-- Top Border -->
                    <div style="position: absolute; top: 0; left: 0; right: 0; height: 8px; background: var(--doc-primary);"></div>
                    
                    <div class="doc-header">
                        <div class="doc-vendor">
                            <h1 id="doc_vendor_name">Penapict</h1>
                            <div class="doc-status" id="doc_status">Draft</div>
                        </div>
                        <div class="doc-meta">
                            <h2>PENAWARAN HARGA</h2>
                            <p style="letter-spacing: 2px;">QUOTATION</p>
                            <p class="doc-no-text" id="doc_no">No. QT-0000</p>
                            <p id="doc_dates">Terbit: - · Berlaku s/d -</p>
                        </div>
                    </div>
                    
                    <div class="doc-info-box">
                        <div class="doc-info-item">
                            <h4>NAMA KLIEN</h4>
                            <p id="doc_client_name">-</p>
                        </div>
                        <div class="doc-info-item">
                            <h4>WHATSAPP</h4>
                            <p id="doc_client_phone">-</p>
                        </div>
                        <div class="doc-info-item">
                            <h4>TANGGAL ACARA</h4>
                            <p id="doc_event_date">-</p>
                        </div>
                        <div class="doc-info-item full-width">
                            <h4>LOKASI / VENUE</h4>
                            <p id="doc_venue">-</p>
                        </div>
                    </div>
                    
                    <table class="doc-table">
                        <thead>
                            <tr>
                                <th style="width: 45%;">ITEM / LAYANAN</th>
                                <th style="text-align: center; width: 10%;">QTY</th>
                                <th style="text-align: right; width: 20%;">HARGA SATUAN</th>
                                <th style="text-align: right; width: 25%;">TOTAL</th>
                            </tr>
                        </thead>
                        <tbody id="doc_items_tbody">
                            <!-- Items rendered here -->
                        </tbody>
                    </table>
                    
                    <div class="doc-totals">
                        <div class="doc-total-row">
                            <span style="color: #666;">Subtotal</span>
                            <span style="font-weight: 600;" id="doc_subtotal">Rp 0</span>
                        </div>
                        <div class="doc-total-row">
                            <span style="color: #666;">Diskon</span>
                            <span style="font-weight: 600;" id="doc_discount">- Rp 0</span>
                        </div>
                        <div class="doc-total-row doc-grand-total">
                            <span>GRAND TOTAL</span>
                            <span id="doc_grand_total">Rp 0</span>
                        </div>
                    </div>
                    
                    <div class="doc-termins-title">JADWAL PEMBAYARAN</div>
                    <div class="doc-termins-grid" id="doc_termins_grid">
                        <!-- Termins here -->
                    </div>
                    
                    <div class="doc-tnc">
                        <h4>SYARAT & KETENTUAN</h4>
                        <div id="doc_tnc_text" style="white-space: pre-wrap;"></div>
                    </div>
                    
                    <div class="doc-signatures">
                        <div class="doc-sig-box">
                            <p>Hormat kami,</p>
                            <div class="sig-line"></div>
                            <div class="sig-name" id="doc_sig_vendor">Penapict</div>
                        </div>
                        <div class="doc-sig-box">
                            <p>Disetujui oleh,</p>
                            <div class="sig-line"></div>
                            <div class="sig-name" id="doc_sig_client">-</div>
                        </div>
                    </div>
                    
                    <!-- Bottom Border -->
                    <div style="position: absolute; bottom: 0; left: 0; right: 0; height: 8px; background: var(--doc-primary);"></div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script>
        const VENDOR_NAME = "{{ auth()->check() ? auth()->user()->name : 'Vendor' }}";
        // Sidebar Collapse
        document.getElementById('btn-collapse').addEventListener('click', function() {
            document.body.classList.toggle('sidebar-collapsed');
        });

        // Modal Logic
        const modal = document.getElementById('quotation-modal');
        const btnClose = document.getElementById('close-modal');
        const btnCancel = document.getElementById('btn-cancel-modal');

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
    <script src="/js/activity-logger.js"></script>
    <script src="/js/quotation.js?v=7"></script>

    <!-- =====================================================
         EXPORT PNG & PDF (satu halaman A4, tanpa offset)
         Dimuat SETELAH quotation.js sehingga menggantikan
         downloadDocPNG() dan downloadDocPDF() bawaan.
         Hanya mengubah cara render hasil download;
         logika simpan/hitung/preview tidak disentuh.
         ===================================================== -->
    <script>
    (function () {
        const PAGE_W = 794;    // lebar A4 @96dpi
        const PAGE_H = 1123;   // tinggi A4 @96dpi
        let exporting = false;

        function notify(msg) {
            try {
                if (typeof showToast === 'function') { showToast(msg); return; }
            } catch (e) {}
            console.log(msg);
        }

        function getFileBase() {
            const el = document.getElementById('doc_no');
            let name = el ? el.textContent.replace(/^\s*No\.?\s*/i, '').trim() : '';
            name = name.replace(/[^\w\-]+/g, '_');
            return 'Penawaran_' + (name || 'Quotation');
        }

        /**
         * Render dokumen ke canvas memakai KLON di luar modal:
         * - tanpa zoom / transform / scroll container → tidak ada offset
         * - lebar tetap 794px, posisi 0,0
         */
        async function renderDocCanvas() {
            const src = document.getElementById('preview-document');
            if (!src) throw new Error('Elemen preview tidak ditemukan');

            if (document.fonts && document.fonts.ready) { await document.fonts.ready; }

            const holder = document.createElement('div');
            holder.style.cssText = 'position:fixed;left:0;top:0;width:' + PAGE_W + 'px;background:#fff;z-index:-1;pointer-events:none;';

            const clone = src.cloneNode(true);
            clone.removeAttribute('id');
            clone.style.zoom = '1';
            clone.style.width = PAGE_W + 'px';
            clone.style.minHeight = PAGE_H + 'px';
            clone.style.margin = '0';
            clone.style.borderRadius = '0';
            clone.style.boxShadow = 'none';
            clone.style.transform = 'none';
            clone.style.overflow = 'hidden';

            holder.appendChild(clone);
            document.body.appendChild(holder);

            try {
                // beri waktu browser menata layout klon
                await new Promise(r => requestAnimationFrame(() => requestAnimationFrame(r)));

                const height = Math.max(PAGE_H, clone.scrollHeight);
                const canvas = await html2canvas(clone, {
                    scale: 2,
                    useCORS: true,
                    backgroundColor: '#ffffff',
                    width: PAGE_W,
                    height: height,
                    windowWidth: PAGE_W,
                    windowHeight: height,
                    x: 0,
                    y: 0,
                    scrollX: 0,
                    scrollY: 0,
                    logging: false
                });
                return canvas;
            } finally {
                holder.remove();
            }
        }

        function triggerDownload(href, filename) {
            const a = document.createElement('a');
            a.href = href;
            a.download = filename;
            document.body.appendChild(a);
            a.click();
            a.remove();
        }

        window.downloadDocPNG = async function () {
            if (exporting) return;
            exporting = true;
            try {
                notify('Menyiapkan PNG...');
                const canvas = await renderDocCanvas();
                canvas.toBlob(function (blob) {
                    const url = URL.createObjectURL(blob);
                    triggerDownload(url, getFileBase() + '.png');
                    setTimeout(() => URL.revokeObjectURL(url), 2000);
                }, 'image/png');
            } catch (err) {
                console.error(err);
                notify('Gagal membuat PNG');
            } finally {
                exporting = false;
            }
        };

        window.downloadDocPDF = async function () {
            if (exporting) return;
            exporting = true;
            try {
                notify('Menyiapkan PDF...');
                const canvas = await renderDocCanvas();
                const { jsPDF } = window.jspdf;
                const pdf = new jsPDF({ orientation: 'portrait', unit: 'mm', format: 'a4', compress: true });

                const pageW = pdf.internal.pageSize.getWidth();   // 210
                const pageH = pdf.internal.pageSize.getHeight();  // 297

                // Muat ke SATU halaman: skala mengikuti rasio, rata tengah
                let w = pageW;
                let h = canvas.height * pageW / canvas.width;
                if (h > pageH) {
                    h = pageH;
                    w = canvas.width * pageH / canvas.height;
                }
                const x = (pageW - w) / 2;

                pdf.addImage(canvas.toDataURL('image/png'), 'PNG', x, 0, w, h, undefined, 'FAST');
                pdf.save(getFileBase() + '.pdf');
            } catch (err) {
                console.error(err);
                notify('Gagal membuat PDF');
            } finally {
                exporting = false;
            }
        };
    })();
    </script>
</body>
</html>