// Global Responsive JS for CloseMateAI

document.addEventListener('DOMContentLoaded', () => {
    // 1. Setup Hamburger Menu Toggle
    const mobileBtn = document.querySelector('.mobile-menu-btn');
    const sidebar = document.querySelector('.sidebar');
    
    if (mobileBtn && sidebar) {
        mobileBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            sidebar.classList.toggle('show');
        });
        
        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', (e) => {
            if (window.innerWidth <= 768) {
                if (!sidebar.contains(e.target) && !mobileBtn.contains(e.target)) {
                    sidebar.classList.remove('show');
                }
            }
        });
    }

    // 2. Setup Chat Mobile Toggles (if on chat page)
    const chatItems = document.querySelectorAll('.chat-item');
    const chatMain = document.querySelector('.chat-main');
    
    if (chatItems.length > 0 && chatMain) {
        // Add a back button to chat-header on mobile
        const chatHeaderActions = document.querySelector('.chat-header-actions');
        if (chatHeaderActions) {
            const backBtn = document.createElement('button');
            backBtn.className = 'btn-icon mobile-back-btn';
            backBtn.style.display = 'none'; // Hidden by default, CSS can show it, or we handle via JS
            backBtn.innerHTML = '<i class="fa-solid fa-arrow-left"></i>';
            backBtn.style.marginRight = '8px';
            backBtn.style.border = 'none';
            backBtn.style.background = 'var(--bg-light)';
            
            // Insert it at the start of .chat-header > .client-info
            const clientInfo = document.querySelector('.chat-header .client-info');
            if (clientInfo) {
                clientInfo.style.display = 'flex';
                clientInfo.style.alignItems = 'center';
                clientInfo.insertBefore(backBtn, clientInfo.firstChild);
            }
            
            // Show chat on mobile when an item is clicked
            chatItems.forEach(item => {
                item.addEventListener('click', () => {
                    if (window.innerWidth <= 768) {
                        chatMain.classList.add('active');
                        backBtn.style.display = 'flex';
                    }
                });
            });
            
            // Hide chat when back is clicked
            backBtn.addEventListener('click', () => {
                chatMain.classList.remove('active');
            });
            
            // Ensure proper display when resizing
            window.addEventListener('resize', () => {
                if (window.innerWidth > 768) {
                    backBtn.style.display = 'none';
                    chatMain.classList.remove('active');
                } else if (chatMain.classList.contains('active')) {
                    backBtn.style.display = 'flex';
                }
            });
        }
    }
});
