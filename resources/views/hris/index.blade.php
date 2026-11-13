<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Studio HRIS">
    <meta name="application-name" content="Studio HRIS">
    <meta name="theme-color" content="#059669">
    <title>HRIS Studio Absensi</title>

    <!-- PWA Manifest & Icons -->
    <link rel="manifest" href="/manifest.json">
    <link rel="icon" type="image/png" sizes="192x192" href="/hris-icon-192.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/hris-icon-192.png">

    <!-- Fonts & Icons (Ionicons + Plus Jakarta Sans) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

@php
    $appTheme = '#006847';
@endphp
    <style>
        :root {
            --primary: #006847;
            --primary-dark: #005238;
            --primary-light: #E6F7F0;
            --bg-page: #F0F4F2;
            --surface: #FFFFFF;
            --text-main: #111827;
            --text-muted: #6B7280;
            --border: #E5E7EB;
            --safe-bottom: env(safe-area-inset-bottom, 16px);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            -webkit-tap-highlight-color: transparent;
        }

        html, body {
            background: #F0F4F2;
            color: var(--text-main);
            min-height: 100vh;
            width: 100%;
            overflow-x: hidden;
            margin: 0;
            padding: 0;
        }

        /* App Loading Screen */
        .loading-screen {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: #FFFFFF;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 16px;
            z-index: 9999;
            transition: opacity 0.3s ease;
        }
        .loading-spinner {
            width: 36px;
            height: 36px;
            border: 3px solid #E5E7EB;
            border-top-color: #006847;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }
        .loading-text {
            font-size: 13px;
            color: #4B5563;
            font-weight: 500;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* App Root Container */
        .app-root {
            width: 100%;
            min-height: 100vh;
            background: #F0F4F2;
            display: flex;
            flex-direction: column;
            position: relative;
        }

        /* Top Header */
        .app-header {
            background: #006847;
            color: #FFFFFF;
            padding: 16px 24px;
            border-bottom-left-radius: 24px;
            border-bottom-right-radius: 24px;
            box-shadow: 0 4px 20px rgba(0, 104, 71, 0.15);
            position: sticky;
            top: 0;
            z-index: 90;
        }

        .header-inner {
            max-width: 1100px;
            margin: 0 auto;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .header-shield-circle {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: #FFFFFF;
            color: #006847;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            flex-shrink: 0;
        }

        .user-avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            border: 2px solid rgba(255, 255, 255, 0.85);
            object-fit: cover;
            background: #10B981;
            flex-shrink: 0;
        }

        .header-title-box h3 {
            font-size: 16px;
            font-weight: 700;
            line-height: 1.2;
            color: #FFFFFF;
            margin: 0 0 2px 0;
        }

        .header-subtitle-text {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.85);
            margin: 0;
            font-weight: 500;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .role-switch-pill {
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.35);
            color: #FFFFFF;
            padding: 7px 14px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
            gap: 6px;
            cursor: pointer;
            text-transform: uppercase;
            transition: all 0.2s;
        }
        .role-switch-pill:hover {
            background: rgba(255, 255, 255, 0.25);
        }

        .header-logout-btn {
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.35);
            color: #FFFFFF;
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            cursor: pointer;
            transition: all 0.2s;
        }
        .header-logout-btn:hover {
            background: rgba(255, 255, 255, 0.25);
        }

        /* App Body Container */
        .app-body {
            flex: 1;
            max-width: 1100px;
            margin: 0 auto;
            width: 100%;
            padding: 20px 24px 90px;
            box-sizing: border-box;
        }

        @media (max-width: 640px) {
            .app-header { padding: 14px 16px; }
            .app-body { padding: 16px 16px 85px; }
        }

        /* Bottom Tab Navigation Bar */
        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 64px;
            background: #FFFFFF;
            border-top: 1px solid #E5E7EB;
            display: flex;
            justify-content: center;
            z-index: 100;
            box-shadow: 0 -2px 10px rgba(0,0,0,0.03);
        }

        .bottom-nav-inner {
            max-width: 1100px;
            width: 100%;
            display: flex;
            justify-content: space-around;
            align-items: center;
        }

        .nav-tab-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #6B7280;
            font-size: 11px;
            font-weight: 600;
            background: none;
            border: none;
            cursor: pointer;
            padding: 6px 12px;
            border-radius: 10px;
            transition: all 0.2s;
            text-decoration: none;
            gap: 3px;
        }

        .nav-tab-item ion-icon {
            font-size: 20px;
        }

        .nav-tab-item.active {
            color: #006847;
            font-weight: 700;
        }

        /* Monitoring Bar (Image 2) */
        .monitoring-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
            padding: 4px 2px;
        }
        .monitoring-left {
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 700;
            font-size: 14px;
            color: #1F2937;
        }
        .monitoring-left .calendar-icon {
            font-size: 18px;
            color: #006847;
        }
        .btn-refresh {
            background: none;
            border: none;
            color: #006847;
            font-weight: 600;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 6px;
            cursor: pointer;
        }
        .btn-refresh:hover { opacity: 0.8; }

        /* 2x2 Stat Cards (Image 2) */
        .stats-2x2-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
            margin-bottom: 16px;
        }
        .stat-box {
            background: #FFFFFF;
            border-radius: 16px;
            padding: 20px;
            border: 1px solid #E5E7EB;
            display: flex;
            flex-direction: column;
            gap: 6px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        }
        .stat-icon-wrapper {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            margin-bottom: 8px;
        }
        .stat-icon-wrapper.green-light {
            background: #E6F7F0;
            color: #006847;
        }
        .stat-icon-wrapper.yellow-light {
            background: #FEF3C7;
            color: #D97706;
        }
        .stat-icon-wrapper.red-light {
            background: #FEE2E2;
            color: #DC2626;
        }
        .stat-number {
            font-size: 32px;
            font-weight: 800;
            color: #111827;
            line-height: 1;
        }
        .stat-label {
            font-size: 13px;
            color: #4B5563;
            font-weight: 500;
        }

        /* Pending Action Rows (Image 2) */
        .pending-actions-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
            margin-bottom: 16px;
        }
        @media (max-width: 640px) {
            .pending-actions-grid {
                grid-template-columns: 1fr;
            }
        }
        .pending-card {
            background: #FFFFFF;
            border-radius: 16px;
            padding: 16px 20px;
            border: 1px solid #E5E7EB;
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: pointer;
            transition: all 0.2s;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        }
        .pending-card:hover {
            border-color: #006847;
            transform: translateY(-1px);
        }
        .pending-left {
            display: flex;
            align-items: center;
            gap: 14px;
        }
        .pending-badge {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 14px;
        }
        .pending-badge.yellow {
            background: #FEF3C7;
            color: #D97706;
        }
        .pending-badge.blue {
            background: #E0F2FE;
            color: #0284C7;
        }
        .pending-title {
            font-weight: 700;
            font-size: 13px;
            color: #111827;
        }
        .pending-subtitle {
            font-size: 11px;
            color: #6B7280;
            margin-top: 1px;
        }
        .pending-chevron {
            color: #9CA3AF;
            font-size: 16px;
        }

        /* Activity Card (Image 2) */
        .activity-card {
            background: #FFFFFF;
            border-radius: 16px;
            padding: 24px;
            border: 1px solid #E5E7EB;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
            margin-bottom: 24px;
        }
        .activity-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .activity-title {
            font-size: 15px;
            font-weight: 700;
            color: #111827;
        }
        .btn-link-pdf {
            background: none;
            border: none;
            color: #006847;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
        }
        .btn-link-pdf:hover { text-decoration: underline; }
        .empty-activity-msg {
            text-align: center;
            color: #6B7280;
            font-size: 13px;
            padding: 35px 0;
        }

        /* Segmented Capsule Tabs (Images 3, 4, 5) */
        .segmented-capsule {
            background: #F1F5F9;
            border-radius: 14px;
            padding: 5px;
            display: flex;
            gap: 6px;
            margin-bottom: 20px;
            border: 1px solid #E2E8F0;
        }
        .segmented-tab-btn {
            flex: 1;
            padding: 10px 16px;
            border-radius: 10px;
            border: none;
            background: transparent;
            color: #4B5563;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            text-align: center;
        }
        .segmented-tab-btn.active {
            background: #FFFFFF;
            color: #006847;
            font-weight: 700;
            box-shadow: 0 2px 6px rgba(0,0,0,0.06);
        }

        /* Search and Add Bar (Image 4) */
        .search-add-bar {
            display: flex;
            gap: 12px;
            margin-bottom: 16px;
            align-items: center;
        }
        .search-input-wrapper {
            flex: 1;
            display: flex;
            align-items: center;
            background: #FFFFFF;
            border: 1px solid #E5E7EB;
            border-radius: 12px;
            padding: 0 14px;
            gap: 10px;
            color: #9CA3AF;
        }
        .search-input-wrapper input {
            width: 100%;
            border: none;
            outline: none;
            padding: 12px 0;
            font-size: 13px;
            color: #111827;
            background: transparent;
        }
        .btn-add-emp {
            background: #006847;
            color: #FFFFFF;
            border: none;
            border-radius: 12px;
            padding: 12px 20px;
            font-size: 13px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            white-space: nowrap;
            transition: all 0.2s;
        }
        .btn-add-emp:hover { background: #047857; }

        /* Employee Card (Image 4) */
        .employee-card {
            background: #FFFFFF;
            border-radius: 16px;
            padding: 18px 20px;
            border: 1px solid #E5E7EB;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        }
        .emp-card-name {
            font-size: 14px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 3px;
        }
        .emp-card-sub, .emp-card-email {
            font-size: 12px;
            color: #6B7280;
            margin-bottom: 2px;
        }
        .emp-card-badge {
            font-size: 11px;
            font-weight: 700;
            color: #006847;
            margin-top: 4px;
        }
        .btn-edit-emp {
            background: #F0FDF4;
            border: 1px solid #BBF7D0;
            color: #006847;
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            cursor: pointer;
            transition: all 0.2s;
        }
        .btn-edit-emp:hover {
            background: #006847;
            color: #FFFFFF;
        }

        /* Studio Geofence Card (Image 5) */
        .studio-settings-card {
            background: #FFFFFF;
            border-radius: 16px;
            padding: 24px;
            border: 1px solid #E5E7EB;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        }
        .settings-card-title {
            font-size: 15px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 6px;
        }
        .settings-card-desc {
            font-size: 12px;
            color: #6B7280;
            line-height: 1.5;
            margin-bottom: 20px;
        }
        .btn-gps-current {
            width: 100%;
            background: #E6F7F0;
            color: #006847;
            border: 1px solid #BBF7D0;
            border-radius: 12px;
            padding: 13px;
            font-size: 13px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            cursor: pointer;
            margin-bottom: 20px;
            transition: all 0.2s;
        }
        .btn-gps-current:hover { background: #D1FAE5; }
        .form-grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }
        @media (max-width: 640px) {
            .form-grid-2 { grid-template-columns: 1fr; }
        }
        .btn-save-studio {
            width: 100%;
            background: #006847;
            color: #FFFFFF;
            border: none;
            border-radius: 14px;
            padding: 14px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            margin-top: 10px;
            transition: all 0.2s;
        }
        .btn-save-studio:hover { background: #047857; }

        .header-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .user-badge-container {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .user-avatar {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            border: 2px solid rgba(255, 255, 255, 0.85);
            object-fit: cover;
            background: #10B981;
        }

        .header-title-box h3 {
            font-size: 15px;
            font-weight: 800;
            line-height: 1.2;
            color: #FFFFFF;
        }

        .header-division-tag {
            display: inline-block;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(4px);
            font-size: 11px;
            font-weight: 600;
            padding: 2px 8px;
            border-radius: 12px;
            margin-top: 3px;
            color: #E6FFFA;
        }

        .role-switch-pill {
            background: rgba(255, 255, 255, 0.18);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: #FFFFFF;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 6px;
            cursor: pointer;
            transition: all 0.2s;
        }
        .role-switch-pill:active { transform: scale(0.96); background: rgba(255,255,255,0.28); }

        /* Common Cards & Components */

        /* Cards & Components */
        .card {
            background: var(--surface);
            border-radius: 20px;
            padding: 16px;
            margin-bottom: 14px;
            border: 1px solid var(--border);
            box-shadow: 0 4px 16px -3px rgba(0, 0, 0, 0.04);
        }

        .card-header-flex {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
        }

        .card-title {
            font-size: 14px;
            font-weight: 800;
            color: var(--text-main);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        /* Badges */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
        }
        .badge-success { background: #D1FAE5; color: #047857; }
        .badge-warning { background: #FEF3C7; color: #B45309; }
        .badge-danger { background: #FEE2E2; color: #B91C1C; }
        .badge-info { background: #E0F2FE; color: #0369A1; }
        .badge-purple { background: #F3E8FF; color: #7E22CE; }

        /* Time & Date Display */
        .clock-hero-box {
            background: #FFFFFF;
            border-radius: 20px;
            padding: 18px 16px;
            margin-bottom: 14px;
            border: 1px solid var(--border);
            text-align: center;
            box-shadow: 0 6px 20px -4px rgba(0,0,0,0.05);
        }

        .clock-time {
            font-size: 34px;
            font-weight: 800;
            color: #064E3B;
            letter-spacing: -1px;
        }

        .clock-date {
            font-size: 12px;
            color: var(--text-muted);
            font-weight: 600;
            margin-top: 2px;
        }

        /* 2 Side-by-Side Metric Cards */
        .metrics-2grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-bottom: 14px;
        }

        .metric-mini-card {
            background: #FFFFFF;
            border-radius: 16px;
            padding: 14px;
            border: 1px solid var(--border);
            box-shadow: 0 4px 12px -2px rgba(0,0,0,0.03);
        }

        .metric-mini-label {
            font-size: 11px;
            font-weight: 700;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            gap: 5px;
            text-transform: uppercase;
        }

        .metric-mini-val {
            font-size: 18px;
            font-weight: 800;
            color: #111827;
            margin-top: 6px;
        }

        .metric-mini-sub {
            font-size: 10px;
            color: #9CA3AF;
            margin-top: 2px;
        }

        /* Geofence Status Pill Banner */
        .geofence-banner {
            background: #ECFDF5;
            border: 1px solid #A7F3D0;
            border-radius: 16px;
            padding: 12px 14px;
            margin-bottom: 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .geofence-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .geofence-icon {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: #D1FAE5;
            color: #059669;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }

        /* Primary Action Buttons */
        .btn-clock-in {
            width: 100%;
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            color: #FFFFFF;
            border: none;
            padding: 15px;
            border-radius: 18px;
            font-size: 15px;
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            cursor: pointer;
            box-shadow: 0 8px 25px rgba(5, 150, 105, 0.4);
            margin-bottom: 10px;
            transition: all 0.2s;
        }
        .btn-clock-in:active { transform: scale(0.98); }

        .btn-clock-out {
            width: 100%;
            background: linear-gradient(135deg, #DC2626 0%, #B91C1C 100%);
            color: #FFFFFF;
            border: none;
            padding: 15px;
            border-radius: 18px;
            font-size: 15px;
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            cursor: pointer;
            box-shadow: 0 8px 25px rgba(220, 38, 38, 0.35);
            transition: all 0.2s;
        }
        .btn-clock-out:active { transform: scale(0.98); }

        /* PWA Install Promo Box */
        .pwa-promo-box {
            background: linear-gradient(135deg, #064E3B 0%, #047857 100%);
            color: #FFFFFF;
            border-radius: 18px;
            padding: 16px;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            cursor: pointer;
            box-shadow: 0 8px 20px rgba(6, 78, 59, 0.3);
        }

        .pwa-promo-title {
            font-size: 13px;
            font-weight: 800;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .pwa-promo-sub {
            font-size: 11px;
            opacity: 0.85;
            margin-top: 3px;
            line-height: 1.3;
        }

        /* Camera Modal Overlay */
        .modal-overlay {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(11, 19, 26, 0.8);
            backdrop-filter: blur(6px);
            display: none;
            align-items: flex-end;
            justify-content: center;
            z-index: 1000;
        }
        .modal-overlay.active { display: flex; }

        .modal-sheet {
            width: 100%;
            max-width: 460px;
            background: #FFFFFF;
            border-top-left-radius: 28px;
            border-top-right-radius: 28px;
            padding: 22px 20px 30px;
            max-height: 88vh;
            overflow-y: auto;
            animation: sheetUp 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }
        @keyframes sheetUp {
            from { transform: translateY(100%); }
            to { transform: translateY(0); }
        }

        .modal-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
            padding-bottom: 12px;
            border-bottom: 1px solid var(--border);
        }

        .modal-title { font-size: 16px; font-weight: 800; color: var(--text-main); }

        .modal-close-btn {
            background: #F3F4F6;
            border: none;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            color: var(--text-muted);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Camera Viewfinder */
        .camera-box {
            width: 100%;
            height: 310px;
            background: #000000;
            border-radius: 24px;
            overflow: hidden;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 16px;
        }

        #camera-feed {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transform: scaleX(-1);
        }

        .face-oval-guide {
            position: absolute;
            width: 190px;
            height: 250px;
            border: 2px dashed rgba(52, 211, 153, 0.9);
            border-radius: 50% / 60%;
            box-shadow: 0 0 0 9999px rgba(0, 0, 0, 0.45);
            pointer-events: none;
        }

        .scan-beam {
            position: absolute;
            width: 100%;
            height: 2px;
            background: linear-gradient(90deg, transparent, #34D399, #FFFFFF, #34D399, transparent);
            box-shadow: 0 0 14px #34D399;
            animation: radarBeam 2s infinite alternate ease-in-out;
        }
        @keyframes radarBeam {
            0% { top: 12%; opacity: 0.5; }
            100% { top: 88%; opacity: 1; }
        }

        /* Form elements */
        .form-group { margin-bottom: 14px; }
        .form-label { display: block; font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 6px; }
        .form-control {
            width: 100%;
            padding: 12px 14px;
            border-radius: 14px;
            border: 1px solid var(--border);
            font-size: 14px;
            outline: none;
            background: #F9FAFB;
            color: var(--text-main);
            transition: border 0.2s;
        }
        .form-control:focus { border-color: var(--primary); background: #FFFFFF; }

        /* Segmented Buttons */
        .segmented-row {
            display: flex;
            background: #E5E7EB;
            padding: 4px;
            border-radius: 14px;
            margin-bottom: 14px;
        }

        .segment-tab-btn {
            flex: 1;
            padding: 9px 12px;
            border: none;
            background: none;
            font-size: 12px;
            font-weight: 800;
            color: var(--text-muted);
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.2s;
        }
        .segment-tab-btn.active {
            background: #FFFFFF;
            color: var(--emerald-700);
            box-shadow: 0 2px 6px rgba(0,0,0,0.06);
        }

        .quick-presets-row {
            display: flex;
            gap: 6px;
            overflow-x: auto;
            margin-bottom: 12px;
            padding-bottom: 4px;
        }

        .preset-chip {
            background: #ECFDF5;
            border: 1px solid #A7F3D0;
            color: #065F46;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            white-space: nowrap;
            cursor: pointer;
        }

        /* History items */
        .list-item-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #F3F4F6;
        }
        .list-item-row:last-child { border-bottom: none; }
    </style>
</head>
<body>

<!-- App Loading Screen (Matching Image 1) -->
<div id="appLoadingScreen" class="loading-screen">
    <div class="loading-spinner"></div>
    <p class="loading-text">Memuat sesi aplikasi...</p>
</div>

<div class="app-root">

    <!-- ============================================== -->
    <!-- 1. LOGIN SCREEN (If Not Logged In)             -->
    <!-- ============================================== -->
    <div id="screen-login" style="display: none; height: 100%; overflow-y: auto; padding: 30px 20px 40px;">
        <div style="text-align: center; margin-bottom: 24px;">
            <img src="/hris-icon-192.png" alt="Logo" style="width: 72px; height: 72px; border-radius: 18px; box-shadow: 0 8px 25px rgba(5,150,105,0.3); margin-bottom: 14px;">
            <h2 style="font-size: 22px; font-weight: 800; color: #064E3B;">Studio Foto HRIS</h2>
            <p style="font-size: 12px; color: var(--text-muted); margin-top: 4px; line-height: 1.4;">
                Sistem Absensi Selfie + GPS, Cuti, Lembur & Slip Gaji Digital
            </p>
        </div>

        <!-- Tab Masuk / Daftar -->
        <div class="segmented-row">
            <button class="segment-tab-btn active" id="tabAuthLogin" onclick="switchAuthTab('login')">Masuk Akun</button>
            <button class="segment-tab-btn" id="tabAuthRegister" onclick="switchAuthTab('register')">Buat Akun Baru</button>
        </div>

        <!-- Form Login -->
        <form id="formLogin" onsubmit="handleAuthLogin(event)" style="margin-top: 10px;">
            <div id="authRegisterNameGroup" class="form-group" style="display: none;">
                <label class="form-label">Nama Lengkap</label>
                <div style="position: relative;">
                    <input type="text" class="form-control" id="loginName" placeholder="Contoh: Budi Owner Studio">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Email Karyawan / Admin</label>
                <input type="email" class="form-control" id="loginEmail" placeholder="nama@studiofoto.com" value="warungkombas@gmail.com" required>
            </div>

            <div class="form-group">
                <label class="form-label">Password</label>
                <div style="position: relative;">
                    <input type="password" class="form-control" id="loginPassword" placeholder="Minimal 6 karakter" value="Fariz123@" required>
                    <span onclick="togglePasswordVisibility('loginPassword')" style="position: absolute; right: 14px; top: 12px; cursor: pointer; color: #9CA3AF; font-size: 18px;">
                        <ion-icon name="eye-outline"></ion-icon>
                    </span>
                </div>
            </div>

            <button type="submit" class="btn-clock-in" id="btnLoginSubmit" style="margin-top: 14px;">
                <ion-icon name="log-in-outline" style="font-size: 20px;"></ion-icon>
                <span id="btnLoginText">Masuk Aplikasi</span>
            </button>
        </form>

        <!-- Quick 1-Click Demo Login Pill -->
        <div onclick="fillDemoCredentials()" style="margin-top: 14px; text-align: center; background: #ECFDF5; border: 1px dashed #10B981; padding: 10px; border-radius: 12px; cursor: pointer;">
            <div style="font-size: 11px; font-weight: 700; color: #047857;">👉 Ketuk di sini untuk Akun Demo Owner / Admin:</div>
            <div style="font-size: 11px; color: #064E3B; margin-top: 2px;">Email: <b>warungkombas@gmail.com</b> | Pass: <b>Fariz123@</b></div>
        </div>

        <!-- PWA Install Banner -->
        <div class="pwa-promo-box" onclick="promptPwaInstall()" style="margin-top: 24px;">
            <div>
                <div class="pwa-promo-title">
                    <ion-icon name="phone-portrait-outline"></ion-icon>
                    Instal Aplikasi Mobile (PWA)
                </div>
                <div class="pwa-promo-sub">
                    Ketuk di sini untuk menginstal aplikasi fullscreen tanpa address bar dengan ikon HD di layar utama HP Anda.
                </div>
            </div>
            <ion-icon name="download-outline" style="font-size: 24px;"></ion-icon>
        </div>

        <p style="font-size: 11px; color: #9CA3AF; text-align: center; margin-top: 16px; line-height: 1.5;">
            Satu aplikasi untuk Karyawan & Admin. Tampilan menu otomatis menyesuaikan hak akses akun Anda di database.
        </p>
    </div>

    <!-- ============================================== -->
    <!-- 2. MAIN APPLICATION (When Logged In)          -->
    <!-- ============================================== -->
    <div id="screen-main" style="display: flex; flex-direction: column; height: 100%;">
        
        <!-- App Top Header (Matching Image 2) -->
        <header class="app-header">
            <div class="header-inner">
                <div class="header-left">
                    <div class="header-shield-circle" id="headerIconShield" style="display: none;">
                        <ion-icon name="shield-checkmark"></ion-icon>
                    </div>
                    <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=150" alt="Avatar" class="user-avatar" id="appHeaderAvatar">
                    <div class="header-title-box">
                        <h3 id="appHeaderName">Administrator / Owner</h3>
                        <p id="appHeaderSubtitle" class="header-subtitle-text">Panel Admin &amp; HR Studio</p>
                    </div>
                </div>

                <div class="header-right">
                    <button class="role-switch-pill" id="appRoleSwitchContainer" onclick="toggleAppRoleView()">
                        <ion-icon name="swap-horizontal-outline"></ion-icon>
                        <span id="roleViewLabel">SISI KARYAWAN</span>
                    </button>
                    <button class="header-logout-btn" onclick="handleAuthLogout()" title="Keluar">
                        <ion-icon name="log-out-outline"></ion-icon>
                    </button>
                </div>
            </div>
        </header>

        <!-- App Body (Tabs Container) -->
        <div class="app-body">

            <!-- PWA Install Notice (Floating) -->
            <div id="floatingInstallBanner" class="pwa-promo-box" onclick="promptPwaInstall()" style="display: none; padding: 12px 14px; margin-bottom: 12px;">
                <div>
                    <div class="pwa-promo-title" style="font-size: 12px;">
                        <ion-icon name="cloud-download-outline"></ion-icon>
                        Pasang Aplikasi di Layar Utama HP
                    </div>
                    <div class="pwa-promo-sub" style="font-size: 10px;">
                        Buka aplikasi layaknya native app tanpa browser bar.
                    </div>
                </div>
                <ion-icon name="chevron-forward" style="font-size: 18px;"></ion-icon>
            </div>

            <!-- ============================================== -->
            <!-- EMPLOYEE TAB 1: ABSENSI                        -->
            <!-- ============================================== -->
            <div id="tab-emp-absen" class="tab-pane">
                <!-- Clock Hero -->
                <div class="clock-hero-box">
                    <div class="clock-time" id="empLiveClock">08:00:00</div>
                    <div class="clock-date" id="empLiveDate">Memuat tanggal...</div>
                </div>

                <!-- 2 Metrics: Sisa Cuti & Shift Hari Ini -->
                <div class="metrics-2grid">
                    <div class="metric-mini-card">
                        <div class="metric-mini-label"><ion-icon name="calendar-outline"></ion-icon> Sisa Cuti</div>
                        <div class="metric-mini-val" id="empLeaveBalanceVal">12 Hari</div>
                        <div class="metric-mini-sub">Kuota 12 hari/thn</div>
                    </div>
                    <div class="metric-mini-card">
                        <div class="metric-mini-label"><ion-icon name="time-outline"></ion-icon> Jadwal Shift</div>
                        <div class="metric-mini-val" style="font-size: 14px; margin-top: 8px;" id="empShiftVal">Shift Pagi</div>
                        <div class="metric-mini-sub">08:00 - 17:00 WIB</div>
                    </div>
                </div>

                <!-- Studio Geofence Bar -->
                <div class="geofence-banner">
                    <div class="geofence-info">
                        <div class="geofence-icon" id="geofenceIconBox"><ion-icon name="location-outline"></ion-icon></div>
                        <div>
                            <div style="font-size: 11px; font-weight: 700; color: #064E3B;" id="geofenceStudioTitle">Studio Foto Utama</div>
                            <div style="font-size: 13px; font-weight: 800; color: #047857;" id="geofenceDistanceText">Mendeteksi GPS...</div>
                        </div>
                    </div>
                    <span id="marketingBypassBadge" class="badge badge-purple" style="display: none;">
                        Marketing · Bebas Jarak
                    </span>
                </div>

                <!-- Clock In & Clock Out Action Buttons -->
                <button class="btn-clock-in" onclick="openCameraModal('in')">
                    <ion-icon name="camera-outline" style="font-size: 22px;"></ion-icon>
                    <span>Clock In — Selfie & GPS</span>
                </button>

                <button class="btn-clock-out" onclick="handleClockOutClick()">
                    <ion-icon name="log-out-outline" style="font-size: 22px;"></ion-icon>
                    <span>Clock Out — Selesai Kerja</span>
                </button>
                <div style="text-align: center; font-size: 11px; color: #9CA3AF; margin-top: 6px; margin-bottom: 14px;">
                    *Sistem mewajibkan foto kamera langsung dan deteksi GPS aktif.
                </div>

                <!-- Status Kehadiran Hari Ini Card -->
                <div class="card">
                    <div class="card-header-flex">
                        <div class="card-title"><ion-icon name="checkmark-done-circle-outline" style="color: #10B981; font-size: 18px;"></ion-icon> Status Kehadiran Hari Ini</div>
                        <span id="empTodayBadge" class="badge badge-warning">Belum Clock In</span>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; background: #F9FAFB; padding: 12px; border-radius: 14px;">
                        <div>
                            <div style="font-size: 11px; color: var(--text-muted); font-weight: 600;">Jam Masuk:</div>
                            <div style="font-size: 15px; font-weight: 800; color: #111827; margin-top: 2px;" id="empTodayInTime">--:--</div>
                            <div style="font-size: 10px; color: #6B7280;" id="empTodayInDist">-- m dari Studio</div>
                        </div>
                        <div>
                            <div style="font-size: 11px; color: var(--text-muted); font-weight: 600;">Jam Pulang:</div>
                            <div style="font-size: 15px; font-weight: 800; color: #111827; margin-top: 2px;" id="empTodayOutTime">Belum Pulang</div>
                            <div style="font-size: 10px; color: #6B7280;" id="empTodayWorkDuration">-</div>
                        </div>
                    </div>
                </div>

                <!-- Riwayat Absensi Terbaru -->
                <div class="card">
                    <div class="card-header-flex">
                        <div class="card-title"><ion-icon name="time-outline" style="color: #059669; font-size: 18px;"></ion-icon> Riwayat Absensi Terbaru</div>
                    </div>
                    <div id="empAttendanceHistoryList">
                        <div style="font-size: 12px; color: var(--text-muted); text-align: center; padding: 15px;">Memuat data absensi...</div>
                    </div>
                </div>
            </div>

            <!-- ============================================== -->
            <!-- EMPLOYEE TAB 2: CUTI                           -->
            <!-- ============================================== -->
            <div id="tab-emp-cuti" class="tab-pane" style="display: none;">
                <div class="card">
                    <div class="card-title" style="margin-bottom: 12px;"><ion-icon name="calendar-outline" style="color: #059669; font-size: 18px;"></ion-icon> Form Pengajuan Cuti</div>
                    <form onsubmit="submitLeave(event)">
                        <div class="form-group">
                            <label class="form-label">Jenis Pengajuan</label>
                            <select class="form-control" id="formLeaveType">
                                <option value="Cuti Tahunan">Cuti Tahunan</option>
                                <option value="Sakit">Sakit (Surat Dokter)</option>
                                <option value="Izin">Izin / Keperluan Pribadi</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Pilih Cepat Durasi</label>
                            <div class="quick-presets-row">
                                <span class="preset-chip" onclick="setLeavePreset(0, 0)">Hari Ini (1 hr)</span>
                                <span class="preset-chip" onclick="setLeavePreset(1, 1)">Besok (1 hr)</span>
                                <span class="preset-chip" onclick="setLeavePreset(1, 2)">Besok (2 hr)</span>
                                <span class="preset-chip" onclick="setLeavePreset(1, 3)">3 Hari</span>
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                            <div class="form-group">
                                <label class="form-label">Tanggal Mulai</label>
                                <input type="date" class="form-control" id="formLeaveStart" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Tanggal Selesai</label>
                                <input type="date" class="form-control" id="formLeaveEnd" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Alasan Pengajuan</label>
                            <textarea class="form-control" id="formLeaveReason" rows="3" placeholder="Sakit, acara keluarga, dll..." required></textarea>
                        </div>

                        <button type="submit" class="btn-clock-in">
                            <ion-icon name="paper-plane-outline"></ion-icon> Kirim Pengajuan Cuti
                        </button>
                    </form>
                </div>

                <div class="card">
                    <div class="card-title" style="margin-bottom: 12px;"><ion-icon name="list-outline" style="color: #059669; font-size: 18px;"></ion-icon> Status Pengajuan Cuti</div>
                    <div id="empLeavesList">
                        <div style="font-size: 12px; color: var(--text-muted); text-align: center; padding: 15px;">Memuat data cuti...</div>
                    </div>
                </div>
            </div>

            <!-- ============================================== -->
            <!-- EMPLOYEE TAB 3: LEMBUR                         -->
            <!-- ============================================== -->
            <div id="tab-emp-lembur" class="tab-pane" style="display: none;">
                <div class="card">
                    <div class="card-title" style="margin-bottom: 12px;"><ion-icon name="time-outline" style="color: #059669; font-size: 18px;"></ion-icon> Form Pengajuan Lembur</div>
                    <form onsubmit="submitOvertime(event)">
                        <div class="form-group">
                            <label class="form-label">Tanggal Lembur</label>
                            <input type="date" class="form-control" id="formOtDate" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Pilih Cepat Jam Lembur</label>
                            <div class="quick-presets-row">
                                <span class="preset-chip" onclick="setOtPreset('17:00', '20:00')">17:00 - 20:00 (3 Jam)</span>
                                <span class="preset-chip" onclick="setOtPreset('18:00', '21:00')">18:00 - 21:00 (3 Jam)</span>
                                <span class="preset-chip" onclick="setOtPreset('19:00', '22:00')">19:00 - 22:00 (3 Jam)</span>
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                            <div class="form-group">
                                <label class="form-label">Jam Mulai</label>
                                <input type="time" class="form-control" id="formOtStart" value="17:00" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Jam Selesai</label>
                                <input type="time" class="form-control" id="formOtEnd" value="20:00" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Keterangan Pekerjaan Lembur</label>
                            <textarea class="form-control" id="formOtDesc" rows="3" placeholder="Contoh: Editing batch wedding event, shooting studio malam..." required></textarea>
                        </div>

                        <button type="submit" class="btn-clock-in">
                            <ion-icon name="send-outline"></ion-icon> Kirim Pengajuan Lembur
                        </button>
                    </form>
                </div>

                <div class="card">
                    <div class="card-title" style="margin-bottom: 12px;"><ion-icon name="time-outline" style="color: #059669; font-size: 18px;"></ion-icon> Riwayat Lembur</div>
                    <div id="empOvertimesList">
                        <div style="font-size: 12px; color: var(--text-muted); text-align: center; padding: 15px;">Memuat data lembur...</div>
                    </div>
                </div>
            </div>

            <!-- ============================================== -->
            <!-- EMPLOYEE TAB 4: SLIP GAJI                      -->
            <!-- ============================================== -->
            <div id="tab-emp-slip" class="tab-pane" style="display: none;">
                <div class="card" style="background: linear-gradient(135deg, #064E3B 0%, #047857 100%); color: #FFFFFF;">
                    <div style="font-size: 11px; text-transform: uppercase; opacity: 0.9;">Total Gaji Bersih (Take Home Pay)</div>
                    <div style="font-size: 28px; font-weight: 800; margin-top: 4px;" id="slipNetSalaryText">Rp 0</div>
                    <div style="font-size: 11px; opacity: 0.85; margin-top: 4px;" id="slipPeriodLabel">Periode: -</div>
                </div>

                <div class="card">
                    <div class="card-header-flex">
                        <div class="card-title"><ion-icon name="document-text-outline" style="color: #059669; font-size: 18px;"></ion-icon> Unduh Dokumen Slip Gaji</div>
                    </div>
                    <p style="font-size: 12px; color: var(--text-muted); margin-bottom: 14px;">
                        Pilih periode bulan untuk mengunduh atau mencetak file dokumen PDF keterangan slip gaji resmi Anda.
                    </p>

                    <div class="form-group">
                        <label class="form-label">Pilih Bulan & Tahun</label>
                        <input type="month" class="form-control" id="slipMonthPicker" onchange="loadEmployeeSlipDetails()">
                    </div>

                    <div id="slipComponentsBox" style="margin-top: 14px; background: #F9FAFB; padding: 14px; border-radius: 14px; border: 1px solid var(--border);">
                        <!-- Rendered by JS -->
                    </div>

                    <div style="display: flex; gap: 10px; margin-top: 16px;">
                        <button class="btn-clock-in" onclick="printActivePayslip()" style="flex: 1; padding: 12px;">
                            <ion-icon name="print-outline"></ion-icon> Cetak Dokumen
                        </button>
                    </div>
                </div>
            </div>

            <!-- ============================================== -->
            <!-- EMPLOYEE TAB 5: PROFIL                         -->
            <!-- ============================================== -->
            <div id="tab-emp-profil" class="tab-pane" style="display: none;">
                <div class="card" style="text-align: center; padding: 22px 16px;">
                    <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=150" alt="Avatar" id="profileAvatarImg" style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover; border: 3px solid #10B981; margin: 0 auto 12px;">
                    <h3 style="font-size: 17px; font-weight: 800;" id="profileNameText">Nama Karyawan</h3>
                    <div style="font-size: 12px; color: var(--text-muted); margin-top: 2px;" id="profileCodeDivText">KRY-001 • Fotografer</div>
                    <div style="margin-top: 8px;"><span class="badge badge-success" id="profileRoleBadge">KARYAWAN</span></div>
                </div>

                <div class="card">
                    <div class="card-title" style="margin-bottom: 12px;"><ion-icon name="person-outline" style="color: #059669; font-size: 18px;"></ion-icon> Biodata & Kontak</div>
                    <form onsubmit="saveProfileBiodata(event)">
                        <div class="form-group">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control" id="profNameInput" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Nomor WhatsApp / HP Aktif</label>
                            <input type="tel" class="form-control" id="profPhoneInput" placeholder="0812xxxxxxxx">
                        </div>
                        <button type="submit" class="btn-clock-in" style="padding: 12px;">
                            <ion-icon name="save-outline"></ion-icon> Simpan Biodata
                        </button>
                    </form>
                </div>

                <button onclick="handleLogout()" style="width: 100%; background: #FEE2E2; color: #DC2626; border: 1px solid #FECACA; padding: 14px; border-radius: 16px; font-weight: 800; font-size: 14px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px;">
                    <ion-icon name="log-out-outline" style="font-size: 20px;"></ion-icon> Keluar dari Aplikasi
                </button>
            </div>

            <!-- ============================================== -->
            <!-- ADMIN TAB 1: DASHBOARD (Matching Image 2)      -->
            <!-- ============================================== -->
            <div id="tab-adm-dash" class="tab-pane" style="display: none;">
                <!-- Monitoring Header Bar -->
                <div class="monitoring-bar">
                    <div class="monitoring-left">
                        <ion-icon name="calendar-outline" class="calendar-icon"></ion-icon>
                        <span class="monitoring-title">Monitoring Hari Ini · <span id="adminDateHeader">{{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</span></span>
                    </div>
                    <button class="btn-refresh" onclick="loadInitialData()">
                        <ion-icon name="sync-outline"></ion-icon>
                        <span>Segarkan</span>
                    </button>
                </div>

                <!-- 4 Stat Cards in 2x2 Grid (Image 2) -->
                <div class="stats-2x2-grid">
                    <div class="stat-box">
                        <div class="stat-icon-wrapper green-light">
                            <ion-icon name="people"></ion-icon>
                        </div>
                        <div class="stat-number" id="adStatTotalEmp">1</div>
                        <div class="stat-label">Karyawan Aktif</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-icon-wrapper green-light">
                            <ion-icon name="checkmark-circle"></ion-icon>
                        </div>
                        <div class="stat-number" id="adStatPresent">0</div>
                        <div class="stat-label">Sudah Hadir</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-icon-wrapper yellow-light">
                            <ion-icon name="alert-circle"></ion-icon>
                        </div>
                        <div class="stat-number" id="adStatLate">0</div>
                        <div class="stat-label">Terlambat</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-icon-wrapper red-light">
                            <ion-icon name="hourglass"></ion-icon>
                        </div>
                        <div class="stat-number" id="adStatAbsent">1</div>
                        <div class="stat-label">Belum Absen</div>
                    </div>
                </div>

                <!-- 2 Pending Action Rows (Image 2) -->
                <div class="pending-actions-grid">
                    <div class="pending-card" onclick="switchAdminTab('approval'); setApprovalSub('leave');">
                        <div class="pending-left">
                            <span class="pending-badge yellow" id="adPendingLeavesCount">0</span>
                            <div class="pending-text">
                                <div class="pending-title">Pending Cuti</div>
                                <div class="pending-subtitle">Ketuk untuk setujui</div>
                            </div>
                        </div>
                        <ion-icon name="chevron-forward-outline" class="pending-chevron"></ion-icon>
                    </div>

                    <div class="pending-card" onclick="switchAdminTab('approval'); setApprovalSub('overtime');">
                        <div class="pending-left">
                            <span class="pending-badge blue" id="adPendingOvertimesCount">0</span>
                            <div class="pending-text">
                                <div class="pending-title">Pending Lembur</div>
                                <div class="pending-subtitle">Ketuk untuk setujui</div>
                            </div>
                        </div>
                        <ion-icon name="chevron-forward-outline" class="pending-chevron"></ion-icon>
                    </div>
                </div>

                <!-- Aktivitas Kehadiran Hari Ini (Image 2) -->
                <div class="activity-card">
                    <div class="activity-header">
                        <h4 class="activity-title">Aktivitas Kehadiran Hari Ini</h4>
                        <button class="btn-link-pdf" onclick="switchAdminTab('rekap')">Cetak Rekap PDF →</button>
                    </div>
                    <div class="activity-content" id="adLiveAttendancesList">
                        <div class="empty-activity-msg">Belum ada karyawan yang melakukan Clock In hari ini.</div>
                    </div>
                </div>
            </div>

            <!-- ============================================== -->
            <!-- ADMIN TAB 2: APPROVAL (Matching Image 3)       -->
            <!-- ============================================== -->
            <div id="tab-adm-approval" class="tab-pane" style="display: none;">
                <div class="segmented-capsule">
                    <button class="segmented-tab-btn active" id="btnAppSubLeave" onclick="setApprovalSub('leave')">
                        Pengajuan Cuti (<span id="countAppSubLeave">0</span>)
                    </button>
                    <button class="segmented-tab-btn" id="btnAppSubOt" onclick="setApprovalSub('overtime')">
                        Pengajuan Lembur (<span id="countAppSubOt">0</span>)
                    </button>
                </div>

                <div id="adminApprovalItemsList">
                    <div class="empty-activity-msg">Belum ada pengajuan cuti dari karyawan.</div>
                </div>
            </div>

            <!-- ============================================== -->
            <!-- ADMIN TAB 3: KARYAWAN & GEOFENCE (Images 4 & 5)-->
            <!-- ============================================== -->
            <div id="tab-adm-karyawan" class="tab-pane" style="display: none;">
                <div class="segmented-capsule">
                    <button class="segmented-tab-btn active" id="btnEmpSubList" onclick="setEmployeeSub('list')">
                        Data Karyawan (<span id="countEmpSubList">1</span>)
                    </button>
                    <button class="segmented-tab-btn" id="btnEmpSubStudio" onclick="setEmployeeSub('studio')">
                        Lokasi Studio (<span id="labelStudioRadius">100m</span>)
                    </button>
                </div>

                <!-- Subtab 1: Data Karyawan (Image 4) -->
                <div id="adminSubEmployeeList">
                    <div class="search-add-bar">
                        <div class="search-input-wrapper">
                            <ion-icon name="search-outline"></ion-icon>
                            <input type="text" id="adminEmpSearchInput" placeholder="Cari nama, ID, email, atau divisi..." oninput="filterAdminEmployees()">
                        </div>
                        <button class="btn-add-emp" onclick="openEmployeeModal()">
                            <ion-icon name="person-add-outline"></ion-icon>
                            <span>Tambah</span>
                        </button>
                    </div>

                    <div id="adminEmployeesContainer">
                        <!-- Rendered by JS -->
                    </div>
                </div>

                <!-- Subtab 2: Lokasi Studio & Geofence (Image 5) -->
                <div id="adminSubStudioSettings" style="display: none;">
                    <div class="studio-settings-card">
                        <h4 class="settings-card-title">Pengaturan Geofence Studio Fotografi</h4>
                        <p class="settings-card-desc">Karyawan biasa wajib berada di dalam radius meter yang ditentukan untuk Clock In &amp; Clock Out. Divisi Marketing otomatis dikecualikan (bebas lokasi).</p>
                        
                        <button type="button" class="btn-gps-current" onclick="setStudioCoordsCurrentGps()">
                            <ion-icon name="locate-outline"></ion-icon>
                            <span>Gunakan Titik Lokasi GPS Saya Saat Ini</span>
                        </button>

                        <form onsubmit="saveStudioSettings(event)">
                            <div class="form-group">
                                <label class="form-label">Nama Lokasi Studio</label>
                                <input type="text" class="form-control" id="studioNameInput" value="Studio Utama" required>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Alamat Lengkap Studio</label>
                                <input type="text" class="form-control" id="studioAddressInput" value="Jl. Studio Fotografi No. 1 (Ubah titik GPS asli langsung dari menu Admin di HP)">
                            </div>

                            <div class="form-grid-2">
                                <div class="form-group">
                                    <label class="form-label">Latitude (-90 s/d 90)</label>
                                    <input type="number" step="any" class="form-control" id="studioLatInput" value="-7.672585" required>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Longitude (-180 s/d 180)</label>
                                    <input type="number" step="any" class="form-control" id="studioLngInput" value="109.649065" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Radius Maksimal Absensi (Meter - Hanya Angka)</label>
                                <input type="number" class="form-control" id="studioRadiusInput" value="100" required>
                            </div>

                            <button type="submit" class="btn-save-studio">Simpan Pengaturan Studio</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- ============================================== -->
            <!-- ADMIN TAB 4: PAYROLL                           -->
            <!-- ============================================== -->
            <div id="tab-adm-payroll" class="tab-pane" style="display: none;">
                <div class="card-title" style="margin-bottom: 12px;"><ion-icon name="cash-outline" style="color: #059669; font-size: 20px;"></ion-icon> Manajemen Gaji & Penerbitan Slip PDF</div>
                
                <div class="card">
                    <div style="font-size: 12px; font-weight: 800; color: #064E3B; margin-bottom: 10px;">1. Pilih Periode & Karyawan</div>
                    <div class="form-group">
                        <label class="form-label">Periode Bulan</label>
                        <input type="month" class="form-control" id="admPayrollMonth" onchange="loadAdminPayrollForEmployee()">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Pilih Karyawan</label>
                        <select class="form-control" id="admPayrollEmpSelect" onchange="loadAdminPayrollForEmployee()">
                            <!-- Rendered by JS -->
                        </select>
                    </div>
                    <div id="admPayrollMetricsBadge" style="display: flex; gap: 8px; font-size: 11px; color: var(--text-muted); background: #F9FAFB; padding: 10px; border-radius: 12px;">
                        <span>Hadir: <b id="payMetPresent">0</b></span> |
                        <span>Terlambat: <b id="payMetLate">0</b></span> |
                        <span>Lembur: <b id="payMetOt">0 Jam</b></span>
                    </div>
                </div>

                <div class="card">
                    <div style="font-size: 12px; font-weight: 800; color: #064E3B; margin-bottom: 10px;">2. Komponen Slip Gaji (Rp)</div>
                    <form onsubmit="saveAdminPayroll(event)">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                            <div class="form-group">
                                <label class="form-label">Gaji Pokok</label>
                                <input type="number" class="form-control" id="payBaseInput" value="4500000" oninput="calculatePayrollTotal()">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Tunjangan</label>
                                <input type="number" class="form-control" id="payAllowanceInput" value="500000" oninput="calculatePayrollTotal()">
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                            <div class="form-group">
                                <label class="form-label">Uang Lembur</label>
                                <input type="number" class="form-control" id="payOtInput" value="0" oninput="calculatePayrollTotal()">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Bonus / Insentif</label>
                                <input type="number" class="form-control" id="payBonusInput" value="0" oninput="calculatePayrollTotal()">
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                            <div class="form-group">
                                <label class="form-label">Potongan Keterlambatan</label>
                                <input type="number" class="form-control" id="payLateDeductInput" value="0" oninput="calculatePayrollTotal()">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Potongan Lainnya</label>
                                <input type="number" class="form-control" id="payOtherDeductInput" value="0" oninput="calculatePayrollTotal()">
                            </div>
                        </div>

                        <div style="background: #ECFDF5; border: 1px solid #A7F3D0; border-radius: 14px; padding: 14px; margin-bottom: 14px; display: flex; justify-content: space-between; align-items: center;">
                            <span style="font-size: 12px; font-weight: 800; color: #064E3B;">Total Gaji Bersih:</span>
                            <span style="font-size: 20px; font-weight: 800; color: #047857;" id="payCalculatedNetText">Rp 5.000.000</span>
                        </div>

                        <div style="display: flex; gap: 8px;">
                            <button type="submit" class="btn-clock-in" style="flex: 1; padding: 12px;">
                                <ion-icon name="checkmark-done-circle-outline"></ion-icon> Terbitkan Slip Gaji
                            </button>
                        </div>
                    </form>

                    <button onclick="generateAllPayrollsMonthly()" style="width: 100%; margin-top: 10px; background: #FFFFFF; border: 1px solid #10B981; color: #059669; padding: 10px; border-radius: 14px; font-size: 12px; font-weight: 800; cursor: pointer;">
                        ⚡ Hitung Otomatis Seluruh Karyawan
                    </button>
                </div>
            </div>

            <!-- ============================================== -->
            <!-- ADMIN TAB 5: REKAP PDF                         -->
            <!-- ============================================== -->
            <div id="tab-adm-rekap" class="tab-pane" style="display: none;">
                <div class="card">
                    <div class="card-title" style="margin-bottom: 12px;"><ion-icon name="print-outline" style="color: #059669; font-size: 20px;"></ion-icon> Rekapitulasi Kehadiran & Cetak PDF</div>
                    
                    <div class="form-group">
                        <label class="form-label">Pilih Bulan Periode</label>
                        <input type="month" class="form-control" id="rekapMonthInput" value="">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Filter Divisi</label>
                        <select class="form-control" id="rekapDivSelect">
                            <option value="all">Semua Divisi</option>
                            <option value="Fotografer">Fotografer (PHOTO)</option>
                            <option value="Videografer">Videografer (VIDEO)</option>
                            <option value="Editor">Editor (EDIT)</option>
                            <option value="Produksi">Produksi (PROD)</option>
                            <option value="Marketing">Marketing (Bebas Radius)</option>
                            <option value="HR">HR & Admin</option>
                        </select>
                    </div>

                    <button onclick="openPrintRecap()" class="btn-clock-in" style="padding: 14px; margin-top: 10px;">
                        <ion-icon name="print-outline"></ion-icon> Cetak Lembar Dokumen Rekap PDF
                    </button>
                </div>
            </div>
        </div>

        <!-- ============================================== -->
        <!-- BOTTOM TAB NAVIGATION (Dynamic Role-Based)     -->
        <!-- ============================================== -->
        <nav class="bottom-nav" id="bottomNavContainer">
            <!-- Rendered by JS based on active role -->
        </nav>
    </div>

</div>

<!-- ============================================== -->
<!-- CAMERA SELFIE MODAL                            -->
<!-- ============================================== -->
<div class="modal-overlay" id="modalCamera">
    <div class="modal-sheet">
        <div class="modal-header">
            <div class="modal-title" id="camModalTitle">Clock In — Selfie & GPS</div>
            <button class="modal-close-btn" onclick="closeCameraModal()"><ion-icon name="close"></ion-icon></button>
        </div>

        <div class="camera-box">
            <video id="camera-feed" autoplay playsinline></video>
            <canvas id="camera-canvas" style="display: none;"></canvas>
            <div class="face-oval-guide">
                <div class="scan-beam"></div>
            </div>
        </div>

        <div style="text-align: center; font-size: 11px; color: var(--text-muted); margin-bottom: 12px;">
            Posisikan wajah Anda di tengah bingkai oval kamera
        </div>

        <button class="btn-clock-in" id="btnSnapPhoto" onclick="captureAndSubmitAttendance()">
            <ion-icon name="camera" style="font-size: 20px;"></ion-icon> Ambil Foto & Kirim Presensi
        </button>
    </div>
</div>

<!-- ============================================== -->
<!-- MODAL TAMBAH/EDIT KARYAWAN                     -->
<!-- ============================================== -->
<div class="modal-overlay" id="modalAdminEmployee">
    <div class="modal-sheet">
        <div class="modal-header">
            <div class="modal-title" id="modalEmpFormTitle">Tambah Karyawan Baru</div>
            <button class="modal-close-btn" onclick="closeModal('modalAdminEmployee')"><ion-icon name="close"></ion-icon></button>
        </div>
        <form onsubmit="saveAdminEmployee(event)">
            <input type="hidden" id="adminEmpId">
            <div class="form-group">
                <label class="form-label">Nama Lengkap</label>
                <input type="text" class="form-control" id="admEmpName" required>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                <div class="form-group">
                    <label class="form-label">Divisi</label>
                    <select class="form-control" id="admEmpDivision" required>
                        <option value="Fotografer">Fotografer (PHOTO)</option>
                        <option value="Videografer">Videografer (VIDEO)</option>
                        <option value="Editor">Editor (EDIT)</option>
                        <option value="Produksi">Produksi (PROD)</option>
                        <option value="Marketing">Marketing (Bebas Radius)</option>
                        <option value="HR">HR & Admin</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Jabatan / Posisi</label>
                    <input type="text" class="form-control" id="admEmpPosition" placeholder="Contoh: Senior Photographer" required>
                </div>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                <div class="form-group">
                    <label class="form-label">Role Akses</label>
                    <select class="form-control" id="admEmpRole" required>
                        <option value="karyawan">Karyawan</option>
                        <option value="supervisor">Supervisor</option>
                        <option value="admin">Admin</option>
                        <option value="owner">Owner</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Gaji Pokok (Rp)</label>
                    <input type="number" class="form-control" id="admEmpBaseSalary" value="4500000" required>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Nomor WhatsApp / HP</label>
                <input type="tel" class="form-control" id="admEmpPhone" placeholder="08xxxxxxxxxx">
            </div>
            <button type="submit" class="btn-clock-in" style="padding: 12px;">Simpan Karyawan</button>
        </form>
    </div>
</div>

<script>
    // State Store
    const state = {
        isLoggedIn: false,
        user: null,
        employee: null,
        employees: [],
        settings: {},
        todayAttendance: null,
        activeRoleView: 'employee', // 'employee' or 'admin'
        activeTab: 'absen',
        currentLat: null,
        currentLng: null,
        currentDistance: null,
        cameraStream: null,
        cameraClockType: 'in', // 'in' or 'out'
        deferredPrompt: null
    };

    // Service Worker for PWA
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('/sw.js').catch(err => console.log('SW register fail:', err));
        });
    }

    // PWA Install prompt listener
    window.addEventListener('beforeinstallprompt', (e) => {
        e.preventDefault();
        state.deferredPrompt = e;
        const banner = document.getElementById('floatingInstallBanner');
        if (banner) banner.style.display = 'flex';
    });

    function promptPwaInstall() {
        if (state.deferredPrompt) {
            state.deferredPrompt.prompt();
            state.deferredPrompt.userChoice.then((choice) => {
                if (choice.outcome === 'accepted') {
                    const banner = document.getElementById('floatingInstallBanner');
                    if (banner) banner.style.display = 'none';
                }
                state.deferredPrompt = null;
            });
        } else {
            alert('Panduan Instalasi PWA:\n\n1. Di Google Chrome / Edge: Klik ikon titik tiga di kanan atas > pilih "Instal Aplikasi" / "Tambahkan ke Layar Utama".\n2. Di iPhone (Safari): Ketuk tombol Share di bawah > pilih "Add to Home Screen".');
        }
    }

    // On Load
    document.addEventListener('DOMContentLoaded', () => {
        startLiveClock();
        loadInitialData();
        detectGpsLocation();

        const currentMonth = new Date().toISOString().slice(0, 7);
        if (document.getElementById('slipMonthPicker')) document.getElementById('slipMonthPicker').value = currentMonth;
        if (document.getElementById('admPayrollMonth')) document.getElementById('admPayrollMonth').value = currentMonth;
        if (document.getElementById('rekapMonthInput')) document.getElementById('rekapMonthInput').value = currentMonth;
        if (document.getElementById('formOtDate')) document.getElementById('formOtDate').value = new Date().toISOString().split('T')[0];
    });

    // 1. Live Digital Clock
    function startLiveClock() {
        const update = () => {
            const now = new Date();
            const timeStr = now.toLocaleTimeString('id-ID', { hour12: false });
            const dateStr = now.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
            if (document.getElementById('empLiveClock')) document.getElementById('empLiveClock').innerText = timeStr;
            if (document.getElementById('empLiveDate')) document.getElementById('empLiveDate').innerText = `${dateStr} · WIB`;
        };
        update();
        setInterval(update, 1000);
    }

    // 2. Fetch Initial API Data
    async function loadInitialData() {
        try {
            const res = await fetch('/api/hris/initial-data');
            const data = await res.json();
            if (data.success) {
                state.settings = data.settings || {};
                state.employee = data.employee;
                state.employees = data.employees || [];
                state.todayAttendance = data.today_attendance;

                // Check auth status
                if (data.is_logged_in && state.employee) {
                    state.isLoggedIn = true;
                    showScreen('main');

                    const isManager = ['owner', 'admin', 'hr', 'supervisor'].includes((state.employee.role || '').toLowerCase());
                    if (isManager) {
                        state.activeRoleView = 'admin';
                        state.activeTab = 'dash';
                        renderAppHeader();
                        renderBottomNav();
                        switchAdminTab('dash');
                        loadAdminStats();
                        loadAdminEmployeesList();
                    } else {
                        state.activeRoleView = 'employee';
                        state.activeTab = 'absen';
                        renderAppHeader();
                        renderBottomNav();
                        switchEmployeeTab('absen');
                        renderEmployeeAttendanceScreen();
                        loadHistory();
                        loadLeaves();
                        loadOvertimes();
                        loadEmployeeSlipDetails();
                    }
                } else {
                    state.isLoggedIn = false;
                    showScreen('login');
                }
            }
        } catch (e) {
            console.error('Failed to load initial HRIS data:', e);
        } finally {
            // Dismiss Loading Screen smoothly
            const loader = document.getElementById('appLoadingScreen');
            if (loader) {
                loader.style.opacity = '0';
                setTimeout(() => { loader.style.display = 'none'; }, 250);
            }
        }
    }

    function showScreen(screenId) {
        document.getElementById('screen-login').style.display = screenId === 'login' ? 'block' : 'none';
        document.getElementById('screen-main').style.display = screenId === 'main' ? 'flex' : 'none';
    }

    // 3. Auth Actions
    function switchAuthTab(type) {
        const tabLogin = document.getElementById('tabAuthLogin');
        const tabReg = document.getElementById('tabAuthRegister');
        const nameGroup = document.getElementById('authRegisterNameGroup');
        const btnText = document.getElementById('btnLoginText');

        if (type === 'login') {
            tabLogin.classList.add('active');
            tabReg.classList.remove('active');
            nameGroup.style.display = 'none';
            btnText.innerText = 'Masuk Aplikasi';
        } else {
            tabReg.classList.add('active');
            tabLogin.classList.remove('active');
            nameGroup.style.display = 'block';
            btnText.innerText = 'Daftarkan & Masuk';
        }
    }

    function togglePasswordVisibility(inputId) {
        const el = document.getElementById(inputId);
        el.type = el.type === 'password' ? 'text' : 'password';
    }

    function fillDemoCredentials() {
        document.getElementById('loginEmail').value = 'warungkombas@gmail.com';
        document.getElementById('loginPassword').value = 'Fariz123@';
        switchAuthTab('login');
    }

    async function handleAuthLogin(e) {
        e.preventDefault();
        const isRegister = document.getElementById('tabAuthRegister').classList.contains('active');
        const endpoint = isRegister ? '/api/hris/auth/register' : '/api/hris/auth/login';

        const payload = {
            email: document.getElementById('loginEmail').value,
            password: document.getElementById('loginPassword').value,
            name: document.getElementById('loginName').value || 'User Studio'
        };

        const btn = document.getElementById('btnLoginSubmit');
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<ion-icon name="sync-outline" class="spin"></ion-icon> Memproses...';

        try {
            const res = await fetch(endpoint, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify(payload)
            });
            const json = await res.json();
            if (json.success) {
                state.isLoggedIn = true;
                state.employee = json.employee;
                loadInitialData();
            } else {
                alert(`❌ ${json.message}`);
            }
        } catch (err) {
            console.error(err);
            alert('Terjadi kesalahan jaringan.');
        } finally {
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    }

    async function handleLogout() {
        if (!confirm('Yakin ingin keluar dari aplikasi?')) return;
        await fetch('/api/hris/auth/logout', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } });
        state.isLoggedIn = false;
        showScreen('login');
    }
    const handleAuthLogout = handleLogout;

    // 4. Header & Navigation Rendering
    function renderAppHeader() {
        const emp = state.employee;
        if (!emp) return;

        const canManage = ['owner', 'admin', 'hr', 'supervisor'].includes((emp.role || '').toLowerCase());
        const switchBtn = document.getElementById('appRoleSwitchContainer');
        if (switchBtn) {
            switchBtn.style.display = canManage ? 'inline-flex' : 'none';
        }

        const shield = document.getElementById('headerIconShield');
        const avatar = document.getElementById('appHeaderAvatar');
        const headerName = document.getElementById('appHeaderName');
        const headerSub = document.getElementById('appHeaderSubtitle');
        const roleLabel = document.getElementById('roleViewLabel');

        if (state.activeRoleView === 'admin') {
            if (shield) shield.style.display = 'flex';
            if (avatar) avatar.style.display = 'none';
            if (headerName) headerName.innerText = 'Administrator / Owner';
            if (roleLabel) roleLabel.innerText = 'SISI KARYAWAN';

            // Subtitle reflects current admin tab (matching reference images)
            if (headerSub) {
                if (state.activeTab === 'dash') headerSub.innerText = 'Panel Admin & HR Studio';
                else if (state.activeTab === 'approval') headerSub.innerText = 'Persetujuan Cuti & Lembur';
                else if (state.activeTab === 'karyawan') headerSub.innerText = 'Data Karyawan & Lokasi Studio';
                else if (state.activeTab === 'payroll') headerSub.innerText = 'Manajemen Gaji & Slip';
                else if (state.activeTab === 'rekap') headerSub.innerText = 'Rekapitulasi Kehadiran Karyawan';
            }
        } else {
            if (shield) shield.style.display = 'none';
            if (avatar) {
                avatar.style.display = 'block';
                if (emp.avatar_url) avatar.src = emp.avatar_url;
            }
            if (headerName) headerName.innerText = emp.name;
            if (headerSub) headerSub.innerText = `${emp.division || 'Studio'} • ${emp.position || 'Staff'}`;
            if (roleLabel) roleLabel.innerText = 'MODE ADMIN';
        }

        // Profile tab fields
        if (document.getElementById('profileNameText')) document.getElementById('profileNameText').innerText = emp.name;
        if (document.getElementById('profileCodeDivText')) document.getElementById('profileCodeDivText').innerText = `${emp.employee_code || 'EMP-001'} • ${emp.division || 'Studio'}`;
        if (document.getElementById('profileRoleBadge')) document.getElementById('profileRoleBadge').innerText = (emp.role || 'karyawan').toUpperCase();
        if (document.getElementById('profNameInput')) document.getElementById('profNameInput').value = emp.name;
        if (document.getElementById('profPhoneInput')) document.getElementById('profPhoneInput').value = emp.phone || '';
    }

    function toggleAppRoleView() {
        state.activeRoleView = state.activeRoleView === 'admin' ? 'employee' : 'admin';
        renderAppHeader();
        renderBottomNav();

        if (state.activeRoleView === 'admin') {
            switchAdminTab('dash');
            loadAdminStats();
        } else {
            switchEmployeeTab('absen');
            renderEmployeeAttendanceScreen();
        }
    }

    function renderBottomNav() {
        const container = document.getElementById('bottomNavContainer');
        if (!container) return;

        if (state.activeRoleView === 'employee') {
            container.innerHTML = `
                <div class="bottom-nav-inner">
                    <button class="nav-tab-item ${state.activeTab === 'absen' ? 'active' : ''}" id="nav-btn-absen" onclick="switchEmployeeTab('absen')">
                        <ion-icon name="finger-print-outline"></ion-icon>
                        <span>Absensi</span>
                    </button>
                    <button class="nav-tab-item ${state.activeTab === 'cuti' ? 'active' : ''}" id="nav-btn-cuti" onclick="switchEmployeeTab('cuti')">
                        <ion-icon name="calendar-outline"></ion-icon>
                        <span>Cuti</span>
                    </button>
                    <button class="nav-tab-item ${state.activeTab === 'lembur' ? 'active' : ''}" id="nav-btn-lembur" onclick="switchEmployeeTab('lembur')">
                        <ion-icon name="time-outline"></ion-icon>
                        <span>Lembur</span>
                    </button>
                    <button class="nav-tab-item ${state.activeTab === 'slip' ? 'active' : ''}" id="nav-btn-slip" onclick="switchEmployeeTab('slip')">
                        <ion-icon name="document-text-outline"></ion-icon>
                        <span>Slip Gaji</span>
                    </button>
                    <button class="nav-tab-item ${state.activeTab === 'profil' ? 'active' : ''}" id="nav-btn-profil" onclick="switchEmployeeTab('profil')">
                        <ion-icon name="person-circle-outline"></ion-icon>
                        <span>Profil</span>
                    </button>
                </div>
            `;
        } else {
            container.innerHTML = `
                <div class="bottom-nav-inner">
                    <button class="nav-tab-item ${state.activeTab === 'dash' ? 'active' : ''}" id="nav-btn-ad-dash" onclick="switchAdminTab('dash')">
                        <ion-icon name="grid-outline"></ion-icon>
                        <span>Dashboard</span>
                    </button>
                    <button class="nav-tab-item ${state.activeTab === 'approval' ? 'active' : ''}" id="nav-btn-ad-approval" onclick="switchAdminTab('approval')">
                        <ion-icon name="checkmark-done-circle-outline"></ion-icon>
                        <span>Approval</span>
                    </button>
                    <button class="nav-tab-item ${state.activeTab === 'karyawan' ? 'active' : ''}" id="nav-btn-ad-karyawan" onclick="switchAdminTab('karyawan')">
                        <ion-icon name="people-outline"></ion-icon>
                        <span>Karyawan</span>
                    </button>
                    <button class="nav-tab-item ${state.activeTab === 'payroll' ? 'active' : ''}" id="nav-btn-ad-payroll" onclick="switchAdminTab('payroll')">
                        <ion-icon name="cash-outline"></ion-icon>
                        <span>Payroll</span>
                    </button>
                    <button class="nav-tab-item ${state.activeTab === 'rekap' ? 'active' : ''}" id="nav-btn-ad-rekap" onclick="switchAdminTab('rekap')">
                        <ion-icon name="print-outline"></ion-icon>
                        <span>Rekap PDF</span>
                    </button>
                </div>
            `;
        }
    }

    function switchEmployeeTab(tab) {
        state.activeTab = tab;
        document.querySelectorAll('.tab-pane').forEach(el => el.style.display = 'none');
        document.querySelectorAll('.nav-tab-item').forEach(el => el.classList.remove('active'));

        const target = document.getElementById(`tab-emp-${tab}`);
        const nav = document.getElementById(`nav-btn-${tab}`);
        if (target) target.style.display = 'block';
        if (nav) nav.classList.add('active');

        renderAppHeader();
    }

    function switchAdminTab(tab) {
        state.activeTab = tab;
        document.querySelectorAll('.tab-pane').forEach(el => el.style.display = 'none');
        document.querySelectorAll('.nav-tab-item').forEach(el => el.classList.remove('active'));

        const target = document.getElementById(`tab-adm-${tab}`);
        const nav = document.getElementById(`nav-btn-ad-${tab}`);
        if (target) target.style.display = 'block';
        if (nav) nav.classList.add('active');

        renderAppHeader();

        if (tab === 'dash') {
            loadAdminStats();
        } else if (tab === 'approval') {
            setApprovalSub('leave');
        } else if (tab === 'karyawan') {
            setEmployeeSub('list');
            loadAdminEmployeesList();
        } else if (tab === 'payroll') {
            loadAdminPayrollForEmployee();
        }
    }

    // 5. GPS & Geofence
    function calculateDistance(lat1, lon1, lat2, lon2) {
        const R = 6371000;
        const dLat = (lat2 - lat1) * Math.PI / 180;
        const dLon = (lon2 - lon1) * Math.PI / 180;
        const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                  Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                  Math.sin(dLon/2) * Math.sin(dLon/2);
        return Math.round(R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a)) * 10) / 10;
    }

    function detectGpsLocation() {
        if (!navigator.geolocation) return;
        navigator.geolocation.getCurrentPosition(
            (pos) => {
                state.currentLat = pos.coords.latitude;
                state.currentLng = pos.coords.longitude;

                const studioLat = parseFloat(state.settings.studio_lat || -6.175392);
                const studioLng = parseFloat(state.settings.studio_lng || 106.827153);
                const maxRadius = parseInt(state.settings.radius_meters || 100);

                const dist = calculateDistance(state.currentLat, state.currentLng, studioLat, studioLng);
                state.currentDistance = dist;

                const isMarketing = (state.employee && state.employee.division || '').toLowerCase() === 'marketing';
                const distEl = document.getElementById('geofenceDistanceText');
                if (distEl) distEl.innerText = `${dist} m dari Studio`;

                const mktBadge = document.getElementById('marketingBypassBadge');
                if (mktBadge) mktBadge.style.display = isMarketing ? 'inline-flex' : 'none';
            },
            (err) => {
                console.warn('GPS notice:', err.message || err);
                const isMarketing = (state.employee && state.employee.division || '').toLowerCase() === 'marketing';
                const distEl = document.getElementById('geofenceDistanceText');
                if (distEl) distEl.innerText = isMarketing ? 'Bebas Radius (Marketing)' : 'Area Studio';
            },
            { enableHighAccuracy: false, timeout: 10000, maximumAge: 30000 }
        );
    }

    // 6. Camera & Attendance Flow
    async function openCameraModal(type) {
        state.cameraClockType = type;
        document.getElementById('camModalTitle').innerText = type === 'in' ? 'Clock In — Selfie & GPS' : 'Clock Out — Selfie & GPS';
        document.getElementById('modalCamera').classList.add('active');

        try {
            const constraints = { video: { facingMode: 'user', width: { ideal: 640 }, height: { ideal: 480 } }, audio: false };
            state.cameraStream = await navigator.mediaDevices.getUserMedia(constraints);
            const feed = document.getElementById('camera-feed');
            if (feed) feed.srcObject = state.cameraStream;
        } catch (e) {
            alert('Akses kamera depan wajib diizinkan untuk mengambil foto selfie presensi.');
            closeCameraModal();
        }
    }

    function closeCameraModal() {
        if (state.cameraStream) {
            state.cameraStream.getTracks().forEach(t => t.stop());
            state.cameraStream = null;
        }
        document.getElementById('modalCamera').classList.remove('active');
    }

    function handleClockOutClick() {
        // Validation matching expo app: Clock in first!
        if (!state.todayAttendance || !state.todayAttendance.clock_in) {
            alert('WAJIB CLOCK IN DULU:\n\nAnda belum melakukan Clock In hari ini. Silakan lakukan Clock In terlebih dahulu sebelum Clock Out.');
            return;
        }

        // Check if before 17:00
        const now = new Date();
        const hour = now.getHours();
        if (hour < 17) {
            if (confirm('Belum Waktunya Pulang!\n\nShift Kerja Studio berakhir pada pk 17:00 WIB.\nApakah Anda yakin ingin Izin Pulang Cepat / Simulasi Clock Out?')) {
                openCameraModal('out');
            }
        } else {
            openCameraModal('out');
        }
    }

    async function captureAndSubmitAttendance() {
        const video = document.getElementById('camera-feed');
        const canvas = document.getElementById('camera-canvas');
        if (!video || !canvas) return;

        canvas.width = video.videoWidth || 640;
        canvas.height = video.videoHeight || 480;
        const ctx = canvas.getContext('2d');
        ctx.translate(canvas.width, 0);
        ctx.scale(-1, 1);
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
        const photoBase64 = canvas.toDataURL('image/jpeg', 0.85);

        const type = state.cameraClockType;
        const endpoint = type === 'in' ? '/api/hris/attendance/clock-in' : '/api/hris/attendance/clock-out';
        const btn = document.getElementById('btnSnapPhoto');
        btn.disabled = true;
        btn.innerHTML = '<ion-icon name="sync-outline" class="spin"></ion-icon> Mengirim ke Database...';

        try {
            const fallbackLat = state.settings && state.settings.studio_lat ? parseFloat(state.settings.studio_lat) : -6.175392;
            const fallbackLng = state.settings && state.settings.studio_lng ? parseFloat(state.settings.studio_lng) : 106.827153;
            const res = await fetch(endpoint, {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}' 
                },
                body: JSON.stringify({
                    employee_id: state.employee.id,
                    photo: photoBase64,
                    lat: (state.currentLat !== null && state.currentLat !== undefined) ? state.currentLat : fallbackLat,
                    lng: (state.currentLng !== null && state.currentLng !== undefined) ? state.currentLng : fallbackLng
                })
            });
            let json;
            try {
                json = await res.json();
            } catch (err) {
                const text = await res.text();
                throw new Error(text.includes('Page Expired') ? 'Sesi kedaluwarsa, silakan refresh halaman.' : `Server error (${res.status})`);
            }
            if (json.success) {
                alert(`✅ ${json.message}`);
                closeCameraModal();
                loadInitialData();
            } else {
                alert(`❌ ${json.message}`);
            }
        } catch (e) {
            console.error(e);
            alert('Gagal mengirim data presensi: ' + (e.message || 'Koneksi error'));
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<ion-icon name="camera"></ion-icon> Ambil Foto & Kirim Presensi';
        }
    }

    function renderEmployeeAttendanceScreen() {
        const att = state.todayAttendance;
        const badge = document.getElementById('empTodayBadge');
        const inTime = document.getElementById('empTodayInTime');
        const inDist = document.getElementById('empTodayInDist');
        const outTime = document.getElementById('empTodayOutTime');
        const outDur = document.getElementById('empTodayWorkDuration');

        if (!att || !att.clock_in) {
            badge.className = 'badge badge-warning';
            badge.innerText = 'Belum Clock In';
            inTime.innerText = '--:--';
            inDist.innerText = '-- m dari Studio';
            outTime.innerText = 'Belum Pulang';
            outDur.innerText = '-';
            return;
        }

        const inDate = new Date(att.clock_in);
        inTime.innerText = inDate.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) + ' WIB';
        inDist.innerText = `${att.distance_meters || 0} m dari Studio`;

        if (att.status === 'terlambat') {
            badge.className = 'badge badge-danger';
            badge.innerText = `Terlambat (${att.late_minutes}m)`;
        } else {
            badge.className = 'badge badge-success';
            badge.innerText = 'Hadir Tepat Waktu';
        }

        if (att.clock_out) {
            const outDate = new Date(att.clock_out);
            outTime.innerText = outDate.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) + ' WIB';
            const diffHours = ((outDate - inDate) / (1000 * 60 * 60)).toFixed(1);
            outDur.innerText = `Durasi: ${diffHours} Jam`;
        } else {
            outTime.innerText = 'Aktif Bekerja';
            outDur.innerText = 'Sedang Berlangsung';
        }
    }

    // 7. Leaves (Cuti)
    function setLeavePreset(startDays, endDays) {
        const today = new Date();
        const start = new Date(today);
        start.setDate(today.getDate() + startDays);
        const end = new Date(today);
        end.setDate(today.getDate() + endDays);

        document.getElementById('formLeaveStart').value = start.toISOString().split('T')[0];
        document.getElementById('formLeaveEnd').value = end.toISOString().split('T')[0];
    }

    async function submitLeave(e) {
        e.preventDefault();
        const payload = {
            employee_id: state.employee.id,
            type: document.getElementById('formLeaveType').value,
            start_date: document.getElementById('formLeaveStart').value,
            end_date: document.getElementById('formLeaveEnd').value,
            reason: document.getElementById('formLeaveReason').value
        };

        const res = await fetch('/api/hris/leaves', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify(payload)
        });
        const json = await res.json();
        if (json.success) {
            alert(`✅ ${json.message}`);
            document.getElementById('formLeaveReason').value = '';
            loadLeaves();
        } else {
            alert(`❌ ${json.message}`);
        }
    }

    async function loadLeaves() {
        const list = document.getElementById('empLeavesList');
        if (!list) return;
        const res = await fetch('/api/hris/leaves');
        const json = await res.json();
        if (json.success && json.leaves.length > 0) {
            list.innerHTML = json.leaves.map(l => `
                <div class="list-item-row">
                    <div>
                        <div style="font-size: 13px; font-weight: 800; color: #111827;">${l.type} (${l.total_days} hari)</div>
                        <div style="font-size: 11px; color: var(--text-muted);">${l.start_date} s/d ${l.end_date}</div>
                        <div style="font-size: 11px; color: #4B5563; margin-top: 2px;"><i>"${l.reason}"</i></div>
                    </div>
                    <span class="badge ${l.status === 'approved' ? 'badge-success' : (l.status === 'rejected' ? 'badge-danger' : 'badge-warning')}">
                        ${l.status.toUpperCase()}
                    </span>
                </div>
            `).join('');
        } else {
            list.innerHTML = '<div style="font-size: 12px; color: var(--text-muted); text-align: center; padding: 15px;">Belum ada riwayat pengajuan cuti di database.</div>';
        }
    }

    // 8. Overtime (Lembur)
    function setOtPreset(start, end) {
        document.getElementById('formOtStart').value = start;
        document.getElementById('formOtEnd').value = end;
    }

    async function submitOvertime(e) {
        e.preventDefault();
        const payload = {
            employee_id: state.employee.id,
            date: document.getElementById('formOtDate').value,
            start_time: document.getElementById('formOtStart').value,
            end_time: document.getElementById('formOtEnd').value,
            description: document.getElementById('formOtDesc').value
        };

        const res = await fetch('/api/hris/overtimes', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify(payload)
        });
        const json = await res.json();
        if (json.success) {
            alert(`✅ ${json.message}`);
            document.getElementById('formOtDesc').value = '';
            loadOvertimes();
        } else {
            alert(`❌ ${json.message}`);
        }
    }

    async function loadOvertimes() {
        const list = document.getElementById('empOvertimesList');
        if (!list) return;
        const res = await fetch('/api/hris/overtimes');
        const json = await res.json();
        if (json.success && json.overtimes.length > 0) {
            list.innerHTML = json.overtimes.map(ot => `
                <div class="list-item-row">
                    <div>
                        <div style="font-size: 13px; font-weight: 800; color: #111827;">${ot.project_name || 'Lembur'}</div>
                        <div style="font-size: 11px; color: var(--text-muted);">${ot.date} • ${ot.start_time} - ${ot.end_time} (${ot.duration_hours} Jam)</div>
                        <div style="font-size: 11px; color: #059669; font-weight: 700; margin-top: 2px;">Est: Rp ${(ot.estimated_pay||0).toLocaleString('id-ID')}</div>
                    </div>
                    <span class="badge ${ot.status === 'approved' ? 'badge-success' : (ot.status === 'rejected' ? 'badge-danger' : 'badge-warning')}">
                        ${ot.status.toUpperCase()}
                    </span>
                </div>
            `).join('');
        } else {
            list.innerHTML = '<div style="font-size: 12px; color: var(--text-muted); text-align: center; padding: 15px;">Belum ada pengajuan lembur di database.</div>';
        }
    }

    // 9. Employee Payslip
    async function loadEmployeeSlipDetails() {
        const month = document.getElementById('slipMonthPicker').value;
        const box = document.getElementById('slipComponentsBox');
        if (!box) return;

        const res = await fetch('/api/hris/payrolls');
        const json = await res.json();
        const payrolls = json.payrolls || [];
        const active = payrolls.find(p => p.period_month === month) || payrolls[0];

        if (active) {
            document.getElementById('slipNetSalaryText').innerText = 'Rp ' + (active.net_salary || 0).toLocaleString('id-ID');
            document.getElementById('slipPeriodLabel').innerText = `Periode: ${active.period_month}`;
            state.activePayrollId = active.id;

            box.innerHTML = `
                <div style="display: flex; justify-content: space-between; font-size: 13px; margin-bottom: 6px;">
                    <span style="color: var(--text-muted);">Gaji Pokok:</span>
                    <b style="color: #111827;">Rp ${(active.base_salary || 0).toLocaleString('id-ID')}</b>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 13px; margin-bottom: 6px;">
                    <span style="color: var(--text-muted);">Tunjangan:</span>
                    <b style="color: #111827;">Rp ${(active.allowance || 0).toLocaleString('id-ID')}</b>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 13px; margin-bottom: 6px;">
                    <span style="color: var(--text-muted);">Uang Lembur (${active.total_overtime_hours || 0} Jam):</span>
                    <b style="color: #059669;">+ Rp ${(active.overtime_pay || 0).toLocaleString('id-ID')}</b>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 13px; margin-bottom: 6px;">
                    <span style="color: var(--text-muted);">Potongan Terlambat:</span>
                    <b style="color: #DC2626;">- Rp ${(active.late_deductions || 0).toLocaleString('id-ID')}</b>
                </div>
                <div style="border-top: 1px dashed var(--border); margin: 8px 0;"></div>
                <div style="display: flex; justify-content: space-between; font-size: 14px; font-weight: 800;">
                    <span style="color: #064E3B;">Total Gaji Bersih:</span>
                    <span style="color: #059669;">Rp ${(active.net_salary || 0).toLocaleString('id-ID')}</span>
                </div>
            `;
        } else {
            const emp = state.employee || {};
            const est = (emp.base_salary || 4500000) + (emp.allowance || 500000);
            document.getElementById('slipNetSalaryText').innerText = 'Rp ' + est.toLocaleString('id-ID');
            document.getElementById('slipPeriodLabel').innerText = `Periode: ${month} (Estimasi Master)`;
            box.innerHTML = `
                <div style="display: flex; justify-content: space-between; font-size: 13px; margin-bottom: 6px;">
                    <span style="color: var(--text-muted);">Gaji Pokok:</span>
                    <b>Rp ${(emp.base_salary || 4500000).toLocaleString('id-ID')}</b>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 13px; margin-bottom: 6px;">
                    <span style="color: var(--text-muted);">Tunjangan:</span>
                    <b>Rp ${(emp.allowance || 500000).toLocaleString('id-ID')}</b>
                </div>
                <div style="font-size: 11px; color: var(--text-muted); margin-top: 10px; font-style: italic;">
                    *Slip resmi bulan ini belum diterbitkan oleh Admin. Hubungi HR untuk penerbitan slip gaji.
                </div>
            `;
        }
    }

    function printActivePayslip() {
        if (state.activePayrollId) {
            window.open(`/hris/payslip/${state.activePayrollId}/print`, '_blank');
        } else {
            alert('Slip gaji resmi bulan ini belum diterbitkan Admin. Silakan pilih bulan yang sudah terbit atau hubungi Admin Studio.');
        }
    }

    // 10. Attendance History
    async function loadHistory() {
        const list = document.getElementById('empAttendanceHistoryList');
        if (!list) return;
        const res = await fetch('/api/hris/attendance/history');
        const json = await res.json();
        if (json.success && json.attendances.length > 0) {
            list.innerHTML = json.attendances.map(a => `
                <div class="list-item-row">
                    <div>
                        <div style="font-size: 13px; font-weight: 800; color: #111827;">${a.date}</div>
                        <div style="font-size: 11px; color: var(--text-muted);">
                            Masuk: <b>${a.clock_in ? new Date(a.clock_in).toLocaleTimeString('id-ID', {hour:'2-digit', minute:'2-digit'}) : '-'}</b>
                            · Pulang: <b>${a.clock_out ? new Date(a.clock_out).toLocaleTimeString('id-ID', {hour:'2-digit', minute:'2-digit'}) : '-'}</b>
                        </div>
                        <div style="font-size: 10px; color: #6B7280; margin-top: 2px;">Jarak: ${a.distance_meters || 0} m</div>
                    </div>
                    <span class="badge ${a.status === 'hadir' ? 'badge-success' : 'badge-danger'}">
                        ${a.status.toUpperCase()}
                    </span>
                </div>
            `).join('');
        } else {
            list.innerHTML = '<div style="font-size: 12px; color: var(--text-muted); text-align: center; padding: 15px;">Belum ada riwayat absensi tercatat di database.</div>';
        }
    }

    // 11. Profile Update
    async function saveProfileBiodata(e) {
        e.preventDefault();
        const payload = {
            name: document.getElementById('profNameInput').value,
            phone: document.getElementById('profPhoneInput').value
        };

        const res = await fetch('/api/hris/profile/update', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify(payload)
        });
        const json = await res.json();
        if (json.success) {
            alert('✅ Biodata kontak berhasil disimpan ke database!');
            loadInitialData();
        }
    }

    // 12. ADMIN FEATURES
    async function loadAdminStats() {
        try {
            const res = await fetch('/api/hris/admin/stats');
            const json = await res.json();
            if (json.success) {
                const s = json.stats;
                const empTotal = s.total_employees !== undefined ? s.total_employees : (state.employees ? state.employees.length : 1);
                
                if (document.getElementById('adStatTotalEmp')) document.getElementById('adStatTotalEmp').innerText = empTotal;
                if (document.getElementById('adStatPresent')) document.getElementById('adStatPresent').innerText = s.present_today || 0;
                if (document.getElementById('adStatLate')) document.getElementById('adStatLate').innerText = s.late_today || 0;
                if (document.getElementById('adStatAbsent')) document.getElementById('adStatAbsent').innerText = s.absent_today !== undefined ? s.absent_today : Math.max(0, empTotal - (s.present_today || 0));
                
                if (document.getElementById('adPendingLeavesCount')) document.getElementById('adPendingLeavesCount').innerText = s.pending_leaves || 0;
                if (document.getElementById('adPendingOvertimesCount')) document.getElementById('adPendingOvertimesCount').innerText = s.pending_overtimes || 0;
                if (document.getElementById('countAppSubLeave')) document.getElementById('countAppSubLeave').innerText = s.pending_leaves || 0;
                if (document.getElementById('countAppSubOt')) document.getElementById('countAppSubOt').innerText = s.pending_overtimes || 0;

                renderAdminLiveAttendances(json.today_attendances || []);
            }
        } catch (e) {
            console.error('loadAdminStats error:', e);
        }
    }

    function renderAdminLiveAttendances(items) {
        const list = document.getElementById('adLiveAttendancesList');
        if (!list) return;

        if (items.length === 0) {
            list.innerHTML = '<div class="empty-activity-msg">Belum ada karyawan yang melakukan Clock In hari ini.</div>';
            return;
        }

        list.innerHTML = items.map(a => `
            <div class="list-item-row" style="padding: 12px 14px; background: #FFFFFF; border-radius: 12px; margin-bottom: 8px; border: 1px solid #E5E7EB; display: flex; align-items: center; justify-content: space-between;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <img src="${a.clock_in_photo || (a.employee && a.employee.avatar_url) || 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=150'}" style="width: 40px; height: 40px; border-radius: 10px; object-fit: cover;">
                    <div>
                        <div style="font-size: 14px; font-weight: 700; color: #111827;">${a.employee ? a.employee.name : 'Karyawan'}</div>
                        <div style="font-size: 12px; color: #6B7280;">
                            ${a.employee ? a.employee.division : 'Studio'} • Masuk: <b>${a.clock_in ? new Date(a.clock_in).toLocaleTimeString('id-ID', {hour:'2-digit', minute:'2-digit'}) : '-'}</b>
                        </div>
                    </div>
                </div>
                <span class="badge ${a.status === 'hadir' ? 'badge-success' : 'badge-danger'}">${a.status.toUpperCase()}</span>
            </div>
        `).join('');
    }

    function setApprovalSub(type) {
        const btnLeave = document.getElementById('btnAppSubLeave');
        const btnOt = document.getElementById('btnAppSubOt');
        if (btnLeave) btnLeave.classList.toggle('active', type === 'leave');
        if (btnOt) btnOt.classList.toggle('active', type === 'overtime');
        loadAdminApprovalList(type);
    }

    async function loadAdminApprovalList(type) {
        const list = document.getElementById('adminApprovalItemsList');
        if (!list) return;

        try {
            const endpoint = type === 'leave' ? '/api/hris/leaves?all=1' : '/api/hris/overtimes?all=1';
            const res = await fetch(endpoint);
            const json = await res.json();
            const items = type === 'leave' ? (json.leaves || []) : (json.overtimes || []);
            const pending = items.filter(i => i.status === 'pending');

            if (pending.length === 0) {
                list.innerHTML = `<div class="empty-activity-msg" style="padding: 48px 16px; text-align: center; color: #6B7280; font-size: 13px;">Belum ada pengajuan ${type === 'leave' ? 'cuti' : 'lembur'} dari karyawan.</div>`;
                return;
            }

            list.innerHTML = pending.map(item => `
                <div class="card" style="margin-bottom: 12px; background: #FFFFFF; border-radius: 16px; padding: 16px; border: 1px solid #E5E7EB;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                        <b style="font-size: 14px; color: #006847;">${item.employee ? item.employee.name : 'Karyawan'}</b>
                        <span class="badge badge-warning" style="background: #FEF3C7; color: #92400E; font-size: 10px; font-weight: 700; padding: 4px 8px; border-radius: 6px;">PENDING</span>
                    </div>
                    <div style="font-size: 13px; color: #374151;">
                        ${type === 'leave' ? `<b>${item.type}</b> (${item.total_days} hari) · ${item.start_date}` : `<b>${item.project_name || 'Lembur'}</b> (${item.duration_hours} Jam) · ${item.date}`}
                    </div>
                    <div style="font-size: 12px; color: #6B7280; margin-top: 6px;">Alasan: <i>"${item.reason || item.description || '-'}"</i></div>
                    <div style="display: flex; gap: 8px; margin-top: 14px;">
                        <button onclick="processApproval('${type}', ${item.id}, 'approved')" style="flex: 1; background: #006847; color: #FFFFFF; border: none; padding: 10px; border-radius: 10px; font-weight: 700; font-size: 13px; cursor: pointer;">
                            ✓ Setujui
                        </button>
                        <button onclick="processApproval('${type}', ${item.id}, 'rejected')" style="flex: 1; background: #DC2626; color: #FFFFFF; border: none; padding: 10px; border-radius: 10px; font-weight: 700; font-size: 13px; cursor: pointer;">
                            ✕ Tolak
                        </button>
                    </div>
                </div>
            `).join('');
        } catch (e) {
            console.error('loadAdminApprovalList error:', e);
        }
    }

    async function processApproval(type, id, action) {
        const endpoint = type === 'leave' ? `/api/hris/admin/leaves/${id}/action` : `/api/hris/admin/overtimes/${id}/action`;
        const res = await fetch(endpoint, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ action: action })
        });
        const json = await res.json();
        if (json.success) {
            alert(`✅ ${json.message}`);
            loadAdminStats();
            loadAdminApprovalList(type);
        }
    }

    // 13. Karyawan & Lokasi Studio Subtab
    function setEmployeeSub(type) {
        const btnList = document.getElementById('btnEmpSubList');
        const btnStudio = document.getElementById('btnEmpSubStudio');
        const subList = document.getElementById('adminSubEmployeeList');
        const subStudio = document.getElementById('adminSubStudioSettings');

        if (btnList) btnList.classList.toggle('active', type === 'list');
        if (btnStudio) btnStudio.classList.toggle('active', type === 'studio');
        if (subList) subList.style.display = type === 'list' ? 'block' : 'none';
        if (subStudio) subStudio.style.display = type === 'studio' ? 'block' : 'none';
    }

    function loadAdminEmployeesList() {
        filterAdminEmployees();
    }

    function filterAdminEmployees() {
        const container = document.getElementById('adminEmployeesContainer');
        const select = document.getElementById('admPayrollEmpSelect');
        if (!container) return;

        const query = (document.getElementById('adminEmpSearchInput')?.value || '').toLowerCase().trim();
        const employees = state.employees || [];

        const filtered = query 
            ? employees.filter(e => 
                (e.name || '').toLowerCase().includes(query) ||
                (e.employee_code || '').toLowerCase().includes(query) ||
                (e.email || '').toLowerCase().includes(query) ||
                (e.division || '').toLowerCase().includes(query) ||
                (e.position || '').toLowerCase().includes(query)
              )
            : employees;

        const badgeCount = document.getElementById('countEmpSubList');
        if (badgeCount) badgeCount.innerText = employees.length;

        const radiusLabel = document.getElementById('labelStudioRadius');
        if (radiusLabel) radiusLabel.innerText = `${state.settings.radius_meters || 100}m`;

        if (select) {
            select.innerHTML = employees.map(e => `<option value="${e.id}">${e.name} (${e.division} • ${e.employee_code})</option>`).join('');
        }

        if (filtered.length === 0) {
            container.innerHTML = `<div style="text-align: center; color: #6B7280; padding: 32px 16px; font-size: 13px;">Tidak ada data karyawan yang cocok.</div>`;
            return;
        }

        const maxRadius = state.settings.radius_meters || 100;
        container.innerHTML = filtered.map(emp => {
            const empCode = emp.employee_code || 'EMP-001';
            const empPos = emp.position || 'Owner Studio';
            const empDiv = emp.division || 'HR & Admin';
            const empEmail = emp.email || 'warungkombas@gmail.com';
            const empRole = (emp.role || 'owner').toUpperCase();
            const serialized = JSON.stringify(emp).replace(/'/g, "&#39;");

            return `
                <div class="employee-card">
                    <div class="employee-card-left">
                        <h4 class="emp-card-name">${emp.name}</h4>
                        <div class="emp-card-sub">${empCode} · ${empPos} · ${empDiv}</div>
                        <div class="emp-card-email">${empEmail}</div>
                        <div class="emp-card-badge">Role: ${empRole} · Wajib &lt;= ${maxRadius}m</div>
                    </div>
                    <div class="employee-card-right">
                        <button class="btn-edit-emp" onclick='editAdminEmployee(${serialized})' title="Edit Karyawan">
                            <ion-icon name="create-outline"></ion-icon>
                        </button>
                    </div>
                </div>
            `;
        }).join('');

        // Populate studio settings form
        const s = state.settings || {};
        if (document.getElementById('studioNameInput')) document.getElementById('studioNameInput').value = s.studio_name || 'Studio Utama';
        if (document.getElementById('studioAddressInput')) document.getElementById('studioAddressInput').value = s.studio_address || 'Jl. Studio Fotografi No. 1 (Ubah titik GPS asli langsung dari menu Admin di HP)';
        if (document.getElementById('studioLatInput')) document.getElementById('studioLatInput').value = s.studio_lat || -7.672585;
        if (document.getElementById('studioLngInput')) document.getElementById('studioLngInput').value = s.studio_lng || 109.649065;
        if (document.getElementById('studioRadiusInput')) document.getElementById('studioRadiusInput').value = s.radius_meters || 100;
        if (document.getElementById('studioLateTolInput')) document.getElementById('studioLateTolInput').value = s.late_tolerance_minutes || 15;
    }

    function openEmployeeModal() {
        document.getElementById('modalEmpFormTitle').innerText = 'Tambah Karyawan Baru';
        document.getElementById('adminEmpId').value = '';
        document.getElementById('admEmpName').value = '';
        document.getElementById('admEmpPosition').value = '';
        document.getElementById('admEmpBaseSalary').value = 4500000;
        document.getElementById('admEmpPhone').value = '';
        document.getElementById('modalAdminEmployee').classList.add('active');
    }

    function editAdminEmployee(emp) {
        document.getElementById('modalEmpFormTitle').innerText = 'Edit Data Karyawan';
        document.getElementById('adminEmpId').value = emp.id;
        document.getElementById('admEmpName').value = emp.name;
        document.getElementById('admEmpDivision').value = emp.division;
        document.getElementById('admEmpPosition').value = emp.position;
        document.getElementById('admEmpRole').value = emp.role;
        document.getElementById('admEmpBaseSalary').value = emp.base_salary;
        document.getElementById('admEmpPhone').value = emp.phone || '';
        document.getElementById('modalAdminEmployee').classList.add('active');
    }

    async function saveAdminEmployee(e) {
        e.preventDefault();
        const payload = {
            id: document.getElementById('adminEmpId').value || null,
            name: document.getElementById('admEmpName').value,
            division: document.getElementById('admEmpDivision').value,
            position: document.getElementById('admEmpPosition').value,
            role: document.getElementById('admEmpRole').value,
            base_salary: document.getElementById('admEmpBaseSalary').value,
            phone: document.getElementById('admEmpPhone').value
        };

        const res = await fetch('/api/hris/admin/employees', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify(payload)
        });
        const json = await res.json();
        if (json.success) {
            alert('✅ Data karyawan berhasil disimpan ke database!');
            closeModal('modalAdminEmployee');
            loadInitialData();
        }
    }

    async function deleteAdminEmployee(id) {
        if (!confirm('Hapus karyawan ini dari database?')) return;
        const res = await fetch(`/api/hris/admin/employees/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } });
        const json = await res.json();
        if (json.success) {
            loadInitialData();
        }
    }

    function setStudioCoordsCurrentGps() {
        if (state.currentLat && state.currentLng) {
            document.getElementById('studioLatInput').value = state.currentLat;
            document.getElementById('studioLngInput').value = state.currentLng;
            alert(`Koordinat GPS terisi: Lat: ${state.currentLat}, Lng: ${state.currentLng}`);
        } else {
            alert('Mendeteksi GPS...');
            detectGpsLocation();
        }
    }

    async function saveStudioSettings(e) {
        e.preventDefault();
        const payload = {
            studio_name: document.getElementById('studioNameInput').value,
            studio_address: document.getElementById('studioAddressInput').value,
            studio_lat: document.getElementById('studioLatInput').value,
            studio_lng: document.getElementById('studioLngInput').value,
            radius_meters: document.getElementById('studioRadiusInput').value,
            late_tolerance_minutes: document.getElementById('studioLateTolInput').value,
            work_start_time: document.getElementById('studioStartTimeInput').value,
            work_end_time: document.getElementById('studioEndTimeInput').value
        };

        const res = await fetch('/api/hris/admin/settings', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify(payload)
        });
        const json = await res.json();
        if (json.success) {
            alert('✅ Pengaturan studio berhasil disimpan ke database!');
            loadInitialData();
        }
    }

    // 14. Admin Payroll
    function calculatePayrollTotal() {
        const base = parseFloat(document.getElementById('payBaseInput').value || 0);
        const allowance = parseFloat(document.getElementById('payAllowanceInput').value || 0);
        const ot = parseFloat(document.getElementById('payOtInput').value || 0);
        const bonus = parseFloat(document.getElementById('payBonusInput').value || 0);
        const late = parseFloat(document.getElementById('payLateDeductInput').value || 0);
        const other = parseFloat(document.getElementById('payOtherDeductInput').value || 0);

        const total = Math.max(0, base + allowance + ot + bonus - late - other);
        document.getElementById('payCalculatedNetText').innerText = 'Rp ' + total.toLocaleString('id-ID');
    }

    function loadAdminPayrollForEmployee() {
        const empId = document.getElementById('admPayrollEmpSelect').value;
        const emp = state.employees.find(e => e.id == empId);
        if (emp) {
            document.getElementById('payBaseInput').value = emp.base_salary || 4500000;
            document.getElementById('payAllowanceInput').value = emp.allowance || 500000;
            calculatePayrollTotal();
        }
    }

    async function saveAdminPayroll(e) {
        e.preventDefault();
        const payload = {
            employee_id: document.getElementById('admPayrollEmpSelect').value,
            period_month: document.getElementById('admPayrollMonth').value,
            base_salary: document.getElementById('payBaseInput').value,
            allowance: document.getElementById('payAllowanceInput').value,
            overtime_pay: document.getElementById('payOtInput').value,
            bonus: document.getElementById('payBonusInput').value,
            late_deductions: document.getElementById('payLateDeductInput').value,
            deductions: document.getElementById('payOtherDeductInput').value
        };

        const res = await fetch('/api/hris/admin/payroll/save', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify(payload)
        });
        const json = await res.json();
        if (json.success) {
            alert(`✅ ${json.message}`);
        }
    }

    async function generateAllPayrollsMonthly() {
        const month = document.getElementById('admPayrollMonth').value;
        if (!confirm(`Hitung dan terbitkan slip gaji otomatis untuk seluruh karyawan periode ${month}?`)) return;

        const res = await fetch('/api/hris/admin/payrolls/generate', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ month: month })
        });
        const json = await res.json();
        if (json.success) {
            alert(`✅ ${json.message}`);
        }
    }

    // 15. Admin Rekap PDF
    function openPrintRecap() {
        const month = document.getElementById('rekapMonthInput').value;
        const div = document.getElementById('rekapDivSelect').value;
        window.open(`/hris/recap/print?month=${month}&division=${div}`, '_blank');
    }

    function closeModal(id) {
        document.getElementById(id).classList.remove('active');
    }
</script>

</body>
</html>
