import glob
import os
import re

files = glob.glob('resources/views/*.blade.php')

for file in files:
    if os.path.basename(file) == 'welcome.blade.php':
        continue
    
    with open(file, 'r', encoding='utf-8') as f:
        content = f.read()
    
    # We want to replace `.nav-menu::-webkit-scrollbar { display: none; }`
    # with styled scrollbar CSS
    
    old_css = ".nav-menu::-webkit-scrollbar { display: none; }"
    new_css = """
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
"""
    
    if old_css in content:
        new_content = content.replace(old_css, new_css.strip("\n"))
        with open(file, 'w', encoding='utf-8') as f:
            f.write(new_content)
        print(f"Updated {file}")
    else:
        print(f"Not found in {file}")

print("Done")
