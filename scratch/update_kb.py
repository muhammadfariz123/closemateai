import re

with open('resources/views/knowledge.blade.php', 'r', encoding='utf-8') as f:
    content = f.read()

# 1. Update the Upload File Section
old_upload_section = """                    <div style="font-weight: 500; font-size: 14px; margin-bottom: 4px;">Upload File Price List (Sumber Teks AI)</div>
                    <div class="kb-card-desc" style="margin-bottom: 12px;">Format: PDF, JPG, PNG, WebP · Maks. 10 MB per file. File ini tidak dikirim ke klien, hanya dikonversi jadi teks rincian.</div>
                    
                    <button class="btn btn-primary"><i class="fa-solid fa-upload"></i> Tambah / Ganti File</button>
                    
                    <div class="file-upload-block">
                        <div class="file-info">
                            <i class="fa-solid fa-file-pdf file-icon"></i>
                            <div>
                                <div class="file-name">BUNDLING WEDDING PRICE GUIDE 2026 PENAPICT</div>
                                <div class="file-meta">BUNDLING WEDDING PRICE GUIDE 2026 PENAPICT.pdf · 2.99 MB · Dokumen · 2/10/2026</div>
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            <button class="btn btn-secondary" style="padding: 8px 12px; font-size: 12px;"><i class="fa-regular fa-eye"></i> Preview</button>
                            <button class="btn btn-danger-outline" style="padding: 8px 12px;"><i class="fa-regular fa-trash-can"></i></button>
                        </div>
                    </div>"""

new_upload_section = """                    <div style="font-weight: 500; font-size: 14px; margin-bottom: 4px;">Upload File Price List (Sumber Teks AI)</div>
                    <div class="kb-card-desc" style="margin-bottom: 12px;">Format: PDF, JPG, PNG, WebP · Maks. 10 MB per file. File ini tidak dikirim ke klien, hanya dikonversi jadi teks rincian.</div>
                    
                    <input type="file" id="file_upload_input" accept=".pdf,.jpg,.jpeg,.png,.webp" style="display: none;">
                    <button class="btn btn-primary" id="btn-upload-file"><i class="fa-solid fa-upload"></i> Tambah / Ganti File</button>
                    
                    <div class="file-upload-block" id="file_info_block" style="{{ isset($file) ? '' : 'display:none;' }}">
                        <div class="file-info">
                            <i class="fa-solid fa-file-pdf file-icon"></i>
                            <div>
                                <div class="file-name" id="display_file_name">{{ $file->file_name ?? '' }}</div>
                                <div class="file-meta" id="display_file_meta">{{ $file->file_name ?? '' }} · {{ $file->file_size ?? '' }} · {{ isset($file) ? $file->created_at->format('j/n/Y') : '' }}</div>
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            <a id="btn_preview_file" href="{{ isset($file) ? asset('storage/' . $file->file_path) : '#' }}" target="_blank" class="btn btn-secondary" style="padding: 8px 12px; font-size: 12px; text-decoration: none;"><i class="fa-regular fa-eye"></i> Preview</a>
                            <button id="btn_delete_file" class="btn btn-danger-outline" style="padding: 8px 12px;"><i class="fa-regular fa-trash-can"></i></button>
                        </div>
                    </div>"""
                    
content = content.replace(old_upload_section, new_upload_section)

# 2. Update Textarea Section
old_textarea_section = """                <div style="font-size: 13px; margin-bottom: 8px; font-weight: 500;">Rincian untuk file: BUNDLING WEDDING PRICE GUIDE 2026 PENAPICT</div>
                
                <textarea class="form-control" style="background: #f9f9fa; border: 1px solid #e1e1e4;"># WEDDING PRICE GUIDE - PENAPICT"""

# We need to replace the whole textarea block. It's quite long, so let's do a regex replacement.
# The textarea ends at <button class="btn btn-primary">Simpan Rincian Teks</button>
# Wait, let's find the exact end.

import re
# Find the start of kb-card for Rincian Teks
rincian_start = content.find('<div class="kb-card-title">Rincian Teks Price List (Pengetahuan Internal AI)</div>')
# Find the end of this kb-card
rincian_end = content.find('</div>', content.find('<button class="btn btn-primary">', rincian_start))
# Actually, let's just replace the textarea and the buttons directly
old_textarea_full = re.search(r'<textarea class="form-control".*?</textarea>', content, re.DOTALL).group(0)

new_textarea_full = r"""                <textarea id="extracted_text_input" class="form-control" style="background: #f9f9fa; border: 1px solid #e1e1e4;">{{ $file->extracted_text ?? '' }}</textarea>"""

content = content.replace(old_textarea_full, new_textarea_full)

# Add IDs to buttons
content = content.replace('<button class="btn btn-secondary"><i class="fa-solid fa-rotate"></i> Coba Ekstraksi Lagi</button>',
                          '<button id="btn_extract_again" class="btn btn-secondary"><i class="fa-solid fa-rotate"></i> Coba Ekstraksi Lagi</button>')
content = content.replace('<button class="btn btn-primary"><i class="fa-regular fa-floppy-disk"></i> Simpan Rincian Teks</button>',
                          '<button id="btn_save_text" class="btn btn-primary"><i class="fa-regular fa-floppy-disk"></i> Simpan Rincian Teks</button>')
                          
# Make the filename dynamic
content = content.replace('Rincian untuk file: BUNDLING WEDDING PRICE GUIDE 2026 PENAPICT', 'Rincian untuk file: <span id="text_file_name">{{ $file->file_name ?? \'Belum ada file\' }}</span>')


# 3. Add Javascript
js_logic = """
        // Knowledge Base Logic
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });

            // Upload File
            $('#btn-upload-file').click(function() {
                $('#file_upload_input').click();
            });

            $('#file_upload_input').change(function() {
                let file = this.files[0];
                if (!file) return;

                let formData = new FormData();
                formData.append('file', file);

                let btn = $('#btn-upload-file');
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
                            $('#file_info_block').show();
                            $('#display_file_name').text(res.data.file_name);
                            $('#display_file_meta').text(res.data.file_name + ' · ' + res.data.file_size + ' · ' + res.data.created_at);
                            $('#btn_preview_file').attr('href', res.data.file_path);
                            
                            $('#text_file_name').text(res.data.file_name);
                            $('#extracted_text_input').val(res.data.extracted_text);
                        } else {
                            alert(res.message);
                        }
                    },
                    error: function() {
                        btn.html(originalText).prop('disabled', false);
                        alert('Gagal mengupload file.');
                    }
                });
            });

            // Delete File
            $('#btn_delete_file').click(function() {
                if(!confirm('Yakin ingin menghapus file ini?')) return;
                
                $.post('/knowledge/delete', function(res) {
                    if (res.success) {
                        showToast(res.message);
                        $('#file_info_block').hide();
                        $('#text_file_name').text('Belum ada file');
                        $('#extracted_text_input').val('');
                        $('#file_upload_input').val('');
                    }
                });
            });

            // Save Text
            $('#btn_save_text').click(function() {
                let btn = $(this);
                let originalText = btn.html();
                btn.html('<i class="fa-solid fa-spinner fa-spin"></i> Menyimpan...').prop('disabled', true);

                let data = {
                    extracted_text: $('#extracted_text_input').val()
                };

                $.post('/knowledge/update-text', data, function(res) {
                    btn.html(originalText).prop('disabled', false);
                    if (res.success) {
                        showToast(res.message);
                    }
                }).fail(function() {
                    btn.html(originalText).prop('disabled', false);
                    alert('Gagal menyimpan teks.');
                });
            });
            
            // Extract Again
            $('#btn_extract_again').click(function() {
                showToast('Fitur ekstraksi ulang sedang dikembangkan.');
            });
        });
"""

# Insert js_logic before the function loadPrompt
insert_pos = content.find('function loadPrompt(type, element) {')
content = content[:insert_pos] + js_logic + "\n        " + content[insert_pos:]

# Wait, add csrf meta tag at the top if it doesn't exist
if 'name="csrf-token"' not in content:
    meta_tag = '\n    <meta name="csrf-token" content="{{ csrf_token() }}">'
    content = content.replace('<title>', meta_tag + '\n    <title>')

# Make sure jQuery is included
if 'code.jquery.com/jquery' not in content:
    jquery_tag = '\n    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>'
    content = content.replace('</body>', jquery_tag + '\n</body>')

with open('resources/views/knowledge.blade.php', 'w', encoding='utf-8') as f:
    f.write(content)

print("Knowledge file updated.")
