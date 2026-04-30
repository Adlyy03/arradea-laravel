import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;

// Enhanced Toast Notification System
window.Arradea = {
    toast: {
        show(message, type = 'success', duration = 4000) {
            const toast = document.createElement('div');
            toast.className = `toast ${type}`;
            
            const icon = {
                success: '✅',
                error: '❌', 
                warning: '⚠️',
                info: 'ℹ️'
            }[type] || '✅';
            
            toast.innerHTML = `
                <div class="flex items-center gap-3">
                    <span class="text-lg">${icon}</span>
                    <span class="font-medium text-sm">${message}</span>
                    <button onclick="this.parentElement.parentElement.remove()" class="ml-auto text-gray-400 hover:text-gray-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            `;
            
            document.body.appendChild(toast);
            
            // Trigger show animation
            setTimeout(() => toast.classList.add('show'), 100);
            
            // Auto remove
            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => toast.remove(), 300);
            }, duration);
        },
        
        success(message) { this.show(message, 'success'); },
        error(message) { this.show(message, 'error'); },
        warning(message) { this.show(message, 'warning'); },
        info(message) { this.show(message, 'info'); }
    },
    
    // Enhanced Loading States
    loading: {
        show(element, text = 'Loading...') {
            element.disabled = true;
            element.dataset.originalText = element.textContent;
            element.innerHTML = `
                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                ${text}
            `;
        },
        
        hide(element) {
            element.disabled = false;
            element.textContent = element.dataset.originalText || 'Submit';
        }
    },
    
    // Smooth Page Transitions
    pageTransition: {
        init() {
            // Add fade effect to page loads
            document.addEventListener('DOMContentLoaded', () => {
                document.body.style.opacity = '0';
                setTimeout(() => {
                    document.body.style.transition = 'opacity 0.3s ease';
                    document.body.style.opacity = '1';
                }, 50);
            });
        }
    }
};

// Initialize Alpine.js
Alpine.start();

// Initialize page transitions
window.Arradea.pageTransition.init();

// Auto-show Laravel flash messages as toasts
document.addEventListener('DOMContentLoaded', () => {
    // Check for Laravel flash messages
    const successMsg = document.querySelector('meta[name="flash-success"]');
    const errorMsg = document.querySelector('meta[name="flash-error"]');
    const warningMsg = document.querySelector('meta[name="flash-warning"]');
    const infoMsg = document.querySelector('meta[name="flash-info"]');
    
    if (successMsg) window.Arradea.toast.success(successMsg.content);
    if (errorMsg) window.Arradea.toast.error(errorMsg.content);
    if (warningMsg) window.Arradea.toast.warning(warningMsg.content);
    if (infoMsg) window.Arradea.toast.info(infoMsg.content);
});

// Enhanced form handling
document.addEventListener('submit', (e) => {
    const form = e.target;
    const submitBtn = form.querySelector('button[type="submit"]');
    
    if (submitBtn && !submitBtn.disabled) {
        window.Arradea.loading.show(submitBtn);
        
        // Reset loading state if form submission fails
        setTimeout(() => {
            if (submitBtn.disabled) {
                window.Arradea.loading.hide(submitBtn);
            }
        }, 10000);
    }
});

// Keyboard shortcuts
document.addEventListener('keydown', (e) => {
    // Ctrl+B to toggle sidebar (if exists)
    if (e.ctrlKey && e.key === 'b') {
        e.preventDefault();
        const sidebarToggle = document.querySelector('[x-data]')?.__x?.$data;
        if (sidebarToggle && 'sideOpen' in sidebarToggle) {
            sidebarToggle.sideOpen = !sidebarToggle.sideOpen;
        }
    }
});
