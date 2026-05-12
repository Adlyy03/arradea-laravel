/**
 * Mobile Menu Handler - Simplified & Robust
 * Handles mobile navigation without conflicts
 */

export function initMobileMenu() {
    console.log('Mobile menu handler initialized');
    
    // Simple reset on page load
    const resetMenu = () => {
        const body = document.querySelector('body[x-data]');
        if (body && body.__x && body.__x.$data) {
            body.__x.$data.mobileOpen = false;
        }
        // Reset body scroll
        document.body.style.overflow = '';
        document.body.style.position = '';
        document.body.style.width = '';
    };
    
    // Reset on page load/navigation
    window.addEventListener('load', resetMenu);
    window.addEventListener('pageshow', resetMenu);
    
    // Close menu on resize to desktop
    let resizeTimer;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(() => {
            if (window.innerWidth >= 768) { // md breakpoint
                resetMenu();
            }
        }, 250);
    });
}

// Auto-initialize
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initMobileMenu);
} else {
    initMobileMenu();
}
