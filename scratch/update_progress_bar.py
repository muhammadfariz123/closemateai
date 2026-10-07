import re

with open('resources/views/knowledge.blade.php', 'r', encoding='utf-8') as f:
    content = f.read()

# Update the Progress Bar HTML
old_progress_html = """                    <div id="ai-extraction-progress" style="display: none; margin-top: 24px; margin-bottom: 8px;">
                        <div style="font-size: 13px; color: var(--primary); margin-bottom: 8px;"><i class="fa-solid fa-wand-magic-sparkles"></i> AI sedang mengekstrak teks...</div>
                        <div style="width: 100%; background-color: #f8f9fa; height: 6px; border-radius: 4px; overflow: hidden; position: relative;">
                            <div style="position: absolute; top: 0; left: 0; width: 60%; height: 100%; background-color: #9282f1; animation: progress-animation 1.5s infinite linear; border-radius: 4px;"></div>
                        </div>
                    </div>"""

new_progress_html = """                    <div id="ai-extraction-progress" style="display: none; margin-top: 24px; margin-bottom: 8px;">
                        <div style="font-size: 13px; color: var(--primary); margin-bottom: 8px;"><i class="fa-solid fa-wand-magic-sparkles"></i> AI sedang mengekstrak teks...</div>
                        <div style="width: 100%; background-color: #f1f1f4; height: 8px; border-radius: 4px; overflow: hidden; position: relative;">
                            <div id="progress-bar-fill" style="width: 0%; height: 100%; background-color: var(--primary); border-radius: 4px; transition: width 0.3s ease;"></div>
                        </div>
                    </div>"""

content = content.replace(old_progress_html, new_progress_html)


# Update JS Upload Logic
old_js_logic = """                let btn = $('#btn-upload-file');
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
                        
                        if (res.success) {"""

new_js_logic = """                let btn = $('#btn-upload-file');
                btn.prop('disabled', true).css('opacity', '0.7');
                $('#file_empty_block').hide();
                $('#file_info_block').hide();
                
                $('#ai-extraction-progress').show();
                $('#progress-bar-fill').css('width', '0%');
                $('#progress-bar-fill').css('transition', 'width 0.5s ease');
                
                let progress = 0;
                let progressInterval = setInterval(() => {
                    if (progress < 90) {
                        progress += Math.random() * 5 + 2;
                        if(progress > 90) progress = 90;
                        $('#progress-bar-fill').css('width', progress + '%');
                    }
                }, 400);

                $.ajax({
                    url: '/knowledge/upload',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        clearInterval(progressInterval);
                        $('#progress-bar-fill').css('width', '100%');
                        
                        setTimeout(() => {
                            btn.prop('disabled', false).css('opacity', '1');
                            $('#ai-extraction-progress').hide();
                            
                            if (res.success) {"""

content = content.replace(old_js_logic, new_js_logic)

# We also need to fix the closing braces for the setTimeout
# Let's just find the closing part
old_js_close = """                        } else {
                            alert(res.message);
                        }
                    },
                    error: function() {
                        btn.prop('disabled', false).css('opacity', '1');
                        $('#ai-extraction-progress').hide();
                        alert('Gagal mengupload file.');
                    }
                });"""
                
new_js_close = """                        } else {
                                alert(res.message);
                            }
                        }, 500); // Wait for progress bar animation
                    },
                    error: function() {
                        clearInterval(progressInterval);
                        btn.prop('disabled', false).css('opacity', '1');
                        $('#ai-extraction-progress').hide();
                        alert('Gagal mengupload file.');
                    }
                });"""
                
content = content.replace(old_js_close, new_js_close)

with open('resources/views/knowledge.blade.php', 'w', encoding='utf-8') as f:
    f.write(content)

print("Progress bar and JS logic updated.")
