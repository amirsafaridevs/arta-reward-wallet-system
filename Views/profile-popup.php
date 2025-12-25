<style>
    #arta-profile-popup-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 999999;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.3s ease, visibility 0.3s ease;
    }
    
    #arta-profile-popup-overlay.show {
        opacity: 1;
        visibility: visible;
    }
    
    .arta-profile-popup {
        background: white;
        border-radius: 8px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        max-width: 500px;
        width: 90%;
        position: relative;
        transform: translateY(-20px);
        transition: transform 0.3s ease;
        overflow: hidden;
    }
    
    #arta-profile-popup-overlay.show .arta-profile-popup {
        transform: translateY(0);
    }
    
    .arta-popup-header {
        background: linear-gradient(135deg, #fff3e0 0%, #ffe0b2 100%);
        padding: 24px;
        text-align: center;
        border-bottom: 3px solid #f57c00;
    }
    
    .arta-popup-icon {
        width: 60px;
        height: 60px;
        background: #f57c00;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 16px;
        box-shadow: 0 4px 12px rgba(245, 124, 0, 0.3);
    }
    
    .arta-popup-icon svg {
        width: 32px;
        height: 32px;
        fill: white;
    }
    
    .arta-popup-title {
        color: #e65100;
        font-size: 18px;
        font-weight: 700;
        margin: 0 0 8px 0;
    }
    
    .arta-popup-bonus {
        color: #f57c00;
        font-size: 24px;
        font-weight: 800;
        margin: 0;
    }
    
    .arta-popup-content {
        padding: 24px;
        text-align: center;
    }
    
    .arta-popup-message {
        color: #555;
        font-size: 15px;
        line-height: 1.6;
        margin-bottom: 24px;
    }
    
    .arta-popup-actions {
        display: flex;
        gap: 12px;
        justify-content: center;
    }
    
    .arta-popup-btn {
        padding: 12px 24px;
        border-radius: 4px;
        font-size: 14px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s ease;
        cursor: pointer;
        border: none;
        display: inline-block;
    }
    
    .arta-popup-btn-primary {
        background: #f57c00;
        color: white;
    }
    
    .arta-popup-btn-primary:hover {
        background: #e65100;
        box-shadow: 0 4px 12px rgba(245, 124, 0, 0.3);
    }
    
    .arta-popup-btn-secondary {
        background: #f5f5f5;
        color: #666;
    }
    
    .arta-popup-btn-secondary:hover {
        background: #e0e0e0;
    }
    
    .arta-popup-close {
        position: absolute;
        top: 12px;
        left: 12px;
        width: 32px;
        height: 32px;
        background: rgba(255, 255, 255, 0.9);
        border: none;
        border-radius: 50%;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
        z-index: 1;
    }
    
    .arta-popup-close:hover {
        background: white;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
    }
    
    .arta-popup-close svg {
        width: 16px;
        height: 16px;
        fill: #666;
    }
    
    @media (max-width: 600px) {
        .arta-profile-popup {
            width: 95%;
        }
        
        .arta-popup-actions {
            flex-direction: column;
        }
        
        .arta-popup-btn {
            width: 100%;
        }
    }
</style>

<div id="arta-profile-popup-overlay">
    <div class="arta-profile-popup">
        <button class="arta-popup-close" onclick="artaCloseProfilePopup()">
            <svg viewBox="0 0 24 24">
                <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
            </svg>
        </button>
        
        <div class="arta-popup-header">
            <div class="arta-popup-icon">
                <svg viewBox="0 0 24 24">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                </svg>
            </div>
            <h3 class="arta-popup-title">تکمیل پروفایل و دریافت پاداش</h3>
            <p class="arta-popup-bonus"><?php echo $bonusFormatted; ?></p>
        </div>
        
        <div class="arta-popup-content">
            <p class="arta-popup-message"><?php echo esc_html($message); ?></p>
            
            <div class="arta-popup-actions">
                <a href="<?php echo esc_url($edit_account_url); ?>" class="arta-popup-btn arta-popup-btn-primary">
                    تکمیل اطلاعات
                </a>
                <button onclick="artaDismissProfilePopup()" class="arta-popup-btn arta-popup-btn-secondary">
                    بعداً
                </button>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    // Show popup after a short delay
    setTimeout(function() {
        document.getElementById('arta-profile-popup-overlay').classList.add('show');
    }, 500);
    
    // Close popup function
    window.artaCloseProfilePopup = function() {
        document.getElementById('arta-profile-popup-overlay').classList.remove('show');
    };
    
    // Dismiss popup function
    window.artaDismissProfilePopup = function() {
        artaCloseProfilePopup();
    };
    
    // Close on overlay click
    document.getElementById('arta-profile-popup-overlay').addEventListener('click', function(e) {
        if (e.target === this) {
            artaCloseProfilePopup();
        }
    });
    
    // Close on ESC key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            artaCloseProfilePopup();
        }
    });
})();
</script>

