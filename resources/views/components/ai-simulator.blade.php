<style>
    /* AI Simulator Overlay */
    .ai-simulator-backdrop { position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1040; display: none; opacity: 0; transition: opacity 0.3s ease; }
    .ai-simulator-backdrop.show { display: block; opacity: 1; }
    
    /* AI Simulator Drawer */
    .ai-simulator-drawer { position: fixed; top: 0; right: -400px; bottom: 0; width: 400px; background: white; z-index: 1050; box-shadow: -4px 0 24px rgba(0,0,0,0.1); display: flex; flex-direction: column; transition: right 0.3s ease; }
    .ai-simulator-drawer.show { right: 0; }
    
    .ai-drawer-header { padding: 16px 20px; border-bottom: 1px solid var(--border-color); display: flex; align-items: center; justify-content: space-between; background: #fff; }
    .ai-drawer-title { font-weight: 600; font-size: 16px; color: var(--text-dark); display: flex; align-items: center; gap: 8px; }
    .btn-close-drawer { background: none; border: none; font-size: 18px; color: var(--text-muted); cursor: pointer; padding: 4px; border-radius: 50%; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; transition: 0.2s; }
    .btn-close-drawer:hover { background: #f1f1f4; color: var(--text-dark); }
    
    .ai-drawer-body { flex: 1; display: flex; flex-direction: column; overflow: hidden; background-color: #f9fafb; }
    
    /* Simulator Settings */
    .simulator-settings { padding: 20px; background: white; border-bottom: 1px solid var(--border-color); }
    .simulator-settings label { font-size: 12px; font-weight: 500; color: var(--text-dark); margin-bottom: 6px; display: block; }
    .simulator-stats { display: flex; align-items: center; justify-content: space-between; margin-top: 12px; font-size: 13px; color: var(--text-muted); }
    
    /* Chat Area */
    .simulator-chat-area { flex: 1; overflow-y: auto; padding: 20px; display: flex; flex-direction: column; gap: 16px; scroll-behavior: smooth; }
    .chat-placeholder { text-align: center; color: var(--text-muted); font-size: 13px; margin: auto; padding: 20px; }
    
    .sim-bubble { max-width: 85%; padding: 12px 16px; font-size: 14px; position: relative; word-wrap: break-word; }
    .sim-bubble-user { align-self: flex-end; background: var(--primary); color: white; border-radius: 16px 16px 0 16px; }
    .sim-bubble-ai { align-self: flex-start; background: white; border: 1px solid var(--border-color); color: var(--text-dark); border-radius: 16px 16px 16px 0; box-shadow: 0 2px 8px rgba(0,0,0,0.02); }
    
    .sim-meta { font-size: 11px; color: var(--text-muted); margin-top: 4px; display: flex; align-items: center; gap: 4px; }
    .sim-ai-wrapper { display: flex; flex-direction: column; align-items: flex-start; }
    
    /* Typing Indicator */
    .typing-indicator { display: none; align-items: center; gap: 8px; color: var(--text-muted); font-size: 12px; font-style: italic; align-self: flex-start; padding-left: 8px; }
    .typing-indicator.active { display: flex; }
    .typing-spinner { width: 14px; height: 14px; border: 2px solid #e4e6ef; border-top-color: var(--primary); border-radius: 50%; animation: spin 1s linear infinite; }
    @keyframes spin { to { transform: rotate(360deg); } }
    
    /* Input Area */
    .simulator-input-area { padding: 16px 20px; background: white; border-top: 1px solid var(--border-color); display: flex; gap: 10px; align-items: center; }
    .sim-input { flex: 1; border-radius: 20px; border: 1px solid var(--border-color); padding: 10px 16px; font-size: 14px; outline: none; transition: 0.2s; }
    .sim-input:focus { border-color: var(--primary); }
    .sim-btn-send { background: var(--primary); color: white; border: none; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: 0.2s; flex-shrink: 0; }
    .sim-btn-send:hover { transform: scale(1.05); }
    .sim-btn-send:disabled { background: #cfd2d6; cursor: not-allowed; transform: none; }
    
    @media (max-width: 480px) {
        .ai-simulator-drawer { width: 100%; right: -100%; }
    }
</style>

<div class="ai-simulator-backdrop" id="aiSimulatorBackdrop"></div>

<div class="ai-simulator-drawer" id="aiSimulatorDrawer">
    <div class="ai-drawer-header">
        <div class="ai-drawer-title">
            🤖 AI Testing Playground
        </div>
        <button class="btn-close-drawer" id="btnCloseSimulator">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>
    
    <div class="ai-drawer-body">
        <div class="simulator-settings">
            <label>Nama Klien Simulasi</label>
            <input type="text" class="form-control" id="simClientName" value="Kak Dinda">
            
            <div class="simulator-stats">
                <span id="simCounterText">Balasan AI: 0/3</span>
                <button class="btn btn-secondary btn-sm" id="btnResetSim" style="padding: 4px 10px; font-size: 12px; background: white; border: 1px solid var(--border-color);">
                    <i class="fa-solid fa-rotate-right"></i> Reset Simulator
                </button>
            </div>
        </div>
        
        <div class="simulator-chat-area" id="simChatArea">
            <div class="chat-placeholder" id="simPlaceholder">
                Kirim pesan seperti calon klien untuk menguji jawaban AI dari Knowledge Base kamu.
            </div>
            <!-- Chat bubbles will go here -->
            <div class="typing-indicator" id="simTypingIndicator">
                <div class="typing-spinner"></div>
                AI sedang mengetik...
            </div>
        </div>
        
        <div class="simulator-input-area">
            <input type="text" class="sim-input" id="simInputMessage" placeholder="Tulis pesan sebagai calon klien... (Enter kirim)">
            <button class="sim-btn-send" id="btnSendSim"><i class="fa-solid fa-paper-plane"></i></button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const backdrop = document.getElementById('aiSimulatorBackdrop');
    const drawer = document.getElementById('aiSimulatorDrawer');
    const btnOpen = document.querySelector('.btn-simulator');
    const btnClose = document.getElementById('btnCloseSimulator');
    
    // UI Elements
    const chatArea = document.getElementById('simChatArea');
    const placeholder = document.getElementById('simPlaceholder');
    const inputMsg = document.getElementById('simInputMessage');
    const btnSend = document.getElementById('btnSendSim');
    const typingIndicator = document.getElementById('simTypingIndicator');
    const clientNameInput = document.getElementById('simClientName');
    const btnReset = document.getElementById('btnResetSim');
    const counterText = document.getElementById('simCounterText');
    
    let replyCount = 0;
    const MAX_REPLIES = 3;
    
    // Open Simulator
    if (btnOpen) {
        btnOpen.addEventListener('click', () => {
            backdrop.classList.add('show');
            // small delay for display block to take effect before sliding
            setTimeout(() => drawer.classList.add('show'), 10);
        });
    }
    
    // Close Simulator
    function closeSimulator() {
        drawer.classList.remove('show');
        setTimeout(() => backdrop.classList.remove('show'), 300);
    }
    btnClose.addEventListener('click', closeSimulator);
    backdrop.addEventListener('click', closeSimulator);
    
    // Reset Simulator
    btnReset.addEventListener('click', () => {
        replyCount = 0;
        updateCounter();
        
        // Remove all bubbles
        const bubbles = chatArea.querySelectorAll('.sim-bubble-user, .sim-ai-wrapper');
        bubbles.forEach(b => b.remove());
        
        placeholder.style.display = 'block';
    });
    
    function updateCounter() {
        counterText.textContent = `Balasan AI: ${replyCount}/${MAX_REPLIES}`;
    }
    
    // Send Message
    async function sendMessage() {
        const msg = inputMsg.value.trim();
        if (!msg) return;
        
        if (replyCount >= MAX_REPLIES) {
            alert('Batas simulasi tercapai. Silakan reset simulator untuk mengulang.');
            return;
        }
        
        // Hide placeholder
        placeholder.style.display = 'none';
        
        // Add User Bubble
        const userBubble = document.createElement('div');
        userBubble.className = 'sim-bubble sim-bubble-user';
        userBubble.textContent = msg;
        chatArea.insertBefore(userBubble, typingIndicator);
        
        inputMsg.value = '';
        inputMsg.focus();
        scrollToBottom();
        
        // Show typing
        typingIndicator.classList.add('active');
        btnSend.disabled = true;
        scrollToBottom();
        
        const clientName = clientNameInput.value.trim() || 'Klien';
        const startTime = Date.now();
        
        try {
            const response = await fetch('/api/simulator/chat', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                body: JSON.stringify({
                    message: msg,
                    client_name: clientName
                })
            });
            
            const data = await response.json();
            const timeTaken = Date.now() - startTime;
            
            typingIndicator.classList.remove('active');
            btnSend.disabled = false;
            
            if (data.success) {
                replyCount++;
                updateCounter();
                
                // Add AI Bubble
                const wrapper = document.createElement('div');
                wrapper.className = 'sim-ai-wrapper';
                
                let rawText = data.reply;
                let isMultiBubble = document.getElementById('ai_multi_bubble_enabled') 
                    ? document.getElementById('ai_multi_bubble_enabled').checked 
                    : true;
                let maxBubbles = document.getElementById('ai_max_bubbles')
                    ? parseInt(document.getElementById('ai_max_bubbles').value)
                    : 3;
                
                // We'll separate URLs and Text into discrete blocks to preserve order
                let blocks = [];
                const urlRegex = /(?:([^\n]+?):\s*)?(https?:\/\/[^\s<]+[^.,!?;:\s<])/g;
                
                let lastIdx = 0;
                rawText.replace(urlRegex, function(match, title, url, offset) {
                    // text before URL
                    let before = rawText.substring(lastIdx, offset).trim();
                    if(before) blocks.push({ type: 'text', content: before });
                    
                    // the URL card
                    title = title ? title.trim().replace(/^-\s*/, '').replace(/^\*\s*/, '') : 'Link Price List';
                    blocks.push({ type: 'url', title: title, url: url });
                    
                    lastIdx = offset + match.length;
                    return match;
                });
                
                // remaining text
                let after = rawText.substring(lastIdx).trim();
                if(after) blocks.push({ type: 'text', content: after });
                
                // Now split text blocks by \n\n if multi-bubble is enabled
                let finalBubbles = [];
                blocks.forEach(b => {
                    if (b.type === 'text') {
                        if (isMultiBubble) {
                            let chunks = b.content.split(/\n\s*\n/).filter(c => c.trim().length > 0);
                            chunks.forEach(c => finalBubbles.push({ type: 'text', content: c }));
                        } else {
                            finalBubbles.push(b);
                        }
                    } else {
                        finalBubbles.push(b);
                    }
                });
                
                // Merge if it exceeds max bubbles
                if (isMultiBubble && finalBubbles.length > maxBubbles) {
                    let mergedTextContent = [];
                    let allowedBubbles = finalBubbles.slice(0, maxBubbles - 1);
                    let overflowBubbles = finalBubbles.slice(maxBubbles - 1);
                    
                    // merge all remaining text into one last text bubble, append URLs directly
                    overflowBubbles.forEach(ob => {
                        if(ob.type === 'text') {
                            mergedTextContent.push(ob.content);
                        } else {
                            if(mergedTextContent.length > 0) {
                                allowedBubbles.push({ type: 'text', content: mergedTextContent.join('\n\n') });
                                mergedTextContent = [];
                            }
                            allowedBubbles.push(ob);
                        }
                    });
                    if(mergedTextContent.length > 0) {
                        allowedBubbles.push({ type: 'text', content: mergedTextContent.join('\n\n') });
                    }
                    finalBubbles = allowedBubbles;
                }
                
                // Render with natural delay if multi bubble
                const delayMs = isMultiBubble ? 1200 : 0;
                
                chatArea.insertBefore(wrapper, typingIndicator);
                
                async function renderBubbles() {
                    for (let i = 0; i < finalBubbles.length; i++) {
                        const b = finalBubbles[i];
                        
                        if (i > 0 && delayMs > 0) {
                            typingIndicator.classList.add('active');
                            chatArea.insertBefore(typingIndicator, null); // ensure it's at the bottom
                            scrollToBottom();
                            await new Promise(r => setTimeout(r, delayMs));
                            typingIndicator.classList.remove('active');
                        }
                        
                        if (b.type === 'text') {
                            let cleanContent = b.content.replace(/:\s*$/, '');
                            if (cleanContent) {
                                const aiBubble = document.createElement('div');
                                aiBubble.className = 'sim-bubble sim-bubble-ai';
                                aiBubble.innerHTML = cleanContent.replace(/\n/g, '<br>');
                                wrapper.appendChild(aiBubble);
                            }
                        } else if (b.type === 'url') {
                            const card = document.createElement('div');
                            card.style.cssText = 'border: 1px solid var(--border-color); border-radius: 12px; padding: 12px; margin-bottom: 8px; width: 280px; background: white; align-self: flex-start; box-shadow: 0 2px 8px rgba(0,0,0,0.02);';
                            card.innerHTML = `
                                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px;">
                                    <div style="width: 40px; height: 40px; background: rgba(107, 92, 216, 0.1); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: var(--primary); font-size: 20px; flex-shrink: 0;">
                                        <i class="fa-regular fa-file-lines"></i>
                                    </div>
                                    <div style="overflow: hidden;">
                                        <div style="font-weight: 600; font-size: 14px; color: var(--text-dark); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">${b.title}</div>
                                        <div style="font-size: 11px; color: var(--text-muted);">Lampiran dokumen</div>
                                    </div>
                                </div>
                                <a href="${b.url}" target="_blank" style="display: block; text-align: center; background: rgba(107, 92, 216, 0.1); color: var(--primary); text-decoration: none; padding: 8px; border-radius: 6px; font-weight: 600; font-size: 13px; transition: 0.2s;">
                                    <i class="fa-solid fa-arrow-up-right-from-square" style="margin-right: 4px;"></i> Buka File
                                </a>
                            `;
                            wrapper.appendChild(card);
                        }
                        scrollToBottom();
                    }
                    
                    // Add meta info at the end
                    const meta = document.createElement('div');
                    meta.className = 'sim-meta';
                    meta.innerHTML = `<i class="fa-solid fa-magnifying-glass"></i> Dikutip dari Knowledge Base (Waktu: ${timeTaken} ms) <i class="fa-solid fa-chevron-down" style="margin-left:4px; font-size:9px;"></i>`;
                    wrapper.appendChild(meta);
                    scrollToBottom();
                }
                
                renderBubbles();
            } else {
                // Add Error Bubble instead of ugly alert popup
                const wrapper = document.createElement('div');
                wrapper.className = 'sim-ai-wrapper';
                
                const errorBubble = document.createElement('div');
                errorBubble.className = 'sim-bubble sim-bubble-ai';
                errorBubble.style.color = '#ef4444';
                errorBubble.style.backgroundColor = '#fef2f2';
                errorBubble.style.borderColor = '#fecaca';
                errorBubble.innerHTML = '⚠️ ' + (data.message || 'Koneksi ke AI terputus.');
                
                wrapper.appendChild(errorBubble);
                chatArea.insertBefore(wrapper, typingIndicator);
            }
            
        } catch (e) {
            console.error("Simulator Error:", e);
            typingIndicator.classList.remove('active');
            btnSend.disabled = false;
            alert('Terjadi kesalahan JavaScript atau Jaringan. Cek console browser (F12).');
        }
        
        scrollToBottom();
    }
    
    btnSend.addEventListener('click', sendMessage);
    inputMsg.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            sendMessage();
        }
    });
    
    function scrollToBottom() {
        chatArea.scrollTop = chatArea.scrollHeight;
    }
});
</script>
