import os
import glob
import re

blade_files = glob.glob('resources/views/*.blade.php')

for file in blade_files:
    if file.endswith('welcome.blade.php') or file.endswith('progress.blade.php') or file.endswith('public_quotation.blade.php'):
        continue
        
    with open(file, 'r', encoding='utf-8') as f:
        content = f.read()

    # 1. Update --sidebar-text
    content = content.replace('--sidebar-text: #a1a5b7;', '--sidebar-text: #cbd5e1;')
    
    # 2. Add responsive CSS link
    if '/css/responsive.css' not in content:
        content = content.replace('</head>', '    <link rel="stylesheet" href="/css/responsive.css">\n</head>')

    # 3. Add responsive JS script
    if '/js/responsive.js' not in content:
        content = content.replace('</body>', '    <script src="/js/responsive.js"></script>\n</body>')

    # 4. Add hamburger menu button to .topbar
    if '<button class="mobile-menu-btn"' not in content:
        # Prepend the hamburger button to the page-title
        content = content.replace('<div class="page-title">', '<button class="mobile-menu-btn" style="display:none; background:none; border:none; font-size:20px; cursor:pointer; margin-right:16px; color:var(--text-dark);"><i class="fa-solid fa-bars"></i></button>\n            <div class="page-title">')
        
    with open(file, 'w', encoding='utf-8') as f:
        f.write(content)

print(f"Updated {len(blade_files)} blade files.")
