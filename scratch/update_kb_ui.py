import re

with open('resources/views/knowledge.blade.php', 'r', encoding='utf-8') as f:
    content = f.read()

# 1. Add CSS for empty state and animation
if '.file-empty-block' not in content:
    css_addition = """
        .file-empty-block { border: 1px dashed var(--border-color); border-radius: 8px; padding: 32px 16px; text-align: center; color: var(--text-muted); font-size: 13px; background: white; margin-top: 16px; }
        @keyframes progress-animation { 0% { transform: translateX(-100%); } 100% { transform: translateX(100%); } }
"""
    content = content.replace('/* Floating Widgets */', css_addition + '        /* Floating Widgets */')

# 2. Update the Upload Block UI
old_upload_block = """                    <input type="file" id="file_upload_input" accept=".pdf,.jpg,.jpeg,.png,.webp" style="display: none;">
                    <button class="btn btn-primary" id="btn-upload-file"><i class="fa-solid fa-upload"></i> Tambah / Ganti File</button>
                    
                    <div class="file-upload-block" id="file_info_block" style="{{ isset($file) ? '' : 'display:none;' }}">"""

new_upload_block = """                    <input type="file" id="file_upload_input" accept=".pdf,.jpg,.jpeg,.png,.webp" style="display: none;">
                    <button class="btn btn-primary" id="btn-upload-file" style="background-color: #9282f1; border: none; padding: 12px 24px;"><i class="fa-solid fa-upload"></i> Tambah / Ganti File</button>
                    
                    <div id="ai-extraction-progress" style="display: none; margin-top: 24px; margin-bottom: 8px;">
                        <div style="font-size: 13px; color: var(--primary); margin-bottom: 8px;"><i class="fa-solid fa-wand-magic-sparkles"></i> AI sedang mengekstrak teks...</div>
                        <div style="width: 100%; background-color: #f8f9fa; height: 6px; border-radius: 4px; overflow: hidden; position: relative;">
                            <div style="position: absolute; top: 0; left: 0; width: 60%; height: 100%; background-color: #9282f1; animation: progress-animation 1.5s infinite linear; border-radius: 4px;"></div>
                        </div>
                    </div>
                    
                    <div class="file-empty-block" id="file_empty_block" style="{{ isset($file) ? 'display:none;' : '' }}">
                        Belum ada file media price list yang diunggah.
                    </div>
                    
                    <div class="file-upload-block" id="file_info_block" style="{{ isset($file) ? '' : 'display:none;' }}">"""
                    
content = content.replace(old_upload_block, new_upload_block)

# 3. Update Rincian Teks placeholder state
old_text_placeholder = """                <textarea id="extracted_text_input" class="form-control" style="background: #f9f9fa; border: 1px solid #e1e1e4;">{{ $file->extracted_text ?? '' }}</textarea>"""

new_text_placeholder = """                <textarea id="extracted_text_input" class="form-control" style="background: #f9f9fa; border: 1px solid #e1e1e4;" placeholder="Unggah file media terlebih dahulu untuk mengisi rincian teksnya.">{{ $file->extracted_text ?? '' }}</textarea>"""

content = content.replace(old_text_placeholder, new_text_placeholder)


# 4. Update JS Logic to toggle states
js_logic_old = """                let btn = $('#btn-upload-file');
                let originalText = btn.html();
                btn.html('<i class="fa-solid fa-spinner fa-spin"></i> Mengupload...').prop('disabled', true);

                $.ajax({
                    url: '/knowledge/upload',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        btn.html(originalText).prop('disabled', false);
                        if (res.success) {
                            showToast(res.message);
                            
                            // Update UI
                            $('#file_info_block').show();"""

js_logic_new = """                let btn = $('#btn-upload-file');
                btn.prop('disabled', true).css('opacity', '0.7');
                $('#file_empty_block').hide();
                $('#file_info_block').hide();
                $('#ai-extraction-progress').show();

                $.ajax({
                    url: '/knowledge/upload',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        btn.prop('disabled', false).css('opacity', '1');
                        $('#ai-extraction-progress').hide();
                        
                        if (res.success) {
                            showToast(res.message);
                            
                            // Update UI
                            $('#file_info_block').show();"""

content = content.replace(js_logic_old, js_logic_new)

js_delete_old = """                $.post('/knowledge/delete', function(res) {
                    if (res.success) {
                        showToast(res.message);
                        $('#file_info_block').hide();"""
                        
js_delete_new = """                $.post('/knowledge/delete', function(res) {
                    if (res.success) {
                        showToast(res.message);
                        $('#file_info_block').hide();
                        $('#file_empty_block').show();"""
                        
content = content.replace(js_delete_old, js_delete_new)


with open('resources/views/knowledge.blade.php', 'w', encoding='utf-8') as f:
    f.write(content)

print("UI states and animations updated.")
