<style>
    .wa-global-widget {
        position: fixed;
        bottom: 24px;
        right: 24px;
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 12px;
        z-index: 9999;
        font-family: inherit;
    }
    .wa-global-bubble {
        background: white;
        padding: 8px 16px;
        border-radius: 20px;
        font-size: 14px;
        font-weight: 500;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        border: 1px solid #e1e1e4;
        animation: floatBubble 2s ease-in-out infinite;
        color: #111827;
    }
    .wa-global-btn-wrapper {
        position: relative;
    }
    .wa-global-btn {
        width: 56px;
        height: 56px;
        background: #25d366;
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 32px;
        box-shadow: 0 4px 12px rgba(37, 211, 102, 0.4);
        cursor: pointer;
        transition: transform 0.2s;
        text-decoration: none;
    }
    .wa-global-btn:hover {
        transform: scale(1.05);
        color: white;
    }
    .wa-global-close {
        position: absolute;
        top: -4px;
        right: -4px;
        width: 20px;
        height: 20px;
        background: white;
        border: 1px solid #e1e1e4;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 10px;
        color: #6b7280;
        cursor: pointer;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        z-index: 2;
    }
    @keyframes floatBubble {
        0% { transform: translateY(0px); }
        50% { transform: translateY(-4px); }
        100% { transform: translateY(0px); }
    }
</style>

<div class="wa-global-widget" id="waGlobalWidget">
    <div class="wa-global-bubble">Chat with us</div>
    <div class="wa-global-btn-wrapper">
        <div class="wa-global-close" onclick="document.getElementById('waGlobalWidget').style.display='none'"><i class="fa-solid fa-xmark"></i></div>
        <a href="https://wa.me/6285878067644" target="_blank" class="wa-global-btn">
            <i class="fa-brands fa-whatsapp"></i>
        </a>
    </div>
</div>
