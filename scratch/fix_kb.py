import re

with open('resources/views/knowledge.blade.php', 'r', encoding='utf-8') as f:
    content = f.read()

# Fix toast container closure
content = content.replace('<div class="toast-container" id="toast-container">    <script', '<div class="toast-container" id="toast-container"></div>\n    <script')

# Fix duplicate body and html at the end
bad_end = """</body>
</html>cript src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</body>
</html>"""
good_end = "</body>\n</html>"
content = content.replace(bad_end, good_end)

with open('resources/views/knowledge.blade.php', 'w', encoding='utf-8') as f:
    f.write(content)

print("Fixed HTML.")
