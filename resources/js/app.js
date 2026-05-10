import './bootstrap';
import Alpine from 'alpinejs';
import AOS from 'aos';
import { gsap } from 'gsap';
import { ScrollToPlugin } from 'gsap/ScrollToPlugin';
import { ScrollTrigger } from 'gsap/ScrollTrigger';

// Import splash screen - PREMIUM & FAST!
import './splash';

// Import performance optimizations
import './performance';

// Import welcome page interactions (only loads on welcome page)
if (document.querySelector('.welcome-section')) {
    import('./welcome-interactions');
}

// Register GSAP plugins
gsap.registerPlugin(ScrollToPlugin, ScrollTrigger);

// Initialize Alpine.js
window.Alpine = Alpine;
Alpine.start();

// Initialize AOS (Animate On Scroll)
document.addEventListener('DOMContentLoaded', () => {
    AOS.init({
        duration: 800,
        easing: 'ease-in-out',
        once: true,
        offset: 100,
        delay: 100,
        disable: 'mobile', // Disable on mobile for better performance
    });
    
    // Refresh AOS on dynamic content
    setTimeout(() => AOS.refresh(), 500);
});

// Make GSAP available globally
window.gsap = gsap;

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
            
            // Trigger show animation with GSAP
            gsap.fromTo(toast, 
                { x: 100, opacity: 0 },
                { x: 0, opacity: 1, duration: 0.3, ease: 'power2.out' }
            );
            
            // Auto remove
            setTimeout(() => {
                gsap.to(toast, {
                    x: 100,
                    opacity: 0,
                    duration: 0.3,
                    ease: 'power2.in',
                    onComplete: () => toast.remove()
                });
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
                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline-block" fill="none" viewBox="0 0 24 24">
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
            // Fade in on page load after splash
            window.addEventListener('splashComplete', () => {
                gsap.from('body', {
                    opacity: 0,
                    duration: 0.5,
                    ease: 'power2.out'
                });
            });
        }
    },
    
    // Scroll to top with animation
    scrollToTop() {
        gsap.to(window, {
            scrollTo: { y: 0 },
            duration: 0.8,
            ease: 'power2.inOut'
        });
    }
};

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
    
    // Escape to close modals
    if (e.key === 'Escape') {
        const modals = document.querySelectorAll('[x-data]');
        modals.forEach(modal => {
            if (modal.__x && modal.__x.$data.open) {
                modal.__x.$data.open = false;
            }
        });
    }
});

// Smooth scroll for anchor links
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const href = this.getAttribute('href');
            if (href !== '#' && href !== '#!') {
                e.preventDefault();
                const target = document.querySelector(href);
                if (target) {
                    gsap.to(window, {
                        scrollTo: { y: target, offsetY: 80 },
                        duration: 0.8,
                        ease: 'power2.inOut'
                    });
                }
            }
        });
    });
});

// Add scroll-to-top button
const createScrollTopButton = () => {
    const button = document.createElement('button');
    button.id = 'scroll-to-top';
    button.className = 'fixed bottom-20 right-6 w-12 h-12 bg-sage text-white rounded-full shadow-lg hover:bg-primary-600 transition-all duration-300 opacity-0 pointer-events-none z-40 flex items-center justify-center';
    button.innerHTML = `
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
        </svg>
    `;
    button.onclick = () => window.Arradea.scrollToTop();
    document.body.appendChild(button);
    
    // Show/hide on scroll
    window.addEventListener('scroll', () => {
        if (window.scrollY > 300) {
            button.classList.remove('opacity-0', 'pointer-events-none');
        } else {
            button.classList.add('opacity-0', 'pointer-events-none');
        }
    });
};

// Initialize scroll-to-top button
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', createScrollTopButton);
} else {
    createScrollTopButton();
}

console.log('🚀 Arradea Marketplace - Modern UI Loaded');
