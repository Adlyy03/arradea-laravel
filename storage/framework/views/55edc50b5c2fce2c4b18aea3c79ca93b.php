
<button id="pwa-install-btn" 
        style="display: none;"
        class="fixed bottom-20 lg:bottom-6 right-5 lg:right-6 z-50 px-5 py-3 rounded-2xl font-bold text-sm text-white shadow-2xl transition-all hover:scale-105 active:scale-95 flex items-center gap-2 animate-bounce"
        style="background: linear-gradient(135deg, #72bf77, #4db85a); box-shadow: 0 8px 32px rgba(114, 191, 119, 0.5);">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
    </svg>
    <span>Install App</span>
</button>

<style>
    @keyframes bounce {
        0%, 100% {
            transform: translateY(0);
        }
        50% {
            transform: translateY(-10px);
        }
    }
    
    #pwa-install-btn {
        animation: bounce 2s ease-in-out infinite;
    }
    
    #pwa-install-btn:hover {
        animation: none;
    }
    
    /* Hide install button when running as PWA */
    .pwa-mode #pwa-install-btn {
        display: none !important;
    }
</style>
<?php /**PATH C:\laragon\www\arradea-laravel\resources\views/components/pwa-install-button.blade.php ENDPATH**/ ?>