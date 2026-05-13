/**
 * Minimal App.js - Emergency Fallback
 * Use this if the main app.js causes issues
 * 
 * To use: Update vite.config.js to use this file instead of app.js
 */

import './bootstrap';
import Alpine from 'alpinejs';

// Initialize Alpine.js only
window.Alpine = Alpine;

// Start Alpine only once
if (!window.alpineStarted) {
    Alpine.start();
    window.alpineStarted = true;
    console.log('✅ Alpine.js started (minimal mode)');
}

// Minimal toast system
window.Arradea = {
    toast: {
        show(message, type = 'success') {
            alert(`${type.toUpperCase()}: ${message}`);
        },
        success(message) { this.show(message, 'success'); },
        error(message) { this.show(message, 'error'); },
        warning(message) { this.show(message, 'warning'); },
        info(message) { this.show(message, 'info'); }
    }
};

// Auto-show Laravel flash messages
document.addEventListener('DOMContentLoaded', () => {
    const successMsg = document.querySelector('meta[name="flash-success"]');
    const errorMsg = document.querySelector('meta[name="flash-error"]');
    
    if (successMsg) window.Arradea.toast.success(successMsg.content);
    if (errorMsg) window.Arradea.toast.error(errorMsg.content);
});

console.log('🚀 Arradea Marketplace - Minimal Mode Loaded');
