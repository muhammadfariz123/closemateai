import re

with open('resources/views/chat.blade.php', 'r', encoding='utf-8') as f:
    content = f.read()

# Replace chat-list dummy data
new_chat_list = '<div class="chat-list" id="chatListContainer">\n<div style="padding: 20px; text-align: center; color: #999; font-size: 13px;">Loading...</div>\n</div>'
content = re.sub(r'<div class="chat-list">.*?</div>\n            </div>\n\n            <!-- Middle Panel', new_chat_list + '\n            </div>\n\n            <!-- Middle Panel', content, flags=re.DOTALL)

# Replace chat-messages dummy data
new_chat_messages = '<div class="chat-messages" id="chatMessagesBox">\n<div style="padding: 20px; text-align: center; color: #999; font-size: 13px;">Pilih chat untuk melihat pesan</div>\n</div>'
content = re.sub(r'<div class="chat-messages" id="chatMessagesBox">.*?</div>\n                \n                <div class="chat-input-area"', new_chat_messages + '\n                \n                <div class="chat-input-area"', content, flags=re.DOTALL)

# Add csrf token meta tag if not exists
if '<meta name="csrf-token"' not in content:
    content = content.replace('<head>', '<head>\n    <meta name="csrf-token" content="{{ csrf_token() }}">')

# Add JS logic
js_logic = """
    <script>
        let activeChatId = null;
        let chatsData = [];

        async function fetchChats() {
            try {
                let res = await fetch('/api/chats');
                let chats = await res.json();
                chatsData = chats;
                renderChatList(chats);
                if(activeChatId) {
                    // Update active chat silently if new messages arrived
                    let stillExists = chats.find(c => c.id === activeChatId);
                    if(stillExists && stillExists.messages.length > 0) {
                        // We will just re-fetch the active chat
                        fetchActiveChatMessages(true);
                    }
                }
            } catch (e) { console.error(e); }
        }

        function renderChatList(chats) {
            const container = document.getElementById('chatListContainer');
            if (chats.length === 0) {
                container.innerHTML = '<div style="padding: 20px; text-align: center; color: #999; font-size: 13px;">Belum ada chat</div>';
                return;
            }
            
            let html = '';
            chats.forEach(chat => {
                let lastMsg = chat.messages && chat.messages.length > 0 ? chat.messages[0].message : 'Tidak ada pesan';
                let time = chat.updated_at ? new Date(chat.updated_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) : '';
                let isActive = chat.id === activeChatId ? 'active' : '';
                let tagColor = chat.is_human_takeover ? 'tag-danger' : 'tag-primary';
                let tagText = chat.is_human_takeover ? 'HUMAN TAKEOVER' : 'AI Active';
                let name = chat.client_name || chat.client_wa_number;

                html += `
                    <div class="chat-item ${isActive}" onclick="openChat(${chat.id})">
                        <div class="chat-item-header">
                            <div class="chat-item-title">${name}</div>
                            <div class="chat-item-time">${time}</div>
                        </div>
                        <div class="chat-item-subtitle">${chat.client_wa_number}</div>
                        <div class="chat-item-msg">${lastMsg.substring(0, 50)}${lastMsg.length > 50 ? '...' : ''}</div>
                        <div class="chat-item-tags">
                            <div class="tag ${tagColor}">${tagText}</div>
                            <div class="tag tag-light">${chat.status}</div>
                        </div>
                    </div>
                `;
            });
            container.innerHTML = html;
        }

        async function openChat(id) {
            activeChatId = id;
            renderChatList(chatsData); // update active class
            await fetchActiveChatMessages(false);
        }

        async function fetchActiveChatMessages(silent = false) {
            if(!activeChatId) return;
            try {
                let res = await fetch(`/api/chats/${activeChatId}`);
                let data = await res.json();
                
                // Update Header
                let name = data.chat.client_name || data.chat.client_wa_number;
                document.querySelector('.chat-header-info h3').innerText = name;
                document.querySelector('.chat-header-avatar').innerText = name.substring(0, 2).toUpperCase();
                
                // Update details panel
                document.querySelector('.client-name h3').innerText = name;
                document.querySelector('.client-name p').innerText = `WA Name: ${name} | ${data.chat.client_wa_number}`;
                document.querySelectorAll('.info-row .info-content p')[1].innerText = data.chat.client_wa_number;
                
                // Update Takeover UI
                const toggle = document.getElementById('humanTakeoverToggle');
                if(toggle && !silent) {
                    toggle.checked = data.chat.is_human_takeover == 1;
                    updateTakeoverUI(data.chat.is_human_takeover == 1, false);
                }

                // Render Messages
                const msgBox = document.getElementById('chatMessagesBox');
                let html = '';
                data.messages.forEach(msg => {
                    let isRight = msg.sender === 'admin' || msg.sender === 'ai';
                    let time = new Date(msg.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
                    let badge = msg.sender === 'ai' ? '<div class="ai-badge">AI</div>' : '';
                    let bgColor = msg.sender === 'admin' ? '#4a3da8' : 'var(--primary)'; // slightly different for human
                    
                    if (isRight) {
                        html += `
                            <div class="bubble-wrapper right">
                                <div class="bubble bubble-right" style="background: ${bgColor};">
                                    ${badge}
                                    ${msg.message.replace(/\\n/g, '<br>')}
                                </div>
                                <div class="bubble-time right">${time}</div>
                            </div>
                        `;
                    } else {
                        html += `
                            <div class="bubble-wrapper left">
                                <div class="bubble bubble-left">
                                    ${msg.message.replace(/\\n/g, '<br>')}
                                </div>
                                <div class="bubble-time">${time}</div>
                            </div>
                        `;
                    }
                });
                msgBox.innerHTML = html;
                
                if(!silent) {
                    msgBox.scrollTop = msgBox.scrollHeight;
                } else {
                    // Only scroll down if already near bottom
                    if (msgBox.scrollHeight - msgBox.scrollTop - msgBox.clientHeight < 100) {
                        msgBox.scrollTop = msgBox.scrollHeight;
                    }
                }
            } catch(e) { console.error(e); }
        }

        async function toggleHumanTakeover() {
            if(!activeChatId) return;
            const toggle = document.getElementById('humanTakeoverToggle');
            const isTakeover = toggle.checked;
            
            try {
                let res = await fetch(`/api/chats/${activeChatId}/takeover`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                    body: JSON.stringify({ is_takeover: isTakeover })
                });
                let data = await res.json();
                if(data.success) {
                    updateTakeoverUI(isTakeover, true);
                    fetchChats(); // refresh list
                }
            } catch(e) { console.error(e); }
        }

        async function sendAdminMessage() {
            if(!activeChatId) return;
            const input = document.getElementById('chatInputMessage');
            const text = input.value.trim();
            if(!text) return;
            
            input.value = '';
            input.disabled = true;
            
            // Optimistic UI
            const msgBox = document.getElementById('chatMessagesBox');
            let time = new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
            msgBox.innerHTML += `
                <div class="bubble-wrapper right" style="opacity: 0.7;">
                    <div class="bubble bubble-right" style="background: #4a3da8;">
                        ${text.replace(/\\n/g, '<br>')}
                    </div>
                    <div class="bubble-time right">${time} (Sending...)</div>
                </div>
            `;
            msgBox.scrollTop = msgBox.scrollHeight;
            
            try {
                let res = await fetch(`/api/chats/${activeChatId}/send`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                    body: JSON.stringify({ message: text })
                });
                await fetchActiveChatMessages(false);
                fetchChats();
            } catch(e) { 
                console.error(e); 
                showToast('Gagal mengirim pesan', 'fa-times-circle');
            } finally {
                input.disabled = false;
                input.focus();
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            fetchChats();
            setInterval(() => {
                fetchChats();
            }, 5000); // refresh every 5s
            
            const btnSend = document.querySelector('.btn-send');
            const input = document.getElementById('chatInputMessage');
            if(btnSend && input) {
                btnSend.onclick = sendAdminMessage;
                input.addEventListener('keypress', function (e) {
                    if (e.key === 'Enter' && !e.shiftKey) {
                        e.preventDefault();
                        sendAdminMessage();
                    }
                });
            }
        });
    """

content = re.sub(r'<script>.*?</script>', js_logic + '\n    <script>\n        function updateTakeoverUI(isTakeover, showToastAlert = false) {\n            const banner = document.getElementById(\'chatBanner\');\n            const tag = document.getElementById(\'tagTakeoverStatus\');\n            const label = document.getElementById(\'takeoverText\');\n            if(!banner || !tag || !label) return;\n            if(isTakeover) {\n                banner.style.display = \'flex\';\n                tag.className = \'tag tag-danger\';\n                tag.innerText = \'HUMAN TAKEOVER\';\n                label.style.color = \'var(--primary)\';\n                if(showToastAlert) showToast(\'Mode Human Takeover Aktif. AI dihentikan.\', \'fa-user-shield\');\n            } else {\n                banner.style.display = \'none\';\n                tag.className = \'tag tag-primary\';\n                tag.innerText = \'AI Active\';\n                label.style.color = \'var(--text-muted)\';\n                if(showToastAlert) showToast(\'AI kembali aktif\', \'fa-robot\');\n            }\n        }\n        function showToast(message, iconClass = \'fa-check-circle\') {\n            const container = document.getElementById(\'toast-container\');\n            if(!container) return;\n            const toast = document.createElement(\'div\');\n            toast.className = \'toast\';\n            toast.innerHTML = `<i class="fa-solid ${iconClass}"></i> ${message}`;\n            container.appendChild(toast);\n            setTimeout(() => {\n                toast.style.animation = \'fadeOut 0.3s forwards\';\n                setTimeout(() => toast.remove(), 300);\n            }, 3000);\n        }\n    </script>', content, flags=re.DOTALL)

# Also add id to input element
content = content.replace('<input type="text" placeholder="Tulis pesan untuk klien', '<input type="text" id="chatInputMessage" placeholder="Tulis pesan untuk klien')

with open('resources/views/chat.blade.php', 'w', encoding='utf-8') as f:
    f.write(content)

print("Updated chat.blade.php")
