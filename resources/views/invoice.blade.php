<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Invoice Generator - CloseMateAI</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
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

        /* Invoice Themes — REFINED TO MATCH REFERENCE IMAGES 1-5 */
        .invoice-paper {
            background: #ffffff;
            padding: 40px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            border-radius: 12px;
            margin-top: 20px;
            position: relative;
            overflow: hidden;
            color: #1e293b;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            transition: all 0.25s ease;
        }

        .invoice-paper .inv-desc-row {
            margin-bottom: 24px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }
        .invoice-paper .inv-table {
            margin-bottom: 16px;
            width: 100%;
            border-collapse: collapse;
        }
        .invoice-paper .inv-totals {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 24px;
            padding-right: 4px;
        }
        .invoice-paper .inv-payment {
            border-radius: 12px;
            padding: 16px 20px;
            margin-bottom: 24px;
        }

        /* --------------------------------------------------------
           1. MINIMALIS & CLEAN (IMAGE 1)
           -------------------------------------------------------- */
        .invoice-paper.theme-minimalist {
            background: #ffffff;
            color: #1e293b;
        }
        .invoice-paper.theme-minimalist .inv-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 20px;
            margin-bottom: 22px;
        }
        .invoice-paper.theme-minimalist .inv-header-brand {
            display: flex;
            align-items: center;
            gap: 16px;
        }
        .invoice-paper.theme-minimalist .inv-logo-text {
            color: #1e293b;
            font-size: 19px;
            font-weight: 700;
            margin-bottom: 2px;
        }
        .invoice-paper.theme-minimalist .inv-addr-text {
            color: #64748b;
            font-size: 11px;
        }
        .invoice-paper.theme-minimalist .inv-meta {
            text-align: right;
        }
        .invoice-paper.theme-minimalist #previewInvoiceTitle {
            color: #475569;
            font-size: 24px;
            font-weight: 400;
            letter-spacing: 2px;
            margin: 0 0 4px 0;
        }
        .invoice-paper.theme-minimalist .inv-meta p {
            color: #64748b;
            font-size: 11px;
            margin: 0 0 2px 0;
        }
        .invoice-paper.theme-minimalist #labelPreviewKepada,
        .invoice-paper.theme-minimalist #labelPreviewTglAcara {
            color: #94a3b8;
            font-size: 9px;
            font-weight: 700;
            letter-spacing: 0.5px;
        }
        .invoice-paper.theme-minimalist #previewTerminLabel {
            color: #334155;
            font-size: 11px;
            font-weight: 500;
        }
        .invoice-paper.theme-minimalist .inv-table th {
            background: #f8fafc;
            color: #475569;
            font-weight: 700;
            font-size: 10px;
            letter-spacing: 0.5px;
            padding: 10px 16px;
            border: none;
        }
        .invoice-paper.theme-minimalist .inv-table td {
            padding: 14px 16px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 12px;
        }
        .invoice-paper.theme-minimalist #labelPreviewGrandTotal,
        .invoice-paper.theme-minimalist #previewGrandTotal {
            color: #1e293b;
            font-weight: 700;
        }
        .invoice-paper.theme-minimalist #previewDpBox {
            background: #f8fafc;
            border-radius: 12px;
            padding: 12px 16px;
            margin-top: 14px;
        }
        .invoice-paper.theme-minimalist #labelPreviewSisaTagihan {
            color: #64748b;
            font-size: 11px;
        }
        .invoice-paper.theme-minimalist #previewSisaTagihan {
            color: #1e293b;
            font-size: 14px;
            font-weight: 700;
        }
        .invoice-paper.theme-minimalist #previewPaymentBox {
            border: 1px solid #f1f5f9;
            background: #ffffff;
        }
        .invoice-paper.theme-minimalist #labelPreviewMetodeBayar {
            color: #94a3b8;
            font-size: 9px;
            font-weight: 700;
            letter-spacing: 0.5px;
        }
        .invoice-paper.theme-minimalist #previewPaymentText {
            color: #1e293b;
            font-size: 13px;
            font-weight: 600;
        }
        .invoice-paper.theme-minimalist #labelPreviewTermsHeader {
            color: #475569;
            font-weight: 700;
            font-size: 10px;
        }

        /* --------------------------------------------------------
           2. CREATIVE & AESTHETIC (IMAGE 2)
           -------------------------------------------------------- */
        .invoice-paper.theme-creative {
            background: #ffffff;
            color: #374151;
        }
        .invoice-paper.theme-creative .inv-header {
            margin: -40px -40px 24px -40px;
            padding: 34px 40px 22px 40px;
            background: linear-gradient(180deg, #fcecee 0%, #fef5f7 75%, #ffffff 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            border-bottom: none;
        }
        .invoice-paper.theme-creative .inv-header-brand {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            gap: 0;
        }
        .invoice-paper.theme-creative #previewLogoContainer {
            margin: 0 auto 10px auto;
            width: 56px;
            height: 56px;
            border-radius: 50%;
            border: 2px solid #f8ccd7;
        }
        .invoice-paper.theme-creative .inv-logo-text {
            color: #d66d85;
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 2px;
        }
        .invoice-paper.theme-creative .inv-addr-text {
            color: #888888;
            font-size: 11px;
            margin-bottom: 12px;
        }
        .invoice-paper.theme-creative .inv-meta {
            text-align: center;
        }
        .invoice-paper.theme-creative #previewInvoiceTitle {
            color: #d66d85;
            font-size: 24px;
            font-weight: 400;
            letter-spacing: 8px;
            margin: 4px 0 6px 0;
        }
        .invoice-paper.theme-creative .inv-meta p {
            color: #999999;
            font-size: 11px;
            margin: 0 0 2px 0;
        }
        .invoice-paper.theme-creative #labelPreviewKepada,
        .invoice-paper.theme-creative #labelPreviewTglAcara {
            color: #a3a3a3;
            font-size: 9px;
            font-weight: 700;
            letter-spacing: 0.5px;
        }
        .invoice-paper.theme-creative #previewTerminLabel {
            color: #d66d85;
            font-size: 11px;
            font-weight: 600;
        }
        .invoice-paper.theme-creative .inv-table th {
            background: #fdf0f2;
            color: #d66d85;
            font-weight: 700;
            font-size: 10px;
            letter-spacing: 0.5px;
            padding: 10px 16px;
            border: none;
        }
        .invoice-paper.theme-creative .inv-table td {
            padding: 14px 16px;
            border-bottom: 1px solid #fde8ed;
            font-size: 12px;
        }
        .invoice-paper.theme-creative #labelPreviewGrandTotal,
        .invoice-paper.theme-creative #previewGrandTotal {
            color: #374151;
            font-weight: 700;
        }
        .invoice-paper.theme-creative #previewDpBox {
            background: #fdf2f4;
            border-radius: 12px;
            padding: 12px 16px;
            margin-top: 14px;
        }
        .invoice-paper.theme-creative #labelPreviewSisaTagihan {
            color: #d66d85;
            font-size: 11px;
            font-weight: 600;
        }
        .invoice-paper.theme-creative #previewSisaTagihan {
            color: #d66d85;
            font-size: 14px;
            font-weight: 700;
        }
        .invoice-paper.theme-creative #previewPaymentBox {
            border: 1px solid #fce7eb;
            background: #ffffff;
        }
        .invoice-paper.theme-creative #labelPreviewMetodeBayar {
            color: #a3a3a3;
            font-size: 9px;
            font-weight: 700;
            letter-spacing: 0.5px;
        }
        .invoice-paper.theme-creative #previewPaymentText {
            color: #374151;
            font-size: 13px;
            font-weight: 600;
        }
        .invoice-paper.theme-creative #labelPreviewTermsHeader {
            color: #d66d85;
            font-weight: 700;
            font-size: 10px;
        }

        /* --------------------------------------------------------
           3. CORPORATE & PROFESSIONAL (IMAGE 3)
           -------------------------------------------------------- */
        .invoice-paper.theme-corporate {
            background: #ffffff;
            color: #1e293b;
        }
        .invoice-paper.theme-corporate .inv-header {
            margin: -40px -40px 24px -40px;
            padding: 30px 40px;
            background: #1e3a5f;
            color: #ffffff;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-radius: 12px 12px 0 0;
            border-bottom: none;
        }
        .invoice-paper.theme-corporate .inv-header-brand {
            display: flex;
            align-items: center;
            gap: 16px;
        }
        .invoice-paper.theme-corporate #previewLogoContainer {
            width: 52px;
            height: 52px;
            border-radius: 50%;
            border: 2px solid rgba(255,255,255,0.4);
        }
        .invoice-paper.theme-corporate .inv-logo-text {
            color: #ffffff;
            font-size: 18px;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin-bottom: 2px;
        }
        .invoice-paper.theme-corporate .inv-addr-text {
            color: #cbd5e1;
            font-size: 11px;
        }
        .invoice-paper.theme-corporate .inv-meta {
            text-align: right;
        }
        .invoice-paper.theme-corporate #previewInvoiceTitle {
            color: #ffffff;
            font-size: 22px;
            font-weight: 500;
            letter-spacing: 3px;
            text-transform: uppercase;
            margin: 0 0 4px 0;
        }
        .invoice-paper.theme-corporate .inv-meta p {
            color: #cbd5e1;
            font-size: 11px;
            margin: 0 0 2px 0;
        }
        .invoice-paper.theme-corporate #labelPreviewKepada,
        .invoice-paper.theme-corporate #labelPreviewTglAcara {
            color: #64748b;
            font-size: 9px;
            font-weight: 700;
            letter-spacing: 0.5px;
        }
        .invoice-paper.theme-corporate #previewTerminLabel {
            color: #1e3a5f;
            font-size: 11px;
            font-weight: 600;
        }
        .invoice-paper.theme-corporate .inv-table th {
            background: #edf2f7;
            color: #1e3a5f;
            font-weight: 700;
            font-size: 10px;
            letter-spacing: 0.5px;
            padding: 10px 16px;
            border: none;
        }
        .invoice-paper.theme-corporate .inv-table td {
            padding: 14px 16px;
            border-bottom: 1px solid #edf2f7;
            font-size: 12px;
        }
        .invoice-paper.theme-corporate #labelPreviewGrandTotal,
        .invoice-paper.theme-corporate #previewGrandTotal {
            color: #1e3a5f;
            font-weight: 700;
        }
        .invoice-paper.theme-corporate #previewDpBox {
            background: #edf2f7;
            border-radius: 12px;
            padding: 12px 16px;
            margin-top: 14px;
        }
        .invoice-paper.theme-corporate #labelPreviewSisaTagihan {
            color: #1e3a5f;
            font-size: 11px;
            font-weight: 600;
        }
        .invoice-paper.theme-corporate #previewSisaTagihan {
            color: #1e3a5f;
            font-size: 14px;
            font-weight: 700;
        }
        .invoice-paper.theme-corporate #previewPaymentBox {
            border: 1px solid #e2e8f0;
            background: #ffffff;
        }
        .invoice-paper.theme-corporate #labelPreviewMetodeBayar {
            color: #64748b;
            font-size: 9px;
            font-weight: 700;
            letter-spacing: 0.5px;
        }
        .invoice-paper.theme-corporate #previewPaymentText {
            color: #1e293b;
            font-size: 13px;
            font-weight: 600;
        }
        .invoice-paper.theme-corporate #labelPreviewTermsHeader {
            color: #1e3a5f;
            font-weight: 700;
            font-size: 10px;
        }

        /* --------------------------------------------------------
           4. MODERN & BOLD (IMAGE 4)
           -------------------------------------------------------- */
        .invoice-paper.theme-modern {
            background: #ffffff;
            color: #111827;
        }
        .invoice-paper.theme-modern .inv-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 3px solid #111827;
            padding-bottom: 16px;
            margin-bottom: 24px;
        }
        .invoice-paper.theme-modern .inv-header-brand {
            display: flex;
            align-items: center;
            gap: 16px;
        }
        .invoice-paper.theme-modern .inv-logo-text {
            color: #111827;
            font-size: 19px;
            font-weight: 900;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin-bottom: 2px;
        }
        .invoice-paper.theme-modern .inv-addr-text {
            color: #4b5563;
            font-size: 11px;
        }
        .invoice-paper.theme-modern .inv-meta {
            text-align: right;
        }
        .invoice-paper.theme-modern #previewInvoiceTitle {
            color: #111827;
            font-size: 28px;
            font-weight: 900;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin: 0 0 4px 0;
        }
        .invoice-paper.theme-modern .inv-meta p {
            color: #4b5563;
            font-size: 11px;
            font-weight: 500;
            margin: 0 0 2px 0;
        }
        .invoice-paper.theme-modern #labelPreviewKepada,
        .invoice-paper.theme-modern #labelPreviewTglAcara {
            color: #6b7280;
            font-size: 9px;
            font-weight: 700;
            letter-spacing: 0.5px;
        }
        .invoice-paper.theme-modern #previewTerminLabel {
            color: #111827;
            font-size: 11px;
            font-weight: 700;
        }
        .invoice-paper.theme-modern .inv-table th {
            background: #111827;
            color: #ffffff;
            font-weight: 700;
            font-size: 10px;
            letter-spacing: 0.5px;
            padding: 10px 16px;
            border: none;
        }
        .invoice-paper.theme-modern .inv-table td {
            padding: 14px 16px;
            border-bottom: 1px solid #e4e4e7;
            font-size: 12px;
        }
        .invoice-paper.theme-modern #labelPreviewGrandTotal,
        .invoice-paper.theme-modern #previewGrandTotal {
            color: #111827;
            font-weight: 800;
        }
        .invoice-paper.theme-modern #previewDpBox {
            background: #f4f4f5;
            border-radius: 12px;
            padding: 12px 16px;
            margin-top: 14px;
        }
        .invoice-paper.theme-modern #labelPreviewSisaTagihan {
            color: #18181b;
            font-size: 11px;
            font-weight: 700;
        }
        .invoice-paper.theme-modern #previewSisaTagihan {
            color: #18181b;
            font-size: 14px;
            font-weight: 800;
        }
        .invoice-paper.theme-modern #previewPaymentBox {
            border: 1px solid #e4e4e7;
            background: #ffffff;
        }
        .invoice-paper.theme-modern #labelPreviewMetodeBayar {
            color: #71717a;
            font-size: 9px;
            font-weight: 700;
            letter-spacing: 0.5px;
        }
        .invoice-paper.theme-modern #previewPaymentText {
            color: #18181b;
            font-size: 13px;
            font-weight: 700;
        }
        .invoice-paper.theme-modern #labelPreviewTermsHeader {
            color: #111827;
            font-weight: 800;
            font-size: 10px;
        }

        /* --------------------------------------------------------
           5. CLASSIC / TRADITIONAL (IMAGE 5)
           -------------------------------------------------------- */
        .invoice-paper.theme-classic {
            background: #ffffff;
            color: #3e3223;
        }
        .invoice-paper.theme-classic .inv-header {
            margin: -40px -40px 24px -40px;
            padding: 34px 40px 24px 40px;
            background: #f5ede0;
            border-radius: 12px 12px 0 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            border-bottom: none;
            font-family: 'Playfair Display', Georgia, serif;
        }
        .invoice-paper.theme-classic .inv-header-brand {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            gap: 0;
        }
        .invoice-paper.theme-classic #previewLogoContainer {
            margin: 0 auto 10px auto;
            width: 56px;
            height: 56px;
            border-radius: 50%;
            border: 2px solid #e0d0bc;
        }
        .invoice-paper.theme-classic .inv-logo-text {
            color: #6e4d25;
            font-size: 22px;
            font-weight: 600;
            font-family: 'Playfair Display', Georgia, serif;
            margin-bottom: 2px;
        }
        .invoice-paper.theme-classic .inv-addr-text {
            color: #8c7860;
            font-size: 11px;
            margin-bottom: 12px;
        }
        .invoice-paper.theme-classic .inv-meta {
            text-align: center;
        }
        .invoice-paper.theme-classic #previewInvoiceTitle {
            color: #6e4d25;
            font-size: 22px;
            font-weight: 400;
            letter-spacing: 8px;
            font-family: 'Playfair Display', Georgia, serif;
            margin: 4px 0 6px 0;
        }
        .invoice-paper.theme-classic .inv-meta p {
            color: #9c8a74;
            font-size: 11px;
            margin: 0 0 2px 0;
        }
        .invoice-paper.theme-classic #labelPreviewKepada,
        .invoice-paper.theme-classic #labelPreviewTglAcara {
            color: #8c7860;
            font-size: 9px;
            font-weight: 700;
            letter-spacing: 0.5px;
        }
        .invoice-paper.theme-classic #previewTerminLabel {
            color: #6e4d25;
            font-size: 11px;
            font-weight: 600;
        }
        .invoice-paper.theme-classic .inv-table th {
            background: #f3ebd9;
            color: #6e4d25;
            font-weight: 700;
            font-size: 10px;
            letter-spacing: 0.5px;
            padding: 10px 16px;
            border: none;
        }
        .invoice-paper.theme-classic .inv-table td {
            padding: 14px 16px;
            border-bottom: 1px solid #f0e5d0;
            font-size: 12px;
        }
        .invoice-paper.theme-classic #labelPreviewGrandTotal,
        .invoice-paper.theme-classic #previewGrandTotal {
            color: #3e3223;
            font-weight: 700;
        }
        .invoice-paper.theme-classic #previewDpBox {
            background: #f5ede0;
            border-radius: 12px;
            padding: 12px 16px;
            margin-top: 14px;
        }
        .invoice-paper.theme-classic #labelPreviewSisaTagihan {
            color: #6e4d25;
            font-size: 11px;
            font-weight: 600;
        }
        .invoice-paper.theme-classic #previewSisaTagihan {
            color: #6e4d25;
            font-size: 14px;
            font-weight: 700;
        }
        .invoice-paper.theme-classic #previewPaymentBox {
            border: 1px solid #eee4cd;
            background: #ffffff;
        }
        .invoice-paper.theme-classic #labelPreviewMetodeBayar {
            color: #8c7860;
            font-size: 9px;
            font-weight: 700;
            letter-spacing: 0.5px;
        }
        .invoice-paper.theme-classic #previewPaymentText {
            color: #3e3223;
            font-size: 13px;
            font-weight: 600;
        }
        .invoice-paper.theme-classic #labelPreviewTermsHeader {
            color: #6e4d25;
            font-weight: 700;
            font-size: 10px;
        }
        
        /* Responsive Mobile Layout for Invoice Generator */
        @media (max-width: 992px) {
            .invoice-grid { grid-template-columns: 1fr !important; gap: 20px !important; }
            .top-actions .status-pill { display: none !important; }
            .actions-row { flex-wrap: wrap; gap: 12px; }
            .design-options { flex-wrap: nowrap; overflow-x: auto; padding-bottom: 8px; -webkit-overflow-scrolling: touch; }
            .design-card { min-width: 140px; flex-shrink: 0; }
        }

        @media (max-width: 768px) {
            .main-content {
                margin-left: 0 !important;
                padding: 135px 16px 24px 16px !important;
                width: 100% !important;
                min-height: 100vh !important;
                height: auto !important;
            }
            .page-title h1 { font-size: 18px; margin-bottom: 2px; }
            .page-title p { font-size: 12px; }
            .actions-row {
                display: flex !important;
                justify-content: space-between !important;
                align-items: center !important;
                margin-bottom: 20px !important;
                gap: 10px !important;
            }
            .form-row:not(.form-row-2col) {
                grid-template-columns: 1fr !important;
                gap: 16px !important;
            }
            .form-row.form-row-2col {
                grid-template-columns: 1fr 1fr !important;
                gap: 12px !important;
            }
            .invoice-paper {
                padding: 24px 16px !important;
                border-radius: 12px !important;
                box-shadow: 0 2px 12px rgba(0,0,0,0.06) !important;
            }
            .inv-header {
                flex-direction: row !important;
                justify-content: space-between !important;
                align-items: flex-start !important;
                gap: 12px !important;
            }
            .inv-desc-row {
                flex-direction: row !important;
                justify-content: space-between !important;
                align-items: flex-start !important;
            }
            .inv-totals {
                padding-right: 0 !important;
                justify-content: flex-end !important;
            }
            .inv-totals > div {
                width: 100% !important;
                max-width: 320px !important;
            }
            .inv-action-btns {
                flex-wrap: wrap !important;
                gap: 10px !important;
            }
            .inv-action-btns .btn {
                flex: 1 1 calc(50% - 10px) !important;
            }
            .inv-action-btns .btn-full {
                flex: 1 1 100% !important;
            }
        }

        /* Date filter buttons & Calendar Popover */
        .date-filter-btn {
            background: white;
            border: 1px solid var(--border-color);
            color: #475569;
            border-radius: 8px;
            height: 40px;
            padding: 0 16px;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            transition: 0.15s;
            white-space: nowrap;
        }
        .date-filter-btn:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
        }
        .date-filter-btn.active {
            background: #6b5cd8 !important;
            color: white !important;
            border-color: #6b5cd8 !important;
            font-weight: 600;
        }
        .cal-day-btn {
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            cursor: pointer;
            font-size: 12px;
            color: #1e293b;
            user-select: none;
            transition: background 0.1s;
            margin: auto;
        }
        .cal-day-btn:hover:not(.empty):not(.range-endpoint) {
            background: #f1f5f9;
        }
        .cal-day-btn.empty {
            cursor: default;
            visibility: hidden;
        }
        .cal-day-btn.range-endpoint {
            background: #6b5cd8 !important;
            color: white !important;
            font-weight: 600;
        }
        .cal-day-cell-wrapper {
            position: relative;
            padding: 2px 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .cal-day-cell-wrapper.in-range {
            background: #ede9fe;
        }
        .cal-day-cell-wrapper.range-start-wrap {
            border-top-left-radius: 16px;
            border-bottom-left-radius: 16px;
            background: #ede9fe;
        }
        .cal-day-cell-wrapper.range-end-wrap {
            border-top-right-radius: 16px;
            border-bottom-right-radius: 16px;
            background: #ede9fe;
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
            <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                <button type="button" class="btn btn-primary" id="tabBtnInvoiceBaru" onclick="switchView('baru')"><i class="fa-solid fa-plus"></i> Invoice Baru</button>
                <button type="button" class="btn btn-secondary" id="tabBtnInvoiceTersimpan" onclick="switchView('tersimpan')"><i class="fa-regular fa-folder-open"></i> <span id="savedInvoiceCountText">Invoice Tersimpan (0)</span></button>
            </div>
            
            <div style="display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-language" style="color: #64748b; font-size: 16px;"></i>
                <div class="btn-group">
                    <button type="button" class="btn active" id="btnLangId" onclick="setLanguage('id')">ID</button>
                    <button type="button" class="btn" id="btnLangEn" onclick="setLanguage('en')">EN</button>
                </div>
            </div>
        </div>
        
        <div class="invoice-grid" id="invoiceEditorView">
            
            <!-- LEFT COLUMN: Forms -->
            <div>
                <!-- Panel 1: Informasi Bisnis -->
                <div class="acc-panel">
                    <div class="acc-header" id="panelTitleBisnis">
                        Informasi Bisnis
                        <i class="fa-solid fa-chevron-up"></i>
                    </div>
                    <div class="acc-body">
                        <label class="form-label" id="labelLogoVendor">Logo Vendor (JPG/PNG, maks 5MB)</label>
                        <div id="logoUploadContainer" style="display: flex; gap: 12px; align-items: center; margin-bottom: 16px;">
                            <div id="logoPreviewThumbnail" style="width: 44px; height: 44px; min-width: 44px; border: 1px solid var(--border-color); border-radius: 8px; display: flex; align-items: center; justify-content: center; background: #f8f9fa; overflow: hidden;">
                                <img id="logoImgThumbnail" src="" style="display: none; max-width: 100%; max-height: 100%; object-fit: contain;">
                                <i class="fa-regular fa-image" id="logoIconPlaceholder" style="color: #a1a5b7; font-size: 20px;"></i>
                            </div>
                            <div class="btn btn-secondary upload-btn-box" id="btnUploadBox" style="flex: 1; height: 44px; display: flex; align-items: center; justify-content: center; border: 1px solid var(--border-color); border-radius: 8px; color: var(--text-dark); font-size: 13px; font-weight: 500; background: white; gap: 8px; cursor: pointer;" onclick="document.getElementById('logoInput').click()">
                                <i class="fa-solid fa-arrow-up-from-bracket"></i> <span id="textUploadLogo">Unggah Logo</span>
                            </div>
                            <span id="btnHapusLogo" style="display: none; color: var(--text-muted); cursor: pointer; font-size: 13px; margin-left: 4px;" onclick="hapusLogo()">Hapus</span>
                            <input type="file" id="logoInput" style="display: none;" accept="image/png, image/jpeg" onchange="previewLogoFile(this)">
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label" id="labelNamaVendor">Nama Vendor</label>
                            <input type="text" class="form-control" value="Fariz hidayat" id="inputNamaVendor" oninput="syncPreview('inputNamaVendor', 'previewNamaVendor'); syncPreview('inputNamaVendor', 'previewTtdNama'); updateWaDraft();">
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label" id="labelAlamatVendor">Alamat Perusahaan</label>
                            <input type="text" class="form-control" value="Kombas purwokerto" id="inputAlamatVendor" oninput="syncPreview('inputAlamatVendor', 'previewAlamatVendor')">
                        </div>
                        
                        <div class="form-group" style="margin-bottom: 8px;">
                            <label class="form-label" id="labelNoInvoiceKustom">Nomor Invoice (Kustom)</label>
                            <input type="text" class="form-control" value="INV-202610-006" id="inputNoInvoice" oninput="syncPreview('inputNoInvoice', 'previewNoInvoice'); updateWaDraft();">
                        </div>
                        
                        <div class="info-text" style="margin-bottom: 16px;"><span id="infoAutoInvoicePrefix">Kosongkan untuk memakai nomor otomatis berurutan: </span><strong id="previewAutoInvoice">INV-202610-006</strong></div>
                        
                        <div class="form-row form-group" style="align-items: flex-end;">
                            <div>
                                <select class="form-control" id="inputFormatInvoice">
                                    <option value="INV-{YYYY}{MM}-{SEQ3}">INV-{YYYY}{MM}-{SEQ3}</option>
                                    <option value="INV-{YYYY}{MM}-{SEQ4}">INV-{YYYY}{MM}-{SEQ4}</option>
                                    <option value="INV-{YY}{MM}{DD}-{SEQ3}">INV-{YY}{MM}{DD}-{SEQ3}</option>
                                </select>
                            </div>
                            <div>
                                <button class="btn btn-secondary" style="width: 100%; border: 1px solid var(--primary); color: var(--primary);" id="btnSimpanFormat" onclick="simpanFormat()"><i class="fa-solid fa-check" id="iconFormat" style="display:none; margin-right:4px;"></i> <span id="textFormat">Simpan Format</span></button>
                            </div>
                        </div>
                        <div class="info-text" style="font-size: 10px; color: #aaa; margin-top: -8px; margin-bottom: 16px;">{YYYY} {YY} {MM} {DD} {SEQ} {SEQ3} {SEQ4} {SEQ5}</div>
                        
                        <button class="btn btn-secondary" style="width: 100%; border: 1px solid var(--border-color); font-weight: 500;" id="btnSimpanBisnis" onclick="simpanBisnis()"><i class="fa-regular fa-floppy-disk" id="iconBisnis"></i> <span id="textBisnis">Simpan Informasi Bisnis</span></button>
                    </div>
                </div>

                <!-- Panel 2: Informasi Klien -->
                <div class="acc-panel">
                    <div class="acc-header" id="panelTitleKlien">
                        Informasi Klien
                        <i class="fa-solid fa-chevron-up"></i>
                    </div>
                    <div class="acc-body">
                        <div id="bookingConnectedBox" style="display: none; padding: 14px 16px; background: rgba(107, 92, 216, 0.06); border: 1px solid rgba(107, 92, 216, 0.2); border-radius: 10px; margin-bottom: 16px;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                                <span style="font-weight: 600; font-size: 13px; color: var(--text-dark);" id="bookingConnectedText">Terhubung ke Booking: -</span>
                                <span style="color: #ef4444; font-size: 12px; font-weight: 600; cursor: pointer;" onclick="putuskanBooking()">Putuskan Hubungan</span>
                            </div>
                            <div style="font-size: 12px; color: var(--text-muted);">Pembayaran yang dicatat di invoice ini otomatis memperbarui status pembayaran booking.</div>
                        </div>
                        
                        <button class="btn btn-primary" style="border-radius: 8px; width: fit-content; padding: 10px 16px; margin-bottom: 6px; font-size: 13px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;" id="btnAmbilBooking" onclick="openBookingModal()"><i class="fa-regular fa-calendar-check"></i> <span id="btnAmbilBookingText">Ambil dari Booking</span></button>
                        <div class="info-text" style="margin-bottom: 16px;" id="infoAmbilBooking">Isi otomatis nama, WhatsApp, alamat, tanggal acara, paket, diskon, dan add-on dari data Booking & Operasional.</div>
                        
                        <div class="form-group">
                            <label class="form-label" id="labelNamaKlien">Nama Klien (Pengantin)</label>
                            <input type="text" class="form-control" value="Rian & Amel" id="inputNamaKlien" oninput="syncPreviewClientWa(); updateWaDraft();">
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label" id="labelNoWaKlien">No WhatsApp Klien</label>
                            <input type="text" class="form-control" value="081234567890" id="inputNoWaKlien" oninput="syncPreviewClientWa(); updateWaDraft();">
                            <div id="helperWaKlien" style="font-size: 11px; color: #a1a5b7; margin-top: 4px;">+6281234567890</div>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label" id="labelAlamatKlien">Alamat Klien (opsional)</label>
                            <input type="text" class="form-control" value="" placeholder="Alamat lengkap klien" id="inputAlamatKlien" oninput="syncPreviewClientWa()">
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label" id="labelTglAcara">Tanggal Acara</label>
                            <div style="position: relative;">
                                <input type="date" class="form-control" id="inputTglAcara" value="" onchange="syncPreviewDate(); updateWaDraft();" oninput="syncPreviewDate(); updateWaDraft();" style="cursor: pointer;">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Panel 3: Rincian Paket & Biaya -->
                <div class="acc-panel">
                    <div class="acc-header" id="panelTitlePaket">
                        Rincian Paket & Biaya
                        <i class="fa-solid fa-chevron-up"></i>
                    </div>
                    <div class="acc-body">
                        <div class="form-group" style="position: relative;">
                            <label class="form-label" id="labelPaketUtama">Nama Paket Pernikahan</label>
                            <div style="position: relative;">
                                <input type="text" class="form-control" value="Premium Wedding Documentation" id="inputPaketUtama" oninput="calculateTotals(); updateWaDraft();" style="padding-right: 40px;">
                                <div id="btnTogglePaketDropdown" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); color: var(--text-dark); cursor: pointer; padding: 4px;" onclick="togglePaketDropdown(event)">
                                    <i class="fa-solid fa-chevron-down" style="font-size: 13px;"></i>
                                </div>
                            </div>
                            <!-- Floating Dropdown Menu Paket Tersimpan -->
                            <div id="paketDropdownMenu" style="display: none; position: absolute; left: 0; right: 0; top: calc(100% + 4px); background: #ffffff; border: 1px solid var(--border-color); border-radius: 10px; box-shadow: 0 10px 25px rgba(0,0,0,0.12); z-index: 1000; max-height: 280px; overflow-y: auto; padding: 6px;"></div>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label" id="labelHargaUtama">Harga Paket Utama</label>
                            <input type="text" class="form-control" value="Rp 10.000.000" id="inputHargaUtama" oninput="handleCurrencyInput(this); calculateTotals();">
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label" id="labelJumlahPaket">Jumlah Paket</label>
                            <input type="number" class="form-control" value="1" min="1" id="inputJumlahPaket" oninput="calculateTotals();">
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label" id="labelDiskon">Diskon / Potongan</label>
                            <input type="text" class="form-control" value="Rp 500.000" id="inputDiskon" oninput="handleCurrencyInput(this); calculateTotals();">
                        </div>

                        <div id="paketTambahanContainer"></div>

                        <button class="btn btn-secondary" style="border: 1px solid var(--border-color); font-weight: 500; padding: 10px 16px; margin-bottom: 8px; display: inline-flex; align-items: center; gap: 8px;" id="btnSimpanBiaya" onclick="simpanBiaya()"><i class="fa-regular fa-floppy-disk" id="iconBiaya"></i> <span id="textBiaya">Simpan Paket & Harga</span></button>
                        <div class="info-text" style="margin-bottom: 16px;" id="infoSimpanBiaya">Tersimpan di perangkat ini, bisa dipilih lagi lewat tombol panah di kolom nama paket.</div>
                        
                        <button type="button" class="btn btn-outline-primary form-group" onclick="addPaketTambahan()"><i class="fa-solid fa-plus"></i> <span id="textTambahPaket">Tambah Paket</span></button>
                        
                        <div class="info-text bg form-group" style="display: flex; justify-content: space-between; font-weight: 500; align-items: center; padding: 12px 16px; border-radius: 8px;">
                            <div style="display: flex; flex-direction: column; gap: 4px;">
                                <span style="font-size: 11px; color: var(--text-muted);" id="labelCalcSubtotal">Subtotal Paket</span>
                                <span id="calcSubtotal" style="font-weight: 600;">Rp 9.500.000</span>
                            </div>
                            <div style="display: flex; flex-direction: column; gap: 4px;">
                                <span style="font-size: 11px; color: var(--text-muted);" id="labelCalcAddon">Total Add-on</span>
                                <span id="calcAddon" style="font-weight: 600;">Rp 0</span>
                            </div>
                            <div style="display: flex; flex-direction: column; gap: 4px; text-align: right;">
                                <span style="font-size: 11px; color: var(--text-muted);" id="labelCalcGrandTotal">GRAND TOTAL</span>
                                <span id="calcGrandTotal" style="color: var(--primary); font-size: 16px; font-weight: 700;">Rp 9.500.000</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Panel 4: Add-on Tambahan -->
                <div class="acc-panel">
                    <div class="acc-header" id="panelTitleAddon">
                        Add-on Tambahan
                        <i class="fa-solid fa-chevron-up"></i>
                    </div>
                    <div class="acc-body">
                        <div id="addonContainer"></div>
                        
                        <button type="button" class="btn btn-outline-primary" style="width: 100%; border: 1px dashed var(--primary); color: var(--primary); background: transparent; padding: 12px; border-radius: 8px; font-weight: 500;" onclick="addAddonItem()"><i class="fa-solid fa-plus"></i> <span id="textTambahAddon">Tambah Item Add-on</span></button>
                    </div>
                </div>
                
                <!-- Panel 5: Termin & Pembayaran -->
                <div class="acc-panel">
                    <div class="acc-header" id="panelTitleTermin">
                        Termin & Pembayaran
                        <i class="fa-solid fa-chevron-up"></i>
                    </div>
                    <div class="acc-body">
                        <div class="form-row form-group" style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 12px;">
                            <div>
                                <label class="form-label" id="labelTerminSaatIni">Termin Saat Ini</label>
                                <select class="form-control" id="selectTermin" onchange="syncTerminPreview(); updateWaDraft();">
                                    <option value="Uang Muka / DP 1" id="optTermin1" selected>Uang Muka / DP 1</option>
                                    <option value="Termin 2 / DP 2" id="optTermin2">Termin 2 / DP 2</option>
                                    <option value="Pelunasan / Final Payment" id="optTermin3">Pelunasan / Final Payment</option>
                                </select>
                            </div>
                            <div id="wrapperDP1">
                                <label class="form-label" id="labelNominalDP1">Nominal DP 1</label>
                                <input type="text" class="form-control" value="Rp 3.000.000" id="inputDP1" oninput="handleCurrencyInput(this); calculateTotals();">
                            </div>
                        </div>
                        
                        <div class="form-group" id="wrapperDP2">
                            <label class="form-label" id="labelNominalDP2">Nominal DP 2</label>
                            <input type="text" class="form-control" value="Rp 0" id="inputDP2" oninput="handleCurrencyInput(this); calculateTotals();">
                        </div>
                        
                        <div class="termin-summary-card" id="cardTerminSummary" style="background: #f8f9fa; border: 1px solid var(--border-color); border-radius: 8px; padding: 14px 18px; margin: 16px 0; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <span style="color: var(--text-dark); font-weight: 500; font-size: 13px;" id="labelTerminSummary">Total DP 1</span>
                                <span style="color: var(--text-dark); font-weight: 700; font-size: 14px;" id="valTerminSummary">Rp 3.000.000</span>
                            </div>
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <span style="color: var(--text-dark); font-weight: 500; font-size: 13px;" id="labelSisaSummary">Sisa Tagihan</span>
                                <span style="color: var(--text-dark); font-weight: 700; font-size: 14px;" id="valSisaSummary">Rp 6.500.000</span>
                            </div>
                        </div>

                        <!-- Card DP Sebelumnya (khusus ketika Pelunasan dipilih) -->
                        <div id="cardDpSebelumnya" style="display: none; border: 1px solid #e2e8f0; border-radius: 10px; padding: 16px; margin: 16px 0; background: #fff;">
                            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; margin-bottom: 8px;">
                                <h5 style="margin: 0; font-size: 14px; font-weight: 600; color: #181c32;">DP Sebelumnya</h5>
                                <div style="display: flex; gap: 8px;">
                                    <button type="button" id="btnPelunasanAuto" onclick="setPelunasanMode('auto')" style="font-size: 12px; padding: 6px 14px; border-radius: 6px; font-weight: 500; cursor: pointer; transition: 0.15s; background: #6c5ce7; color: #fff; border: 1px solid #6c5ce7;">Hitung Ulang Otomatis</button>
                                    <button type="button" id="btnPelunasanManual" onclick="setPelunasanMode('manual')" style="font-size: 12px; padding: 6px 14px; border-radius: 6px; font-weight: 500; cursor: pointer; transition: 0.15s; background: #fff; color: #555; border: 1px solid #dcdcdc;">Isi Manual</button>
                                </div>
                            </div>
                            <div id="pelunasanInfoText" style="font-size: 12px; color: #888; margin-bottom: 12px;">Belum ada DP tercatat untuk klien ini.</div>

                            <!-- Input Manual Total DP -->
                            <div id="pelunasanManualInputGroup" style="display: none; margin-bottom: 14px;">
                                <label class="form-label" style="font-size: 12px; font-weight: 600; color: #444; margin-bottom: 6px;">Total DP</label>
                                <input type="text" class="form-control" id="inputManualTotalDP" value="Rp 0" oninput="handleCurrencyInput(this); calculateTotals(); updateWaDraft();" placeholder="Rp 0">
                            </div>

                            <!-- Baris DP sudah dibayar & Sisa yang harus dibayar -->
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; font-size: 13px;">
                                <span style="font-weight: 600; color: #181c32;">DP sudah dibayar</span>
                                <span style="font-weight: 600; color: #181c32;" id="valPelunasanDpSudahDibayar">- Rp 0</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px; font-size: 14px;">
                                <span style="font-weight: 700; color: #181c32;">Sisa yang harus dibayar</span>
                                <span style="font-weight: 700; color: #181c32;" id="valPelunasanSisaHarusDibayar">Rp 0</span>
                            </div>
                            <div id="pelunasanAutoNote" style="font-size: 11px; color: #888; margin-top: 8px;">Nominal pelunasan dihitung otomatis dari DP yang sudah dibayar.</div>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label" id="labelTglJatuhTempo">Tanggal Jatuh Tempo Pelunasan</label>
                            <div style="position: relative;">
                                <input type="date" class="form-control" id="inputTglJatuhTempo" value="" onchange="syncJatuhTempoPreview(); updateWaDraft();" oninput="syncJatuhTempoPreview(); updateWaDraft();" style="cursor: pointer;" onclick="try { if (typeof this.showPicker === 'function') this.showPicker(); } catch(e){}">
                            </div>
                        </div>
                        
                        <div class="form-row form-group">
                            <div>
                                <label class="form-label" id="labelNamaBank">Nama Bank</label>
                                <input type="text" class="form-control" value="BCA" id="inputNamaBank" oninput="syncBankPreview(); updateWaDraft();">
                            </div>
                            <div>
                                <label class="form-label" id="labelNoRekening">No Rekening</label>
                                <input type="text" class="form-control" value="0431063867" id="inputNoRekening" oninput="syncBankPreview(); updateWaDraft();">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label" id="labelAtasNama">Atas Nama</label>
                            <input type="text" class="form-control" value="Umi Salamah" id="inputAtasNama" oninput="syncBankPreview(); updateWaDraft();">
                        </div>
                        
                        <button class="btn btn-secondary" style="width: 100%; border: 1px solid var(--border-color); font-weight: 500;" id="btnSimpanBank" onclick="simpanBank()"><i class="fa-regular fa-floppy-disk" id="iconBank"></i> <span id="textBank">Simpan Data Bank</span></button>
                    </div>
                </div>

                <!-- Panel 6: Syarat & Ketentuan -->
                <div class="acc-panel">
                    <div class="acc-header" id="panelTitleSyarat">
                        Syarat & Ketentuan (Kustom)
                        <i class="fa-solid fa-chevron-up"></i>
                    </div>
                    <div class="acc-body">
                        <div class="info-text form-group" style="font-size: 12px; color: var(--text-muted); margin-bottom: 14px;" id="infoTerms">Atur poin-poin S&K yang muncul di invoice. Perubahan tersimpan otomatis di perangkat ini.</div>
                        
                        <div id="termsContainer">
                            <div class="term-point-card" style="border: 1px solid var(--border-color); border-radius: 8px; padding: 12px; margin-bottom: 12px; background: #fff; position: relative;">
                                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px; gap: 8px;">
                                    <input type="text" class="form-control term-title" placeholder="Judul (cth: Pelunasan:)" value="Pelunasan" oninput="syncTermsPreview()" style="font-weight: 500; font-size: 13px;">
                                    <button type="button" class="btn-trash" onclick="removeTermPoint(this)" style="background: none; border: none; color: var(--text-muted); cursor: pointer; font-size: 15px; padding: 4px;" title="Hapus poin">
                                        <i class="fa-regular fa-trash-can"></i>
                                    </button>
                                </div>
                                <textarea class="form-control term-content" placeholder="Isi ketentuan..." rows="2" oninput="syncTermsPreview()" style="font-size: 12px; resize: vertical;">Pelunasan wajib dibayarkan maksimal H-7 sebelum acara berlangsung.</textarea>
                            </div>
                        </div>

                        <div class="form-row" style="margin-top: 14px; display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                            <button type="button" class="btn btn-outline-primary" style="font-weight: 500; border-radius: 8px; border: 1px solid var(--primary); color: var(--primary); background: transparent; padding: 10px;" onclick="addTermPoint()"><i class="fa-solid fa-plus"></i> <span id="textTambahPoin">Tambah Poin</span></button>
                            <button type="button" class="btn btn-secondary" style="border: 1px dashed var(--border-color); font-weight: 500; border-radius: 8px; padding: 10px;" onclick="resetTermsToDefault()"><span id="textResetDefault">Reset ke Default</span></button>
                        </div>
                    </div>
                </div>
                
                <!-- Draf Pesan WA -->
                <div class="draft-area">
                    <div class="draft-title" id="sectionTitleWaDraft">
                        <i class="fa-regular fa-message"></i> Draf Pesan WhatsApp
                    </div>
                    
                    <div class="tone-badges">
                        <div class="tone-badge active" data-tone="ramah" id="badgeToneRamah">✨ Ramah</div>
                        <div class="tone-badge" data-tone="formal" id="badgeToneFormal">📋 Formal</div>
                        <div class="tone-badge" data-tone="ringkas" id="badgeToneRingkas">⚡ Ringkas</div>
                        <div class="tone-badge" data-tone="reminder" id="badgeToneReminder">⏰ Reminder</div>
                    </div>
                    
                    <textarea class="draft-textarea" id="wa-draft-textarea"></textarea>
                    
                    <div class="info-text form-group" id="labelWaEditNote">Pesan bisa diedit langsung sebelum dikirim ke klien.</div>
                    
                    <button type="button" class="btn btn-primary" style="width: 100%; padding: 14px; font-weight: 600;" id="btnKirimWa" onclick="kirimWhatsApp()"><i class="fa-regular fa-paper-plane"></i> <span id="textKirimWa">Kirim ke WhatsApp Klien</span></button>
                </div>
                
            </div>
            
            <!-- RIGHT COLUMN: Preview -->
            <div>
                <div class="section-title" id="sectionTitleDesign"><i class="fa-solid fa-palette"></i> Gaya Desain Invoice</div>
                <div class="design-options">
                    <div class="design-card active" data-theme="theme-minimalist" id="cardThemeMinimalist">
                        Minimalis & Clean
                        <div style="height: 4px; background: #666; border-radius: 2px; margin-top: 12px; width: 100%;"></div>
                    </div>
                    <div class="design-card" data-theme="theme-creative" id="cardThemeCreative">
                        Creative & Aesthetic
                        <div style="height: 4px; background: #d67a9a; border-radius: 2px; margin-top: 12px; width: 100%;"></div>
                    </div>
                    <div class="design-card" data-theme="theme-corporate" id="cardThemeCorporate">
                        Corporate & Professional
                        <div style="height: 4px; background: #1b365d; border-radius: 2px; margin-top: 12px; width: 100%;"></div>
                    </div>
                    <div class="design-card" data-theme="theme-modern" id="cardThemeModern">
                        Modern & Bold
                        <div style="height: 4px; background: #111; border-radius: 2px; margin-top: 12px; width: 100%;"></div>
                    </div>
                    <div class="design-card" data-theme="theme-classic" id="cardThemeClassic">
                        Classic / Traditional
                        <div style="height: 4px; background: #876839; border-radius: 2px; margin-top: 12px; width: 100%;"></div>
                    </div>
                </div>
                
                <div class="section-title" id="sectionTitlePreview"><i class="fa-regular fa-eye"></i> LIVE PREVIEW INVOICE</div>
                
                <!-- Paper Invoice -->
                <div class="invoice-paper theme-minimalist" id="invoice-preview" style="background: white; padding: 40px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); border-radius: 12px; margin-top: 20px;">
                    <!-- HEADER -->
                    <div class="inv-header" id="previewHeader">
                        <div class="inv-header-brand" id="previewHeaderBrand">
                            <div id="previewLogoContainer" style="width: 52px; height: 52px; border-radius: 50%; background: #f1f1f4; display: flex; align-items: center; justify-content: center; overflow: hidden; flex-shrink: 0; border: 1px solid #e4e6ef;">
                                <img id="previewLogoImg" src="" style="max-height: 100%; max-width: 100%; display: none;">
                                <i class="fa-regular fa-image" id="previewLogoPlaceholder" style="color: #a1a5b7; font-size: 20px;"></i>
                            </div>
                            <div>
                                <div class="inv-logo-text" id="previewNamaVendor">Fariz hidayat</div>
                                <div class="inv-addr-text" id="previewAlamatVendor">Kombas purwokerto</div>
                            </div>
                        </div>
                        <div class="inv-meta" id="previewHeaderMeta">
                            <h2 id="previewInvoiceTitle">INVOICE</h2>
                            <p>No. <span id="previewNoInvoice">INV-202610-006</span></p>
                            <p id="previewInvoiceDate">12 Oktober 2026</p>
                        </div>
                    </div>
                    
                    <!-- KEPADA / TANGGAL ACARA -->
                    <div class="inv-desc-row" style="margin-bottom: 24px; display: flex; justify-content: space-between;">
                        <div class="inv-bill-to">
                            <p style="font-size: 9px; font-weight: 600; color: #999; margin-bottom: 8px; text-transform: uppercase;" id="labelPreviewKepada">KEPADA</p>
                            <h4 id="previewNamaKlien" style="font-size: 15px; font-weight: 500; color: #333; margin-bottom: 4px;">Rian & Amel</h4>
                            <p style="font-size: 11px; color: #888; margin-bottom: 2px;" id="previewWaKlien">+6281234567890</p>
                            <p style="font-size: 11px; color: #888; display: none;" id="previewAlamatKlien"></p>
                        </div>
                        <div class="inv-bill-to" style="text-align: right;">
                            <p style="font-size: 9px; font-weight: 600; color: #999; margin-bottom: 8px; text-transform: uppercase;" id="labelPreviewTglAcara">TANGGAL ACARA</p>
                            <h4 style="font-size: 15px; font-weight: 500; color: #333; margin-bottom: 4px;" id="previewTglAcara">-</h4>
                            <p style="font-size: 11px; color: #888;" id="previewTerminLabel">Uang Muka / DP 1</p>
                        </div>
                    </div>
                    
                    <!-- TABEL -->
                    <table class="inv-table" style="margin-bottom: 16px; width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: #f8f9fa;">
                                <th style="text-align: left; padding: 12px 16px; font-size: 10px; font-weight: 600; color: #666; text-transform: uppercase;" id="labelPreviewThDesc">DESKRIPSI</th>
                                <th style="text-align: right; padding: 12px 16px; font-size: 10px; font-weight: 600; color: #666; text-transform: uppercase;" id="labelPreviewThPrice">HARGA</th>
                            </tr>
                        </thead>
                        <tbody id="previewTableBody">
                            <tr>
                                <td style="padding: 16px; border-bottom: 1px solid #f1f1f4;">
                                    <div style="font-size: 13px; font-weight: 500; color: #333; margin-bottom: 4px;">Premium Wedding Documentation</div>
                                    <div style="font-size: 10px; color: #888;" id="labelPreviewPaketUtama">Paket Utama</div>
                                </td>
                                <td style="text-align: right; padding: 16px; border-bottom: 1px solid #f1f1f4; font-size: 13px; color: #333;">Rp 10.000.000</td>
                            </tr>
                        </tbody>
                    </table>
                    
                    <!-- TOTALS -->
                    <div class="inv-totals" style="display: flex; justify-content: flex-end; margin-bottom: 24px; padding-right: 16px;">
                        <div style="width: 280px;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 11px;">
                                <div style="color: #666;" id="labelPreviewSubtotalPaket">Subtotal Paket</div>
                                <div id="previewSubtotal" style="color: #333;">Rp 9.500.000</div>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 12px; font-size: 11px;">
                                <div style="color: #666;" id="labelPreviewTotalAddon">Total Add-on</div>
                                <div id="previewAddon" style="color: #333;">Rp 0</div>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 12px; font-size: 11px;" id="rowPreviewDiskon">
                                <div style="color: #666;" id="labelPreviewDiskon">Diskon</div>
                                <div id="previewDiskon" style="color: #f1416c;">- Rp 500.000</div>
                            </div>
                            <!-- Grand Total -->
                            <div style="display: flex; justify-content: space-between; padding-top: 12px; border-top: 1px solid #eee; font-size: 12px; font-weight: 700;">
                                <div style="color: #333;" id="labelPreviewGrandTotal">GRAND TOTAL</div>
                                <div id="previewGrandTotal" style="color: #333;">Rp 9.500.000</div>
                            </div>
                            
                            <!-- DP Info Box -->
                            <div id="previewDpBox" style="background: #f8f9fa; border-radius: 8px; padding: 12px; margin-top: 16px;">
                                <div style="display: flex; justify-content: space-between; margin-bottom: 6px; font-size: 11px;">
                                    <div style="color: #666;" id="previewDP1Label">Uang Muka / DP 1</div>
                                    <div id="previewDP1" style="color: #333; font-weight: 500;">Rp 3.000.000</div>
                                </div>
                                <div id="previewDP2Row" style="display: none; justify-content: space-between; margin-bottom: 6px; font-size: 11px;">
                                    <div style="color: #666;" id="previewDP2Label">Termin 2 / DP 2</div>
                                    <div id="previewDP2" style="color: #333; font-weight: 500;">Rp 0</div>
                                </div>
                                <div id="previewTotalDPRow" style="display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 11px; padding-bottom: 6px; border-bottom: 1px dashed #ddd;">
                                    <div style="color: #666; font-weight: 600;" id="previewTotalDPLabel">Total DP 1</div>
                                    <div id="previewTotalDP" style="color: #333; font-weight: 600;">Rp 3.000.000</div>
                                </div>
                                <div style="display: flex; justify-content: space-between; font-size: 11px; font-weight: 600;">
                                    <div style="color: #666;" id="labelPreviewSisaTagihan">Sisa Tagihan</div>
                                    <div id="previewSisaTagihan" style="color: #333; font-size: 13px;">Rp 6.500.000</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- METODE PEMBAYARAN -->
                    <div class="inv-payment" style="border: 1px solid #f1f1f4; border-radius: 8px; padding: 16px; margin-bottom: 24px;" id="previewPaymentBox">
                        <p style="text-transform: uppercase; font-size: 9px; font-weight: 600; color: #999; margin-bottom: 6px; letter-spacing: 0.5px;" id="labelPreviewMetodeBayar">METODE PEMBAYARAN</p>
                        <h5 style="font-size: 12px; font-weight: 500; color: #333; margin: 0 0 4px 0;" id="previewPaymentText">
                            <strong style="color: #333;" id="previewBankName">BCA</strong> &middot; <span id="previewNoRek">0431063867</span> &middot; a.n <span id="previewAtasNama">Umi Salamah</span>
                        </h5>
                        <p style="font-size: 11px; color: #777; margin: 0; display: none;" id="previewJatuhTempoRow">
                            <span id="labelPreviewJatuhTempoPrefix">Jatuh tempo pelunasan: </span><span id="previewJatuhTempoDate">-</span>
                        </p>
                    </div>
                    
                    <!-- SYARAT KETENTUAN -->
                    <div class="inv-footer" style="display: block;">
                        <div style="margin-bottom: 24px;">
                            <p style="font-weight: 700; font-size: 10px; margin-bottom: 8px; color: #555; text-transform: uppercase; letter-spacing: 0.5px;" id="labelPreviewTermsHeader">SYARAT & KETENTUAN</p>
                            <div id="previewTermsList" style="font-size: 10px; color: #555; line-height: 1.6; margin-bottom: 20px;">
                                <div style="margin-bottom: 4px;">1. <strong>Pelunasan</strong> Pelunasan wajib dibayarkan maksimal H-7 sebelum acara berlangsung.</div>
                            </div>
                            
                            <!-- Line divider and signature -->
                            <div style="border-top: 1px solid #eee; padding-top: 20px; display: flex; justify-content: flex-end;">
                                <div class="inv-signature" style="text-align: center; width: 160px;">
                                    <div style="margin-bottom: 4px; font-weight: 500; font-size: 13px; color: #333;" id="previewTtdNama">Fariz hidayat</div>
                                    <div id="previewTtdImgBox" style="height: 48px; display: flex; align-items: center; justify-content: center; margin-bottom: 6px;">
                                        <img id="previewTtdImg" src="" style="max-height: 44px; max-width: 130px; display: none;">
                                        <span id="previewTtdPlaceholder" style="font-style: italic; font-size: 11px; color: #999;">signature</span>
                                    </div>
                                    <div style="border-top: 1px solid #ccc; padding-top: 6px;">
                                        <p style="font-size: 9px; color: #888; margin: 0;" id="labelPreviewTtd">Tanda Tangan</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="section-title" style="margin-top: 24px;" id="sectionTitleSignature"><i class="fa-solid fa-pen-nib"></i> Tanda Tangan (PNG/JPG, maks 2MB)</div>
                <div style="background: white; border: 1px solid var(--border-color); border-radius: 10px; padding: 16px; margin-bottom: 16px;">
                    <div style="display: flex; gap: 12px; align-items: center; margin-bottom: 12px;">
                        <div id="ttdPreviewThumbnail" style="width: 50px; height: 50px; min-width: 50px; border: 1px solid var(--border-color); border-radius: 8px; display: flex; align-items: center; justify-content: center; background: #f8f9fa; overflow: hidden;">
                            <img id="ttdImgThumbnail" src="" style="display: none; max-width: 100%; max-height: 100%; object-fit: contain;">
                            <i class="fa-solid fa-signature" id="ttdIconPlaceholder" style="color: #a1a5b7; font-size: 20px;"></i>
                        </div>
                        <div class="btn btn-secondary" id="btnUploadTtd" style="flex: 1; height: 44px; display: flex; align-items: center; justify-content: center; border: 1px solid var(--border-color); border-radius: 8px; color: var(--text-dark); font-size: 13px; font-weight: 500; background: white; gap: 8px; cursor: pointer;" onclick="document.getElementById('ttdInput').click()">
                            <i class="fa-solid fa-arrow-up-from-bracket"></i> <span id="textUploadTtd">Unggah TTD</span>
                        </div>
                        <span id="btnHapusTtd" style="display: none; color: var(--text-muted); cursor: pointer; font-size: 13px; margin-left: 4px;" onclick="hapusTtd()">Hapus</span>
                        <input type="file" id="ttdInput" style="display: none;" accept="image/png, image/jpeg" onchange="previewTtdFile(this)">
                    </div>
                    <button type="button" class="btn btn-secondary" style="width: 100%; border: 1px solid var(--border-color); font-weight: 500;" id="btnSimpanTtd" onclick="simpanTtd()"><i class="fa-regular fa-floppy-disk" id="iconTtd"></i> <span id="textTtd">Simpan TTD</span></button>
                </div>
                
                <div style="display: flex; gap: 10px; margin-bottom: 20px; align-items: center; flex-wrap: wrap;">
                    <div style="display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; background: rgba(80, 205, 137, 0.1); border-radius: 20px; font-size: 12px; color: #50cd89; font-weight: 500;">
                        <i class="fa-solid fa-check"></i> <span id="badgeLogoSiap">Logo siap</span>
                    </div>
                    <div style="display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; background: rgba(255, 199, 0, 0.1); border-radius: 20px; font-size: 12px; color: #ff9800; font-weight: 500;" id="badgeTtdStatus">
                        <i class="fa-solid fa-pen-nib"></i> <span id="badgeTtdText">Tanda tangan siap</span>
                    </div>
                </div>
                
                <div class="inv-action-btns" style="display: flex; gap: 12px; margin-bottom: 40px;">
                    <button type="button" class="btn btn-secondary" id="btnUnduhPng" onclick="unduhPNG()"><i class="fa-regular fa-image"></i> Unduh PNG</button>
                    <button type="button" class="btn btn-secondary" id="btnUnduhPdf" style="background: var(--text-dark); color: white; border-color: var(--text-dark);" onclick="unduhPDF()"><i class="fa-regular fa-file-pdf"></i> Unduh PDF</button>
                    <button type="button" class="btn btn-primary btn-full" style="flex: 1;" id="btnSimpanFolder" onclick="simpanInvoiceKeDatabase()"><i class="fa-regular fa-folder-open"></i> Simpan ke Folder</button>
                </div>
                
            </div>
            
        </div>

        <!-- TAB: INVOICE TERSIMPAN VIEW -->
        <div id="savedInvoicesView" style="display: none; width: 100%;">
            <!-- Search & Filter Bar -->
            <div style="display: flex; gap: 10px; align-items: center; margin-bottom: 20px; flex-wrap: wrap;">
                <div style="position: relative; flex: 1; min-width: 240px;">
                    <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #a1a5b7; font-size: 14px;"></i>
                    <input type="text" id="searchSavedInvoice" class="form-control" placeholder="Cari nama klien / no. invoice..." oninput="filterSavedInvoices()" style="padding-left: 38px; height: 40px; border-radius: 8px; font-size: 13px; background: white;">
                </div>
                
                <div class="date-filter-group" style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
                    <button type="button" class="date-filter-btn active" id="btnDateFilterAll" onclick="handleDateFilterAllClick(this)">Semua Tanggal</button>
                    
                    <div id="dateFilterExtraGroup" style="display: none; align-items: center; gap: 8px; flex-wrap: wrap;">
                        <button type="button" class="date-filter-btn" id="btnDateFilterThisMonth" onclick="setDateFilter('this_month', this)">Bulan Ini</button>
                        <button type="button" class="date-filter-btn" id="btnDateFilterLastMonth" onclick="setDateFilter('last_month', this)">Bulan Lalu</button>
                        <button type="button" class="date-filter-btn" id="btnDateFilterLast30" onclick="setDateFilter('last_30_days', this)">30 Hari Terakhir</button>
                        <button type="button" class="date-filter-btn" id="btnDateFilterThisYear" onclick="setDateFilter('this_year', this)">Tahun Ini</button>
                        
                        <div style="position: relative;" id="customRangePickerContainer">
                            <button type="button" class="date-filter-btn" id="btnDateFilterCustom" onclick="toggleCustomRangePicker(event)" style="display: inline-flex; align-items: center; gap: 6px;">
                                <i class="fa-regular fa-calendar"></i> <span id="labelCustomRangeBtn">Custom Range</span>
                            </button>
                            
                            <!-- 2-Month Calendar Popover Dropdown (Image 4) -->
                            <div id="customRangePickerPopover" style="display: none; position: absolute; right: 0; top: calc(100% + 8px); background: #ffffff; border-radius: 16px; box-shadow: 0 12px 48px rgba(0,0,0,0.15); border: 1px solid #e2e8f0; padding: 22px; z-index: 9999; width: 620px;">
                                <div style="display: flex; gap: 24px;">
                                    <!-- Left Month -->
                                    <div style="flex: 1;">
                                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px;">
                                            <button type="button" onclick="prevCalendarMonth(event)" style="background: none; border: none; cursor: pointer; color: #475569; padding: 4px 8px; font-size: 14px;"><i class="fa-solid fa-chevron-left"></i></button>
                                            <span id="calMonthTitle1" style="font-weight: 600; font-size: 14px; color: #1e293b;">September 2026</span>
                                            <span style="width: 24px;"></span>
                                        </div>
                                        <div style="display: grid; grid-template-columns: repeat(7, 1fr); text-align: center; font-size: 12px; color: #64748b; font-weight: 500; margin-bottom: 8px;">
                                            <span>Su</span><span>Mo</span><span>Tu</span><span>We</span><span>Th</span><span>Fr</span><span>Sa</span>
                                        </div>
                                        <div id="calGridMonth1" style="display: grid; grid-template-columns: repeat(7, 1fr); gap: 2px;"></div>
                                    </div>
                                    <!-- Right Month -->
                                    <div style="flex: 1;">
                                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px;">
                                            <span style="width: 24px;"></span>
                                            <span id="calMonthTitle2" style="font-weight: 600; font-size: 14px; color: #1e293b;">October 2026</span>
                                            <button type="button" onclick="nextCalendarMonth(event)" style="background: none; border: none; cursor: pointer; color: #475569; padding: 4px 8px; font-size: 14px;"><i class="fa-solid fa-chevron-right"></i></button>
                                        </div>
                                        <div style="display: grid; grid-template-columns: repeat(7, 1fr); text-align: center; font-size: 12px; color: #64748b; font-weight: 500; margin-bottom: 8px;">
                                            <span>Su</span><span>Mo</span><span>Tu</span><span>We</span><span>Th</span><span>Fr</span><span>Sa</span>
                                        </div>
                                        <div id="calGridMonth2" style="display: grid; grid-template-columns: repeat(7, 1fr); gap: 2px;"></div>
                                    </div>
                                </div>
                                
                                <!-- Bottom Action Bar -->
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 20px; padding-top: 16px; border-top: 1px solid #f1f5f9;">
                                    <div id="customRangeFooterDisplay" style="font-size: 13px; font-weight: 500; color: #475569;">13 September 2026 – 12 Oktober 2026</div>
                                    <div style="display: flex; gap: 10px; align-items: center;">
                                        <button type="button" id="btnCalReset" onclick="resetCustomRangeCalendar()" style="background: none; border: none; color: #64748b; font-size: 13px; font-weight: 500; cursor: pointer; padding: 6px 12px;">Reset</button>
                                        <button type="button" id="btnCalApply" onclick="applyCustomRangeCalendar()" style="background: #6b5cd8; border: none; color: white; font-size: 13px; font-weight: 600; cursor: pointer; padding: 8px 20px; border-radius: 8px;">Terapkan</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <button type="button" class="btn btn-secondary" onclick="exportCsvInvoices()" id="btnExportCsv" style="border-radius: 8px; height: 40px; padding: 0 16px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px; background: white; border: 1px solid var(--border-color); color: var(--text-dark); cursor: pointer;">
                    <i class="fa-solid fa-file-csv" style="color: #64748b;"></i> <span id="textExportCsv">Ekspor CSV</span>
                </button>
            </div>

            <!-- Status Filter Pills -->
            <div style="display: flex; gap: 8px; margin-bottom: 20px; flex-wrap: wrap;">
                <button type="button" class="status-filter-pill active" onclick="setFilterStatus('all', this)" style="padding: 6px 18px; border-radius: 20px; font-size: 13px; font-weight: 600; border: none; cursor: pointer; background: #6c5ce7; color: white; transition: 0.2s;">Semua</button>
                <button type="button" class="status-filter-pill" onclick="setFilterStatus('Belum Dibayar', this)" style="padding: 6px 18px; border-radius: 20px; font-size: 13px; font-weight: 500; border: 1px solid var(--border-color); cursor: pointer; background: white; color: #555; transition: 0.2s;">Belum Dibayar</button>
                <button type="button" class="status-filter-pill" onclick="setFilterStatus('Terbayar Sebagian', this)" style="padding: 6px 18px; border-radius: 20px; font-size: 13px; font-weight: 500; border: 1px solid var(--border-color); cursor: pointer; background: white; color: #555; transition: 0.2s;">Terbayar Sebagian</button>
                <button type="button" class="status-filter-pill" onclick="setFilterStatus('Lunas', this)" style="padding: 6px 18px; border-radius: 20px; font-size: 13px; font-weight: 500; border: 1px solid var(--border-color); cursor: pointer; background: white; color: #555; transition: 0.2s;">Lunas</button>
            </div>

            <!-- Counters Row -->
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; font-size: 13px; color: #64748b;">
                <div><span id="savedCountLabel">0 invoice</span></div>
                <div style="font-weight: 600; color: #1e1e2d;"><span id="savedTotalValLabel">Total nilai: Rp 0</span></div>
            </div>

            <!-- Invoices List Container -->
            <div id="savedInvoicesListContainer" style="background: white; border-radius: 12px; border: 1px solid var(--border-color); overflow: hidden;">
                <div style="text-align: center; padding: 40px; color: #a1a5b7; font-size: 13px;">Memuat invoice tersimpan...</div>
            </div>
        </div>

        <!-- MODAL RIWAYAT PEMBAYARAN -->
        <div id="paymentModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 100000; align-items: center; justify-content: center;">
            <div style="background: white; width: 90%; max-width: 480px; border-radius: 16px; padding: 26px; box-shadow: 0 10px 40px rgba(0,0,0,0.15); position: relative;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 6px;">
                    <h3 style="margin: 0; font-size: 16px; font-weight: 700; color: #181c32;">Riwayat Pembayaran — <span id="paymentModalInvNo">INV-202610-001</span></h3>
                    <i class="fa-solid fa-xmark" style="cursor: pointer; color: #a1a5b7; font-size: 16px; padding: 4px;" onclick="closePaymentModal()"></i>
                </div>
                <div style="font-size: 13px; color: #64748b; margin-bottom: 20px;" id="paymentModalSubtitle">
                    Total Rp 0 &middot; Terbayar Rp 0 &middot; Sisa Rp 0
                </div>

                <div id="paymentHistoryList" style="margin-bottom: 20px; max-height: 180px; overflow-y: auto;">
                    <div style="text-align: center; padding: 20px; color: #a1a5b7; font-size: 13px;">Belum ada pembayaran tercatat.</div>
                </div>

                <form id="recordPaymentForm" onsubmit="submitRecordPayment(event)">
                    <input type="hidden" id="paymentInvoiceId">
                    <div class="form-group" style="margin-bottom: 12px;">
                        <input type="text" id="inputPaymentNominal" class="form-control" placeholder="Nominal pembayaran" style="height: 44px; font-size: 14px; font-weight: 600; border-radius: 8px;" oninput="handleCurrencyInput(this)">
                    </div>
                    <div class="form-row form-row-2col" style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 18px;">
                        <div style="position: relative;">
                            <input type="text" id="inputPaymentDate" class="form-control" placeholder="mm/dd/yyyy" style="height: 44px; font-size: 13px; border-radius: 8px; padding-right: 32px;">
                            <i class="fa-regular fa-calendar" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); color: #64748b; pointer-events: none;"></i>
                        </div>
                        <div>
                            <input type="text" id="inputPaymentNote" class="form-control" placeholder="Catatan (opsional)" style="height: 44px; font-size: 13px; border-radius: 8px;">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary" style="width: 100%; height: 46px; background: #6c5ce7; border-radius: 10px; font-size: 14px; font-weight: 600; display: flex; align-items: center; justify-content: center; border: none; cursor: pointer; color: white;">
                        Catat Pembayaran
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Booking Modal -->
    <div id="bookingModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 100000; align-items: center; justify-content: center;">
        <div style="background: white; width: 90%; max-width: 600px; border-radius: 16px; padding: 24px; box-shadow: 0 10px 40px rgba(0,0,0,0.15); position: relative; max-height: 85vh; display: flex; flex-direction: column;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                <h3 style="margin: 0; font-size: 16px; font-weight: 700; color: #181c32;">Pilih Data Booking</h3>
                <i class="fa-solid fa-xmark" style="cursor: pointer; color: #a1a5b7; font-size: 16px; padding: 4px;" onclick="closeBookingModal()"></i>
            </div>
            <div style="position: relative; margin-bottom: 14px;">
                <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #a1a5b7; font-size: 13px;"></i>
                <input type="text" id="bookingSearchInput" class="form-control" placeholder="Cari nama pengantin / nomor booking..." oninput="filterBookingList()" style="padding-left: 36px; height: 38px; font-size: 13px;">
            </div>
            <div id="bookingListContainer" style="overflow-y: auto; flex: 1; min-height: 200px;">
                <div style="text-align: center; padding: 40px; color: #a1a5b7; font-size: 13px;">Memuat data booking...</div>
            </div>
        </div>
    </div>

    <!-- Full JavaScript -->
    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        // Sidebar collapse
        const btnCollapse = document.getElementById('btn-collapse');
        if (btnCollapse) {
            btnCollapse.addEventListener('click', () => {
                document.body.classList.toggle('sidebar-collapsed');
            });
        }

        // Accordion Toggle
        document.querySelectorAll('.acc-header').forEach(header => {
            header.addEventListener('click', () => {
                const body = header.nextElementSibling;
                const icon = header.querySelector('i');
                if (body && body.style.display === 'none') {
                    body.style.display = 'block';
                    if (icon) icon.style.transform = 'rotate(0deg)';
                } else if (body) {
                    body.style.display = 'none';
                    if (icon) icon.style.transform = 'rotate(180deg)';
                }
            });
        });

        // Theme Switcher
        let currentSelectedTheme = 'theme-minimalist';

        function applyTheme(theme) {
            currentSelectedTheme = theme || 'theme-minimalist';
            const invoicePaper = document.getElementById('invoice-preview');
            if (invoicePaper) {
                invoicePaper.className = 'invoice-paper ' + currentSelectedTheme;
            }
            document.querySelectorAll('.design-card').forEach(card => {
                if (card.getAttribute('data-theme') === currentSelectedTheme) {
                    card.classList.add('active');
                } else {
                    card.classList.remove('active');
                }
            });
        }

        const designCards = document.querySelectorAll('.design-card');
        designCards.forEach(card => {
            card.addEventListener('click', () => {
                const theme = card.getAttribute('data-theme');
                if (theme) applyTheme(theme);
            });
        });

        // --- Multi-Language & Translation System ---
        let currentLang = localStorage.getItem('closemate_invoice_lang') || 'id';

        const i18n = {
            id: {
                tab_baru: 'Invoice Baru',
                tab_tersimpan: 'Invoice Tersimpan',
                // Panel 1
                panel_bisnis: 'Informasi Bisnis',
                label_logo: 'Logo Vendor (JPG/PNG, maks 5MB)',
                btn_unggah_logo: 'Unggah Logo',
                btn_hapus_logo: 'Hapus',
                label_nama_vendor: 'Nama Vendor',
                label_alamat_perusahaan: 'Alamat Perusahaan',
                label_no_invoice: 'Nomor Invoice (Kustom)',
                label_auto_no_note: 'Kosongkan untuk memakai nomor otomatis berurutan: ',
                btn_simpan_format: 'Simpan Format',
                btn_simpan_bisnis: 'Simpan Informasi Bisnis',
                // Panel 2
                panel_klien: 'Informasi Klien',
                btn_ambil_booking: 'Ambil dari Booking',
                note_ambil_booking: 'Isi otomatis nama, WhatsApp, alamat, tanggal acara, paket, diskon, dan add-on dari data Booking & Operasional.',
                label_nama_klien: 'Nama Klien (Pengantin)',
                label_no_wa_klien: 'No WhatsApp Klien',
                label_alamat_klien: 'Alamat Klien (opsional)',
                placeholder_alamat_klien: 'Alamat lengkap klien',
                label_tgl_acara: 'Tanggal Acara',
                // Panel 3
                panel_paket: 'Rincian Paket & Biaya',
                label_nama_paket: 'Nama Paket Pernikahan',
                label_harga_utama: 'Harga Paket Utama',
                label_jumlah_paket: 'Jumlah Paket',
                label_diskon: 'Diskon / Potongan',
                btn_simpan_biaya: 'Simpan Paket & Harga',
                note_simpan_biaya: 'Tersimpan di perangkat ini, bisa dipilih lagi lewat tombol panah di kolom nama paket.',
                btn_tambah_paket: 'Tambah Paket',
                label_subtotal_paket: 'Subtotal Paket',
                label_total_addon: 'Total Add-on',
                label_grand_total: 'GRAND TOTAL',
                // Panel 4
                panel_addon: 'Add-on Tambahan',
                btn_tambah_addon: 'Tambah Item Add-on',
                label_nama_addon: 'Nama Add-on',
                label_qty_addon: 'Jumlah (pcs)',
                label_harga_satuan: 'Harga Satuan',
                // Panel 5
                panel_termin: 'Termin & Pembayaran',
                label_termin_saat_ini: 'Termin Saat Ini',
                termin_opt_dp1: 'Uang Muka / DP 1',
                termin_opt_dp2: 'Termin 2 / DP 2',
                termin_opt_pelunasan: 'Pelunasan',
                label_dp1: 'Nominal DP 1',
                label_dp2: 'Nominal DP 2',
                label_total_dp: 'Total DP 1',
                label_sisa_tagihan: 'Sisa Tagihan',
                label_jatuh_tempo: 'Tanggal Jatuh Tempo Pelunasan',
                label_nama_bank: 'Nama Bank',
                label_no_rekening: 'No Rekening',
                label_atas_nama: 'Atas Nama',
                btn_simpan_bank: 'Simpan Data Bank',
                // Panel 6
                panel_terms: 'Syarat & Ketentuan (Kustom)',
                note_terms: 'Atur poin-poin S&K yang muncul di invoice. Perubahan tersimpan otomatis di perangkat ini.',
                btn_tambah_poin: 'Tambah Poin',
                btn_reset_terms: 'Reset ke Default',
                // Desain
                title_desain: 'Gaya Desain Invoice',
                theme_minimalist: 'Minimalis & Clean',
                theme_creative: 'Creative & Aesthetic',
                theme_corporate: 'Corporate & Professional',
                theme_modern: 'Modern & Bold',
                theme_classic: 'Classic / Traditional',
                // Preview
                title_preview: 'LIVE PREVIEW INVOICE',
                preview_kepada: 'KEPADA',
                preview_tgl_acara: 'TANGGAL ACARA',
                preview_th_desc: 'DESKRIPSI',
                preview_th_price: 'HARGA',
                preview_paket_utama: 'Paket Utama',
                preview_subtotal_paket: 'Subtotal Paket',
                preview_total_addon: 'Total Add-on',
                preview_diskon: 'Diskon',
                preview_grand_total: 'GRAND TOTAL',
                preview_sisa_tagihan: 'Sisa Tagihan',
                preview_metode_bayar: 'METODE PEMBAYARAN',
                preview_jatuh_tempo_prefix: 'Jatuh tempo pelunasan: ',
                preview_terms_header: 'SYARAT & KETENTUAN',
                preview_ttd: 'Tanda Tangan',
                title_ttd_section: 'Tanda Tangan (PNG/JPG, maks 2MB)',
                btn_unggah_ttd: 'Unggah TTD',
                btn_hapus_ttd: 'Hapus',
                btn_simpan_ttd: 'Simpan TTD',
                badge_logo_ready: 'Logo siap',
                badge_ttd_ready: 'Tanda tangan siap',
                btn_unduh_png: 'Unduh PNG',
                btn_unduh_pdf: 'Unduh PDF',
                btn_simpan_folder: 'Simpan ke Folder',
                // WA Draft
                title_wa_draft: 'Draf Pesan WhatsApp',
                tone_ramah: '✨ Ramah',
                tone_formal: '📋 Formal',
                tone_ringkas: '⚡ Ringkas',
                tone_reminder: '⏰ Reminder',
                note_wa_edit: 'Pesan bisa diedit langsung sebelum dikirim ke klien.',
                btn_kirim_wa: 'Kirim ke WhatsApp Klien',
                // Saved Invoices Tab
                search_placeholder: 'Cari nama klien / no. invoice...',
                filter_all_dates: 'Semua Tanggal',
                filter_this_month: 'Bulan Ini',
                filter_last_month: 'Bulan Lalu',
                filter_last_30: '30 Hari Terakhir',
                filter_this_year: 'Tahun Ini',
                filter_custom_range: 'Custom Range',
                btn_export_csv: 'Ekspor CSV',
                pill_all: 'Semua',
                pill_unpaid: 'Belum Dibayar',
                pill_partial: 'Terbayar Sebagian',
                pill_paid: 'Lunas',
                btn_tandai_lunas: 'Tandai Lunas',
                btn_buka: 'Buka',
                label_total_nilai: 'Total nilai: Rp',
                label_acara: 'Acara:',
                label_terbayar: 'Terbayar: Rp',
                cal_reset: 'Reset',
                cal_apply: 'Terapkan'
            },
            en: {
                tab_baru: 'New Invoice',
                tab_tersimpan: 'Saved Invoices',
                // Panel 1
                panel_bisnis: 'Business Information',
                label_logo: 'Vendor Logo (JPG/PNG, max 5MB)',
                btn_unggah_logo: 'Upload Logo',
                btn_hapus_logo: 'Remove',
                label_nama_vendor: 'Vendor Name',
                label_alamat_perusahaan: 'Company Address',
                label_no_invoice: 'Custom Invoice Number',
                label_auto_no_note: 'Leave empty to use the automatic sequential number: ',
                btn_simpan_format: 'Save Format',
                btn_simpan_bisnis: 'Save Business Info',
                // Panel 2
                panel_klien: 'Client Information',
                btn_ambil_booking: 'Ambil dari Booking',
                note_ambil_booking: 'Isi otomatis nama, WhatsApp, alamat, tanggal acara, paket, diskon, dan add-on dari data Booking & Operasional.',
                label_nama_klien: 'Client Name (Couple)',
                label_no_wa_klien: 'Client WhatsApp Number',
                label_alamat_klien: 'Client Address (optional)',
                placeholder_alamat_klien: 'Full client address',
                label_tgl_acara: 'Event Date',
                // Panel 3
                panel_paket: 'Package & Pricing Details',
                label_nama_paket: 'Wedding Package Name',
                label_harga_utama: 'Main Package Price',
                label_jumlah_paket: 'Package Qty',
                label_diskon: 'Discount',
                btn_simpan_biaya: 'Simpan Paket & Harga',
                note_simpan_biaya: 'Tersimpan di perangkat ini, bisa dipilih lagi lewat tombol panah di kolom nama paket.',
                btn_tambah_paket: 'Add Package',
                label_subtotal_paket: 'Package Subtotal',
                label_total_addon: 'Total Add-ons',
                label_grand_total: 'GRAND TOTAL',
                // Panel 4
                panel_addon: 'Add-ons',
                btn_tambah_addon: 'Add New Add-on',
                label_nama_addon: 'Add-on Name',
                label_qty_addon: 'Quantity (pcs)',
                label_harga_satuan: 'Unit Price',
                // Panel 5
                panel_termin: 'Payment Term',
                label_termin_saat_ini: 'Current Term',
                termin_opt_dp1: 'Down Payment / DP 1',
                termin_opt_dp2: 'Term 2 / DP 2',
                termin_opt_pelunasan: 'Final Payment',
                label_dp1: 'DP 1 Amount',
                label_dp2: 'DP 2 Amount',
                label_total_dp: 'Total DP 1',
                label_sisa_tagihan: 'Remaining Balance',
                label_jatuh_tempo: 'Final Payment Due Date',
                label_nama_bank: 'Bank Name',
                label_no_rekening: 'Account Number',
                label_atas_nama: 'Account Holder',
                btn_simpan_bank: 'Save Bank Details',
                // Panel 6
                panel_terms: 'Terms & Conditions (Custom)',
                note_terms: 'Customize the T&C clauses that appear on the invoice. Changes are saved on this device.',
                btn_tambah_poin: 'Add Clause',
                btn_reset_terms: 'Reset to Default',
                // Desain
                title_desain: 'Invoice Design Style',
                theme_minimalist: 'Minimal & Clean',
                theme_creative: 'Creative & Aesthetic',
                theme_corporate: 'Corporate & Professional',
                theme_modern: 'Modern & Bold',
                theme_classic: 'Classic / Traditional',
                // Preview
                title_preview: 'Live Invoice Preview',
                preview_kepada: 'Billed To',
                preview_tgl_acara: 'Event Date',
                preview_th_desc: 'Description',
                preview_th_price: 'Price',
                preview_paket_utama: 'Main Package',
                preview_subtotal_paket: 'Package Subtotal',
                preview_total_addon: 'Total Add-ons',
                preview_diskon: 'Discount',
                preview_grand_total: 'GRAND TOTAL',
                preview_sisa_tagihan: 'Remaining Balance',
                preview_metode_bayar: 'Payment Method',
                preview_jatuh_tempo_prefix: 'Payment due date: ',
                preview_terms_header: 'TERMS & CONDITIONS',
                preview_ttd: 'Signature',
                title_ttd_section: 'Signature (PNG/JPG, max 2MB)',
                btn_unggah_ttd: 'Upload Signature',
                btn_hapus_ttd: 'Remove',
                btn_simpan_ttd: 'Save Signature',
                badge_logo_ready: 'Logo ready',
                badge_ttd_ready: 'Signature ready',
                btn_unduh_png: 'Download PNG',
                btn_unduh_pdf: 'Download PDF',
                btn_simpan_folder: 'Save to Folder',
                // WA Draft
                title_wa_draft: 'WhatsApp Message Draft',
                tone_ramah: '✨ Friendly',
                tone_formal: '📋 Formal',
                tone_ringkas: '⚡ Short',
                tone_reminder: '⏰ Reminder',
                note_wa_edit: 'You can edit this message before sending it to the client.',
                btn_kirim_wa: "Send to Client's WhatsApp",
                // Saved Invoices Tab
                search_placeholder: 'Search client name / invoice no...',
                filter_all_dates: 'All Dates',
                filter_this_month: 'This Month',
                filter_last_month: 'Last Month',
                filter_last_30: 'Last 30 Days',
                filter_this_year: 'This Year',
                filter_custom_range: 'Custom Range',
                btn_export_csv: 'Export CSV',
                pill_all: 'All',
                pill_unpaid: 'Unpaid',
                pill_partial: 'Partially Paid',
                pill_paid: 'Paid',
                btn_tandai_lunas: 'Mark as Paid',
                btn_buka: 'Open',
                label_total_nilai: 'Total value: Rp',
                label_acara: 'Event:',
                label_terbayar: 'Paid: Rp',
                cal_reset: 'Reset',
                cal_apply: 'Apply'
            }
        };

        function setLanguage(lang) {
            currentLang = lang;
            localStorage.setItem('closemate_invoice_lang', lang);
            
            const btnId = document.getElementById('btnLangId');
            const btnEn = document.getElementById('btnLangEn');
            if (btnId && btnEn) {
                if (lang === 'en') {
                    btnId.classList.remove('active');
                    btnEn.classList.add('active');
                } else {
                    btnId.classList.add('active');
                    btnEn.classList.remove('active');
                }
            }
            applyLanguage(lang);
            updatePreviewInvoiceDate();
            updateWaDraft();
            filterSavedInvoices();
        }

        function applyLanguage(lang) {
            const dict = i18n[lang] || i18n.id;
            const setTxt = (id, val) => {
                const el = document.getElementById(id);
                if (el && val !== undefined) el.innerText = val;
            };
            const setHtml = (id, val) => {
                const el = document.getElementById(id);
                if (el && val !== undefined) el.innerHTML = val;
            };

            // Tabs
            setHtml('tabBtnInvoiceBaru', `<i class="fa-solid fa-plus"></i> ${dict.tab_baru}`);
            loadSavedInvoicesCount();

            // Panel 1
            const p1Header = document.getElementById('panelTitleBisnis');
            if (p1Header) p1Header.childNodes[0].nodeValue = dict.panel_bisnis + ' ';
            setTxt('labelLogoVendor', dict.label_logo);
            setTxt('textUploadLogo', dict.btn_unggah_logo);
            setTxt('btnHapusLogo', dict.btn_hapus_logo);
            setTxt('labelNamaVendor', dict.label_nama_vendor);
            setTxt('labelAlamatVendor', dict.label_alamat_perusahaan);
            setTxt('labelNoInvoiceKustom', dict.label_no_invoice);
            setTxt('infoAutoInvoicePrefix', dict.label_auto_no_note);
            setTxt('textFormat', dict.btn_simpan_format);
            setTxt('textBisnis', dict.btn_simpan_bisnis);

            // Panel 2
            const p2Header = document.getElementById('panelTitleKlien');
            if (p2Header) p2Header.childNodes[0].nodeValue = dict.panel_klien + ' ';
            setTxt('btnAmbilBookingText', dict.btn_ambil_booking);
            setTxt('infoAmbilBooking', dict.note_ambil_booking);
            setTxt('labelNamaKlien', dict.label_nama_klien);
            setTxt('labelNoWaKlien', dict.label_no_wa_klien);
            setTxt('labelAlamatKlien', dict.label_alamat_klien);
            const addrInput = document.getElementById('inputAlamatKlien');
            if (addrInput) addrInput.placeholder = dict.placeholder_alamat_klien;
            setTxt('labelTglAcara', dict.label_tgl_acara);

            // Panel 3
            const p3Header = document.getElementById('panelTitlePaket');
            if (p3Header) p3Header.childNodes[0].nodeValue = dict.panel_paket + ' ';
            setTxt('labelPaketUtama', dict.label_nama_paket);
            setTxt('labelHargaUtama', dict.label_harga_utama);
            setTxt('labelJumlahPaket', dict.label_jumlah_paket);
            setTxt('labelDiskon', dict.label_diskon);
            setTxt('textBiaya', dict.btn_simpan_biaya);
            setTxt('infoSimpanBiaya', dict.note_simpan_biaya);
            setTxt('textTambahPaket', dict.btn_tambah_paket);
            setTxt('labelCalcSubtotal', dict.label_subtotal_paket);
            setTxt('labelCalcAddon', dict.label_total_addon);
            setTxt('labelCalcGrandTotal', dict.label_grand_total);

            // Panel 4
            const p4Header = document.getElementById('panelTitleAddon');
            if (p4Header) p4Header.childNodes[0].nodeValue = dict.panel_addon + ' ';
            setTxt('textTambahAddon', dict.btn_tambah_addon);

            // Panel 5
            const p5Header = document.getElementById('panelTitleTermin');
            if (p5Header) p5Header.childNodes[0].nodeValue = dict.panel_termin + ' ';
            setTxt('labelTerminSaatIni', dict.label_termin_saat_ini);
            setTxt('optTermin1', dict.termin_opt_dp1);
            setTxt('optTermin2', dict.termin_opt_dp2);
            setTxt('optTermin3', dict.termin_opt_pelunasan);
            setTxt('labelNominalDP1', dict.label_dp1);
            setTxt('labelNominalDP2', dict.label_dp2);
            setTxt('labelSisaSummary', dict.label_sisa_tagihan);
            setTxt('labelTglJatuhTempo', dict.label_jatuh_tempo);
            setTxt('labelNamaBank', dict.label_nama_bank);
            setTxt('labelNoRekening', dict.label_no_rekening);
            setTxt('labelAtasNama', dict.label_atas_nama);
            setTxt('textBank', dict.btn_simpan_bank);

            // Panel 6
            const p6Header = document.getElementById('panelTitleSyarat');
            if (p6Header) p6Header.childNodes[0].nodeValue = dict.panel_terms + ' ';
            setTxt('infoTerms', dict.note_terms);
            setTxt('textTambahPoin', dict.btn_tambah_poin);
            setTxt('textResetDefault', dict.btn_reset_terms);

            // Desain Style
            setHtml('sectionTitleDesign', `<i class="fa-solid fa-palette"></i> ${dict.title_desain}`);
            const d1 = document.getElementById('cardThemeMinimalist');
            if (d1) d1.childNodes[0].nodeValue = dict.theme_minimalist + ' ';
            const d2 = document.getElementById('cardThemeCreative');
            if (d2) d2.childNodes[0].nodeValue = dict.theme_creative + ' ';
            const d3 = document.getElementById('cardThemeCorporate');
            if (d3) d3.childNodes[0].nodeValue = dict.theme_corporate + ' ';
            const d4 = document.getElementById('cardThemeModern');
            if (d4) d4.childNodes[0].nodeValue = dict.theme_modern + ' ';
            const d5 = document.getElementById('cardThemeClassic');
            if (d5) d5.childNodes[0].nodeValue = dict.theme_classic + ' ';

            // Live Preview
            setHtml('sectionTitlePreview', `<i class="fa-regular fa-eye"></i> ${dict.title_preview}`);
            setTxt('labelPreviewKepada', dict.preview_kepada);
            setTxt('labelPreviewTglAcara', dict.preview_tgl_acara);
            setTxt('labelPreviewThDesc', dict.preview_th_desc);
            setTxt('labelPreviewThPrice', dict.preview_th_price);
            setTxt('labelPreviewPaketUtama', dict.preview_paket_utama);
            setTxt('labelPreviewSubtotalPaket', dict.preview_subtotal_paket);
            setTxt('labelPreviewTotalAddon', dict.preview_total_addon);
            setTxt('labelPreviewDiskon', dict.preview_diskon);
            setTxt('labelPreviewGrandTotal', dict.preview_grand_total);
            setTxt('labelPreviewSisaTagihan', dict.preview_sisa_tagihan);
            setTxt('labelPreviewMetodeBayar', dict.preview_metode_bayar);
            setTxt('labelPreviewJatuhTempoPrefix', dict.preview_jatuh_tempo_prefix);
            setTxt('labelPreviewTermsHeader', dict.preview_terms_header);
            setTxt('labelPreviewTtd', dict.preview_ttd);

            // Signature
            setHtml('sectionTitleSignature', `<i class="fa-solid fa-pen-nib"></i> ${dict.title_ttd_section}`);
            setTxt('textUploadTtd', dict.btn_unggah_ttd);
            setTxt('btnHapusTtd', dict.btn_hapus_ttd);
            setTxt('textTtd', dict.btn_simpan_ttd);
            setTxt('badgeLogoSiap', dict.badge_logo_ready);
            setTxt('badgeTtdText', dict.badge_ttd_ready);

            // Action Buttons
            setHtml('btnUnduhPng', `<i class="fa-regular fa-image"></i> ${dict.btn_unduh_png}`);
            setHtml('btnUnduhPdf', `<i class="fa-regular fa-file-pdf"></i> ${dict.btn_unduh_pdf}`);
            setHtml('btnSimpanFolder', `<i class="fa-regular fa-folder-open"></i> ${dict.btn_simpan_folder}`);

            // WA Draft
            setHtml('sectionTitleWaDraft', `<i class="fa-regular fa-message"></i> ${dict.title_wa_draft}`);
            setTxt('badgeToneRamah', dict.tone_ramah);
            setTxt('badgeToneFormal', dict.tone_formal);
            setTxt('badgeToneRingkas', dict.tone_ringkas);
            setTxt('badgeToneReminder', dict.tone_reminder);
            setTxt('labelWaEditNote', dict.note_wa_edit);
            setHtml('btnKirimWa', `<i class="fa-regular fa-paper-plane"></i> <span id="textKirimWa">${dict.btn_kirim_wa}</span>`);

            // Saved Invoices Tab
            const searchInput = document.getElementById('searchSavedInvoice');
            if (searchInput) searchInput.placeholder = dict.search_placeholder;
            setTxt('btnDateFilterAll', dict.filter_all_dates);
            setTxt('btnDateFilterThisMonth', dict.filter_this_month);
            setTxt('btnDateFilterLastMonth', dict.filter_last_month);
            setTxt('btnDateFilterLast30', dict.filter_last_30);
            setTxt('btnDateFilterThisYear', dict.filter_this_year);
            setTxt('labelCustomRangeBtn', dict.filter_custom_range);
            setHtml('btnExportCsv', `<i class="fa-solid fa-file-csv" style="color: #64748b;"></i> <span id="textExportCsv">${dict.btn_export_csv}</span>`);
            setTxt('btnCalReset', dict.cal_reset);
            setTxt('btnCalApply', dict.cal_apply);

            const pills = document.querySelectorAll('.status-filter-pill');
            if (pills.length >= 4) {
                pills[0].innerText = dict.pill_all;
                pills[1].innerText = dict.pill_unpaid;
                pills[2].innerText = dict.pill_partial;
                pills[3].innerText = dict.pill_paid;
            }

            syncTerminPreview();
            syncJatuhTempoPreview();
        }

        function updatePreviewInvoiceDate() {
            const el = document.getElementById('previewInvoiceDate');
            if (!el) return;
            const now = new Date();
            const monthsId = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            const monthsEn = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
            if (currentLang === 'en') {
                el.innerText = `${monthsEn[now.getMonth()]} ${now.getDate()}, ${now.getFullYear()}`;
            } else {
                el.innerText = `${now.getDate()} ${monthsId[now.getMonth()]} ${now.getFullYear()}`;
            }
        }

        // --- Dynamic WhatsApp Draft Generation ---
        let currentWaTone = 'ramah';

        function getWaDraftMessage(tone, lang) {
            const clientName = document.getElementById('inputNamaKlien')?.value?.trim() || '';
            const vendorName = document.getElementById('inputNamaVendor')?.value?.trim() || 'Fariz hidayat';
            
            // Format termin label according to language
            const selectTermin = document.getElementById('selectTermin');
            let terminLabel = selectTermin ? selectTermin.options[selectTermin.selectedIndex]?.text : 'Uang Muka / DP 1';
            if (!terminLabel) terminLabel = 'Uang Muka / DP 1';

            const invNo = document.getElementById('inputNoInvoice')?.value?.trim() || document.getElementById('previewNoInvoice')?.innerText?.trim() || 'INV-202610-006';
            const bankName = document.getElementById('inputNamaBank')?.value?.trim() || 'BCA';
            const bankAccNo = document.getElementById('inputNoRekening')?.value?.trim() || '0431063867';
            const bankAccName = document.getElementById('inputAtasNama')?.value?.trim() || 'Umi Salamah';
            
            // Current term nominal
            const dp1Val = parseCurrency(document.getElementById('inputDP1')?.value);
            const dp2Val = parseCurrency(document.getElementById('inputDP2')?.value);
            const grandTotalVal = parseCurrency(document.getElementById('calcGrandTotal')?.innerText);
            
            let termNominal = dp1Val;
            if (selectTermin && selectTermin.value.includes('DP 2')) {
                termNominal = dp2Val;
            } else if (selectTermin && selectTermin.value.includes('Pelunasan')) {
                if (typeof pelunasanMode !== 'undefined' && pelunasanMode === 'manual') {
                    const manualDp = parseCurrency(document.getElementById('inputManualTotalDP')?.value);
                    termNominal = Math.max(0, grandTotalVal - manualDp);
                } else {
                    termNominal = Math.max(0, grandTotalVal - (dp1Val + dp2Val));
                }
            }
            const terminNominalText = 'Rp ' + formatRupiah(termNominal);

            if (lang === 'en') {
                if (tone === 'formal') {
                    return `Dear Mr./Ms. ${clientName ? clientName + ' ' : ''},

Here is the official invoice from *${vendorName}* for *${terminLabel}*.

• No. Invoice: ${invNo}
• Amount Due: *${terminNominalText}*
• Payment Account: *${bankName}* ${bankAccNo} a.n. ${bankAccName}

Invoice document with package details and Terms & Conditions is attached.
Thank you for your cooperation.`;
                } else if (tone === 'ringkas') {
                    return `*INVOICE — ${vendorName}*
Client: ${clientName}
Due: ${terminLabel} — *${terminNominalText}*
Transfer to: *${bankName}* ${bankAccNo} a.n. ${bankAccName}

Digital invoice attached. Please send payment proof once transferred.`;
                } else if (tone === 'reminder') {
                    return `Hi ${clientName ? 'Kak ' + clientName : 'Kak '}, greetings from *${vendorName}*. 🙏

Just a gentle reminder for invoice *${terminLabel}* amounting to *${terminNominalText}*.

Payment can be transferred to:
*${bankName}* ${bankAccNo} a.n. ${bankAccName}

If you have already paid, please disregard this message or share the receipt. Thank you very much! 🤍`;
                } else {
                    // friendly / ramah
                    return `Hi ${clientName ? 'Kak ' + clientName : 'Kak '}, hope your preparations are going smoothly. ✨ 
This is *${vendorName}* sending the invoice for *${terminLabel}*. 

The amount due for this term is *${terminNominalText}* 

Payable to:
 *${bankName}* ${bankAccNo} a.n. ${bankAccName}. 

Full invoice details (package, discount, add-ons, and T&C) are attached. 
Thank you! 🤍`;
                }
            } else {
                // Indonesian
                if (tone === 'formal') {
                    return `Kepada Yth. Bapak/Ibu ${clientName ? clientName + ' ' : ''},

Berikut kami sampaikan tagihan resmi dari *${vendorName}* untuk *${terminLabel}*.

• No. Invoice: ${invNo}
• Jumlah Tagihan: *${terminNominalText}*
• Rekening Pembayaran: *${bankName}* ${bankAccNo} a.n ${bankAccName}

Dokumen invoice beserta rincian paket dan Syarat & Ketentuan terlampir.
Atas perhatian dan kerja samanya, kami ucapkan terima kasih.`;
                } else if (tone === 'ringkas') {
                    return `*INVOICE — ${vendorName}*
Klien: ${clientName}
Tagihan: ${terminLabel} — *${terminNominalText}*
Transfer ke: *${bankName}* ${bankAccNo} a.n ${bankAccName}

Invoice digital terlampir. Mohon konfirmasi bukti transfer jika sudah dibayar ya.`;
                } else if (tone === 'reminder') {
                    return `Halo Kak ${clientName ? clientName + ' ' : ''}, salam dari *${vendorName}*. 🙏

Sekadar mengingatkan untuk invoice *${terminLabel}* sebesar *${terminNominalText}*.

Pembayaran dapat ditransfer ke:
*${bankName}* ${bankAccNo} a.n ${bankAccName}

Jika pembayaran sudah dilakukan, mohon abaikan pesan ini atau kirimkan buktinya ya Kak. Terima kasih banyak! 🤍`;
                } else {
                    // ramah
                    return `Halo Kak ${clientName ? clientName + ' ' : ''}, semoga persiapannya lancar ya. ✨ 
Kami dari *${vendorName}* ingin mengirimkan invoice untuk *${terminLabel}*. 

Total tagihan untuk termin ini sebesar *${terminNominalText}* 

Dapat ditransfer ke:
 *${bankName}* ${bankAccNo} a.n ${bankAccName}. 

Detail invoice (termasuk rincian paket, diskon, add-on, serta Syarat & Ketentuan) terlampir ya Kak. 
Terima kasih banyak! 🤍`;
                }
            }
        }

        function updateWaDraft() {
            const waTextarea = document.getElementById('wa-draft-textarea');
            if (waTextarea) {
                waTextarea.value = getWaDraftMessage(currentWaTone, currentLang);
            }
        }

        document.querySelectorAll('.tone-badge').forEach(badge => {
            badge.addEventListener('click', () => {
                document.querySelectorAll('.tone-badge').forEach(b => b.classList.remove('active'));
                badge.classList.add('active');
                const tone = badge.getAttribute('data-tone');
                if (tone) {
                    currentWaTone = tone;
                    updateWaDraft();
                }
            });
        });

        function kirimWhatsApp() {
            const rawPhone = document.getElementById('inputNoWaKlien')?.value?.trim() || '';
            if (!rawPhone) {
                showToastNotification(currentLang === 'en' ? "Client WhatsApp number is required" : "Nomor WhatsApp klien belum diisi");
                document.getElementById('inputNoWaKlien')?.focus();
                return;
            }
            let cleanPhone = rawPhone.replace(/[^0-9]/g, '');
            if (cleanPhone.startsWith('0')) {
                cleanPhone = '62' + cleanPhone.slice(1);
            } else if (cleanPhone.startsWith('8')) {
                cleanPhone = '62' + cleanPhone;
            }
            const message = document.getElementById('wa-draft-textarea')?.value || '';
            const encoded = encodeURIComponent(message);
            window.open(`https://wa.me/${cleanPhone}?text=${encoded}`, '_blank');
        }

        // --- Live Preview & Calculation Functions ---
        function syncPreview(inputId, previewId) {
            const inputVal = document.getElementById(inputId)?.value || '';
            const previewEl = document.getElementById(previewId);
            if (previewEl) previewEl.innerText = inputVal;
            
            if (inputId === 'inputNoInvoice') {
                const autoInvoiceEl = document.getElementById('previewAutoInvoice');
                if (autoInvoiceEl) autoInvoiceEl.innerText = inputVal || 'INV-202610-006';
            }
        }

        function syncPreviewClientWa() {
            const nama = document.getElementById('inputNamaKlien')?.value || '';
            const rawWa = document.getElementById('inputNoWaKlien')?.value || '';
            const alamat = document.getElementById('inputAlamatKlien')?.value || '';

            const pNama = document.getElementById('previewNamaKlien');
            const pWa = document.getElementById('previewWaKlien');
            const pAlamat = document.getElementById('previewAlamatKlien');
            const helper = document.getElementById('helperWaKlien');

            if (pNama) pNama.innerText = nama || "-";

            let cleanWa = rawWa.replace(/[^0-9]/g, '');
            if (cleanWa.startsWith('0')) {
                cleanWa = '62' + cleanWa.slice(1);
            } else if (cleanWa.startsWith('8')) {
                cleanWa = '62' + cleanWa;
            }

            if (cleanWa) {
                if (pWa) pWa.innerText = "+" + cleanWa;
                if (helper) helper.innerText = "+" + cleanWa;
            } else {
                if (pWa) pWa.innerText = "-";
                if (helper) helper.innerText = "+628xxxxxxxxxx";
            }

            if (alamat) {
                if (pAlamat) {
                    pAlamat.innerText = alamat;
                    pAlamat.style.display = 'block';
                }
            } else {
                if (pAlamat) pAlamat.style.display = 'none';
            }
        }

        function syncPreviewDate() {
            const tgl = document.getElementById('inputTglAcara')?.value || '';
            const previewEl = document.getElementById('previewTglAcara');
            if (previewEl) {
                if (tgl) {
                    previewEl.innerText = formatTglIndo(tgl) || tgl;
                } else {
                    previewEl.innerText = "-";
                }
            }
        }

        function formatRupiah(number) {
            return new Intl.NumberFormat('id-ID').format(number);
        }

        function parseCurrency(str) {
            if (!str) return 0;
            const clean = str.toString().replace(/[^0-9]/g, '');
            return parseInt(clean, 10) || 0;
        }

        function handleCurrencyInput(input) {
            let val = parseCurrency(input.value);
            if (val === 0 && input.value === '') {
                input.value = '';
            } else {
                input.value = 'Rp ' + formatRupiah(val);
            }
            calculateTotals();
            updateWaDraft();
        }

        function calculateTotals() {
            const rawMainName = document.getElementById('inputPaketUtama')?.value?.trim();
            const mainPrice = parseCurrency(document.getElementById('inputHargaUtama')?.value);
            const mainQty = parseInt(document.getElementById('inputJumlahPaket')?.value, 10) || 1;
            const discount = parseCurrency(document.getElementById('inputDiskon')?.value);

            let subtotalPaket = mainPrice * mainQty;
            let totalDiscount = discount;

            let liveRowsHtml = '';
            if (mainPrice > 0 || rawMainName) {
                const mainName = rawMainName || (currentLang === 'en' ? 'Main Package' : 'Paket Utama');
                liveRowsHtml += `
                <tr>
                    <td style="padding: 12px 16px; border-bottom: 1px solid #f1f1f4;">
                        <div style="font-size: 13px; font-weight: 500; color: #333; margin-bottom: 2px;">${mainName}</div>
                        <div style="font-size: 10px; color: #888;">${currentLang === 'en' ? 'Main Package' : 'Paket Utama'}${mainQty > 1 ? ` &bull; ${mainQty} pcs` : ''}</div>
                    </td>
                    <td style="text-align: right; padding: 12px 16px; border-bottom: 1px solid #f1f1f4; font-size: 13px; color: #333;">
                        Rp ${formatRupiah(mainPrice * mainQty)}
                    </td>
                </tr>`;
            }

            // Loop Paket Tambahan
            const extraBoxes = document.querySelectorAll('.paket-tambahan-box');
            extraBoxes.forEach(box => {
                const rawName = box.querySelector('.pkt-extra-name')?.value?.trim();
                const price = parseCurrency(box.querySelector('.pkt-extra-price')?.value);
                const disc = parseCurrency(box.querySelector('.pkt-extra-disc')?.value);

                subtotalPaket += price;
                totalDiscount += disc;

                if (price > 0 || disc > 0 || rawName) {
                    const name = rawName || (currentLang === 'en' ? 'Additional Package' : 'Paket Tambahan');
                    liveRowsHtml += `
                    <tr>
                        <td style="padding: 12px 16px; border-bottom: 1px solid #f1f1f4;">
                            <div style="font-size: 13px; font-weight: 500; color: #333; margin-bottom: 2px;">${name}</div>
                            <div style="font-size: 10px; color: #888;">${currentLang === 'en' ? 'Additional Package' : 'Paket Tambahan'}</div>
                        </td>
                        <td style="text-align: right; padding: 12px 16px; border-bottom: 1px solid #f1f1f4; font-size: 13px; color: #333;">
                            Rp ${formatRupiah(price)}
                        </td>
                    </tr>`;
                }
            });

            // Loop Addons
            let totalAddon = 0;
            const addonBoxes = document.querySelectorAll('.addon-item-box');
            addonBoxes.forEach(box => {
                const rawName = box.querySelector('.addon-name')?.value?.trim();
                const qty = parseInt(box.querySelector('.addon-qty')?.value, 10) || 1;
                const price = parseCurrency(box.querySelector('.addon-price')?.value);
                const lineTotal = qty * price;
                totalAddon += lineTotal;

                const formulaEl = box.querySelector('.addon-calc-formula');
                const resultEl = box.querySelector('.addon-calc-result');
                if (formulaEl) formulaEl.innerText = `${qty} x Rp ${formatRupiah(price)} = `;
                if (resultEl) resultEl.innerText = `Rp ${formatRupiah(lineTotal)}`;

                if (price > 0 || rawName) {
                    const name = rawName || 'Add-on';
                    liveRowsHtml += `
                    <tr>
                        <td style="padding: 12px 16px; border-bottom: 1px solid #f1f1f4;">
                            <div style="font-size: 13px; font-weight: 500; color: #333; margin-bottom: 2px;">${name}</div>
                            <div style="font-size: 10px; color: #888;">Add-on &bull; ${qty} pcs</div>
                        </td>
                        <td style="text-align: right; padding: 12px 16px; border-bottom: 1px solid #f1f1f4; font-size: 13px; color: #333;">
                            Rp ${formatRupiah(lineTotal)}
                        </td>
                    </tr>`;
                }
            });

            // Unified Discount Row in Table (Image 5)
            if (totalDiscount > 0) {
                liveRowsHtml += `
                <tr>
                    <td style="padding: 12px 16px; border-bottom: 1px solid #f1f1f4;">
                        <div style="font-size: 13px; font-weight: 500; color: #333; margin-bottom: 2px;">${currentLang === 'en' ? 'Discount / Price Reduction' : 'Diskon / Potongan Harga'}</div>
                        <div style="font-size: 10px; color: #888;">${currentLang === 'en' ? 'Special offer' : 'Penawaran khusus'}</div>
                    </td>
                    <td style="text-align: right; padding: 12px 16px; border-bottom: 1px solid #f1f1f4; font-size: 13px; color: #f1416c; font-weight: 500;">
                        - Rp ${formatRupiah(totalDiscount)}
                    </td>
                </tr>`;
            }

            const grandTotal = Math.max(0, (subtotalPaket + totalAddon) - totalDiscount);

            // Update UI Counters in Form
            const calcSubtotal = document.getElementById('calcSubtotal');
            if (calcSubtotal) calcSubtotal.innerText = 'Rp ' + formatRupiah(subtotalPaket);

            const calcAddon = document.getElementById('calcAddon');
            if (calcAddon) calcAddon.innerText = 'Rp ' + formatRupiah(totalAddon);

            const calcGrandTotal = document.getElementById('calcGrandTotal');
            if (calcGrandTotal) calcGrandTotal.innerText = 'Rp ' + formatRupiah(grandTotal);

            // Update Live Preview Paper
            const tableBody = document.getElementById('previewTableBody');
            if (tableBody) tableBody.innerHTML = liveRowsHtml || `<tr><td colspan="2" style="text-align: center; color: #a1a5b7; padding: 20px;">${currentLang === 'en' ? 'No items yet' : 'Belum ada item'}</td></tr>`;

            const pSub = document.getElementById('previewSubtotal') || document.getElementById('previewSubtotalPaket');
            if (pSub) pSub.innerText = 'Rp ' + formatRupiah(subtotalPaket);

            const pAdd = document.getElementById('previewAddon') || document.getElementById('previewTotalAddon');
            if (pAdd) pAdd.innerText = 'Rp ' + formatRupiah(totalAddon);

            const pDis = document.getElementById('previewDiskon');
            const rowDis = document.getElementById('rowPreviewDiskon');
            if (totalDiscount > 0) {
                if (rowDis) rowDis.style.display = 'flex';
                if (pDis) pDis.innerText = '- Rp ' + formatRupiah(totalDiscount);
            } else {
                if (rowDis) rowDis.style.display = 'none';
            }

            const pGrand = document.getElementById('previewGrandTotal');
            if (pGrand) pGrand.innerText = 'Rp ' + formatRupiah(grandTotal);

            // Update Termin & Balance
            syncTerminPreview();
        }

        let pelunasanMode = 'auto'; // 'auto' | 'manual'

        function setPelunasanMode(mode) {
            pelunasanMode = mode;
            const btnAuto = document.getElementById('btnPelunasanAuto');
            const btnManual = document.getElementById('btnPelunasanManual');
            const manualGroup = document.getElementById('pelunasanManualInputGroup');
            const autoNote = document.getElementById('pelunasanAutoNote');

            if (mode === 'manual') {
                if (btnAuto) {
                    btnAuto.style.background = '#fff';
                    btnAuto.style.color = '#555';
                    btnAuto.style.border = '1px solid #dcdcdc';
                }
                if (btnManual) {
                    btnManual.style.background = '#6c5ce7';
                    btnManual.style.color = '#fff';
                    btnManual.style.border = '1px solid #6c5ce7';
                }
                if (manualGroup) manualGroup.style.display = 'block';
                if (autoNote) autoNote.style.display = 'none';

                const manualInput = document.getElementById('inputManualTotalDP');
                if (manualInput && (!manualInput.value || parseCurrency(manualInput.value) === 0)) {
                    const dp1Val = parseCurrency(document.getElementById('inputDP1')?.value);
                    const dp2Val = parseCurrency(document.getElementById('inputDP2')?.value);
                    const initVal = (dp1Val + dp2Val) > 0 ? (dp1Val + dp2Val) : 0;
                    manualInput.value = 'Rp ' + formatRupiah(initVal);
                }
            } else {
                if (btnAuto) {
                    btnAuto.style.background = '#6c5ce7';
                    btnAuto.style.color = '#fff';
                    btnAuto.style.border = '1px solid #6c5ce7';
                }
                if (btnManual) {
                    btnManual.style.background = '#fff';
                    btnManual.style.color = '#555';
                    btnManual.style.border = '1px solid #dcdcdc';
                }
                if (manualGroup) manualGroup.style.display = 'none';
                if (autoNote) autoNote.style.display = 'block';
            }

            calculateTotals();
            updateWaDraft();
        }

        function syncTerminPreview() {
            const grandTotal = parseCurrency(document.getElementById('calcGrandTotal')?.innerText);
            const selectTermin = document.getElementById('selectTermin');
            const terminType = selectTermin ? selectTermin.options[selectTermin.selectedIndex]?.text : 'Uang Muka / DP 1';
            const terminVal = selectTermin ? selectTermin.value : 'Uang Muka / DP 1';

            const dp1Val = parseCurrency(document.getElementById('inputDP1')?.value);
            const dp2Val = parseCurrency(document.getElementById('inputDP2')?.value);

            const isDp2 = terminVal.includes('DP 2');
            const isPelunasan = terminVal.includes('Pelunasan');

            // Card DP Sebelumnya visibility
            const cardDpSebelumnya = document.getElementById('cardDpSebelumnya');
            if (cardDpSebelumnya) {
                cardDpSebelumnya.style.display = isPelunasan ? 'block' : 'none';
            }

            // Total dari input DP 1 & DP 2
            const totalDpInputs = dp1Val + dp2Val;
            const sisaInput = Math.max(0, grandTotal - totalDpInputs);

            // Summary card (Row 3)
            const lblSummary = document.getElementById('labelTerminSummary');
            if (lblSummary) {
                if (dp2Val > 0 || isPelunasan) {
                    lblSummary.innerText = currentLang === 'en' ? 'Total DP 1 + DP 2' : 'Total DP 1 + DP 2';
                } else {
                    lblSummary.innerText = currentLang === 'en' ? 'Total DP 1' : 'Total DP 1';
                }
            }
            const valSummary = document.getElementById('valTerminSummary');
            if (valSummary) valSummary.innerText = 'Rp ' + formatRupiah(totalDpInputs);

            const valSisa = document.getElementById('valSisaSummary');
            if (valSisa) valSisa.innerText = 'Rp ' + formatRupiah(sisaInput);

            // Perhitungan DP & Sisa untuk Pelunasan
            let effectiveDpPaid = totalDpInputs;
            let effectiveSisaHarusDibayar = sisaInput;

            if (isPelunasan) {
                if (pelunasanMode === 'manual') {
                    const manualDp = parseCurrency(document.getElementById('inputManualTotalDP')?.value);
                    effectiveDpPaid = manualDp;
                    effectiveSisaHarusDibayar = Math.max(0, grandTotal - manualDp);
                } else {
                    effectiveDpPaid = totalDpInputs;
                    effectiveSisaHarusDibayar = sisaInput;
                }

                const valPelunasanDp = document.getElementById('valPelunasanDpSudahDibayar');
                if (valPelunasanDp) {
                    valPelunasanDp.innerText = '- Rp ' + formatRupiah(effectiveDpPaid);
                }
                const valPelunasanSisa = document.getElementById('valPelunasanSisaHarusDibayar');
                if (valPelunasanSisa) {
                    valPelunasanSisa.innerText = 'Rp ' + formatRupiah(effectiveSisaHarusDibayar);
                }
            }

            // Paper Preview
            const pTerminLbl = document.getElementById('previewTerminLabel');
            if (pTerminLbl) pTerminLbl.innerText = terminType;

            const pDp1 = document.getElementById('previewDP1');
            if (pDp1) {
                pDp1.innerText = isPelunasan ? ('- Rp ' + formatRupiah(dp1Val)) : ('Rp ' + formatRupiah(dp1Val));
            }

            const rowDp2 = document.getElementById('previewDP2Row');
            const pDp2 = document.getElementById('previewDP2');
            if (dp2Val > 0 || isPelunasan) {
                if (rowDp2) rowDp2.style.display = 'flex';
                if (pDp2) {
                    pDp2.innerText = isPelunasan ? ('- Rp ' + formatRupiah(dp2Val)) : ('Rp ' + formatRupiah(dp2Val));
                }
            } else {
                if (rowDp2) rowDp2.style.display = 'none';
            }

            const pTotDp = document.getElementById('previewTotalDP');
            const pTotDpLbl = document.getElementById('previewTotalDPLabel');
            if (pTotDp) {
                pTotDp.innerText = isPelunasan ? ('- Rp ' + formatRupiah(totalDpInputs)) : ('Rp ' + formatRupiah(totalDpInputs));
            }
            if (pTotDpLbl) {
                pTotDpLbl.innerText = (dp2Val > 0 || isPelunasan) ? (currentLang === 'en' ? 'Total DP 1 + DP 2' : 'Total DP 1 + DP 2') : (currentLang === 'en' ? 'Total DP 1' : 'Total DP 1');
            }

            const pSisaLbl = document.getElementById('labelPreviewSisaTagihan');
            if (pSisaLbl) {
                pSisaLbl.innerText = isPelunasan ? (currentLang === 'en' ? 'Sisa yang harus dibayar' : 'Sisa yang harus dibayar') : (currentLang === 'en' ? 'Remaining Balance' : 'Sisa Tagihan');
            }

            const pSisa = document.getElementById('previewSisaTagihan');
            if (pSisa) {
                pSisa.innerText = 'Rp ' + formatRupiah(isPelunasan ? effectiveSisaHarusDibayar : sisaInput);
            }
        }

        function syncJatuhTempoPreview() {
            const val = document.getElementById('inputTglJatuhTempo')?.value || '';
            const el = document.getElementById('previewJatuhTempoDate');
            const row = document.getElementById('previewJatuhTempoRow');
            if (el && row) {
                if (val) {
                    el.innerText = formatTglIndo(val) || val;
                    row.style.display = 'block';
                } else {
                    row.style.display = 'none';
                }
            }
        }

        function syncBankPreview() {
            const nama = document.getElementById('inputNamaBank')?.value || 'BCA';
            const rek = document.getElementById('inputNoRekening')?.value || '-';
            const an = document.getElementById('inputAtasNama')?.value || '-';
            
            const bNama = document.getElementById('previewBankName') || document.getElementById('previewBankNama');
            if (bNama) bNama.innerText = nama;

            const bRek = document.getElementById('previewNoRek') || document.getElementById('previewBankRekening');
            if (bRek) bRek.innerText = rek;

            const bAn = document.getElementById('previewAtasNama') || document.getElementById('previewBankAn');
            if (bAn) bAn.innerText = an;
        }

        function simpanBank() {
            const bank_name = document.getElementById('inputNamaBank')?.value || '';
            const bank_account_number = document.getElementById('inputNoRekening')?.value || '';
            const bank_account_name = document.getElementById('inputAtasNama')?.value || '';

            fetch('/api/invoices/save-bank', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ bank_name, bank_account_number, bank_account_name })
            })
            .then(res => res.json())
            .then(data => {
                showToastNotification(currentLang === 'en' ? "Bank details saved" : "Data bank berhasil disimpan");
            })
            .catch(() => {
                showToastNotification(currentLang === 'en' ? "Bank details saved locally" : "Data bank tersimpan di browser");
            });
        }

        function simpanBisnis() {
            const vendor_name = document.getElementById('inputNamaVendor')?.value || '';
            const vendor_address = document.getElementById('inputAlamatVendor')?.value || '';
            fetch('/api/invoices/save-business', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ vendor_name, vendor_address })
            })
            .then(res => res.json())
            .then(() => {
                showToastNotification(currentLang === 'en' ? "Business info saved" : "Informasi bisnis berhasil disimpan");
            })
            .catch(() => {
                showToastNotification(currentLang === 'en' ? "Business info saved" : "Informasi bisnis berhasil disimpan");
            });
        }

        function simpanFormat() {
            const format = document.getElementById('inputPrefixInvoice')?.value || 'INV-{YYYY}{MM}-{SEQ3}';
            fetch('/api/invoices/save-format', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ format })
            })
            .then(res => res.json())
            .then(() => {
                showToastNotification(currentLang === 'en' ? "Invoice format saved" : "Format invoice berhasil disimpan");
            })
            .catch(() => {
                showToastNotification(currentLang === 'en' ? "Invoice format saved" : "Format invoice berhasil disimpan");
            });
        }

        function simpanBiaya() {
            const name = document.getElementById('inputPaketUtama')?.value?.trim();
            const price = parseCurrency(document.getElementById('inputHargaUtama')?.value);
            const discount = parseCurrency(document.getElementById('inputDiskon')?.value);

            const extraPackages = [];
            document.querySelectorAll('.paket-tambahan-box').forEach(box => {
                const eName = box.querySelector('.pkt-extra-name')?.value?.trim();
                const ePrice = parseCurrency(box.querySelector('.pkt-extra-price')?.value);
                const eDisc = parseCurrency(box.querySelector('.pkt-extra-disc')?.value);
                if (eName || ePrice > 0) {
                    extraPackages.push({ name: eName || (currentLang === 'en' ? 'Additional Package' : 'Paket Tambahan'), price: ePrice, discount: eDisc });
                }
            });

            if (!name && extraPackages.length === 0) {
                alert("Silakan masukkan nama paket terlebih dahulu");
                return;
            }

            fetch('/api/invoices/save-package', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ name, price, discount, extra_packages: extraPackages })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showToastNotification(currentLang === 'en' ? "Package & price saved" : "Paket & harga disimpan");
                } else {
                    alert(data.error || "Gagal menyimpan paket");
                }
            })
            .catch(() => {
                showToastNotification(currentLang === 'en' ? "Package & price saved" : "Paket & harga disimpan");
            });
        }

        function simpanTtd() {
            showToastNotification(currentLang === 'en' ? "Signature saved" : "Tanda tangan berhasil disimpan");
        }

        // --- Logo & Signature Handlers ---
        function previewLogoFile(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const thumb = document.getElementById('logoImgThumbnail');
                    const icon = document.getElementById('logoIconPlaceholder');
                    const btnHapus = document.getElementById('btnHapusLogo');
                    const prevLogo = document.getElementById('previewLogoImg');
                    const prevIcon = document.getElementById('previewLogoPlaceholder');

                    if (thumb) { thumb.src = e.target.result; thumb.style.display = 'block'; }
                    if (icon) icon.style.display = 'none';
                    if (btnHapus) btnHapus.style.display = 'inline';
                    if (prevLogo) { prevLogo.src = e.target.result; prevLogo.style.display = 'block'; }
                    if (prevIcon) prevIcon.style.display = 'none';
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        function hapusLogo() {
            const thumb = document.getElementById('logoImgThumbnail');
            const icon = document.getElementById('logoIconPlaceholder');
            const btnHapus = document.getElementById('btnHapusLogo');
            const prevLogo = document.getElementById('previewLogoImg');
            const prevIcon = document.getElementById('previewLogoPlaceholder');
            const input = document.getElementById('logoInput');

            if (thumb) { thumb.src = ''; thumb.style.display = 'none'; }
            if (icon) icon.style.display = 'block';
            if (btnHapus) btnHapus.style.display = 'none';
            if (prevLogo) { prevLogo.src = ''; prevLogo.style.display = 'none'; }
            if (prevIcon) prevIcon.style.display = 'block';
            if (input) input.value = '';
        }

        function previewTtdFile(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const thumb = document.getElementById('ttdImgThumbnail');
                    const icon = document.getElementById('ttdIconPlaceholder');
                    const btnHapus = document.getElementById('btnHapusTtd');
                    const prevTtd = document.getElementById('previewTtdImg');
                    const prevPlc = document.getElementById('previewTtdPlaceholder');

                    if (thumb) { thumb.src = e.target.result; thumb.style.display = 'block'; }
                    if (icon) icon.style.display = 'none';
                    if (btnHapus) btnHapus.style.display = 'inline';
                    if (prevTtd) { prevTtd.src = e.target.result; prevTtd.style.display = 'block'; }
                    if (prevPlc) prevPlc.style.display = 'none';
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        function hapusTtd() {
            const thumb = document.getElementById('ttdImgThumbnail');
            const icon = document.getElementById('ttdIconPlaceholder');
            const btnHapus = document.getElementById('btnHapusTtd');
            const prevTtd = document.getElementById('previewTtdImg');
            const prevPlc = document.getElementById('previewTtdPlaceholder');
            const input = document.getElementById('ttdInput');

            if (thumb) { thumb.src = ''; thumb.style.display = 'none'; }
            if (icon) icon.style.display = 'block';
            if (btnHapus) btnHapus.style.display = 'none';
            if (prevTtd) { prevTtd.src = ''; prevTtd.style.display = 'none'; }
            if (prevPlc) prevPlc.style.display = 'inline';
            if (input) input.value = '';
        }

        // --- Additional Package & Addons Dynamic UI ---
        function addPaketTambahan() {
            const container = document.getElementById('paketTambahanContainer');
            if (!container) return;
            const div = document.createElement('div');
            div.className = 'paket-tambahan-box';
            div.style = "border: 1px solid var(--border-color); border-radius: 8px; padding: 16px; margin-bottom: 16px; position: relative; background: #ffffff;";
            div.innerHTML = `
                <button type="button" class="btn-trash" onclick="removePaketTambahan(this)" title="Hapus paket tambahan" style="position: absolute; right: 12px; top: 12px; background: none; border: none; color: var(--text-muted); cursor: pointer; font-size: 15px;">
                    <i class="fa-regular fa-trash-can"></i>
                </button>
                <div class="form-group" style="margin-bottom: 12px;">
                    <label class="form-label">${currentLang === 'en' ? 'Additional Package Name' : 'Nama Paket Tambahan'}</label>
                    <input type="text" class="form-control pkt-extra-name" placeholder="${currentLang === 'en' ? 'Package name' : 'Nama Paket Tambahan'}" oninput="calculateTotals(); updateWaDraft();">
                </div>
                <div class="form-row form-row-2col" style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                    <div>
                        <label class="form-label">${currentLang === 'en' ? 'Package Price' : 'Harga Paket'}</label>
                        <input type="text" class="form-control pkt-extra-price" placeholder="Rp 0" oninput="handleCurrencyInput(this); calculateTotals();">
                    </div>
                    <div>
                        <label class="form-label">${currentLang === 'en' ? 'Discount' : 'Diskon'}</label>
                        <input type="text" class="form-control pkt-extra-disc" placeholder="Rp 0" oninput="handleCurrencyInput(this); calculateTotals();">
                    </div>
                </div>
            `;
            container.appendChild(div);
            calculateTotals();
        }

        function removePaketTambahan(btn) {
            btn.closest('.paket-tambahan-box')?.remove();
            calculateTotals();
            updateWaDraft();
        }

        function addAddonItem() {
            const container = document.getElementById('addonContainer');
            if (!container) return;
            const div = document.createElement('div');
            div.className = 'addon-item-box';
            div.style = "border: 1px solid var(--border-color); border-radius: 8px; padding: 16px; margin-bottom: 16px;";
            div.innerHTML = `
                <div style="position: relative; margin-bottom: 12px;">
                    <input type="text" class="form-control addon-name" placeholder="${currentLang === 'en' ? 'Add-on Name' : 'Nama Add-on'}" oninput="calculateTotals(); updateWaDraft();" style="padding-right: 36px;">
                    <button type="button" class="btn-trash" onclick="removeAddonItem(this)" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--text-muted); cursor: pointer; font-size: 15px;">
                        <i class="fa-regular fa-trash-can"></i>
                    </button>
                </div>
                <div class="form-row form-row-2col" style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 8px;">
                    <div>
                        <label class="form-label">${currentLang === 'en' ? 'Quantity (pcs)' : 'Jumlah (pcs)'}</label>
                        <input type="number" class="form-control addon-qty" value="1" min="1" oninput="calculateTotals(); updateWaDraft();">
                    </div>
                    <div>
                        <label class="form-label">${currentLang === 'en' ? 'Unit Price' : 'Harga Satuan'}</label>
                        <input type="text" class="form-control addon-price" placeholder="Rp 0" oninput="handleCurrencyInput(this); calculateTotals();">
                    </div>
                </div>
                <div class="subtotal-text" style="text-align: right; font-size: 12px; color: var(--text-muted); margin-top: 8px;">
                    <span class="addon-calc-formula">1 x Rp 0 = </span><strong class="addon-calc-result" style="color: var(--text-dark);">Rp 0</strong>
                </div>
            `;
            container.appendChild(div);
            calculateTotals();
        }

        function removeAddonItem(btn) {
            btn.closest('.addon-item-box')?.remove();
            calculateTotals();
        }

        // --- Terms & Conditions Handlers ---
        function addTermPoint() {
            const container = document.getElementById('termsContainer');
            if (!container) return;
            const div = document.createElement('div');
            div.className = 'term-point-card';
            div.style = "border: 1px solid var(--border-color); border-radius: 8px; padding: 12px; margin-bottom: 12px; background: #fff; position: relative;";
            div.innerHTML = `
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px; gap: 8px;">
                    <input type="text" class="form-control term-title" placeholder="${currentLang === 'en' ? 'Title (e.g. Due Date:)' : 'Judul (cth: Pelunasan:)'}" oninput="syncTermsPreview()" style="font-weight: 500; font-size: 13px;">
                    <button type="button" class="btn-trash" onclick="removeTermPoint(this)" style="background: none; border: none; color: var(--text-muted); cursor: pointer; font-size: 15px; padding: 4px;" title="Hapus poin">
                        <i class="fa-regular fa-trash-can"></i>
                    </button>
                </div>
                <textarea class="form-control term-content" placeholder="${currentLang === 'en' ? 'Clause content...' : 'Isi ketentuan...'}" rows="2" oninput="syncTermsPreview()" style="font-size: 12px; resize: vertical;"></textarea>
            `;
            container.appendChild(div);
            syncTermsPreview();
        }

        function removeTermPoint(btn) {
            btn.closest('.term-point-card')?.remove();
            syncTermsPreview();
        }

        function resetTermsToDefault() {
            const container = document.getElementById('termsContainer');
            if (!container) return;
            container.innerHTML = `
                <div class="term-point-card" style="border: 1px solid var(--border-color); border-radius: 8px; padding: 12px; margin-bottom: 12px; background: #fff; position: relative;">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px; gap: 8px;">
                        <input type="text" class="form-control term-title" placeholder="Judul (cth: Pelunasan:)" value="Pelunasan" oninput="syncTermsPreview()" style="font-weight: 500; font-size: 13px;">
                        <button type="button" class="btn-trash" onclick="removeTermPoint(this)" style="background: none; border: none; color: var(--text-muted); cursor: pointer; font-size: 15px; padding: 4px;" title="Hapus poin">
                            <i class="fa-regular fa-trash-can"></i>
                        </button>
                    </div>
                    <textarea class="form-control term-content" placeholder="Isi ketentuan..." rows="2" oninput="syncTermsPreview()" style="font-size: 12px; resize: vertical;">Pelunasan wajib dibayarkan maksimal H-7 sebelum acara berlangsung.</textarea>
                </div>
            `;
            syncTermsPreview();
            showToastNotification(currentLang === 'en' ? "Terms reset to default" : "Syarat & ketentuan di-reset");
        }

        function syncTermsPreview() {
            const listEl = document.getElementById('previewTermsList');
            if (!listEl) return;
            let html = '';
            let count = 1;
            document.querySelectorAll('.term-point-card').forEach(card => {
                const title = card.querySelector('.term-title')?.value || '';
                const content = card.querySelector('.term-content')?.value || '';
                if (title || content) {
                    html += `<div style="margin-bottom: 4px;">${count}. <strong>${title}</strong> ${content}</div>`;
                    count++;
                }
            });
            listEl.innerHTML = html || (currentLang === 'en' ? '<div>No terms specified.</div>' : '<div>Tidak ada ketentuan.</div>');
        }

        // --- Tabs Navigation ---
        function switchView(tab) {
            const editorView = document.getElementById('invoiceEditorView');
            const savedView = document.getElementById('savedInvoicesView');
            const btnBaru = document.getElementById('tabBtnInvoiceBaru');
            const btnTersimpan = document.getElementById('tabBtnInvoiceTersimpan');

            if (tab === 'baru') {
                if (editorView) editorView.style.display = 'grid';
                if (savedView) savedView.style.display = 'none';
                if (btnBaru) { btnBaru.className = 'btn btn-primary'; }
                if (btnTersimpan) { btnTersimpan.className = 'btn btn-secondary'; }
            } else {
                if (editorView) editorView.style.display = 'none';
                if (savedView) savedView.style.display = 'block';
                if (btnBaru) { btnBaru.className = 'btn btn-secondary'; }
                if (btnTersimpan) { btnTersimpan.className = 'btn btn-primary'; }
                loadSavedInvoicesTab();
            }
        }

        // --- Saved Invoices & Filtering System ---
        let savedInvoicesData = [];
        let activeFilterStatus = 'all';
        let activeDateFilter = 'all';
        let customRangeStart = '2026-09-13';
        let customRangeEnd = '2026-10-12';

        function loadSavedInvoicesCount() {
            fetch('/api/invoices')
                .then(res => res.json())
                .then(list => {
                    const total = Array.isArray(list) ? list.length : 0;
                    const textEl = document.getElementById('savedInvoiceCountText');
                    const dict = i18n[currentLang] || i18n.id;
                    if (textEl) {
                        textEl.innerText = `${dict.tab_tersimpan} (${total})`;
                    }
                })
                .catch(e => {});
        }

        function cleanDateOnly(rawDate) {
            if (!rawDate) return '';
            let s = String(rawDate).trim();
            const isoMatch = s.match(/^(\d{4}-\d{2}-\d{2})/);
            if (isoMatch) return isoMatch[1];
            if (s.includes('T')) s = s.split('T')[0];
            if (s.includes(' ')) s = s.split(' ')[0];
            if (s.includes('/')) {
                const parts = s.split('/');
                if (parts.length === 3) {
                    if (parts[2].length === 4) {
                        const p0 = parseInt(parts[0], 10);
                        const p1 = parseInt(parts[1], 10);
                        if (p0 > 12) {
                            return `${parts[2]}-${parts[1].padStart(2, '0')}-${parts[0].padStart(2, '0')}`;
                        } else if (p1 > 12) {
                            return `${parts[2]}-${parts[0].padStart(2, '0')}-${parts[1].padStart(2, '0')}`;
                        } else {
                            return `${parts[2]}-${parts[1].padStart(2, '0')}-${parts[0].padStart(2, '0')}`;
                        }
                    } else if (parts[0].length === 4) {
                        return `${parts[0]}-${parts[1].padStart(2, '0')}-${parts[2].padStart(2, '0')}`;
                    }
                }
            }
            return s;
        }

        function formatTglIndo(val) {
            if (!val) return '';
            try {
                val = cleanDateOnly(val);
                const daysId = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                const daysEn = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                const monthsId = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                const monthsEn = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
                const days = currentLang === 'en' ? daysEn : daysId;
                const months = currentLang === 'en' ? monthsEn : monthsId;

                let d = null;
                if (typeof val === 'string' && val.includes('-')) {
                    const p = val.split('-');
                    if (p.length === 3) {
                        d = new Date(parseInt(p[0], 10), parseInt(p[1], 10) - 1, parseInt(p[2], 10));
                    }
                }
                if (!d || isNaN(d.getTime())) {
                    d = new Date(val);
                }
                if (isNaN(d.getTime())) return val;

                const dayName = days[d.getDay()];
                return `${dayName}, ${d.getDate()} ${months[d.getMonth()]} ${d.getFullYear()}`;
            } catch(e) {
                return val;
            }
        }

        function loadSavedInvoicesTab() {
            const container = document.getElementById('savedInvoicesListContainer');
            if (container) container.innerHTML = `<div style="text-align: center; padding: 40px; color: #a1a5b7; font-size: 13px;">${currentLang === 'en' ? 'Loading saved invoices...' : 'Memuat riwayat invoice...'}</div>`;

            fetch('/api/invoices')
                .then(res => res.json())
                .then(list => {
                    savedInvoicesData = Array.isArray(list) ? list : [];
                    filterSavedInvoices();
                    loadSavedInvoicesCount();
                })
                .catch(err => {
                    if (container) container.innerHTML = `<div style="text-align: center; padding: 40px; color: #f1416c; font-size: 13px;">${currentLang === 'en' ? 'Failed to load invoices' : 'Gagal memuat invoice.'}</div>`;
                });
        }

        function setFilterStatus(status, btn) {
            activeFilterStatus = status;
            document.querySelectorAll('.status-filter-pill').forEach(b => {
                b.style.background = 'white';
                b.style.color = '#555';
                b.style.fontWeight = '500';
                b.style.border = '1px solid var(--border-color)';
            });
            btn.style.background = '#6c5ce7';
            btn.style.color = 'white';
            btn.style.fontWeight = '600';
            btn.style.border = 'none';

            filterSavedInvoices();
        }

        // --- Date Filter Button Handlers ---
        function handleDateFilterAllClick(btn) {
            const extraGroup = document.getElementById('dateFilterExtraGroup');
            if (extraGroup) {
                if (extraGroup.style.display === 'none' || extraGroup.style.display === '') {
                    extraGroup.style.display = 'inline-flex';
                }
            }
            setDateFilter('all', btn);
        }

        function setDateFilter(type, btn) {
            activeDateFilter = type;
            document.querySelectorAll('.date-filter-btn').forEach(b => b.classList.remove('active'));
            if (btn) btn.classList.add('active');
            
            const pop = document.getElementById('customRangePickerPopover');
            if (pop) pop.style.display = 'none';

            filterSavedInvoices();
        }

        function getFilteredSavedInvoices() {
            const query = (document.getElementById('searchSavedInvoice')?.value || '').toLowerCase().trim();
            const now = new Date();
            
            let startDate = null;
            let endDate = null;
            let rangeSubtitle = '';

            if (activeDateFilter === 'this_month') {
                startDate = new Date(now.getFullYear(), now.getMonth(), 1, 0, 0, 0);
                endDate = new Date(now.getFullYear(), now.getMonth() + 1, 0, 23, 59, 59);
                rangeSubtitle = `${formatTglIndo(startDate)} – ${formatTglIndo(endDate)}`;
            } else if (activeDateFilter === 'last_month') {
                startDate = new Date(now.getFullYear(), now.getMonth() - 1, 1, 0, 0, 0);
                endDate = new Date(now.getFullYear(), now.getMonth(), 0, 23, 59, 59);
                rangeSubtitle = `${formatTglIndo(startDate)} – ${formatTglIndo(endDate)}`;
            } else if (activeDateFilter === 'last_30_days') {
                startDate = new Date(now.getTime() - (30 * 24 * 60 * 60 * 1000));
                startDate.setHours(0,0,0,0);
                endDate = new Date(now.getFullYear(), now.getMonth(), now.getDate(), 23, 59, 59);
                rangeSubtitle = `${formatTglIndo(startDate)} – ${formatTglIndo(endDate)}`;
            } else if (activeDateFilter === 'this_year') {
                startDate = new Date(now.getFullYear(), 0, 1, 0, 0, 0);
                endDate = new Date(now.getFullYear(), 11, 31, 23, 59, 59);
                rangeSubtitle = `${formatTglIndo(startDate)} – ${formatTglIndo(endDate)}`;
            } else if (activeDateFilter === 'custom' && customRangeStart) {
                startDate = new Date(customRangeStart);
                startDate.setHours(0,0,0,0);
                endDate = customRangeEnd ? new Date(customRangeEnd) : new Date(customRangeStart);
                endDate.setHours(23,59,59,999);
                rangeSubtitle = `${formatTglIndo(startDate)} – ${formatTglIndo(endDate)}`;
            }

            let filtered = savedInvoicesData.filter(inv => {
                const matchQuery = !query || 
                    (inv.client_name && inv.client_name.toLowerCase().includes(query)) ||
                    (inv.invoice_number && inv.invoice_number.toLowerCase().includes(query));

                let matchStatus = true;
                if (activeFilterStatus === 'Lunas') {
                    matchStatus = inv.payment_status === 'Lunas' || (parseFloat(inv.remaining_balance || 0) <= 0 && parseFloat(inv.grand_total || 0) > 0);
                } else if (activeFilterStatus === 'Terbayar Sebagian') {
                    matchStatus = inv.payment_status === 'Terbayar Sebagian' || (parseFloat(inv.total_dp || 0) > 0 && parseFloat(inv.remaining_balance || 0) > 0);
                } else if (activeFilterStatus === 'Belum Dibayar') {
                    matchStatus = (!inv.payment_status || inv.payment_status === 'Belum Dibayar') && parseFloat(inv.total_dp || 0) === 0;
                }

                let matchDate = true;
                if (startDate && endDate) {
                    const invDateVal = inv.created_at || inv.invoice_date;
                    if (invDateVal) {
                        const invD = new Date(invDateVal);
                        matchDate = (invD >= startDate && invD <= endDate);
                    }
                }

                return matchQuery && matchStatus && matchDate;
            });

            return { filtered, rangeSubtitle };
        }

        function filterSavedInvoices() {
            const { filtered, rangeSubtitle } = getFilteredSavedInvoices();
            renderSavedInvoices(filtered, rangeSubtitle);
        }

        function renderSavedInvoices(list, rangeSubtitle = '') {
            const container = document.getElementById('savedInvoicesListContainer');
            const countLabel = document.getElementById('savedCountLabel');
            const totalValLabel = document.getElementById('savedTotalValLabel');

            if (!container) return;

            let totalVal = 0;
            list.forEach(inv => {
                totalVal += parseFloat(inv.grand_total || 0);
            });

            const dict = i18n[currentLang] || i18n.id;

            if (countLabel) {
                if (rangeSubtitle) {
                    countLabel.innerText = `${list.length} invoice · ${rangeSubtitle}`;
                } else {
                    countLabel.innerText = `${list.length} invoice`;
                }
            }
            if (totalValLabel) {
                totalValLabel.innerText = `${dict.label_total_nilai} ${formatRupiah(totalVal)}`;
            }

            if (list.length === 0) {
                container.innerHTML = `<div style="text-align: center; padding: 48px; color: #a1a5b7; font-size: 13px;">${currentLang === 'en' ? 'No invoices found matching criteria.' : 'Belum ada invoice yang sesuai kriteria.'}</div>`;
                return;
            }

            let html = '';
            list.forEach(inv => {
                const isLunas = inv.payment_status === 'Lunas' || (parseFloat(inv.remaining_balance || 0) <= 0 && parseFloat(inv.grand_total || 0) > 0);
                const isPartial = !isLunas && (inv.payment_status === 'Terbayar Sebagian' || parseFloat(inv.total_dp || 0) > 0);

                let badgeHtml = '';
                if (isLunas) {
                    badgeHtml = `<span style="background: #e8fdf0; color: #10b981; padding: 3px 12px; border-radius: 14px; font-size: 11px; font-weight: 600;">${dict.pill_paid}</span>`;
                } else if (isPartial) {
                    badgeHtml = `<span style="background: #eff6ff; color: #3b82f6; padding: 3px 12px; border-radius: 14px; font-size: 11px; font-weight: 600;">${dict.pill_partial}</span>`;
                } else {
                    badgeHtml = `<span style="background: #fff8e6; color: #f59e0b; padding: 3px 12px; border-radius: 14px; font-size: 11px; font-weight: 600;">${dict.pill_unpaid}</span>`;
                }

                const clientDisplay = inv.client_name ? `${inv.client_name} - ` : '-- ';
                const invNo = inv.invoice_number || 'INV';
                const totalDisplay = formatRupiah(inv.grand_total || 0);
                const dpDisplay = formatRupiah(inv.total_dp || 0);
                const tglDisplay = formatTglIndo(inv.created_at || inv.invoice_date);
                const acaraDisplay = inv.event_date ? (formatTglIndo(inv.event_date) || inv.event_date) : '';

                html += `
                <div class="saved-inv-row" style="display: flex; justify-content: space-between; align-items: center; padding: 18px 22px; border-bottom: 1px solid #f1f1f4; background: white; transition: 0.15s;" onmouseover="this.style.backgroundColor='#fafafa'" onmouseout="this.style.backgroundColor='#ffffff'">
                    <div>
                        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 6px;">
                            <span style="font-weight: 600; font-size: 14px; color: #181c32;">${clientDisplay}${invNo}</span>
                            ${badgeHtml}
                        </div>
                        <div style="font-size: 12px; color: #888; display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                            <span>Rp ${totalDisplay}</span>
                            ${(parseFloat(inv.total_dp || 0) > 0 || isLunas) ? `<span>&bull; ${dict.label_terbayar} ${dpDisplay}</span>` : ''}
                            ${tglDisplay ? `<span>&bull; ${tglDisplay}</span>` : ''}
                            ${acaraDisplay ? `<span>&bull; ${dict.label_acara} ${acaraDisplay}</span>` : ''}
                        </div>
                    </div>
                    <div style="display: flex; align-items: center; gap: 8px;">
                        ${!isLunas ? `<button type="button" class="btn btn-tandai-lunas" onclick="tandaiLunas('${inv.id}')" style="background: #f0fdf4; border: 1px solid #bbf7d0; color: #16a34a; font-weight: 500; font-size: 12px; padding: 6px 14px; border-radius: 8px; cursor: pointer;">${dict.btn_tandai_lunas}</button>` : ''}
                        <button type="button" class="btn-icon" onclick="openPaymentHistoryModal('${inv.id}')" title="Riwayat Pembayaran" style="width: 36px; height: 36px; display: inline-flex; align-items: center; justify-content: center; border: 1px solid var(--border-color); border-radius: 8px; background: white; color: #555; cursor: pointer;"><i class="fa-solid fa-wallet"></i></button>
                        <button type="button" class="btn-icon" onclick="salinLinkPublik('${inv.id}')" title="Salin Link Publik" style="width: 36px; height: 36px; display: inline-flex; align-items: center; justify-content: center; border: 1px solid var(--border-color); border-radius: 8px; background: white; color: #555; cursor: pointer;"><i class="fa-solid fa-link"></i></button>
                        <button type="button" class="btn-icon" onclick="duplikatInvoice('${inv.id}')" title="Duplikat Invoice" style="width: 36px; height: 36px; display: inline-flex; align-items: center; justify-content: center; border: 1px solid var(--border-color); border-radius: 8px; background: white; color: #555; cursor: pointer;"><i class="fa-regular fa-copy"></i></button>
                        <button type="button" class="btn btn-secondary" onclick="bukaInvoice('${inv.id}')" style="font-size: 12px; padding: 6px 14px; border-radius: 8px; border: 1px solid var(--border-color); background: white; cursor: pointer;">${dict.btn_buka}</button>
                        <button type="button" class="btn-icon" onclick="hapusInvoice('${inv.id}')" title="Hapus Invoice" style="width: 36px; height: 36px; display: inline-flex; align-items: center; justify-content: center; border: 1px solid var(--border-color); border-radius: 8px; background: white; color: #f1416c; cursor: pointer;"><i class="fa-regular fa-trash-can"></i></button>
                    </div>
                </div>`;
            });

            container.innerHTML = html;
        }

        // --- Duplikat Invoice ---
        function duplikatInvoice(id) {
            fetch(`/api/invoices/${id}/duplicate`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showToastNotification(data.message || (currentLang === 'en' ? `Invoice duplicated as ${data.invoice_number}` : `Invoice diduplikat sebagai ${data.invoice_number}`));
                    loadSavedInvoicesTab();
                    loadSavedInvoicesCount();
                } else {
                    showToastNotification(data.error || (currentLang === 'en' ? "Failed to duplicate invoice" : "Gagal menduplikat invoice"));
                }
            })
            .catch(() => {
                showToastNotification(currentLang === 'en' ? "Failed to duplicate invoice" : "Gagal menduplikat invoice");
            });
        }

        // --- Ekspor CSV ---
        function exportCsvInvoices() {
            showToastNotification(currentLang === 'en' ? "CSV diunduh" : "CSV diunduh");

            const { filtered } = getFilteredSavedInvoices();
            const headers = ["No Invoice", "Klien", "WhatsApp", "Tanggal Acara", "Grand Total", "Terbayar", "Status", "Dibuat"];
            
            const escapeCsv = (val) => {
                let str = String(val === null || val === undefined ? '' : val);
                if (str.includes('"') || str.includes(',') || str.includes('\n') || str.includes('\r')) {
                    return '"' + str.replace(/"/g, '""') + '"';
                }
                return str;
            };

            let csvRows = [];
            csvRows.push(headers.map(escapeCsv).join(","));

            filtered.forEach(inv => {
                const isLunas = inv.payment_status === 'Lunas' || (parseFloat(inv.remaining_balance || 0) <= 0 && parseFloat(inv.grand_total || 0) > 0);
                const isPartial = !isLunas && (inv.payment_status === 'Terbayar Sebagian' || parseFloat(inv.total_dp || 0) > 0);
                const status = isLunas ? 'Lunas' : (isPartial ? 'Terbayar Sebagian' : 'Belum Dibayar');

                const noInv = inv.invoice_number || '';
                const klien = inv.client_name || '';
                const wa = inv.client_wa || '';
                let tglAcara = '';
                if (inv.event_date) {
                    const ed = new Date(inv.event_date);
                    if (!isNaN(ed.getTime())) {
                        const mm = String(ed.getMonth() + 1).padStart(2, '0');
                        const dd = String(ed.getDate()).padStart(2, '0');
                        const yyyy = ed.getFullYear();
                        tglAcara = `${dd}/${mm}/${yyyy}`;
                    }
                }
                const grandTotal = Math.round(parseFloat(inv.grand_total || 0));
                const terbayar = Math.round(parseFloat(inv.total_dp || 0));
                
                let dibuat = '';
                if (inv.created_at) {
                    const cd = new Date(inv.created_at);
                    if (!isNaN(cd.getTime())) {
                        const mm = String(cd.getMonth() + 1).padStart(2, '0');
                        const dd = String(cd.getDate()).padStart(2, '0');
                        const yyyy = cd.getFullYear();
                        dibuat = `${mm}/${dd}/${yyyy}`;
                    }
                } else {
                    const now = new Date();
                    dibuat = `${String(now.getMonth()+1).padStart(2,'0')}/${String(now.getDate()).padStart(2,'0')}/${now.getFullYear()}`;
                }

                const row = [noInv, klien, wa, tglAcara, grandTotal, terbayar, status, dibuat];
                csvRows.push(row.map(escapeCsv).join(","));
            });

            const csvContent = "\uFEFF" + csvRows.join("\r\n");
            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement("a");
            link.href = URL.createObjectURL(blob);
            link.download = `invoices_export_${new Date().toISOString().slice(0,10)}.csv`;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        // --- 2-Month Calendar Popover Implementation (Image 4) ---
        let calCurrentYear = 2026;
        let calCurrentMonth = 8; // 8 = September (0-indexed)
        let tempRangeStart = '2026-09-13';
        let tempRangeEnd = '2026-10-12';

        function toggleCustomRangePicker(e) {
            e.stopPropagation();
            const pop = document.getElementById('customRangePickerPopover');
            if (pop.style.display === 'none' || pop.style.display === '') {
                renderCalendarMonths();
                pop.style.display = 'block';
            } else {
                pop.style.display = 'none';
            }
        }

        function prevCalendarMonth(e) {
            e.stopPropagation();
            calCurrentMonth--;
            if (calCurrentMonth < 0) {
                calCurrentMonth = 11;
                calCurrentYear--;
            }
            renderCalendarMonths();
        }

        function nextCalendarMonth(e) {
            e.stopPropagation();
            calCurrentMonth++;
            if (calCurrentMonth > 11) {
                calCurrentMonth = 0;
                calCurrentYear++;
            }
            renderCalendarMonths();
        }

        function renderCalendarMonths() {
            const mNames = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
            const m1Year = calCurrentYear;
            const m1Month = calCurrentMonth;
            
            let m2Month = m1Month + 1;
            let m2Year = m1Year;
            if (m2Month > 11) {
                m2Month = 0;
                m2Year++;
            }

            document.getElementById('calMonthTitle1').innerText = `${mNames[m1Month]} ${m1Year}`;
            document.getElementById('calMonthTitle2').innerText = `${mNames[m2Month]} ${m2Year}`;

            renderMonthGrid('calGridMonth1', m1Year, m1Month);
            renderMonthGrid('calGridMonth2', m2Year, m2Month);

            updateCalendarFooterDisplay();
        }

        function renderMonthGrid(gridId, year, month) {
            const grid = document.getElementById(gridId);
            if (!grid) return;
            grid.innerHTML = '';

            const firstDayIndex = new Date(year, month, 1).getDay();
            const totalDays = new Date(year, month + 1, 0).getDate();

            // Empty cells
            for (let i = 0; i < firstDayIndex; i++) {
                const emptyCell = document.createElement('div');
                emptyCell.className = 'cal-day-cell-wrapper';
                emptyCell.innerHTML = `<span class="cal-day-btn empty"></span>`;
                grid.appendChild(emptyCell);
            }

            // Days
            for (let d = 1; d <= totalDays; d++) {
                const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(d).padStart(2, '0')}`;
                const wrapper = document.createElement('div');
                wrapper.className = 'cal-day-cell-wrapper';

                const isStart = (tempRangeStart && tempRangeStart === dateStr);
                const isEnd = (tempRangeEnd && tempRangeEnd === dateStr);
                const inRange = (tempRangeStart && tempRangeEnd && dateStr > tempRangeStart && dateStr < tempRangeEnd);

                if (inRange) {
                    wrapper.classList.add('in-range');
                }
                if (isStart) {
                    wrapper.classList.add('range-start-wrap');
                }
                if (isEnd) {
                    wrapper.classList.add('range-end-wrap');
                }

                const btn = document.createElement('span');
                btn.className = 'cal-day-btn';
                btn.innerText = d;
                btn.dataset.date = dateStr;

                if (isStart || isEnd) {
                    btn.classList.add('range-endpoint');
                }

                btn.onclick = (e) => {
                    e.stopPropagation();
                    selectCalendarDate(dateStr);
                };

                wrapper.appendChild(btn);
                grid.appendChild(wrapper);
            }
        }

        function selectCalendarDate(dateStr) {
            if (!tempRangeStart || (tempRangeStart && tempRangeEnd)) {
                tempRangeStart = dateStr;
                tempRangeEnd = null;
            } else if (tempRangeStart && !tempRangeEnd) {
                if (dateStr < tempRangeStart) {
                    tempRangeEnd = tempRangeStart;
                    tempRangeStart = dateStr;
                } else {
                    tempRangeEnd = dateStr;
                }
            }
            renderCalendarMonths();
        }

        function updateCalendarFooterDisplay() {
            const displayEl = document.getElementById('customRangeFooterDisplay');
            if (displayEl) {
                if (tempRangeStart && tempRangeEnd) {
                    displayEl.innerText = `${formatTglIndo(tempRangeStart)} – ${formatTglIndo(tempRangeEnd)}`;
                } else if (tempRangeStart) {
                    displayEl.innerText = `${formatTglIndo(tempRangeStart)}`;
                } else {
                    displayEl.innerText = '-';
                }
            }
        }

        function resetCustomRangeCalendar() {
            tempRangeStart = null;
            tempRangeEnd = null;
            renderCalendarMonths();
        }

        function applyCustomRangeCalendar() {
            if (!tempRangeStart) {
                alert(currentLang === 'en' ? "Please select a date range" : "Pilih rentang tanggal terlebih dahulu");
                return;
            }
            customRangeStart = tempRangeStart;
            customRangeEnd = tempRangeEnd || tempRangeStart;
            
            const pop = document.getElementById('customRangePickerPopover');
            if (pop) pop.style.display = 'none';

            activeDateFilter = 'custom';
            document.querySelectorAll('.date-filter-btn').forEach(b => b.classList.remove('active'));
            const customBtn = document.getElementById('btnDateFilterCustom');
            if (customBtn) customBtn.classList.add('active');

            filterSavedInvoices();
        }

        function initCalendarMonths() {
            const now = new Date();
            calCurrentYear = now.getFullYear();
            calCurrentMonth = Math.max(0, now.getMonth() - 1);
            
            const past30 = new Date(now.getTime() - 29 * 24 * 60 * 60 * 1000);
            tempRangeStart = `${past30.getFullYear()}-${String(past30.getMonth()+1).padStart(2,'0')}-${String(past30.getDate()).padStart(2,'0')}`;
            tempRangeEnd = `${now.getFullYear()}-${String(now.getMonth()+1).padStart(2,'0')}-${String(now.getDate()).padStart(2,'0')}`;
            
            customRangeStart = tempRangeStart;
            customRangeEnd = tempRangeEnd;
            renderCalendarMonths();
        }

        document.addEventListener('click', (e) => {
            const pop = document.getElementById('customRangePickerPopover');
            const btn = document.getElementById('btnDateFilterCustom');
            if (pop && pop.style.display === 'block' && !pop.contains(e.target) && !btn.contains(e.target)) {
                pop.style.display = 'none';
            }
        });

        // --- Payment Recording Modal Handlers ---
        let currentPayingInvoice = null;

        function openPaymentHistoryModal(invId) {
            const inv = savedInvoicesData.find(i => String(i.id) === String(invId));
            if (!inv) return;
            currentPayingInvoice = inv;

            const modal = document.getElementById('paymentModal');
            document.getElementById('paymentInvoiceId').value = inv.id;
            document.getElementById('paymentModalInvNo').innerText = inv.invoice_number || 'INV';
            
            const grandTotal = parseFloat(inv.grand_total || 0);
            const totalPaid = parseFloat(inv.total_dp || 0);
            const sisa = Math.max(0, grandTotal - totalPaid);

            document.getElementById('paymentModalSubtitle').innerText = `Total Rp ${formatRupiah(grandTotal)} · Terbayar Rp ${formatRupiah(totalPaid)} · Sisa Rp ${formatRupiah(sisa)}`;
            document.getElementById('inputPaymentNominal').value = sisa > 0 ? 'Rp ' + formatRupiah(sisa) : '';
            
            const now = new Date();
            document.getElementById('inputPaymentDate').value = `${String(now.getMonth()+1).padStart(2,'0')}/${String(now.getDate()).padStart(2,'0')}/${now.getFullYear()}`;

            const listContainer = document.getElementById('paymentHistoryList');
            let pHistory = [];
            if (Array.isArray(inv.payments)) {
                pHistory = inv.payments;
            } else if (typeof inv.payments === 'string') {
                try { pHistory = JSON.parse(inv.payments); } catch(e) {}
            }

            if (pHistory.length > 0) {
                let phtml = '';
                pHistory.forEach((p, idx) => {
                    phtml += `
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 10px 14px; background: #f8f9fa; border-radius: 8px; margin-bottom: 8px; font-size: 13px;">
                        <div>
                            <div style="font-weight: 600; color: #181c32;">Rp ${formatRupiah(p.amount || 0)}</div>
                            <div style="font-size: 11px; color: #888;">${p.date || '-'} ${p.note ? `&bull; ${p.note}` : ''}</div>
                        </div>
                        <span style="font-size: 11px; color: #10b981; font-weight: 600;">Berhasil</span>
                    </div>`;
                });
                listContainer.innerHTML = phtml;
            } else {
                listContainer.innerHTML = `<div style="text-align: center; padding: 20px; color: #a1a5b7; font-size: 13px;">Belum ada pembayaran tercatat.</div>`;
            }

            if (modal) modal.style.display = 'flex';
        }

        function closePaymentModal() {
            const modal = document.getElementById('paymentModal');
            if (modal) modal.style.display = 'none';
        }

        function submitRecordPayment(e) {
            e.preventDefault();
            const invId = document.getElementById('paymentInvoiceId').value;
            const nominal = parseCurrency(document.getElementById('inputPaymentNominal').value);
            const date = document.getElementById('inputPaymentDate').value;
            const note = document.getElementById('inputPaymentNote').value;

            if (nominal <= 0) {
                alert("Masukkan nominal pembayaran");
                return;
            }

            fetch(`/api/invoices/${invId}/record-payment`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ amount: nominal, date, note })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showToastNotification("Pembayaran berhasil dicatat");
                    closePaymentModal();
                    loadSavedInvoicesTab();
                } else {
                    alert(data.error || "Gagal mencatat pembayaran");
                }
            })
            .catch(() => {
                alert("Terjadi kesalahan sistem");
            });
        }

        function tandaiLunas(invId) {
            fetch(`/api/invoices/${invId}/mark-paid`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showToastNotification(currentLang === 'en' ? "Invoice marked as paid" : "Invoice ditandai lunas");
                    loadSavedInvoicesTab();
                }
            })
            .catch(() => {});
        }

        function salinLinkPublik(invId) {
            const url = `${window.location.origin}/i/${invId}`;
            navigator.clipboard.writeText(url).then(() => {
                showToastNotification(currentLang === 'en' ? "Public invoice link copied" : "Link publik berhasil disalin ke clipboard");
            });
        }

        function bukaInvoice(invId) {
            window.open(`/i/${invId}`, '_blank');
        }

        function hapusInvoice(invId) {
            if (!confirm(currentLang === 'en' ? "Are you sure you want to delete this invoice?" : "Apakah Anda yakin ingin menghapus invoice ini?")) return;
            fetch(`/api/invoices/${invId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            })
            .then(res => res.json())
            .then(data => {
                showToastNotification(currentLang === 'en' ? "Invoice deleted" : "Invoice berhasil dihapus");
                loadSavedInvoicesTab();
                loadSavedInvoicesCount();
            })
            .catch(() => {});
        }

        // --- Save Invoice to Database ---
        let selectedBookingId = null;

        function simpanInvoiceKeDatabase() {
            const extraPkgs = [];
            document.querySelectorAll('.paket-tambahan-box').forEach(box => {
                const name = box.querySelector('.pkt-extra-name')?.value?.trim();
                const price = parseCurrency(box.querySelector('.pkt-extra-price')?.value);
                const disc = parseCurrency(box.querySelector('.pkt-extra-disc')?.value);
                if (name || price > 0) extraPkgs.push({ name, price, discount: disc });
            });

            const addonsList = [];
            document.querySelectorAll('.addon-item-box').forEach(box => {
                const name = box.querySelector('.addon-name')?.value?.trim();
                const qty = parseInt(box.querySelector('.addon-qty')?.value, 10) || 1;
                const price = parseCurrency(box.querySelector('.addon-price')?.value);
                if (name || price > 0) addonsList.push({ name, qty, price });
            });

            const terminVal = document.getElementById('selectTermin')?.value || 'Uang Muka / DP 1';
            const isPelunasan = terminVal.includes('Pelunasan');
            const grandTotalVal = parseCurrency(document.getElementById('calcGrandTotal')?.innerText);
            const dp1Val = parseCurrency(document.getElementById('inputDP1')?.value);
            const dp2Val = parseCurrency(document.getElementById('inputDP2')?.value);

            let effTotalDp = dp1Val + dp2Val;
            let effRemaining = Math.max(0, grandTotalVal - effTotalDp);

            if (isPelunasan) {
                if (pelunasanMode === 'manual') {
                    const manualDp = parseCurrency(document.getElementById('inputManualTotalDP')?.value);
                    effTotalDp = manualDp;
                    effRemaining = Math.max(0, grandTotalVal - manualDp);
                } else {
                    effTotalDp = dp1Val + dp2Val;
                    effRemaining = Math.max(0, grandTotalVal - effTotalDp);
                }
            }

            const payload = {
                booking_id: selectedBookingId || null,
                invoice_number: document.getElementById('inputNoInvoice')?.value || 'INV-202610-006',
                vendor_name: document.getElementById('inputNamaVendor')?.value || 'Fariz hidayat',
                vendor_address: document.getElementById('inputAlamatVendor')?.value || 'Kombas purwokerto',
                client_name: document.getElementById('inputNamaKlien')?.value || 'Rian & Amel',
                client_wa: document.getElementById('inputNoWaKlien')?.value || '081234567890',
                client_address: document.getElementById('inputAlamatKlien')?.value || '',
                event_date: document.getElementById('inputTglAcara')?.value || '',
                termin_label: terminVal,
                package_name: document.getElementById('inputPaketUtama')?.value || 'Premium Wedding Documentation',
                package_price: parseCurrency(document.getElementById('inputHargaUtama')?.value),
                package_qty: parseInt(document.getElementById('inputJumlahPaket')?.value, 10) || 1,
                discount: parseCurrency(document.getElementById('inputDiskon')?.value),
                extra_packages: extraPkgs,
                addons: addonsList,
                grand_total: grandTotalVal,
                dp1_amount: dp1Val,
                dp2_amount: dp2Val,
                total_dp: effTotalDp,
                remaining_balance: effRemaining,
                bank_name: document.getElementById('inputNamaBank')?.value || 'BCA',
                bank_account_number: document.getElementById('inputNoRekening')?.value || '0431063867',
                bank_account_name: document.getElementById('inputAtasNama')?.value || 'Umi Salamah',
                due_date: document.getElementById('inputTglJatuhTempo')?.value || '',
                theme: currentSelectedTheme || 'theme-minimalist'
            };

            fetch('/api/invoices', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify(payload)
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showToastNotification(currentLang === 'en' ? "Invoice saved to folder" : "Invoice tersimpan ke folder");
                    loadSavedInvoicesCount();
                } else {
                    alert(data.error || "Gagal menyimpan invoice");
                }
            })
            .catch(() => {
                alert("Gagal menyimpan invoice");
            });
        }

        // --- Booking Modal Handlers ---
        let allBookingsList = [];

        function openBookingModal() {
            const modal = document.getElementById('bookingModal');
            if (modal) modal.style.display = 'flex';
            const container = document.getElementById('bookingListContainer');
            if (container) container.innerHTML = `<div style="text-align: center; padding: 40px; color: #a1a5b7; font-size: 13px;">Memuat data booking...</div>`;

            fetch('/api/bookings')
                .then(res => res.json())
                .then(list => {
                    allBookingsList = Array.isArray(list) ? list : [];
                    renderBookingList(allBookingsList);
                })
                .catch(() => {
                    if (container) container.innerHTML = `<div style="text-align: center; padding: 40px; color: #a1a5b7; font-size: 13px;">Belum ada data booking.</div>`;
                });
        }

        function closeBookingModal() {
            const modal = document.getElementById('bookingModal');
            if (modal) modal.style.display = 'none';
        }

        function filterBookingList() {
            const q = (document.getElementById('bookingSearchInput')?.value || '').toLowerCase().trim();
            const filtered = allBookingsList.filter(b => {
                return (b.client_name && b.client_name.toLowerCase().includes(q)) ||
                       (b.booking_code && b.booking_code.toLowerCase().includes(q));
            });
            renderBookingList(filtered);
        }

        function renderBookingList(list) {
            const container = document.getElementById('bookingListContainer');
            if (!container) return;
            if (list.length === 0) {
                container.innerHTML = `<div style="text-align: center; padding: 40px; color: #a1a5b7; font-size: 13px;">Tidak ada booking ditemukan.</div>`;
                return;
            }
            let html = '';
            list.forEach(b => {
                const bDateClean = cleanDateOnly(b.event_date);
                html += `
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px 14px; border: 1px solid #e2e8f0; border-radius: 8px; margin-bottom: 8px; cursor: pointer; transition: 0.15s;" onmouseover="this.style.borderColor='#6c5ce7'" onmouseout="this.style.borderColor='#e2e8f0'" onclick="pilihBooking('${b.id}')">
                    <div>
                        <div style="font-weight: 600; font-size: 14px; color: #181c32;">${b.client_name || '-'}</div>
                        <div style="font-size: 12px; color: #888;">${b.booking_code || ''} &bull; ${bDateClean || '-'}</div>
                    </div>
                    <button type="button" class="btn btn-secondary" style="font-size: 12px; padding: 6px 12px; border-radius: 6px;">Pilih</button>
                </div>`;
            });
            container.innerHTML = html;
        }

        function pilihBooking(bookingId) {
            const b = allBookingsList.find(x => String(x.id) === String(bookingId));
            if (!b) return;

            selectedBookingId = b.id;

            const cleanDate = cleanDateOnly(b.event_date);

            if (b.client_name) document.getElementById('inputNamaKlien').value = b.client_name;
            if (b.client_phone) document.getElementById('inputNoWaKlien').value = b.client_phone;
            if (b.client_address) document.getElementById('inputAlamatKlien').value = b.client_address;
            if (cleanDate) document.getElementById('inputTglAcara').value = cleanDate;
            if (b.package_name) document.getElementById('inputPaketUtama').value = b.package_name;
            if (b.total_price) document.getElementById('inputHargaUtama').value = 'Rp ' + formatRupiah(b.total_price);

            const box = document.getElementById('bookingConnectedBox');
            if (box) {
                box.style.display = 'block';
                const txt = document.getElementById('bookingConnectedText');
                if (txt) txt.textContent = `Terhubung ke Booking: ${b.client_name || '-'} — ${cleanDate || '-'}`;
            }

            if (b.paid_amount && parseCurrency(b.paid_amount) > 0) {
                const paidVal = parseCurrency(b.paid_amount);
                const infoText = document.getElementById('pelunasanInfoText');
                if (infoText) {
                    infoText.innerText = `DP tercatat dari booking: Rp ${formatRupiah(paidVal)}`;
                }
                const manualInput = document.getElementById('inputManualTotalDP');
                if (manualInput) {
                    manualInput.value = 'Rp ' + formatRupiah(paidVal);
                }
            } else {
                const infoText = document.getElementById('pelunasanInfoText');
                if (infoText) {
                    infoText.innerText = 'Belum ada DP tercatat untuk klien ini.';
                }
            }

            syncPreviewClientWa();
            syncPreviewDate();
            calculateTotals();
            updateWaDraft();
            closeBookingModal();
            showToastNotification("Data klien diambil dari booking");
        }

        function putuskanBooking() {
            selectedBookingId = null;
            const box = document.getElementById('bookingConnectedBox');
            if (box) box.style.display = 'none';
            const infoText = document.getElementById('pelunasanInfoText');
            if (infoText) {
                infoText.innerText = 'Belum ada DP tercatat untuk klien ini.';
            }
            showToastNotification("Hubungan booking diputuskan");
        }

        function togglePaketDropdown(e) {
            e.stopPropagation();
            const menu = document.getElementById('paketDropdownMenu');
            if (!menu) return;
            if (menu.style.display === 'block') {
                menu.style.display = 'none';
                return;
            }
            loadAndRenderPaketDropdown();
        }

        function loadAndRenderPaketDropdown() {
            const menu = document.getElementById('paketDropdownMenu');
            if (!menu) return;
            menu.innerHTML = `<div style="padding: 12px; text-align: center; color: #888; font-size: 12px;"><i class="fa-solid fa-spinner fa-spin"></i> Memuat data paket...</div>`;
            menu.style.display = 'block';

            fetch('/api/invoices/packages')
                .then(res => res.json())
                .then(list => {
                    if (!Array.isArray(list) || list.length === 0) {
                        menu.innerHTML = `<div style="padding: 12px; text-align: center; color: #888; font-size: 12px;">Belum ada paket tersimpan.</div>`;
                        return;
                    }
                    let html = '';
                    list.forEach(p => {
                        const safeName = (p.name || '').replace(/'/g, "\\'");
                        html += `
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 10px 14px; border-bottom: 1px solid #f1f1f4; cursor: pointer; transition: 0.15s; position: relative;" onmouseover="this.style.background='#f8f9fa'" onmouseout="this.style.background='transparent'">
                            <div style="flex: 1; min-width: 0; padding-right: 12px;" onclick="pilihPresetPaket('${safeName}', ${p.price || 0}, ${p.discount || 0})">
                                <div style="font-weight: 500; font-size: 13px; color: #181c32; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">${p.name || '-'}</div>
                                <div style="font-size: 11px; color: #888; margin-top: 2px;">Rp ${formatRupiah(p.price || 0)}</div>
                            </div>
                            <button type="button" class="btn-hapus-paket-preset" onclick="hapusPaketTersimpan(${p.id}, event)" style="position: relative; top: auto; right: auto; background: none; border: none; color: #a1a5b7; cursor: pointer; font-size: 15px; padding: 6px 10px; border-radius: 6px; display: inline-flex; align-items: center; justify-content: center; flex-shrink: 0; transition: 0.15s;" title="Hapus paket" onmouseover="this.style.color='#ef4444'; this.style.background='rgba(239, 68, 68, 0.08)'" onmouseout="this.style.color='#a1a5b7'; this.style.background='transparent'">
                                <i class="fa-regular fa-trash-can"></i>
                            </button>
                        </div>`;
                    });
                    menu.innerHTML = html;
                })
                .catch(() => {
                    menu.innerHTML = `<div style="padding: 12px; text-align: center; color: #ef4444; font-size: 12px;">Gagal memuat paket.</div>`;
                });
        }

        function pilihPresetPaket(name, price, disc) {
            document.getElementById('inputPaketUtama').value = name;
            document.getElementById('inputHargaUtama').value = 'Rp ' + formatRupiah(price);
            if (disc > 0) {
                document.getElementById('inputDiskon').value = 'Rp ' + formatRupiah(disc);
            } else {
                document.getElementById('inputDiskon').value = 'Rp 0';
            }
            calculateTotals();
            updateWaDraft();
            const menu = document.getElementById('paketDropdownMenu');
            if (menu) menu.style.display = 'none';
        }

        function hapusPaketTersimpan(id, e) {
            if (e) {
                e.preventDefault();
                e.stopPropagation();
            }
            if (!confirm("Hapus paket tersimpan ini?")) return;
            fetch(`/api/invoices/packages/${id}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showToastNotification(currentLang === 'en' ? "Package deleted" : "Paket berhasil dihapus");
                } else {
                    showToastNotification(data.error || (currentLang === 'en' ? "Failed to delete package" : "Gagal menghapus paket"));
                }
                loadAndRenderPaketDropdown();
            })
            .catch(() => {
                fetch(`/api/invoices/packages/${id}/delete`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                })
                .then(res => res.json())
                .then(data => {
                    showToastNotification(currentLang === 'en' ? "Package deleted" : "Paket berhasil dihapus");
                    loadAndRenderPaketDropdown();
                })
                .catch(() => {
                    showToastNotification("Gagal menghapus paket");
                });
            });
        }

        document.addEventListener('click', () => {
            const menu = document.getElementById('paketDropdownMenu');
            if (menu) menu.style.display = 'none';
        });

        // --- Export PNG / PDF ---
        function unduhPNG() {
            const paper = document.getElementById('invoice-preview');
            if (!paper) return;
            if (typeof html2canvas !== 'undefined') {
                html2canvas(paper, { scale: 2 }).then(canvas => {
                    const link = document.createElement('a');
                    link.download = `Invoice_${document.getElementById('previewNoInvoice')?.innerText || 'INV'}.png`;
                    link.href = canvas.toDataURL('image/png');
                    link.click();
                    showToastNotification(currentLang === 'en' ? "PNG downloaded" : "PNG berhasil diunduh");
                });
            } else {
                window.print();
            }
        }

        function unduhPDF() {
            window.print();
        }

        // --- Toast Notification ---
        function showToastNotification(message) {
            const toast = document.getElementById('invoiceToast');
            const textEl = document.getElementById('invoiceToastText');
            if (toast && textEl) {
                textEl.innerText = message;
                toast.style.display = 'flex';
                toast.style.opacity = '1';
                toast.style.transform = 'translateY(0)';
                
                if (window.toastTimeout) clearTimeout(window.toastTimeout);
                window.toastTimeout = setTimeout(() => {
                    toast.style.opacity = '0';
                    toast.style.transform = 'translateY(-10px)';
                    setTimeout(() => { toast.style.display = 'none'; }, 300);
                }, 3000);
            }
        }

        // --- Initial Load Setup ---
        document.addEventListener('DOMContentLoaded', () => {
            calculateTotals();
            syncPreviewClientWa();
            syncPreviewDate();
            syncJatuhTempoPreview();
            syncBankPreview();
            syncTerminPreview();
            syncTermsPreview();
            loadSavedInvoicesCount();
            initCalendarMonths();
            updateWaDraft();
            updatePreviewInvoiceDate();

            if (currentLang && currentLang !== 'id') {
                setLanguage(currentLang);
            } else {
                setLanguage('id');
            }

            fetch('/api/invoices/settings')
                .then(res => res.json())
                .then(settings => {
                    if (settings.bank_name) {
                        document.getElementById('inputNamaBank').value = settings.bank_name;
                    }
                    if (settings.bank_account_number) {
                        document.getElementById('inputNoRekening').value = settings.bank_account_number;
                    }
                    if (settings.bank_account_name) {
                        document.getElementById('inputAtasNama').value = settings.bank_account_name;
                    }
                    if (settings.business_name) {
                        const vName = document.getElementById('inputNamaVendor');
                        if (vName) vName.value = settings.business_name;
                        const pvName = document.getElementById('previewNamaVendor');
                        if (pvName) pvName.innerText = settings.business_name;
                    }
                    if (settings.vendor_address) {
                        const vAddr = document.getElementById('inputAlamatVendor');
                        if (vAddr) vAddr.value = settings.vendor_address;
                        const pvAddr = document.getElementById('previewAlamatVendor');
                        if (pvAddr) pvAddr.innerText = settings.vendor_address;
                    }
                    if (settings.invoice_format) {
                        const vFmt = document.getElementById('inputPrefixInvoice');
                        if (vFmt) vFmt.value = settings.invoice_format;
                    }
                    syncBankPreview();
                    updateWaDraft();
                })
                .catch(e => {});
        });
    </script>
    
    <!-- Toast Notification Element -->
    <div id="invoiceToast" style="display: none; position: fixed; top: 76px; right: 28px; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); padding: 12px 20px; font-size: 13px; font-weight: 600; color: #181c32; z-index: 99999; align-items: center; gap: 10px; transition: all 0.3s ease; opacity: 0; transform: translateY(-10px);">
        <span style="display: inline-flex; align-items: center; justify-content: center; width: 20px; height: 20px; background: #181c32; color: #fff; border-radius: 50%; font-size: 11px;">
            <i class="fa-solid fa-check"></i>
        </span>
        <span id="invoiceToastText">Data klien diambil dari booking</span>
    </div>
    <script src="/js/responsive.js"></script>
</body>
</html>
