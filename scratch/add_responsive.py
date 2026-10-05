import glob
import os

css_to_add = """
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
"""

files = glob.glob('resources/views/*.blade.php')

for file in files:
    if os.path.basename(file) in ['welcome.blade.php', 'settings.blade.php']:
        continue
    
    with open(file, 'r', encoding='utf-8') as f:
        content = f.read()
    
    if "/* Responsive Mobile Layout */" in content:
        continue # Already added
        
    new_content = content.replace("</style>", css_to_add + "    </style>")
    
    if new_content != content:
        with open(file, 'w', encoding='utf-8') as f:
            f.write(new_content)
        print(f"Updated {file}")

print("Done")
