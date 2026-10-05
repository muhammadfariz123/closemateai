import glob
import os
import re

php_code = r"""
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
"""

files = glob.glob('resources/views/*.blade.php')

for file in files:
    if os.path.basename(file) == 'welcome.blade.php':
        continue
    
    with open(file, 'r', encoding='utf-8') as f:
        content = f.read()
    
    pattern1 = r'<div class="status-pill">\s*<div class="status-dot"></div>\s*WhatsApp Connected\s*</div>'
    
    match = re.search(pattern1, content)
    if match:
        new_content = content[:match.start()] + php_code.strip('\n') + content[match.end():]
        with open(file, 'w', encoding='utf-8') as f:
            f.write(new_content)
        print(f"Updated {file}")
    else:
        print(f"Skipped {file}")

print("Done")
