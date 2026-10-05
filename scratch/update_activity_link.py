import os
import glob

files = glob.glob(r'd:\FREELANCE\closemateai\resources\views\*.blade.php')

target = '<a class="nav-item"><i class="fa-solid fa-clock-rotate-left"></i> <span>Riwayat Aktivitas</span></a>'
replacement = '<a href="/activity" class="nav-item {{ request()->is(\'activity\') ? \'active\' : \'\' }}"><i class="fa-solid fa-clock-rotate-left"></i> <span>Riwayat Aktivitas</span></a>'

for file in files:
    with open(file, 'r', encoding='utf-8') as f:
        content = f.read()
    
    if target in content:
        content = content.replace(target, replacement)
        with open(file, 'w', encoding='utf-8') as f:
            f.write(content)
        print(f"Updated {file}")
    else:
        print(f"Target not found in {file}")
