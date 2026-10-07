import re

with open('resources/views/settings.blade.php', 'r', encoding='utf-8') as f:
    content = f.read()

# 1. Swap the two cards
# Let's find the left column
col_left_start = content.find('<!-- Left Column -->')
# Find the start of Onboarding WhatsApp Card
onboarding_start = content.find('<!-- Onboarding WhatsApp Card -->', col_left_start)
# Find the start of Business Profile Card
business_start = content.find('<!-- Business Profile Card -->', onboarding_start)
# Find the end of Business Profile Card
business_end = content.find('<!-- Right Column -->', business_start)
# Let's be more precise. Find the closing div of the Business Profile Card
business_end = content.rfind('</div>', business_start, content.find('<!-- Right Column -->'))

onboarding_code = content[onboarding_start:business_start]
# We need to find the exact end of business profile code. 
# It ends with </div>\n\n            </div>\n            \n            <!-- Right Column -->
business_code = content[business_start:business_end].strip()

# Now swap them
# wait, business_end actually points to the </div> of col-left. Let's find the closing </div> of business_code exactly.
# It ends at: <button class="btn btn-primary"><i class="fa-regular fa-floppy-disk"></i> Save Profile</button>\n                </div>
business_end_match = re.search(r'<button class="btn btn-primary">.*?Save Profile</button>\s*</div>', content)
if business_end_match:
    business_end = business_end_match.end()
    business_code = content[business_start:business_end]
    
    # We replace the col_left content
    before = content[:onboarding_start]
    after = content[business_end:]
    
    new_content = before + business_code + "\n\n                " + onboarding_code.strip() + "\n\n            " + after
    content = new_content
    print("Cards swapped.")

# 2. Add IDs and values to Business Profile
form_replacements = [
    (r'<input type="text" class="form-control" value="Penapict">',
     r'<input type="text" id="business_name" class="form-control" value="{{ $user->business_name ?? \'\' }}">'),
    
    (r'<label class="form-label">WhatsApp Number</label>\s*<input type="text" class="form-control">',
     r'<label class="form-label">WhatsApp Number</label>\n                            <input type="text" id="business_wa_number" class="form-control" value="{{ $user->business_wa_number ?? \'\' }}">'),
    
    (r'<label class="form-label">Owner Name</label>\s*<input type="text" class="form-control">',
     r'<label class="form-label">Owner Name</label>\n                            <input type="text" id="owner_name" class="form-control" value="{{ $user->name ?? \'\' }}">'),
    
    (r'<input type="email" class="form-control" value="hotautomag@gmail.com">',
     r'<input type="email" id="business_email" class="form-control" value="{{ $user->email ?? \'\' }}">'),
    
    (r'<select class="form-control">',
     r'<select id="business_category" class="form-control">'),
    
    (r'<option>Fotografi / Videografi</option>',
     r'<option {{ ($user && $user->category == "Fotografi / Videografi") ? "selected" : "" }}>Fotografi / Videografi</option>'),
    
    (r'<option>Makeup Artist</option>',
     r'<option {{ ($user && $user->category == "Makeup Artist") ? "selected" : "" }}>Makeup Artist</option>'),
    
    (r'<option>Dekorasi</option>',
     r'<option {{ ($user && $user->category == "Dekorasi") ? "selected" : "" }}>Dekorasi</option>'),
    
    (r'<textarea class="form-control" rows="4" placeholder="Ceritakan singkat tentang bisnis kamu..."></textarea>',
     r'<textarea id="business_description" class="form-control" rows="4" placeholder="Ceritakan singkat tentang bisnis kamu...">{{ $user->business_description ?? \'\' }}</textarea>'),
     
    (r'<button class="btn btn-primary"><i class="fa-regular fa-floppy-disk"></i> Save Profile</button>',
     r'<button id="btn-save-profile" class="btn btn-primary"><i class="fa-regular fa-floppy-disk"></i> Save Profile</button>')
]

for old, new in form_replacements:
    content = re.sub(old, new, content)

# 3. Add Javascript
js_code = r"""
            $('#btn-save-profile').click(function() {
                let btn = $(this);
                let originalText = btn.html();
                btn.html('<i class="fa-solid fa-spinner fa-spin"></i> Menyimpan...').prop('disabled', true);

                let data = {
                    business_name: $('#business_name').val(),
                    business_wa_number: $('#business_wa_number').val(),
                    name: $('#owner_name').val(),
                    email: $('#business_email').val(),
                    category: $('#business_category').val(),
                    business_description: $('#business_description').val()
                };

                $.post('/settings/business-profile', data, function(res) {
                    btn.html(originalText).prop('disabled', false);
                    if (res.success) {
                        $('#toast-message').html('<i class="fa-solid fa-check-circle" style="color:var(--success);"></i> Profil Bisnis tersimpan');
                        $('#toast-box').css('display', 'flex').hide().fadeIn().delay(3000).fadeOut();
                        
                        // Update UI names if changed
                        if (data.business_name) {
                            $('.sidebar-title h2').text(data.business_name);
                        }
                    }
                }).fail(function() {
                    btn.html(originalText).prop('disabled', false);
                    alert('Gagal menyimpan profil bisnis.');
                });
            });
"""

if '#btn-save-profile' not in content:
    # Insert JS before }); </script>
    insert_pos = content.rfind('});\n    </script>')
    if insert_pos != -1:
        content = content[:insert_pos] + js_code + "\n        " + content[insert_pos:]
        print("JS added.")

with open('resources/views/settings.blade.php', 'w', encoding='utf-8') as f:
    f.write(content)

print("Done")
