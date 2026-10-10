// Global Responsive JS for CloseMateAI

document.addEventListener('DOMContentLoaded', () => {
    // 1. Center the active menu item in the horizontal scroll view on mobile
    if (window.innerWidth <= 768) {
        const activeNav = document.querySelector('.nav-item.active');
        const navMenu = document.querySelector('.nav-menu');
        if (activeNav && navMenu) {
            // Scroll to the active item so it's visible
            navMenu.scrollLeft = activeNav.offsetLeft - (window.innerWidth / 2) + (activeNav.offsetWidth / 2);
        }
    }

    // 2. Setup Chat Mobile Toggles (if on chat page)
    const chatMain = document.querySelector('.chat-main');
    
    if (chatMain) {
        // Add a back button to chat-header on mobile
        const chatHeaderActions = document.querySelector('.chat-header-actions');
        if (chatHeaderActions) {
            // Create back button
            const backBtn = document.createElement('button');
            backBtn.className = 'btn-icon mobile-back-btn';
            backBtn.style.display = 'none'; // Hidden by default
            backBtn.innerHTML = '<i class="fa-solid fa-arrow-left"></i>';
            backBtn.style.marginRight = '8px';
            backBtn.style.border = 'none';
            backBtn.style.background = 'var(--bg-light)';
            
            // Insert it at the start of .chat-header > .chat-header-user
            const clientInfo = document.querySelector('.chat-header-user');
            if (clientInfo) {
                clientInfo.style.display = 'flex';
                clientInfo.style.alignItems = 'center';
                clientInfo.insertBefore(backBtn, clientInfo.firstChild);
            }
            
            // Use Event Delegation for chat items (since they are generated dynamically)
            document.body.addEventListener('click', (e) => {
                const chatItem = e.target.closest('.chat-item');
                if (chatItem) {
                    if (window.innerWidth <= 768) {
                        chatMain.classList.add('active');
                        backBtn.style.display = 'flex';
                    }
                }
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
